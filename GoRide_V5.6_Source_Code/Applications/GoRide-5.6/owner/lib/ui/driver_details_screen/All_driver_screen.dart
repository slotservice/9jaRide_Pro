import 'package:flutter/cupertino.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:owner/constant/constant.dart';
import 'package:owner/controller/All_driver_controller.dart';
import 'package:owner/model/driver_user_model.dart';
import 'package:owner/themes/app_colors.dart';
import 'package:owner/themes/responsive.dart';
import 'package:owner/ui/driver_details_screen/add_driver_screen.dart';
import 'package:owner/utils/DarkThemeProvider.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:owner/utils/network_image_widget.dart';
import 'package:provider/provider.dart';

class AllDriverScreen extends StatelessWidget {
  const AllDriverScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final themeChange = Provider.of<DarkThemeProvider>(context);
    return GetX<AllDriverController>(
        init: AllDriverController(),
        builder: (controller) {
          return Scaffold(
            appBar: AppBar(
              backgroundColor: AppColors.lightprimary,
              title: Text("View All Drivers".tr),
              leading: InkWell(
                  onTap: () {
                    Get.back();
                  },
                  child: const Icon(
                    Icons.arrow_back,
                  )),
            ),
            backgroundColor: AppColors.lightprimary,
            body: Column(
              children: [
                // SizedBox(
                //   height: Responsive.width(12, context),
                //   width: Responsive.width(100, context),
                // ),
                Expanded(
                  child: Container(
                    height: Responsive.height(100, context),
                    width: Responsive.width(100, context),
                    decoration: BoxDecoration(color: Theme.of(context).colorScheme.background, borderRadius: const BorderRadius.only(topLeft: Radius.circular(25), topRight: Radius.circular(25))),
                    child: Padding(
                      padding: const EdgeInsets.only(left: 16, right: 16, top: 20, bottom: 10),
                      child: controller.isLoading.value == true
                          ? Constant.loader(isDarkTheme: themeChange.getThem())
                          : controller.driverUserList.isEmpty == true
                              ? Constant.showEmptyView(message: 'Driver Not Found'.tr)
                              : ListView.builder(
                                  itemCount: controller.driverUserList.length,
                                  shrinkWrap: true,
                                  itemBuilder: (context, index) {
                                    DriverUserModel driverModel = controller.driverUserList[index];
                                    return Padding(
                                      padding: const EdgeInsets.only(bottom: 12),
                                      child: Container(
                                        padding: const EdgeInsets.all(8.0),
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
                                        child: Row(
                                          children: [
                                            ClipRRect(
                                              borderRadius: BorderRadius.circular(50),
                                              child: NetworkImageWidget(
                                                imageUrl: driverModel.profilePic.toString(),
                                                height: 50,
                                                width: 50,
                                                fit: BoxFit.fill,
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
                                                driverModel.isOnline == true ? "Online".tr : "Offline".tr,
                                                style: GoogleFonts.poppins(
                                                  fontWeight: FontWeight.w500,
                                                  fontSize: 14,
                                                  color: driverModel.isOnline == true ? (themeChange.getThem() ? AppColors.darksecondprimary : AppColors.lightsecondprimary) : AppColors.error,
                                                ),
                                              ),
                                            ),
                                            PopupMenuButton<String>(
                                              padding: EdgeInsets.zero,
                                              onSelected: (value) async {
                                                if (value == 'Edit Driver') {
                                                  Get.to(AddDriverScreen(), arguments: {"driverUserModel": driverModel})!.then(
                                                    (value0) {
                                                      if (value0 == true) {
                                                        controller.getDriverList();
                                                      }
                                                    },
                                                  );
                                                } else if (value == 'Delete Driver') {
                                                  await controller.deleteDriver(driverModel: driverModel);
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
                                      ),
                                    );
                                  },
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
