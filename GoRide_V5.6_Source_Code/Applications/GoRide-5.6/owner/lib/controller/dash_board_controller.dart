import 'package:owner/constant/constant.dart';
import 'package:owner/constant/show_toast_dialog.dart';
import 'package:owner/model/owner_user_model.dart';
import 'package:owner/ui/auth_screen/login_screen.dart';
import 'package:owner/ui/bank_details/bank_details_screen.dart';
import 'package:owner/ui/help_support_screen/help_support_screen.dart';
import 'package:owner/ui/home_screens/home_dashbord.dart';
import 'package:owner/ui/online_registration/online_registartion_screen.dart';
import 'package:owner/ui/settings_screen/setting_screen.dart';
import 'package:owner/ui/subscription_plan_screen/subscription_history.dart';
import 'package:owner/ui/subscription_plan_screen/subscription_list_screen.dart';
import 'package:owner/utils/fire_store_utils.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';

class DashBoardController extends GetxController {
  RxList<DrawerItem> drawerItems = <DrawerItem>[].obs;

  Widget getDrawerItemWidget(int pos) {
    if (Constant.isVerifyOwnerDocument == true) {
      if (Constant.isSubscriptionModelApplied == true) {
        switch (pos) {
          case 0:
            return const HomeDashboardScreen();
          case 1:
            return const BankDetailsScreen();
          case 2:
            return const OnlineRegistrationScreen();
          case 3:
            return const SettingScreen();
          case 4:
            return const SubscriptionListScreen();
          case 5:
            return const SubscriptionHistory();
          case 6:
            return HelpSupportScreen(
              userId: ownerUser.value.id,
              userName: ownerUser.value.fullName,
              userProfileImage: ownerUser.value.profilePic,
              token: ownerUser.value.fcmToken,
              isShowAppbar: false,
            );
          default:
            return const Text("Error");
        }
      } else {
        switch (pos) {
          case 0:
            return const HomeDashboardScreen();
          case 1:
            return const BankDetailsScreen();
          case 2:
            return const OnlineRegistrationScreen();
          case 3:
            return const SettingScreen();
          case 4:
            return const SubscriptionHistory();
          case 5:
            return HelpSupportScreen(
              userId: ownerUser.value.id,
              userName: ownerUser.value.fullName,
              userProfileImage: ownerUser.value.profilePic,
              token: ownerUser.value.fcmToken,
              isShowAppbar: false,
            );
          default:
            return const Text("Error");
        }
      }
    } else {
      if (Constant.isSubscriptionModelApplied == true) {
        switch (pos) {
          case 0:
            return const HomeDashboardScreen();
          case 1:
            return const BankDetailsScreen();
          case 2:
            return const SettingScreen();
          case 3:
            return const SubscriptionListScreen();
          case 4:
            return const SubscriptionHistory();
          case 5:
            return HelpSupportScreen(
              userId: ownerUser.value.id,
              userName: ownerUser.value.fullName,
              userProfileImage: ownerUser.value.profilePic,
              token: ownerUser.value.fcmToken,
              isShowAppbar: false,
            );
          default:
            return const Text("Error");
        }
      } else {
        switch (pos) {
          case 0:
            return const HomeDashboardScreen();
          case 1:
            return const BankDetailsScreen();
          case 2:
            return const SettingScreen();
          case 3:
            return const SubscriptionHistory();
          case 4:
            return HelpSupportScreen(
              userId: ownerUser.value.id,
              userName: ownerUser.value.fullName,
              userProfileImage: ownerUser.value.profilePic,
              token: ownerUser.value.fcmToken,
              isShowAppbar: false,
            );
          default:
            return const Text("Error");
        }
      }
    }
  }

  RxInt selectedDrawerIndex = 0.obs;

