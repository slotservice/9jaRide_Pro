import 'dart:convert';
import 'dart:developer';
import 'dart:typed_data';

import 'package:driver/firebase_options.dart';
import 'package:driver/model/driver_user_model.dart';
import 'package:driver/model/intercity_order_model.dart';
import 'package:driver/model/order_model.dart';
import 'package:driver/model/user_model.dart';
import 'package:driver/constant/show_toast_dialog.dart';
import 'package:driver/ui/auth_screen/login_screen.dart';
import 'package:driver/ui/chat_screen/chat_screen.dart';
import 'package:driver/ui/help_support_screen/help_support_screen.dart';
import 'package:driver/ui/home_screens/incoming_ride_screen.dart';
import 'package:driver/ui/order_intercity_screen/complete_intecity_order_screen.dart';
import 'package:driver/ui/order_screen/complete_order_screen.dart';
import 'package:driver/utils/Preferences.dart';
import 'package:driver/utils/fire_store_utils.dart';
import 'package:driver/utils/ride_ringtone.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:get/get.dart';

/// The push type the customer app sends when a rider books.
const String kRideRequestType = "city_order";

/// One id for the ride request alert, so a fresh request replaces the previous
/// one in the shade instead of stacking up behind it.
const int kRideRequestNotificationId = 1001;

/// Runs in its own isolate when a push arrives with the app backgrounded or
/// killed. Nothing from the running app is available here, so Firebase and the
/// notifications plugin both have to be set up from scratch.
///
/// The vm:entry-point pragma matters: without it tree shaking can drop this
/// function from a release build and background pushes stop being handled.
@pragma('vm:entry-point')
Future<void> firebaseMessageBackgroundHandle(RemoteMessage message) async {
  try {
    await Firebase.initializeApp(options: DefaultFirebaseOptions.currentPlatform);
  } catch (e) {
    // Already initialised in this isolate, or genuinely broken. Either way the
    // notification below does not depend on Firebase, so carry on.
    log("Background handler Firebase init: $e");
  }
  await NotificationService.showMessageNotification(message);
}

class NotificationService {
  FlutterLocalNotificationsPlugin flutterLocalNotificationsPlugin = FlutterLocalNotificationsPlugin();

  // Must match com.google.firebase.messaging.default_notification_channel_id in
  // AndroidManifest.xml. Android 8 and up takes the importance from the channel
  // and ignores whatever the notification asks for, so a channel that is never
  // created gets made for us at default importance. That is why pushes were
  // landing silently in the shade instead of popping up.
  static const AndroidNotificationChannel androidChannel = AndroidNotificationChannel(
    'njaridepro-driver',
    'njaridepro-driver',
    description: 'Ride requests and trip updates',
    importance: Importance.max,
  );

  // Ride requests get a channel of their own, and it has to be a NEW id.
  // Android freezes a channel's sound, vibration and Do Not Disturb behaviour
  // at the moment it is created and ignores every later change, so reusing
  // 'njaridepro-driver' would leave every phone that already has the app on the
  // old quiet settings for ever.
  //
  // playSound is false on purpose. RideRingtone plays the looping tone instead,
  // because a channel sound cannot be stopped once Android has started it: the
  // driver would accept the ride and the phone would carry on ringing. Doing
  // both would also play two tones over each other.
  //
  // bypassDnd only takes effect if the driver has granted the app Do Not
  // Disturb access. Android gives an app no way to force that, so the flag is
  // set here and the grant is asked for once during setup.
  static final AndroidNotificationChannel rideRequestChannel = AndroidNotificationChannel(
    'njaridepro-driver-call',
    'Incoming ride requests',
    description: 'Rings like a call when a rider books',
    importance: Importance.max,
    bypassDnd: true,
    playSound: false,
    enableVibration: true,
    vibrationPattern: Int64List.fromList(<int>[0, 800, 400, 800, 400, 800, 400, 800]),
    audioAttributesUsage: AudioAttributesUsage.notificationRingtone,
  );

