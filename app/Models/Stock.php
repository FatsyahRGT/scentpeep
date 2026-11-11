<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'product_id',
        'qty',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
        ];
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Total stok yang sedang terkunci / dialokasikan untuk pesanan PO yang pending.
     */
    public function getReservedPoQty(): int
    {
        return (int) Invoice::where('product_id', $this->product_id)
            ->where('location_id', $this->location_id)
            ->where('order_type', Invoice::ORDER_TYPE_PREORDER)
            ->where('po_status', Invoice::PO_STATUS_PENDING)
            ->sum('qty');
    }

    /**
     * Stok siap jual bebas di toko (Stok Fisik - Stok Terkunci PO).
     */
    public function getAvailableQty(): int
    {
        return max(0, (int) $this->qty - $this->getReservedPoQty());
    }
}
