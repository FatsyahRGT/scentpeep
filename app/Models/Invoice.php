<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    public const STATUS_UNPAID = 'BELUM LUNAS';
    public const STATUS_PAID = 'LUNAS';

    /**
     * Dapatkan daftar nama sales dinamis dari pengguna (User) yang aktif.
     */
    public static function getSalesOptions(): \Illuminate\Support\Collection
    {
        return User::where('is_active', true)
            ->orderBy('name')
            ->pluck('name');
    }

    protected $fillable = [
        'invoice_no',
        'sales_name',
        'customer_name',
        'product_id',
        'qty',
        'location_id',
        'total_price',
        'payment_status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'qty' => 'integer',
        'total_price' => 'decimal:2',
    ];

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
