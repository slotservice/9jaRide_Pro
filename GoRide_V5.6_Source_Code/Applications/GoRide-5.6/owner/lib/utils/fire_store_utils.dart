import 'dart:async';
import 'dart:convert';
import 'dart:developer';
import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:firebase_core/firebase_core.dart';
import 'package:flutter/material.dart';
import 'package:owner/constant/collection_name.dart';
import 'package:owner/constant/constant.dart';
import 'package:owner/constant/show_toast_dialog.dart';
import 'package:owner/firebase_options.dart';
import 'package:owner/model/admin_commission.dart';
import 'package:owner/model/bank_details_model.dart';
import 'package:owner/model/conversation_model.dart';
import 'package:owner/model/currency_model.dart';
import 'package:owner/model/document_model.dart';
import 'package:owner/model/driver_document_model.dart';
import 'package:owner/model/driver_rules_model.dart';
import 'package:owner/model/driver_user_model.dart';
import 'package:owner/model/inbox_model.dart';
import 'package:owner/model/intercity_order_model.dart';
import 'package:owner/model/language_model.dart';
import 'package:owner/model/language_privacy_policy.dart';
import 'package:owner/model/language_terms_condition.dart';
import 'package:owner/model/on_boarding_model.dart';
import 'package:owner/model/order/driverId_accept_reject.dart';
import 'package:owner/model/order_model.dart';
import 'package:owner/model/owner_user_model.dart';
import 'package:owner/model/payment_model.dart';
import 'package:owner/model/service_model.dart';
import 'package:owner/model/subscription_history.dart';
import 'package:owner/model/subscription_plan_model.dart';
import 'package:owner/model/user_model.dart';
import 'package:owner/model/wallet_transaction_model.dart';
import 'package:owner/model/withdraw_model.dart';
import 'package:owner/model/zone_model.dart';
import 'package:owner/themes/app_colors.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:http/http.dart' as http;

enum FirebaseEnv { defaultDb, staging }

/// Change this to switch between default / staging
const FirebaseEnv currentEnv = FirebaseEnv.defaultDb;

class FireStoreUtils {
  FireStoreUtils._privateConstructor();

  static final FireStoreUtils instance = FireStoreUtils._privateConstructor();

  static late FirebaseFirestore fireStore;

  /// Initialize Firestore with a FirebaseApp and optional databaseId
  void init(FirebaseApp app, {String? databaseId}) {
    fireStore = FirebaseFirestore.instanceFor(app: app, databaseId: databaseId);
  }

  static Future<bool> isLogin() async {
    bool isLogin = false;
    if (FirebaseAuth.instance.currentUser != null) {
      isLogin = await userExitOrNot(FirebaseAuth.instance.currentUser!.uid);
    } else {
      isLogin = false;
    }
    return isLogin;
  }

  static Future<bool> isMaintenanceMode() async {
    bool isMaintenance = false;
    await fireStore.collection(CollectionName.settings).doc('maintenance_settings').get().then((value) async {
      isMaintenance = value.data()?['ownerApp'] == true;
      log("isMaintenance :: $isMaintenance");
    });
    return isMaintenance;
  }

  static Future<void> getGoogleAPIKey() async {
    await fireStore.collection(CollectionName.settings).doc("globalKey").get().then((value) {
      if (value.exists) {
        Constant.mapAPIKey = value.data()!["googleMapKey"];
      }
    });

    await fireStore.collection(CollectionName.settings).doc("notification_setting").get().then((value) {
      if (value.exists) {
        if (value.data() != null) {
          Constant.senderId = value.data()!['senderId'].toString();
          Constant.jsonNotificationFileURL = value.data()!['serviceJson'].toString();
        }
      }
    });

    await fireStore.collection(CollectionName.settings).doc("adminCommission").get().then((value) {
      if (value.data() != null) {
        AdminCommission adminCommission = AdminCommission.fromJson(value.data()!);
        Constant.adminCommission = adminCommission;
      }
    });

    await fireStore.collection(CollectionName.settings).doc("global").get().then((value) {
      if (value.exists) {
        if (value.data()!["privacyPolicy"] != null) {
          Constant.privacyPolicy = <LanguagePrivacyPolicy>[];
          value.data()!["privacyPolicy"].forEach((v) {
            Constant.privacyPolicy.add(LanguagePrivacyPolicy.fromJson(v));
          });
        }

        if (value.data()!["termsAndConditions"] != null) {
          Constant.termsAndConditions = <LanguageTermsCondition>[];
          value.data()!["termsAndConditions"].forEach((v) {
            Constant.termsAndConditions.add(LanguageTermsCondition.fromJson(v));
          });
        }
        Constant.appVersion = value.data()!["appVersion"];
        Constant.ownerDeleteUrl = value.data()!["ownerPanelUrl"] ?? '';
      }
    });

    await fireStore.collection(CollectionName.settings).doc("contact_us").get().then((value) {
      if (value.exists) {
        Constant.supportURL = value.data()!["supportURL"];
      }
    });
  }

