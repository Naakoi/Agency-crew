import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/foundation.dart';
import 'api_service.dart';

class FcmService {
  static final FirebaseMessaging _messaging = FirebaseMessaging.instance;
  static final ApiService _apiService = ApiService();

  static Future<void> initialize() async {
    // Request permission (Required for iOS, good practice for Android 13+)
    NotificationSettings settings = await _messaging.requestPermission(
      alert: true,
      announcement: false,
      badge: true,
      carPlay: false,
      criticalAlert: false,
      provisional: false,
      sound: true,
    );

    if (settings.authorizationStatus == AuthorizationStatus.authorized) {
      if (kDebugMode) {
        print('User granted permission');
      }
      
      // Get the token and send it to our server
      String? token = await _messaging.getToken();
      if (token != null) {
        await _saveToken(token);
      }

      // Listen for token refreshes
      _messaging.onTokenRefresh.listen((newToken) {
        _saveToken(newToken);
      });

      // Handle foreground messages
      FirebaseMessaging.onMessage.listen((RemoteMessage message) {
        if (kDebugMode) {
          print('Got a message whilst in the foreground!');
          print('Message data: ${message.data}');
        }

        if (message.notification != null) {
          if (kDebugMode) {
            print('Message also contained a notification: ${message.notification?.title}');
          }
        }
      });
    }
  }

  static Future<void> _saveToken(String token) async {
    try {
      await _apiService.saveFcmToken(token);
      if (kDebugMode) {
        print('FCM Token saved to server: $token');
      }
    } catch (e) {
      if (kDebugMode) {
        print('Error saving FCM token: $e');
      }
    }
  }
}
