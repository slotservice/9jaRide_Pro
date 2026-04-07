import 'dart:async';
import 'dart:developer';
import 'package:customer/ui/auth_screen/login_screen.dart';
import 'package:customer/ui/dashboard_screen.dart';
import 'package:customer/ui/maintenance_mode_screen/maintenance_mode_screen.dart';
import 'package:customer/ui/on_boarding_screen.dart';
import 'package:customer/utils/Preferences.dart';
import 'package:customer/utils/fire_store_utils.dart';
import 'package:get/get.dart';

class SplashController extends GetxController {
  @override
  void onInit() {
    Timer(const Duration(seconds: 3), () => redirectScreen());
    super.onInit();
  }

  Future<void> redirectScreen() async {
    try {
      if (await FireStoreUtils.isMaintenanceMode() == true) {
        Get.offAll(() => MaintenanceModeScreen());
        return;
      } else {
        if (Preferences.getBoolean(Preferences.notificationPlayload) == true) {
          await Preferences.setBoolean(Preferences.notificationPlayload, false);
          log("Preferences.getBoolean(Preferences.notificationPlayload) :::::: ${Preferences.getBoolean(Preferences.notificationPlayload)}");
        } else {
          if (Preferences.getBoolean(Preferences.isFinishOnBoardingKey) == false) {
            Get.offAll(const OnBoardingScreen());
          } else {
            bool isLogin = await FireStoreUtils.isLogin();
            if (isLogin == true) {
              Get.offAll(const DashBoardScreen());
            } else {
              Get.offAll(const LoginScreen());
            }
          }
        }
      }
    } catch (e) {
      log("Splash redirect error: $e");
      Get.offAll(const LoginScreen());
    }
  }
}
