import 'package:get/get.dart';
import 'package:owner/constant/constant.dart';
import 'package:owner/model/driver_user_model.dart';
import 'package:owner/model/owner_user_model.dart';
import 'package:owner/utils/fire_store_utils.dart';

class AllDriverController extends GetxController {
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
          driverUserList.addAll(value);
        }
      },
    );

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
        }
      }
    }
    await FireStoreUtils.deleteAuthUser(driverModel.id!);
    await FireStoreUtils.deleteDriverById(driverModel.id!).then(
      (value) {
        if (value == true) {
          driverUserList.remove(driverModel);
        }
      },
    );
    isLoading.value = false;
  }
}
