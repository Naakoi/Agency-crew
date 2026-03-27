<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'crew_id',
        'company_id',
        'hotel_id',
        'crew_title',
        'check_in',
        'check_out',
        'invoice_number',
        'remarks',
        'status',
        'assigned_user_id',
    ];

    protected $casts = [
        'check_in'  => 'datetime',
        'check_out' => 'datetime',
    ];

    public function crew()
    {
        return $this->belongsTo(Crew::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function statusUpdatedBy()
    {
        return $this->belongsTo(User::class, 'status_updated_by');
    }

    public function statusLogs()
    {
        return $this->hasMany(BookingStatusLog::class)->orderBy('created_at', 'desc');
    }

    public function getStatusLabelAttribute(): string
    {
        return [
            'booked'            => 'Hotel Booked',
            'pickup_to_hotel'   => 'Pickup to Hotel',
            'in_hotel'          => 'In Hotel',
            'pickup_to_ship'    => 'Pick up to Ship',
            'pickup_to_plane'   => 'Pickup to Plane',
            'cancelled'         => 'Cancelled',
        ][$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColorClassAttribute(): string
    {
        return [
            'booked'            => 'badge-blue',
            'pickup_to_hotel'   => 'badge-amber',
            'in_hotel'          => 'badge-green',
            'pickup_to_ship'    => 'badge-purple',
            'pickup_to_plane'   => 'badge-teal',
            'cancelled'         => 'badge-red',
        ][$this->status] ?? 'badge-gray';
    }
}
