<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'receptionist_id',
        'room_id',
        'hotel_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'check_in_date',
        'check_out_date',
        'status',
        'source',
        'total_price',
    ];

    protected $casts = [
        'total_price' => 'float',
    ];


    // Relasi ke User (Online Booking)
    public function guest()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke Receptionist (Offline Booking)
    public function receptionist()
    {
        return $this->belongsTo(Receptionist::class);
    }

    // Relasi ke Room
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    // Relasi ke Hotel
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'booking_id');
    }


    public function invoice()
    {
        return $this->HasOne(Invoice::class, 'booking_id');
    }
}
