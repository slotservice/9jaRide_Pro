import 'package:customer/constant/constant.dart';
import 'package:customer/model/order_model.dart';
import 'package:customer/themes/app_colors.dart';
import 'package:customer/themes/button_them.dart';
import 'package:customer/ui/review/review_screen.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';

/// Shown as soon as a payment goes through.
///
/// Riders previously got nothing but a toast, which is easy to miss and left
/// them unsure whether the payment had actually landed. This is also the only
/// place the rating prompt is offered at the end of a trip, instead of the
/// rider having to dig back into ride history to find it.
Future<void> showPaymentConfirmedDialog({
  required OrderModel orderModel,
  required String amount,
  required String paymentMethod,
  bool awaitingDriverConfirmation = false,
}) async {
  final BuildContext? context = Get.context;
  if (context == null) return;

  await showDialog(
    context: context,
    barrierDismissible: false,
    builder: (BuildContext dialogContext) {
      return AlertDialog(
        shape: const RoundedRectangleBorder(borderRadius: BorderRadius.all(Radius.circular(14))),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(
              awaitingDriverConfirmation ? Icons.hourglass_top_rounded : Icons.check_circle_rounded,
              color: awaitingDriverConfirmation ? Colors.orange : Colors.green,
              size: 56,
            ),
            const SizedBox(height: 12),
            Text(
              awaitingDriverConfirmation ? "Payment Sent".tr : "Payment Confirmed".tr,
              textAlign: TextAlign.center,
              style: GoogleFonts.poppins(fontSize: 18, fontWeight: FontWeight.w600),
            ),
            const SizedBox(height: 6),
            Text(
              awaitingDriverConfirmation
                  ? "Your driver needs to confirm they received the cash.".tr
                  : "Your payment has been received. Thank you for riding with us.".tr,
              textAlign: TextAlign.center,
              style: GoogleFonts.poppins(fontSize: 13, color: AppColors.subTitleColor),
            ),
            const SizedBox(height: 14),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text("Amount".tr, style: GoogleFonts.poppins(fontSize: 13, color: AppColors.subTitleColor)),
                Text(
                  Constant.amountShow(amount: amount),
                  style: GoogleFonts.poppins(fontSize: 15, fontWeight: FontWeight.w600),
                ),
              ],
            ),
            const SizedBox(height: 4),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text("Paid with".tr, style: GoogleFonts.poppins(fontSize: 13, color: AppColors.subTitleColor)),
                Flexible(
                  child: Text(
                    paymentMethod,
                    textAlign: TextAlign.right,
                    style: GoogleFonts.poppins(fontSize: 15, fontWeight: FontWeight.w600),
                  ),
                ),
              ],
            ),
          ],
        ),
        actions: [
          ButtonThem.buildButton(
            dialogContext,
            title: "Rate your trip".tr,
            btnHeight: 44,
            onPress: () {
              Navigator.of(dialogContext).pop();
              Get.to(const ReviewScreen(), arguments: {
                "type": "orderModel",
                "orderModel": orderModel,
              });
            },
          ),
          const SizedBox(height: 6),
          TextButton(
            onPressed: () => Navigator.of(dialogContext).pop(),
            child: Text("Done".tr, style: GoogleFonts.poppins(fontWeight: FontWeight.w500)),
          ),
        ],
      );
    },
  );
}
