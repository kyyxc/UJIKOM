<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hotel extends Model
{
    /** @use HasFactory<\Database\Factories\HotelFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'address',
        'city',
        'state_province',
        'country',
        'latitude',
        'longitude',
        'email',
        'website',
        'star_rating',
        'check_in_time',
        'check_out_time',
        'cancellation_policy',
        'is_active',
    ];

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'hotel_amenities', 'hotel_id', 'amenity_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(HotelImages::class, 'hotel_id');
    }
}
