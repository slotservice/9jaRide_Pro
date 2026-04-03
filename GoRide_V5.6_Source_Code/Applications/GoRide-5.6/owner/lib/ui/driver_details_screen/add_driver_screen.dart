import 'package:cached_network_image/cached_network_image.dart';
import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:country_code_picker/country_code_picker.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import 'package:owner/constant/constant.dart';
import 'package:owner/constant/show_toast_dialog.dart';
import 'package:owner/controller/add_driver_controller.dart';
import 'package:owner/model/service_model.dart';
import 'package:owner/model/zone_model.dart';
import 'package:owner/themes/app_colors.dart';
import 'package:owner/themes/button_them.dart';
import 'package:owner/themes/responsive.dart';
import 'package:owner/themes/text_field_them.dart';
import 'package:owner/utils/DarkThemeProvider.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:owner/utils/fire_store_utils.dart';
import 'package:provider/provider.dart';

class AddDriverScreen extends StatelessWidget {
  const AddDriverScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final themeChange = Provider.of<DarkThemeProvider>(context);
    return GetX<AddDriverController>(
        init: AddDriverController(),
        builder: (controller) {
          return Scaffold(
            appBar: AppBar(
              backgroundColor: AppColors.lightprimary,
              title: Text("Driver Details".tr),
              leading: InkWell(
                  onTap: () {
                    Get.back();
                  },
                  child: const Icon(
                    Icons.arrow_back,
                  )),
            ),
            backgroundColor: AppColors.lightprimary,
            body: Column(
              children: [
                Expanded(
                  child: Container(
                    height: Responsive.height(100, context),
                    width: Responsive.width(100, context),
                    decoration: BoxDecoration(color: Theme.of(context).colorScheme.background, borderRadius: const BorderRadius.only(topLeft: Radius.circular(25), topRight: Radius.circular(25))),
                    child: Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 10),
                      child: controller.isLoading.value == true
                          ? Constant.loader(isDarkTheme: themeChange.getThem())
                          : ListView(
                              // crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text("Driver Information".tr, style: GoogleFonts.poppins(fontSize: 16, fontWeight: FontWeight.w600)),
                                const SizedBox(
                                  height: 10,
                                ),
                                TextFieldThem.buildTextFiled(context, hintText: 'Full name'.tr, controller: controller.fullNameController.value),
                                const SizedBox(
                                  height: 10,
                                ),
                                TextFormField(
                                    validator: (value) => value != null && value.isNotEmpty ? null : 'Required'.tr,
                                    keyboardType: TextInputType.number,
                                    textCapitalization: TextCapitalization.sentences,
                                    controller: controller.phoneNumberController.value,
                                    textAlign: TextAlign.start,
                                    enabled: controller.driverModel.value.id == null,
                                    style: GoogleFonts.poppins(color: themeChange.getThem() ? AppColors.textField : AppColors.darkTextField),
                                    decoration: InputDecoration(
                                        isDense: true,
                                        filled: true,
                                        fillColor: themeChange.getThem() ? AppColors.darkTextField : AppColors.textField,
                                        contentPadding: const EdgeInsets.symmetric(vertical: 12),
                                        prefixIcon: CountryCodePicker(
                                          onInit: (value) {
                                            controller.countryCode.value.text = value?.dialCode ?? '';
                                            controller.countryISOCode.value.text = value?.code ?? '';
                                          },
                                          onChanged: (value) {
                                            controller.countryCode.value.text = value.dialCode.toString();
                                            controller.countryISOCode.value.text = value.code.toString();
                                          },
                                          dialogBackgroundColor: themeChange.getThem() ? AppColors.darkBackground : AppColors.background,
                                          initialSelection: controller.countryISOCode.value.text,
                                          comparator: (a, b) => b.name!.compareTo(a.name.toString()),
                                          flagDecoration: const BoxDecoration(
                                            borderRadius: BorderRadius.all(Radius.circular(2)),
                                          ),
                                        ),
                                        disabledBorder: OutlineInputBorder(
                                          borderRadius: const BorderRadius.all(Radius.circular(4)),
                                          borderSide: BorderSide(color: themeChange.getThem() ? AppColors.darkTextFieldBorder : AppColors.textFieldBorder, width: 1),
                                        ),
                                        focusedBorder: OutlineInputBorder(
                                          borderRadius: const BorderRadius.all(Radius.circular(4)),
                                          borderSide: BorderSide(color: themeChange.getThem() ? AppColors.darkTextFieldBorder : AppColors.textFieldBorder, width: 1),
                                        ),
                                        enabledBorder: OutlineInputBorder(
                                          borderRadius: const BorderRadius.all(Radius.circular(4)),
                                          borderSide: BorderSide(color: themeChange.getThem() ? AppColors.darkTextFieldBorder : AppColors.textFieldBorder, width: 1),
                                        ),
                                        errorBorder: OutlineInputBorder(
                                          borderRadius: const BorderRadius.all(Radius.circular(4)),
                                          borderSide: BorderSide(color: themeChange.getThem() ? AppColors.darkTextFieldBorder : AppColors.textFieldBorder, width: 1),
                                        ),
                                        border: OutlineInputBorder(
                                          borderRadius: const BorderRadius.all(Radius.circular(4)),
                                          borderSide: BorderSide(color: themeChange.getThem() ? AppColors.darkTextFieldBorder : AppColors.textFieldBorder, width: 1),
                                        ),
                                        hintText: "Phone number".tr)),
                                const SizedBox(
                                  height: 10,
                                ),
                                TextFieldThem.buildTextFiled(
                                  context,
                                  enable: controller.driverModel.value.id == null,
                                  hintText: 'Email'.tr,
                                  controller: controller.emailController.value,
                                ),
                                const SizedBox(
                                  height: 20,
                                ),
                                Text("Vehicle Information".tr, style: GoogleFonts.poppins(fontSize: 16, fontWeight: FontWeight.w600)),
                                Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    const SizedBox(
                                      height: 10,
                                    ),
                                    SizedBox(
                                      height: Responsive.height(18, context),
                                      child: ListView.builder(
                                        itemCount: controller.serviceList.length,
                                        scrollDirection: Axis.horizontal,
                                        shrinkWrap: true,
                                        itemBuilder: (context, index) {
                                          ServiceModel serviceModel = controller.serviceList[index];
                                          return Obx(
                                            () => InkWell(
                                              onTap: () async {
                                                if (controller.driverModel.value.serviceId == null) {
                                                  controller.selectedServiceType.value = serviceModel;
                                                  controller.getZone();
                                                }
                                              },
                                              child: Padding(
                                                padding: const EdgeInsets.all(6.0),
                                                child: Container(
                                                  width: Responsive.width(28, context),
                                                  decoration: BoxDecoration(
                                                      color: controller.selectedServiceType.value.id == serviceModel.id
                                                          ? themeChange.getThem()
                                                              ? AppColors.darksecondprimary
                                                              : AppColors.lightsecondprimary
                                                          : themeChange.getThem()
                                                              ? AppColors.darkService
                                                              : controller.colors[index % controller.colors.length],
                                                      borderRadius: const BorderRadius.all(
                                                        Radius.circular(20),
                                                      )),
                                                  child: Column(
                                                    crossAxisAlignment: CrossAxisAlignment.center,
                                                    mainAxisAlignment: MainAxisAlignment.center,
                                                    children: [
                                                      Container(
                                                        decoration: const BoxDecoration(
                                                            color: AppColors.background,
                                                            borderRadius: BorderRadius.all(
                                                              Radius.circular(20),
                                                            )),
                                                        child: Padding(
                                                          padding: const EdgeInsets.all(8.0),
                                                          child: CachedNetworkImage(
                                                            imageUrl: serviceModel.image.toString(),
                                                            fit: BoxFit.contain,
                                                            height: Responsive.height(8, context),
                                                            width: Responsive.width(18, context),
                                                            placeholder: (context, url) => Constant.loader(isDarkTheme: themeChange.getThem()),
                                                            errorWidget: (context, url, error) => Image.network(
                                                                'https://firebasestorage.googleapis.com/v0/b/goride-1a752.appspot.com/o/placeholderImages%2Fuser-placeholder.jpeg?alt=media&token=34a73d67-ba1d-4fe4-a29f-271d3e3ca115'),
                                                          ),
                                                        ),
                                                      ),
                                                      const SizedBox(
                                                        height: 10,
                                                      ),
                                                      Text(Constant.localizationTitle(serviceModel.title),
                                                          style: GoogleFonts.poppins(
                                                              color: controller.selectedServiceType.value.id == serviceModel.id
                                                                  ? themeChange.getThem()
                                                                      ? Colors.black
                                                                      : Colors.white
                                                                  : themeChange.getThem()
                                                                      ? Colors.white
                                                                      : Colors.black)),
                                                    ],
                                                  ),
                                                ),
                                              ),
                                            ),
                                          );
                                        },
                                      ),
                                    ),
                                    const SizedBox(
                                      height: 10,
                                    ),
                                    TextFieldThem.buildTextFiled(context, hintText: 'Vehicle Number'.tr, controller: controller.vehicleNumberController.value),
                                    const SizedBox(
                                      height: 10,
                                    ),
                                    InkWell(
                                      onTap: () async {
                                        await Constant.selectDate(context).then((value) {
                                          if (value != null) {
                                            controller.selectedDate.value = value;
                                            controller.registrationDateController.value.text = DateFormat("dd-MM-yyyy").format(value);
                                          }
                                        });
                                      },
                                      child: TextFieldThem.buildTextFiled(context, hintText: 'Registration Date'.tr, controller: controller.registrationDateController.value, enable: false),
                                    ),
                                    const SizedBox(
                                      height: 10,
                                    ),
                                    DropdownButtonFormField<String>(
                                        decoration: InputDecoration(
                                          filled: true,
                                          fillColor: themeChange.getThem() ? AppColors.darkTextField : AppColors.textField,
                                          contentPadding: const EdgeInsets.only(left: 10, right: 10),
                                          disabledBorder: OutlineInputBorder(
                                            borderRadius: const BorderRadius.all(Radius.circular(4)),
                                            borderSide: BorderSide(color: themeChange.getThem() ? AppColors.darkTextFieldBorder : AppColors.textFieldBorder, width: 1),
                                          ),
                                          focusedBorder: OutlineInputBorder(
                                            borderRadius: const BorderRadius.all(Radius.circular(4)),
                                            borderSide: BorderSide(color: themeChange.getThem() ? AppColors.darkTextFieldBorder : AppColors.textFieldBorder, width: 1),
                                          ),
                                          enabledBorder: OutlineInputBorder(
                                            borderRadius: const BorderRadius.all(Radius.circular(4)),
                                            borderSide: BorderSide(color: themeChange.getThem() ? AppColors.darkTextFieldBorder : AppColors.textFieldBorder, width: 1),
                                          ),
                                          errorBorder: OutlineInputBorder(
                                            borderRadius: const BorderRadius.all(Radius.circular(4)),
                                            borderSide: BorderSide(color: themeChange.getThem() ? AppColors.darkTextFieldBorder : AppColors.textFieldBorder, width: 1),
                                          ),
                                          border: OutlineInputBorder(
                                            borderRadius: const BorderRadius.all(Radius.circular(4)),
                                            borderSide: BorderSide(color: themeChange.getThem() ? AppColors.darkTextFieldBorder : AppColors.textFieldBorder, width: 1),
                                          ),
                                        ),
                                        validator: (value) => value == null ? 'field required'.tr : null,
                                        value: controller.selectedColor.value.isEmpty ? null : controller.selectedColor.value,
                                        onChanged: (value) {
                                          controller.selectedColor.value = value!;
                                        },
                                        hint: Text("Select vehicle color".tr),
                                        items: controller.carColorList.map((item) {
                                          return DropdownMenuItem(
                                            value: item,
                                            child: Text(item.toString()),
                                          );
                                        }).toList()),
                                    const SizedBox(
                                      height: 10,
                                    ),
                                    DropdownButtonFormField<String>(
                                        decoration: InputDecoration(
                                          filled: true,
                                          fillColor: themeChange.getThem() ? AppColors.darkTextField : AppColors.textField,
                                          contentPadding: const EdgeInsets.only(left: 10, right: 10),
                                          disabledBorder: OutlineInputBorder(
                                            borderRadius: const BorderRadius.all(Radius.circular(4)),
                                            borderSide: BorderSide(color: themeChange.getThem() ? AppColors.darkTextFieldBorder : AppColors.textFieldBorder, width: 1),
                                          ),
                                          focusedBorder: OutlineInputBorder(
                                            borderRadius: const BorderRadius.all(Radius.circular(4)),
                                            borderSide: BorderSide(color: themeChange.getThem() ? AppColors.darkTextFieldBorder : AppColors.textFieldBorder, width: 1),
                                          ),
                                          enabledBorder: OutlineInputBorder(
                                            borderRadius: const BorderRadius.all(Radius.circular(4)),
                                            borderSide: BorderSide(color: themeChange.getThem() ? AppColors.darkTextFieldBorder : AppColors.textFieldBorder, width: 1),
                                          ),
                                          errorBorder: OutlineInputBorder(
                                            borderRadius: const BorderRadius.all(Radius.circular(4)),
                                            borderSide: BorderSide(color: themeChange.getThem() ? AppColors.darkTextFieldBorder : AppColors.textFieldBorder, width: 1),
                                          ),
                                          border: OutlineInputBorder(
                                            borderRadius: const BorderRadius.all(Radius.circular(4)),
                                            borderSide: BorderSide(color: themeChange.getThem() ? AppColors.darkTextFieldBorder : AppColors.textFieldBorder, width: 1),
                                          ),
                                        ),
                                        validator: (value) => value == null ? 'field required'.tr : null,
                                        value: controller.seatsController.value.text.isEmpty ? null : controller.seatsController.value.text,
                                        onChanged: (value) {
                                          controller.seatsController.value.text = value!;
                                        },
                                        hint: Text("How Many Seats".tr),
                                        items: controller.sheetList.map((item) {
                                          return DropdownMenuItem(
                                            value: item,
                                            child: Text(item.toString()),
                                          );
                                        }).toList()),
                                    const SizedBox(
                                      height: 10,
                                    ),
                                    InkWell(
                                      onTap: () {
                                        controller.selectedTempZone.clear();
                                        controller.selectedTempZone.addAll(controller.selectedZone);
                                        zoneDialog(context, controller);
                                      },
                                      child: TextFieldThem.buildTextFiled(
                                        context,
                                        hintText: 'Select Zone'.tr,
                                        controller: controller.zoneNameController.value,
                                        enable: false,
                                      ),
                                    ),
                                    const SizedBox(
                                      height: 10,
                                    ),
                                    if (controller.selectedPrices.isNotEmpty)
                                      Obx(
                                        () => Container(
                                          decoration: BoxDecoration(
                                            borderRadius: const BorderRadius.all(Radius.circular(4)),
                                            border: Border.all(color: themeChange.getThem() ? AppColors.darkTextFieldBorder : AppColors.textFieldBorder, width: 1),
                                          ),
                                          width: Responsive.width(100, context),
                                          child: DefaultTabController(
                                            length: controller.selectedPrices.length,
                                            child: Column(
                                              crossAxisAlignment: CrossAxisAlignment.start,
                                              children: [
                                                TabBar(
                                                  onTap: (value) {
                                                    controller.tabBarheight.value = controller.selectedPrices[value].isAcNonAc == true ? 200 : 100;
                                                    controller.update();
                                                  },
                                                  indicatorColor: themeChange.getThem() ? AppColors.darksecondprimary : AppColors.lightsecondprimary,
                                                  padding: EdgeInsets.zero,
                                                  isScrollable: true,
                                                  labelColor: themeChange.getThem() ? AppColors.darksecondprimary : AppColors.lightsecondprimary,
                                                  unselectedLabelColor: themeChange.getThem() ? AppColors.gray : AppColors.darkGray,
                                                  labelStyle: GoogleFonts.poppins(fontWeight: FontWeight.w600),
                                                  tabs: controller.selectedPrices.map((price) {
                                                    final zoneName = Constant.localizationName(
                                                      controller.zoneAllList
                                                          .firstWhere(
                                                            (zone) => zone.id == price.zoneId,
                                                            orElse: () => ZoneModel(),
                                                          )
                                                          .name,
                                                    );
                                                    return Tab(text: zoneName);
                                                  }).toList(),
                                                ),
                                                SizedBox(
                                                  height: controller.tabBarheight.value,
                                                  child: TabBarView(
                                                    physics: const NeverScrollableScrollPhysics(),
                                                    children: controller.selectedPrices.map((price) {
                                                      int index = controller.selectedPrices.indexOf(price);

                                                      if (price.isAcNonAc == true) {
                                                        return Padding(
                                                          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                                                          child: SingleChildScrollView(
                                                            child: Column(
                                                              crossAxisAlignment: CrossAxisAlignment.start,
                                                              children: [
                                                                Text("${"A/C Per".tr} ${Constant.distanceType} ${"Rate".tr}".tr, style: GoogleFonts.poppins(fontWeight: FontWeight.w500, fontSize: 15)),
                                                                const SizedBox(height: 5),
                                                                TextFieldThem.buildTextFiledWithPrefixIcon(
                                                                  context,
                                                                  hintText: '${"A/C Per".tr} ${Constant.distanceType} ${"Rate".tr}'.tr,
                                                                  keyBoardType: const TextInputType.numberWithOptions(decimal: true),
                                                                  controller: controller.acPerKmRate[index],
                                                                  prefix: Padding(
                                                                    padding: const EdgeInsets.only(right: 10),
                                                                    child: Text(Constant.currencyModel!.symbol.toString()),
                                                                  ),
                                                                ),
                                                                const SizedBox(height: 20),
                                                                Text("${"Non A/C Per".tr} ${Constant.distanceType} ${"Rate".tr}".tr,
                                                                    style: GoogleFonts.poppins(fontWeight: FontWeight.w500, fontSize: 15)),
                                                                const SizedBox(height: 5),
                                                                TextFieldThem.buildTextFiledWithPrefixIcon(
                                                                  context,
                                                                  hintText: '${"Non A/C Per".tr} ${Constant.distanceType} ${"Rate".tr}'.tr,
                                                                  keyBoardType: const TextInputType.numberWithOptions(decimal: true),
                                                                  controller: controller.nonAcPerKmRate[index],
                                                                  prefix: Padding(
                                                                    padding: const EdgeInsets.only(right: 10),
                                                                    child: Text(Constant.currencyModel!.symbol.toString()),
                                                                  ),
                                                                ),
                                                              ],
                                                            ),
                                                          ),
                                                        );
                                                      } else {
                                                        return SingleChildScrollView(
                                                          child: Padding(
                                                            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                                                            child: Column(
                                                              crossAxisAlignment: CrossAxisAlignment.start,
                                                              children: [
                                                                Text("${"Per".tr} ${Constant.distanceType} ${"Rate".tr}".tr, style: GoogleFonts.poppins(fontWeight: FontWeight.w500, fontSize: 15)),
                                                                const SizedBox(height: 5),
                                                                TextFieldThem.buildTextFiledWithPrefixIcon(
                                                                  context,
                                                                  hintText: '${"Per".tr} ${Constant.distanceType} ${"Rate".tr}'.tr,
                                                                  keyBoardType: const TextInputType.numberWithOptions(decimal: true),
                                                                  controller: controller.acNonAcWithoutPerKmRate[index],
                                                                  prefix: Padding(
                                                                    padding: const EdgeInsets.only(right: 10),
                                                                    child: Text(Constant.currencyModel!.symbol.toString()),
                                                                  ),
                                                                ),
                                                              ],
                                                            ),
                                                          ),
                                                        );
                                                      }
                                                    }).toList(),
                                                  ),
                                                )
                                              ],
                                            ),
                                          ),
                                        ),
                                      ),
                                  ],
                                ),
                                const SizedBox(
                                  height: 10,
                                ),
                                Text("Select Your Rules".tr, style: GoogleFonts.poppins(fontWeight: FontWeight.w600, fontSize: 16)),
                                ListBody(
                                  children: controller.driverRulesList
                                      .map((item) => CheckboxListTile(
                                            checkColor: themeChange.getThem() ? AppColors.darksecondprimary : AppColors.lightsecondprimary,
                                            value: controller.selectedDriverRulesList.indexWhere((element) => element.id == item.id) == -1 ? false : true,
                                            title: Text(Constant.localizationName(item.name), style: GoogleFonts.poppins(fontWeight: FontWeight.w400)),
                                            onChanged: (value) {
                                              if (value == true) {
                                                controller.selectedDriverRulesList.add(item);
                                              } else {
                                                controller.selectedDriverRulesList.removeAt(controller.selectedDriverRulesList.indexWhere((element) => element.id == item.id));
                                              }
                                            },
                                          ))
                                      .toList(),
                                ),
                                ButtonThem.buildButton(
                                  context,
                                  title: controller.driverModel.value.id == null ? "Verify OTP".tr : "Update".tr,
                                  onPress: () async {
                                    if (controller.fullNameController.value.text.isEmpty) {
                                      ShowToastDialog.showToast("Please enter full name".tr);
                                      return;
                                    } else if (controller.emailController.value.text.isEmpty) {
                                      ShowToastDialog.showToast("Please enter email".tr);
                                      return;
                                    } else if (controller.phoneNumberController.value.text.isEmpty) {
                                      ShowToastDialog.showToast("Please enter phone number".tr);
                                      return;
                                    } else if (Constant.validateEmail(controller.emailController.value.text) == false) {
                                      ShowToastDialog.showToast("Please enter valid email".tr);
                                      return;
                                    } else {
                                      ShowToastDialog.showLoader("Please wait".tr);

                                      controller.driverModel.value.fullName = controller.fullNameController.value.text;
                                      controller.driverModel.value.email = controller.emailController.value.text;
                                      controller.driverModel.value.countryCode = controller.countryCode.value.text;
                                      controller.driverModel.value.countryISOCode = controller.countryISOCode.value.text;
                                      controller.driverModel.value.phoneNumber = controller.phoneNumberController.value.text;
                                      controller.driverModel.value.documentVerification = Constant.isVerifyOwnerDocument == true ? false : true;
                                      controller.driverModel.value.createdAt = Timestamp.now();

                                      if (controller.selectedServiceType.value.id == null || controller.selectedServiceType.value.id!.isEmpty) {
                                        ShowToastDialog.showToast("Please select service".tr);
                                        return;
                                      }

                                      if (controller.vehicleNumberController.value.text.isEmpty) {
                                        ShowToastDialog.showToast(
                                          "Please enter Vehicle number".tr,
                                        );
                                        return;
                                      } else if (controller.registrationDateController.value.text.isEmpty) {
                                        ShowToastDialog.showToast(
                                          "Please select registration date".tr,
                                        );
                                        return;
                                      } else if (controller.selectedColor.value.isEmpty) {
                                        ShowToastDialog.showToast(
                                          "Please enter Vehicle color".tr,
                                        );
                                        return;
                                      } else if (controller.seatsController.value.text.isEmpty) {
                                        ShowToastDialog.showToast(
                                          "Please enter seats".tr,
                                        );
                                        return;
                                      } else if (controller.selectedZone.isEmpty) {
                                        ShowToastDialog.showToast(
                                          "Please select Zone".tr,
                                        );
                                        return;
                                      } else {
                                        for (int index = 0; index < controller.selectedPrices.length; index++) {
                                          ZoneModel zoneModel = await FireStoreUtils.getZoneById(zoneId: controller.selectedPrices[index].zoneId!);
                                          if (controller.selectedPrices[index].isAcNonAc == true) {
                                            if (controller.acPerKmRate[index].text.isEmpty) {
                                              ShowToastDialog.showToast(
                                                  '${"Please Enter A/C Per".tr} ${Constant.distanceType} ${'Rate for'.tr} ${Constant.localizationName(zoneModel.name)} ${'Zone'.tr}.');
                                              return;
                                            } else if (double.parse(controller.selectedPrices[index].acCharge.toString()) < double.parse(controller.acPerKmRate[index].text)) {
                                              ShowToastDialog.showToast(
                                                "${"Maximum Allowed value is".tr} ${controller.selectedPrices[index].acCharge.toString()} ${"Please enter a lower A/c value for".tr} ${Constant.localizationName(zoneModel.name)} ${'Zone'.tr}."
                                                    .tr,
                                              );
                                              return;
                                            } else if (controller.nonAcPerKmRate[index].text.isEmpty) {
                                              ShowToastDialog.showToast(
                                                "${"Please Enter Non A/C Per".tr} ${Constant.distanceType} ${"Rate for".tr} ${Constant.localizationName(zoneModel.name)} ${'Zone'.tr}".tr,
                                              );
                                              return;
                                            } else if (double.parse(controller.selectedPrices[index].nonAcCharge.toString()) < double.parse(controller.nonAcPerKmRate[index].text)) {
                                              ShowToastDialog.showToast(
                                                "${"Maximum Allowed value is".tr} ${controller.selectedPrices[index].nonAcCharge.toString()} ${"Please enter a lower Non A/c value for".tr}  ${Constant.localizationName(zoneModel.name)} ${'Zone'.tr}."
                                                    .tr,
                                              );
                                              return;
                                            }
                                          } else if (controller.selectedPrices[index].isAcNonAc == false) {
                                            ZoneModel zoneData = await FireStoreUtils.getZoneById(zoneId: controller.selectedPrices[index].zoneId!);
                                            if (controller.acNonAcWithoutPerKmRate[index].text.isEmpty) {
                                              ShowToastDialog.showToast(
                                                "${'Please Enter Per'.tr} ${Constant.distanceType} ${'Rate for'.tr} ${Constant.localizationName(zoneData.name)} ${'Zone'.tr}.".tr,
                                              );
                                              return;
                                            } else if (double.parse(controller.selectedPrices[index].kmCharge.toString()) < double.parse(controller.acNonAcWithoutPerKmRate[index].text)) {
                                              ShowToastDialog.showToast(
                                                "${"Maximum Allowed value is".tr} ${controller.selectedPrices[index].kmCharge.toString()} ${"Please Enter a Lower Price for".tr}  ${Constant.localizationName(zoneData.name)} ${'Zone'.tr}."
                                                    .tr,
                                              );
                                              return;
                                            }
                                          }
                                        }
                                        controller.sendCode();
                                      }
                                    }
                                  },
                                )
                              ],
                            ),
                    ),
                  ),
                ),
              ],
            ),
          );
        });
  }

  void zoneDialog(BuildContext context, AddDriverController controller) {
    showDialog(
        context: context,
        builder: (BuildContext context) {
          return AlertDialog(
            title: Text('Zone list'.tr),
            content: SizedBox(
              width: Responsive.width(90, context),
              // Change as per your requirement
              child: controller.zoneList.isEmpty
                  ? Container()
                  : ListView.builder(
                      shrinkWrap: true,
                      itemCount: controller.zoneList.length,
                      itemBuilder: (BuildContext context, int index) {
                        return Obx(
                          () => CheckboxListTile(
                            value: controller.selectedTempZone.contains(controller.zoneList[index].id),
                            onChanged: (value) {
                              if (controller.selectedTempZone.contains(controller.zoneList[index].id)) {
                                controller.selectedTempZone.remove(controller.zoneList[index].id); // unselect
                              } else {
                                controller.selectedTempZone.add(controller.zoneList[index].id); // select
                              }
                            },
                            activeColor: AppColors.lightprimary,
                            title: Text(Constant.localizationName(controller.zoneList[index].name)),
                          ),
                        );
                      },
                    ),
            ),
            actions: [
              TextButton(
                child: Text(
                  "Cancel".tr,
                  style: TextStyle(),
                ),
                onPressed: () {
                  controller.selectedTempZone.value = controller.selectedZone;
                  Get.back();
                },
              ),
              TextButton(
                child: Text("Continue".tr),
                onPressed: () {
                  controller.selectedZone.clear();
                  controller.selectedZone.addAll(controller.selectedTempZone);
                  if (controller.selectedTempZone.isEmpty) {
                    ShowToastDialog.showToast("Please select zone".tr);
                  } else {
                    controller.selectedPrices.value = controller.selectedServiceType.value.prices?.where((price) => controller.selectedZone.contains(price.zoneId)).toList() ?? <Price>[];
                    controller.acPerKmRate.value = List.generate(controller.selectedPrices.length, (index) => TextEditingController());
                    controller.nonAcPerKmRate.value = List.generate(controller.selectedPrices.length, (index) => TextEditingController());
                    controller.acNonAcWithoutPerKmRate.value = List.generate(controller.selectedPrices.length, (index) => TextEditingController());
                    final hasAcNonAc = controller.selectedPrices.any((e) => e.isAcNonAc == true);
                    controller.tabBarheight.value = hasAcNonAc ? 200 : 100;
                    String nameValue = "";
                    for (var element in controller.selectedZone) {
                      List<ZoneModel> list = controller.zoneList.where((p0) => p0.id == element).toList();
                      if (list.isNotEmpty) {
                        nameValue = "$nameValue${nameValue.isEmpty ? "" : ","} ${Constant.localizationName(list.first.name)}";
                      }
                    }
                    controller.zoneNameController.value.text = nameValue;
                    controller.update();
                    Get.back();
                  }
                },
              ),
            ],
          );
        });
  }
}
