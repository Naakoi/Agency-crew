<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Crew extends Model
{
    protected $fillable = [
        'full_name',
        'nationality',
        'passport_number',
        'passport_expiry_date',
        'date_of_birth',
        'photo',
        'biodata_file',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'passport_expiry_date' => 'date',
        'date_of_birth' => 'date',
    ];

    public function getIsPassportSoonExpiringAttribute(): bool
    {
        if (!$this->passport_expiry_date) return false;
        return $this->passport_expiry_date->isAfter(now()) && $this->passport_expiry_date->diffInMonths(now()) < 6;
    }

    public function getIsPassportExpiredAttribute(): bool
    {
        if (!$this->passport_expiry_date) return false;
        return $this->passport_expiry_date->isBefore(now());
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function latestBooking()
    {
        return $this->hasOne(Booking::class)->latestOfMany();
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->latestBooking ? $this->latestBooking->status_label : 'No Bookings';
    }

    public function getStatusColorClassAttribute(): string
    {
        return $this->latestBooking ? $this->latestBooking->status_color_class : 'badge-gray';
    }
}
