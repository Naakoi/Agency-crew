import 'package:flutter/material.dart';
import '../services/api_service.dart';
import '../models/models.dart';

class BookingProvider with ChangeNotifier {
  final ApiService _api = ApiService();
  
  List<Booking> _bookings = [];
  List<Booking> get bookings => _bookings;
  
  bool _isLoading = false;
  bool get isLoading => _isLoading;

  List<Crew> _crews = [];
  List<Company> _companies = [];
  List<Hotel> _hotels = [];

  List<Crew> get crews => _crews;
  List<Company> get companies => _companies;
  List<Hotel> get hotels => _hotels;


  Map<String, int> _stats = {
    'total': 0, 
    'booked': 0, 
    'pickup_to_hotel': 0, 
    'in_hotel': 0, 
    'pickup_to_plane': 0, 
    'cancelled': 0
  };
  Map<String, int> get stats => _stats;

  String? _currentSearch;
  String? _currentStatus;
  
  String? get currentStatus => _currentStatus;
  String? get currentSearch => _currentSearch;

  Future<void> fetchBookings({String? search, String? status}) async {
    if (search != null) _currentSearch = search;
    if (status != null) _currentStatus = status;
    
    _isLoading = true;
    notifyListeners();
    try {
      final res = await _api.getBookings(search: _currentSearch, status: _currentStatus);
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

  void clearFilters() {
    _currentSearch = null;
    _currentStatus = null;
    fetchBookings();
  }

  Future<void> fetchMetadata() async {
    try {
      final creRes = await _api.getCrews();
      if (creRes.statusCode == 200) {
        _crews = (creRes.data as List).map((c) => Crew.fromJson(c)).toList();
      }
      final comRes = await _api.getCompanies();
      if (comRes.statusCode == 200) {
        _companies = (comRes.data as List).map((c) => Company.fromJson(c)).toList();
      }
      final hotRes = await _api.getHotels();
      if (hotRes.statusCode == 200) {
        _hotels = (hotRes.data as List).map((h) => Hotel.fromJson(h)).toList();
      }
      notifyListeners();
    } catch (e) {
      debugPrint("Fetch metadata error: $e");
    }
  }

  Future<bool> createBooking(Map<String, dynamic> data) async {
    _isLoading = true;
    notifyListeners();
    try {
      final res = await _api.createBooking(data);
      if (res.statusCode == 201) {
        fetchBookings(); // Refresh the list
        return true;
      }
    } catch (e) {
      debugPrint("Create booking error: $e");
    } finally {
      _isLoading = false;
      notifyListeners();
    }
    return false;
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
