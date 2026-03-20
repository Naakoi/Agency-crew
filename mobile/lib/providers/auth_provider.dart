import 'package:flutter/material.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../services/api_service.dart';

class AuthProvider with ChangeNotifier {
  final ApiService _api = ApiService();
  final _storage = const FlutterSecureStorage();
  
  bool _isAuthenticated = false;
  bool get isAuthenticated => _isAuthenticated;
  
  String? _token;
  Map<String, dynamic>? _user;
  Map<String, dynamic>? get user => _user;

  Future<void> checkAuth() async {
    _token = await _storage.read(key: 'auth_token');
    _isAuthenticated = _token != null;
    notifyListeners();
  }

  Future<bool> login(String email, String password) async {
    try {
      final res = await _api.login(email, password);
      if (res.statusCode == 200) {
        _token = res.data['token'];
        _user = res.data['user'];
        await _storage.write(key: 'auth_token', value: _token);
        _isAuthenticated = true;
        notifyListeners();
        return true;
      }
    } catch (e) {
      debugPrint("Login error: $e");
    }
    return false;
  }

  Future<void> logout() async {
    await _storage.delete(key: 'auth_token');
    _token = null;
    _isAuthenticated = false;
    notifyListeners();
  }
}
