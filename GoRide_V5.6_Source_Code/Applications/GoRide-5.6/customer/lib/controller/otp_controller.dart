import 'package:flutter/material.dart';
import 'package:get/get.dart';

class OtpController extends GetxController {
  Rx<TextEditingController> otpController = TextEditingController().obs;

  RxString countryCode = "".obs;
  RxString countryISOCode = "".obs;
  RxString phoneNumber = "".obs;
  RxString pinId = "".obs;

  @override
  void onInit() {
    getArgument();
    super.onInit();
  }

  Future<void> getArgument() async {
    dynamic argumentData = Get.arguments;
    if (argumentData != null) {
      countryCode.value = argumentData['countryCode'] ?? '';
      countryISOCode.value = argumentData['countryISOCode'] ?? '';
      phoneNumber.value = argumentData['phoneNumber'] ?? '';
      pinId.value = argumentData['pinId'] ?? '';
    }
    update();
  }
}
