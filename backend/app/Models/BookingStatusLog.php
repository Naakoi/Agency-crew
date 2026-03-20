<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingStatusLog extends Model
{
    protected $fillable = ['booking_id', 'status', 'user_id'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return [
            'booked'            => 'Hotel Booked',
            'pickup_to_hotel'   => 'Pickup to Hotel',
            'in_hotel'          => 'In Hotel',
            'pickup_to_plane'   => 'Pickup to Plane',
            'cancelled'         => 'Cancelled',
        ][$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return [
            'booked'            => '#1a78c2',
            'pickup_to_hotel'   => '#f59e0b',
            'in_hotel'          => '#22c55e',
            'pickup_to_plane'   => '#0cb8a8',
            'cancelled'         => '#ef4444',
        ][$this->status] ?? '#94a3b8';
    }
}
