import 'package:owner/model/document_model.dart';
import 'package:owner/model/driver_document_model.dart';
import 'package:owner/utils/fire_store_utils.dart';
import 'package:get/get.dart';

class OnlineRegistrationController extends GetxController {
  RxBool isLoading = true.obs;

  @override
  void onInit() {
    // TODO: implement onInit
    getDocument();
    super.onInit();
  }

  RxList documentList = <DocumentModel>[].obs;
  RxList ownerDocumentList = <Documents>[].obs;

  Future<void> getDocument() async {
    await FireStoreUtils.getDocumentList().then((value) {
      documentList.value = value;
      isLoading.value = false;
    });

    await FireStoreUtils.getDocumentOfOwner().then((value) {
      if (value != null) {
        ownerDocumentList.value = value.documents!;
      }
    });
    update();
  }
}
