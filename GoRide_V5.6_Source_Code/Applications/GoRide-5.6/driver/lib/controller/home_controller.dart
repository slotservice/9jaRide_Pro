import 'dart:developer';
import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:driver/constant/collection_name.dart';
import 'package:driver/constant/constant.dart';
import 'package:driver/constant/send_notification.dart';
import 'package:driver/constant/show_toast_dialog.dart';
import 'package:driver/controller/dash_board_controller.dart';
import 'package:driver/model/order_model.dart';
import 'package:driver/model/user_model.dart';
import 'package:driver/model/driver_user_model.dart';
import 'package:driver/model/order/location_lat_lng.dart';
import 'package:driver/model/order/positions.dart';
import 'package:driver/ui/auth_screen/login_screen.dart';
import 'package:driver/ui/home_screens/accepted_orders.dart';
import 'package:driver/ui/home_screens/active_order_screen.dart';
import 'package:driver/ui/home_screens/new_orders_screen.dart';
import 'package:driver/ui/order_screen/order_screen.dart';
import 'package:driver/utils/fire_store_utils.dart';
import 'package:driver/widget/geoflutterfire/src/geoflutterfire.dart';
import 'package:driver/widget/geoflutterfire/src/models/point.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:location/location.dart';

class HomeController extends GetxController {
  RxInt selectedIndex = 0.obs;
  List<Widget> widgetOptions = <Widget>[const NewOrderScreen(), const AcceptedOrders(), const ActiveOrderScreen(), const OrderScreen()];
  DashBoardController dashboardController = Get.put(DashBoardController());

  void onItemTapped(int index) {
    selectedIndex.value = index;
  }

  @override
  void onInit() {
    getDriver();
    getActiveRide();
    super.onInit();
  }

  Rx<DriverUserModel> driverModel = DriverUserModel().obs;

  RxBool isLoading = true.obs;

  Future<void> getDriver() async {
    FireStoreUtils.fireStore.collection(CollectionName.driverUsers).doc(FireStoreUtils.getCurrentUid()).snapshots().listen((event) {
      if (event.exists) {
        driverModel.value = DriverUserModel.fromJson(event.data()!);
        // Kill switch: force logout if admin locks the driver account
        if (driverModel.value.appLocked == true) {
          FirebaseAuth.instance.signOut();
          ShowToastDialog.showToast(
            'Your account has been locked by admin. Reason: ${driverModel.value.lockReason ?? 'HP payment overdue'}',
          );
          Get.offAllNamed('/login');
          Get.offAll(const LoginScreen());
        }
      }
    }, onError: (error) {
      // Do not fail open on stream errors — surface the problem instead of
      // silently ignoring a possible kill-switch state.
      ShowToastDialog.showToast("Unable to verify account status. Please try again.".tr);
    });

    updateCurrentLocation();
  }

  RxInt isActiveValue = 0.obs;

  // The ride the driver is currently on their way to collect, if any. Held here
  // so the location stream can spot the arrival on its own, without needing the
  // driver to have the tracking screen open.
  Rx<OrderModel?> pickupRide = Rx<OrderModel?>(null);

  // The ride whose stage colours the whole home screen. Null when the driver is
  // between jobs, which is what keeps the screen on the normal brand green
  // rather than leaving it stuck on the last trip's colour.
  Rx<OrderModel?> liveRide = Rx<OrderModel?>(null);

  void getActiveRide() {
    FireStoreUtils.fireStore
        .collection(CollectionName.orders)
        .where('driverId', isEqualTo: FireStoreUtils.getCurrentUid())
        .where('status', whereIn: [Constant.rideInProgress, Constant.rideActive])
        .snapshots()
        .listen((event) {
          isActiveValue.value = event.size;

          OrderModel? headingToPickup;
          OrderModel? inProgress;
          OrderModel? active;
          for (final doc in event.docs) {
            final OrderModel order = OrderModel.fromJson(doc.data());
            if (order.status == Constant.rideInProgress) {
              inProgress ??= order;
            } else if (order.status == Constant.rideActive) {
              active ??= order;
              // Only rides on the way to the pickup, and only until we have
              // already recorded the arrival once.
              if (order.driverArrivedAt == null) {
                headingToPickup ??= order;
              }
            }
          }
          pickupRide.value = headingToPickup;
          // A running trip wins, so the screen stays green for the trip the
          // driver is actually in rather than flipping to a newer request.
          liveRide.value = inProgress ?? active;
        });
  }

  Location location = Location();

  Future<void> updateCurrentLocation() async {
    PermissionStatus permissionStatus = await location.hasPermission();
    if (permissionStatus == PermissionStatus.granted) {
      location.enableBackgroundMode(enable: true);
      location.changeSettings(accuracy: LocationAccuracy.high, distanceFilter: double.parse(Constant.driverLocationUpdate.toString()), interval: 2000);
      location.onLocationChanged.listen(_pushLocation);
    } else {
      location.requestPermission().then((permissionStatus) {
        if (permissionStatus == PermissionStatus.granted) {
          location.enableBackgroundMode(enable: true);
          location.changeSettings(accuracy: LocationAccuracy.high, distanceFilter: double.parse(Constant.driverLocationUpdate.toString()), interval: 2000);
          location.onLocationChanged.listen(_pushLocation);
        }
      });
    }
    isLoading.value = false;
    update();
  }

