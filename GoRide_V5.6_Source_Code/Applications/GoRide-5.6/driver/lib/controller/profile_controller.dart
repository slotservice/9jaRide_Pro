import 'dart:io';

import 'package:driver/constant/constant.dart';
import 'package:driver/constant/show_toast_dialog.dart';
import 'package:driver/model/document_model.dart';
import 'package:driver/model/driver_document_model.dart';
import 'package:driver/model/driver_user_model.dart';
import 'package:driver/utils/fire_store_utils.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:image_picker/image_picker.dart';

class ProfileController extends GetxController {
  RxBool isLoading = true.obs;
  Rx<DriverUserModel> driverModel = DriverUserModel().obs;

  Rx<TextEditingController> fullNameController = TextEditingController().obs;
  Rx<TextEditingController> emailController = TextEditingController().obs;
  Rx<TextEditingController> phoneNumberController = TextEditingController().obs;
  Rx<TextEditingController> countryCode = TextEditingController(text: Constant.defaultCountryCode).obs;
  Rx<TextEditingController> countryISOCode = TextEditingController(text: Constant.defaultCountryCode).obs;

  @override
  void onInit() {
    // TODO: implement onInit
    getData();
    super.onInit();
  }

  getData() async {
    await FireStoreUtils.getDriverProfile(FireStoreUtils.getCurrentUid()).then((value) {
      if (value != null) {
        driverModel.value = value;

        phoneNumberController.value.text = driverModel.value.phoneNumber.toString();
        countryCode.value.text = driverModel.value.countryCode.toString();
        countryISOCode.value.text = driverModel.value.countryISOCode.toString();
        emailController.value.text = driverModel.value.email.toString();
        fullNameController.value.text = driverModel.value.fullName.toString();
        profileImage.value = driverModel.value.profilePic ?? '';
        isLoading.value = false;
      }
    });

    await FireStoreUtils.getDocumentList().then((value) {
      documentList.value = value;
    });

    await FireStoreUtils.getDocumentOfDriver().then((value) {
      if (value != null) {
        driverDocumentList.value = value.documents ?? [];
        for (int i = 0; i < documentList.length && i < 2; i++) {
          var existing = driverDocumentList.where((d) => d.documentId == documentList[i].id);
          if (existing.isNotEmpty && (existing.first.frontImage?.isNotEmpty == true)) {
            if (i == 0) doc0Image.value = existing.first.frontImage!;
            if (i == 1) doc1Image.value = existing.first.frontImage!;
          }
        }
      }
    });
  }

  RxList<DocumentModel> documentList = <DocumentModel>[].obs;
  RxList<Documents> driverDocumentList = <Documents>[].obs;
  RxString doc0Image = ''.obs;
  RxString doc1Image = ''.obs;

  Future<void> saveKycDocs() async {
    ShowToastDialog.showLoader("Please wait".tr);
    for (int i = 0; i < documentList.length && i < 2; i++) {
      String imgPath = i == 0 ? doc0Image.value : doc1Image.value;
      if (imgPath.isEmpty || Constant().hasValidUrl(imgPath)) continue;

      String uploaded = await Constant.uploadUserImageToFireStorage(
        File(imgPath),
        "driverDocument/${FireStoreUtils.getCurrentUid()}_${documentList[i].id}",
        imgPath.split('/').last,
      );

      Documents doc = Documents();
      doc.documentId = documentList[i].id;
      doc.frontImage = uploaded;
      doc.verified = false;

      var existing = driverDocumentList.where((d) => d.documentId == documentList[i].id);
      if (existing.isNotEmpty) {
        doc.documentNumber = existing.first.documentNumber;
        doc.backImage = existing.first.backImage;
        doc.expireAt = existing.first.expireAt;
      }

      await FireStoreUtils.uploadDriverDocument(doc);
    }
    ShowToastDialog.closeLoader();
    ShowToastDialog.showToast("Documents saved successfully".tr);
  }

  final ImagePicker _imagePicker = ImagePicker();
  RxString profileImage = "".obs;

  Future pickFile({required ImageSource source}) async {
    try {
      XFile? image = await _imagePicker.pickImage(source: source);
      if (image == null) return;
      Get.back();
      profileImage.value = image.path;
    } on PlatformException catch (e) {
      ShowToastDialog.showToast("Failed to Pick : \n $e");
    }
  }

  Future pickDocImage({required ImageSource source, required int docIndex}) async {
    try {
      XFile? image = await _imagePicker.pickImage(source: source);
      if (image == null) return;
      if (docIndex == 0) doc0Image.value = image.path;
      if (docIndex == 1) doc1Image.value = image.path;
    } on PlatformException catch (e) {
      ShowToastDialog.showToast("Failed to Pick : \n $e");
    }
  }
}
