import 'dart:developer';

import 'package:flutter/material.dart';
import 'package:owner/model/driver_user_model.dart';
import 'package:owner/model/payment_model.dart';
import 'package:owner/utils/fire_store_utils.dart';
import 'package:get/get.dart';

class BookingRequestController extends GetxController {
  final LayerLink layerLink = LayerLink();
  GlobalKey overlayKey = GlobalKey();
  RxString bookingType = "City Rides".obs;
  RxList<String> types = <String>[
    'City Rides',
    'OutStation Rides',
    'Freight Rides',
  ].obs;

  void selectType(String type) {
    log("selectType :: ${type}");
    bookingType.value = type;
  }

  @override
  void onInit() {
    // TODO: implement onInit\
    getDriverList();

    super.onInit();
  }

  RxList<DriverUserModel> driverUserList = <DriverUserModel>[].obs;
  Rx<DriverUserModel> selectedDriver = DriverUserModel().obs;
  Rx<DriverUserModel> allDriversOption = DriverUserModel(id: "all", fullName: "Selected All Driver").obs;

  Future<void> getDriverList() async {
    await FireStoreUtils.getAllDriverList().then(
      (value) {
        if (value.isNotEmpty == true) {
          driverUserList.value = value;
        }
      },
    );
    await getPayment();
  }

  Rx<PaymentModel> paymentModel = PaymentModel().obs;
  RxBool isLoading = true.obs;

  Future<void> getPayment() async {
    await FireStoreUtils.getPayment().then((value) {
      if (value != null) {
        paymentModel.value = value;
        isLoading.value = false;
      }
    });
  }
}
