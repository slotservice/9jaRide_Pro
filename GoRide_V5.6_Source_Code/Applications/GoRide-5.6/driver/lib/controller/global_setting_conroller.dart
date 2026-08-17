import 'dart:convert';
import 'dart:developer';

import 'package:driver/constant/constant.dart';
import 'package:driver/model/currency_model.dart';
import 'package:driver/model/driver_user_model.dart';
import 'package:driver/model/language_model.dart';
import 'package:driver/services/localization_service.dart';
import 'package:driver/utils/Preferences.dart';
import 'package:driver/utils/fire_store_utils.dart';
import 'package:driver/utils/notification_service.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:get/get.dart';

class GlobalSettingController extends GetxController {
  RxBool isLoading = true.obs;
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

    final apiF = () async {
      try {
        await FireStoreUtils.getGoogleAPIKey();
      } catch (e) {
        log("Settings load error: $e");
      }
    }();

    await Future.wait([langF, currF, apiF]);

    isLoading.value = false;
    update();
  }

  NotificationService notificationService = NotificationService();

  void notificationInit() {
    notificationService.initInfo().then((value) async {
      String token = await NotificationService.getToken();
      log(":::::::TOKEN:::::: $token");
      _storeToken(token);
    });
    // Keep the stored FCM token fresh. Tokens rotate (app reinstall, data clear,
    // periodic refresh); without listening for rotation the driver_users doc
    // keeps a stale token and booking pushes silently fail with 404 UNREGISTERED
    // — the driver never gets notified of a new ride.
    FirebaseMessaging.instance.onTokenRefresh.listen(_storeToken);
  }

  /// Saves the push token against the signed in driver.
  ///
  /// This runs from onInit at startup, and Firebase restores the signed in user
  /// asynchronously, so currentUser is usually still null at that moment on a
  /// cold start. Reading it directly threw the token away on almost every
  /// launch, leaving whatever the last successful login happened to write. That
  /// is how most of the online drivers ended up with tokens FCM rejects as
  /// NotRegistered, and why booking alerts stopped arriving for them.
  void _storeToken(String token) {
    final User? user = FirebaseAuth.instance.currentUser;
    if (user != null) {
      FireStoreUtils.updateDriverToken(user.uid, token);
      return;
    }
    // Not signed in yet. Wait for the session to come back rather than dropping
    // the token on the floor.
    FirebaseAuth.instance.authStateChanges().firstWhere((User? u) => u != null).then((User? u) {
      if (u != null) {
        FireStoreUtils.updateDriverToken(u.uid, token);
      }
    });
  }
}
