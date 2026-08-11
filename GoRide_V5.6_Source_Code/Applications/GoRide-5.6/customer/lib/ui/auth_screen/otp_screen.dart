import 'dart:async';
import 'dart:convert';

import 'package:customer/constant/constant.dart';
import 'package:customer/constant/show_toast_dialog.dart';
import 'package:customer/controller/otp_controller.dart';
import 'package:customer/model/user_model.dart';
import 'package:customer/themes/app_colors.dart';
import 'package:customer/themes/button_them.dart';
import 'package:customer/ui/auth_screen/information_screen.dart';
import 'package:customer/ui/dashboard_screen.dart';
import 'package:customer/ui/kyc/kyc_screen.dart';
import 'package:customer/utils/DarkThemeProvider.dart';
import 'package:customer/utils/fire_store_utils.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:http/http.dart' as http;
import 'package:pin_code_fields/pin_code_fields.dart';
import 'package:provider/provider.dart';

const String _backendUrl = 'https://app.9jaridepro.com';

class OtpScreen extends StatelessWidget {
  const OtpScreen({Key? key}) : super(key: key);

  Future<void> _verifyAndLogin(BuildContext context, OtpController controller) async {
    if (controller.otpController.value.text.length != 6) {
      ShowToastDialog.showToast("Please Enter Valid OTP".tr);
      return;
    }

    ShowToastDialog.showLoader("Verify OTP".tr);

    try {
      // Verify OTP + mint Firebase custom token on the backend. The OTP is now
      // verified server-side (the backend calls Termii with pin_id + pin); it is
      // no longer verified client-side, so this endpoint cannot be used to obtain
      // a login token without a valid OTP (C1).
      final fullPhone = controller.countryCode.value + controller.phoneNumber.value;
      final tokenRes = await http
          .post(
            Uri.parse('$_backendUrl/api/auth/custom-token'),
            headers: {'Content-Type': 'application/json'},
            body: jsonEncode({
              'phone': fullPhone,
              'pin_id': controller.pinId.value,
              'pin': controller.otpController.value.text,
            }),
          )
          .timeout(const Duration(seconds: 30));

      final tokenData = jsonDecode(tokenRes.body);
      if (tokenData['token'] == null) {
        ShowToastDialog.closeLoader();
        ShowToastDialog.showToast((tokenData['error'] ?? "Code is Invalid").toString().tr);
        return;
      }

      // Step 3: Sign in to Firebase with custom token
      await FirebaseAuth.instance.signInWithCustomToken(tokenData['token']).then((value) async {
        if (value.additionalUserInfo!.isNewUser) {
          UserModel userModel = UserModel();
          userModel.id = value.user!.uid;
          userModel.countryCode = controller.countryCode.value;
          userModel.countryISOCode = controller.countryISOCode.value;
          userModel.phoneNumber = controller.phoneNumber.value;
          userModel.loginType = Constant.phoneLoginType;

          ShowToastDialog.closeLoader();
          Get.to(const InformationScreen(), arguments: {
            "userModel": userModel,
          });
        } else {
          await FireStoreUtils.userExitCustomerOrDriverRole(value.user!.uid).then((userExit) async {
            ShowToastDialog.closeLoader();
            if (userExit == '') {
              UserModel userModel = UserModel();
              userModel.id = value.user!.uid;
              userModel.countryCode = controller.countryCode.value;
              userModel.countryISOCode = controller.countryISOCode.value;
              userModel.phoneNumber = controller.phoneNumber.value;
              userModel.loginType = Constant.phoneLoginType;

              ShowToastDialog.closeLoader();
              Get.to(const InformationScreen(), arguments: {
                "userModel": userModel,
              });
            } else if (userExit == Constant.currentUserType) {
              UserModel? userModel = await FireStoreUtils.getUserProfile(value.user!.uid);
              if (userModel != null) {
                if (userModel.isActive == true) {
                  var existingDocs = await FireStoreUtils.getDocumentOfCustomer();
                  bool hasUploaded = existingDocs != null && (existingDocs.documents?.isNotEmpty == true);
                  if (hasUploaded) {
                    Get.offAll(const DashBoardScreen());
                  } else {
                    Get.offAll(const KycScreen());
                  }
                } else {
                  await FirebaseAuth.instance.signOut();
                  ShowToastDialog.showToast("This user is disable please contact administrator".tr);
                }
              } else {
                ShowToastDialog.showToast("Something went wrong, please try again.".tr);
              }
            } else {
              UserModel? customerModel = await FireStoreUtils.getUserProfile(value.user!.uid);
              if (customerModel != null) {
                if (customerModel.isActive == true) {
                  var existingDocs = await FireStoreUtils.getDocumentOfCustomer();
                  bool hasUploaded = existingDocs != null && (existingDocs.documents?.isNotEmpty == true);
                  if (hasUploaded) {
                    Get.offAll(const DashBoardScreen());
                  } else {
                    Get.offAll(const KycScreen());
                  }
                } else {
                  await FirebaseAuth.instance.signOut();
                  ShowToastDialog.showToast("This user is disable please contact administrator".tr);
                }
              } else {
                UserModel newUser = UserModel();
                newUser.id = value.user!.uid;
                newUser.countryCode = controller.countryCode.value;
                newUser.countryISOCode = controller.countryISOCode.value;
                newUser.phoneNumber = controller.phoneNumber.value;
                newUser.loginType = Constant.phoneLoginType;
                Get.to(const InformationScreen(), arguments: {"userModel": newUser});
              }
            }
          });
        }
      });
    } on TimeoutException catch (_) {
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast("Network timeout. Please check your internet connection and try again.".tr);
    } catch (e) {
      debugPrint('OTP verify error: $e');
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast("Login failed. Please check your connection and try again.".tr);
    }
  }

