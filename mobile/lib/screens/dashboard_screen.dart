import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import 'package:url_launcher/url_launcher.dart';
import '../providers/booking_provider.dart';
import '../providers/auth_provider.dart';
import '../theme/theme.dart';
import '../models/models.dart';
import 'booking_detail_screen.dart';
import 'create_booking_screen.dart';
import 'package:flutter_svg/flutter_svg.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  final _searchController = TextEditingController();

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
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisSize: MainAxisSize.min,
          children: [
            if (Provider.of<AuthProvider>(context).user != null)
              Padding(
                padding: const EdgeInsets.only(bottom: 4),
                child: Text(
                  'Mauri ${Provider.of<AuthProvider>(context).user!['name']}',
                  style: const TextStyle(fontSize: 14, color: AppTheme.muted, fontWeight: FontWeight.normal),
                ),
              ),
            SvgPicture.asset(
              'assets/images/logo.svg',
              height: 28, // slightly smaller to fit the text above
            ),
          ],
        ),
        centerTitle: false,
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
              // Search Bar
              TextField(
                controller: _searchController,
                decoration: InputDecoration(
                  hintText: 'Search crew or invoice...',
                  prefixIcon: const Icon(Icons.search, size: 20),
                  suffixIcon: _searchController.text.isNotEmpty 
                    ? IconButton(
                        icon: const Icon(Icons.clear, size: 18),
                        onPressed: () {
                          _searchController.clear();
                          setState(() {});
                          bookingProvider.fetchBookings(search: '');
                        },
                      )
                    : null,
                  filled: true,
                  fillColor: Colors.white.withOpacity(0.05),
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
                  contentPadding: const EdgeInsets.symmetric(vertical: 0),
                ),
                onChanged: (val) {
                  setState(() {});
                  bookingProvider.fetchBookings(search: val);
                },
              ),
              const SizedBox(height: 20),
              
              // Stats Grid
              GridView(
                gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                  crossAxisCount: 2,
                  crossAxisSpacing: 10,
                  mainAxisSpacing: 10,
                  mainAxisExtent: 70,
                ),
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                children: [
                  _buildStatCard('TOTAL', stats['total']?.toString() ?? '0', AppTheme.accent, 'total', bookingProvider),
                  _buildStatCard('BOOKED', stats['booked']?.toString() ?? '0', AppTheme.accentLight, 'booked', bookingProvider),
                  _buildStatCard('TO HOTEL', stats['pickup_to_hotel']?.toString() ?? '0', AppTheme.amber, 'pickup_to_hotel', bookingProvider),
                  _buildStatCard('IN HOTEL', stats['in_hotel']?.toString() ?? '0', AppTheme.green, 'in_hotel', bookingProvider),
                  _buildStatCard('TO PLANE', stats['pickup_to_plane']?.toString() ?? '0', AppTheme.teal, 'pickup_to_plane', bookingProvider),
                  _buildStatCard('CANCELLED', stats['cancelled']?.toString() ?? '0', AppTheme.muted, 'cancelled', bookingProvider),
                ],
              ),
              const SizedBox(height: 32),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    bookingProvider.currentStatus != null ? '${bookingProvider.currentStatus!.replaceAll('_', ' ').toUpperCase()} BOOKINGS' : 'RECENT BOOKINGS', 
                    style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w800, color: AppTheme.muted, letterSpacing: 1.2)
                  ),
                  if (bookingProvider.currentStatus != null || (bookingProvider.currentSearch?.isNotEmpty ?? false))
                    TextButton(
                      onPressed: () {
                        _searchController.clear();
                        bookingProvider.clearFilters();
                      },
                      child: const Text('Clear Filters', style: TextStyle(fontSize: 12, color: AppTheme.accentLight)),
                    ),
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
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (context) => const CreateBookingScreen(),
            ),
          );
        },
        backgroundColor: AppTheme.accent,
        child: const Icon(Icons.add),
      ),
    );
  }

  Widget _buildStatCard(String label, String value, Color color, String statusKey, BookingProvider provider) {
    final bool isActive = provider.currentStatus == statusKey || (statusKey == 'total' && provider.currentStatus == null);

    return InkWell(
      onTap: () {
        if (statusKey == 'total') {
          _searchController.clear();
          setState(() {});
          provider.clearFilters();
        } else {
          provider.fetchBookings(status: statusKey);
        }
      },
      borderRadius: BorderRadius.circular(12),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
        decoration: BoxDecoration(
          color: isActive ? color.withOpacity(0.15) : color.withOpacity(0.08),
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: isActive ? color : color.withOpacity(0.2), width: isActive ? 1.5 : 1),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Text(label, style: TextStyle(fontSize: 9, fontWeight: FontWeight.w800, color: color, letterSpacing: 0.5)),
            const SizedBox(height: 2),
            Text(value, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: Colors.white, height: 1.1)),
          ],
        ),
      ),
    );
  }

  Widget _buildBookingCard(Booking booking) {
    final String currentStatus = booking.status;
    final DateTime? lastStatusDate = booking.statusLogs.isNotEmpty ? booking.statusLogs.last.createdAt : null;
    
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
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      _buildStatusBadge(currentStatus),
                      if (lastStatusDate != null)
                        Padding(
                          padding: const EdgeInsets.only(top: 6.0),
                          child: Text(
                            DateFormat('dd MMM').format(lastStatusDate),
                            style: const TextStyle(fontSize: 9, color: AppTheme.muted, fontWeight: FontWeight.w600),
                          ),
                        ),
                    ],
                  ),
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
                            final String url = "https://agencycrew.cppl.com.ki/storage/${booking.crew.biodataFile}";
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

  Widget _buildStatusBadge(String status) {
    Color color;
    String label;
    switch (status) {
      case 'booked': color = AppTheme.accentLight; label = 'BOOKED'; break;
      case 'pickup_to_hotel': color = AppTheme.amber; label = 'TO HOTEL'; break;
      case 'in_hotel': color = AppTheme.green; label = 'IN HOTEL'; break;
      case 'pickup_to_plane': color = AppTheme.teal; label = 'TO PLANE'; break;
      case 'cancelled': color = AppTheme.muted; label = 'CANCELLED'; break;
      default: color = AppTheme.muted; label = status.toUpperCase();
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(
        color: color.withOpacity(0.15),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: color.withOpacity(0.4)),
      ),
      child: Text(
        label,
        style: TextStyle(
          color: color,
          fontSize: 10,
          fontWeight: FontWeight.w800,
        ),
      ),
    );
  }
}