  static String getCurrentUid() {
    return FirebaseAuth.instance.currentUser!.uid;
  }

  static Future getGlobalSetting() async {
    await fireStore.collection(CollectionName.settings).doc("globalValue").get().then((value) async {
      if (value.exists) {
        Constant.defaultCountryCode = value.data()!["defaultCountryCode"];
        AppColors.lightsecondprimary = Color(int.parse(value.data()?['app_owner_light_color'].replaceFirst("#", "0xff")));
        AppColors.darksecondprimary = Color(int.parse(value.data()?['app_owner_color'].replaceFirst("#", "0xff")));
        Constant.distanceType = value.data()!["distanceType"];
        Constant.radius = value.data()!["radius"];
        Constant.minimumAmountToWithdrawal = value.data()!["minimumAmountToWithdrawal"];
        Constant.mapType = value.data()!["mapType"];
        Constant.selectedMapType = value.data()!["selectedMapType"];
        Constant.isVerifyOwnerDocument = value.data()?["isVerifyOwnerDocument"] ?? false;
        Constant.isSubscriptionModelApplied = value.data()!["subscription_model"];
      }
    });
  }

  static Future<DriverUserModel?> getDriverProfile(String uuid) async {
    DriverUserModel? driverModel;
    await fireStore.collection(CollectionName.driverUsers).doc(uuid).get().then((value) {
      if (value.exists) {
        driverModel = DriverUserModel.fromJson(value.data()!);
      }
    }).catchError((error) {
      log("Failed to update user: $error");
      driverModel = null;
    });
    return driverModel;
  }

  static Future<UserModel?> getCustomer(String uuid) async {
    UserModel? userModel;
    await fireStore.collection(CollectionName.users).doc(uuid).get().then((value) {
      if (value.exists) {
        userModel = UserModel.fromJson(value.data()!);
      }
    }).catchError((error) {
      log("Failed to update user: $error");
      userModel = null;
    });
    return userModel;
  }

  static Future<bool> updateUser(UserModel userModel) async {
    bool isUpdate = false;

    await fireStore.collection(CollectionName.users).doc(userModel.id).set(userModel.toJson()).whenComplete(() {
      isUpdate = true;
    }).catchError((error) {
      log("Failed to update user: $error");
      isUpdate = false;
    });
    return isUpdate;
  }

  static Future<PaymentModel?> getPayment() async {
    PaymentModel? paymentModel;
    await fireStore.collection(CollectionName.settings).doc("payment").get().then((value) {
      paymentModel = PaymentModel.fromJson(value.data()!);
    });
    return paymentModel;
  }

  static Future<CurrencyModel?> getCurrency() async {
    CurrencyModel? currencyModel;
    await fireStore.collection(CollectionName.currency).where("enable", isEqualTo: true).get().then((value) {
      if (value.docs.isNotEmpty) {
        currencyModel = CurrencyModel.fromJson(value.docs.first.data());
      }
    });
    return currencyModel;
  }

  static Future<bool> updateDriverUser(DriverUserModel userModel) async {
    bool isUpdate = false;
    await fireStore.collection(CollectionName.driverUsers).doc(userModel.id).set(userModel.toJson()).whenComplete(() {
      isUpdate = true;
    }).catchError((error) {
      log("Failed to update user: $error");
      isUpdate = false;
    });
    return isUpdate;
  }

  static Future<bool> userExitOrNot(String uid) async {
    bool isExit = false;

    await fireStore.collection(CollectionName.ownerUsers).doc(uid).get().then(
      (value) {
        if (value.exists) {
          isExit = true;
        } else {
          isExit = false;
        }
      },
    ).catchError((error) {
      log("Failed to update user: $error");
      isExit = false;
    });
    return isExit;
  }

  static Future<String> userExitRole(String uid) async {
    String role = '';
    try {
      await fireStore.collection(CollectionName.users).doc(uid).get().then((value) {
        if (value.exists) {
          role = Constant.customerType;
        } else {
          role = '';
        }
      });
      if (role == '') {
        await fireStore.collection(CollectionName.driverUsers).doc(uid).get().then((value) {
          if (value.exists) {
            role = Constant.driverType;
          } else {
            role = '';
          }
        });
      }
      if (role == '') {
        await fireStore.collection(CollectionName.ownerUsers).doc(uid).get().then((value) {
          if (value.exists) {
            role = Constant.currentUserType;
          } else {
            role = '';
          }
        });
      }
    } catch (e) {
      role = '';
    }
    return role;
  }

