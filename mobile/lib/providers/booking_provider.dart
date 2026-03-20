import 'package:flutter/material.dart';
import '../services/api_service.dart';
import '../models/models.dart';

class BookingProvider with ChangeNotifier {
  final ApiService _api = ApiService();
  
  List<Booking> _bookings = [];
  List<Booking> get bookings => _bookings;
  
  bool _isLoading = false;
  bool get isLoading => _isLoading;

  Map<String, int> _stats = {'total': 0, 'in_hotel': 0, 'departed': 0};
  Map<String, int> get stats => _stats;

  Future<void> fetchBookings() async {
    _isLoading = true;
    notifyListeners();
    try {
      final res = await _api.getBookings();
      if (res.statusCode == 200) {
        final List data = res.data['data'];
        _bookings = data.map((b) => Booking.fromJson(b)).toList();
      }
      final sRes = await _api.getStats();
      if (sRes.statusCode == 200) {
        _stats = Map<String, int>.from(sRes.data);
      }
    } catch (e) {
      debugPrint("Fetch bookings error: $e");
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<bool> updateStatus(Booking booking, String status) async {
    try {
      final res = await _api.toggleStatus(booking.id, status: status);
      if (res.statusCode == 200) {
        booking.status = res.data['status'];
        // Refresh all stats instead of manual local update for simplicity/accuracy
        fetchBookings(); 
        return true;
      }
    } catch (e) {
      debugPrint("Update status error: $e");
    }
    return false;
  }
}
