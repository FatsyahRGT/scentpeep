<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RawMaterial extends Model
{
    use HasFactory;

    public const CATEGORY_BOX = 'BOX';
    public const CATEGORY_BOTOL = 'BOTOL';
    public const CATEGORY_OTHER = 'LAINNYA';

    protected $fillable = [
        'name',
        'category',
        'category_id',
        'stock_qty',
        'min_stock',
    ];

    protected $casts = [
        'stock_qty' => 'integer',
        'min_stock' => 'integer',
    ];

    public function materialCategory(): BelongsTo
    {
        return $this->belongsTo(RawMaterialCategory::class, 'category_id');
    }

    public function mutations(): HasMany
    {
        return $this->hasMany(RawMaterialMutation::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock_qty <= $this->min_stock;
    }
}