  static Future<List<DocumentModel>> getDocumentList() async {
    List<DocumentModel> documentList = [];
    await fireStore.collection(CollectionName.documents).where('type', isEqualTo: Constant.currentUserType).where('enable', isEqualTo: true).where('isDeleted', isEqualTo: false).get().then((value) {
      for (var element in value.docs) {
        DocumentModel documentModel = DocumentModel.fromJson(element.data());
        documentList.add(documentModel);
      }
    }).catchError((error) {
      log(error.toString());
    });
    return documentList;
  }

  static Future<List<ServiceModel>> getService() async {
    List<ServiceModel> serviceList = [];
    await fireStore.collection(CollectionName.service).where('enable', isEqualTo: true).get().then((value) {
      for (var element in value.docs) {
        ServiceModel documentModel = ServiceModel.fromJson(element.data());
        serviceList.add(documentModel);
      }
    }).catchError((error) {
      log(error.toString());
    });
    return serviceList;
  }

  static Future<ServiceModel> getServiceById(String? id) async {
    ServiceModel serviceList = ServiceModel();
    await fireStore.collection(CollectionName.service).where('id', isEqualTo: id).where('enable', isEqualTo: true).get().then((value) {
      serviceList = ServiceModel.fromJson(value.docs.first.data());
    }).catchError((error) {
      log(error.toString());
    });
    return serviceList;
  }

  static Future<DriverDocumentModel?> getDocumentOfOwner() async {
    DriverDocumentModel? driverDocumentModel;
    await fireStore.collection(CollectionName.driverDocument).doc(getCurrentUid()).get().then((value) async {
      if (value.exists) {
        driverDocumentModel = DriverDocumentModel.fromJson(value.data()!);
      }
    });
    return driverDocumentModel;
  }

  static Future<bool> uploadDriverDocument(Documents documents) async {
    bool isAdded = false;
    DriverDocumentModel driverDocumentModel = DriverDocumentModel();
    List<Documents> documentsList = [];
    await fireStore.collection(CollectionName.driverDocument).doc(getCurrentUid()).get().then((value) async {
      if (value.exists) {
        DriverDocumentModel newDriverDocumentModel = DriverDocumentModel.fromJson(value.data()!);
        documentsList = newDriverDocumentModel.documents!;
        var contain = newDriverDocumentModel.documents!.where((element) => element.documentId == documents.documentId);
        if (contain.isEmpty) {
          documentsList.add(documents);

          driverDocumentModel.id = getCurrentUid();
          driverDocumentModel.documents = documentsList;
        } else {
          var index = newDriverDocumentModel.documents!.indexWhere((element) => element.documentId == documents.documentId);

          driverDocumentModel.id = getCurrentUid();
          documentsList.removeAt(index);
          documentsList.insert(index, documents);
          driverDocumentModel.documents = documentsList;
          isAdded = false;
          ShowToastDialog.showToast("Document is under verification");
        }
      } else {
        documentsList.add(documents);
        driverDocumentModel.id = getCurrentUid();
        driverDocumentModel.documents = documentsList;
      }
    });

    await fireStore.collection(CollectionName.driverDocument).doc(getCurrentUid()).set(driverDocumentModel.toJson()).then((value) {
      isAdded = true;
    }).catchError((error) {
      isAdded = false;
      log(error.toString());
    });

    return isAdded;
  }

  static Future<List<DriverUserModel>> getAllDriverList() async {
    try {
      final querySnapshot = await fireStore.collection(CollectionName.driverUsers).where('ownerId', isEqualTo: FireStoreUtils.getCurrentUid()).get();

      if (querySnapshot.docs.isNotEmpty) {
        return querySnapshot.docs.map((doc) => DriverUserModel.fromJson(doc.data())).toList();
      } else {
        return [];
      }
    } catch (error) {
      log("Failed to fetch driver list: $error");
      return [];
    }
  }

  static Future<bool?> deleteDriverById(String uid) async {
    bool? isDelete;
    try {
      await fireStore.collection(CollectionName.driverUsers).doc(uid).delete().then((value) {
        isDelete = true;
      });
    } catch (e, s) {
      log('FireStoreUtils.firebaseCreateNewUser $e $s');
      return false;
    }
    return isDelete;
  }

  static Future<List<DriverRulesModel>?> getDriverRules() async {
    List<DriverRulesModel> driverRulesModel = [];
    await fireStore.collection(CollectionName.driverRules).where('enable', isEqualTo: true).where('isDeleted', isEqualTo: false).get().then((value) async {
      for (var element in value.docs) {
        DriverRulesModel vehicleModel = DriverRulesModel.fromJson(element.data());
        driverRulesModel.add(vehicleModel);
      }
    });
    return driverRulesModel;
  }

