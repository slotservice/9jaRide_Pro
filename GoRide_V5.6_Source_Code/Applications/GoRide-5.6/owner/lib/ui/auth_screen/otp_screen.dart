import 'dart:developer';
import 'package:firebase_core/firebase_core.dart';
import 'package:owner/constant/constant.dart';
import 'package:owner/constant/show_toast_dialog.dart';
import 'package:owner/controller/otp_controller.dart';
import 'package:owner/model/driver_user_model.dart';
import 'package:owner/model/owner_user_model.dart';
import 'package:owner/themes/app_colors.dart';
import 'package:owner/themes/button_them.dart';
import 'package:owner/ui/auth_screen/information_screen.dart';
import 'package:owner/ui/dashboard_screen.dart';
import 'package:owner/ui/subscription_plan_screen/subscription_list_screen.dart';
import 'package:owner/utils/DarkThemeProvider.dart';
import 'package:owner/utils/fire_store_utils.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:pin_code_fields/pin_code_fields.dart';
import 'package:provider/provider.dart';
import '../../themes/responsive.dart';

class OtpScreen extends StatelessWidget {
  const OtpScreen({super.key});

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
                  Image.asset("assets/images/login_image.png", width: Responsive.width(100, context)),
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
                        const SizedBox(
                          height: 30,
                        ),
                        ButtonThem.buildButton(
                          context,
                          title: "Verify".tr,
                          onPress: () async {
                            if (controller.otpController.value.text.length == 6) {
                              ShowToastDialog.showLoader("Verify OTP".tr);
                              PhoneAuthCredential credential = PhoneAuthProvider.credential(verificationId: controller.verificationId.value, smsCode: controller.otpController.value.text);
                              await controller.firebaseAuth?.signInWithCredential(credential).then((value) async {
                                if (controller.argumentData['driverUserModel'] == null) {
                                  if (value.additionalUserInfo!.isNewUser) {
                                    log("----->new user");
                                    OwnerUserModel userModel = OwnerUserModel();
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
                                    await FireStoreUtils.userExitRole(value.user!.uid).then((userExit) async {
                                      ShowToastDialog.closeLoader();
                                      if (userExit == '') {
                                        OwnerUserModel userModel = OwnerUserModel();
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
                                        await FireStoreUtils.getOwnerProfile(value.user!.uid).then(
                                          (value) {
                                            if (value != null) {
                                              OwnerUserModel userModel = value;
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
                                              if (userModel.subscriptionPlanId == null || isPlanExpire == true) {
                                                if (Constant.adminCommission?.isEnabled == false && Constant.isSubscriptionModelApplied == false) {
                                                  Get.offAll(const DashBoardScreen());
                                                } else {
                                                  Get.offAll(const SubscriptionListScreen(), arguments: {"isShow": true});
                                                }
                                              } else {
                                                Get.offAll(const DashBoardScreen());
                                              }
                                            }
                                          },
                                        );
                                      } else {
                                        await FirebaseAuth.instance.signOut();
                                        ShowToastDialog.showToast('This mobile number is already registered with a different role.'.tr);
                                      }
                                    });
                                  }
                                } else {
                                  OwnerUserModel? ownerUser = await FireStoreUtils.getOwnerProfile(FireStoreUtils.getCurrentUid());
                                  if (ownerUser?.subscriptionTotalDrivers != "-1") {
                                    if (Constant.isSubscriptionModelApplied == false && Constant.adminCommission!.isEnabled == false) {
                                    } else {
                                      if ((ownerUser?.subscriptionExpiryDate != null && ownerUser?.subscriptionExpiryDate!.toDate().isBefore(DateTime.now()) == false) ||
                                          ownerUser?.subscriptionPlan?.expiryDay == '-1') {
                                        if (ownerUser?.subscriptionTotalDrivers != '0') {
                                          ownerUser?.subscriptionTotalDrivers = (int.parse(ownerUser.subscriptionTotalDrivers.toString()) - 1).toString();
                                        } else {
                                          ShowToastDialog.showToast("Your add driver limit has reached their maximum capacity. Please subscribe another subscription".tr);
                                          Get.back();
                                          return;
                                        }
                                      } else {
                                        ShowToastDialog.showToast("Your add driver has reached their maximum capacity. Please subscribe another subscription".tr);
                                        Get.back();
                                        return;
                                      }
                                    }
                                  }

                                  if (value.additionalUserInfo?.isNewUser == true) {
                                    DriverUserModel driverUserModel = controller.argumentData['driverUserModel'];
                                    driverUserModel.id = value.user?.uid;
                                    driverUserModel.ownerId = FireStoreUtils.getCurrentUid();
                                    controller.firebaseAuth?.signOut();
                                    try {
                                      await Firebase.app('SecondaryApp').delete();
                                    } catch (e) {
                                      debugPrint("No old SecondaryApp to delete");
                                    }
                                    log("----->new user");
                                    await FireStoreUtils.updateOwnerUser(ownerUser!);
                                    bool isUpdated = await FireStoreUtils.updateDriverUser(driverUserModel);
                                    ShowToastDialog.closeLoader();
                                    if (isUpdated == true) {
                                      // DashBoardController controller = Get.put(DashBoardController());
                                      // controller.selectedDrawerIndex.value = 0;
                                      Get.offAll(DashBoardScreen());
                                      ShowToastDialog.showToast('Driver registered successfully'.tr);
                                    }
                                  } else {
                                    await FireStoreUtils.userExitRole(value.user!.uid).then((userExit) async {
                                      ShowToastDialog.closeLoader();
                                      if (userExit == '') {
                                        DriverUserModel driverUserModel = controller.argumentData['driverUserModel'];
                                        driverUserModel.id = value.user!.uid;
                                        driverUserModel.ownerId = FireStoreUtils.getCurrentUid();
                                        controller.firebaseAuth?.signOut();
                                        try {
                                          await Firebase.app('SecondaryApp').delete();
                                        } catch (e) {
                                          debugPrint("No old SecondaryApp to delete");
                                        }
                                        await FireStoreUtils.updateOwnerUser(ownerUser!);
                                        bool isUpdated = await FireStoreUtils.updateDriverUser(driverUserModel);
                                        ShowToastDialog.closeLoader();
                                        if (isUpdated == true) {
                                          // DashBoardController controller = Get.put(DashBoardController());
                                          // controller.selectedDrawerIndex.value = 0;
                                          Get.offAll(DashBoardScreen());
                                          ShowToastDialog.showToast('Driver registered successfully'.tr);
                                        }
                                      } else {
                                        Get.back();
                                        ShowToastDialog.showToast('This mobile number is already registered with a different role.'.tr);
                                      }
                                    });
                                  }
                                }
                              }).catchError((error) {
                                ShowToastDialog.closeLoader();
                                ShowToastDialog.showToast("Code is Invalid".tr);
                              });
                            } else {
                              ShowToastDialog.showToast("Please Enter Valid OTP".tr);
                            }

                            // print(controller.countryCode.value);
                            // print(controller.phoneNumberController.value.text);
                          },
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
