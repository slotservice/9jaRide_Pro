import 'package:driver/constant/constant.dart';
import 'package:driver/constant/show_toast_dialog.dart';
import 'package:driver/controller/bank_details_controller.dart';
import 'package:driver/model/bank_details_model.dart';
import 'package:driver/themes/app_colors.dart';
import 'package:driver/themes/button_them.dart';
import 'package:driver/themes/responsive.dart';
import 'package:driver/themes/text_field_them.dart';
import 'package:driver/utils/DarkThemeProvider.dart';
import 'package:driver/utils/fire_store_utils.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';

class BankDetailsScreen extends StatelessWidget {
  const BankDetailsScreen({super.key});

  void _selectBank(BuildContext context, BankDetailsController controller) {
    String query = '';
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Theme.of(context).colorScheme.background,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(18))),
      builder: (ctx) {
        return StatefulBuilder(builder: (ctx, setSheetState) {
          final all = controller.banks;
          final filtered = query.isEmpty
              ? all
              : all.where((b) => (b['name'] ?? '').toLowerCase().contains(query.toLowerCase())).toList();
          return Padding(
            padding: EdgeInsets.only(bottom: MediaQuery.of(ctx).viewInsets.bottom),
            child: SizedBox(
              height: Responsive.height(70, context),
              child: Column(
                children: [
                  const SizedBox(height: 12),
                  Text("Select your bank".tr, style: GoogleFonts.poppins(fontWeight: FontWeight.w600, fontSize: 16)),
                  Padding(
                    padding: const EdgeInsets.all(14),
                    child: TextField(
                      autofocus: true,
                      onChanged: (v) => setSheetState(() => query = v),
                      decoration: InputDecoration(
                        hintText: "Search bank".tr,
                        prefixIcon: const Icon(Icons.search),
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(10)),
                      ),
                    ),
                  ),
                  if (all.isEmpty)
                    Padding(
                      padding: const EdgeInsets.all(20),
                      child: Text("Loading banks...".tr, style: GoogleFonts.poppins(color: Colors.grey)),
                    ),
                  Expanded(
                    child: ListView.builder(
                      itemCount: filtered.length,
                      itemBuilder: (c, i) {
                        final bank = filtered[i];
                        return ListTile(
                          title: Text(bank['name'] ?? '', style: GoogleFonts.poppins(fontSize: 14)),
                          onTap: () {
                            controller.selectBank(bank['name'] ?? '', bank['code'] ?? '');
                            Navigator.pop(ctx);
                          },
                        );
                      },
                    ),
                  ),
                ],
              ),
            ),
          );
        });
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    final themeChange = Provider.of<DarkThemeProvider>(context);
    return GetX<BankDetailsController>(
        init: BankDetailsController(),
        builder: (controller) {
          return Scaffold(
            backgroundColor: AppColors.lightprimary,
            body: Column(
              children: [
                SizedBox(
                  height: Responsive.width(12, context),
                  width: Responsive.width(100, context),
                ),
                Expanded(
                  child: Container(
                    height: Responsive.height(100, context),
                    width: Responsive.width(100, context),
                    decoration: BoxDecoration(color: Theme.of(context).colorScheme.background, borderRadius: const BorderRadius.only(topLeft: Radius.circular(25), topRight: Radius.circular(25))),
                    child: controller.isLoading.value
                        ? Constant.loader(isDarkTheme: themeChange.getThem())
                        : Padding(
                            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
                            child: SingleChildScrollView(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text("Bank Name".tr, style: GoogleFonts.poppins()),
                                  const SizedBox(height: 5),
                                  InkWell(
                                    onTap: () => _selectBank(context, controller),
                                    child: Container(
                                      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 15),
                                      decoration: BoxDecoration(
                                        color: themeChange.getThem() ? AppColors.darkTextField : AppColors.textField,
                                        borderRadius: BorderRadius.circular(10),
                                        border: Border.all(color: themeChange.getThem() ? AppColors.darkTextFieldBorder : AppColors.textFieldBorder),
                                      ),
                                      child: Row(
                                        children: [
                                          Expanded(
                                            child: Text(
                                              controller.bankNameController.value.text.isEmpty ? "Select your bank".tr : controller.bankNameController.value.text,
                                              style: GoogleFonts.poppins(color: controller.bankNameController.value.text.isEmpty ? Colors.grey : null),
                                            ),
                                          ),
                                          const Icon(Icons.keyboard_arrow_down),
                                        ],
                                      ),
                                    ),
                                  ),
                                  const SizedBox(height: 10),
                                  Text("Account Number".tr, style: GoogleFonts.poppins()),
                                  const SizedBox(height: 5),
                                  TextFieldThem.buildTextFiled(context, hintText: 'Account Number'.tr, controller: controller.accountNumberController.value),
                                  const SizedBox(height: 10),
                                  Row(
                                    children: [
                                      OutlinedButton.icon(
                                        onPressed: () => controller.verifyAccount(),
                                        icon: const Icon(Icons.verified_user_outlined, size: 18),
                                        label: Text("Verify Account".tr, style: GoogleFonts.poppins(fontSize: 13)),
                                      ),
                                      const SizedBox(width: 10),
                                      if (controller.accountVerified.value)
                                        Row(
                                          children: [
                                            const Icon(Icons.check_circle, color: Colors.green, size: 18),
                                            const SizedBox(width: 4),
                                            Text("Verified".tr, style: GoogleFonts.poppins(fontSize: 12, color: Colors.green)),
                                          ],
                                        ),
                                    ],
                                  ),
                                  const SizedBox(height: 10),
                                  Text("Account Holder Name".tr, style: GoogleFonts.poppins()),
                                  const SizedBox(height: 5),
                                  TextFieldThem.buildTextFiled(context, hintText: 'Account Holder Name'.tr, controller: controller.holderNameController.value),
                                  const SizedBox(height: 10),
                                  Text("Branch Name".tr, style: GoogleFonts.poppins()),
                                  const SizedBox(height: 5),
                                  TextFieldThem.buildTextFiled(context, hintText: 'Branch Name'.tr, controller: controller.branchNameController.value),
                                  const SizedBox(height: 10),
                                  Text("Other Information".tr, style: GoogleFonts.poppins()),
                                  const SizedBox(height: 5),
                                  TextFieldThem.buildTextFiled(context, hintText: 'Other Information'.tr, controller: controller.otherInformationController.value),
                                  const SizedBox(height: 40),
                                  ButtonThem.buildButton(
                                    context,
                                    title: "Save".tr,
                                    onPress: () async {
                                      if (controller.bankNameController.value.text.isEmpty || controller.bankCode.value.isEmpty) {
                                        ShowToastDialog.showToast("Please select your bank".tr);
                                      } else if (controller.accountNumberController.value.text.isEmpty) {
                                        ShowToastDialog.showToast("Please enter account number".tr);
                                      } else if (controller.holderNameController.value.text.isEmpty) {
                                        ShowToastDialog.showToast("Please verify your account or enter the holder name".tr);
                                      } else {
                                        ShowToastDialog.showLoader("Please wait".tr);
                                        BankDetailsModel bankDetailsModel = controller.bankDetailsModel.value;

                                        bankDetailsModel.userId = FireStoreUtils.getCurrentUid();
                                        bankDetailsModel.bankName = controller.bankNameController.value.text;
                                        bankDetailsModel.bankCode = controller.bankCode.value;
                                        bankDetailsModel.branchName = controller.branchNameController.value.text;
                                        bankDetailsModel.holderName = controller.holderNameController.value.text;
                                        bankDetailsModel.accountNumber = controller.accountNumberController.value.text;
                                        bankDetailsModel.otherInformation = controller.otherInformationController.value.text;

                                        await FireStoreUtils.updateBankDetails(bankDetailsModel).then((value) {
                                          ShowToastDialog.closeLoader();
                                          ShowToastDialog.showToast("Bank details update successfully".tr);
                                        });
                                      }
                                    },
                                  )
                                ],
                              ),
                            ),
                          ),
                  ),
                ),
              ],
            ),
          );
        });
  }
}
