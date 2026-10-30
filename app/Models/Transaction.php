<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    public const TYPE_INCOME = 'INCOME';
    public const TYPE_OUTCOME = 'OUTCOME';

    public const EFFECT_NONE = 'NONE';
    public const EFFECT_IN = 'IN';
    public const EFFECT_OUT = 'OUT';

    // Standardized categories
    public const CATEGORIES_INCOME = [
        'Penjualan',
        'Pendapatan Jasa',
        'Pemasukan Lain-lain',
    ];

    public const CATEGORIES_OUTCOME = [
        'Entertain',
        'Iklan Shopee',
        'Bahan Baku',
        'Bayar Jasa',
        'Pembelian Stok',
        'Operasional',
        'Marketing',
        'Gaji & Bonus',
        'Pengeluaran Lain-lain',
    ];

    protected $fillable = [
        'tanggal',
        'type',
        'category',
        'description',
        'amount',
        'customer_name',
        'channel',
        'is_free',
        'product_id',
        'location_id',
        'qty',
        'stock_effect',
        'invoice_no',
        'note',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'amount' => 'decimal:2',
            'qty' => 'integer',
            'is_free' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
