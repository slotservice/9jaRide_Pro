import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:customer/model/admin_commission.dart';
import 'package:customer/model/contact_model.dart';
import 'package:customer/model/coupon_model.dart';
import 'package:customer/model/driver_user_model.dart';
import 'package:customer/model/order/location_lat_lng.dart';
import 'package:customer/model/order/positions.dart';
import 'package:customer/model/service_model.dart';
import 'package:customer/model/tax_model.dart';
import 'package:customer/model/zone_model.dart';

class OrderModel {
  String? sourceLocationName;
  String? destinationLocationName;
  String? paymentType;
  LocationLatLng? sourceLocationLAtLng;
  LocationLatLng? destinationLocationLAtLng;
  String? id;
  String? serviceId;
  String? userId;
  String? offerRate;
  String? finalRate;
  String? distance;
  String? distanceType;
  String? status;
  String? driverId;
  String? duration;
  String? otp;
  String? totalHoldingCharges;
  String? acNonAcCharges;
  String? rideHoldTimeMinutes;
  List<dynamic>? acceptedDriverId;
  List<dynamic>? rejectedDriverId;
  Positions? position;
  Timestamp? createdDate;
  Timestamp? updateDate;
  Timestamp? acceptHoldTime;

  /// Set once, by the driver app, the first time the driver gets within
  /// arrival range of the pickup. Deliberately a timestamp rather than a new
  /// status value, so every existing status query and filter keeps working.
  Timestamp? driverArrivedAt;

  /// Set when a driver starts the trip without the rider's code, using the
  /// 'rider could not provide code' option. Left null on a normal start.
  /// Set when the driver has confirmed the rider's code but has not yet
  /// tapped Start Ride. The trip only begins on that second action, so this
  /// is what tells the button which of the two states it is in. Kept as a
  /// timestamp rather than a new status string, because a new status would
  /// have to be added to every status query and filter in both apps.
  Timestamp? otpVerifiedAt;

  Timestamp? otpSkippedAt;

  /// How far the driver still is from the pickup, in km, published by the
  /// driver app while it is on the way so the rider gets more than just
  /// 'driver on the way'. Null until the first update lands.
  double? driverDistanceKm;
  int? driverEtaMinutes;
  bool? paymentStatus;
  bool? isAcSelected;
  // Rider needs special assistance on this trip. Set from their profile at
  // booking so only drivers who opted in are offered the ride.
  bool? specialAssistance;
  List<TaxModel>? taxList;
  ContactModel? someOneElse;
  CouponModel? coupon;
  ServiceModel? service;
  AdminCommission? adminCommission;
  ZoneModel? zone;
  String? zoneId;
  VehicleInformation? vehicleInformation;
  String? ownerId;
  List<String>? assistanceNeeds;

  OrderModel(
      {this.position,
      this.serviceId,
      this.paymentType,
      this.sourceLocationName,
      this.destinationLocationName,
      this.sourceLocationLAtLng,
      this.destinationLocationLAtLng,
      this.id,
      this.userId,
      this.distance,
      this.distanceType,
      this.status,
      this.driverId,
      this.duration,
      this.otp,
      this.totalHoldingCharges,
      this.acNonAcCharges,
      this.rideHoldTimeMinutes,
      this.offerRate,
      this.finalRate,
      this.paymentStatus,
      this.isAcSelected,
      this.createdDate,
      this.updateDate,
      this.acceptHoldTime,
      this.driverArrivedAt,
      this.otpVerifiedAt,
      this.otpSkippedAt,
      this.driverDistanceKm,
      this.driverEtaMinutes,
      this.taxList,
      this.coupon,
      this.someOneElse,
      this.service,
      this.adminCommission,
      this.zone,
      this.vehicleInformation,
      this.zoneId,
      this.ownerId,
      this.assistanceNeeds});

