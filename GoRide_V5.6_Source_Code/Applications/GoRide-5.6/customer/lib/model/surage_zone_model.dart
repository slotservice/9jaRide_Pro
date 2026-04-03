import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:customer/model/language_name.dart';

class SurgeZonesModel {
  List<GeoPoint>? area;
  bool? publish;
  double? latitude;
  List<LanguageName>? name;
  String? id;
  num? activeRequests;
  double? longitude;
  Timestamp? createdAt;
  num? onlineDrivers;
  num? surgeMultiplier;
  String? radiusKm;

  SurgeZonesModel(
      {this.area,
      this.publish,
      this.latitude,
      this.name,
      this.id,
      this.longitude,
      this.activeRequests,
      required this.createdAt,
      this.onlineDrivers,
      this.surgeMultiplier,
      this.radiusKm});

  SurgeZonesModel.fromJson(Map<String, dynamic> json) {
    if (json['area'] != null) {
      area = <GeoPoint>[];
      json['area'].forEach((v) {
        area!.add(v);
      });
    }

    if (json['name'] != null) {
      name = <LanguageName>[];
      json['name'].forEach((v) {
        name!.add(LanguageName.fromJson(v));
      });
    }

    publish = json['publish'];
    latitude = json['latitude'];
    id = json['id'];
    longitude = json['longitude'];
    activeRequests = json['activeRequests'];
    createdAt = json['createdAt'];
    onlineDrivers = json['onlineDrivers'];
    surgeMultiplier = json['surgeMultiplier'];
    radiusKm = json['radiusKm'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    if (area != null) {
      data['area'] = area!.map((v) => v).toList();
    }
    if (name != null) {
      data['name'] = name!.map((v) => v.toJson()).toList();
    }
    data['publish'] = publish;
    data['latitude'] = latitude;
    data['id'] = id;
    data['longitude'] = longitude;
    data['activeRequests'] = activeRequests;
    data['createdAt'] = createdAt;
    data['onlineDrivers'] = onlineDrivers;
    data['surgeMultiplier'] = surgeMultiplier;
    data['radiusKm'] = radiusKm;
    return data;
  }
}