  Future<void> initInfo() async {
    await FirebaseMessaging.instance.setForegroundNotificationPresentationOptions(
      alert: true,
      badge: true,
      sound: true,
    );
    var request = await FirebaseMessaging.instance.requestPermission(
      alert: true,
      announcement: false,
      badge: true,
      carPlay: false,
      criticalAlert: false,
      provisional: false,
      sound: true,
    );

    if (request.authorizationStatus == AuthorizationStatus.authorized || request.authorizationStatus == AuthorizationStatus.provisional) {
      const AndroidInitializationSettings initializationSettingsAndroid = AndroidInitializationSettings('@mipmap/ic_launcher');
      var iosInitializationSettings = const DarwinInitializationSettings();
      final InitializationSettings initializationSettings = InitializationSettings(android: initializationSettingsAndroid, iOS: iosInitializationSettings);
      await flutterLocalNotificationsPlugin.initialize(
        settings: initializationSettings,
        onDidReceiveNotificationResponse: (NotificationResponse response) {
          _handleNotificationResponse(response.payload);
        },
      );
      // Created up front so they exist before the first push arrives, including
      // when the app is in the background and the system posts it for us.
      final AndroidFlutterLocalNotificationsPlugin? androidPlugin =
          flutterLocalNotificationsPlugin.resolvePlatformSpecificImplementation<AndroidFlutterLocalNotificationsPlugin>();
      await androidPlugin?.createNotificationChannel(androidChannel);
      await androidPlugin?.createNotificationChannel(rideRequestChannel);
      // From Android 14 a full screen alert is special access and is not
      // granted to an app just for asking in the manifest. Without it the ride
      // request still arrives, it just appears as a banner rather than taking
      // over the lock screen.
      try {
        await androidPlugin?.requestFullScreenIntentPermission();
      } catch (e) {
        log("Full screen intent permission request: $e");
      }
      await _handleColdStartFromNotification();
      setupInteractedMessage();
    }
  }

  /// The app was launched by tapping the alert, or by the full screen intent
  /// firing while the app was not running. Either way the payload only reaches
  /// us through the launch details.
  ///
  /// The tone starts straight away, but the screen has to wait. On a cold start
  /// the splash controller is still deciding where to send the driver, and it
  /// routes with offAll, which would wipe a ring screen pushed before it. So we
  /// ring first, which is the part the driver actually needs in their pocket,
  /// and show the request once the app has settled.
  Future<void> _handleColdStartFromNotification() async {
    try {
      final NotificationAppLaunchDetails? details = await flutterLocalNotificationsPlugin.getNotificationAppLaunchDetails();
      if (details?.didNotificationLaunchApp != true) return;
      final String? payload = details?.notificationResponse?.payload;
      if (payload == null || payload.isEmpty) return;

      final Map<String, dynamic> data = Map<String, dynamic>.from(jsonDecode(payload) as Map);
      if (data['type']?.toString() == kRideRequestType) {
        RideRingtone.start();
      }
      Future.delayed(const Duration(seconds: 3), () => _handleNotificationResponse(payload));
    } catch (e) {
      log("Notification launch details: $e");
    }
  }

  void _handleNotificationResponse(String? payload) {
    if (payload == null || payload.isEmpty) return;
    try {
      final Map<String, dynamic> data = Map<String, dynamic>.from(jsonDecode(payload) as Map);
      handleMessageClick(payload: data);
    } catch (e) {
      log("Notification payload decode error: $e");
    }
  }

  Future<void> setupInteractedMessage() async {
    // Register the background handler unconditionally at cold start, otherwise
    // background/terminated data-only pushes are silently dropped. It has to be
    // the top level function itself, not a closure wrapping it: the plugin
    // resolves a callback handle for it, and a closure has no handle.
    FirebaseMessaging.onBackgroundMessage(firebaseMessageBackgroundHandle);

    FirebaseMessaging.onMessage.listen((RemoteMessage message) {
      log("::::::::::::onMessage:::::::::::::::::");
      log(message.data.toString());
      if (_isRideRequest(message)) {
        // Foreground: no point posting into the shade, the driver is looking at
        // the app. Ring and put the request in front of them directly.
        _startRideRequest(message.data['orderId']?.toString());
        return;
      }
      if (message.notification != null) {
        display(message);
      }
    });
    FirebaseMessaging.onMessageOpenedApp.listen((RemoteMessage message) async {
      log("::::::::::::onMessageOpenedApp:::::::::::::::::");
      handleMessageClick(payload: message.data);
    });

    FirebaseMessaging.instance.getInitialMessage().then((message) async {
      log("::::::::::::getInitialMessage:::::::::::::::::");
      if (message?.data != null) {
        await Preferences.setBoolean(Preferences.notificationPlayload, true);
        log("Preferences.getBoolean(Preferences.notificationPlayload) :::: ${Preferences.getBoolean(Preferences.notificationPlayload)}");
        handleMessageClick(payload: message?.data);
      }
    });
    await FirebaseMessaging.instance.subscribeToTopic("njaridepro_driver");
  }

