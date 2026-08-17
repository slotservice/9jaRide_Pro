import 'dart:convert';
import 'dart:developer';

import 'package:customer/constant/constant.dart';
import 'package:customer/model/currency_model.dart';
import 'package:customer/model/language_model.dart';
import 'package:customer/services/localization_service.dart';
import 'package:customer/utils/Preferences.dart';
import 'package:customer/utils/fire_store_utils.dart';
import 'package:customer/utils/notification_service.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:get/get.dart';

class GlobalSettingController extends GetxController {
  @override
  void onInit() {
    // TODO: implement onInit
    notificationInit();
    getCurrentCurrency();

    super.onInit();
  }

  Future<void> getCurrentCurrency() async {
    final langF = () async {
      try {
        if (Preferences.getString(Preferences.languageCodeKey).toString().isNotEmpty) {
          LanguageModel languageModel = Constant.getLanguage();
          LocalizationService().changeLocale(languageModel.code.toString());
        } else {
          final value = await FireStoreUtils.getLanguage();
          if (value != null) {
            List<LanguageModel> languageList = value;
            if (languageList.where((element) => element.isDefault == true).isNotEmpty) {
              LanguageModel languageModel = languageList.firstWhere((element) => element.isDefault == true);
              Preferences.setString(Preferences.languageCodeKey, jsonEncode(languageModel));
              LocalizationService().changeLocale(languageModel.code.toString());
            }
          }
        }
      } catch (e) {
        log("Language load error: $e");
      }
    }();

    final currF = () async {
      try {
        final value = await FireStoreUtils.getCurrency();
        if (value != null) {
          Constant.currencyModel = value;
        } else {
          Constant.currencyModel = CurrencyModel(id: "", code: "USD", decimalDigits: 2, enable: true, name: "US Dollar", symbol: "\$", symbolAtRight: false);
        }
      } catch (e) {
        log("Currency load error: $e");
        Constant.currencyModel = CurrencyModel(id: "", code: "USD", decimalDigits: 2, enable: true, name: "US Dollar", symbol: "\$", symbolAtRight: false);
      }
    }();

    final settF = () async {
      try {
        await FireStoreUtils.getSettings();
      } catch (e) {
        log("Settings load error: $e");
      }
    }();

    await Future.wait([langF, currF, settF]);
  }

  NotificationService notificationService = NotificationService();

  void notificationInit() {
    notificationService.initInfo().then((value) async {
      String token = await NotificationService.getToken();
      log(":::::::TOKEN:::::: $token");
      _storeToken(token);
    });
    // Tokens rotate on reinstall, data clear and periodically. Without this the
    // stored token goes stale and the rider silently stops being told when a
    // driver accepts, arrives or starts the trip.
    FirebaseMessaging.instance.onTokenRefresh.listen(_storeToken);
  }

  /// Saves the push token against the signed in rider.
  ///
  /// This runs from onInit at startup, and Firebase restores the signed in user
  /// asynchronously, so currentUser is usually still null at that moment on a
  /// cold start. Reading it directly threw the token away on almost every
  /// launch, leaving whatever the last successful login wrote.
  void _storeToken(String token) {
    final User? user = FirebaseAuth.instance.currentUser;
    if (user != null) {
      FireStoreUtils.updateUserToken(user.uid, token);
      return;
    }
    FirebaseAuth.instance.authStateChanges().firstWhere((User? u) => u != null).then((User? u) {
      if (u != null) {
        FireStoreUtils.updateUserToken(u.uid, token);
      }
    });
  }
}
