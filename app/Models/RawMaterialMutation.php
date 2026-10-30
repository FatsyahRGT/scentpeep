<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RawMaterialMutation extends Model
{
    use HasFactory;

    public const TYPE_IN = 'IN';
    public const TYPE_OUT = 'OUT';

    protected $fillable = [
        'raw_material_id',
        'type',
        'qty',
        'amount',
        'supplier',
        'note',
        'created_by',
    ];

    protected $casts = [
        'qty' => 'integer',
        'amount' => 'decimal:2',
    ];

    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
