import 'dart:async';
import 'dart:convert';
import 'dart:developer';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:http/http.dart' as http;
import 'package:webview_flutter/webview_flutter.dart';

class MidtransScreen extends StatefulWidget {
  final String initialURl;
  final String serverKey;
  final bool isSandbox;

  const MidtransScreen({super.key, required this.initialURl, required this.serverKey, required this.isSandbox});

  @override
  State<MidtransScreen> createState() => _MidtransScreenState();
}

class _MidtransScreenState extends State<MidtransScreen> {
  WebViewController controller = WebViewController();
  bool isLoading = true;
  @override
  void initState() {
    controller.clearCache();
    initController();

    super.initState();
  }

  initController() {
    controller = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..setBackgroundColor(const Color(0x00000000))
      ..setNavigationDelegate(
        NavigationDelegate(
          onPageFinished: ((url) {
            setState(() {
              isLoading = false;
            });
          }),
          onNavigationRequest: (NavigationRequest navigation) async {
            log("Midtrans :: ${navigation.url}");
            String? orderId =
                Uri.parse(navigation.url).queryParameters['merchant_order_id'];
            if (orderId != null) {
              // The finish callback being reached does NOT mean the payment
              // succeeded. Verify the real transaction status against the
              // Midtrans status API before treating it as paid.
              final bool verified = await _verifyMidtransStatus(orderId);
              Get.back(result: verified);
            }
            return NavigationDecision.navigate;
          },
        ),
      )
      ..loadRequest(Uri.parse(widget.initialURl));
  }

  Future<bool> _verifyMidtransStatus(String orderId) async {
    try {
      final String base = widget.isSandbox ? 'https://api.sandbox.midtrans.com/v2' : 'https://api.midtrans.com/v2';
      final String auth = 'Basic ${base64Encode(utf8.encode('${widget.serverKey}:'))}';
      final response = await http.get(
        Uri.parse('$base/$orderId/status'),
        headers: {
          'Accept': 'application/json',
          'Authorization': auth,
        },
      );
      log("Midtrans status :: ${response.body}");
      final data = jsonDecode(response.body);
      final status = data['transaction_status'];
      final fraud = data['fraud_status'];
      return status == 'settlement' || (status == 'capture' && (fraud == null || fraud == 'accept'));
    } catch (e) {
      log("Midtrans status error :: $e");
      return false;
    }
  }

  @override
  Widget build(BuildContext context) {
    // ignore: deprecated_member_use
    return WillPopScope(
        onWillPop: () async {
          _showMyDialog();
          return false;
        },
        child: Scaffold(
            appBar: AppBar(
                backgroundColor: Colors.black,
                centerTitle: false,
                leading: GestureDetector(
                  onTap: () {
                    _showMyDialog();
                  },
                  child: const Icon(
                    Icons.arrow_back,
                    color: Colors.white,
                  ),
                )),
            body: Stack(alignment: Alignment.center, children: [
              WebViewWidget(controller: controller),
              Visibility(
                  visible: isLoading,
                  child: const Center(child: CircularProgressIndicator()))
            ])));
  }

  Future<void> _showMyDialog() async {
    return showDialog<void>(
      context: context,
      barrierDismissible: true, // user must tap button!
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text('Cancel Payment'.tr),
          content: SingleChildScrollView(
            child: Text("cancelPayment?".tr),
          ),
          actions: <Widget>[
            TextButton(
              child: Text(
                'Cancel'.tr,
                style: const TextStyle(color: Colors.red),
              ),
              onPressed: () {
                Get.back(result: false);
                Get.back(result: false);
              },
            ),
            TextButton(
              child: Text(
                'Continue'.tr,
                style: const TextStyle(color: Colors.green),
              ),
              onPressed: () {
                Get.back(result: false);
              },
            ),
          ],
        );
      },
    );
  }
}
