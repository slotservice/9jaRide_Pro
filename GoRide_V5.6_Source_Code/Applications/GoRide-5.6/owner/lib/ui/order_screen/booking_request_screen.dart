import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:flutter_svg/svg.dart';
import 'package:owner/constant/constant.dart';
import 'package:owner/controller/booking_order_controller.dart';
import 'package:owner/model/driver_user_model.dart';
import 'package:owner/model/intercity_order_model.dart';
import 'package:owner/model/order_model.dart';
import 'package:owner/model/user_model.dart';
import 'package:owner/themes/app_colors.dart';
import 'package:owner/themes/button_them.dart';
import 'package:owner/themes/responsive.dart';
import 'package:owner/ui/home_screens/live_tracking_screen.dart';
import 'package:owner/ui/home_screens/order_map_screen.dart';
import 'package:owner/ui/intercity_screen/pacel_details_screen.dart';
import 'package:owner/ui/order_intercity_screen/complete_intecity_order_screen.dart';
import 'package:owner/ui/order_screen/complete_order_screen.dart';
import 'package:owner/utils/DarkThemeProvider.dart';
import 'package:owner/utils/fire_store_utils.dart';
import 'package:owner/utils/utils.dart';
import 'package:owner/widget/location_view.dart';
import 'package:owner/widget/user_view.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';

class BookingRequestScreen extends StatelessWidget {
  const BookingRequestScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final themeChange = Provider.of<DarkThemeProvider>(context);

