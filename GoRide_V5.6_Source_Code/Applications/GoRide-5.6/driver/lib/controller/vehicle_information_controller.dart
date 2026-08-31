import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:driver/constant/constant.dart';
import 'package:driver/constant/show_toast_dialog.dart';
import 'package:driver/model/driver_rules_model.dart';
import 'package:driver/model/driver_user_model.dart';
import 'package:driver/model/service_model.dart';
import 'package:driver/model/zone_model.dart';
import 'package:driver/themes/app_colors.dart';
import 'package:driver/ui/dashboard_screen.dart';
import 'package:driver/utils/fire_store_utils.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';

class VehicleInformationController extends GetxController {
  Rx<TextEditingController> vehicleNumberController = TextEditingController().obs;
  Rx<TextEditingController> seatsController = TextEditingController().obs;
  Rx<TextEditingController> registrationDateController = TextEditingController().obs;
  Rx<TextEditingController> driverRulesController = TextEditingController().obs;
  Rx<TextEditingController> zoneNameController = TextEditingController().obs;
  RxList<TextEditingController> acPerKmRate = <TextEditingController>[].obs;
  RxList<TextEditingController> nonAcPerKmRate = <TextEditingController>[].obs;
  RxList<TextEditingController> acNonAcWithoutPerKmRate = <TextEditingController>[].obs;
  Rx<DateTime?> selectedDate = DateTime.now().obs;

  RxBool isLoading = true.obs;

  // Vehicle ownership the driver declares ('personal' or 'hire_purchase') and
  // whether they opt in to carrying riders who need special assistance. Both are
  // set at registration and stay editable here so a mistake can be corrected.
  RxString driverType = 'personal'.obs;
  RxBool canAssist = false.obs;

  Rx<String> selectedColor = "".obs;
  List<String> carColorList = <String>['Red', 'Black', 'White', 'Blue', 'Green', 'Orange', 'Silver', 'Gray', 'Yellow', 'Brown', 'Gold', 'Beige', 'Purple'].obs;
  List<String> sheetList = <String>['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15'].obs;

  @override
  void onInit() {
    // TODO: implement onInit
    getVehicleTye();
    super.onInit();
  }

  var colors = [
    AppColors.serviceColor1,
    AppColors.serviceColor2,
    AppColors.serviceColor3,
  ];
  Rx<DriverUserModel> driverModel = DriverUserModel().obs;
  RxList<DriverRulesModel> driverRulesList = <DriverRulesModel>[].obs;
  RxList<DriverRulesModel> selectedDriverRulesList = <DriverRulesModel>[].obs;

  RxList<ServiceModel> serviceList = <ServiceModel>[].obs;
  Rx<ServiceModel> selectedServiceType = ServiceModel().obs;
  RxList<ZoneModel> zoneAllList = <ZoneModel>[].obs;
  RxList<ZoneModel> zoneList = <ZoneModel>[].obs;
  RxList selectedTempZone = <String>[].obs;
  RxList selectedZone = <String>[].obs;
  RxString zoneString = "".obs;
  RxList<Price> selectedPrices = <Price>[].obs;

  Future<void> getVehicleTye() async {
    await FireStoreUtils.getService().then((value) {
      serviceList.value = value.where((s) => s.type != 'assist').toList();
    });

    await FireStoreUtils.getZone().then((value) {
      if (value != null) {
        zoneAllList.value = value;
      }
    });

    await FireStoreUtils.getDriverProfile(FireStoreUtils.getCurrentUid()).then((value) async {
      if (value != null) {
        driverModel.value = value;
        driverType.value = value.driverType ?? 'personal';
        canAssist.value = value.canAssist ?? false;
        if (driverModel.value.vehicleInformation != null) {
          vehicleNumberController.value.text = driverModel.value.vehicleInformation!.vehicleNumber.toString();
          selectedDate.value = driverModel.value.vehicleInformation!.registrationDate!.toDate();
          registrationDateController.value.text = DateFormat("dd-MM-yyyy").format(selectedDate.value!);
          selectedColor.value = driverModel.value.vehicleInformation!.vehicleColor.toString();
          seatsController.value.text = driverModel.value.vehicleInformation!.seats ?? "2";
          selectedServiceType.value = await FireStoreUtils.getServiceById(driverModel.value.serviceId);
          zoneList.clear();
          final priceIds = (selectedServiceType.value.prices ?? []).map((p) => p.zoneId).toSet();
          zoneList.addAll(zoneAllList.where((z) => priceIds.contains(z.id)));
          if (driverModel.value.zoneIds != null) {
            if (zoneList.isNotEmpty) {
              for (var element in zoneList) {
                if (driverModel.value.zoneIds?.contains(element.id.toString()) == true) {
                  zoneString.value = "${zoneString.value}${zoneString.value.isEmpty ? "" : ","} ${Constant.localizationName(element.name)}";
                  selectedZone.add(element.id);
                }
              }
            }
            zoneNameController.value.text = zoneString.value;
            selectedPrices.value = selectedServiceType.value.prices?.where((price) => selectedZone.contains(price.zoneId)).toList() ?? <Price>[];
            acPerKmRate.value = List.generate(selectedPrices.length, (index) => TextEditingController());
            nonAcPerKmRate.value = List.generate(selectedPrices.length, (index) => TextEditingController());
            acNonAcWithoutPerKmRate.value = List.generate(selectedPrices.length, (index) => TextEditingController());

            for (int index = 0; index < driverModel.value.vehicleInformation!.rates!.length; index++) {
              if (driverModel.value.vehicleInformation!.rates?[index].acPerKmRate != null) {
                acPerKmRate[index].text = driverModel.value.vehicleInformation!.rates?[index].acPerKmRate ?? '';
                acNonAcWithoutPerKmRate[index].text = driverModel.value.vehicleInformation!.rates?[index].perKmRate ?? '';
                nonAcPerKmRate[index].text = driverModel.value.vehicleInformation!.rates?[index].nonAcPerKmRate ?? '';
              } else {
                nonAcPerKmRate[index].text = driverModel.value.vehicleInformation!.rates?[index].nonAcPerKmRate ?? '';
                acNonAcWithoutPerKmRate[index].text = driverModel.value.vehicleInformation!.rates?[index].perKmRate ?? '';
              }
            }
            // Overrides whatever the driver had saved. The rate belongs to the
            // service, not to the driver, so an older stored figure must not
            // survive a price change.
            fillRatesFromService();
          }
          tabBarheight.value = selectedPrices.isNotEmpty && selectedPrices.first.isAcNonAc == true ? 200 : 100;
        }
        if (driverModel.value.zoneIds == null) {
          if (serviceList.isNotEmpty) {
            selectedServiceType.value = serviceList.first;
            getZone();
          }
        }
      }
    });

    await FireStoreUtils.getDriverRules().then((value) {
      if (value != null) {
        driverRulesList.value = value;
        if (driverModel.value.vehicleInformation != null) {
          if (driverModel.value.vehicleInformation!.driverRules != null) {
            for (var element in driverModel.value.vehicleInformation!.driverRules!) {
              selectedDriverRulesList.add(element);
            }
          }
        }
      }
    });
    isLoading.value = false;
    update();
  }

