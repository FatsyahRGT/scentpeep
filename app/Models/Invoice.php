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
