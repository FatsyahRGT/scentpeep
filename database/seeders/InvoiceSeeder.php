<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Location;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds with sample multi-month sales for RIZKY, IWAN, and VANNY.
     */
    public function run(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $rizky = Location::where('name', 'RIZKY')->first();
        $iwan = Location::where('name', 'IWAN')->first();
        $vanny = Location::where('name', 'VANNY')->first() ?? $rizky;

        $products = Product::where('is_active', true)->get();
        if ($products->isEmpty() || ! $rizky || ! $iwan) {
            return;
        }

        // Only seed if no invoices exist yet
        if (Invoice::count() > 0) {
            return;
        }

        $now = Carbon::now();
        $samples = [
            // Month 5 ago
            ['sales' => 'RIZKY', 'months_ago' => 5, 'qty' => 12, 'loc' => $rizky, 'cust' => 'Toko Wangi Jaya'],
            ['sales' => 'IWAN', 'months_ago' => 5, 'qty' => 10, 'loc' => $iwan, 'cust' => 'Outlet Parfum Bandung'],
            ['sales' => 'VANNY', 'months_ago' => 5, 'qty' => 6, 'loc' => $vanny, 'cust' => 'Boutique Scent Jakarta'],

            // Month 4 ago
            ['sales' => 'RIZKY', 'months_ago' => 4, 'qty' => 18, 'loc' => $rizky, 'cust' => 'Reseller Surabaya'],
            ['sales' => 'IWAN', 'months_ago' => 4, 'qty' => 14, 'loc' => $iwan, 'cust' => 'Toko Aroma Sejahtera'],
            ['sales' => 'VANNY', 'months_ago' => 4, 'qty' => 9, 'loc' => $vanny, 'cust' => 'Citra Gallery'],

            // Month 3 ago
            ['sales' => 'RIZKY', 'months_ago' => 3, 'qty' => 22, 'loc' => $rizky, 'cust' => 'Toko Wangi Jaya'],
            ['sales' => 'IWAN', 'months_ago' => 3, 'qty' => 17, 'loc' => $iwan, 'cust' => 'Outlet Parfum Bandung'],
            ['sales' => 'VANNY', 'months_ago' => 3, 'qty' => 13, 'loc' => $vanny, 'cust' => 'Rumah Parfum Medan'],

            // Month 2 ago
            ['sales' => 'RIZKY', 'months_ago' => 2, 'qty' => 28, 'loc' => $rizky, 'cust' => 'Reseller Bali'],
            ['sales' => 'IWAN', 'months_ago' => 2, 'qty' => 24, 'loc' => $iwan, 'cust' => 'Toko Aroma Sejahtera'],
            ['sales' => 'VANNY', 'months_ago' => 2, 'qty' => 18, 'loc' => $vanny, 'cust' => 'Boutique Scent Jakarta'],

            // Month 1 ago
            ['sales' => 'RIZKY', 'months_ago' => 1, 'qty' => 32, 'loc' => $rizky, 'cust' => 'Toko Wangi Jaya'],
            ['sales' => 'IWAN', 'months_ago' => 1, 'qty' => 27, 'loc' => $iwan, 'cust' => 'Outlet Parfum Bandung'],
            ['sales' => 'VANNY', 'months_ago' => 1, 'qty' => 22, 'loc' => $vanny, 'cust' => 'Citra Gallery'],

            // Current Month
            ['sales' => 'RIZKY', 'months_ago' => 0, 'qty' => 35, 'loc' => $rizky, 'cust' => 'Reseller Yogyakarta'],
            ['sales' => 'IWAN', 'months_ago' => 0, 'qty' => 31, 'loc' => $iwan, 'cust' => 'Toko Aroma Sejahtera'],
            ['sales' => 'VANNY', 'months_ago' => 0, 'qty' => 25, 'loc' => $vanny, 'cust' => 'Rumah Parfum Semarang'],
        ];

        $invCounter = 1;
        foreach ($samples as $s) {
            $prod = $products->random();
            $date = $now->copy()->subMonths($s['months_ago'])->subDays(rand(2, 20));
            $unitPrice = $prod->sell_price ?: 180000;
            $totalPrice = $unitPrice * $s['qty'];

            Invoice::create([
                'invoice_no' => sprintf('INV-%s-%03d', $date->format('ym'), $invCounter++),
                'sales_name' => $s['sales'],
                'customer_name' => $s['cust'],
                'product_id' => $prod->id,
                'qty' => $s['qty'],
                'location_id' => $s['loc']->id,
                'total_price' => $totalPrice,
                'payment_status' => 'LUNAS',
                'notes' => 'Sample initial sales data',
                'created_by' => $admin?->id,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }
    }
}
