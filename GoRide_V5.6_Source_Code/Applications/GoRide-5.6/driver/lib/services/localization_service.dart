import 'package:driver/lang/app_ar.dart';
import 'package:driver/lang/app_en.dart';
import 'package:driver/lang/app_fr.dart';
import 'package:driver/lang/app_ha.dart';
import 'package:driver/lang/app_ig.dart';
import 'package:driver/lang/app_yo.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';

class LocalizationService extends Translations {
  // Default locale
  static const locale = Locale('en', 'US');

  static final locales = [
    const Locale('en'),
    const Locale('ar'),
    const Locale('fr'),
    const Locale('yo'),
    const Locale('ha'),
    const Locale('ig'),
  ];

  // Keys and their translations
  // Translations are separated maps in `lang` file
  @override
  Map<String, Map<String, String>> get keys => {
        'en': enUS,
        'ar': arAR,
        'fr': frFR,
        'yo': yoYO,
        'ha': haHA,
        'ig': igIG,
      };

  // Gets locale from language, and updates the locale
  void changeLocale(String lang) {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Get.updateLocale(Locale(lang));
    });
  }
}
