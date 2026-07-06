import 'package:customer/constant/constant.dart';
import 'package:customer/controller/kyc_controller.dart';
import 'package:customer/model/customer_document_model.dart';
import 'package:customer/model/document_model.dart';
import 'package:customer/themes/app_colors.dart';
import 'package:customer/themes/button_them.dart';
import 'package:customer/themes/responsive.dart';
import 'package:customer/ui/dashboard_screen.dart';
import 'package:customer/ui/kyc/kyc_document_upload_screen.dart';
import 'package:customer/utils/DarkThemeProvider.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';

class KycScreen extends StatelessWidget {
  const KycScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final themeChange = Provider.of<DarkThemeProvider>(context);
    return GetBuilder<KycController>(
      init: KycController(),
      builder: (controller) {
        return Scaffold(
          backgroundColor: AppColors.background,
          body: Column(
            children: [
              SizedBox(height: Responsive.width(10, context)),
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 20),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Identity Verification'.tr,
                      style: GoogleFonts.poppins(color: Colors.white, fontSize: 22, fontWeight: FontWeight.w600),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      'Upload your documents to verify your identity.'.tr,
                      style: GoogleFonts.poppins(color: Colors.white70, fontSize: 13),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 16),
              Expanded(
                child: Container(
                  decoration: BoxDecoration(
                    color: Theme.of(context).colorScheme.background,
                    borderRadius: const BorderRadius.only(topLeft: Radius.circular(25), topRight: Radius.circular(25)),
                  ),
                  child: controller.isLoading.value
                      ? Constant.loader(isDarkTheme: themeChange.getThem())
                      : controller.hasError.value
                          ? _errorState(context, themeChange, controller)
                          : controller.documentList.isEmpty
                          ? _emptyState(context, themeChange)
                          : Column(
                              children: [
                                Expanded(
                                  child: ListView.builder(
                                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 12),
                                    itemCount: controller.documentList.length,
                                    itemBuilder: (context, index) {
                                      DocumentModel doc = controller.documentList[index];
                                      Documents? uploaded = controller.getUploadedDoc(doc.id ?? '');
                                      return _documentCard(context, themeChange, doc, uploaded);
                                    },
                                  ),
                                ),
                                Padding(
                                  padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
                                  child: ButtonThem.buildButton(
                                    context,
                                    btnWidthRatio: 1,
                                    title: 'Continue to App'.tr,
                                    onPress: () => Get.offAll(const DashBoardScreen()),
                                  ),
                                ),
                              ],
                            ),
                ),
              ),
            ],
          ),
        );
      },
    );
  }

  Widget _documentCard(BuildContext context, DarkThemeProvider themeChange, DocumentModel doc, Documents? uploaded) {
    String title = _safeTitle(doc);
    bool isVerified = uploaded?.verified == true;
    bool isUploaded = uploaded != null;

    return InkWell(
      onTap: () async {
        await Get.to(const KycDocumentUploadScreen(), arguments: {'documentModel': doc});
        Get.find<KycController>().loadDocuments();
      },
      child: Padding(
        padding: const EdgeInsets.symmetric(vertical: 6, horizontal: 4),
        child: Container(
          decoration: BoxDecoration(
            color: themeChange.getThem() ? AppColors.darkContainerBackground : AppColors.containerBackground,
            borderRadius: const BorderRadius.all(Radius.circular(10)),
            border: Border.all(
              color: themeChange.getThem() ? AppColors.darkContainerBorder : AppColors.containerBorder,
              width: 0.5,
            ),
            boxShadow: themeChange.getThem()
                ? null
                : [BoxShadow(color: Colors.grey.withOpacity(0.4), blurRadius: 6, offset: const Offset(0, 2))],
          ),
          child: Padding(
            padding: const EdgeInsets.all(12),
            child: Row(
              children: [
                Expanded(
                  child: Text(title, style: GoogleFonts.poppins(fontWeight: FontWeight.w500)),
                ),
                const SizedBox(width: 10),
                Container(
                  decoration: BoxDecoration(
                    color: isVerified
                        ? Colors.green
                        : isUploaded
                            ? Colors.orange
                            : AppColors.lightprimary,
                    borderRadius: const BorderRadius.all(Radius.circular(8)),
                  ),
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                  child: Text(
                    isVerified
                        ? 'Verified'.tr
                        : isUploaded
                            ? 'Pending'.tr
                            : 'Upload'.tr,
                    style: const TextStyle(color: Colors.white, fontSize: 12),
                  ),
                ),
                const SizedBox(width: 6),
                const Icon(Icons.arrow_forward_ios_rounded, color: Colors.grey, size: 16),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _errorState(BuildContext context, DarkThemeProvider themeChange, KycController controller) {
    return Column(
      children: [
        const SizedBox(height: 40),
        Icon(Icons.error_outline, size: 64, color: Colors.grey.shade400),
        const SizedBox(height: 12),
        Text('Unable to load documents. Please try again.'.tr, style: GoogleFonts.poppins(color: Colors.grey)),
        const SizedBox(height: 16),
        ButtonThem.buildBorderButton(
          context,
          title: 'Retry'.tr,
          btnWidthRatio: 0.4,
          onPress: () => controller.loadDocuments(),
        ),
        const Spacer(),
      ],
    );
  }

  Widget _emptyState(BuildContext context, DarkThemeProvider themeChange) {
    return Column(
      children: [
        const SizedBox(height: 40),
        Icon(Icons.description_outlined, size: 64, color: Colors.grey.shade400),
        const SizedBox(height: 12),
        Text('No documents required at this time.'.tr, style: GoogleFonts.poppins(color: Colors.grey)),
        const Spacer(),
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
          child: ButtonThem.buildButton(
            context,
            btnWidthRatio: 1,
            title: 'Continue to App'.tr,
            onPress: () => Get.offAll(const DashBoardScreen()),
          ),
        ),
      ],
    );
  }

  String _safeTitle(DocumentModel doc) {
    try {
      return Constant.localizationTitle(doc.title);
    } catch (_) {
      if (doc.title != null && doc.title!.isNotEmpty) {
        return doc.title!.first.title ?? doc.title!.first.name ?? 'Document';
      }
      return 'Document';
    }
  }
}
