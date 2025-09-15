<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelImages extends Model
{
    /** @use HasFactory<\Database\Factories\HotelImagesFactory> */
    use HasFactory;

    protected $guarded = ['id'];
}
