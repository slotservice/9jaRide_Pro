import 'dart:convert';

import 'package:customer/constant/collection_name.dart';
import 'package:customer/constant/constant.dart';
import 'package:customer/controller/dash_board_controller.dart';
import 'package:customer/model/contact_model.dart';
import 'package:customer/model/freight_vehicle.dart';
import 'package:customer/model/intercity_service_model.dart';
import 'package:customer/model/order/location_lat_lng.dart';
import 'package:customer/model/payment_model.dart';
import 'package:customer/model/surage_zone_model.dart';
import 'package:customer/model/zone_model.dart';
import 'package:customer/themes/app_colors.dart';
import 'package:customer/utils/Preferences.dart';
import 'package:customer/utils/fire_store_utils.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:image_picker/image_picker.dart';

class InterCityController extends GetxController {
  DashBoardController dashboardController = Get.put(DashBoardController());

  Rx<TextEditingController> sourceCityController = TextEditingController().obs;
  Rx<TextEditingController> sourceLocationController = TextEditingController().obs;
  Rx<LocationLatLng> sourceLocationLAtLng = LocationLatLng().obs;

  Rx<TextEditingController> destinationCityController = TextEditingController().obs;
  Rx<TextEditingController> destinationLocationController = TextEditingController().obs;
  Rx<LocationLatLng> destinationLocationLAtLng = LocationLatLng().obs;

  Rx<TextEditingController> parcelWeight = TextEditingController().obs;
  Rx<TextEditingController> parcelDimension = TextEditingController().obs;

  Rx<TextEditingController> noOfPassengers = TextEditingController().obs;
  Rx<TextEditingController> offerYourRateController = TextEditingController().obs;
  Rx<TextEditingController> whenController = TextEditingController().obs;
  Rx<TextEditingController> commentsController = TextEditingController().obs;

  RxList<IntercityServiceModel> intercityService = <IntercityServiceModel>[].obs;
  RxList<FreightVehicle> frightVehicleList = <FreightVehicle>[].obs;
  Rx<IntercityServiceModel> selectedInterCityType = IntercityServiceModel().obs;
  Rx<FreightVehicle> selectedFreightVehicle = FreightVehicle().obs;
  RxList zoneList = <ZoneModel>[].obs;
  Rx<ZoneModel> selectedZone = ZoneModel().obs;

  Rx<bool> loaderNeeded = false.obs;

  DateTime? dateAndTime;

  RxList<XFile> images = <XFile>[].obs;

  var colors = [
    AppColors.serviceColor1,
    AppColors.serviceColor2,
    AppColors.serviceColor3,
  ];

  @override
  void onInit() {
    // TODO: implement onInit
    getPaymentData();
    getIntercityService();

    super.onInit();
  }

  RxBool isLoading = true.obs;

  Future<void> getIntercityService() async {
    await FireStoreUtils.getIntercityService().then((value) {
      intercityService.value = value;
      if (intercityService.isNotEmpty) {
        selectedInterCityType.value = intercityService.first;
      }
    });
    await FireStoreUtils.getFreightVehicle().then((value) {
      frightVehicleList.value = value;
      // if (frightVehicleList.isNotEmpty) {
      //   selectedFreightVehicle.value = frightVehicleList.first;
      // }
    });
    getSurageZone();
    isLoading.value = false;
  }

  RxList<SurgeZonesModel> surgeZoneList = <SurgeZonesModel>[].obs;

  void getSurageZone() {
    FireStoreUtils.fireStore.collection(CollectionName.surgeZones).snapshots().listen((value) {
      for (var element in value.docs) {
        SurgeZonesModel model = SurgeZonesModel.fromJson(element.data());
        if (model.publish == true) {
          if (!surgeZoneList.any((element) => element.id == model.id)) {
            surgeZoneList.add(model);
          } else {
            int index = surgeZoneList.indexWhere((element) => element.id == model.id);
            surgeZoneList[index] = model;
          }
        } else {
          surgeZoneList.removeWhere((element) => element.id == model.id);
        }
      }
    });
  }

  RxDouble surgeMultiplier = 1.0.obs;
  RxBool isSurge = false.obs;

  void checkSurgeZoneList() {
    print("Checking surge zones...");
    print("Total surge zones: ${surgeZoneList.length}");
    if (surgeZoneList.isEmpty) {
      resetSurge();
      return;
    }
    print("sourceLocationLAtLng :: ${sourceLocationLAtLng.value.latitude} :: ${sourceLocationLAtLng.value.longitude}");

    final lat = sourceLocationLAtLng.value.latitude;
    final lng = sourceLocationLAtLng.value.longitude;

    if (lat == null || lng == null) {
      resetSurge();
      return;
    }

    for (final zone in surgeZoneList) {
      if (Constant.isPointInsideZone(lat, lng, zone.area ?? [])) {
        isSurge.value = true;
        surgeMultiplier.value = (zone.surgeMultiplier ?? 1.0).toDouble();

        print("matchedZone :: ${zone.id}");
        return; // stop immediately
      }
    }
    // If no zone matched
    resetSurge();
  }

