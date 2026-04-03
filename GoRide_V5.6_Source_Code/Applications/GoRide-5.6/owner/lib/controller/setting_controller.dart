import 'package:owner/constant/constant.dart';
import 'package:owner/model/driver_user_model.dart';
import 'package:owner/model/language_model.dart';
import 'package:owner/model/subscription_history.dart';
import 'package:owner/utils/Preferences.dart';
import 'package:owner/utils/fire_store_utils.dart';
import 'package:get/get.dart';
import 'package:http/http.dart' as http;

class SettingController extends GetxController {
  @override
  void onInit() {
    // TODO: implement onInit
    getLanguage();
    super.onInit();
  }

  RxBool isLoading = true.obs;
  RxList<LanguageModel> languageList = <LanguageModel>[].obs;
  RxList<String> modeList = <String>['Light mode', 'Dark mode'].obs;
  Rx<LanguageModel> selectedLanguage = LanguageModel().obs;
  Rx<String> selectedMode = "".obs;

  Future<void> getLanguage() async {
    await FireStoreUtils.getLanguage().then((value) {
      if (value != null) {
        languageList.value = value;
        if (Preferences.getString(Preferences.languageCodeKey).toString().isNotEmpty) {
          LanguageModel pref = Constant.getLanguage();

          for (var element in languageList) {
            if (element.id == pref.id) {
              selectedLanguage.value = element;
            }
          }
        }
      }
    });
    if (Preferences.getString(Preferences.themKey).toString().isNotEmpty) {
      selectedMode.value = Preferences.getString(Preferences.themKey).toString();
    } else {
      if (Get.isDarkMode == true) {
        selectedMode.value = 'Dark mode';
      } else {
        selectedMode.value = 'Light mode';
      }
    }
    isLoading.value = false;
    update();
  }

  Future<bool> deleteUserFromServer() async {
    var url = '${Constant.ownerDeleteUrl}/api/delete-user';
    try {
      var response = await http.post(
        Uri.parse(url),
        body: {
          'uuid': FireStoreUtils.getCurrentUid(),
        },
      );
      if (response.statusCode == 200) {
        return true;
      } else {
        return false;
      }
    } catch (e) {
      return false;
    }
  }

  Future<void> deleteAllSubscriptionHistoty() async {
    List<SubscriptionHistoryModel> subscriptionHistoryList = await FireStoreUtils.getSubscriptionHistory();
    if (subscriptionHistoryList.isNotEmpty) {
      await Future.wait(subscriptionHistoryList.map((subscription) async {
        if (subscription.id != null) {
          await FireStoreUtils.deleteSubscriptionById(subscriptionId: subscription.id!);
        }
      }));
    }
  }

  Future<void> deleteAllDriver() async {
    List<DriverUserModel> driverList = await FireStoreUtils.getAllDriverList();
    if (driverList.isNotEmpty) {
      await Future.wait(driverList.map((driver) async {
        if (driver.id != null) {
          await FireStoreUtils.deleteAuthUser(driver.id!);
          await FireStoreUtils.deleteDriverById(driver.id!);
        }
      }));
    }
  }
}