  static Future<bool?> setOrder(OrderModel orderModel) async {
    bool isAdded = false;
    await fireStore.collection(CollectionName.orders).doc(orderModel.id).set(orderModel.toJson()).then((value) {
      isAdded = true;
    }).catchError((error) {
      log("Failed to update user: $error");
      isAdded = false;
    });
    return isAdded;
  }

  static Future<bool?> bankDetailsIsAvailable() async {
    bool isAdded = false;
    await fireStore.collection(CollectionName.bankDetails).doc(FireStoreUtils.getCurrentUid()).get().then((value) {
      if (value.exists) {
        isAdded = true;
      } else {
        isAdded = false;
      }
    }).catchError((error) {
      log("Failed to update user: $error");
      isAdded = false;
    });
    return isAdded;
  }

  static Future<OrderModel?> getOrder(String orderId) async {
    OrderModel? orderModel;
    await fireStore.collection(CollectionName.orders).doc(orderId).get().then((value) {
      if (value.data() != null) {
        orderModel = OrderModel.fromJson(value.data()!);
      }
    });
    return orderModel;
  }

  static Future<InterCityOrderModel?> getInterCityOrder(String orderId) async {
    InterCityOrderModel? orderModel;
    await fireStore.collection(CollectionName.ordersIntercity).doc(orderId).get().then((value) {
      if (value.data() != null) {
        orderModel = InterCityOrderModel.fromJson(value.data()!);
      }
    });
    return orderModel;
  }

  static Future<bool?> acceptRide(OrderModel orderModel, DriverIdAcceptReject driverIdAcceptReject) async {
    bool isAdded = false;
    await fireStore.collection(CollectionName.orders).doc(orderModel.id).collection("acceptedDriver").doc(driverIdAcceptReject.driverId).set(driverIdAcceptReject.toJson()).then((value) {
      isAdded = true;
    }).catchError((error) {
      log("Failed to update user: $error");
      isAdded = false;
    });
    return isAdded;
  }

  static Future<List<WalletTransactionModel>?> getWalletTransaction() async {
    List<WalletTransactionModel> walletTransactionModel = [];

    await fireStore.collection(CollectionName.walletTransaction).where('userId', isEqualTo: FireStoreUtils.getCurrentUid()).orderBy('createdDate', descending: true).get().then((value) {
      for (var element in value.docs) {
        WalletTransactionModel taxModel = WalletTransactionModel.fromJson(element.data());
        walletTransactionModel.add(taxModel);
      }
    }).catchError((error) {
      log(error.toString());
    });
    return walletTransactionModel;
  }

  static Future<List<WalletTransactionModel>?> getWeekWalletTransaction() async {
    DateTime now = DateTime.now();
    DateTime startOfWeek = now.subtract(Duration(days: now.weekday - 1));
    DateTime startOfWeekMidnight = DateTime(startOfWeek.year, startOfWeek.month, startOfWeek.day);

    List<WalletTransactionModel> walletTransactionModel = [];

    await fireStore
        .collection(CollectionName.walletTransaction)
        .where('userId', isEqualTo: FireStoreUtils.getCurrentUid())
        .where('createdDate', isGreaterThanOrEqualTo: startOfWeekMidnight)
        .orderBy('createdDate', descending: true)
        .get()
        .then((value) {
      for (var element in value.docs) {
        WalletTransactionModel taxModel = WalletTransactionModel.fromJson(element.data());
        walletTransactionModel.add(taxModel);
      }
    }).catchError((error) {
      log(error.toString());
    });
    return walletTransactionModel;
  }

  static Future<bool?> setWalletTransaction(WalletTransactionModel walletTransactionModel) async {
    bool isAdded = false;
    await fireStore.collection(CollectionName.walletTransaction).doc(walletTransactionModel.id).set(walletTransactionModel.toJson()).then((value) {
      isAdded = true;
    }).catchError((error) {
      log("Failed to update user: $error");
      isAdded = false;
    });
    return isAdded;
  }

  static Future<List<LanguageModel>?> getLanguage() async {
    List<LanguageModel> languageList = [];

    await fireStore.collection(CollectionName.languages).where("enable", isEqualTo: true).where("isDeleted", isEqualTo: false).get().then((value) {
      for (var element in value.docs) {
        LanguageModel taxModel = LanguageModel.fromJson(element.data());
        languageList.add(taxModel);
      }
    }).catchError((error) {
      log(error.toString());
    });
    return languageList;
  }

  static Future<List<OnBoardingModel>> getOnBoardingList() async {
    List<OnBoardingModel> onBoardingModel = [];
    await fireStore.collection(CollectionName.onBoarding).where("type", isEqualTo: "ownerApp").get().then((value) {
      for (var element in value.docs) {
        OnBoardingModel documentModel = OnBoardingModel.fromJson(element.data());
        onBoardingModel.add(documentModel);
      }
    }).catchError((error) {
      log(error.toString());
    });
    return onBoardingModel;
  }