  void resetSurge() {
    isSurge.value = false;
    surgeMultiplier.value = 1.0;
  }

  Rx<PaymentModel> paymentModel = PaymentModel().obs;

  RxString selectedPaymentMethod = "".obs;

  Future<void> getPaymentData() async {
    await FireStoreUtils.getZone().then((value) {
      if (value != null) {
        zoneList.value = value;
      }
    });
    await FireStoreUtils.getPayment().then((value) {
      if (value != null) {
        paymentModel.value = value;
      }
    });
  }

  RxString duration = "".obs;
  RxString distance = "".obs;
  RxString amount = "".obs;

  Future<void> calculateOsmAmount() async {
    print("${sourceLocationLAtLng.value.latitude}::: duration sourceLocationLAtLng :::${sourceLocationLAtLng.value.longitude}");
    print(
        "${destinationLocationLAtLng.value.latitude}::: duration destinationLocationLAtLng :::${destinationLocationLAtLng.value.longitude}");
    if (selectedInterCityType.value.id == "Kn2VEnPI3ikF58uK8YqY") {
      if (selectedFreightVehicle.value.id == null) {
        amount.value = "0.0";
        offerYourRateController.value.text = "0.0";
      } else {
        if (sourceLocationLAtLng.value.latitude != null && destinationLocationLAtLng.value.latitude != null) {
          await Constant.getDurationOsmDistance(LatLng(sourceLocationLAtLng.value.latitude!, sourceLocationLAtLng.value.longitude!),
                  LatLng(destinationLocationLAtLng.value.latitude!, destinationLocationLAtLng.value.longitude!))
              .then((value) {
            if (value != {} && value.isNotEmpty) {
              int hours = value['routes'].first['duration'] ~/ 3600;
              int minutes = ((value['routes'].first['duration'] % 3600) / 60).round();
              duration.value = '$hours hours $minutes minutes';

              if (Constant.distanceType == "Km") {
                distance.value = (value['routes'].first['distance'] / 1000).toString();
                amount.value = Constant.amountCalculate(selectedFreightVehicle.value.kmCharge.toString(), distance.value)
                    .toStringAsFixed(Constant.currencyModel!.decimalDigits!);
                amount.value = (double.parse(amount.value) * surgeMultiplier.value).toStringAsFixed(Constant.currencyModel!.decimalDigits!);
                offerYourRateController.value.text = amount.value;
              } else {
                distance.value = (value['routes'].first['distance'] / 1609.34).toString();
                amount.value = Constant.amountCalculate(selectedFreightVehicle.value.kmCharge.toString(), distance.value)
                    .toStringAsFixed(Constant.currencyModel!.decimalDigits!);
                amount.value = (double.parse(amount.value) * surgeMultiplier.value).toStringAsFixed(Constant.currencyModel!.decimalDigits!);
                offerYourRateController.value.text = amount.value;
              }
            }
          });
        }
      }
    } else {
      amount.value = "0.0";
      offerYourRateController.value.text = "0.0";
      if (sourceLocationLAtLng.value.latitude != null && destinationLocationLAtLng.value.latitude != null) {
        await Constant.getDurationOsmDistance(LatLng(sourceLocationLAtLng.value.latitude!, sourceLocationLAtLng.value.longitude!),
                LatLng(destinationLocationLAtLng.value.latitude!, destinationLocationLAtLng.value.longitude!))
            .then((value) {
          if (value != {} && value.isNotEmpty) {
            int hours = value['routes'].first['duration'] ~/ 3600;
            int minutes = ((value['routes'].first['duration'] % 3600) / 60).round();
            duration.value = '$hours hours $minutes minutes';
            if (Constant.distanceType == "Km") {
              distance.value = (value['routes'].first['distance'] / 1000).toString();
              amount.value = Constant.amountCalculate(selectedInterCityType.value.kmCharge.toString(), distance.value)
                  .toStringAsFixed(Constant.currencyModel!.decimalDigits!);
              amount.value = (double.parse(amount.value) * surgeMultiplier.value).toStringAsFixed(Constant.currencyModel!.decimalDigits!);
              offerYourRateController.value.text = amount.value;
            } else {
              distance.value = (value['routes'].first['distance'] / 1609.34).toString();
              amount.value = Constant.amountCalculate(selectedInterCityType.value.kmCharge.toString(), distance.value)
                  .toStringAsFixed(Constant.currencyModel!.decimalDigits!);
              amount.value = (double.parse(amount.value) * surgeMultiplier.value).toStringAsFixed(Constant.currencyModel!.decimalDigits!);
              offerYourRateController.value.text = amount.value;
            }
          }
        });
      }
    }
  }

