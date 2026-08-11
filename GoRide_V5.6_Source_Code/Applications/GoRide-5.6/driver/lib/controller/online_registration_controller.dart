import 'dart:async';

import 'package:driver/model/document_model.dart';
import 'package:driver/model/driver_document_model.dart';
import 'package:driver/utils/fire_store_utils.dart';
import 'package:get/get.dart';

class OnlineRegistrationController extends GetxController {
  RxBool isLoading = true.obs;

  @override
  void onInit() {
    // TODO: implement onInit
    getDocument();
    super.onInit();
  }

  @override
  void onClose() {
    _driverDocumentSubscription?.cancel();
    super.onClose();
  }

  RxList documentList = <DocumentModel>[].obs;
  RxList driverDocumentList = <Documents>[].obs;

  StreamSubscription<DriverDocumentModel?>? _driverDocumentSubscription;

  getDocument() async {
    await FireStoreUtils.getDocumentList().then((value) {
      documentList.value = value;
      isLoading.value = false;
    });

    // Listen instead of reading once, so an approval or rejection from the
    // admin panel shows up on this screen straight away.
    _driverDocumentSubscription?.cancel();
    _driverDocumentSubscription = FireStoreUtils.streamDocumentOfDriver().listen((value) {
      driverDocumentList.value = value?.documents ?? <Documents>[];
      update();
    });
    update();
  }
}
