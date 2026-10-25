<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    public const TYPE_WAREHOUSE = 'warehouse';
    public const TYPE_RESELLER = 'reseller';

    protected $fillable = [
        'name',
        'type',
        'user_id',
    ];

    public function isWarehouse(): bool
    {
        return $this->type === self::TYPE_WAREHOUSE;
    }

    public function isReseller(): bool
    {
        return $this->type === self::TYPE_RESELLER;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function outgoingTransfers(): HasMany
    {
        return $this->hasMany(StockTransfer::class, 'from_location_id');
    }

    public function incomingTransfers(): HasMany
    {
        return $this->hasMany(StockTransfer::class, 'to_location_id');
    }
}
