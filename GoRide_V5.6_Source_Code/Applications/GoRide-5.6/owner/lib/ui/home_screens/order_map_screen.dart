import 'dart:io';
import 'package:owner/constant/constant.dart';
import 'package:owner/controller/order_map_controller.dart';
import 'package:owner/themes/app_colors.dart';
import 'package:owner/themes/responsive.dart';
import 'package:owner/utils/DarkThemeProvider.dart';
import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart' as flutterMap;
import 'package:get/get.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:provider/provider.dart';
import 'package:latlong2/latlong.dart' as location;

class OrderMapScreen extends StatelessWidget {
  const OrderMapScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final themeChange = Provider.of<DarkThemeProvider>(context);

    return GetX<OrderMapController>(
        init: OrderMapController(),
        builder: (controller) {
          return Scaffold(
            appBar: AppBar(
              backgroundColor: AppColors.lightprimary,
              leading: InkWell(
                  onTap: () {
                    Get.back();
                  },
                  child: const Icon(
                    Icons.arrow_back,
                  )),
            ),
            body: controller.isLoading.value
                ? Constant.loader(isDarkTheme: themeChange.getThem())
                : Column(
                    children: [
                      Container(
                        height: Responsive.width(10, context),
                        width: Responsive.width(100, context),
                        color: AppColors.lightprimary,
                      ),
                      Expanded(
                        child: Container(
                          transform: Matrix4.translationValues(0.0, -20.0, 0.0),
                          decoration:
                              BoxDecoration(color: Theme.of(context).colorScheme.background, borderRadius: const BorderRadius.only(topLeft: Radius.circular(25), topRight: Radius.circular(25))),
                          child: ClipRRect(
                            borderRadius: const BorderRadius.only(topLeft: Radius.circular(30), topRight: Radius.circular(30)),
                            child: Stack(
                              children: [
                                Constant.selectedMapType == 'osm'
                                    ? flutterMap.FlutterMap(
                                        mapController: controller.osmMapController,
                                        options: flutterMap.MapOptions(
                                          initialCenter: location.LatLng(Constant.currentLocation?.latitude ?? 45.521563, Constant.currentLocation?.longitude ?? -122.677433),
                                          initialZoom: 10,
                                        ),
                                        children: [
                                          flutterMap.TileLayer(
                                            urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                                            userAgentPackageName: Platform.isAndroid ? 'com.goride.owner' : 'com.goride.owner',
                                          ),
                                          flutterMap.MarkerLayer(
                                            markers: [
                                              flutterMap.Marker(
                                                point: controller.source.value,
                                                width: 50,
                                                height: 50,
                                                child: Image.asset('assets/images/pickup.png'),
                                              ),
                                              flutterMap.Marker(
                                                point: controller.destination.value,
                                                width: 50,
                                                height: 50,
                                                child: Image.asset('assets/images/dropoff.png'),
                                              ),
                                            ],
                                          ),
                                          if (controller.routePoints.isNotEmpty)
                                            flutterMap.PolylineLayer(
                                              polylines: [
                                                flutterMap.Polyline(
                                                  points: controller.routePoints,
                                                  strokeWidth: 5.0,
                                                  color: themeChange.getThem() ? AppColors.darksecondprimary : AppColors.lightsecondprimary,
                                                ),
                                              ],
                                            ),
                                        ],
                                      )
                                    : GoogleMap(
                                        myLocationEnabled: true,
                                        myLocationButtonEnabled: true,
                                        mapType: MapType.terrain,
                                        zoomControlsEnabled: false,
                                        polylines: Set<Polyline>.of(controller.polyLines.values),
                                        padding: const EdgeInsets.only(
                                          top: 22.0,
                                        ),
                                        markers: Set<Marker>.of(controller.markers.values),
                                        onMapCreated: (GoogleMapController mapController) {
                                          controller.mapController.complete(mapController);
                                        },
                                        initialCameraPosition: CameraPosition(
                                          zoom: 15,
                                          target: LatLng(Constant.currentLocation?.latitude ?? 45.521563, Constant.currentLocation?.longitude ?? -122.677433),
                                        ),
                                      ),
                              ],
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