  static Future addInBox(InboxModel inboxModel) async {
    return await fireStore.collection(CollectionName.chat).doc(inboxModel.orderId).set(inboxModel.toJson()).then((document) {
      return inboxModel;
    });
  }

  static Future addChat(ConversationModel conversationModel) async {
    log("Chat :: ${conversationModel.orderId} :: ${conversationModel.id}");
    return await fireStore.collection(CollectionName.chat).doc(conversationModel.orderId).collection("thread").doc(conversationModel.id).set(conversationModel.toJson()).then((document) {
      return conversationModel;
    });
  }

  static Future addInAdminBox(InboxModel inboxModel) async {
    return await fireStore.collection(CollectionName.chat).doc(FireStoreUtils.getCurrentUid()).set(inboxModel.toJson()).then((document) {
      return inboxModel;
    });
  }

  static Future addAdminChat(ConversationModel conversationModel) async {
    return await fireStore.collection(CollectionName.chat).doc(conversationModel.senderId).collection("thread").doc(conversationModel.id).set(conversationModel.toJson()).then((document) {
      return conversationModel;
    });
  }

  static Future<BankDetailsModel?> getBankDetails() async {
    BankDetailsModel? bankDetailsModel;
    await fireStore.collection(CollectionName.bankDetails).doc(FireStoreUtils.getCurrentUid()).get().then((value) {
      if (value.data() != null) {
        bankDetailsModel = BankDetailsModel.fromJson(value.data()!);
      }
    });
    return bankDetailsModel;
  }

  static Future<bool?> updateBankDetails(BankDetailsModel bankDetailsModel) async {
    bool isAdded = false;
    await fireStore.collection(CollectionName.bankDetails).doc(bankDetailsModel.userId).set(bankDetailsModel.toJson()).then((value) {
      isAdded = true;
    }).catchError((error) {
      log("Failed to update user: $error");
      isAdded = false;
    });
    return isAdded;
  }

  static Future<bool?> setWithdrawRequest(WithdrawModel withdrawModel) async {
    bool isAdded = false;
    await fireStore.collection(CollectionName.withdrawalHistory).doc(withdrawModel.id).set(withdrawModel.toJson()).then((value) {
      isAdded = true;
    }).catchError((error) {
      log("Failed to update user: $error");
      isAdded = false;
    });
    return isAdded;
  }

  static Future<List<WithdrawModel>> getWithDrawRequest() async {
    List<WithdrawModel> withdrawalList = [];
    await fireStore.collection(CollectionName.withdrawalHistory).where('userId', isEqualTo: getCurrentUid()).orderBy('createdDate', descending: true).get().then((value) {
      for (var element in value.docs) {
        WithdrawModel documentModel = WithdrawModel.fromJson(element.data());
        withdrawalList.add(documentModel);
      }
    }).catchError((error) {
      log(error.toString());
    });
    return withdrawalList;
  }

  static Future<List<ZoneModel>?> getZone() async {
    List<ZoneModel> airPortList = [];
    await fireStore.collection(CollectionName.zone).where('publish', isEqualTo: true).get().then((value) {
      for (var element in value.docs) {
        ZoneModel ariPortModel = ZoneModel.fromJson(element.data());
        airPortList.add(ariPortModel);
      }
    }).catchError((error) {
      log(error.toString());
    });
    return airPortList;
  }

  static Future<ZoneModel> getZoneById({required String zoneId}) async {
    ZoneModel zoneModeldata = ZoneModel();
    await fireStore.collection(CollectionName.zone).doc(zoneId).get().then((value) {
      if (value.data() != null) {
        zoneModeldata = ZoneModel.fromJson(value.data()!);
      }
    }).catchError((error) {
      log(error.toString());
    });
    return zoneModeldata;
  }

  static Future<List<SubscriptionPlanModel>> getAllSubscriptionPlans() async {
    List<SubscriptionPlanModel> subscriptionPlanModels = [];
    await fireStore
        .collection(CollectionName.subscriptionPlans)
        .where('isEnable', isEqualTo: true)
        .orderBy(
          'place',
          descending: false,
        )
        .get()
        .then((value) async {
      if (value.docs.isNotEmpty) {
        for (var element in value.docs) {
          SubscriptionPlanModel subscriptionPlanModel = SubscriptionPlanModel.fromJson(element.data());
          if (Constant.isSubscriptionModelApplied == false) {
            if (subscriptionPlanModel.id == Constant.commissionSubscriptionID) {
              if (subscriptionPlanModel.planFor == Constant.currentUserType || subscriptionPlanModel.planFor == 'both') {
                subscriptionPlanModels.add(subscriptionPlanModel);
              }
            }
          } else {
            if (subscriptionPlanModel.planFor == Constant.currentUserType || subscriptionPlanModel.planFor == 'both') {
              subscriptionPlanModels.add(subscriptionPlanModel);
            }
          }
        }
      }
    });
    return subscriptionPlanModels;
  }

