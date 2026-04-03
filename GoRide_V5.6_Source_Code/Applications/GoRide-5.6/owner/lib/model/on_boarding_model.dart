import 'package:owner/model/language_description.dart';
import 'package:owner/model/language_name.dart';

class OnBoardingModel {
  String? image;
  List<LanguageDescription>? description;
  String? id;
  List<LanguageName>? title;
  String? type;

  OnBoardingModel({this.image, this.description, this.id, this.title, this.type});

  OnBoardingModel.fromJson(Map<String, dynamic> json) {
    image = json['image'];
    id = json['id'];
    type = json['type'];
    if (json['title'] != null) {
      title = <LanguageName>[];
      json['title'].forEach((v) {
        title!.add(LanguageName.fromJson(v));
      });
    }

    if (json['description'] != null) {
      description = <LanguageDescription>[];
      json['description'].forEach((v) {
        description!.add(LanguageDescription.fromJson(v));
      });
    }
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data['image'] = image;
    data['type'] = type;
    if (description != null) {
      data['description'] = description!.map((v) => v.toJson()).toList();
    }
    data['id'] = id;
    if (title != null) {
      data['title'] = title!.map((v) => v.toJson()).toList();
    }
    return data;
  }
}
