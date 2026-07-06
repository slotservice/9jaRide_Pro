import 'package:customer/model/customer_document_model.dart';
import 'package:customer/model/document_model.dart';
import 'package:customer/utils/fire_store_utils.dart';
import 'package:get/get.dart';

class KycController extends GetxController {
  RxBool isLoading = true.obs;
  RxBool hasError = false.obs;
  RxList<DocumentModel> documentList = <DocumentModel>[].obs;
  RxList<Documents> uploadedDocuments = <Documents>[].obs;

  @override
  void onInit() {
    loadDocuments();
    super.onInit();
  }

  Future<void> loadDocuments() async {
    isLoading.value = true;
    hasError.value = false;
    try {
      documentList.value = await FireStoreUtils.getCustomerDocumentList();
      CustomerDocumentModel? uploaded = await FireStoreUtils.getDocumentOfCustomer();
      if (uploaded != null && uploaded.documents != null) {
        uploadedDocuments.value = uploaded.documents!;
      }
    } catch (e) {
      hasError.value = true;
    }
    isLoading.value = false;
    update();
  }

  Documents? getUploadedDoc(String documentId) {
    var match = uploadedDocuments.where((d) => d.documentId == documentId);
    return match.isNotEmpty ? match.first : null;
  }
}
