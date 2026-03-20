<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    protected $fillable = ['hotel_name', 'location', 'contact', 'email'];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
