import 'dart:io';

import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:customer/constant/constant.dart';
import 'package:customer/constant/show_toast_dialog.dart';
import 'package:customer/model/customer_document_model.dart';
import 'package:customer/model/document_model.dart';
import 'package:customer/utils/fire_store_utils.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:image_picker/image_picker.dart';
import 'package:intl/intl.dart';

class KycDocumentUploadController extends GetxController {
  Rx<DocumentModel> documentModel = DocumentModel().obs;

  Rx<TextEditingController> documentNumberController = TextEditingController().obs;
  Rx<TextEditingController> expireAtController = TextEditingController().obs;
  Rx<DateTime?> selectedDate = DateTime.now().obs;

  RxString frontImage = ''.obs;
  RxString backImage = ''.obs;

  RxBool isLoading = true.obs;

  Rx<Documents> documents = Documents().obs;

  @override
  void onInit() {
    _loadArguments();
    super.onInit();
  }

  void _loadArguments() async {
    dynamic args = Get.arguments;
    if (args != null) {
      documentModel.value = args['documentModel'];
    }
    await _loadExistingDocument();
    update();
  }

  Future<void> _loadExistingDocument() async {
    CustomerDocumentModel? existing = await FireStoreUtils.getDocumentOfCustomer();
    isLoading.value = false;
    if (existing != null && existing.documents != null) {
      var match = existing.documents!.where((e) => e.documentId == documentModel.value.id);
      if (match.isNotEmpty) {
        documents.value = match.first;
        documentNumberController.value.text = documents.value.documentNumber ?? '';
        frontImage.value = documents.value.frontImage ?? '';
        backImage.value = documents.value.backImage ?? '';
        if (documents.value.expireAt != null) {
          selectedDate.value = documents.value.expireAt!.toDate();
          expireAtController.value.text = DateFormat('dd-MM-yyyy').format(selectedDate.value!);
        }
      }
    }
  }

  final ImagePicker _imagePicker = ImagePicker();

  Future<void> pickFile({required ImageSource source, required String type}) async {
    try {
      XFile? image = await _imagePicker.pickImage(source: source);
      if (image == null) return;
      Get.back();
      if (type == 'front') {
        frontImage.value = image.path;
      } else {
        backImage.value = image.path;
      }
    } on PlatformException catch (e) {
      ShowToastDialog.showToast('Failed to pick image: $e');
    }
  }

  Future<void> uploadDocument() async {
    if (frontImage.value.isNotEmpty && Constant().hasValidUrl(frontImage.value) == false) {
      String fileName = File(frontImage.value).path.split('/').last;
      frontImage.value = await Constant.uploadUserImageToFireStorage(
          File(frontImage.value), 'customerDocument/${FireStoreUtils.getCurrentUid()}', fileName);
    }

    if (backImage.value.isNotEmpty && Constant().hasValidUrl(backImage.value) == false) {
      String fileName = File(backImage.value).path.split('/').last;
      backImage.value = await Constant.uploadUserImageToFireStorage(
          File(backImage.value), 'customerDocument/${FireStoreUtils.getCurrentUid()}', fileName);
    }

    documents.value.documentId = documentModel.value.id;
    documents.value.documentNumber = documentNumberController.value.text;
    documents.value.frontImage = frontImage.value;
    documents.value.backImage = backImage.value;
    documents.value.verified = false;
    if (documentModel.value.expireAt == true) {
      documents.value.expireAt = Timestamp.fromDate(selectedDate.value!);
    }

    bool success = await FireStoreUtils.uploadCustomerDocument(documents.value);
    ShowToastDialog.closeLoader();
    if (success) {
      ShowToastDialog.showToast('Document uploaded successfully'.tr);
      Get.back();
    }
  }
}
