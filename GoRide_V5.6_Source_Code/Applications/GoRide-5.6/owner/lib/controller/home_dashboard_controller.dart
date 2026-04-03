import 'dart:developer';

import 'package:owner/constant/constant.dart';
import 'package:owner/constant/show_toast_dialog.dart';
import 'package:owner/model/driver_user_model.dart';
import 'package:owner/model/owner_user_model.dart';
import 'package:owner/model/wallet_transaction_model.dart';
import 'package:owner/utils/fire_store_utils.dart';
import 'package:get/get.dart';

class HomeController extends GetxController {
  @override
  void onInit() {
    getDriverList();
    super.onInit();
  }

  RxBool isLoading = true.obs;
  RxList<DriverUserModel> driverUserList = <DriverUserModel>[].obs;

  Future<void> getDriverList() async {
    await FireStoreUtils.getAllDriverList().then(
      (value) {
        if (value.isNotEmpty == true) {
          driverUserList.value = value;
        }
      },
    );
    await getTotalRide();
    await getWalletTraction();
    await getOwnerProfile();
    isLoading.value = false;
  }

  Future<void> deleteDriver({required DriverUserModel driverModel}) async {
    OwnerUserModel? ownerUserModel = await FireStoreUtils.getOwnerProfile(FireStoreUtils.getCurrentUid());
    if (ownerUserModel?.subscriptionTotalDrivers != "-1") {
      if (Constant.isSubscriptionModelApplied == true || Constant.adminCommission?.isEnabled == true) {
        if ((ownerUserModel?.subscriptionExpiryDate != null && ownerUserModel?.subscriptionExpiryDate!.toDate().isBefore(DateTime.now()) == false) ||
            ownerUserModel?.subscriptionPlan?.expiryDay == '-1') {
          ownerUserModel?.subscriptionTotalDrivers = (int.parse(ownerUserModel.subscriptionTotalDrivers.toString()) + 1).toString();
          await FireStoreUtils.updateOwnerUser(ownerUserModel!);
          ownerUser.value.subscriptionTotalDrivers = ownerUserModel.subscriptionTotalDrivers;
        }
      }
    }

    await FireStoreUtils.deleteAuthUser(driverModel.id!);
    ShowToastDialog.showLoader("Please wait".tr);
    await FireStoreUtils.deleteDriverById(driverModel.id!).then(
      (value) {
        if (value == true) {
          driverUserList.remove(driverModel);
        }
      },
    );

    ShowToastDialog.closeLoader();
  }

  RxInt totalRide = 0.obs;
  Future<void> getTotalRide() async {
    totalRide.value = 0;

    await FireStoreUtils.getAllCityRide().then(
      (value) {
        if (value.isNotEmpty) {
          totalRide.value += value.length;
        }
      },
    );
    await FireStoreUtils.getAllIntercityRide().then(
      (value) {
        if (value.isNotEmpty) {
          totalRide.value += value.length;
        }
      },
    );
  }

  RxList<WalletTransactionModel> transactionWalletList = <WalletTransactionModel>[].obs;
  RxDouble totalWalletBalance = 0.0.obs;
  Future<void> getWalletTraction() async {
    await FireStoreUtils.getWeekWalletTransaction().then((value) {
      totalWalletBalance.value = 0.0;
      if (value != null) {
        transactionWalletList.value = value;
        for (var wallet in transactionWalletList) {
          if (Constant.isNegative(double.parse(wallet.amount.toString())) == false) {
            totalWalletBalance.value = totalWalletBalance.value + (double.parse(wallet.amount ?? '0.0'));
          }
        }
      }
    });
  }

  Rx<OwnerUserModel> ownerUser = OwnerUserModel().obs;
  Future<void> getOwnerProfile() async {
    await FireStoreUtils.getOwnerProfile(FireStoreUtils.getCurrentUid()).then((driver) {
      if (driver?.id != null) {
        ownerUser.value = driver!;
        log("OwnerProfile :: ${ownerUser.value.subscriptionPlan?.driverLimit}");
      }
    });
  }
}