  Future<void> onSelectItem(int index) async {
    if (Constant.isVerifyOwnerDocument == true) {
      if (Constant.isSubscriptionModelApplied == true ? index == 7 : index == 6) {
        await FirebaseAuth.instance.signOut();
        Get.offAll(const LoginScreen());
      } else {
        selectedDrawerIndex.value = index;
      }
    } else {
      if (Constant.isSubscriptionModelApplied == true ? index == 6 : index == 5) {
        await FirebaseAuth.instance.signOut();
        Get.offAll(const LoginScreen());
      } else {
        selectedDrawerIndex.value = index;
      }
    }
    Get.back();
  }

  @override
  void onInit() {
    getOwnerProfile();
    super.onInit();
  }

  Rx<OwnerUserModel> ownerUser = OwnerUserModel().obs;
  RxBool isLoading = true.obs;
  Future<void> getOwnerProfile() async {
    setDrawerList();
    await FireStoreUtils.getOwnerProfile(FireStoreUtils.getCurrentUid()).then((driver) {
      if (driver?.id != null) {
        ownerUser.value = driver!;
      }
      isLoading.value = false;
    });
  }

  void setDrawerList() {
    if (Constant.isVerifyOwnerDocument == true) {
      if (Constant.isSubscriptionModelApplied == true) {
        drawerItems.value = [
          DrawerItem('Dashboard', "assets/icons/ic_city.svg"),
          DrawerItem('Bank Details', "assets/icons/ic_profile.svg"),
          DrawerItem('Online Registration', "assets/icons/ic_document.svg"),
          DrawerItem('Settings', "assets/icons/ic_settings.svg"),
          DrawerItem('Subscription', "assets/icons/ic_subscription.svg"),
          DrawerItem('Subscription History', "assets/icons/ic_subscription_history.svg"),
          DrawerItem('Help & Support', "assets/icons/ic_help_support.svg"),
          DrawerItem('Log out', "assets/icons/ic_logout.svg"),
        ];
      } else {
        drawerItems.value = [
          DrawerItem('Dashboard', "assets/icons/ic_city.svg"),
          DrawerItem('Bank Details', "assets/icons/ic_profile.svg"),
          DrawerItem('Online Registration', "assets/icons/ic_document.svg"),
          DrawerItem('Settings', "assets/icons/ic_settings.svg"),
          DrawerItem('Subscription History', "assets/icons/ic_subscription_history.svg"),
          DrawerItem('Help & Support', "assets/icons/ic_help_support.svg"),
          DrawerItem('Log out', "assets/icons/ic_logout.svg"),
        ];
      }
    } else {
      if (Constant.isSubscriptionModelApplied == true) {
        drawerItems.value = [
          DrawerItem('Dashboard', "assets/icons/ic_city.svg"),
          DrawerItem('Bank Details', "assets/icons/ic_profile.svg"),
          DrawerItem('Settings', "assets/icons/ic_settings.svg"),
          DrawerItem('Subscription', "assets/icons/ic_subscription.svg"),
          DrawerItem('Subscription History', "assets/icons/ic_subscription_history.svg"),
          DrawerItem('Help & Support', "assets/icons/ic_help_support.svg"),
          DrawerItem('Log out', "assets/icons/ic_logout.svg"),
        ];
      } else {
        drawerItems.value = [
          DrawerItem('Dashboard', "assets/icons/ic_city.svg"),
          DrawerItem('Bank Details', "assets/icons/ic_profile.svg"),
          DrawerItem('Settings', "assets/icons/ic_settings.svg"),
          DrawerItem('Subscription History', "assets/icons/ic_subscription_history.svg"),
          DrawerItem('Help & Support', "assets/icons/ic_help_support.svg"),
          DrawerItem('Log out', "assets/icons/ic_logout.svg"),
        ];
      }
    }
  }

  Rx<DateTime> currentBackPressTime = DateTime.now().obs;

  Future<bool> onWillPop() {
    DateTime now = DateTime.now();
    if (now.difference(currentBackPressTime.value) > const Duration(seconds: 2)) {
      currentBackPressTime.value = now;
      ShowToastDialog.showToast(
        "Double press to exit".tr,
      );
      return Future.value(false);
    }
    return Future.value(true);
  }
}

class DrawerItem {
  String title;
  String icon;

  DrawerItem(this.title, this.icon);
}
