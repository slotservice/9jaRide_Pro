import 'dart:io';

import 'package:cached_network_image/cached_network_image.dart';
import 'package:customer/constant/constant.dart';
import 'package:customer/constant/show_toast_dialog.dart';
import 'package:customer/controller/kyc_document_upload_controller.dart';
import 'package:customer/themes/app_colors.dart';
import 'package:customer/themes/button_them.dart';
import 'package:customer/themes/responsive.dart';
import 'package:customer/themes/text_field_them.dart';
import 'package:customer/utils/DarkThemeProvider.dart';
import 'package:dotted_border/dotted_border.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:image_picker/image_picker.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';

class KycDocumentUploadScreen extends StatelessWidget {
  const KycDocumentUploadScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final themeChange = Provider.of<DarkThemeProvider>(context);
    return GetX<KycDocumentUploadController>(
      init: KycDocumentUploadController(),
      builder: (controller) {
        String title = _safeTitle(controller);
        return Scaffold(
          appBar: AppBar(
            backgroundColor: AppColors.lightprimary,
            centerTitle: true,
            title: Text(title, style: GoogleFonts.poppins(color: Colors.white)),
            leading: InkWell(
              onTap: () => Get.back(),
              child: const Icon(Icons.arrow_back, color: Colors.white),
            ),
          ),
          backgroundColor: AppColors.lightprimary,
          body: Column(
            children: [
              SizedBox(height: Responsive.width(6, context)),
              Expanded(
                child: Container(
                  decoration: BoxDecoration(
                    color: Theme.of(context).colorScheme.background,
                    borderRadius: const BorderRadius.only(topLeft: Radius.circular(25), topRight: Radius.circular(25)),
                  ),
                  child: controller.isLoading.value
                      ? Constant.loader(isDarkTheme: themeChange.getThem())
                      : SingleChildScrollView(
                          child: Padding(
                            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 20),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                TextFieldThem.buildTextFiled(
                                  context,
                                  hintText: '$title number'.tr,
                                  controller: controller.documentNumberController.value,
                                  enable: controller.documents.value.verified != true,
                                ),
                                if (controller.documentModel.value.expireAt == true) ...[
                                  const SizedBox(height: 10),
                                  InkWell(
                                    onTap: () async {
                                      if (controller.documents.value.verified == true) return;
                                      DateTime? picked = await Constant.selectFetureDate(context);
                                      if (picked != null) {
                                        controller.selectedDate.value = picked;
                                        controller.expireAtController.value.text = DateFormat('dd-MM-yyyy').format(picked);
                                      }
                                    },
                                    child: TextFieldThem.buildTextFiledWithSuffixIcon(
                                      context,
                                      hintText: 'Expiry date'.tr,
                                      controller: controller.expireAtController.value,
                                      enable: false,
                                      suffixIcon: const Icon(Icons.calendar_month),
                                    ),
                                  ),
                                ],
                                if (controller.documentModel.value.frontSide == true) ...[
                                  const SizedBox(height: 16),
                                  Text(
                                    'Front side of $title'.tr,
                                    style: GoogleFonts.poppins(fontSize: 15, fontWeight: FontWeight.w600),
                                  ),
                                  const SizedBox(height: 8),
                                  _imagePicker(context, themeChange, controller, 'front'),
                                ],
                                if (controller.documentModel.value.backSide == true) ...[
                                  const SizedBox(height: 16),
                                  Text(
                                    'Back side of $title'.tr,
                                    style: GoogleFonts.poppins(fontSize: 15, fontWeight: FontWeight.w600),
                                  ),
                                  const SizedBox(height: 8),
                                  _imagePicker(context, themeChange, controller, 'back'),
                                ],
                                const SizedBox(height: 30),
                                if (controller.documents.value.verified != true)
                                  ButtonThem.buildButton(
                                    context,
                                    btnWidthRatio: 1,
                                    title: 'Save Document'.tr,
                                    onPress: () {
                                      if (controller.documentNumberController.value.text.isEmpty) {
                                        ShowToastDialog.showToast('Please enter document number'.tr);
                                      } else if (controller.documentModel.value.frontSide == true && controller.frontImage.value.isEmpty) {
                                        ShowToastDialog.showToast('Please upload front side of document.'.tr);
                                      } else if (controller.documentModel.value.backSide == true && controller.backImage.value.isEmpty) {
                                        ShowToastDialog.showToast('Please upload back side of document.'.tr);
                                      } else {
                                        ShowToastDialog.showLoader('Please wait..'.tr);
                                        controller.uploadDocument();
                                      }
                                    },
                                  ),
                              ],
                            ),
                          ),
                        ),
                ),
              ),
            ],
          ),
        );
      },
    );
  }

  Widget _imagePicker(BuildContext context, DarkThemeProvider themeChange, KycDocumentUploadController controller, String type) {
    String imageValue = type == 'front' ? controller.frontImage.value : controller.backImage.value;
    bool isVerified = controller.documents.value.verified == true;

    if (imageValue.isNotEmpty) {
      return InkWell(
        onTap: () {
          if (!isVerified) _showImageSourceSheet(context, controller, type);
        },
        child: SizedBox(
          height: Responsive.height(20, context),
          width: double.infinity,
          child: ClipRRect(
            borderRadius: const BorderRadius.all(Radius.circular(10)),
            child: Constant().hasValidUrl(imageValue)
                ? CachedNetworkImage(
                    imageUrl: imageValue,
                    fit: BoxFit.cover,
                    placeholder: (context, url) => Constant.loader(isDarkTheme: themeChange.getThem()),
                    errorWidget: (context, url, error) => const Icon(Icons.broken_image),
                  )
                : Image.file(File(imageValue), fit: BoxFit.cover),
          ),
        ),
      );
    }

    return InkWell(
      onTap: () => _showImageSourceSheet(context, controller, type),
      child: DottedBorder(
        options: RoundedRectDottedBorderOptions(
          radius: const Radius.circular(12),
          dashPattern: const [6, 6, 6, 6],
          color: AppColors.textFieldBorder,
        ),
        child: SizedBox(
          height: Responsive.height(18, context),
          width: double.infinity,
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Container(
                height: Responsive.height(7, context),
                width: Responsive.width(18, context),
                decoration: const BoxDecoration(color: Colors.black, borderRadius: BorderRadius.all(Radius.circular(10))),
                padding: const EdgeInsets.all(10),
                child: Image.asset('assets/icons/document_placeholder.png'),
              ),
              const SizedBox(height: 8),
              Text('Add photo'.tr),
            ],
          ),
        ),
      ),
    );
  }

  void _showImageSourceSheet(BuildContext context, KycDocumentUploadController controller, String type) {
    showModalBottomSheet(
      context: context,
      builder: (context) => SizedBox(
        height: 160,
        child: Column(
          children: [
            Padding(
              padding: const EdgeInsets.only(top: 14),
              child: Text('Select source'.tr, style: GoogleFonts.poppins(fontSize: 16, fontWeight: FontWeight.w600)),
            ),
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                _sourceOption(context, Icons.camera_alt, 'Camera'.tr, () => controller.pickFile(source: ImageSource.camera, type: type)),
                _sourceOption(context, Icons.photo_library_sharp, 'Gallery'.tr, () => controller.pickFile(source: ImageSource.gallery, type: type)),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _sourceOption(BuildContext context, IconData icon, String label, VoidCallback onTap) {
    return Padding(
      padding: const EdgeInsets.all(18),
      child: Column(
        children: [
          IconButton(onPressed: onTap, icon: Icon(icon, size: 32)),
          Text(label),
        ],
      ),
    );
  }

  String _safeTitle(KycDocumentUploadController controller) {
    try {
      return Constant.localizationTitle(controller.documentModel.value.title);
    } catch (_) {
      List? t = controller.documentModel.value.title;
      if (t != null && t.isNotEmpty) {
        return t.first.title ?? t.first.name ?? 'Document';
      }
      return 'Document';
    }
  }
}
