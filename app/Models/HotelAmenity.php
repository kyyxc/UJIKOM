<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class HotelAmenity extends Model
{
    /** @use HasFactory<\Database\Factories\HotelAmenityFactory> */
    use HasFactory;

    protected $guarded = ['id'];
}
