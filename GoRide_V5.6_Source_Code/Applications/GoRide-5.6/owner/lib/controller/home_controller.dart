import 'package:owner/controller/dash_board_controller.dart';
import 'package:owner/model/owner_user_model.dart';
import 'package:owner/ui/home_screens/home.dart';
import 'package:owner/ui/order_screen/booking_request_screen.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:owner/ui/profile_screen/profile_screen.dart';
import 'package:owner/ui/wallet/wallet_screen.dart';
import 'package:owner/utils/fire_store_utils.dart';

class HomeDashboardController extends GetxController {
  RxInt selectedIndex = 0.obs;
  RxList<Widget> widgetOptions = <Widget>[Home(), BookingRequestScreen(), const WalletScreen(), const ProfileScreen()].obs;
  RxBool isLoading = true.obs;
  DashBoardController dashboardController = Get.put(DashBoardController());

  void onItemTapped(int index) {
    selectedIndex.value = index;
  }

  @override
  void onInit() {
    getOwnerProfile();
    super.onInit();
  }

  Rx<OwnerUserModel> ownerUser = OwnerUserModel().obs;
  Future<void> getOwnerProfile() async {
    await FireStoreUtils.getOwnerProfile(FireStoreUtils.getCurrentUid()).then((driver) {
      if (driver?.id != null) {
        ownerUser.value = driver!;
      }
    });
    isLoading.value = false;
  }
}
