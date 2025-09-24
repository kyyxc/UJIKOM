<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Amenity extends Model
{
    /** @use HasFactory<\Database\Factories\AmenityFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    public function hotels(): BelongsToMany
    {
        return $this->belongsToMany(HotelAmenity::class, 'hotel_amenities', 'amenity_id', 'hotel_id');
    }

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'room_amenities', 'amenity_id', 'room_id');
    }
}
