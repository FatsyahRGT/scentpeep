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

    public const ORDER_TYPE_REGULAR = 'REGULAR';
    public const ORDER_TYPE_PREORDER = 'PREORDER';

    public const PO_STATUS_NONE = 'NONE';
    public const PO_STATUS_PENDING = 'PENDING';
    public const PO_STATUS_COMPLETED = 'COMPLETED';

    /**
     * Dapatkan daftar nama sales dinamis dari pengguna (User) yang aktif.
     * Mengambil staf & reseller lapangan aktif (mengecualikan akun admin dan akun placeholder generik).
     */
    public static function getSalesOptions(): \Illuminate\Support\Collection
    {
        $sales = User::where('is_active', true)
            ->whereIn('role', [User::ROLE_STAFF, User::ROLE_RESELLER])
            ->whereNotIn('email', ['admin@scentpeep.com', 'staff@scentpeep.com'])
            ->whereNotIn('name', ['Admin Scentpeep', 'Staff Scentpeep', 'admin', 'staff'])
            ->orderBy('name')
            ->pluck('name');

        if ($sales->isEmpty()) {
            return Location::orderBy('name')->pluck('name');
        }

        return $sales;
    }

    protected $fillable = [
        'invoice_no',
        'tanggal',
        'sales_name',
        'customer_name',
        'product_id',
        'qty',
        'location_id',
        'total_price',
        'payment_status',
        'order_type',
        'po_status',
        'po_completed_at',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'qty' => 'integer',
        'total_price' => 'decimal:2',
        'po_completed_at' => 'datetime',
    ];

    public function isPreOrder(): bool
    {
        return $this->order_type === self::ORDER_TYPE_PREORDER;
    }

    public function isPoPending(): bool
    {
        return $this->isPreOrder() && $this->po_status === self::PO_STATUS_PENDING;
    }

    public function isPoCompleted(): bool
    {
        return $this->isPreOrder() && $this->po_status === self::PO_STATUS_COMPLETED;
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
