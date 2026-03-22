<?php

namespace App\Listeners;

use App\Events\BookingStatusChanged;
use App\Models\FcmToken;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class SendFirebasePushNotification
{
    protected $messaging;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    public function handle(BookingStatusChanged $event)
    {
        $booking = $event->booking;
        $booking->load('crew');
        
        $title = "Status Updated";
        $body = "{$booking->crew->full_name} is now: {$booking->status_label}";

        // Send to everyone except the person who made the change
        $tokens = FcmToken::where('user_id', '!=', $event->userId)->pluck('token')->toArray();
        
        if (empty($tokens)) return;

        // Firebase limits multicast to 500 tokens per call, chunking just in case.
        $chunks = array_chunk($tokens, 500);

        foreach ($chunks as $chunk) {
            $message = CloudMessage::new()
                ->withNotification(Notification::create($title, $body))
                ->withData([
                    'booking_id' => (string) $booking->id, 
                    'type' => 'status_update'
                ]);
            $this->messaging->sendMulticast($message, $chunk);
        }
    }
}
