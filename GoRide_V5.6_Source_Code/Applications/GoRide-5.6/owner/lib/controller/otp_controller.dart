import 'package:firebase_auth/firebase_auth.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';

class OtpController extends GetxController {
  Rx<TextEditingController> otpController = TextEditingController().obs;
  RxString countryCode = "".obs;
  RxString countryISOCode = "".obs;
  RxString phoneNumber = "".obs;
  RxString verificationId = "".obs;

  @override
  void onInit() {
    getArgument();
    super.onInit();
  }

  FirebaseAuth? firebaseAuth;
  dynamic argumentData = Get.arguments;

  Future<void> getArgument() async {
    if (argumentData != null) {
      countryCode.value = argumentData['countryCode'];
      countryISOCode.value = argumentData['countryISOCode'];
      phoneNumber.value = argumentData['phoneNumber'];
      verificationId.value = argumentData['verificationId'];
      if (argumentData['driverUserModel'] != null) {
        firebaseAuth = argumentData['driverAccountAuth'];
      } else {
        firebaseAuth = FirebaseAuth.instance;
      }
    }
    update();
  }
}
