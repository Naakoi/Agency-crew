import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class ApiService {
  // Production URL for the Oracle Cloud instance (Secure HTTPS)
  static const String baseUrl = "https://agencycrew.cppl.com.ki/api";
  
  final Dio _dio = Dio(BaseOptions(
    baseUrl: baseUrl,
    connectTimeout: const Duration(seconds: 10),
    receiveTimeout: const Duration(seconds: 10),
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    },
  ));

  final _storage = const FlutterSecureStorage();

  ApiService() {
    _dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) async {
        final token = await _storage.read(key: 'auth_token');
        if (token != null) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        return handler.next(options);
      },
      onError: (e, handler) {
        // Handle common errors like 401 Unauthorized
        return handler.next(e);
      },
    ));
  }

  Future<Response> login(String email, String password) async {
    return await _dio.post('/login', data: {
      'email': email,
      'password': password,
    });
  }

  Future<Response> saveFcmToken(String token) async {
    return await _dio.post('/fcm-token', data: {
      'token': token,
      'device_type': 'mobile',
    });
  }

  Future<Response> getStats() async => await _dio.get('/stats');
  Future<Response> getBookings({String? search, String? status}) async => 
      await _dio.get('/bookings', queryParameters: {
        if (search != null && search.isNotEmpty) 'search': search,
        if (status != null && status != 'total') 'status': status,
      });
  Future<Response> getCrews() async => await _dio.get('/crews');
  Future<Response> getHotels() async => await _dio.get('/hotels');
  Future<Response> getCompanies() async => await _dio.get('/companies');

  Future<Response> createBooking(Map<String, dynamic> data) async => 
      await _dio.post('/bookings', data: data);

  Future<Response> toggleStatus(int id, {String? status}) async => 
      await _dio.post('/bookings/$id/toggle-status', data: status != null ? {'status': status} : null);
}