  static Future<SubscriptionPlanModel?> getSubscriptionPlanById({required String planId}) async {
    SubscriptionPlanModel? subscriptionPlanModel = SubscriptionPlanModel();
    if (planId.isNotEmpty) {
      await fireStore.collection(CollectionName.subscriptionPlans).doc(planId).get().then((value) async {
        if (value.exists) {
          subscriptionPlanModel = SubscriptionPlanModel.fromJson(value.data() as Map<String, dynamic>);
        }
      });
    }
    return subscriptionPlanModel;
  }

  static Future<bool?> setSubscriptionTransaction(SubscriptionHistoryModel subscriptionPlan) async {
    bool isAdded = false;
    await fireStore.collection(CollectionName.subscriptionHistory).doc(subscriptionPlan.id).set(subscriptionPlan.toJson()).then((value) {
      isAdded = true;
    }).catchError((error) {
      log("Failed to update user: $error");
      isAdded = false;
    });
    return isAdded;
  }

  static Future<List<SubscriptionHistoryModel>> getSubscriptionHistory() async {
    List<SubscriptionHistoryModel> subscriptionHistoryList = [];
    await fireStore.collection(CollectionName.subscriptionHistory).where('user_id', isEqualTo: getCurrentUid()).orderBy('createdAt', descending: true).get().then((value) async {
      if (value.docs.isNotEmpty) {
        for (var element in value.docs) {
          SubscriptionHistoryModel subscriptionHistoryModel = SubscriptionHistoryModel.fromJson(element.data());
          subscriptionHistoryList.add(subscriptionHistoryModel);
        }
      }
    });
    return subscriptionHistoryList;
  }

  static Future<bool> deleteSubscriptionById({required String subscriptionId}) async {
    try {
      await fireStore.collection(CollectionName.subscriptionHistory).doc(subscriptionId).delete();
      return true;
    } catch (e) {
      debugPrint('Failed to delete subscription: $e');
      return false;
    }
  }

  static late StreamSubscription<QuerySnapshot> adminChatSeenSubscription;
  static void setSeen() {
    final currentUserId = FireStoreUtils.getCurrentUid();

    adminChatSeenSubscription = fireStore
        .collection(CollectionName.chat)
        .doc(currentUserId)
        .collection("thread")
        .where('senderId', isEqualTo: Constant.adminType)
        .where('seen', isEqualTo: false)
        .snapshots()
        .listen((querySnapshot) async {
      for (final doc in querySnapshot.docs) {
        try {
          await doc.reference.update({'seen': true});
        } catch (e) {
          log(e.toString());
        }
      }
    }, onError: (error) {
      log(error.toString());
    });
  }

  static void stopSeenListener() {
    adminChatSeenSubscription.cancel();
  }

  static late StreamSubscription<QuerySnapshot> customerChatSeenSubscription;
  static void setCustomerChatSeen({required String orderId, required String customerId}) {
    customerChatSeenSubscription =
        fireStore.collection(CollectionName.chat).doc(orderId).collection("thread").where('senderId', isEqualTo: customerId).where('seen', isEqualTo: false).snapshots().listen((querySnapshot) async {
      for (final doc in querySnapshot.docs) {
        try {
          log("querySnapshot.docs :: ${doc.data().length}");
          await doc.reference.update({'seen': true});
        } catch (e) {
          log(e.toString());
        }
      }
    }, onError: (error) {
      log(error.toString());
    });
  }

  static void stopCustomerSeenListener() {
    customerChatSeenSubscription.cancel();
  }

  //Owner
  static Future<bool> updateOwnerUser(OwnerUserModel userModel) async {
    bool isUpdate = false;
    await fireStore.collection(CollectionName.ownerUsers).doc(userModel.id).set(userModel.toJson()).whenComplete(() {
      isUpdate = true;
    }).catchError((error) {
      log("Failed to update user: $error");
      isUpdate = false;
    });
    return isUpdate;
  }

  static Future<OwnerUserModel?> getOwnerProfile(String uuid) async {
    OwnerUserModel? ownerModel;
    await fireStore.collection(CollectionName.ownerUsers).doc(uuid).get().then((value) {
      if (value.exists) {
        ownerModel = OwnerUserModel.fromJson(value.data()!);
      }
    }).catchError((error) {
      log("Failed to update user: $error");
      ownerModel = null;
    });
    return ownerModel;
  }