  // Push the driver's live location to Firestore. Uses the cached driverModel
  // (kept current by the snapshot listener in getDriver) instead of re-reading
  // the whole profile on every tick, and writes ONLY the location fields — much
  // less network traffic on poor connections, and no risk of overwriting
  // walletAmount or other fields that changed elsewhere between a read and write.
  void _pushLocation(LocationData locationData) {
    if (locationData.latitude == null || locationData.longitude == null) return;
    Constant.currentLocation = LocationLatLng(latitude: locationData.latitude, longitude: locationData.longitude);
    if (driverModel.value.isOnline != true) return;
    GeoFirePoint position = Geoflutterfire().point(latitude: locationData.latitude!, longitude: locationData.longitude!);
    FireStoreUtils.updateDriverLocation(FireStoreUtils.getCurrentUid(), {
      'position': Positions(geoPoint: position.geoPoint, geohash: position.hash).toJson(),
      'location': LocationLatLng(latitude: locationData.latitude, longitude: locationData.longitude).toJson(),
      'rotation': locationData.heading,
    });

    _checkArrivalAtPickup(position);
  }

  /// How close the driver has to get before the rider is told they have
  /// arrived. 0.1 km is the 100 metres the client asked for.
  static const double _arrivalRadiusKm = 0.1;

  // Stops a second location tick firing the same notification while the first
  // write is still in flight.
  bool _arrivalCheckInFlight = false;

  /// Only tell the rider the distance again once the driver has actually
  /// covered this much ground. Without it we would write to the order on every
  /// single location tick for the whole approach.
  static const double _distanceWriteStepKm = 0.2;
  double? _lastPublishedDistanceKm;
  String? _distanceOrderId;

  /// Publishes roughly how far the driver still is, so the rider app can say
  /// "driver is 300 metres away" instead of just "driver on the way".
  Future<void> _publishDistanceToRider(String orderId, double km) async {
    // A different ride means the previous figure is meaningless.
    if (_distanceOrderId != orderId) {
      _distanceOrderId = orderId;
      _lastPublishedDistanceKm = null;
    }
    final double? last = _lastPublishedDistanceKm;
    if (last != null && (last - km).abs() < _distanceWriteStepKm) return;
    _lastPublishedDistanceKm = km;
    try {
      await FireStoreUtils.fireStore.collection(CollectionName.orders).doc(orderId).update({'driverDistanceKm': km});
    } catch (e) {
      // Cosmetic only, so never let it break the location stream. Put the old
      // figure back so the next tick retries rather than skipping ahead.
      _lastPublishedDistanceKm = last;
      log("Distance publish error :: $e");
    }
  }

  Future<void> _checkArrivalAtPickup(GeoFirePoint position) async {
    final OrderModel? order = pickupRide.value;
    if (order == null || _arrivalCheckInFlight) return;
    // Firestore's doc() generates a brand new id when handed null, so without
    // this guard a missing id would silently create a stray order document.
    final String orderId = order.id ?? '';
    if (orderId.isEmpty) return;

    final LocationLatLng? pickup = order.sourceLocationLAtLng;
    if (pickup?.latitude == null || pickup?.longitude == null) return;

    final double km = position.kmDistance(lat: pickup!.latitude!, lng: pickup.longitude!);
    await _publishDistanceToRider(orderId, km);

    if (km > _arrivalRadiusKm) return;

    _arrivalCheckInFlight = true;
    try {
      // Update just this one field rather than writing the whole order back.
      // The order here came from a snapshot, so a full write could quietly
      // revert anything the rider changed in the meantime.
      await FireStoreUtils.fireStore.collection(CollectionName.orders).doc(orderId).update({'driverArrivedAt': Timestamp.now()});
      // Clear locally so we do not fire again in the window before the
      // snapshot listener catches up with the write.
      pickupRide.value = null;

      final UserModel? customer = await FireStoreUtils.getCustomer(order.userId.toString());
      if (customer?.fcmToken != null) {
        await SendNotification.sendOneNotification(
          token: customer!.fcmToken.toString(),
          title: 'Your driver has arrived'.tr,
          body: 'Your driver is at the pickup point and waiting for you.'.tr,
          payload: <String, dynamic>{"type": "city_order", "orderId": orderId},
        );
      }
    } catch (e) {
      // Never let this break the location stream. driverArrivedAt stays unset
      // on a failed write, so the next location tick simply tries again.
      log("Arrival check error :: $e");
    } finally {
      _arrivalCheckInFlight = false;
    }
  }
}
