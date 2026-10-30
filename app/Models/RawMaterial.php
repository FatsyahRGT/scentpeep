<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'stock_qty',
        'min_stock',
    ];

    protected $casts = [
        'stock_qty' => 'integer',
        'min_stock' => 'integer',
    ];

    public function mutations(): HasMany
    {
        return $this->hasMany(RawMaterialMutation::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock_qty <= $this->min_stock;
    }
}
