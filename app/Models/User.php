<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = ['id'];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi ke Admin
    public function admin(): HasOne
    {
        return $this->hasOne(Admin::class);
    }

    // Relasi ke Owner
    public function owner(): HasOne
    {
        return $this->hasOne(Owner::class);
    }

    // Relasi ke Receptionist
    public function receptionist(): HasOne
    {
        return $this->hasOne(Receptionist::class);
    }

    public function getRoleAttribute()
    {
        if ($this->admin) return 'admin';
        if ($this->receptionist) return 'receptionist';
        if ($this->owner) return 'owner';
        return 'customer'; 
    }
}