  static bool _isRideRequest(RemoteMessage message) => message.data['type']?.toString() == kRideRequestType;

  /// Rings and shows the incoming request. Runs in the app's own isolate, so it
  /// is the only place allowed to touch audio or navigation.
  static void _startRideRequest(String? orderId) {
    if (orderId == null || orderId.isEmpty) return;
    final String id = orderId;
    RideRingtone.start();
    // Do not stack a second ring screen on top of one already showing.
    if (Get.currentRoute.contains('IncomingRideScreen')) return;
    Get.to(() => IncomingRideScreen(orderId: id));
  }

  /// Builds and posts the alert for an incoming push.
  ///
  /// Static and self contained because the background isolate calls it with no
  /// running app around it.
  static Future<void> showMessageNotification(RemoteMessage message) async {
    try {
      final bool isRideRequest = _isRideRequest(message);
      final String title = message.notification?.title ?? message.data['title']?.toString() ?? 'New Ride Available';
      final String body = message.notification?.body ?? message.data['body']?.toString() ?? 'A customer has placed a ride near your location.';

      final FlutterLocalNotificationsPlugin plugin = FlutterLocalNotificationsPlugin();
      const AndroidInitializationSettings androidInit = AndroidInitializationSettings('@mipmap/ic_launcher');
      const DarwinInitializationSettings iosInit = DarwinInitializationSettings();
      await plugin.initialize(settings: const InitializationSettings(android: androidInit, iOS: iosInit));
      final AndroidFlutterLocalNotificationsPlugin? androidPlugin = plugin.resolvePlatformSpecificImplementation<AndroidFlutterLocalNotificationsPlugin>();
      await androidPlugin?.createNotificationChannel(androidChannel);
      await androidPlugin?.createNotificationChannel(rideRequestChannel);

      final AndroidNotificationDetails androidDetails = isRideRequest
          ? AndroidNotificationDetails(
              rideRequestChannel.id,
              rideRequestChannel.name,
              channelDescription: rideRequestChannel.description,
              importance: Importance.max,
              priority: Priority.max,
              playSound: false,
              enableVibration: true,
              vibrationPattern: Int64List.fromList(<int>[0, 800, 400, 800, 400, 800, 400, 800]),
              category: AndroidNotificationCategory.call,
              // This is what takes over the lock screen instead of sliding a
              // banner down. Android only honours it when the screen is off or
              // locked; otherwise it falls back to a heads up banner, which is
              // the right behaviour when the driver is already using the phone.
              fullScreenIntent: true,
              // Deliberately not ongoing. An ongoing notification cannot be
              // swiped away, and a ride alert that will not clear is a worse
              // complaint than one that clears too easily.
              ongoing: false,
              autoCancel: true,
              timeoutAfter: RideRingtone.maxRingDuration.inMilliseconds,
            )
          : AndroidNotificationDetails(
              androidChannel.id,
              androidChannel.name,
              channelDescription: androidChannel.description,
              importance: Importance.high,
              priority: Priority.high,
              ticker: 'ticker',
            );

      const DarwinNotificationDetails darwinDetails = DarwinNotificationDetails(presentAlert: true, presentBadge: true, presentSound: true);

      await plugin.show(
        id: isRideRequest ? kRideRequestNotificationId : 0,
        title: title,
        body: body,
        notificationDetails: NotificationDetails(android: androidDetails, iOS: darwinDetails),
        payload: jsonEncode(message.data),
      );
    } catch (e) {
      log("showMessageNotification error: $e");
    }
  }

  /// Clears the ride request alert once it has been answered.
  static Future<void> cancelRideRequestNotification() async {
    try {
      await FlutterLocalNotificationsPlugin().cancel(id: kRideRequestNotificationId);
    } catch (e) {
      log("cancelRideRequestNotification error: $e");
    }
  }

