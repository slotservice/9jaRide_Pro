import 'dart:convert';
import 'dart:math';
import 'package:crypto/crypto.dart';
import 'package:driver/constant/constant.dart';
import 'package:driver/constant/show_toast_dialog.dart';
import 'package:driver/ui/auth_screen/otp_screen.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_sign_in/google_sign_in.dart';
import 'package:http/http.dart' as http;
import 'package:sign_in_with_apple/sign_in_with_apple.dart';

const String _termiiApiKey = 'TLQWUgFmliowHdqTGgUDjIRhIkIgVDHIEexBOIfHpIZkZOKQBrXEGuGGtFeFMs';
const String _termiiBaseUrl = 'https://v3.api.termii.com';

class LoginController extends GetxController {
  Rx<TextEditingController> phoneNumberController = TextEditingController().obs;
  Rx<TextEditingController> countryCode = TextEditingController(text: Constant.defaultCountryCode).obs;
  Rx<TextEditingController> countryISOCode = TextEditingController(text: Constant.defaultCountryCode).obs;

  Rx<GlobalKey<FormState>> formKey = GlobalKey<FormState>().obs;

  Future<void> sendCode() async {
    ShowToastDialog.showLoader("Please wait");
    try {
      final fullPhone = (countryCode.value.text + phoneNumberController.value.text).replaceAll('+', '');

      final response = await http.post(
        Uri.parse('$_termiiBaseUrl/api/sms/otp/send'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({
          'api_key': _termiiApiKey,
          'message_type': 'NUMERIC',
          'to': fullPhone,
          'from': 'naijaride',
          'channel': 'dnd',
          'pin_attempts': 3,
          'pin_time_to_live': 5,
          'pin_length': 6,
          'pin_placeholder': '< 1234 >',
          'message_text': 'Your 9jaRide Pro verification code is < 1234 >. Valid for 5 minutes.',
          'pin_type': 'NUMERIC',
        }),
      );

      ShowToastDialog.closeLoader();

      final data = jsonDecode(response.body);
      debugPrint('Termii sendOtp response: $data');

      if (response.statusCode == 200 && data['pinId'] != null) {
        Get.to(const OtpScreen(), arguments: {
          'countryCode': countryCode.value.text,
          'countryISOCode': countryISOCode.value.text,
          'phoneNumber': phoneNumberController.value.text,
          'pinId': data['pinId'],
        });
      } else {
        ShowToastDialog.showToast('Could not send OTP. Please try again.');
      }
    } catch (e) {
      debugPrint('Termii sendCode error: $e');
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast('Could not send OTP. Check your connection and try again.');
    }
  }

  Future<UserCredential?> signInWithGoogle() async {
    try {
      final GoogleSignIn googleSignIn = GoogleSignIn.instance;

      await googleSignIn.initialize();

      final GoogleSignInAccount googleUser = await googleSignIn.authenticate();
      if (googleUser.id.isEmpty) return null;

      final GoogleSignInAuthentication googleAuth = googleUser.authentication;

      final credential = GoogleAuthProvider.credential(
        idToken: googleAuth.idToken,
      );
      final userCredential = await FirebaseAuth.instance.signInWithCredential(credential);

      return userCredential;
    } catch (e) {
      print("Google Sign-In Error: $e");
      return null;
    }
  }

  Future<Map<String, dynamic>?> signInWithApple() async {
    try {
      // Request credential for the currently signed in Apple account.
      AuthorizationCredentialAppleID appleCredential = await SignInWithApple.getAppleIDCredential(
        scopes: [
          AppleIDAuthorizationScopes.email,
          AppleIDAuthorizationScopes.fullName,
        ],
      );
      print(appleCredential);

      // Create an `OAuthCredential` from the credential returned by Apple.
      final oauthCredential = OAuthProvider("apple.com").credential(idToken: appleCredential.identityToken, accessToken: appleCredential.authorizationCode);

      // Sign in the user with Firebase. If the nonce we generated earlier does
      // not match the nonce in `appleCredential.identityToken`, sign in will fail.
      UserCredential userCredential = await FirebaseAuth.instance.signInWithCredential(oauthCredential);
      return {"appleCredential": appleCredential, "userCredential": userCredential};
    } catch (e) {
      debugPrint(e.toString());
    }
    return null;
  }

  String generateNonce([int length = 32]) {
    const charset = '0123456789ABCDEFGHIJKLMNOPQRSTUVXYZabcdefghijklmnopqrstuvwxyz-._';
    final random = Random.secure();
    return List.generate(length, (_) => charset[random.nextInt(charset.length)]).join();
  }

  /// Returns the sha256 hash of [input] in hex notation.
  String sha256ofString(String input) {
    final bytes = utf8.encode(input);
    final digest = sha256.convert(bytes);
    return digest.toString();
  }

  @override
  void onInit() {
    super.onInit();
  }

//
// Location location = Location();
//
// getLocation() async {
//   bool serviceEnabled;
//   PermissionStatus permissionGranted;
//
//
//   serviceEnabled = await location.serviceEnabled();
//   print(serviceEnabled);
//   if (!serviceEnabled) {
//     serviceEnabled = await location.requestService();
//     if (!serviceEnabled) {
//       return;
//     }
//   }
//
//   permissionGranted = await location.hasPermission();
//   print(permissionGranted);
//   if (permissionGranted == PermissionStatus.denied) {
//     permissionGranted = await location.requestPermission();
//     if (permissionGranted != PermissionStatus.granted) {
//       return;
//     }
//   }
//   print("111");
//   Geolocator.getCurrentPosition().then((value){
//     print("location-->${value}");
//   });
//   await location.getLocation().then((value) {
//     print("location-->");
//     Constant.currentLocation = value;
//     update();
//   });
// }
}
