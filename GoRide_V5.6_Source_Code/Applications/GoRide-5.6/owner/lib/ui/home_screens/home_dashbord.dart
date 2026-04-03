import 'package:flutter_svg/svg.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:owner/constant/constant.dart';
import 'package:owner/controller/home_controller.dart';
import 'package:owner/themes/app_colors.dart';
import 'package:owner/themes/button_them.dart';
import 'package:owner/ui/dashboard_screen.dart';
import 'package:owner/utils/DarkThemeProvider.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:provider/provider.dart';

class HomeDashboardScreen extends StatelessWidget {
  const HomeDashboardScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final themeChange = Provider.of<DarkThemeProvider>(context);
    return GetX<HomeDashboardController>(
        init: HomeDashboardController(),
        builder: (controller) {
          return Scaffold(
            backgroundColor: AppColors.lightprimary,
            drawer: buildAppDrawer(context, controller.dashboardController),
            appBar: controller.selectedIndex.value == 0
                ? AppBar(
                    backgroundColor: AppColors.lightprimary,
                    title: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          '${'Welcome,'.tr} ${controller.dashboardController.ownerUser.value.fullName ?? ''}',
                          style: GoogleFonts.poppins(fontWeight: FontWeight.w500, color: AppColors.background, fontSize: 16),
                        ),
                        Text(
                          'Manage your fleet, bookings, and drivers in one place'.tr,
                          style: GoogleFonts.poppins(fontWeight: FontWeight.w500, color: AppColors.background, fontSize: 12),
                        )
                      ],
                    ),
                    centerTitle: false,
                    titleSpacing: 0,
                    toolbarHeight: 80,
                  )
                : AppBar(
                    backgroundColor: AppColors.lightprimary,
                    title: Text(
                      controller.dashboardController.drawerItems[controller.dashboardController.selectedDrawerIndex.value].title.tr,
                      style: GoogleFonts.poppins(
                        color: Colors.white,
                      ),
                    ),
                    centerTitle: true,
                    leading: Builder(builder: (context) {
                      return InkWell(
                        onTap: () {
                          Scaffold.of(context).openDrawer();
                        },
                        child: Padding(
                          padding: const EdgeInsets.only(left: 10, right: 20, top: 20, bottom: 20),
                          child: SvgPicture.asset('assets/icons/ic_humber.svg'),
                        ),
                      );
                    }),
                  ),
            body: controller.isLoading.value == true
                ? Container(child: Constant.loader(isDarkTheme: themeChange.getThem()))
                : controller.ownerUser.value.documentVerification == false && Constant.isVerifyOwnerDocument == true && controller.selectedIndex.value <= 1
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
                                  "Document verification pending".tr,
                                  textAlign: TextAlign.center,
                                  style: GoogleFonts.poppins(fontWeight: FontWeight.w700, fontSize: 20),
                                ),
                                const SizedBox(height: 8),

                                // Subtitle
                                Text(
                                  "Your documents are under review. We’ll notify you once the verification is complete.".tr,
                                  textAlign: TextAlign.center,
                                  style: GoogleFonts.poppins(fontWeight: FontWeight.w500, fontSize: 14),
                                ),

                                const SizedBox(height: 32),
                                ButtonThem.buildButton(
                                  context,
                                  title: "Upload Documents".tr,
                                  onPress: () {
                                    controller.dashboardController.selectedDrawerIndex.value = 2;
                                  },
                                ),
                              ],
                            ),
                          ),
                        ),
                      )
                    : controller.widgetOptions.elementAt(controller.selectedIndex.value),
            bottomNavigationBar: BottomNavigationBar(
                items: <BottomNavigationBarItem>[
                  BottomNavigationBarItem(
                    icon: Padding(
                      padding: const EdgeInsets.all(6.0),
                      child: SvgPicture.asset("assets/icons/ic_home.svg",
                          width: 18,
                          color: controller.selectedIndex.value == 0
                              ? themeChange.getThem()
                                  ? AppColors.darksecondprimary
                                  : AppColors.lightsecondprimary
                              : Colors.white),
                    ),
                    label: 'Home'.tr,
                  ),
                  BottomNavigationBarItem(
                    icon: Padding(
                      padding: const EdgeInsets.all(6.0),
                      child: SvgPicture.asset("assets/icons/ic_request.svg",
                          width: 18,
                          color: controller.selectedIndex.value == 1
                              ? themeChange.getThem()
                                  ? AppColors.darksecondprimary
                                  : AppColors.lightsecondprimary
                              : Colors.white),
                    ),
                    label: 'Request'.tr,
                  ),
                  BottomNavigationBarItem(
                    icon: Padding(
                      padding: const EdgeInsets.all(6.0),
                      child: SvgPicture.asset("assets/icons/ic_wallet.svg",
                          width: 18,
                          color: controller.selectedIndex.value == 2
                              ? themeChange.getThem()
                                  ? AppColors.darksecondprimary
                                  : AppColors.lightsecondprimary
                              : Colors.white),
                    ),
                    label: 'Wallet'.tr,
                  ),
                  BottomNavigationBarItem(
                    icon: Padding(
                      padding: const EdgeInsets.all(6.0),
                      child: SvgPicture.asset("assets/icons/ic_user.svg",
                          width: 18,
                          color: controller.selectedIndex.value == 3
                              ? themeChange.getThem()
                                  ? AppColors.darksecondprimary
                                  : AppColors.lightsecondprimary
                              : Colors.white),
                    ),
                    label: 'Profile'.tr,
                  ),
                ],
                backgroundColor: AppColors.lightprimary,
                type: BottomNavigationBarType.fixed,
                currentIndex: controller.selectedIndex.value,
                selectedItemColor: themeChange.getThem() ? AppColors.darksecondprimary : AppColors.lightsecondprimary,
                unselectedItemColor: Colors.white,
                selectedFontSize: 12,
                unselectedFontSize: 12,
                onTap: controller.onItemTapped),
          );
        });
  }
}
