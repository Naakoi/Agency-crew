<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\FcmToken;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\ApnsConfig;

class NotifyExpiringBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:expiring-bookings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send high-priority alerts for bookings expiring in 1 day (repeat 2 times every 4h)';

    protected $messaging;

    public function __construct(Messaging $messaging)
    {
        parent::__construct();
        $this->messaging = $messaging;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();
        $tomorrow = now()->addHours(24);

        // Find bookings expiring within the next 24 hours that haven't exhausted reminders
        // and haven't been notified in the last 4 hours.
        $bookings = Booking::with('crew')
            ->where('check_out', '<=', $tomorrow)
            ->where('check_out', '>', $now)
            ->where('expiry_reminder_count', '<', 3)
            ->where(function($q) {
                $q->whereNull('expiry_reminder_sent_at')
                  ->orWhere('expiry_reminder_sent_at', '<', now()->subHours(4));
            })
            ->whereNotIn('status', ['cancelled'])
            ->get();

        if ($bookings->isEmpty()) {
            $this->info('No expiring bookings found.');
            return;
        }

        $tokens = FcmToken::pluck('token')->toArray();
        if (empty($tokens)) {
            $this->warn('No FCM tokens found in the system.');
            return;
        }

        $this->info("Found {$bookings->count()} expiring bookings. Sending notifications...");

        foreach ($bookings as $booking) {
            $title = "⚠️ URGENT: Booking Expiring Soon";
            $body = "Booking for {$booking->crew->full_name} is expiring at {$booking->check_out->format('H:i')}. Please confirm checkout or extend.";

            // Use high priority for both Android and iOS
            $message = CloudMessage::new()
                ->withNotification(Notification::create($title, $body))
                ->withAndroidConfig(AndroidConfig::fromArray([
                    'priority' => 'high',
                    'notification' => [
                        'color' => '#ef4444', // Red for urgency
                        'sound' => 'default',
                    ],
                ]))
                ->withApnsConfig(ApnsConfig::fromArray([
                    'headers' => [
                        'apns-priority' => '10',
                    ],
                    'payload' => [
                        'aps' => [
                            'alert' => [
                                'title' => $title,
                                'body' => $body,
                            ],
                            'sound' => 'default',
                        ],
                    ],
                ]))
                ->withData([
                    'booking_id' => (string) $booking->id,
                    'type' => 'expiry_alert',
                    'priority' => 'high'
                ]);

            $chunks = array_chunk($tokens, 500);
            foreach ($chunks as $chunk) {
                $this->messaging->sendMulticast($message, $chunk);
            }

            // Update tracking
            $booking->expiry_reminder_sent_at = now();
            $booking->expiry_reminder_count += 1;
            $booking->save();

            $this->info("Notification sent for Booking #{$booking->id} ({$booking->crew->full_name}). Attempts: {$booking->expiry_reminder_count}");
        }
    }
}
