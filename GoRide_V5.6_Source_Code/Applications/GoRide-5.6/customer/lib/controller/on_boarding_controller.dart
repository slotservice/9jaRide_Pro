import 'package:customer/model/on_boarding_model.dart';
import 'package:customer/ui/auth_screen/login_screen.dart';
import 'package:customer/utils/fire_store_utils.dart';
import 'package:customer/utils/Preferences.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';

class OnBoardingController extends GetxController {
  var selectedPageIndex = 0.obs;

  bool get isLastPage => selectedPageIndex.value == onBoardingList.length - 1;
  var pageController = PageController();

  @override
  void onInit() {
    getOnBoardingData();
    super.onInit();
  }

  RxBool isLoading = true.obs;
  RxList<OnBoardingModel> onBoardingList = <OnBoardingModel>[].obs;

  getOnBoardingData() async {
    await FireStoreUtils.getOnBoardingList().then((value) {
      onBoardingList.value = value;
      isLoading.value = false;
    }).catchError((e) {
      isLoading.value = false;
    });
    if (onBoardingList.isEmpty) {
      Preferences.setBoolean(Preferences.isFinishOnBoardingKey, true);
      Get.offAll(() => const LoginScreen());
      return;
    }
    update();
  }
}
