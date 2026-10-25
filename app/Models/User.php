<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_RESELLER = 'reseller';
    public const ROLE_STAFF = 'staff';

    public const STATUS_PENDING = 'pending';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isReseller(): bool
    {
        return $this->role === self::ROLE_RESELLER;
    }

    public function isStaff(): bool
    {
        return $this->role === self::ROLE_STAFF;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_ACTIVE && $this->is_active;
    }

    public function location(): HasOne
    {
        return $this->hasOne(Location::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'created_by');
    }
}
