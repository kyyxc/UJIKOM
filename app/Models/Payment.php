<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $guarded = ['id'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }


    public function invoice()
    {
        return $this->HasOne(Invoice::class, 'payment');
    }
}
