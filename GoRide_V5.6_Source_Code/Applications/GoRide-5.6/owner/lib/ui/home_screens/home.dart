import 'package:owner/constant/constant.dart';
import 'package:owner/constant/show_toast_dialog.dart';
import 'package:owner/controller/home_dashboard_controller.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:owner/model/driver_user_model.dart';
import 'package:owner/themes/app_colors.dart';
import 'package:owner/themes/responsive.dart';
import 'package:owner/ui/driver_details_screen/All_driver_screen.dart';
import 'package:owner/ui/driver_details_screen/add_driver_screen.dart';
import 'package:owner/utils/DarkThemeProvider.dart';
import 'package:owner/utils/network_image_widget.dart';
import 'package:provider/provider.dart';

class Home extends StatelessWidget {
  const Home({super.key});

  @override
  Widget build(BuildContext context) {
    final themeChange = Provider.of<DarkThemeProvider>(context);

    return GetX<HomeController>(
        global: false,
        init: HomeController(),
        builder: (controller) {
          return Scaffold(
            backgroundColor: AppColors.lightprimary,
            floatingActionButton: controller.isLoading.value == true
                ? null
                : FloatingActionButton(
                    onPressed: () {
                      if (controller.ownerUser.value.subscriptionTotalDrivers == "-1") {
                        Get.to(AddDriverScreen());
                      } else {
                        if (Constant.isSubscriptionModelApplied == false && Constant.adminCommission!.isEnabled == false) {
                          Get.to(AddDriverScreen());
                        } else {
                          if ((controller.ownerUser.value.subscriptionExpiryDate != null && controller.ownerUser.value.subscriptionExpiryDate!.toDate().isBefore(DateTime.now()) == false) ||
                              controller.ownerUser.value.subscriptionPlan?.expiryDay == '-1') {
                            if (controller.ownerUser.value.subscriptionTotalDrivers != '0') {
                              Get.to(AddDriverScreen());
                            } else {
                              ShowToastDialog.showToast("Your add driver limit has reached their maximum capacity. Please subscribe another subscription".tr);
                            }
                          } else {
                            ShowToastDialog.showToast("Your add driver has reached their maximum capacity. Please subscribe another subscription".tr);
                          }
                        }
                      }
                    },
                    backgroundColor: themeChange.getThem() ? AppColors.darksecondprimary : AppColors.lightsecondprimary,
                    foregroundColor: AppColors.background,
                    elevation: 8,
                    child: Icon(Icons.add, size: 28),
                  ),
            body: controller.isLoading.value == true
                ? Container(
                    decoration: BoxDecoration(color: Theme.of(context).colorScheme.background, borderRadius: const BorderRadius.only(topLeft: Radius.circular(25), topRight: Radius.circular(25))),
                    child: Constant.loader(isDarkTheme: themeChange.getThem()))
                : controller.driverUserList.isEmpty
                    ? Container(
                        decoration: BoxDecoration(color: Theme.of(context).colorScheme.background, borderRadius: const BorderRadius.only(topLeft: Radius.circular(25), topRight: Radius.circular(25))),
                        child: Center(
                          child: Padding(
                            padding: const EdgeInsets.all(20.0),
                            child: Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                // Illustration / Placeholder Image
                                SizedBox(
                                  height: 150,
                                  child: Image.asset(
                                    'assets/images/ic_document_empty.png', // replace with your asset
                                    fit: BoxFit.contain,
                                  ),
                                ),
                                const SizedBox(height: 24),
                                // Title
                                Text(
                                  "No drivers or vehicles added yet".tr,
                                  textAlign: TextAlign.center,
                                  style: GoogleFonts.poppins(fontWeight: FontWeight.w700, fontSize: 20),
                                ),
                                const SizedBox(height: 8),
                                // Subtitle
                                Text(
                                  "Start by adding your first driver and vehicle to manage dashboard.".tr,
                                  textAlign: TextAlign.center,
                                  style: GoogleFonts.poppins(fontWeight: FontWeight.w500, fontSize: 14),
                                ),

                                const SizedBox(height: 32),
                              ],
                            ),
                          ),
                        ),
                      )
                    : Container(
                        height: Responsive.height(100, context),
                        width: Responsive.width(100, context),
                        decoration: BoxDecoration(color: Theme.of(context).colorScheme.background, borderRadius: const BorderRadius.only(topLeft: Radius.circular(25), topRight: Radius.circular(25))),
                        child: Padding(
                          padding: const EdgeInsets.symmetric(horizontal: 16),
                          child: Column(
                            children: [
                              SizedBox(height: 20),
                              Row(
                                children: [
                                  Expanded(
                                    child: Container(
                                      decoration: BoxDecoration(
                                        image: DecorationImage(image: AssetImage('assets/images/bg_rides.png')),
                                        color: themeChange.getThem() ? AppColors.darkContainerBackground : AppColors.containerBackground,
                                        borderRadius: const BorderRadius.all(Radius.circular(10)),
                                        border: Border.all(color: themeChange.getThem() ? AppColors.darkContainerBorder : AppColors.containerBorder, width: 0.5),
                                        boxShadow: themeChange.getThem()
                                            ? [
                                                BoxShadow(
                                                  color: Colors.black.withOpacity(0.30),
                                                  blurRadius: 5,
                                                  offset: const Offset(0, 4), // changes position of shadow
                                                ),
                                              ]
                                            : [
                                                BoxShadow(
                                                  color: Colors.grey.withOpacity(0.5),
                                                  blurRadius: 8,
                                                  offset: const Offset(0, 2), // changes position of shadow
                                                ),
                                              ],
                                      ),
                                      child: Padding(
                                        padding: const EdgeInsets.all(8.0),
                                        child: Column(
                                          crossAxisAlignment: CrossAxisAlignment.start,
                                          children: [
                                            Image.asset("assets/images/ic_ride.png"),
                                            SizedBox(
                                              height: 10,
                                            ),
                                            Text(
                                              '${controller.totalRide.value} ${'Rides'.tr}',
                                              style: GoogleFonts.poppins(fontWeight: FontWeight.w600, fontSize: 16),
                                            ),
                                            SizedBox(
                                              height: 5,
                                            ),
                                            Text(
                                              'Total Bookings'.tr,
                                              textAlign: TextAlign.center,
                                              style: GoogleFonts.poppins(fontWeight: FontWeight.w500, fontSize: 14),
                                            ),
                                          ],
                                        ),
                                      ),
                                    ),
                                  ),
                                  SizedBox(
                                    width: 10,
                                  ),
                                  Expanded(
                                    child: Container(
                                      decoration: BoxDecoration(
                                        image: DecorationImage(image: AssetImage('assets/images/bg_driver.png')),
                                        color: themeChange.getThem() ? AppColors.darkContainerBackground : AppColors.containerBackground,
                                        borderRadius: const BorderRadius.all(Radius.circular(10)),
                                        border: Border.all(color: themeChange.getThem() ? AppColors.darkContainerBorder : AppColors.containerBorder, width: 0.5),
                                        boxShadow: themeChange.getThem()
                                            ? [
                                                BoxShadow(
                                                  color: Colors.black.withOpacity(0.30),
                                                  blurRadius: 5,
                                                  offset: const Offset(0, 4), // changes position of shadow
                                                ),
                                              ]
                                            : [
                                                BoxShadow(
                                                  color: Colors.grey.withOpacity(0.5),
                                                  blurRadius: 8,
                                                  offset: const Offset(0, 2), // changes position of shadow
                                                ),
                                              ],
                                      ),
                                      child: Padding(
                                        padding: const EdgeInsets.all(8.0),
                                        child: Column(
                                          crossAxisAlignment: CrossAxisAlignment.start,
                                          children: [
                                            Image.asset("assets/images/ic_driver.png"),
                                            SizedBox(
                                              height: 10,
                                            ),
                                            Text(
                                              '${controller.driverUserList.length}',
                                              style: GoogleFonts.poppins(fontWeight: FontWeight.w600, fontSize: 16),
                                            ),
                                            SizedBox(
                                              height: 5,
                                            ),
                                            Text(
                                              'Total Drivers'.tr,
                                              textAlign: TextAlign.center,
                                              style: GoogleFonts.poppins(fontWeight: FontWeight.w500, fontSize: 14),
                                            ),
                                          ],
                                        ),
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                              SizedBox(
                                height: 10,
                              ),
                              Container(
                                width: double.infinity,
                                decoration: BoxDecoration(
                                  image: DecorationImage(image: AssetImage('assets/images/bg_wallet.png'), fit: BoxFit.fitWidth),
                                  color: themeChange.getThem() ? AppColors.darkContainerBackground : AppColors.containerBackground,
                                  borderRadius: const BorderRadius.all(Radius.circular(10)),
                                  border: Border.all(color: themeChange.getThem() ? AppColors.darkContainerBorder : AppColors.containerBorder, width: 0.5),
                                  boxShadow: themeChange.getThem()
                                      ? [
                                          BoxShadow(
                                            color: Colors.black.withOpacity(0.30),
                                            blurRadius: 5,
                                            offset: const Offset(0, 4), // changes position of shadow
                                          ),
                                        ]
                                      : [
                                          BoxShadow(
                                            color: Colors.grey.withOpacity(0.5),
                                            blurRadius: 8,
                                            offset: const Offset(0, 2), // changes position of shadow
                                          ),
                                        ],
                                ),
                                child: Padding(
                                  padding: const EdgeInsets.all(8.0),
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Image.asset("assets/images/ic_earning.png"),
                                      SizedBox(
                                        height: 10,
                                      ),
                                      Text(
                                        Constant.amountShow(amount: '${controller.totalWalletBalance.value}'),
                                        style: GoogleFonts.poppins(fontWeight: FontWeight.w600, fontSize: 16),
                                      ),
                                      SizedBox(
                                        height: 5,
                                      ),
                                      Text(
                                        'Earnings (This Week)'.tr,
                                        textAlign: TextAlign.center,
                                        style: GoogleFonts.poppins(fontWeight: FontWeight.w500, fontSize: 14),
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                              SizedBox(height: 20),
                              controller.driverUserList.isEmpty
                                  ? SizedBox()
                                  : Column(
                                      mainAxisAlignment: MainAxisAlignment.start,
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Row(
                                          children: [
                                            Expanded(
                                              child: Column(
                                                crossAxisAlignment: CrossAxisAlignment.start,
                                                children: [
                                                  Text(
                                                    'Your Available Drivers'.tr,
                                                    textAlign: TextAlign.center,
                                                    style: GoogleFonts.poppins(
                                                      fontSize: 16,
                                                    ),
                                                  ),
                                                  Text(
                                                    'Real-time status and earnings summary'.tr,
                                                    textAlign: TextAlign.center,
                                                    style: GoogleFonts.poppins(
                                                      fontSize: 12,
                                                    ),
                                                  ),
                                                ],
                                              ),
                                            ),
                                            InkWell(
                                              onTap: () async {
                                                await Get.to(() => AllDriverScreen())?.then((value) async {
                                                  controller.getDriverList();
                                                  controller.getOwnerProfile();
                                                });
                                              },
                                              child: Text('View all'.tr,
                                                  textAlign: TextAlign.center,
                                                  style: GoogleFonts.poppins(
                                                    fontWeight: FontWeight.w600,
                                                    color: themeChange.getThem() ? AppColors.darksecondprimary : AppColors.lightsecondprimary,
                                                  )),
                                            ),
                                          ],
                                        ),
                                        SizedBox(
                                          height: 10,
                                        ),
                                        Container(
                                          decoration: BoxDecoration(
                                            color: themeChange.getThem() ? AppColors.darkContainerBackground : AppColors.containerBackground,
                                            borderRadius: const BorderRadius.all(Radius.circular(10)),
                                            border: Border.all(color: themeChange.getThem() ? AppColors.darkContainerBorder : AppColors.containerBorder, width: 0.5),
                                            boxShadow: themeChange.getThem()
                                                ? [
                                                    BoxShadow(
                                                      color: Colors.black.withOpacity(0.30),
                                                      blurRadius: 5,
                                                      offset: const Offset(0, 4), // changes position of shadow
                                                    ),
                                                  ]
                                                : [
                                                    BoxShadow(
                                                      color: Colors.grey.withOpacity(0.5),
                                                      blurRadius: 8,
                                                      offset: const Offset(0, 2), // changes position of shadow
                                                    ),
                                                  ],
                                          ),
                                          child: ListView.builder(
                                            itemCount: controller.driverUserList.length > 5 ? 5 : controller.driverUserList.length,
                                            physics: NeverScrollableScrollPhysics(),
                                            shrinkWrap: true,
                                            itemBuilder: (context, index) {
                                              DriverUserModel driverModel = controller.driverUserList[index];
                                              return Padding(
                                                padding: const EdgeInsets.all(8.0),
                                                child: Row(
                                                  children: [
                                                    ClipRRect(
                                                      borderRadius: BorderRadius.circular(50),
                                                      child: NetworkImageWidget(
                                                        imageUrl: driverModel.profilePic.toString(),
                                                        height: 50,
                                                        width: 50,
                                                        fit: BoxFit.cover,
                                                      ),
                                                    ),
                                                    SizedBox(
                                                      width: 10,
                                                    ),
                                                    Expanded(
                                                      child: Column(
                                                        crossAxisAlignment: CrossAxisAlignment.start,
                                                        children: [
                                                          Text(
                                                            '${driverModel.fullName}',
                                                            maxLines: 1,
                                                            textAlign: TextAlign.center,
                                                            style: GoogleFonts.poppins(
                                                              fontSize: 16,
                                                            ),
                                                          ),
                                                          Text(
                                                            '${driverModel.countryCode} ${driverModel.phoneNumber}',
                                                            textAlign: TextAlign.center,
                                                            style: GoogleFonts.poppins(
                                                              fontSize: 12,
                                                            ),
                                                          ),
                                                        ],
                                                      ),
                                                    ),
                                                    SizedBox(
                                                      width: 10,
                                                    ),
                                                    Container(
                                                      width: MediaQuery.of(context).size.width * 0.24, // same as btnWidthRatio: 0.25
                                                      height: 42,
                                                      decoration: BoxDecoration(
                                                        color: driverModel.isOnline == true
                                                            ? (themeChange.getThem() ? AppColors.darksecondprimary.withAlpha(50) : AppColors.lightsecondprimary.withAlpha(50))
                                                            : (themeChange.getThem() ? AppColors.errorDark.withAlpha(50) : AppColors.errorLight),
                                                        borderRadius: BorderRadius.circular(50), // rounded corners
                                                      ),
                                                      alignment: Alignment.center,
                                                      child: Text(
                                                        driverModel.isOnline == true ? "Online".tr : "Offline",
                                                        style: GoogleFonts.poppins(
                                                          fontWeight: FontWeight.w500,
                                                          fontSize: 14,
                                                          color: driverModel.isOnline == true ? (themeChange.getThem() ? AppColors.darksecondprimary : AppColors.lightsecondprimary) : AppColors.error,
                                                        ),
                                                      ),
                                                    ),
                                                    PopupMenuButton<String>(
                                                      padding: EdgeInsets.zero,
                                                      onSelected: (value) {
                                                        if (value == 'Edit Driver') {
                                                          Get.to(AddDriverScreen(), arguments: {"driverUserModel": driverModel})!.then(
                                                            (value0) {
                                                              if (value0 == true) {
                                                                controller.getDriverList();
                                                              }
                                                            },
                                                          );
                                                        } else if (value == 'Delete Driver') {
                                                          controller.deleteDriver(driverModel: driverModel);
                                                        }
                                                      },
                                                      itemBuilder: (BuildContext context) => <PopupMenuEntry<String>>[
                                                        PopupMenuItem<String>(
                                                          value: 'Edit Driver'.tr,
                                                          child: Text('Edit Driver'.tr),
                                                        ),
                                                        PopupMenuItem<String>(
                                                          value: 'Delete Driver'.tr,
                                                          child: Text('Delete Driver'.tr),
                                                        ),
                                                      ],
                                                      color: themeChange.getThem() ? AppColors.darkBackground : AppColors.background,
                                                      icon: Icon(Icons.more_vert), // Three dots icon
                                                    ),
                                                  ],
                                                ),
                                              );
                                            },
                                          ),
                                        ),
                                      ],
                                    ),
                            ],
                          ),
                        ),
                      ),
          );
        });
  }
}