  OrderModel.fromJson(Map<String, dynamic> json) {
    serviceId = json['serviceId'];
    sourceLocationName = json['sourceLocationName'];
    paymentType = json['paymentType'];
    destinationLocationName = json['destinationLocationName'];
    sourceLocationLAtLng = json['sourceLocationLAtLng'] != null ? LocationLatLng.fromJson(json['sourceLocationLAtLng']) : null;
    destinationLocationLAtLng = json['destinationLocationLAtLng'] != null ? LocationLatLng.fromJson(json['destinationLocationLAtLng']) : null;
    coupon = json['coupon'] != null ? CouponModel.fromJson(json['coupon']) : null;
    someOneElse = json['someOneElse'] != null ? ContactModel.fromJson(json['someOneElse']) : null;
    vehicleInformation = json['vehicleInformation'] != null ? VehicleInformation.fromJson(json['vehicleInformation']) : null;
    id = json['id'];
    userId = json['userId'];
    offerRate = json['offerRate'];
    finalRate = json['finalRate'] ?? '0.0';
    distance = json['distance'];
    distanceType = json['distanceType'];
    status = json['status'];
    driverId = json['driverId'];
    duration = json['duration'];
    otp = json['otp'];
    totalHoldingCharges = json['totalHoldingCharges'] ?? "0.0";
    acNonAcCharges = json['acNonAcCharges'];
    rideHoldTimeMinutes = json['rideHoldTimeMinutes'];
    createdDate = json['createdDate'];
    updateDate = json['updateDate'];
    acceptHoldTime = json['acceptHoldTime'];
    driverArrivedAt = json['driverArrivedAt'];
    otpVerifiedAt = json['otpVerifiedAt'];
    otpSkippedAt = json['otpSkippedAt'];
    driverDistanceKm = (json['driverDistanceKm'] as num?)?.toDouble();
    driverEtaMinutes = (json['driverEtaMinutes'] as num?)?.toInt();
    acceptedDriverId = json['acceptedDriverId'];
    rejectedDriverId = json['rejectedDriverId'];
    paymentStatus = json['paymentStatus'];
    isAcSelected = json['isAcSelected'];
    specialAssistance = json['specialAssistance'] ?? false;
    position = json['position'] != null ? Positions.fromJson(json['position']) : null;
    service = json['service'] != null ? ServiceModel.fromJson(json['service']) : null;
    adminCommission = json['adminCommission'] != null ? AdminCommission.fromJson(json['adminCommission']) : null;
    zone = json['zone'] != null ? ZoneModel.fromJson(json['zone']) : null;
    zoneId = json['zoneId'];
    ownerId = json['ownerId'];
    assistanceNeeds = json['assistanceNeeds'] != null ? List<String>.from(json['assistanceNeeds']) : null;
    if (json['taxList'] != null) {
      taxList = <TaxModel>[];
      json['taxList'].forEach((v) {
        taxList!.add(TaxModel.fromJson(v));
      });
    }
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data['serviceId'] = serviceId;
    data['sourceLocationName'] = sourceLocationName;
    data['destinationLocationName'] = destinationLocationName;
    if (sourceLocationLAtLng != null) {
      data['sourceLocationLAtLng'] = sourceLocationLAtLng!.toJson();
    }
    if (coupon != null) {
      data['coupon'] = coupon!.toJson();
    }
    if (vehicleInformation != null) {
      data['vehicleInformation'] = vehicleInformation!.toJson();
    }
    if (someOneElse != null) {
      data['someOneElse'] = someOneElse!.toJson();
    }
    if (destinationLocationLAtLng != null) {
      data['destinationLocationLAtLng'] = destinationLocationLAtLng!.toJson();
    }
    if (service != null) {
      data['service'] = service!.toJson();
    }
    if (adminCommission != null) {
      data['adminCommission'] = adminCommission!.toJson();
    }
    if (zone != null) {
      data['zone'] = zone!.toJson();
    }
    data['zoneId'] = zoneId;
    data['id'] = id;
    data['userId'] = userId;
    data['paymentType'] = paymentType;
    data['offerRate'] = offerRate;
    data['finalRate'] = finalRate;
    data['distance'] = distance;
    data['distanceType'] = distanceType;
    data['status'] = status;
    data['driverId'] = driverId;
    data['duration'] = duration;
    data['otp'] = otp;
    data['totalHoldingCharges'] = totalHoldingCharges;
    data['acNonAcCharges'] = acNonAcCharges;
    data['rideHoldTimeMinutes'] = rideHoldTimeMinutes;
    data['createdDate'] = createdDate;
    data['updateDate'] = updateDate;
    data['acceptHoldTime'] = acceptHoldTime;
    data['driverArrivedAt'] = driverArrivedAt;
    data['otpVerifiedAt'] = otpVerifiedAt;
    data['otpSkippedAt'] = otpSkippedAt;
    data['driverDistanceKm'] = driverDistanceKm;
    data['driverEtaMinutes'] = driverEtaMinutes;
    data['acceptedDriverId'] = acceptedDriverId;
    data['rejectedDriverId'] = rejectedDriverId;
    data['paymentStatus'] = paymentStatus;
    data['isAcSelected'] = isAcSelected;
    data['specialAssistance'] = specialAssistance;
    data['ownerId'] = ownerId;
    data['assistanceNeeds'] = assistanceNeeds;
    if (taxList != null) {
      data['taxList'] = taxList!.map((v) => v.toJson()).toList();
    }
    if (position != null) {
      data['position'] = position!.toJson();
    }
    return data;
  }
}
