import 'dart:io';

import 'package:cached_network_image/cached_network_image.dart';
import 'package:country_code_picker/country_code_picker.dart';
import 'package:driver/constant/constant.dart';
import 'package:driver/constant/show_toast_dialog.dart';
import 'package:driver/controller/profile_controller.dart';
import 'package:driver/model/driver_user_model.dart';
import 'package:driver/themes/app_colors.dart';
import 'package:driver/themes/button_them.dart';
import 'package:driver/themes/responsive.dart';
import 'package:driver/themes/text_field_them.dart';
import 'package:driver/ui/hire_purchase/hp_tracker_screen.dart';
import 'package:driver/ui/online_registration/online_registartion_screen.dart';
import 'package:driver/utils/DarkThemeProvider.dart';
import 'package:driver/utils/fire_store_utils.dart';
import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:image_picker/image_picker.dart';
import 'package:provider/provider.dart';

class ProfileScreen extends StatelessWidget {
  const ProfileScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final themeChange = Provider.of<DarkThemeProvider>(context);

    return GetX<ProfileController>(
        init: ProfileController(),
        builder: (controller) {
          return Scaffold(
              backgroundColor: AppColors.lightprimary,
              body: Column(
                children: [
                  SizedBox(
                    height: Responsive.width(45, context),
                    width: Responsive.width(100, context),
                    child: Stack(
                      alignment: Alignment.bottomCenter,
                      children: [
                        Positioned(
                          bottom: 50,
                          child: Center(
                            child: controller.profileImage.isEmpty
                                ? ClipRRect(
                                    borderRadius: BorderRadius.circular(60),
                                    child: CachedNetworkImage(
                                      imageUrl: Constant.userPlaceHolder,
                                      fit: BoxFit.fill,
                                      height: Responsive.width(30, context),
                                      width: Responsive.width(30, context),
                                      placeholder: (context, url) => Constant.loader(isDarkTheme: themeChange.getThem()),
                                      errorWidget: (context, url, error) => Image.network(Constant.userPlaceHolder),
                                    ),
                                  )
                                : ClipRRect(
                                    borderRadius: BorderRadius.circular(60),
                                    child: Constant().hasValidUrl(controller.profileImage.value) == false
                                        ? Image.file(
                                            File(controller.profileImage.value),
                                            height: Responsive.width(30, context),
                                            width: Responsive.width(30, context),
                                            fit: BoxFit.fill,
                                          )
                                        : CachedNetworkImage(
                                            imageUrl: controller.profileImage.value.toString(),
                                            fit: BoxFit.fill,
                                            height: Responsive.width(30, context),
                                            width: Responsive.width(30, context),
                                            placeholder: (context, url) => Constant.loader(isDarkTheme: themeChange.getThem()),
                                            errorWidget: (context, url, error) => Image.network(Constant.userPlaceHolder),
                                          ),
                                  ),
                          ),
                        ),
                        Positioned(
                          bottom: 50,
                          right: Responsive.width(36, context),
                          child: InkWell(
                            onTap: () {
                              buildBottomSheet(context, controller);
                            },
                            child: ClipOval(
                              child: Container(
                                color: Colors.white,
                                child: Padding(
                                  padding: const EdgeInsets.all(8.0),
                                  child: SvgPicture.asset(
                                    'assets/icons/ic_edit_profile.svg',
                                    width: 22,
                                    height: 22,
                                  ),
                                ),
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                  Expanded(
                    child: controller.isLoading.value
                        ? Constant.loader(isDarkTheme: themeChange.getThem())
                        : Container(
                            decoration:
                                BoxDecoration(color: Theme.of(context).colorScheme.background, borderRadius: const BorderRadius.only(topLeft: Radius.circular(25), topRight: Radius.circular(25))),
                            child: Padding(
                              padding: const EdgeInsets.symmetric(horizontal: 10),
                              child: Padding(
                                padding: const EdgeInsets.only(top: 20),
                                child: Padding(
                                  padding: const EdgeInsets.symmetric(horizontal: 10),
                                  child: SingleChildScrollView(
                                    child: Column(
                                      children: [
                                        TextFieldThem.buildTextFiled(context, hintText: 'Full name'.tr, controller: controller.fullNameController.value),
                                        const SizedBox(
                                          height: 10,
                                        ),
                                        TextFormField(
                                            validator: (value) => value != null && value.isNotEmpty ? null : 'Required',
                                            keyboardType: TextInputType.number,
                                            textCapitalization: TextCapitalization.sentences,
                                            controller: controller.phoneNumberController.value,
                                            textAlign: TextAlign.start,
                                            enabled: false,
                                            decoration: InputDecoration(
                                                isDense: true,
                                                filled: true,
                                                fillColor: themeChange.getThem() ? AppColors.darkTextField : AppColors.textField,
                                                contentPadding: const EdgeInsets.symmetric(vertical: 12),
                                                prefixIcon: CountryCodePicker(
                                                  onInit: (value) {
                                                    controller.countryCode.value.text = value?.dialCode ?? Constant.defaultCountryCode;
                                                    controller.countryISOCode.value.text = value?.code ?? Constant.defaultCountryCode;
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
                                        TextFieldThem.buildTextFiled(context, hintText: 'Email'.tr, controller: controller.emailController.value, enable: false),
                                        const SizedBox(height: 10),
                                        // My Documents tile
                                        InkWell(
                                          onTap: () => Get.to(const OnlineRegistrationScreen()),
                                          child: Container(
                                            decoration: BoxDecoration(
                                              borderRadius: const BorderRadius.all(Radius.circular(4)),
                                              border: Border.all(color: themeChange.getThem() ? AppColors.darkTextFieldBorder : AppColors.textFieldBorder, width: 1),
                                            ),
                                            child: Padding(
                                              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 12),
                                              child: Row(
                                                children: [
                                                  const Icon(Icons.folder_outlined),
                                                  const SizedBox(width: 10),
                                                  Expanded(
                                                    child: Column(
                                                      crossAxisAlignment: CrossAxisAlignment.start,
                                                      children: [
                                                        Text("My Documents".tr, style: GoogleFonts.poppins(fontWeight: FontWeight.w500)),
                                                        Text("(License & Vehicle Reg.)".tr, style: GoogleFonts.poppins(fontSize: 12, color: Colors.grey)),
                                                      ],
                                                    ),
                                                  ),
                                                  const Icon(Icons.arrow_forward_ios_rounded, size: 16, color: Colors.grey),
                                                ],
                                              ),
                                            ),
                                          ),
                                        ),
                                        const SizedBox(height: 16),
                                        // Update KYC Documents section
                                        Align(
                                          alignment: Alignment.centerLeft,
                                          child: Text("Update KYC Documents".tr, style: GoogleFonts.poppins(fontWeight: FontWeight.w600, fontSize: 14)),
                                        ),
                                        const SizedBox(height: 8),
                                        Obx(() => Row(
                                          children: [
                                            for (int i = 0; i < controller.documentList.length && i < 2; i++) ...[
                                              Expanded(
                                                child: GestureDetector(
                                                  onTap: () async {
                                                    await showModalBottomSheet(
                                                      context: context,
                                                      builder: (_) => SafeArea(
                                                        child: Column(
                                                          mainAxisSize: MainAxisSize.min,
                                                          children: [
                                                            ListTile(
                                                              leading: const Icon(Icons.camera_alt),
                                                              title: Text("Camera".tr),
                                                              onTap: () { Get.back(); controller.pickDocImage(source: ImageSource.camera, docIndex: i); },
                                                            ),
                                                            ListTile(
                                                              leading: const Icon(Icons.photo_library),
                                                              title: Text("Gallery".tr),
                                                              onTap: () { Get.back(); controller.pickDocImage(source: ImageSource.gallery, docIndex: i); },
                                                            ),
                                                          ],
                                                        ),
                                                      ),
                                                    );
                                                  },
                                                  child: Container(
                                                    height: 100,
                                                    margin: EdgeInsets.only(right: i == 0 ? 6 : 0),
                                                    decoration: BoxDecoration(
                                                      color: themeChange.getThem() ? AppColors.darkTextField : AppColors.textField,
                                                      borderRadius: BorderRadius.circular(8),
                                                      border: Border.all(color: themeChange.getThem() ? AppColors.darkTextFieldBorder : AppColors.textFieldBorder),
                                                    ),
                                                    child: Column(
                                                      mainAxisAlignment: MainAxisAlignment.center,
                                                      children: [
                                                        Builder(builder: (_) {
                                                          String img = i == 0 ? controller.doc0Image.value : controller.doc1Image.value;
                                                          if (img.isNotEmpty && !Constant().hasValidUrl(img)) {
                                                            return ClipRRect(borderRadius: BorderRadius.circular(8), child: Image.file(File(img), height: 60, width: double.infinity, fit: BoxFit.cover));
                                                          } else if (img.isNotEmpty) {
                                                            return ClipRRect(borderRadius: BorderRadius.circular(8), child: Image.network(img, height: 60, width: double.infinity, fit: BoxFit.cover, errorBuilder: (_, __, ___) => const Icon(Icons.upload_file, size: 36, color: Colors.grey)));
                                                          }
                                                          return const Icon(Icons.upload_file, size: 36, color: Colors.grey);
                                                        }),
                                                        const SizedBox(height: 4),
                                                        Text(
                                                          Constant.localizationTitle(controller.documentList[i].title),
                                                          style: GoogleFonts.poppins(fontSize: 11),
                                                          textAlign: TextAlign.center,
                                                          maxLines: 2,
                                                          overflow: TextOverflow.ellipsis,
                                                        ),
                                                      ],
                                                    ),
                                                  ),
                                                ),
                                              ),
                                            ],
                                          ],
                                        )),
                                        const SizedBox(height: 10),
                                        ButtonThem.buildButton(
                                          context,
                                          title: "Save KYC Documents".tr,
                                          onPress: () async {
                                            bool hasNew = (controller.doc0Image.value.isNotEmpty && !Constant().hasValidUrl(controller.doc0Image.value)) ||
                                                (controller.doc1Image.value.isNotEmpty && !Constant().hasValidUrl(controller.doc1Image.value));
                                            if (!hasNew) {
                                              ShowToastDialog.showToast("Please select at least one document image".tr);
                                              return;
                                            }
                                            await controller.saveKycDocs();
                                          },
                                        ),
                                        const SizedBox(height: 10),
                                        // Hire-Purchase Tracker tile
                                        if (controller.driverModel.value.ownerId != null)
                                          InkWell(
                                            onTap: () => Get.to(() => HpTrackerScreen(driverModel: controller.driverModel.value)),
                                            child: Container(
                                              decoration: BoxDecoration(
                                                borderRadius: const BorderRadius.all(Radius.circular(4)),
                                                border: Border.all(color: themeChange.getThem() ? AppColors.darkTextFieldBorder : AppColors.textFieldBorder, width: 1),
                                              ),
                                              child: Padding(
                                                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 12),
                                                child: Row(
                                                  children: [
                                                    const Icon(Icons.directions_car_outlined),
                                                    const SizedBox(width: 10),
                                                    Expanded(
                                                      child: Column(
                                                        crossAxisAlignment: CrossAxisAlignment.start,
                                                        children: [
                                                          Text("Hire-Purchase Tracker".tr, style: GoogleFonts.poppins(fontWeight: FontWeight.w500)),
                                                          Text("View vehicle payment progress".tr, style: GoogleFonts.poppins(fontSize: 12, color: Colors.grey)),
                                                        ],
                                                      ),
                                                    ),
                                                    const Icon(Icons.arrow_forward_ios_rounded, size: 16, color: Colors.grey),
                                                  ],
                                                ),
                                              ),
                                            ),
                                          ),
                                        const SizedBox(
                                          height: 20,
                                        ),
                                        ButtonThem.buildButton(
                                          context,
                                          title: "Update Profile".tr,
                                          onPress: () async {
                                            if (controller.fullNameController.value.text.isEmpty) {
                                              ShowToastDialog.showToast("Please enter full name");
                                            } else {
                                              ShowToastDialog.showLoader("Please wait".tr);
                                              if (controller.profileImage.value.isNotEmpty && Constant().hasValidUrl(controller.profileImage.value) == false) {
                                                controller.profileImage.value = await Constant.uploadUserImageToFireStorage(
                                                    File(controller.profileImage.value), "profileImage/${FireStoreUtils.getCurrentUid()}", File(controller.profileImage.value).path.split('/').last);
                                              }

                                              DriverUserModel driverUserModel = controller.driverModel.value;
                                              driverUserModel.fullName = controller.fullNameController.value.text;
                                              driverUserModel.profilePic = controller.profileImage.value;

                                              await FireStoreUtils.updateDriverUser(driverUserModel).then((value) {
                                                ShowToastDialog.closeLoader();
                                                controller.getData();
                                                ShowToastDialog.showToast("Profile update successfully".tr);
                                              });
                                            }
                                          },
                                        ),
                                      ],
                                    ),
                                  ),
                                ),
                              ),
                            ),
                          ),
                  ),
                ],
              ));
        });
  }

  buildBottomSheet(BuildContext context, ProfileController controller) {
    return showModalBottomSheet(
        context: context,
        builder: (context) {
          return StatefulBuilder(builder: (context, setState) {
            return SizedBox(
              height: Responsive.height(22, context),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.center,
                children: [
                  Padding(
                    padding: const EdgeInsets.only(top: 15),
                    child: Text(
                      "Please Select".tr,
                      style: GoogleFonts.poppins(
                        fontSize: 16,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    crossAxisAlignment: CrossAxisAlignment.center,
                    children: [
                      Padding(
                        padding: const EdgeInsets.all(18.0),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.center,
                          children: [
                            IconButton(
                                tooltip: 'Take photo with camera',
                                onPressed: () => controller.pickFile(source: ImageSource.camera),
                                icon: const Icon(
                                  Icons.camera_alt,
                                  size: 32,
                                )),
                            Padding(
                              padding: EdgeInsets.only(top: 3),
                              child: Text("Camera".tr),
                            ),
                          ],
                        ),
                      ),
                      Padding(
                        padding: const EdgeInsets.all(18.0),
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          crossAxisAlignment: CrossAxisAlignment.center,
                          children: [
                            IconButton(
                                tooltip: 'Choose from gallery',
                                onPressed: () => controller.pickFile(source: ImageSource.gallery),
                                icon: const Icon(
                                  Icons.photo_library_sharp,
                                  size: 32,
                                )),
                            Padding(
                              padding: EdgeInsets.only(top: 3),
                              child: Text("Gallery".tr),
                            ),
                          ],
                        ),
                      )
                    ],
                  ),
                ],
              ),
            );
          });
        });
  }
}
