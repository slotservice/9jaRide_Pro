import 'dart:convert';
import 'dart:developer';
import 'package:owner/model/owner_user_model.dart';
import 'package:owner/ui/help_support_screen/help_support_screen.dart';
import 'package:owner/utils/Preferences.dart';
import 'package:owner/utils/fire_store_utils.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:get/get.dart';

Future<void> firebaseMessageBackgroundHandle(RemoteMessage message) async {
  log("BackGround Message :: ${message.messageId}");
  if (message.notification != null) {
    log(message.data.toString());
    NotificationService().display(message);
  }
}

class NotificationService {
  FlutterLocalNotificationsPlugin flutterLocalNotificationsPlugin = FlutterLocalNotificationsPlugin();

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
      final InitializationSettings initializationSettings =
          InitializationSettings(android: initializationSettingsAndroid, iOS: iosInitializationSettings);
      await flutterLocalNotificationsPlugin.initialize(settings: initializationSettings, onDidReceiveNotificationResponse: (payload) {});
      setupInteractedMessage();
    }
  }

  Future<void> setupInteractedMessage() async {
    RemoteMessage? initialMessage = await FirebaseMessaging.instance.getInitialMessage();
    if (initialMessage != null) {
      FirebaseMessaging.onBackgroundMessage((message) => firebaseMessageBackgroundHandle(message));
    }

    // if (initialMessage != null) {
    //   log('Message also contained a notification: ${initialMessage.notification!.body}');
    // }

    FirebaseMessaging.onMessage.listen((RemoteMessage message) {
      log("::::::::::::onMessage:::::::::::::::::");
      if (message.notification != null) {
        log(message.data.toString());
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
    await FirebaseMessaging.instance.subscribeToTopic("goRide_driver");
  }

  Future<void> handleMessageClick({required dynamic payload}) async {
    log("handleMessageClick :::::: ${payload.toString()}");
    final data = payload;

    if (data != null) {
      // display(message);
      if (data['type'] == "admin_chat") {
        OwnerUserModel? owner = await FireStoreUtils.getOwnerProfile(data['driverId']);
        Get.to(HelpSupportScreen(
          userId: owner?.id,
          userName: owner?.fullName,
          userProfileImage: owner?.profilePic,
          token: owner?.fcmToken,
          isShowAppbar: true,
        ));
      }
    }
  }

  static Future<String> getToken() async {
    String? token = await FirebaseMessaging.instance.getToken();
    return token!;
  }

  void display(RemoteMessage message) async {
    log('Got a message whilst in the foreground!');
    log('Message data: ${message.notification!.body.toString()}');
    try {
      // final id = DateTime.now().millisecondsSinceEpoch ~/ 1000;

      AndroidNotificationChannel channel = const AndroidNotificationChannel(
        '0',
        'goRide-owner',
        description: 'Show GoRide Notification',
        importance: Importance.max,
      );
      AndroidNotificationDetails notificationDetails = AndroidNotificationDetails(channel.id, channel.name,
          channelDescription: 'your channel Description', importance: Importance.high, priority: Priority.high, ticker: 'ticker');
      const DarwinNotificationDetails darwinNotificationDetails =
          DarwinNotificationDetails(presentAlert: true, presentBadge: true, presentSound: true);
      NotificationDetails notificationDetailsBoth = NotificationDetails(android: notificationDetails, iOS: darwinNotificationDetails);
      await FlutterLocalNotificationsPlugin().show(
        id: 0,
        title: message.notification!.title,
        body: message.notification!.body,
        notificationDetails: notificationDetailsBoth,
        payload: jsonEncode(message.data),
      );
    } on Exception catch (e) {
      log(e.toString());
    }
  }
}