  @override
  Widget build(BuildContext context) {
    final themeChange = Provider.of<DarkThemeProvider>(context);
    return GetX<OtpController>(
        init: OtpController(),
        builder: (controller) {
          return Scaffold(
            body: SingleChildScrollView(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    width: double.infinity,
                    height: 220,
                    color: Colors.black,
                    child: Image.asset("assets/images/login_image.png", fit: BoxFit.contain),
                  ),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Padding(
                          padding: const EdgeInsets.only(top: 10),
                          child: Text("Verify Phone Number".tr, style: GoogleFonts.poppins(fontWeight: FontWeight.w600, fontSize: 18)),
                        ),
                        Padding(
                          padding: const EdgeInsets.only(top: 2),
                          child: Text("We just send a verification code to \n${controller.countryCode.value + controller.phoneNumber.value}".tr, style: GoogleFonts.poppins()),
                        ),
                        Padding(
                          padding: const EdgeInsets.only(top: 50),
                          child: PinCodeTextField(
                            length: 6,
                            appContext: context,
                            keyboardType: TextInputType.phone,
                            pinTheme: PinTheme(
                              fieldHeight: 50,
                              fieldWidth: 50,
                              activeColor: themeChange.getThem() ? AppColors.darkTextFieldBorder : AppColors.textFieldBorder,
                              selectedColor: themeChange.getThem() ? AppColors.darkTextFieldBorder : AppColors.textFieldBorder,
                              inactiveColor: themeChange.getThem() ? AppColors.darkTextFieldBorder : AppColors.textFieldBorder,
                              activeFillColor: themeChange.getThem() ? AppColors.darkTextField : AppColors.textField,
                              inactiveFillColor: themeChange.getThem() ? AppColors.darkTextField : AppColors.textField,
                              selectedFillColor: themeChange.getThem() ? AppColors.darkTextField : AppColors.textField,
                              shape: PinCodeFieldShape.box,
                              borderRadius: BorderRadius.circular(10),
                            ),
                            enableActiveFill: true,
                            cursorColor: AppColors.lightprimary,
                            controller: controller.otpController.value,
                            onCompleted: (v) async {},
                            onChanged: (value) {},
                          ),
                        ),
                        const SizedBox(height: 30),
                        ButtonThem.buildButton(
                          context,
                          title: "Verify".tr,
                          onPress: () => _verifyAndLogin(context, controller),
                        ),
                      ],
                    ),
                  )
                ],
              ),
            ),
          );
        });
  }
}
