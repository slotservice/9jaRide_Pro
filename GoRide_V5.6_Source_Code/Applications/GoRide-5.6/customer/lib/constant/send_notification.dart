// ignore_for_file: non_constant_identifier_names

import 'dart:convert';
import 'package:customer/constant/constant.dart';
import 'package:flutter/cupertino.dart';
import 'package:googleapis_auth/auth_io.dart';
import 'package:googleapis_auth/googleapis_auth.dart';
import 'package:http/http.dart' as http;

class SendNotification {
  static final _scopes = ['https://www.googleapis.com/auth/firebase.messaging'];

  // The token is good for an hour. Minting a fresh one per notification meant
  // downloading the service json and doing a full OAuth exchange every single
  // send, which is two extra network round trips before the FCM call itself.
  // That was the bulk of the dispatch delay when a ride was booked.
  static AccessCredentials? _cachedCredentials;

  // Every network call in here is bounded. Without a timeout one stalled
  // request on a weak connection hangs the caller for ever: the accept-ride
  // flow awaits this inside a "Please wait" loader, and the loader's finally
  // never runs, so the driver is stuck on a spinner with no way out. A thrown
  // error was always handled; a hang never was.
  static const Duration _networkTimeout = Duration(seconds: 15);

  static Future getCharacters() {
    return http.get(Uri.parse(Constant.jsonNotificationFileURL.toString())).timeout(_networkTimeout);
  }

  static Future<String> getAccessToken() async {
    final AccessCredentials? cached = _cachedCredentials;
    // Refresh a couple of minutes early so a send never races the expiry.
    if (cached != null && cached.accessToken.expiry.isAfter(DateTime.now().toUtc().add(const Duration(minutes: 2)))) {
      return cached.accessToken.data;
    }

    Map<String, dynamic> jsonData = {};

    await getCharacters().then((response) {
      jsonData = json.decode(response.body);
    });
    final serviceAccountCredentials = ServiceAccountCredentials.fromJson(jsonData);

    final client = await clientViaServiceAccount(serviceAccountCredentials, _scopes).timeout(_networkTimeout);
    final AccessCredentials credentials = client.credentials;
    _cachedCredentials = credentials;
    // We drive the FCM call with plain http below, so this client is not needed
    // once we have the token. Leaving it open leaked a connection per send.
    client.close();
    return credentials.accessToken.data;
  }

  static Future<bool> sendOneNotification({required String token, required String title, required String body, required Map<String, dynamic> payload}) async {
    try {
      final String accessToken = await getAccessToken();
      debugPrint("accessToken=======${Constant.senderId}>");
      debugPrint(accessToken);

      final response = await http.post(
        Uri.parse('https://fcm.googleapis.com/v1/projects/${Constant.senderId}/messages:send'),
        headers: <String, String>{
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $accessToken',
        },
        body: jsonEncode(
          <String, dynamic>{
            'message': {
              'token': token,
              'notification': {'body': body, 'title': title},
              'data': payload,
            }
          },
        ),
      ).timeout(_networkTimeout);

      debugPrint("Notification=======>");
      debugPrint(response.statusCode.toString());
      debugPrint(response.body);
      return true;
    } catch (e) {
      debugPrint(e.toString());
      return false;
    }
  }
}
