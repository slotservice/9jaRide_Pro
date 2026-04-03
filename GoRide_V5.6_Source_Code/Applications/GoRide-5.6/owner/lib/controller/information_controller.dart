import 'dart:developer';
import 'package:owner/constant/constant.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:owner/model/owner_user_model.dart';

class InformationController extends GetxController {
  Rx<TextEditingController> fullNameController = TextEditingController().obs;
  Rx<TextEditingController> emailController = TextEditingController().obs;
  Rx<TextEditingController> phoneNumberController = TextEditingController().obs;
  Rx<TextEditingController> countryCode = TextEditingController(text: Constant.defaultCountryCode).obs;
  Rx<TextEditingController> countryISOCode = TextEditingController(text: Constant.defaultCountryCode).obs;
  RxString loginType = "".obs;

  @override
  void onInit() {
    getArgument();
    super.onInit();
  }

  Rx<OwnerUserModel> userModel = OwnerUserModel().obs;

  Future<void> getArgument() async {
    dynamic argumentData = Get.arguments;
    if (argumentData != null) {
      userModel.value = argumentData['userModel'];
      loginType.value = userModel.value.loginType.toString();
      if (loginType.value == Constant.phoneLoginType) {
        phoneNumberController.value.text = userModel.value.phoneNumber.toString();
        countryCode.value.text = userModel.value.countryCode.toString();
        countryISOCode.value.text = userModel.value.countryISOCode.toString();
      } else {
        emailController.value.text = userModel.value.email ?? '';
        fullNameController.value.text = userModel.value.fullName ?? '';
      }
      log("------->${loginType.value}");
    }
    update();
  }
}
