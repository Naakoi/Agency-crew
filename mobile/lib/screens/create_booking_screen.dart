import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../providers/booking_provider.dart';
import '../theme/theme.dart';
import '../models/models.dart';

class CreateBookingScreen extends StatefulWidget {
  const CreateBookingScreen({super.key});

  @override
  State<CreateBookingScreen> createState() => _CreateBookingScreenState();
}

class _CreateBookingScreenState extends State<CreateBookingScreen> {
  final _formKey = GlobalKey<FormState>();
  final _crewTitleController = TextEditingController();
  final _invoiceNumberController = TextEditingController();
  final _remarksController = TextEditingController();

  Crew? _selectedCrew;
  Company? _selectedCompany;
  Hotel? _selectedHotel;
  DateTime? _checkIn;
  DateTime? _checkOut;
  String? _selectedStatus;

  final List<String> _statusOptions = [
    'booked',
    'pickup_to_hotel',
    'in_hotel',
    'pickup_to_plane',
  ];

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<BookingProvider>(context, listen: false).fetchMetadata();
    });
  }

  @override
  void dispose() {
    _crewTitleController.dispose();
    _invoiceNumberController.dispose();
    _remarksController.dispose();
    super.dispose();
  }

  void _submitForm() async {
    if (_formKey.currentState!.validate() &&
        _selectedCrew != null &&
        _selectedCompany != null &&
        _selectedHotel != null &&
        _checkIn != null &&
        _checkOut != null &&
        _selectedStatus != null) {
      
      final data = {
        'crew_id': _selectedCrew!.id,
        'company_id': _selectedCompany!.id,
        'hotel_id': _selectedHotel!.id,
        'crew_title': _crewTitleController.text,
        'check_in': _checkIn!.toIso8601String().split('T')[0],
        'check_out': _checkOut!.toIso8601String().split('T')[0],
        'invoice_number': _invoiceNumberController.text.isNotEmpty ? _invoiceNumberController.text : null,
        'remarks': _remarksController.text.isNotEmpty ? _remarksController.text : null,
        'status': _selectedStatus,
      };

      final provider = Provider.of<BookingProvider>(context, listen: false);
      final success = await provider.createBooking(data);

      if (success && mounted) {
        Navigator.pop(context);
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Booking created successfully!'), backgroundColor: AppTheme.green),
        );
      } else if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Failed to create booking'), backgroundColor: AppTheme.amber),
        );
      }
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please fill all required fields correctly.'), backgroundColor: AppTheme.amber),
      );
    }
  }

  Future<void> _selectDate(BuildContext context, bool isCheckIn) async {
    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: isCheckIn 
        ? (_checkIn ?? DateTime.now()) 
        : (_checkOut ?? _checkIn?.add(const Duration(days: 1)) ?? DateTime.now().add(const Duration(days: 1))),
      firstDate: DateTime.now().subtract(const Duration(days: 365)),
      lastDate: DateTime.now().add(const Duration(days: 365 * 5)),
      builder: (context, child) {
        return Theme(
          data: ThemeData.dark().copyWith(
            colorScheme: const ColorScheme.dark(
              primary: AppTheme.accent,
              onPrimary: Colors.white,
              surface: AppTheme.navy,
              onSurface: Colors.white,
            ),
          ),
          child: child!,
        );
      },
    );
    if (picked != null) {
      setState(() {
        if (isCheckIn) {
          _checkIn = picked;
          if (_checkOut != null && _checkOut!.isBefore(_checkIn!)) {
            _checkOut = _checkIn!.add(const Duration(days: 1));
          }
        } else {
          _checkOut = picked;
        }
      });
    }
  }

  String _formatStatusLabel(String status) {
    switch (status) {
      case 'booked': return 'Booked';
      case 'pickup_to_hotel': return 'Pickup to Hotel';
      case 'in_hotel': return 'In Hotel';
      case 'pickup_to_plane': return 'Pickup to Plane';
      default: return status;
    }
  }

  @override
  Widget build(BuildContext context) {
    final provider = Provider.of<BookingProvider>(context);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Create Booking'),
      ),
      body: provider.isLoading && provider.crews.isEmpty
          ? const Center(child: CircularProgressIndicator(color: AppTheme.accent))
          : SingleChildScrollView(
              padding: const EdgeInsets.all(20),
              child: Form(
                key: _formKey,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    _buildDropdownField<Crew>(
                      value: _selectedCrew,
                      items: provider.crews,
                      label: 'Crew Member',
                      hint: 'Select Crew Member',
                      itemLabel: (c) => c.fullName,
                      onChanged: (val) => setState(() => _selectedCrew = val),
                    ),
                    const SizedBox(height: 16),
                    _buildDropdownField<Company>(
                      value: _selectedCompany,
                      items: provider.companies,
                      label: 'Company',
                      hint: 'Select Company',
                      itemLabel: (c) => '${c.companyName} (${c.shipName})',
                      onChanged: (val) => setState(() => _selectedCompany = val),
                    ),
                    const SizedBox(height: 16),
                    _buildDropdownField<Hotel>(
                      value: _selectedHotel,
                      items: provider.hotels,
                      label: 'Hotel',
                      hint: 'Select Hotel',
                      itemLabel: (h) => h.hotelName,
                      onChanged: (val) => setState(() => _selectedHotel = val),
                    ),
                    const SizedBox(height: 16),
                    TextFormField(
                      controller: _crewTitleController,
                      decoration: _inputDecoration('Crew Title', 'e.g. Captain, Engineer'),
                      validator: (val) => val == null || val.isEmpty ? 'Required' : null,
                    ),
                    const SizedBox(height: 16),
                    Row(
                      children: [
                        Expanded(
                          child: InkWell(
                            onTap: () => _selectDate(context, true),
                            child: InputDecorator(
                              decoration: _inputDecoration('Check In', ''),
                              child: Text(
                                _checkIn == null ? 'Select Date' : DateFormat('dd MMM yyyy').format(_checkIn!),
                                style: TextStyle(color: _checkIn == null ? Colors.white38 : Colors.white),
                              ),
                            ),
                          ),
                        ),
                        const SizedBox(width: 16),
                        Expanded(
                          child: InkWell(
                            onTap: () => _selectDate(context, false),
                            child: InputDecorator(
                              decoration: _inputDecoration('Check Out', ''),
                              child: Text(
                                _checkOut == null ? 'Select Date' : DateFormat('dd MMM yyyy').format(_checkOut!),
                                style: TextStyle(color: _checkOut == null ? Colors.white38 : Colors.white),
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 16),
                    TextFormField(
                      controller: _invoiceNumberController,
                      decoration: _inputDecoration('Invoice Number (Optional)', ''),
                    ),
                    const SizedBox(height: 16),
                    TextFormField(
                      controller: _remarksController,
                      decoration: _inputDecoration('Remarks (Optional)', ''),
                      maxLines: 3,
                    ),
                    const SizedBox(height: 16),
                    _buildDropdownField<String>(
                      value: _selectedStatus,
                      items: _statusOptions,
                      label: 'Initial Status',
                      hint: 'Select Status',
                      itemLabel: _formatStatusLabel,
                      onChanged: (val) => setState(() => _selectedStatus = val),
                    ),
                    const SizedBox(height: 32),
                    ElevatedButton(
                      style: ElevatedButton.styleFrom(
                        backgroundColor: AppTheme.accent,
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      ),
                      onPressed: provider.isLoading ? null : _submitForm,
                      child: provider.isLoading
                          ? const SizedBox(height: 20, width: 20, child: CircularProgressIndicator(strokeWidth: 2, valueColor: AlwaysStoppedAnimation<Color>(Colors.white)))
                          : const Text('Create Booking', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.white)),
                    ),
                  ],
                ),
              ),
            ),
    );
  }

  InputDecoration _inputDecoration(String label, String hint) {
    return InputDecoration(
      labelText: label,
      hintText: hint,
      filled: true,
      fillColor: Colors.white.withOpacity(0.05),
      border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
      enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
      focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: AppTheme.accent, width: 1)),
    );
  }

  Widget _buildDropdownField<T>({
    required T? value,
    required List<T> items,
    required String label,
    required String hint,
    required String Function(T) itemLabel,
    required void Function(T?) onChanged,
  }) {
    return DropdownButtonFormField<T>(
      value: value,
      decoration: _inputDecoration(label, hint),
      dropdownColor: AppTheme.navyLighter,
      items: items.map((item) {
        return DropdownMenuItem<T>(
          value: item,
          child: Text(itemLabel(item)),
        );
      }).toList(),
      onChanged: onChanged,
      validator: (val) => val == null ? 'Required' : null,
    );
  }
}
