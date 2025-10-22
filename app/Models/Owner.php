<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Owner extends Model
{
    protected $fillable = [
        'user_id',
        'hotel_id',
        'bank_name',
        'account_number',
        'account_holder_name',
        'business_license_number',
        'business_license_file',
        'tax_id_number',
        'tax_id_file',
        'identity_card_file',
        'registration_status',
        'rejection_reason',
        'submitted_at',
        'approved_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }
}