  Future<void> handleMessageClick({required dynamic payload}) async {
    log("handleMessageClick :::::: ${payload.toString()}");
    final data = payload;

    if (data != null) {
      // There has to be a signed in driver before any of these routes mean
      // anything. On a cold start currentUser is still null for the first
      // moment, because Firebase restores the session asynchronously, so read
      // it once and then wait rather than throwing the request away. This is
      // the same trap that quietly discarded push tokens at startup.
      User? signedInUser = FirebaseAuth.instance.currentUser;
      if (signedInUser == null) {
        signedInUser = await FirebaseAuth.instance.authStateChanges().firstWhere((User? u) => u != null).timeout(
              const Duration(seconds: 8),
              onTimeout: () => null,
            );
      }
      if (signedInUser == null) {
        // Signed out, or the session never came back. Nothing to show, and the
        // tone must not keep going with no screen to stop it.
        await RideRingtone.stop();
        return;
      }
      // Kill switch: do not honor any pending-ride/chat route if the driver
      // account has been locked by admin.
      final currentUid = FireStoreUtils.getCurrentUid();
      if (FirebaseAuth.instance.currentUser != null) {
        final DriverUserModel? currentDriver = await FireStoreUtils.getDriverProfile(currentUid);
        if (currentDriver?.appLocked == true) {
          await FirebaseAuth.instance.signOut();
          ShowToastDialog.showToast('Your account has been locked by admin. Reason: ${currentDriver?.lockReason ?? 'HP payment overdue'}');
          Get.offAll(const LoginScreen());
          return;
        }
      }
      // display(message);
      if (data['type'] == "admin_chat") {
        DriverUserModel? driver = await FireStoreUtils.getDriverProfile(data['driverId']);
        Get.to(HelpSupportScreen(
          userId: driver?.id,
          userName: driver?.fullName,
          userProfileImage: driver?.profilePic,
          token: driver?.fcmToken,
          isShowAppbar: true,
        ));
      } else if (data['type'] == kRideRequestType) {
        // The alert has been answered, so take it out of the shade and let the
        // ring screen own the tone from here.
        await cancelRideRequestNotification();
        _startRideRequest(data['orderId']?.toString());
      } else if (data['type'] == "city_order_payment_complete") {
        OrderModel? orderModel = await FireStoreUtils.getOrder(data['orderId']);
        Get.to(const CompleteOrderScreen(), arguments: {
          "orderModel": orderModel,
        });
      } else if (data['type'] == "intercity_order_payment_complete") {
        InterCityOrderModel? orderModel = await FireStoreUtils.getInterCityOrder(data['orderId']);
        Get.to(const CompleteIntercityOrderScreen(), arguments: {
          "orderModel": orderModel,
        });
      } else if (data['type'] == "chat") {
        UserModel? customer = await FireStoreUtils.getCustomer(data['customerId']);
        DriverUserModel? driver = await FireStoreUtils.getDriverProfile(data['driverId']);

        Get.to(ChatScreens(
          driverId: driver!.id,
          customerId: customer!.id,
          customerName: customer.fullName,
          customerProfileImage: customer.profilePic,
          driverName: driver.fullName,
          driverProfileImage: driver.profilePic,
          orderId: data['orderId'],
          token: customer.fcmToken,
        ));
      }
    }
  }

  static getToken() async {
    String? token = await FirebaseMessaging.instance.getToken();
    return token!;
  }

  void display(RemoteMessage message) async {
    log('Got a message whilst in the foreground!');
    log('Message data: ${message.notification?.body.toString()}');
    try {
      AndroidNotificationChannel channel = androidChannel;
      AndroidNotificationDetails notificationDetails =
          AndroidNotificationDetails(channel.id, channel.name, channelDescription: 'your channel Description', importance: Importance.high, priority: Priority.high, ticker: 'ticker');
      const DarwinNotificationDetails darwinNotificationDetails = DarwinNotificationDetails(presentAlert: true, presentBadge: true, presentSound: true);
      NotificationDetails notificationDetailsBoth = NotificationDetails(android: notificationDetails, iOS: darwinNotificationDetails);
      await FlutterLocalNotificationsPlugin().show(
        id: 0,
        title: message.notification?.title,
        body: message.notification?.body,
        notificationDetails: notificationDetailsBoth,
        payload: jsonEncode(message.data),
      );
    } on Exception catch (e) {
      log(e.toString());
    }
  }
}
