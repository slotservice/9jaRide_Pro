import 'dart:async';

import 'package:customer/payment/paystack/paystack_url_genrater.dart';
import 'package:customer/themes/app_colors.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:webview_flutter/webview_flutter.dart';

class PayStackScreen extends StatefulWidget {
  final String initialURl;
  final String reference;
  final String amount;
  final String secretKey;
  final String callBackUrl;

  const PayStackScreen({Key? key, required this.initialURl, required this.reference, required this.amount, required this.secretKey, required this.callBackUrl}) : super(key: key);

  @override
  State<PayStackScreen> createState() => _PayStackScreenState();
}

class _PayStackScreenState extends State<PayStackScreen> {
  WebViewController controller = WebViewController();
  bool _paymentHandled = false;

  @override
  void initState() {
    initController();
    super.initState();
  }

  initController() {
    controller = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..setBackgroundColor(const Color(0x00000000))
      ..setNavigationDelegate(
        NavigationDelegate(
          onProgress: (int progress) {
            // Update loading bar.
          },
          onPageStarted: (String url) {},
          onPageFinished: (String url) {},
          onWebResourceError: (WebResourceError error) {},
          onNavigationRequest: (NavigationRequest navigation) async {
            debugPrint("--->2${navigation.url}");
            final String url = navigation.url;
            final String cb = widget.callBackUrl;
            // Detect the Paystack return/callback robustly: any redirect that carries
            // our transaction reference, or lands on the configured callback URL, or on
            // Paystack's own close/callback pages. We do NOT rely on an exact string
            // match (query-param order/extra params vary) — verifyTransaction() below
            // asks Paystack directly, so it's the source of truth and returns false if
            // the charge did not actually succeed.
            final bool isCallbackHit = url.contains('trxref=${widget.reference}') ||
                url.contains('reference=${widget.reference}') ||
                (cb.isNotEmpty && url.startsWith(cb)) ||
                url.startsWith('https://foodieweb.siswebapp.com/success') ||
                url == 'https://hello.pstk.xyz/callback' ||
                url == 'https://standard.paystack.co/close' ||
                url == 'https://talazo.app/login';
            if (isCallbackHit && !_paymentHandled) {
              _paymentHandled = true;
              final isDone = await PayStackURLGen.verifyTransaction(secretKey: widget.secretKey, reference: widget.reference, amount: widget.amount);
              Get.back(result: isDone);
              return NavigationDecision.prevent;
            }
            return NavigationDecision.navigate;
          },
        ),
      )
      ..loadRequest(Uri.parse(widget.initialURl));
  }

  @override
  Widget build(BuildContext context) {
    return WillPopScope(
      onWillPop: () async {
        _showMyDialog();
        return false;
      },
      child: Scaffold(
        appBar: AppBar(
            backgroundColor: AppColors.background,
            title: const Text("Payment"),
            centerTitle: false,
            leading: GestureDetector(
              onTap: () {
                _showMyDialog();
              },
              child: Icon(
                Icons.arrow_back,
                color: AppColors.lightprimary,
              ),
            )),
        body: WebViewWidget(controller: controller),
      ),
    );
  }

  Future<void> _showMyDialog() async {
    return showDialog<void>(
      context: context,
      barrierDismissible: true, // user must tap button!
      builder: (BuildContext context) {
        return AlertDialog(
          title: const Text('Cancel Payment'),
          content: const SingleChildScrollView(
            child: Text("cancelPayment?"),
          ),
          actions: <Widget>[
            TextButton(
              child: const Text(
                'Cancel',
                style: TextStyle(color: Colors.red),
              ),
              onPressed: () {
                Navigator.of(context).pop();
                Navigator.of(context).pop(false);
              },
            ),
            TextButton(
              child: const Text(
                'Continue',
                style: TextStyle(color: Colors.green),
              ),
              onPressed: () {
                Navigator.of(context).pop();
              },
            ),
          ],
        );
      },
    );
  }
}
