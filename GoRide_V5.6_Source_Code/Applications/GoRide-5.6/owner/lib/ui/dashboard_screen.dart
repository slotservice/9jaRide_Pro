import 'package:cached_network_image/cached_network_image.dart';
import 'package:owner/constant/constant.dart';
import 'package:owner/controller/dash_board_controller.dart';
import 'package:owner/model/owner_user_model.dart';
import 'package:owner/themes/app_colors.dart';
import 'package:owner/themes/responsive.dart';
import 'package:owner/utils/DarkThemeProvider.dart';
import 'package:owner/utils/fire_store_utils.dart';
import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';

class DashBoardScreen extends StatelessWidget {
  const DashBoardScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return GetX<DashBoardController>(
        init: DashBoardController(),
        builder: (controller) {
          return Scaffold(
            backgroundColor: AppColors.lightprimary,
            drawer: controller.selectedDrawerIndex.value == 0 ? null : buildAppDrawer(context, controller),
            appBar: controller.selectedDrawerIndex.value == 0
                ? null
                : AppBar(
                    backgroundColor: AppColors.lightprimary,
                    title: Text(
                      controller.drawerItems[controller.selectedDrawerIndex.value].title.tr,
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
            body: WillPopScope(onWillPop: controller.onWillPop, child: controller.getDrawerItemWidget(controller.selectedDrawerIndex.value)),
          );
        });
  }
}

Drawer buildAppDrawer(BuildContext context, DashBoardController controller) {
  final themeChange = Provider.of<DarkThemeProvider>(context);

  return Drawer(
    child: ListView(
      padding: EdgeInsets.zero,
      children: [
        DrawerHeader(
          child: FutureBuilder<OwnerUserModel?>(
            future: FireStoreUtils.getOwnerProfile(FireStoreUtils.getCurrentUid()),
            builder: (context, snapshot) {
              if (snapshot.connectionState == ConnectionState.waiting) {
                return Constant.loader(isDarkTheme: themeChange.getThem());
              }

              if (snapshot.hasError) {
                return Text(snapshot.error.toString());
              }

              if (!snapshot.hasData || snapshot.data == null) {
                return Text("No user found".tr);
              }

              final ownerUserModel = snapshot.data!;
              return Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  ClipRRect(
                    borderRadius: BorderRadius.circular(60),
                    child: CachedNetworkImage(
                      height: Responsive.width(18, context),
                      width: Responsive.width(18, context),
                      imageUrl: ownerUserModel.profilePic ?? Constant.userPlaceHolder,
                      fit: BoxFit.cover,
                      placeholder: (context, url) => Constant.loader(isDarkTheme: themeChange.getThem()),
                      errorWidget: (context, url, error) => Image.network(Constant.userPlaceHolder),
                    ),
                  ),
                  const SizedBox(height: 6),
                  Text(
                    ownerUserModel.fullName ?? "",
                    style: GoogleFonts.poppins(fontWeight: FontWeight.w500),
                  ),
                  const SizedBox(height: 2),
                  Text(
                    ownerUserModel.email ?? "",
                    style: GoogleFonts.poppins(),
                  ),
                ],
              );
            },
          ),
        ),
        ...List.generate(controller.drawerItems.length, (i) {
          final d = controller.drawerItems[i];
          final isSelected = i == controller.selectedDrawerIndex.value;

          return InkWell(
            onTap: () => controller.onSelectItem(i),
            child: Padding(
              padding: const EdgeInsets.all(8.0),
              child: Container(
                decoration: BoxDecoration(
                  color: isSelected
                      ? themeChange.getThem()
                          ? AppColors.darksecondprimary
                          : AppColors.lightsecondprimary
                      : Colors.transparent,
                  borderRadius: BorderRadius.circular(10),
                ),
                padding: const EdgeInsets.all(12),
                child: Row(
                  children: [
                    SvgPicture.asset(
                      d.icon,
                      width: 20,
                      color: isSelected ? (themeChange.getThem() ? Colors.black : Colors.white) : (themeChange.getThem() ? Colors.white : AppColors.drawerIcon),
                    ),
                    const SizedBox(width: 20),
                    Text(
                      d.title.tr,
                      style: GoogleFonts.poppins(
                        color: isSelected ? (themeChange.getThem() ? Colors.black : Colors.white) : (themeChange.getThem() ? Colors.white : Colors.black),
                        fontWeight: FontWeight.w500,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          );
        }),
      ],
    ),
  );
}
