import 'package:cached_network_image/cached_network_image.dart';
import 'package:owner/constant/constant.dart';
import 'package:owner/controller/on_boarding_controller.dart';
import 'package:owner/themes/app_colors.dart';
import 'package:owner/themes/button_them.dart';
import 'package:owner/ui/auth_screen/login_screen.dart';
import 'package:owner/utils/DarkThemeProvider.dart';
import 'package:owner/utils/Preferences.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';

class OnBoardingScreen extends StatelessWidget {
  const OnBoardingScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final themeChange = Provider.of<DarkThemeProvider>(context);
    return GetX<OnBoardingController>(
      init: OnBoardingController(),
      builder: (controller) {
        return Scaffold(
            backgroundColor: AppColors.lightprimary,
            appBar: PreferredSize(
              preferredSize: Size.fromHeight(100.0),
              child: AppBar(
                backgroundColor: AppColors.lightprimary,
                flexibleSpace: Center(
                  child: Padding(
                    padding: const EdgeInsets.only(top: 20), // adjust spacing
                    child: Image.asset(
                      "assets/app_logo.png",
                      width: 110,
                      height: 76.48, // increase logo height
                      fit: BoxFit.cover,
                    ),
                  ),
                ),
              ),
            ),
            body: Container(
              decoration: BoxDecoration(color: Theme.of(context).colorScheme.background, borderRadius: const BorderRadius.only(topLeft: Radius.circular(25), topRight: Radius.circular(25))),
              child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 10),
                child: controller.isLoading.value
                    ? Constant.loader(isDarkTheme: themeChange.getThem())
                    : Column(children: [
                        Expanded(
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            crossAxisAlignment: CrossAxisAlignment.center,
                            children: [
                              Expanded(
                                flex: 5,
                                child: PageView.builder(
                                    controller: controller.pageController,
                                    onPageChanged: controller.selectedPageIndex,
                                    itemCount: controller.onBoardingList.length,
                                    itemBuilder: (context, index) {
                                      return Column(
                                        children: [
                                          Expanded(
                                            flex: 2,
                                            child: CachedNetworkImage(
                                              imageUrl: controller.onBoardingList[index].image.toString(),
                                              fit: BoxFit.cover,
                                              placeholder: (context, url) => Constant.loader(isDarkTheme: themeChange.getThem()),
                                              errorWidget: (context, url, error) => Image.network(Constant.userPlaceHolder),
                                            ),
                                          ),
                                          Padding(
                                            padding: const EdgeInsets.symmetric(vertical: 30),
                                            child: Row(
                                              mainAxisAlignment: MainAxisAlignment.center,
                                              children: List.generate(
                                                controller.onBoardingList.length,
                                                (index) => Container(
                                                    margin: const EdgeInsets.symmetric(horizontal: 4),
                                                    width: controller.selectedPageIndex.value == index ? 30 : 10,
                                                    height: 10,
                                                    decoration: BoxDecoration(
                                                      color: controller.selectedPageIndex.value == index ? AppColors.lightprimary : const Color(0xffD4D5E0),
                                                      borderRadius: const BorderRadius.all(Radius.circular(20.0)),
                                                    )),
                                              ),
                                            ),
                                          ),
                                          const SizedBox(
                                            height: 10,
                                          ),
                                          Column(
                                            children: [
                                              Text(
                                                Constant.localizationTitle(controller.onBoardingList[index].title),
                                                style: GoogleFonts.poppins(fontSize: 18, fontWeight: FontWeight.w600, letterSpacing: 1.5),
                                              ),
                                              const SizedBox(
                                                height: 10,
                                              ),
                                              Padding(
                                                padding: const EdgeInsets.symmetric(horizontal: 20.0),
                                                child: Text(
                                                  Constant.localizationDescription(controller.onBoardingList[index].description),
                                                  textAlign: TextAlign.center,
                                                  style: GoogleFonts.poppins(fontWeight: FontWeight.w400, letterSpacing: 1.5),
                                                ),
                                              ),
                                            ],
                                          )
                                        ],
                                      );
                                    }),
                              ),
                              SizedBox(height: 40),
                              Expanded(
                                  flex: 1,
                                  child: Column(
                                    children: [
                                      SizedBox(
                                        height: 30,
                                      ),
                                      Row(children: [
                                        controller.selectedPageIndex.value != 2
                                            ? Expanded(
                                                child: ButtonThem.buildButton(
                                                  context,
                                                  title: 'skip'.tr,
                                                  textColor: themeChange.getThem() ? AppColors.grey200 : AppColors.lightprimary,
                                                  bgColors: themeChange.getThem() ? AppColors.lightprimary : AppColors.grey200,
                                                  btnRadius: 30,
                                                  onPress: () {
                                                    controller.pageController.jumpToPage(2);
                                                  },
                                                ),
                                              )
                                            : SizedBox(),
                                        if (controller.selectedPageIndex.value != 2) SizedBox(width: 10),
                                        Expanded(
                                          child: ButtonThem.buildButton(
                                            context,
                                            title: controller.selectedPageIndex.value == 2 ? 'Get started'.tr : 'Next'.tr,
                                            btnRadius: 30,
                                            textColor: themeChange.getThem() ? AppColors.grey200 : AppColors.lightprimary,
                                            onPress: () {
                                              if (controller.selectedPageIndex.value == 2) {
                                                Preferences.setBoolean(Preferences.isFinishOnBoardingKey, true);
                                                Get.offAll(const LoginScreen());
                                              } else {
                                                controller.pageController.jumpToPage(controller.selectedPageIndex.value + 1);
                                              }
                                            },
                                          ),
                                        ),
                                      ]),
                                      const SizedBox(
                                        height: 20,
                                      ),
                                    ],
                                  ))
                            ],
                          ),
                        ),
                      ]),
              ),
            ));
      },
    );
  }
}