  void getZone() {
    selectedZone.value = <String>[];
    zoneNameController.value.text = '';
    selectedPrices.clear();
    zoneList.clear();
    final priceIds = (selectedServiceType.value.prices ?? []).map((p) => p.zoneId).toSet();
    zoneList.addAll(zoneAllList.where((z) => priceIds.contains(z.id)));
  }

  void setVehicleDetails() {
    if (driverModel.value.serviceId == null) {
      driverModel.value.serviceId = selectedServiceType.value.id;
      driverModel.value.serviceName = selectedServiceType.value.title;
    }
    driverModel.value.zoneIds = selectedZone;
    // Driver's own declaration. hpEnabled and the hire purchase deduction
    // amounts are intentionally NOT touched here — those stay admin-controlled.
    driverModel.value.driverType = driverType.value;
    driverModel.value.canAssist = canAssist.value;
    List<RateModel>? rates = <RateModel>[];
    for (int index = 0; index < selectedPrices.length; index++) {
      rates.add(RateModel(
        acPerKmRate: acPerKmRate[index].value.text,
        nonAcPerKmRate: nonAcPerKmRate[index].text,
        perKmRate: acNonAcWithoutPerKmRate[index].text,
        zoneId: selectedPrices[index].zoneId,
      ));
    }

    driverModel.value.vehicleInformation = VehicleInformation(
        registrationDate: Timestamp.fromDate(selectedDate.value!),
        vehicleColor: selectedColor.value,
        vehicleNumber: vehicleNumberController.value.text,
        seats: seatsController.value.text,
        driverRules: selectedDriverRulesList,
        rates: rates);
  }

  /// Fills the per km rate fields from the service's own price for each zone.
  ///
  /// The rate belongs to the company, not to the driver. The client's rule is
  /// that every driver on a service charges the same rate wherever they work.
  /// The vendor design let each driver type their own figure at registration,
  /// validated only as greater than zero and no higher than the service rate,
  /// so a driver could put themselves on 10 naira a kilometre and every ride
  /// they completed would settle at that. The fields are shown read only, so
  /// this is the only thing that ever writes them.
  ///
  /// Guarded on length because the three controller lists are rebuilt from
  /// selectedPrices and must never be indexed past it.
  void fillRatesFromService() {
    for (int index = 0; index < selectedPrices.length; index++) {
      final Price price = selectedPrices[index];
      if (index < acPerKmRate.length) acPerKmRate[index].text = price.acCharge ?? '';
      if (index < nonAcPerKmRate.length) nonAcPerKmRate[index].text = price.nonAcCharge ?? '';
      if (index < acNonAcWithoutPerKmRate.length) acNonAcWithoutPerKmRate[index].text = price.kmCharge ?? '';
    }
  }

  Future<void> saveDetails() async {
    setVehicleDetails();
    await FireStoreUtils.updateDriverUser(driverModel.value).then((value) {
      ShowToastDialog.closeLoader();
      if (value == true) {
        ShowToastDialog.showToast("Information update successfully".tr);
        Get.offAll(const DashBoardScreen());
      }
    });
  }

  RxDouble tabBarheight = 200.0.obs;
}
