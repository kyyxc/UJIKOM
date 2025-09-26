<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receptionist extends Model
{
    protected $fillable = ['user_id', 'hotel_id', 'shift'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
