import 'package:driver/controller/splash_controller.dart';
import 'package:driver/themes/app_colors.dart';
import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:get/get.dart';

class SplashScreen extends StatelessWidget {
  const SplashScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<SplashController>(
        init: SplashController(),
        builder: (controller) {
          return Scaffold(
            backgroundColor: AppColors.lightprimary,
            body: Center(
                child: SvgPicture.asset(
              "assets/app_logo.svg",
              width: MediaQuery.of(context).size.width * 0.65,
            )),
          );
        });
  }
}
