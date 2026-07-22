import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:driver/model/driver_rules_model.dart';
import 'package:driver/model/language_name.dart';
import 'package:driver/model/order/location_lat_lng.dart';
import 'package:driver/model/order/positions.dart';
import 'package:driver/model/subscription_plan_model.dart';

class DriverUserModel {
  bool? isEnabled;
  bool? isActive;
  String? phoneNumber;
  String? loginType;
  String? countryCode;
  String? countryISOCode;
  String? profilePic;
  bool? documentVerification;
  String? fullName;
  bool? isOnline;
  String? id;
  String? serviceId;
  List<LanguageName>? serviceName;
  String? fcmToken;
  String? email;
  VehicleInformation? vehicleInformation;
  String? reviewsCount;
  String? reviewsSum;
  String? walletAmount;
  LocationLatLng? location;
  double? rotation;
  Positions? position;
  Timestamp? createdAt;
  List<dynamic>? zoneIds;
  String? subscriptionTotalOrders;
  String? subscriptionPlanId;
  Timestamp? subscriptionExpiryDate;
  SubscriptionPlanModel? subscriptionPlan;
  String? ownerId;
  bool? appLocked;
  String? lockReason;
  bool? hpEnabled;
  double? hpTotalCost;
  double? hpAmountPaid;
  double? hpBalance;
  double? hpDailyDeduction;
  String? hpStatus;
  Timestamp? hpStartDate;
  Timestamp? hpLastPaymentDate;

  // Vehicle ownership the driver declares at registration: 'personal' or
  // 'hire_purchase'. The driver can correct this themselves, but it deliberately
  // does NOT drive the hire purchase payment plan — hpEnabled and the deduction
  // amounts stay admin-controlled, so a driver cannot switch themselves out of
  // their daily deductions by editing this.
  String? driverType;

  // Opt-in: this driver is able to carry riders who need special assistance.
  // Only drivers with this set receive those ride requests.
  bool? canAssist;

  DriverUserModel({
    this.isEnabled,
    this.isActive,
    this.phoneNumber,
    this.loginType,
    this.countryCode,
    this.countryISOCode,
    this.profilePic,
    this.documentVerification,
    this.fullName,
    this.isOnline,
    this.id,
    this.serviceId,
    this.serviceName,
    this.fcmToken,
    this.email,
    this.location,
    this.vehicleInformation,
    this.reviewsCount,
    this.reviewsSum,
    this.rotation,
    this.position,
    this.walletAmount,
    this.createdAt,
    this.zoneIds,
    this.subscriptionTotalOrders,
    this.subscriptionPlanId,
    this.subscriptionExpiryDate,
    this.subscriptionPlan,
    this.ownerId,
    this.appLocked,
    this.lockReason,
    this.hpEnabled,
    this.hpTotalCost,
    this.hpAmountPaid,
    this.hpBalance,
    this.hpDailyDeduction,
    this.hpStatus,
    this.hpStartDate,
    this.hpLastPaymentDate,
    this.driverType,
    this.canAssist,
  });