    return GetX<BookingRequestController>(
        init: BookingRequestController(),
        builder: (controller) {
          return DefaultTabController(
            length: 3, // Number of tabs
            child: Scaffold(
                backgroundColor: AppColors.lightprimary,
                // drawer: buildAppDrawer(context, controller.dashboardController),
                // appBar: AppBar(
                //   backgroundColor: AppColors.lightprimary,
                //   title: Text(
                //     controller.dashboardController.drawerItems[controller.dashboardController.selectedDrawerIndex.value].title.tr,
                //     style: GoogleFonts.poppins(
                //       color: Colors.white,
                //     ),
                //   ),
                //   centerTitle: true,
                //   leading: Builder(builder: (context) {
                //     return InkWell(
                //       onTap: () {
                //         Scaffold.of(context).openDrawer();
                //       },
                //       child: Padding(
                //         padding: const EdgeInsets.only(left: 10, right: 20, top: 20, bottom: 20),
                //         child: SvgPicture.asset('assets/icons/ic_humber.svg'),
                //       ),
                //     );
                //   }),
                //   actions: [
                // CompositedTransformTarget(
                //   link: controller.layerLink,
                //   child: InkWell(
                //     key: controller.overlayKey,
                //     onTap: () {
                //       showOverlay(context, controller, themeChange);
                //     },
                //     child: Padding(
                //       padding: const EdgeInsets.symmetric(horizontal: 16),
                //       child: SvgPicture.asset(
                //         "assets/icons/ic_filter.svg",
                //         colorFilter: ColorFilter.mode(AppColors.background, BlendMode.srcIn),
                //       ),
                //     ),
                //   ),
                // ),
                //   ],
                // ),
                body: Column(
                  children: [
                    Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 8),
                      child: Row(
                        children: [
                          Expanded(
                            child: DropdownButtonFormField<DriverUserModel>(
                              dropdownColor: AppColors.darkTextField,
                              icon: Icon(
                                Icons.keyboard_arrow_down_sharp,
                                color: AppColors.textField,
                              ),
                              decoration: InputDecoration(
                                filled: true,
                                fillColor: AppColors.darkTextField,
                                contentPadding: const EdgeInsets.only(left: 10, right: 10),
                                errorBorder: OutlineInputBorder(
                                  borderRadius: BorderRadius.all(Radius.circular(4)),
                                  borderSide: BorderSide(color: AppColors.darkTextField, width: 1),
                                ),
                                border: OutlineInputBorder(
                                  borderRadius: BorderRadius.all(Radius.circular(4)),
                                  borderSide: BorderSide(color: AppColors.darkTextField, width: 1),
                                ),
                                enabledBorder: OutlineInputBorder(
                                  borderRadius: BorderRadius.all(Radius.circular(4)),
                                  borderSide: BorderSide(color: AppColors.darkTextField, width: 1),
                                ),
                                focusedBorder: OutlineInputBorder(
                                  borderRadius: BorderRadius.all(Radius.circular(4)),
                                  borderSide: BorderSide(color: AppColors.darkTextField, width: 1),
                                ),
                                disabledBorder: OutlineInputBorder(
                                  borderRadius: BorderRadius.all(Radius.circular(4)),
                                  borderSide: BorderSide(color: AppColors.darkTextField, width: 1),
                                ),
                              ),
                              value: controller.selectedDriver.value.id == null ? controller.allDriversOption.value : controller.selectedDriver.value,
                              onChanged: (value) {
                                controller.selectedDriver.value = value!;
                              },
                              items: [
                                DropdownMenuItem<DriverUserModel>(
                                  value: controller.allDriversOption.value,
                                  child: Text(
                                    controller.allDriversOption.value.fullName!,
                                    style: GoogleFonts.poppins(color: AppColors.textField, fontSize: 14),
                                  ),
                                ),
                                ...controller.driverUserList.map((item) {
                                  return DropdownMenuItem<DriverUserModel>(
                                    value: item,
                                    child: Text(
                                      item.fullName ?? '',
                                      style: GoogleFonts.poppins(color: AppColors.textField, fontSize: 14),
                                    ),
                                  );
                                }),
                              ],
                            ),
                          ),
                          Padding(
                            padding: const EdgeInsets.symmetric(horizontal: 8),
                            child: CompositedTransformTarget(
                              link: controller.layerLink,
                              child: InkWell(
                                key: controller.overlayKey,
                                onTap: () {
                                  showOverlay(context, controller, themeChange);
                                },
                                child: Container(
                                  height: 50,
                                  decoration: BoxDecoration(color: AppColors.darkTextField, borderRadius: BorderRadius.circular(4)),
                                  child: Padding(
                                    padding: const EdgeInsets.only(left: 10, right: 10),
                                    child: SvgPicture.asset(
                                      "assets/icons/ic_filter.svg",
                                      colorFilter: ColorFilter.mode(AppColors.background, BlendMode.srcIn),
                                    ),
                                  ),
                                ),
                              ),
                            ),
                          )
                        ],
                      ),
                    ),
                    SizedBox(
                      height: Responsive.width(15, context),
                      width: Responsive.width(100, context),
                      child: Padding(
                        padding: const EdgeInsets.only(bottom: 1, left: 8, right: 8),
                        child: TabBar(
                          indicatorColor: themeChange.getThem() ? AppColors.darksecondprimary : AppColors.lightsecondprimary,
                          isScrollable: false,
                          indicatorSize: TabBarIndicatorSize.label,
                          labelPadding: const EdgeInsets.symmetric(horizontal: 20),
                          labelColor: themeChange.getThem() ? AppColors.darksecondprimary : AppColors.lightsecondprimary, // selected tab text color
                          unselectedLabelColor: AppColors.background, // unselected tab text color
                          labelStyle: GoogleFonts.poppins(fontWeight: FontWeight.w600),
                          unselectedLabelStyle: GoogleFonts.poppins(fontWeight: FontWeight.w400),
                          tabs: [
                            Tab(child: Text("Accepted".tr)),
                            Tab(child: Text("Active".tr)),
                            Tab(child: Text("Completed".tr)),
                          ],
                        ),
                      ),
                    ),
                    Expanded(
                      child: Container(
                          width: Responsive.width(100, context),
                          decoration:
                              BoxDecoration(color: Theme.of(context).colorScheme.background, borderRadius: const BorderRadius.only(topLeft: Radius.circular(25), topRight: Radius.circular(25))),
                          child: Padding(
                              padding: const EdgeInsets.only(left: 4, right: 4, top: 10),
                              child: controller.bookingType.value == 'City Rides'
                                  ? TabBarView(children: [
                                      acceptedCityBookingWidget(themeChange, controller),
                                      activeCityBookingWidget(themeChange, controller),
                                      completedCityBookingWidget(themeChange, controller),
                                    ])
                                  : controller.bookingType.value == 'OutStation Rides'
                                      ? TabBarView(children: [
                                          acceptedOutStationBookingWidget(themeChange, controller),
                                          activeOutStationBookingWidget(themeChange, controller),
                                          completedOutStationBookingWidget(themeChange, controller),
                                        ])
                                      : TabBarView(children: [
                                          acceptedFreightBookingWidget(themeChange, controller),
                                          activeFreightBookingWidget(themeChange, controller),
                                          completedFreightBookingWidget(themeChange, controller),
                                        ]))),
                    ),
                  ],
                )),
          );
        });
  }

  void showOverlay(BuildContext context, BookingRequestController controller, DarkThemeProvider themeChange) {
    final OverlayState overlayState = Overlay.of(context);
    final RenderBox renderBox = controller.overlayKey.currentContext!.findRenderObject() as RenderBox;
    final Offset offset = renderBox.localToGlobal(Offset.zero);
    final Size size = renderBox.size;
    late OverlayEntry entry;

    entry = OverlayEntry(
      builder: (_) => Stack(
        children: [
          Positioned.fill(
            child: GestureDetector(
              onTap: () => entry.remove(),
              child: Container(color: Colors.transparent),
            ),
          ),
          Positioned(
            top: offset.dy + size.height + 10,
            right: 16,
            child: Material(
              color: Colors.transparent,
              child: Container(
                width: 200,
                padding: EdgeInsets.symmetric(vertical: 12),
                decoration: BoxDecoration(
                  color: Theme.of(context).colorScheme.background,
                  borderRadius: BorderRadius.circular(12),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black12,
                      blurRadius: 10,
                      offset: Offset(0, 4),
                    ),
                  ],
                ),
                child: Obx(() {
                  return Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: controller.types.map((type) {
                      bool selected = controller.bookingType.value == type;
                      return GestureDetector(
                        onTap: () {
                          controller.selectType(type);
                          entry.remove();
                        },
                        child: Padding(
                          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                          child: Row(
                            children: [
                              Expanded(
                                child: Text(
                                  type,
                                  style: TextStyle(
                                    fontWeight: selected ? FontWeight.bold : FontWeight.normal,
                                    color: selected
                                        ? themeChange.getThem()
                                            ? AppColors.darksecondprimary
                                            : AppColors.lightsecondprimary
                                        : null,
                                  ),
                                ),
                              ),
                              Icon(
                                selected ? Icons.radio_button_checked : Icons.radio_button_off,
                                color: selected
                                    ? themeChange.getThem()
                                        ? AppColors.darksecondprimary
                                        : AppColors.lightsecondprimary
                                    : Colors.grey,
                              ),
                            ],
                          ),
                        ),
                      );
                    }).toList(),
                  );
                }),
              ),
            ),
          ),
        ],
      ),
    );

    overlayState.insert(entry);
  }

  Widget cityRideWidget({required OrderModel orderModel, required DarkThemeProvider themeChange, required BuildContext context, required BookingRequestController controller}) {
    return InkWell(
      onTap: () {
        if (orderModel.status == Constant.ridePlaced) {
          Get.to(const OrderMapScreen(), arguments: {"orderModel": orderModel.id.toString()});
        } else if (orderModel.status == Constant.rideComplete) {
          Get.to(const CompleteOrderScreen(), arguments: {
            "orderModel": orderModel,
          });
        } else {
          if (orderModel.status == Constant.rideActive || orderModel.status == Constant.rideInProgress) {
            if (Constant.mapType == "inappmap") {
              Get.to(const LiveTrackingScreen(), arguments: {
                "orderModel": orderModel,
                "type": "orderModel",
              });
            } else {
              if (orderModel.status == Constant.rideInProgress) {
                Utils.redirectMap(
                    latitude: orderModel.destinationLocationLAtLng!.latitude!, longLatitude: orderModel.destinationLocationLAtLng!.longitude!, name: orderModel.destinationLocationName.toString());
              } else {
                Utils.redirectMap(latitude: orderModel.sourceLocationLAtLng!.latitude!, longLatitude: orderModel.sourceLocationLAtLng!.longitude!, name: orderModel.destinationLocationName.toString());
              }
            }
          }
        }
      },
      child: Padding(
        padding: const EdgeInsets.all(10.0),
        child: Container(
          decoration: BoxDecoration(
            color: themeChange.getThem() ? AppColors.darkContainerBackground : AppColors.containerBackground,
            borderRadius: const BorderRadius.all(Radius.circular(10)),
            border: Border.all(color: themeChange.getThem() ? AppColors.darkContainerBorder : AppColors.containerBorder, width: 0.5),
            boxShadow: themeChange.getThem()
                ? null
                : [
                    BoxShadow(
                      color: Colors.grey.withOpacity(0.5),
                      blurRadius: 8,
                      offset: const Offset(0, 2), // changes position of shadow
                    ),
                  ],
          ),
          child: Padding(
            padding: const EdgeInsets.all(15.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                UserView(
                  userId: orderModel.userId,
                  amount: orderModel.finalRate,
                  distance: orderModel.distance,
                  distanceType: orderModel.distanceType,
                  isAcOrNonAc: orderModel.service?.prices?.first.isAcNonAc == false ? null : orderModel.isAcSelected,
                ),
                const SizedBox(
                  height: 10,
                ),
                LocationView(
                  sourceLocation: orderModel.sourceLocationName.toString(),
                  destinationLocation: orderModel.destinationLocationName.toString(),
                ),
                const SizedBox(
                  height: 10,
                ),
                orderModel.status == Constant.rideComplete
                    ? Container(
                        decoration: BoxDecoration(color: themeChange.getThem() ? AppColors.darkGray : AppColors.gray, borderRadius: const BorderRadius.all(Radius.circular(10))),
                        child: Padding(
                            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 10),
                            child: Row(
                              children: [
                                Expanded(child: Text(orderModel.status.toString(), style: GoogleFonts.poppins(fontWeight: FontWeight.w600))),
                                Text(Constant().formatTimestamp(orderModel.createdDate), style: GoogleFonts.poppins()),
                              ],
                            )),
                      )
                    : Container(
                        decoration: BoxDecoration(color: themeChange.getThem() ? AppColors.darkGray : AppColors.gray, borderRadius: const BorderRadius.all(Radius.circular(10))),
                        child: Padding(
                            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 10),
                            child: Row(
                              mainAxisAlignment: MainAxisAlignment.center,
                              crossAxisAlignment: CrossAxisAlignment.center,
                              children: [
                                const Icon(Icons.access_time_outlined),
                                const SizedBox(
                                  width: 10,
                                ),
                                Text(Constant().formatTimestamp(orderModel.createdDate), style: GoogleFonts.poppins()),
                              ],
                            )),
                      ),
                const SizedBox(
                  height: 10,
                ),
                Visibility(
                  visible: orderModel.status == Constant.rideComplete ? false : true,
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      // Expanded(
                      //   flex: 1,
                      //   child: InkWell(
                      //     onTap: () async {
                      //       UserModel? customer = await FireStoreUtils.getCustomer(orderModel.userId.toString());
                      //       DriverUserModel? driver = await FireStoreUtils.getDriverProfile(orderModel.driverId.toString());

                      //       Get.to(ChatScreens(
                      //         driverId: driver!.id,
                      //         customerId: customer!.id,
                      //         customerName: customer.fullName,
                      //         customerProfileImage: customer.profilePic,
                      //         driverName: driver.fullName,
                      //         driverProfileImage: driver.profilePic,
                      //         orderId: orderModel.id,
                      //         token: customer.fcmToken,
                      //       ));
                      //     },
                      //     child: Container(
                      //       height: 44,
                      //       decoration: BoxDecoration(color: themeChange.getThem() ? AppColors.darksecondprimary : AppColors.lightsecondprimary, borderRadius: BorderRadius.circular(5)),
                      //       child: Icon(Icons.chat, color: themeChange.getThem() ? Colors.black : Colors.white),
                      //     ),
                      //   ),
                      // ),
                      // SizedBox(
                      //   width: 10,
                      // ),
                      Expanded(
                        flex: 1,
                        child: InkWell(
                          onTap: () async {
                            UserModel? customer = await FireStoreUtils.getCustomer(orderModel.userId.toString());
                            Constant.makePhoneCall("${customer!.countryCode}${customer.phoneNumber}");
                          },
                          child: Container(
                            height: 44,
                            decoration: BoxDecoration(color: themeChange.getThem() ? AppColors.darksecondprimary : AppColors.lightsecondprimary, borderRadius: BorderRadius.circular(5)),
                            child: Icon(Icons.call, color: themeChange.getThem() ? Colors.black : Colors.white),
                          ),
                        ),
                      )
                    ],
                  ),
                ),
                if (orderModel.status == Constant.rideComplete)
                  Column(
                    children: [
                      const SizedBox(
                        height: 10,
                      ),
                      ButtonThem.buildButton(
                        context,
                        title: orderModel.paymentStatus == true ? "Payment completed".tr : "Payment Pending".tr,
                        btnHeight: 44,
                        onPress: () async {},
                      ),
                    ],
                  ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget acceptedCityBookingWidget(DarkThemeProvider themeChange, BookingRequestController controller) {
    return Column(children: [
      controller.isLoading.value
          ? Constant.loader(isDarkTheme: themeChange.getThem())
          : StreamBuilder<QuerySnapshot>(
              stream: FireStoreUtils.getNewCityOrdersByFilter(driverId: controller.selectedDriver.value.id ?? 'all', status: Constant.ridePlaced),
              builder: (BuildContext context, AsyncSnapshot<QuerySnapshot> snapshot) {
                if (snapshot.hasError) {
                  return Center(child: Text('Something went wrong'.tr));
                }

                if (snapshot.connectionState == ConnectionState.waiting) {
                  return Constant.loader(isDarkTheme: themeChange.getThem());
                }

                return snapshot.data!.docs.isEmpty
                    ? Expanded(
                        child: Center(child: Text("Ride not found".tr)),
                      )
                    : Expanded(
                        child: ListView.builder(
                            itemCount: snapshot.data!.docs.length,
                            scrollDirection: Axis.vertical,
                            shrinkWrap: true,
                            itemBuilder: (context, index) {
                              OrderModel orderModel = OrderModel.fromJson(snapshot.data!.docs[index].data() as Map<String, dynamic>);
                              return cityRideWidget(context: context, controller: controller, orderModel: orderModel, themeChange: themeChange);
                            }),
                      );
              },
            )
    ]);
  }

  Widget activeCityBookingWidget(DarkThemeProvider themeChange, BookingRequestController controller) {
    return Column(children: [
      controller.isLoading.value
          ? Constant.loader(isDarkTheme: themeChange.getThem())
          : StreamBuilder<QuerySnapshot>(
              stream: FireStoreUtils.getCityOrdersByFilter(driverId: controller.selectedDriver.value.id ?? 'all', status: [Constant.rideActive, Constant.rideInProgress]),
              builder: (BuildContext context, AsyncSnapshot<QuerySnapshot> snapshot) {
                if (snapshot.hasError) {
                  return Center(child: Text('Something went wrong'.tr));
                }

                if (snapshot.connectionState == ConnectionState.waiting) {
                  return Padding(
                    padding: const EdgeInsets.only(top: 20),
                    child: Constant.loader(isDarkTheme: themeChange.getThem()),
                  );
                }

                return snapshot.data!.docs.isEmpty
                    ? Expanded(
                        child: Center(
                          child: Text("No Ride found".tr),
                        ),
                      )
                    : Expanded(
                        child: ListView.builder(
                            itemCount: snapshot.data!.docs.length,
                            scrollDirection: Axis.vertical,
                            shrinkWrap: true,
                            itemBuilder: (context, index) {
                              OrderModel orderModel = OrderModel.fromJson(snapshot.data!.docs[index].data() as Map<String, dynamic>);
                              return cityRideWidget(context: context, controller: controller, orderModel: orderModel, themeChange: themeChange);
                            }),
                      );
              },
            ),
    ]);
  }

  Widget completedCityBookingWidget(DarkThemeProvider themeChange, BookingRequestController controller) {
    return Column(children: [
      controller.isLoading.value
          ? Constant.loader(isDarkTheme: themeChange.getThem())
          : StreamBuilder<QuerySnapshot>(
              stream: FireStoreUtils.getCityOrdersByFilter(driverId: controller.selectedDriver.value.id ?? 'all', status: [Constant.rideComplete]),
              builder: (BuildContext context, AsyncSnapshot<QuerySnapshot> snapshot) {
                if (snapshot.hasError) {
                  return Center(child: Text('Something went wrong'.tr));
                }

                if (snapshot.connectionState == ConnectionState.waiting) {
                  return Padding(
                    padding: const EdgeInsets.only(top: 20),
                    child: Constant.loader(isDarkTheme: themeChange.getThem()),
                  );
                }

                return snapshot.data!.docs.isEmpty
                    ? Expanded(
                        child: Center(
                          child: Text("No Ride found".tr),
                        ),
                      )
                    : Expanded(
                        child: ListView.builder(
                            itemCount: snapshot.data!.docs.length,
                            scrollDirection: Axis.vertical,
                            shrinkWrap: true,
                            itemBuilder: (context, index) {
                              OrderModel orderModel = OrderModel.fromJson(snapshot.data!.docs[index].data() as Map<String, dynamic>);
                              return cityRideWidget(context: context, controller: controller, orderModel: orderModel, themeChange: themeChange);
                            }),
                      );
              },
            ),
    ]);
  }

  Widget outStationRideWidget({required InterCityOrderModel orderModel, required DarkThemeProvider themeChange, required BuildContext context, required BookingRequestController controller}) {
    return InkWell(
      onTap: () {
        if (orderModel.status == Constant.rideComplete) {
          Get.to(const CompleteIntercityOrderScreen(), arguments: {
            "orderModel": orderModel,
          });
        } else {
          if (orderModel.status == Constant.rideActive || orderModel.status == Constant.rideInProgress) {
            if (Constant.mapType == "inappmap") {
              Get.to(const LiveTrackingScreen(), arguments: {
                "interCityOrderModel": orderModel,
                "type": "interCityOrderModel",
              });
            } else {
              if (orderModel.status == Constant.rideInProgress) {
                Utils.redirectMap(
                    latitude: orderModel.destinationLocationLAtLng!.latitude!, longLatitude: orderModel.destinationLocationLAtLng!.longitude!, name: orderModel.destinationLocationName.toString());
              } else {
                Utils.redirectMap(latitude: orderModel.sourceLocationLAtLng!.latitude!, longLatitude: orderModel.sourceLocationLAtLng!.longitude!, name: orderModel.destinationLocationName.toString());
              }
            }
          }
        }
      },
      child: Padding(
        padding: const EdgeInsets.all(10.0),
        child: Container(
          decoration: BoxDecoration(
            color: themeChange.getThem() ? AppColors.darkContainerBackground : AppColors.containerBackground,
            borderRadius: const BorderRadius.all(Radius.circular(10)),
            border: Border.all(color: themeChange.getThem() ? AppColors.darkContainerBorder : AppColors.containerBorder, width: 0.5),
            boxShadow: themeChange.getThem()
                ? null
                : [
                    BoxShadow(
                      color: Colors.grey.withOpacity(0.5),
                      blurRadius: 8,
                      offset: const Offset(0, 2), // changes position of shadow
                    ),
                  ],
          ),
          child: Padding(
            padding: const EdgeInsets.all(15.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                UserView(
                  userId: orderModel.userId,
                  amount: orderModel.finalRate,
                  distance: orderModel.distance,
                  distanceType: orderModel.distanceType,
                ),
                Column(
                  children: [
                    const SizedBox(
                      height: 10,
                    ),
                    Row(
                      children: [
                        Expanded(
                          child: Row(
                            children: [
                              Container(
                                decoration: BoxDecoration(color: Colors.grey.withOpacity(0.30), borderRadius: const BorderRadius.all(Radius.circular(5))),
                                child: Padding(
                                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                                  child: Text(orderModel.paymentType.toString()),
                                ),
                              ),
                              const SizedBox(
                                width: 10,
                              ),
                              Container(
                                decoration: BoxDecoration(color: AppColors.lightprimary.withOpacity(0.30), borderRadius: const BorderRadius.all(Radius.circular(5))),
                                child: Padding(
                                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                                  child: Text(Constant.localizationName(orderModel.intercityService!.name)),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
                const SizedBox(
                  height: 10,
                ),
                LocationView(
                  sourceLocation: orderModel.sourceLocationName.toString(),
                  destinationLocation: orderModel.destinationLocationName.toString(),
                ),
                const SizedBox(
                  height: 10,
                ),
                orderModel.status == Constant.rideComplete
                    ? Container(
                        decoration: BoxDecoration(color: themeChange.getThem() ? AppColors.darkGray : AppColors.gray, borderRadius: const BorderRadius.all(Radius.circular(10))),
                        child: Padding(
                            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 10),
                            child: Row(
                              children: [
                                Expanded(child: Text(orderModel.status.toString(), style: GoogleFonts.poppins(fontWeight: FontWeight.w600))),
                                Text(Constant().formatTimestamp(orderModel.createdDate), style: GoogleFonts.poppins()),
                              ],
                            )),
                      )
                    : Container(
                        decoration: BoxDecoration(color: themeChange.getThem() ? AppColors.darkGray : AppColors.gray, borderRadius: const BorderRadius.all(Radius.circular(10))),
                        child: Padding(
                            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 10),
                            child: Row(
                              mainAxisAlignment: MainAxisAlignment.center,
                              crossAxisAlignment: CrossAxisAlignment.center,
                              children: [
                                const Icon(Icons.access_time_outlined),
                                const SizedBox(
                                  width: 10,
                                ),
                                Text(Constant().formatTimestamp(orderModel.createdDate), style: GoogleFonts.poppins()),
                              ],
                            )),
                      ),
                const SizedBox(
                  height: 10,
                ),
                Visibility(
                  visible: orderModel.status == Constant.rideComplete || orderModel.status == Constant.ridePlaced ? false : true,
                  child: Row(
                    mainAxisSize: MainAxisSize.max,
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Expanded(
                        child: InkWell(
                          onTap: () async {
                            UserModel? customer = await FireStoreUtils.getCustomer(orderModel.userId.toString());
                            Constant.makePhoneCall("${customer!.countryCode}${customer.phoneNumber}");
                          },
                          child: Container(
                            height: 44,
                            decoration: BoxDecoration(color: themeChange.getThem() ? AppColors.darksecondprimary : AppColors.lightsecondprimary, borderRadius: BorderRadius.circular(5)),
                            child: Icon(Icons.call, color: themeChange.getThem() ? Colors.black : Colors.white),
                          ),
                        ),
                      )
                    ],
                  ),
                ),
                if (orderModel.status == Constant.rideComplete)
                  ButtonThem.buildButton(
                    context,
                    textColor: themeChange.getThem() ? AppColors.darkBackground : AppColors.background,
                    title: orderModel.paymentStatus == true ? "Payment completed".tr : "Payment Pending".tr,
                    btnHeight: 44,
                    onPress: () async {},
                  ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget acceptedOutStationBookingWidget(DarkThemeProvider themeChange, BookingRequestController controller) {
    return Column(children: [
      controller.isLoading.value
          ? Constant.loader(isDarkTheme: themeChange.getThem())
          : StreamBuilder<QuerySnapshot>(
              stream: FireStoreUtils.getNewIntercityOrdersByFilter(driverId: controller.selectedDriver.value.id ?? 'all', status: Constant.ridePlaced),
              builder: (BuildContext context, AsyncSnapshot<QuerySnapshot> snapshot) {
                if (snapshot.hasError) {
                  return Center(child: Text('Something went wrong'.tr));
                }

                if (snapshot.connectionState == ConnectionState.waiting) {
                  return Constant.loader(isDarkTheme: themeChange.getThem());
                }

                return snapshot.data!.docs.isEmpty
                    ? Expanded(
                        child: Center(
                          child: Text("No Ride found".tr),
                        ),
                      )
                    : Expanded(
                        child: ListView.builder(
                            itemCount: snapshot.data!.docs.length,
                            scrollDirection: Axis.vertical,
                            shrinkWrap: true,
                            itemBuilder: (context, index) {
                              InterCityOrderModel orderModel = InterCityOrderModel.fromJson(snapshot.data!.docs[index].data() as Map<String, dynamic>);
                              return outStationRideWidget(context: context, controller: controller, orderModel: orderModel, themeChange: themeChange);
                            }),
                      );
              },
            ),
    ]);
  }

  Widget activeOutStationBookingWidget(DarkThemeProvider themeChange, BookingRequestController controller) {
    return Column(children: [
      controller.isLoading.value
          ? Constant.loader(isDarkTheme: themeChange.getThem())
          : StreamBuilder<QuerySnapshot>(
              stream: FireStoreUtils.getIntercityOrdersByFilter(driverId: controller.selectedDriver.value.id ?? 'all', status: [Constant.rideActive, Constant.rideInProgress]),
              builder: (BuildContext context, AsyncSnapshot<QuerySnapshot> snapshot) {
                if (snapshot.hasError) {
                  return Center(child: Text('Something went wrong'.tr));
                }

                if (snapshot.connectionState == ConnectionState.waiting) {
                  return Constant.loader(isDarkTheme: themeChange.getThem());
                }

                return snapshot.data!.docs.isEmpty
                    ? Expanded(
                        child: Center(
                          child: Text("No Ride found".tr),
                        ),
                      )
                    : Expanded(
                        child: ListView.builder(
                            itemCount: snapshot.data!.docs.length,
                            scrollDirection: Axis.vertical,
                            shrinkWrap: true,
                            itemBuilder: (context, index) {
                              InterCityOrderModel orderModel = InterCityOrderModel.fromJson(snapshot.data!.docs[index].data() as Map<String, dynamic>);
                              return outStationRideWidget(context: context, controller: controller, orderModel: orderModel, themeChange: themeChange);
                            }),
                      );
              },
            ),
    ]);
  }

  Widget completedOutStationBookingWidget(DarkThemeProvider themeChange, BookingRequestController controller) {
    return Column(children: [
      controller.isLoading.value
          ? Constant.loader(isDarkTheme: themeChange.getThem())
          : StreamBuilder<QuerySnapshot>(
              stream: FireStoreUtils.getIntercityOrdersByFilter(driverId: controller.selectedDriver.value.id ?? 'all', status: [Constant.rideComplete]),
              builder: (BuildContext context, AsyncSnapshot<QuerySnapshot> snapshot) {
                if (snapshot.hasError) {
                  return Center(child: Text('Something went wrong'.tr));
                }

                if (snapshot.connectionState == ConnectionState.waiting) {
                  return Constant.loader(isDarkTheme: themeChange.getThem());
                }

                return snapshot.data!.docs.isEmpty
                    ? Expanded(
                        child: Center(
                          child: Text("No Ride found".tr),
                        ),
                      )
                    : Expanded(
                        child: ListView.builder(
                            itemCount: snapshot.data!.docs.length,
                            scrollDirection: Axis.vertical,
                            shrinkWrap: true,
                            itemBuilder: (context, index) {
                              InterCityOrderModel orderModel = InterCityOrderModel.fromJson(snapshot.data!.docs[index].data() as Map<String, dynamic>);
                              return outStationRideWidget(context: context, controller: controller, orderModel: orderModel, themeChange: themeChange);
                            }),
                      );
              },
            ),
    ]);
  }

  Widget freightRideWidget({required InterCityOrderModel orderModel, required DarkThemeProvider themeChange, required BuildContext context, required BookingRequestController controller}) {
    return InkWell(
      onTap: () {
        if (orderModel.status == Constant.rideComplete) {
          Get.to(const CompleteIntercityOrderScreen(), arguments: {
            "orderModel": orderModel,
          });
        } else {
          if (orderModel.status == Constant.rideActive || orderModel.status == Constant.rideInProgress) {
            if (Constant.mapType == "inappmap") {
              Get.to(const LiveTrackingScreen(), arguments: {
                "interCityOrderModel": orderModel,
                "type": "interCityOrderModel",
              });
            } else {
              Utils.redirectMap(
                  latitude: orderModel.destinationLocationLAtLng!.latitude!, longLatitude: orderModel.destinationLocationLAtLng!.longitude!, name: orderModel.destinationLocationName.toString());
            }
          }
        }
      },
      child: Padding(
        padding: const EdgeInsets.all(8.0),
        child: Container(
          decoration: BoxDecoration(
            color: themeChange.getThem() ? AppColors.darkContainerBackground : AppColors.containerBackground,
            borderRadius: const BorderRadius.all(Radius.circular(10)),
            border: Border.all(color: themeChange.getThem() ? AppColors.darkContainerBorder : AppColors.containerBorder, width: 0.5),
            boxShadow: themeChange.getThem()
                ? null
                : [
                    BoxShadow(
                      color: Colors.grey.withOpacity(0.5),
                      blurRadius: 8,
                      offset: const Offset(0, 2), // changes position of shadow
                    ),
                  ],
          ),
          child: Padding(
            padding: const EdgeInsets.symmetric(vertical: 10, horizontal: 10),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                UserView(
                  userId: orderModel.userId,
                  amount: orderModel.offerRate,
                  distance: orderModel.distance,
                  distanceType: orderModel.distanceType,
                ),
                const SizedBox(
                  height: 10,
                ),
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(Constant.amountShow(amount: orderModel.offerRate.toString()), style: GoogleFonts.poppins(fontWeight: FontWeight.bold, fontSize: 18)),
                    // Text(" For ${orderModel.numberOfPassenger} Person".tr, style: GoogleFonts.poppins(fontWeight: FontWeight.bold, fontSize: 18)),
                  ],
                ),
                const SizedBox(
                  height: 10,
                ),
                Row(
                  children: [
                    Expanded(
                      child: Row(
                        children: [
                          Container(
                            decoration: BoxDecoration(color: Colors.grey.withOpacity(0.30), borderRadius: const BorderRadius.all(Radius.circular(5))),
                            child: Padding(
                              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                              child: Text(orderModel.paymentType.toString()),
                            ),
                          ),
                          const SizedBox(
                            width: 10,
                          ),
                          Container(
                            decoration: BoxDecoration(color: AppColors.lightprimary.withOpacity(0.30), borderRadius: const BorderRadius.all(Radius.circular(5))),
                            child: Padding(
                              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                              child: Text(Constant.localizationName(orderModel.intercityService!.name)),
                            ),
                          ),
                        ],
                      ),
                    ),
                    InkWell(
                        onTap: () {
                          Get.to(const ParcelDetailsScreen(), arguments: {
                            "orderModel": orderModel,
                          });
                        },
                        child: Text(
                          "View details".tr,
                          style: GoogleFonts.poppins(),
                        ))
                  ],
                ),
                const SizedBox(
                  height: 10,
                ),
                if (orderModel.status == Constant.ridePlaced || orderModel.status == Constant.rideActive || orderModel.status == Constant.rideComplete)
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(children: [
                        const Icon(Icons.fire_truck),
                        const SizedBox(
                          width: 10,
                        ),
                        Text(
                          Constant.localizationName(orderModel.freightVehicle!.name),
                          style: GoogleFonts.poppins(fontSize: 16, fontWeight: FontWeight.w600),
                        )
                      ]),
                      orderModel.status == Constant.ridePlaced || orderModel.status == Constant.rideActive
                          ? Padding(
                              padding: const EdgeInsets.symmetric(vertical: 14),
                              child: Container(
                                decoration: BoxDecoration(color: themeChange.getThem() ? AppColors.darkGray : AppColors.gray, borderRadius: const BorderRadius.all(Radius.circular(10))),
                                child: Padding(
                                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 12),
                                    child: Row(
                                      mainAxisAlignment: MainAxisAlignment.center,
                                      crossAxisAlignment: CrossAxisAlignment.center,
                                      children: [
                                        Text(orderModel.whenDates.toString(), style: GoogleFonts.poppins(fontWeight: FontWeight.w600)),
                                        const SizedBox(
                                          width: 10,
                                        ),
                                        Text(orderModel.whenTime.toString(), style: GoogleFonts.poppins(fontWeight: FontWeight.w600)),
                                      ],
                                    )),
                              ),
                            )
                          : SizedBox(height: 10),
                    ],
                  ),
                LocationView(
                  sourceLocation: orderModel.sourceLocationName.toString(),
                  destinationLocation: orderModel.destinationLocationName.toString(),
                ),
                const SizedBox(
                  height: 10,
                ),
                if (orderModel.status == Constant.rideComplete)
                  Container(
                    decoration: BoxDecoration(color: themeChange.getThem() ? AppColors.darkGray : AppColors.gray, borderRadius: const BorderRadius.all(Radius.circular(10))),
                    child: Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 10),
                        child: Row(
                          children: [
                            Expanded(child: Text(orderModel.status.toString(), style: GoogleFonts.poppins(fontWeight: FontWeight.w600))),
                            Text(Constant().formatTimestamp(orderModel.createdDate), style: GoogleFonts.poppins()),
                          ],
                        )),
                  ),
                const SizedBox(
                  height: 10,
                ),
                Visibility(
                  visible: orderModel.status == Constant.rideComplete || orderModel.status == Constant.ridePlaced ? false : true,
                  child: Row(
                    children: [
                      // Expanded(
                      //   child: InkWell(
                      //     onTap: () async {
                      //       UserModel? customer = await FireStoreUtils.getCustomer(orderModel.userId.toString());
                      //       DriverUserModel? driver = await FireStoreUtils.getDriverProfile(orderModel.driverId.toString());

                      //       Get.to(ChatScreens(
                      //         driverId: driver!.id,
                      //         customerId: customer!.id,
                      //         customerName: customer.fullName,
                      //         customerProfileImage: customer.profilePic,
                      //         driverName: driver.fullName,
                      //         driverProfileImage: driver.profilePic,
                      //         orderId: orderModel.id,
                      //         token: customer.fcmToken,
                      //       ));
                      //     },
                      //     child: Container(
                      //       height: 44,
                      //       width: 44,
                      //       decoration: BoxDecoration(color: themeChange.getThem() ? AppColors.darksecondprimary : AppColors.lightsecondprimary, borderRadius: BorderRadius.circular(5)),
                      //       child: Icon(Icons.chat, color: themeChange.getThem() ? Colors.black : Colors.white),
                      //     ),
                      //   ),
                      // ),
                      // const SizedBox(
                      //   width: 10,
                      // ),
                      Expanded(
                        child: InkWell(
                          onTap: () async {
                            UserModel? customer = await FireStoreUtils.getCustomer(orderModel.userId.toString());
                            Constant.makePhoneCall("${customer!.countryCode}${customer.phoneNumber}");
                          },
                          child: Container(
                            height: 44,
                            width: 44,
                            decoration: BoxDecoration(color: themeChange.getThem() ? AppColors.darksecondprimary : AppColors.lightsecondprimary, borderRadius: BorderRadius.circular(5)),
                            child: Icon(Icons.call, color: themeChange.getThem() ? Colors.black : Colors.white),
                          ),
                        ),
                      )
                    ],
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget acceptedFreightBookingWidget(DarkThemeProvider themeChange, BookingRequestController controller) {
    return Column(children: [
      controller.isLoading.value
          ? Constant.loader(isDarkTheme: themeChange.getThem())
          : Expanded(
              child: Padding(
                padding: const EdgeInsets.only(top: 10),
                child: StreamBuilder<QuerySnapshot>(
                  stream: FireStoreUtils.getNewFreightOrdersByFilter(driverId: controller.selectedDriver.value.id ?? 'all', status: Constant.ridePlaced),
                  builder: (BuildContext context, AsyncSnapshot<QuerySnapshot> snapshot) {
                    if (snapshot.hasError) {
                      return Text('Something went wrong'.tr);
                    }
                    if (snapshot.connectionState == ConnectionState.waiting) {
                      return Constant.loader(isDarkTheme: themeChange.getThem());
                    }
                    return snapshot.data!.docs.isEmpty
                        ? Center(
                            child: Text("No Ride found".tr),
                          )
                        : ListView.builder(
                            itemCount: snapshot.data!.docs.length,
                            scrollDirection: Axis.vertical,
                            shrinkWrap: true,
                            itemBuilder: (context, index) {
                              InterCityOrderModel orderModel = InterCityOrderModel.fromJson(snapshot.data!.docs[index].data() as Map<String, dynamic>);
                              return freightRideWidget(context: context, controller: controller, orderModel: orderModel, themeChange: themeChange);
                            });
                  },
                ),
              ),
            )
    ]);
  }

  Widget activeFreightBookingWidget(DarkThemeProvider themeChange, BookingRequestController controller) {
    return Column(children: [
      controller.isLoading.value
          ? Constant.loader(isDarkTheme: themeChange.getThem())
          : Expanded(
              child: Padding(
                padding: const EdgeInsets.only(top: 10),
                child: StreamBuilder<QuerySnapshot>(
                  stream: FireStoreUtils.getFreightOrdersByFilter(driverId: controller.selectedDriver.value.id ?? 'all', status: [Constant.rideActive, Constant.rideInProgress]),
                  builder: (BuildContext context, AsyncSnapshot<QuerySnapshot> snapshot) {
                    if (snapshot.hasError) {
                      return Text('Something went wrong'.tr);
                    }
                    if (snapshot.connectionState == ConnectionState.waiting) {
                      return Constant.loader(isDarkTheme: themeChange.getThem());
                    }
                    return snapshot.data!.docs.isEmpty
                        ? Center(
                            child: Text("No Ride found".tr),
                          )
                        : ListView.builder(
                            itemCount: snapshot.data!.docs.length,
                            scrollDirection: Axis.vertical,
                            shrinkWrap: true,
                            itemBuilder: (context, index) {
                              InterCityOrderModel orderModel = InterCityOrderModel.fromJson(snapshot.data!.docs[index].data() as Map<String, dynamic>);
                              return freightRideWidget(context: context, controller: controller, orderModel: orderModel, themeChange: themeChange);
                            });
                  },
                ),
              ),
            )
    ]);
  }

  Widget completedFreightBookingWidget(DarkThemeProvider themeChange, BookingRequestController controller) {
    return Column(children: [
      controller.isLoading.value
          ? Constant.loader(isDarkTheme: themeChange.getThem())
          : Expanded(
              child: Padding(
                padding: const EdgeInsets.only(top: 10),
                child: StreamBuilder<QuerySnapshot>(
                  stream: FireStoreUtils.getFreightOrdersByFilter(driverId: controller.selectedDriver.value.id ?? 'all', status: [Constant.rideComplete]),
                  builder: (BuildContext context, AsyncSnapshot<QuerySnapshot> snapshot) {
                    if (snapshot.hasError) {
                      return Text('Something went wrong'.tr);
                    }
                    if (snapshot.connectionState == ConnectionState.waiting) {
                      return Constant.loader(isDarkTheme: themeChange.getThem());
                    }
                    return snapshot.data!.docs.isEmpty
                        ? Center(
                            child: Text("No Ride found".tr),
                          )
                        : ListView.builder(
                            itemCount: snapshot.data!.docs.length,
                            scrollDirection: Axis.vertical,
                            shrinkWrap: true,
                            itemBuilder: (context, index) {
                              InterCityOrderModel orderModel = InterCityOrderModel.fromJson(snapshot.data!.docs[index].data() as Map<String, dynamic>);
                              return freightRideWidget(context: context, controller: controller, orderModel: orderModel, themeChange: themeChange);
                            });
                  },
                ),
              ),
            )
    ]);
  }
}