  static Future<bool> deleteOwnerUser() async {
    try {
      final currentUser = FirebaseAuth.instance.currentUser;
      final String? userId = currentUser?.uid;
      if (userId?.isEmpty == true) {
        return false;
      }

      await fireStore.collection(CollectionName.ownerUsers).doc(userId).delete();
      await deleteAuthUser(userId!);
      // await FirebaseAuth.instance.currentUser?.delete();
      return true;
    } catch (e, s) {
      log('deleteUser: Failed to delete user. Error: $e\nStackTrace: $s');
      return false;
    }
  }

  static Future<bool?> updatedOwnerWallet({required String amount}) async {
    bool isAdded = false;
    await getOwnerProfile(FireStoreUtils.getCurrentUid()).then((value) async {
      if (value != null) {
        OwnerUserModel userModel = value;
        userModel.walletAmount = (double.parse(userModel.walletAmount.toString()) + double.parse(amount)).toString();
        await FireStoreUtils.updateOwnerUser(userModel).then((value) {
          isAdded = value;
        });
      }
    });
    return isAdded;
  }

  static Future<List<OrderModel>> getAllCityRide() async {
    List<OrderModel> orderList = <OrderModel>[];
    await fireStore.collection(CollectionName.orders).where('ownerId', isEqualTo: FireStoreUtils.getCurrentUid()).orderBy("createdDate", descending: true).get().then((value) {
      if (value.docs.isNotEmpty) {
        for (var element in value.docs) {
          OrderModel orderModel = OrderModel.fromJson(element.data());
          orderList.add(orderModel);
        }
      }
    }).catchError((error) {
      log("Failed to update user: $error");
      orderList = [];
    });
    return orderList;
  }

  static Future<List<InterCityOrderModel>> getAllIntercityRide() async {
    List<InterCityOrderModel> orderList = <InterCityOrderModel>[];
    await fireStore.collection(CollectionName.ordersIntercity).where('ownerId', isEqualTo: FireStoreUtils.getCurrentUid()).orderBy("createdDate", descending: true).get().then((value) {
      if (value.docs.isNotEmpty) {
        for (var element in value.docs) {
          InterCityOrderModel orderModel = InterCityOrderModel.fromJson(element.data());
          orderList.add(orderModel);
        }
      }
    }).catchError((error) {
      log("Failed to update user: $error");
      orderList = [];
    });
    return orderList;
  }

  static Stream<QuerySnapshot> getNewCityOrdersByFilter({required String status, required String driverId}) {
    try {
      return driverId == 'all'
          ? fireStore
              .collection(CollectionName.orders)
              .where('ownerId', isEqualTo: FireStoreUtils.getCurrentUid())
              .where('status', isEqualTo: status)
              .orderBy('createdDate', descending: true)
              .snapshots()
          : fireStore
              .collection(CollectionName.orders)
              .where('ownerId', isEqualTo: FireStoreUtils.getCurrentUid())
              .where('acceptedDriverId', arrayContains: FireStoreUtils.getCurrentUid())
              .where('status', isEqualTo: status)
              .orderBy('createdDate', descending: true)
              .snapshots();
    } catch (e, stack) {
      log("🔥 getCityOrdersByFilter error: $e\n$stack");
      return const Stream.empty();
    }
  }

  static Stream<QuerySnapshot> getCityOrdersByFilter({required List<String> status, required String driverId}) {
    try {
      return driverId == 'all'
          ? fireStore
              .collection(CollectionName.orders)
              .where('ownerId', isEqualTo: FireStoreUtils.getCurrentUid())
              .where('status', whereIn: status)
              .orderBy('createdDate', descending: true)
              .snapshots()
          : fireStore
              .collection(CollectionName.orders)
              .where('ownerId', isEqualTo: FireStoreUtils.getCurrentUid())
              .where('driverId', isEqualTo: driverId)
              .where('status', whereIn: status)
              .orderBy('createdDate', descending: true)
              .snapshots();
    } catch (e, stack) {
      log("🔥 getCityOrdersByFilter error: $e\n$stack");
      return const Stream.empty();
    }
  }

  static Stream<QuerySnapshot> getNewIntercityOrdersByFilter({required String status, required String driverId}) {
    try {
      return driverId == 'all'
          ? fireStore
              .collection(CollectionName.ordersIntercity)
              .where('ownerId', isEqualTo: FireStoreUtils.getCurrentUid())
              .where('intercityServiceId', whereIn: ["647f340e35553", '647f350983ba2', 'UmQ2bjWTnlwoKqdCIlTr'])
              .where('status', isEqualTo: status)
              .orderBy('createdDate', descending: true)
              .snapshots()
          : fireStore
              .collection(CollectionName.ordersIntercity)
              .where('ownerId', isEqualTo: FireStoreUtils.getCurrentUid())
              .where('intercityServiceId', whereIn: ["647f340e35553", '647f350983ba2', 'UmQ2bjWTnlwoKqdCIlTr'])
              .where('acceptedDriverId', arrayContains: driverId)
              .where('status', isEqualTo: status)
              .orderBy('createdDate', descending: true)
              .snapshots();
    } catch (e, stack) {
      log("🔥 getCityOrdersByFilter error: $e\n$stack");
      return const Stream.empty();
    }
  }

