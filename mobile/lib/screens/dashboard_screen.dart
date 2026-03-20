import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import 'package:url_launcher/url_launcher.dart';
import '../providers/booking_provider.dart';
import '../providers/auth_provider.dart';
import '../theme/theme.dart';
import '../models/models.dart';
import 'booking_detail_screen.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<BookingProvider>(context, listen: false).fetchBookings();
    });
  }

  @override
  Widget build(BuildContext context) {
    final bookingProvider = Provider.of<BookingProvider>(context);
    final stats = bookingProvider.stats;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Dashboard'),
        actions: [
          IconButton(
            icon: const Icon(Icons.logout, size: 20),
            onPressed: () => Provider.of<AuthProvider>(context, listen: false).logout(),
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: bookingProvider.fetchBookings,
        color: AppTheme.accent,
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          padding: const EdgeInsets.all(20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Stats Cards
              Row(
                children: [
                  _buildStatCard('TOTAL', stats['total'].toString(), AppTheme.accent),
                  const SizedBox(width: 12),
                  _buildStatCard('IN HOTEL', stats['in_hotel'].toString(), AppTheme.green),
                  const SizedBox(width: 12),
                  _buildStatCard('DEPARTED', stats['departed'].toString(), AppTheme.muted),
                ],
              ),
              const SizedBox(height: 32),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text('RECENT BOOKINGS', style: TextStyle(fontSize: 13, fontWeight: FontWeight.w800, color: AppTheme.muted, letterSpacing: 1.2)),
                  if (bookingProvider.isLoading)
                    const SizedBox(height: 14, width: 14, child: CircularProgressIndicator(strokeWidth: 2)),
                ],
              ),
              const SizedBox(height: 16),
              if (bookingProvider.bookings.isEmpty && !bookingProvider.isLoading)
                const Center(child: Padding(padding: EdgeInsets.all(40), child: Text('No bookings found', style: TextStyle(color: AppTheme.muted)))),
              
              ...bookingProvider.bookings.map((booking) => _buildBookingCard(booking)),
            ],
          ),
        ),
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () {
          // TODO: Open Create Booking screen
        },
        backgroundColor: AppTheme.accent,
        child: const Icon(Icons.add),
      ),
    );
  }

  Widget _buildStatCard(String label, String value, Color color) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: color.withOpacity(0.1),
          borderRadius: BorderRadius.circular(14),
          border: Border.all(color: color.withOpacity(0.3)),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(label, style: TextStyle(fontSize: 10, fontWeight: FontWeight.w700, color: color, letterSpacing: 0.5)),
            const SizedBox(height: 4),
            Text(value, style: const TextStyle(fontSize: 22, fontWeight: FontWeight.w900, color: Colors.white)),
          ],
        ),
      ),
    );
  }

  Widget _buildBookingCard(Booking booking) {
    final bool isInHotel = booking.status == 'in_hotel';
    
    return Card(
      child: InkWell(
        onTap: () {
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (context) => BookingDetailScreen(booking: booking),
            ),
          );
        },
        borderRadius: BorderRadius.circular(14),
        child: Padding(
          padding: const EdgeInsets.all(16.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Expanded(child: Text(booking.crew.fullName, style: const TextStyle(fontSize: 17, fontWeight: FontWeight.w800, color: Colors.white))),
                  _buildStatusBadge(isInHotel),
                ],
              ),
              const SizedBox(height: 4),
              Text('${booking.company.companyName} — ${booking.company.shipName}', style: const TextStyle(color: AppTheme.muted, fontSize: 13)),
              const Divider(height: 24, color: Colors.white10),
              Row(
                children: [
                  const Icon(Icons.hotel, size: 16, color: AppTheme.accentLight),
                  const SizedBox(width: 8),
                  Text(booking.hotel.hotelName, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w600)),
                ],
              ),
              const SizedBox(height: 8),
              Row(
                children: [
                  Icon(Icons.calendar_today, size: 14, color: AppTheme.muted.withOpacity(0.6)),
                  const SizedBox(width: 8),
                  Text(
                    '${DateFormat('dd MMM').format(booking.checkIn)} - ${DateFormat('dd MMM').format(booking.checkOut)}',
                    style: const TextStyle(color: AppTheme.muted, fontSize: 13),
                  ),
                ],
              ),
              if (booking.invoiceNumber != null)
                Padding(
                  padding: const EdgeInsets.only(top: 8.0),
                  child: Row(
                    children: [
                      const Icon(Icons.file_copy, size: 14, color: AppTheme.accentLight),
                      const SizedBox(width: 8),
                      const Text('Invoice: ', style: TextStyle(color: AppTheme.muted, fontSize: 13)),
                      if (booking.crew.biodataFile != null)
                        GestureDetector(
                          onTap: () async {
                            final String url = "http://10.0.2.2:8000/storage/${booking.crew.biodataFile}";
                            if (await canLaunchUrl(Uri.parse(url))) {
                              await launchUrl(Uri.parse(url), mode: LaunchMode.externalApplication);
                            }
                          },
                          child: Text(
                            '#${booking.invoiceNumber}',
                            style: const TextStyle(
                              color: AppTheme.accentLight,
                              fontSize: 13,
                              fontWeight: FontWeight.bold,
                              decoration: TextDecoration.underline,
                            ),
                          ),
                        )
                      else
                        Text('#${booking.invoiceNumber}', style: const TextStyle(color: Colors.white, fontSize: 13, fontWeight: FontWeight.bold)),
                    ],
                  ),
                ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildStatusBadge(bool inHotel) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(
        color: (inHotel ? AppTheme.green : AppTheme.muted).withOpacity(0.15),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: (inHotel ? AppTheme.green : AppTheme.muted).withOpacity(0.4)),
      ),
      child: Text(
        inHotel ? 'IN HOTEL' : 'DEPARTED',
        style: TextStyle(
          color: inHotel ? AppTheme.green : AppTheme.muted,
          fontSize: 10,
          fontWeight: FontWeight.w800,
        ),
      ),
    );
  }
}
