import 'dart:convert';
import 'dart:developer';

import 'package:driver/constant/constant.dart';
import 'package:driver/constant/show_toast_dialog.dart';
import 'package:driver/controller/otp_controller.dart';
import 'package:driver/model/driver_user_model.dart';
import 'package:driver/themes/app_colors.dart';
import 'package:driver/themes/button_them.dart';
import 'package:driver/ui/auth_screen/information_screen.dart';
import 'package:driver/ui/dashboard_screen.dart';
import 'package:driver/ui/subscription_plan_screen/subscription_list_screen.dart';
import 'package:driver/utils/DarkThemeProvider.dart';
import 'package:driver/utils/fire_store_utils.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:http/http.dart' as http;
import 'package:pin_code_fields/pin_code_fields.dart';
import 'package:provider/provider.dart';

import '../../themes/responsive.dart';

const String _backendUrl = 'http://148.230.120.40';

class OtpScreen extends StatelessWidget {
  const OtpScreen({super.key});

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
      final tokenRes = await http.post(
        Uri.parse('$_backendUrl/api/auth/custom-token'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({
          'phone': fullPhone,
          'pin_id': controller.pinId.value,
          'pin': controller.otpController.value.text,
        }),
      );

      final tokenData = jsonDecode(tokenRes.body);
      if (tokenData['token'] == null) {
        ShowToastDialog.closeLoader();
        ShowToastDialog.showToast("Code is Invalid".tr);
        return;
      }

      // Step 3: Sign in to Firebase with custom token
      await FirebaseAuth.instance.signInWithCustomToken(tokenData['token']).then((value) async {
        if (value.additionalUserInfo!.isNewUser) {
          log("----->new user");
          DriverUserModel userModel = DriverUserModel();
          userModel.id = value.user!.uid;
          userModel.countryCode = controller.countryCode.value;
          userModel.countryISOCode = controller.countryISOCode.value;
          userModel.phoneNumber = controller.phoneNumber.value;
          userModel.loginType = Constant.phoneLoginType;

          ShowToastDialog.closeLoader();
          Get.off(const InformationScreen(), arguments: {
            "userModel": userModel,
          });
        } else {
          log("----->old user");
          await FireStoreUtils.userExitCustomerOrDriverRole(value.user!.uid).then((userExit) async {
            ShowToastDialog.closeLoader();
            if (userExit == '') {
              DriverUserModel userModel = DriverUserModel();
              userModel.id = value.user!.uid;
              userModel.countryCode = controller.countryCode.value;
              userModel.countryISOCode = controller.countryISOCode.value;
              userModel.phoneNumber = controller.phoneNumber.value;
              userModel.loginType = Constant.phoneLoginType;

              ShowToastDialog.closeLoader();
              Get.off(const InformationScreen(), arguments: {
                "userModel": userModel,
              });
            } else if (userExit == Constant.currentUserType) {
              await FireStoreUtils.getDriverProfile(value.user!.uid).then(
                (driverProfile) async {
                  if (driverProfile == null) {
                    DriverUserModel newUser = DriverUserModel();
                    newUser.id = value.user!.uid;
                    newUser.countryCode = controller.countryCode.value;
                    newUser.countryISOCode = controller.countryISOCode.value;
                    newUser.phoneNumber = controller.phoneNumber.value;
                    newUser.loginType = Constant.phoneLoginType;
                    ShowToastDialog.closeLoader();
                    Get.off(const InformationScreen(), arguments: {"userModel": newUser});
                  } else {
                    DriverUserModel userModel = driverProfile;
                    if (userModel.isActive == false) {
                      await FirebaseAuth.instance.signOut();
                      ShowToastDialog.showToast("This user is disable please contact administrator".tr);
                      return;
                    }
                    if (userModel.appLocked == true) {
                      await FirebaseAuth.instance.signOut();
                      ShowToastDialog.showToast('Your account has been locked by admin. Reason: ${userModel.lockReason ?? 'HP payment overdue'}');
                      return;
                    }
                    bool isPlanExpire = false;
                    if (userModel.subscriptionPlan?.id != null) {
                      if (userModel.subscriptionExpiryDate == null) {
                        if (userModel.subscriptionPlan?.expiryDay == '-1') {
                          isPlanExpire = false;
                        } else {
                          isPlanExpire = true;
                        }
                      } else {
                        DateTime expiryDate = userModel.subscriptionExpiryDate!.toDate();
                        isPlanExpire = expiryDate.isBefore(DateTime.now());
                      }
                    } else {
                      isPlanExpire = true;
                    }
                    if ((userModel.subscriptionPlanId == null || isPlanExpire == true) && userModel.ownerId == null) {
                      if (Constant.adminCommission?.isEnabled == false && Constant.isSubscriptionModelApplied == false) {
                        Get.offAll(const DashBoardScreen());
                      } else {
                        Get.offAll(const SubscriptionListScreen(), arguments: {"isShow": true});
                      }
                    } else {
                      if (userModel.ownerId != null && userModel.isEnabled == false) {
                        await FirebaseAuth.instance.signOut();
                        Get.back();
                        ShowToastDialog.showToast('This account has been disabled. Please reach out to the owner'.tr);
                      } else {
                        Get.offAll(const DashBoardScreen());
                      }
                    }
                  }
                },
              );
            } else {
              await FirebaseAuth.instance.signOut();
              ShowToastDialog.showToast('This account is already registered with a different role.'.tr);
            }
          });
        }
      });
    } catch (e) {
      debugPrint('OTP verify error: $e');
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast("Code is Invalid".tr);
    }
  }

  @override
  Widget build(BuildContext context) {
    final themeChange = Provider.of<DarkThemeProvider>(context);

    return GetX<OtpController>(
        init: OtpController(),
        builder: (controller) {
          return Scaffold(
            backgroundColor: Theme.of(context).colorScheme.background,
            body: SingleChildScrollView(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    width: Responsive.width(100, context),
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
                          child: Text("${"We just send a verification code to".tr} \n${controller.countryCode.value + controller.phoneNumber.value}".tr, style: GoogleFonts.poppins()),
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
