class Crew {
  final int id;
  final String fullName;
  final String? nationality;
  final String? passportNumber;
  final DateTime? passportExpiryDate;
  final String? photo;
  final String? biodataFile;

  Crew({
    required this.id,
    required this.fullName,
    this.nationality,
    this.passportNumber,
    this.passportExpiryDate,
    this.photo,
    this.biodataFile,
  });

  factory Crew.fromJson(Map<String, dynamic> json) {
    return Crew(
      id: json['id'],
      fullName: json['full_name'],
      nationality: json['nationality'],
      passportNumber: json['passport_number'],
      passportExpiryDate: json['passport_expiry_date'] != null ? DateTime.parse(json['passport_expiry_date']) : null,
      photo: json['photo'],
      biodataFile: json['biodata_file'],
    );
  }

  bool get isPassportSoonExpiring {
    if (passportExpiryDate == null) return false;
    final now = DateTime.now();
    final sixMonthsFromNow = now.add(const Duration(days: 180));
    return passportExpiryDate!.isAfter(now) && passportExpiryDate!.isBefore(sixMonthsFromNow);
  }

  bool get isPassportExpired {
    if (passportExpiryDate == null) return false;
    return passportExpiryDate!.isBefore(DateTime.now());
  }
}

class Company {
  final int id;
  final String companyName;
  final String shipName;

  Company({required this.id, required this.companyName, required this.shipName});

  factory Company.fromJson(Map<String, dynamic> json) {
    return Company(
      id: json['id'],
      companyName: json['company_name'],
      shipName: json['ship_name'],
    );
  }
}

class Hotel {
  final int id;
  final String hotelName;
  final String? location;

  Hotel({required this.id, required this.hotelName, this.location});

  factory Hotel.fromJson(Map<String, dynamic> json) {
    return Hotel(
      id: json['id'],
      hotelName: json['hotel_name'],
      location: json['location'],
    );
  }
}

class StatusLog {
  final String status;
  final String? userName;
  final DateTime createdAt;

  StatusLog({required this.status, this.userName, required this.createdAt});

  factory StatusLog.fromJson(Map<String, dynamic> json) {
    return StatusLog(
      status: json['status'],
      userName: json['user']?['name'],
      createdAt: DateTime.parse(json['created_at']),
    );
  }

  String get statusLabel {
    switch (status) {
      case 'in_hotel': return 'In Hotel';
      case 'pickup_to_ship': return 'Pick up to Ship';
      case 'departed': return 'Departed';
      case 'picked_up': return 'Picked Up';
      default: return status;
    }
  }
}

class Booking {
  final int id;
  final Crew crew;
  final Company company;
  final Hotel hotel;
  final String crewTitle;
  final DateTime checkIn;
  final DateTime checkOut;
  final String? invoiceNumber;
  final String? remarks;
  String status;
  final List<StatusLog> statusLogs;

  Booking({
    required this.id,
    required this.crew,
    required this.company,
    required this.hotel,
    required this.crewTitle,
    required this.checkIn,
    required this.checkOut,
    this.invoiceNumber,
    this.remarks,
    required this.status,
    required this.statusLogs,
  });

  factory Booking.fromJson(Map<String, dynamic> json) {
    var logsJson = json['status_logs'] as List? ?? [];
    return Booking(
      id: json['id'],
      crew: Crew.fromJson(json['crew']),
      company: Company.fromJson(json['company']),
      hotel: Hotel.fromJson(json['hotel']),
      crewTitle: json['crew_title'],
      checkIn: DateTime.parse(json['check_in']),
      checkOut: DateTime.parse(json['check_out']),
      invoiceNumber: json['invoice_number'],
      remarks: json['remarks'],
      status: json['status'],
      statusLogs: logsJson.map((l) => StatusLog.fromJson(l)).toList(),
    );
  }
}