  static Stream<QuerySnapshot> getIntercityOrdersByFilter({required List<String> status, required String driverId}) {
    try {
      return driverId == 'all'
          ? fireStore
              .collection(CollectionName.ordersIntercity)
              .where('ownerId', isEqualTo: FireStoreUtils.getCurrentUid())
              .where('intercityServiceId', whereIn: ["647f340e35553", '647f350983ba2', 'UmQ2bjWTnlwoKqdCIlTr'])
              .where('status', whereIn: status)
              .orderBy('createdDate', descending: true)
              .snapshots()
          : fireStore
              .collection(CollectionName.ordersIntercity)
              .where('ownerId', isEqualTo: FireStoreUtils.getCurrentUid())
              .where('intercityServiceId', whereIn: ["647f340e35553", '647f350983ba2', 'UmQ2bjWTnlwoKqdCIlTr'])
              .where('driverId', isEqualTo: driverId)
              .where('status', whereIn: status)
              .orderBy('createdDate', descending: true)
              .snapshots();
    } catch (e, stack) {
      log("🔥 getCityOrdersByFilter error: $e\n$stack");
      return const Stream.empty();
    }
  }

  static Stream<QuerySnapshot> getNewFreightOrdersByFilter({required String status, required String driverId}) {
    try {
      return driverId == 'all'
          ? fireStore
              .collection(CollectionName.ordersIntercity)
              .where('ownerId', isEqualTo: FireStoreUtils.getCurrentUid())
              .where('intercityServiceId', isEqualTo: "Kn2VEnPI3ikF58uK8YqY")
              .where('status', isEqualTo: status)
              .orderBy('createdDate', descending: true)
              .snapshots()
          : fireStore
              .collection(CollectionName.ordersIntercity)
              .where('ownerId', isEqualTo: FireStoreUtils.getCurrentUid())
              .where('acceptedDriverId', arrayContains: driverId)
              .where('intercityServiceId', isEqualTo: "Kn2VEnPI3ikF58uK8YqY")
              .where('status', isEqualTo: status)
              .orderBy('createdDate', descending: true)
              .snapshots();
    } catch (e, stack) {
      log("🔥 getCityOrdersByFilter error: $e\n$stack");
      return const Stream.empty();
    }
  }

  static Stream<QuerySnapshot> getFreightOrdersByFilter({required List<String> status, required String driverId}) {
    try {
      return driverId == 'all'
          ? fireStore
              .collection(CollectionName.ordersIntercity)
              .where('ownerId', isEqualTo: FireStoreUtils.getCurrentUid())
              .where('intercityServiceId', isEqualTo: "Kn2VEnPI3ikF58uK8YqY")
              .where('status', whereIn: status)
              .orderBy('createdDate', descending: true)
              .snapshots()
          : fireStore
              .collection(CollectionName.ordersIntercity)
              .where('ownerId', isEqualTo: FireStoreUtils.getCurrentUid())
              .where('intercityServiceId', isEqualTo: "Kn2VEnPI3ikF58uK8YqY")
              .where('driverId', isEqualTo: driverId)
              .where('status', whereIn: status)
              .orderBy('createdDate', descending: true)
              .snapshots();
    } catch (e, stack) {
      log("🔥 getCityOrdersByFilter error: $e\n$stack");
      return const Stream.empty();
    }
  }

  static Future<bool> deleteAuthUser(String uid) async {
    try {
      final user = FirebaseAuth.instance.currentUser;
      if (user == null) {
        print("❌ No user is logged in.");
        return false;
      }

      final idToken = await user.getIdToken();
      final projectId = DefaultFirebaseOptions.currentPlatform.projectId;
      final url = Uri.parse('https://us-central1-$projectId.cloudfunctions.net/deleteUser');

      final response = await http.post(
        url,
        headers: {
          'Authorization': 'Bearer $idToken',
          'Content-Type': 'application/json',
        },
        body: jsonEncode({
          'data': {'uid': uid}, // 👈 matches your Cloud Function structure
        }),
      );

      print("Response [${response.statusCode}]: ${response.body}");

      if (response.statusCode == 200) {
        final decoded = jsonDecode(response.body);
        return decoded['result']?['success'] == true || decoded['success'] == true;
      } else {
        print("⚠️ Cloud Function failed: ${response.body}");
        return false;
      }
    } catch (e) {
      print("❌ Error deleting driver: $e");
      return false;
    }
  }
}
