import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../providers/booking_provider.dart';
import '../models/models.dart';
import '../theme/theme.dart';

class BookingDetailScreen extends StatelessWidget {
  final Booking booking;

  const BookingDetailScreen({super.key, required this.booking});

  @override
  Widget build(BuildContext context) {

    return Scaffold(
      appBar: AppBar(
        title: const Text('Booking Details'),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Crew Header
            Row(
              children: [
                CircleAvatar(
                  radius: 30,
                  backgroundColor: AppTheme.accent,
                  child: Text(booking.crew.fullName[0].toUpperCase(), style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold, color: Colors.white)),
                ),
                const SizedBox(width: 16),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(booking.crew.fullName, style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w900, color: Colors.white)),
                      Row(
                        children: [
                          Text(booking.crewTitle, style: const TextStyle(color: AppTheme.muted, fontSize: 13)),
                          if (booking.crew.passportNumber != null) ...[
                            const Text(' • ', style: TextStyle(color: AppTheme.muted)),
                            Text(booking.crew.passportNumber!, style: const TextStyle(color: AppTheme.muted, fontSize: 13)),
                          ],
                        ],
                      ),
                      if (booking.crew.passportExpiryDate != null)
                        Padding(
                          padding: const EdgeInsets.only(top: 4),
                          child: Row(
                            children: [
                              Icon(
                                Icons.calendar_today, 
                                size: 12, 
                                color: booking.crew.isPassportSoonExpiring ? Colors.orange : (booking.crew.isPassportExpired ? Colors.red : AppTheme.muted)
                              ),
                              const SizedBox(width: 4),
                              Text(
                                'Passport Expiry: ${DateFormat('dd MMM yyyy').format(booking.crew.passportExpiryDate!)}',
                                style: TextStyle(
                                  fontSize: 12,
                                  color: booking.crew.isPassportSoonExpiring ? Colors.orange : (booking.crew.isPassportExpired ? Colors.red : AppTheme.muted),
                                  fontWeight: (booking.crew.isPassportSoonExpiring || booking.crew.isPassportExpired) ? FontWeight.bold : FontWeight.normal,
                                ),
                              ),
                              if (booking.crew.isPassportSoonExpiring)
                                const Padding(
                                  padding: EdgeInsets.only(left: 4),
                                  child: Icon(Icons.warning_amber_rounded, size: 14, color: Colors.orange),
                                ),
                              if (booking.crew.isPassportExpired)
                                const Padding(
                                  padding: EdgeInsets.only(left: 4),
                                  child: Icon(Icons.error_outline, size: 14, color: Colors.red),
                                ),
                            ],
                          ),
                        ),
                    ],
                  ),
                ),
              ],
            ),
            const SizedBox(height: 32),
            
            _buildSection(
              'ACCOMMODATION',
              [
                _buildInfoRow(Icons.hotel, 'Hotel', booking.hotel.hotelName),
                _buildInfoRow(Icons.map, 'Location', booking.hotel.location ?? 'N/A'),
                _buildInfoRow(Icons.tag, 'Invoice', booking.invoiceNumber ?? 'N/A'),
                _buildInfoRow(
                  Icons.sync, 
                  'Status', 
                  booking.status.toUpperCase().replaceAll('_', ' '),
                  valueColor: booking.status == 'in_hotel' ? AppTheme.green : (booking.status == 'cancelled' ? AppTheme.red : AppTheme.accent),
                ),
              ],
            ),
            
            if (booking.statusLogs.isNotEmpty) ...[
              const SizedBox(height: 24),
              _buildSection(
                'STATUS TRACKING',
                booking.statusLogs.map((log) => Padding(
                  padding: const EdgeInsets.only(bottom: 8),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(log.statusLabel, style: const TextStyle(color: Colors.white, fontSize: 13, fontWeight: FontWeight.bold)),
                      Text('${log.userName ?? 'System'} • ${DateFormat('HH:mm').format(log.createdAt)}', style: const TextStyle(color: AppTheme.muted, fontSize: 11)),
                    ],
                  ),
                )).toList(),
              ),
            ],
            
            const SizedBox(height: 24),
            
            _buildSection(
              'STAY DATES',
              [
                _buildInfoRow(Icons.login, 'Check In', DateFormat('EEEE, dd MMM yyyy').format(booking.checkIn)),
                _buildInfoRow(Icons.logout, 'Check Out', DateFormat('EEEE, dd MMM yyyy').format(booking.checkOut)),
              ],
            ),
            
            const SizedBox(height: 24),
            
            _buildSection(
              'COMPANY & VESSEL',
              [
                _buildInfoRow(Icons.business, 'Company', booking.company.companyName),
                _buildInfoRow(Icons.directions_boat, 'Vessel', booking.company.shipName),
              ],
            ),
            
            const SizedBox(height: 24),
            
            if (booking.remarks != null && booking.remarks!.isNotEmpty)
            _buildSection(
              'REMARKS',
              [
                Text(booking.remarks!, style: const TextStyle(color: AppTheme.textBody, fontSize: 14, height: 1.5)),
              ],
            ),
            
            const SizedBox(height: 48),
            
            SizedBox(
              width: double.infinity,
              child: OutlinedButton.icon(
                onPressed: () {
                  showDialog(
                    context: context,
                    builder: (context) => AlertDialog(
                      backgroundColor: AppTheme.navyLighter,
                      title: const Text('Status Terms', style: TextStyle(color: Colors.white)),
                      content: const Text(
                        '• Hotel Booked: Accommodation confirmed.\n'
                        '• Pickup to Hotel: Crew in transit to hotel.\n'
                        '• In Hotel: Crew currently at hotel.\n'
                        '• Pick up to Ship: Crew in transit to ship.\n'
                        '• Pickup to Plane: Crew in transit to airport.\n'
                        '• Cancelled: Booking was cancelled.',
                        style: TextStyle(color: AppTheme.textBody),
                      ),
                      actions: [
                        TextButton(onPressed: () => Navigator.pop(context), child: const Text('OK'))
                      ],
                    ),
                  );
                },
                icon: const Icon(Icons.info_outline, size: 18),
                label: const Text('EXPLAIN STATUS TERMS'),
                style: OutlinedButton.styleFrom(foregroundColor: AppTheme.muted),
              ),
            ),
            const SizedBox(height: 12),
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: () {
                  _showStatusSelection(context);
                },
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppTheme.accent,
                ),
                child: const Text('CHANGE STATUS'),
              ),
            ),
          ],
        ),
      ),
    );
  }

  void _showStatusSelection(BuildContext context) {
    showModalBottomSheet(
      context: context,
      backgroundColor: AppTheme.navyLighter,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (context) => Container(
        padding: const EdgeInsets.symmetric(vertical: 20),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Padding(
              padding: EdgeInsets.only(bottom: 12),
              child: Text('Select Status', style: TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.bold)),
            ),
            _statusItem(context, 'booked', 'Hotel Booked', Icons.bookmark),
            _statusItem(context, 'pickup_to_hotel', 'Pickup to Hotel', Icons.airport_shuttle),
            _statusItem(context, 'in_hotel', 'In Hotel', Icons.hotel),
            _statusItem(context, 'pickup_to_ship', 'Pick up to Ship', Icons.directions_boat),
            _statusItem(context, 'pickup_to_plane', 'Pickup to Plane', Icons.airplane_ticket),
            _statusItem(context, 'cancelled', 'Cancelled', Icons.cancel),
          ],
        ),
      ),
    );
  }

  Widget _statusItem(BuildContext context, String value, String label, IconData icon) {
    final isSelected = booking.status == value;
    return ListTile(
      leading: Icon(icon, color: isSelected ? AppTheme.accent : AppTheme.muted),
      title: Text(label, style: TextStyle(color: isSelected ? Colors.white : AppTheme.textBody, fontWeight: isSelected ? FontWeight.bold : FontWeight.normal)),
      trailing: isSelected ? const Icon(Icons.check, color: AppTheme.accent) : null,
      onTap: () async {
        Navigator.pop(context);
        final provider = Provider.of<BookingProvider>(context, listen: false);
        final success = await provider.updateStatus(booking, value);
        if (success && context.mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text('Status updated to: $label')),
          );
          Navigator.pop(context); // Close detail screen as it's updated or just let it refresh
        }
      },
    );
  }

  Widget _buildSection(String title, List<Widget> children) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(title, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w800, color: AppTheme.accentLight, letterSpacing: 1.5)),
        const SizedBox(height: 12),
        Container(
          padding: const EdgeInsets.all(20),
          decoration: BoxDecoration(
            color: Colors.white.withOpacity(0.04),
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: Colors.white.withOpacity(0.05)),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: children,
          ),
        ),
      ],
    );
  }

  Widget _buildInfoRow(IconData icon, String label, String value, {Color? valueColor}) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Row(
        children: [
          Icon(icon, size: 18, color: AppTheme.muted),
          const SizedBox(width: 12),
          Text(label, style: const TextStyle(color: AppTheme.muted, fontSize: 13)),
          const Spacer(),
          Text(value, style: TextStyle(color: valueColor ?? Colors.white, fontSize: 14, fontWeight: FontWeight.bold)),
        ],
      ),
    );
  }
}
