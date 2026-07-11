import 'dart:convert';

import 'package:driver/constant/show_toast_dialog.dart';
import 'package:driver/model/bank_details_model.dart';
import 'package:driver/utils/fire_store_utils.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:http/http.dart' as http;

class BankDetailsController extends GetxController {
  Rx<TextEditingController> bankNameController = TextEditingController().obs;
  Rx<TextEditingController> branchNameController = TextEditingController().obs;
  Rx<TextEditingController> holderNameController = TextEditingController().obs;
  Rx<TextEditingController> accountNumberController = TextEditingController().obs;
  Rx<TextEditingController> otherInformationController = TextEditingController().obs;

  // Paystack bank list (each item: {'name': ..., 'code': ...}) + selection state,
  // used so payouts can be sent to the driver's bank automatically.
  RxList<Map<String, String>> banks = <Map<String, String>>[].obs;
  RxString bankCode = ''.obs;
  RxBool accountVerified = false.obs;
  String? _paystackSecret;

  @override
  void onInit() {
    getBankDetails();
    loadBanks();
    super.onInit();
  }

  RxBool isLoading = true.obs;
  Rx<BankDetailsModel> bankDetailsModel = BankDetailsModel().obs;

  getBankDetails() async {
    await FireStoreUtils.getBankDetails().then((value) {
      if (value != null) {
        bankDetailsModel.value = value;
        bankNameController.value.text = bankDetailsModel.value.bankName ?? '';
        branchNameController.value.text = bankDetailsModel.value.branchName ?? '';
        holderNameController.value.text = bankDetailsModel.value.holderName ?? '';
        accountNumberController.value.text = bankDetailsModel.value.accountNumber ?? '';
        otherInformationController.value.text = bankDetailsModel.value.otherInformation ?? '';
        bankCode.value = bankDetailsModel.value.bankCode ?? '';
        accountVerified.value = (bankDetailsModel.value.bankCode ?? '').isNotEmpty;
      }
    });
    isLoading.value = false;
    update();
  }

  Future<String?> _secret() async {
    if (_paystackSecret != null && _paystackSecret!.isNotEmpty) return _paystackSecret;
    _paystackSecret = (await FireStoreUtils.getPayment())?.payStack?.secretKey;
    return _paystackSecret;
  }

  Future<void> loadBanks() async {
    try {
      final secret = await _secret();
      if (secret == null || secret.isEmpty) return;
      final res = await http.get(
        Uri.parse('https://api.paystack.co/bank?country=nigeria&perPage=200'),
        headers: {'Authorization': 'Bearer $secret'},
      ).timeout(const Duration(seconds: 30));
      final data = jsonDecode(res.body);
      if (data['status'] == true && data['data'] is List) {
        final list = <Map<String, String>>[];
        for (final b in (data['data'] as List)) {
          list.add({'name': (b['name'] ?? '').toString(), 'code': (b['code'] ?? '').toString()});
        }
        list.sort((a, b) => a['name']!.toLowerCase().compareTo(b['name']!.toLowerCase()));
        banks.value = list;
      }
    } catch (e) {
      debugPrint('loadBanks error: $e');
    }
  }

  void selectBank(String name, String code) {
    bankNameController.value.text = name;
    bankCode.value = code;
    // Bank changed — the previously verified name no longer applies.
    accountVerified.value = false;
    holderNameController.value.text = '';
    update();
  }

  Future<void> verifyAccount() async {
    final acct = accountNumberController.value.text.trim();
    if (bankCode.value.isEmpty) {
      ShowToastDialog.showToast("Please select your bank first".tr);
      return;
    }
    if (acct.length < 10) {
      ShowToastDialog.showToast("Enter a valid 10-digit account number".tr);
      return;
    }
    ShowToastDialog.showLoader("Verifying account...".tr);
    try {
      final secret = await _secret();
      final res = await http.get(
        Uri.parse('https://api.paystack.co/bank/resolve?account_number=$acct&bank_code=${bankCode.value}'),
        headers: {'Authorization': 'Bearer $secret'},
      ).timeout(const Duration(seconds: 30));
      ShowToastDialog.closeLoader();
      final data = jsonDecode(res.body);
      if (data['status'] == true && data['data'] != null && data['data']['account_name'] != null) {
        holderNameController.value.text = data['data']['account_name'].toString();
        accountVerified.value = true;
        ShowToastDialog.showToast("Account verified".tr);
      } else {
        accountVerified.value = false;
        ShowToastDialog.showToast((data['message'] ?? "Could not verify this account").toString());
      }
      update();
    } catch (e) {
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast("Verification failed. Please check your connection.".tr);
    }
  }
}