  DriverUserModel.fromJson(Map<String, dynamic> json) {
    isEnabled = json['isEnabled'];
    isActive = json['isActive'];
    phoneNumber = json['phoneNumber'];
    loginType = json['loginType'];
    countryCode = json['countryCode'];
    countryISOCode = json['countryISOCode'];
    profilePic = json['profilePic'] ?? '';
    documentVerification = json['documentVerification'];
    fullName = json['fullName'];
    isOnline = json['isOnline'];
    id = json['id'];
    serviceId = json['serviceId'];
    fcmToken = json['fcmToken'];
    email = json['email'];
    vehicleInformation = json['vehicleInformation'] != null ? VehicleInformation.fromJson(json['vehicleInformation']) : null;
    reviewsCount = json['reviewsCount'] ?? '0.0';
    reviewsSum = json['reviewsSum'] ?? '0.0';
    rotation = json['rotation'];
    walletAmount = json['walletAmount'] ?? "0.0";
    location = json['location'] != null ? LocationLatLng.fromJson(json['location']) : null;
    position = json['position'] != null ? Positions.fromJson(json['position']) : null;
    createdAt = json['createdAt'];
    zoneIds = json['zoneIds'];
    subscriptionTotalOrders = json['subscriptionTotalOrders'];
    subscriptionPlanId = json['subscriptionPlanId'];
    subscriptionExpiryDate = json['subscriptionExpiryDate'];
    subscriptionPlan = json['subscription_plan'] != null ? SubscriptionPlanModel.fromJson(json['subscription_plan']) : null;
    ownerId = json['ownerId'];
    appLocked = json['appLocked'] ?? false;
    lockReason = json['lockReason'];
    hpEnabled = json['hpEnabled'] ?? false;
    hpTotalCost = (json['hpTotalCost'] as num?)?.toDouble();
    hpAmountPaid = (json['hpAmountPaid'] as num?)?.toDouble();
    hpBalance = (json['hpBalance'] as num?)?.toDouble();
    hpDailyDeduction = (json['hpDailyDeduction'] as num?)?.toDouble();
    hpStatus = json['hpStatus'];
    hpStartDate = json['hpStartDate'];
    hpLastPaymentDate = json['hpLastPaymentDate'];
    driverType = json['driverType'] ?? 'personal';
    canAssist = json['canAssist'] ?? false;
    if (json['serviceName'] != null) {
      serviceName = <LanguageName>[];
      json['serviceName'].forEach((v) {
        serviceName!.add(LanguageName.fromJson(v));
      });
    }
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data['isEnabled'] = isEnabled;
    data['phoneNumber'] = phoneNumber;
    data['loginType'] = loginType;
    data['countryCode'] = countryCode;
    data['countryISOCode'] = countryISOCode;
    data['profilePic'] = profilePic;
    data['documentVerification'] = documentVerification;
    data['fullName'] = fullName;
    data['isOnline'] = isOnline;
    data['id'] = id;
    data['serviceId'] = serviceId;
    data['fcmToken'] = fcmToken;
    data['email'] = email;
    data['rotation'] = rotation;
    data['createdAt'] = createdAt;
    if (vehicleInformation != null) {
      data['vehicleInformation'] = vehicleInformation!.toJson();
    }
    if (location != null) {
      data['location'] = location!.toJson();
    }
    data['reviewsCount'] = reviewsCount;
    data['reviewsSum'] = reviewsSum;
    data['walletAmount'] = walletAmount;
    data['zoneIds'] = zoneIds;
    if (position != null) {
      data['position'] = position!.toJson();
    }
    data['subscriptionTotalOrders'] = subscriptionTotalOrders;
    data['subscriptionPlanId'] = subscriptionPlanId;
    data['subscriptionExpiryDate'] = subscriptionExpiryDate;
    data['subscription_plan'] = subscriptionPlan?.toJson();
    data['ownerId'] = ownerId;
    data['appLocked'] = appLocked;
    data['lockReason'] = lockReason;
    data['hpEnabled'] = hpEnabled;
    data['hpTotalCost'] = hpTotalCost;
    data['hpAmountPaid'] = hpAmountPaid;
    data['hpBalance'] = hpBalance;
    data['hpDailyDeduction'] = hpDailyDeduction;
    data['hpStatus'] = hpStatus;
    data['hpStartDate'] = hpStartDate;
    data['hpLastPaymentDate'] = hpLastPaymentDate;
    data['driverType'] = driverType;
    data['canAssist'] = canAssist;
    if (serviceName != null) {
      data['serviceName'] = serviceName!.map((v) => v.toJson()).toList();
    }
    return data;
  }
}

class VehicleInformation {
  Timestamp? registrationDate;
  String? vehicleColor;
  String? vehicleNumber;
  String? seats;
  List<DriverRulesModel>? driverRules;
  List<RateModel>? rates;

  VehicleInformation({
    this.registrationDate,
    this.vehicleColor,
    this.vehicleNumber,
    this.seats,
    this.driverRules,
    this.rates,
  });

  VehicleInformation.fromJson(Map<String, dynamic> json) {
    registrationDate = json['registrationDate'];
    vehicleColor = json['vehicleColor'];
    vehicleNumber = json['vehicleNumber'];
    seats = json['seats'];
    if (json['driverRules'] != null) {
      driverRules = <DriverRulesModel>[];
      json['driverRules'].forEach((v) {
        driverRules!.add(DriverRulesModel.fromJson(v));
      });
    }
    if (json['rates'] != null) {
      rates = <RateModel>[];
      json['rates'].forEach((v) {
        rates!.add(RateModel.fromJson(v));
      });
    }
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data['registrationDate'] = registrationDate;
    data['vehicleColor'] = vehicleColor;
    data['vehicleNumber'] = vehicleNumber;
    data['seats'] = seats;
    if (driverRules != null) {
      data['driverRules'] = driverRules!.map((v) => v.toJson()).toList();
    }
    if (rates != null) {
      data['rates'] = rates!.map((v) => v.toJson()).toList();
    }
    return data;
  }
}

class RateModel {
  String? acPerKmRate;
  String? nonAcPerKmRate;
  String? perKmRate;
  String? zoneId;

  RateModel({this.acPerKmRate, this.nonAcPerKmRate, this.perKmRate, this.zoneId});

  RateModel.fromJson(Map<String, dynamic> json) {
    acPerKmRate = json['acPerKmRate'];
    nonAcPerKmRate = json['nonAcPerKmRate'];
    perKmRate = json['perKmRate'];
    zoneId = json['zoneId'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data['acPerKmRate'] = acPerKmRate;
    data['nonAcPerKmRate'] = nonAcPerKmRate;
    data['perKmRate'] = perKmRate;
    data['zoneId'] = zoneId;
    return data;
  }
}