  Future<void> calculateAmount() async {
    if (selectedInterCityType.value.id == "Kn2VEnPI3ikF58uK8YqY") {
      if (selectedFreightVehicle.value.id == null) {
        amount.value = "0.0";
        offerYourRateController.value.text = "0.0";
      } else {
        if (sourceLocationLAtLng.value.latitude != null && destinationLocationLAtLng.value.latitude != null) {
          await Constant.getDurationDistance(LatLng(sourceLocationLAtLng.value.latitude!, sourceLocationLAtLng.value.longitude!),
                  LatLng(destinationLocationLAtLng.value.latitude!, destinationLocationLAtLng.value.longitude!))
              .then((value) {
            if (value != null) {
              duration.value = value.rows!.first.elements!.first.duration!.text.toString();
              print("duration  :: 11 :: ${duration.value}");
              if (Constant.distanceType == "Km") {
                distance.value = (value.rows!.first.elements!.first.distance!.value!.toInt() / 1000).toString();
                amount.value = Constant.amountCalculate(selectedFreightVehicle.value.kmCharge.toString(), distance.value)
                    .toStringAsFixed(Constant.currencyModel!.decimalDigits!);
                amount.value = (double.parse(amount.value) * surgeMultiplier.value).toStringAsFixed(Constant.currencyModel!.decimalDigits!);
                offerYourRateController.value.text = amount.value;
              } else {
                distance.value = (value.rows!.first.elements!.first.distance!.value!.toInt() / 1609.34).toString();
                amount.value = Constant.amountCalculate(selectedFreightVehicle.value.kmCharge.toString(), distance.value)
                    .toStringAsFixed(Constant.currencyModel!.decimalDigits!);
                amount.value = (double.parse(amount.value) * surgeMultiplier.value).toStringAsFixed(Constant.currencyModel!.decimalDigits!);
                offerYourRateController.value.text = amount.value;
              }
            }
          });
        }
      }
    } else {
      amount.value = "0.0";
      offerYourRateController.value.text = "0.0";
      if (sourceLocationLAtLng.value.latitude != null && destinationLocationLAtLng.value.latitude != null) {
        await Constant.getDurationDistance(LatLng(sourceLocationLAtLng.value.latitude!, sourceLocationLAtLng.value.longitude!),
                LatLng(destinationLocationLAtLng.value.latitude!, destinationLocationLAtLng.value.longitude!))
            .then((value) {
          if (value != null) {
            duration.value = value.rows!.first.elements!.first.duration!.text.toString();
            if (Constant.distanceType == "Km") {
              distance.value = (value.rows!.first.elements!.first.distance!.value!.toInt() / 1000).toString();
              amount.value = Constant.amountCalculate(selectedInterCityType.value.kmCharge.toString(), distance.value)
                  .toStringAsFixed(Constant.currencyModel!.decimalDigits!);
              amount.value = (double.parse(amount.value) * surgeMultiplier.value).toStringAsFixed(Constant.currencyModel!.decimalDigits!);
              offerYourRateController.value.text = amount.value;
            } else {
              distance.value = (value.rows!.first.elements!.first.distance!.value!.toInt() / 1609.34).toString();
              amount.value = Constant.amountCalculate(selectedInterCityType.value.kmCharge.toString(), distance.value)
                  .toStringAsFixed(Constant.currencyModel!.decimalDigits!);
              amount.value = (double.parse(amount.value) * surgeMultiplier.value).toStringAsFixed(Constant.currencyModel!.decimalDigits!);
              offerYourRateController.value.text = amount.value;
            }
          }
        });
      }
    }
  }

  RxList<ContactModel> contactList = <ContactModel>[].obs;
  Rx<ContactModel> selectedTakingRide = ContactModel(fullName: "Myself", contactNumber: "").obs;

  void setContact() {
    print(jsonEncode(contactList));
    Preferences.setString(Preferences.contactList, json.encode(contactList.map<Map<String, dynamic>>((music) => music.toJson()).toList()));
    getContact();
  }

  void getContact() {
    String contactListJson = Preferences.getString(Preferences.contactList);

    if (contactListJson.isNotEmpty) {
      print("---->");
      contactList.clear();
      contactList.value = (json.decode(contactListJson) as List<dynamic>).map<ContactModel>((item) => ContactModel.fromJson(item)).toList();
    }
  }
}
