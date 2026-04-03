import 'package:owner/constant/constant.dart';
import 'package:owner/constant/show_toast_dialog.dart';
import 'package:owner/model/owner_user_model.dart';
import 'package:owner/utils/fire_store_utils.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:image_picker/image_picker.dart';

class ProfileController extends GetxController {
  RxBool isLoading = true.obs;
  Rx<OwnerUserModel> ownerModel = OwnerUserModel().obs;

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

  Future<void> getData() async {
    await FireStoreUtils.getOwnerProfile(FireStoreUtils.getCurrentUid()).then((value) {
      if (value != null) {
        ownerModel.value = value;

        phoneNumberController.value.text = ownerModel.value.phoneNumber.toString();
        countryCode.value.text = ownerModel.value.countryCode.toString();
        countryISOCode.value.text = ownerModel.value.countryISOCode.toString();
        emailController.value.text = ownerModel.value.email.toString();
        fullNameController.value.text = ownerModel.value.fullName.toString();
        profileImage.value = ownerModel.value.profilePic ?? '';
        isLoading.value = false;
      }
    });
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
}
