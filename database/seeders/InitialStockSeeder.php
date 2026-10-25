<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class InitialStockSeeder extends Seeder
{
    /**
     * Run the database seeds with manual stock baseline:
     * Rizky total 35 pcs, Iwan total 43 pcs.
     */
    public function run(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $rizky = Location::where('name', 'RIZKY')->first();
        $iwan = Location::where('name', 'IWAN')->first();

        if (! $rizky || ! $iwan) {
            return;
        }

        // Data Stok Awal Manual dari Catatan Lapangan
        $stockTable = [
            'eros'        => ['rizky' => 1, 'iwan' => 3],
            '1mill'       => ['rizky' => 0, 'iwan' => 1],
            'creed'       => ['rizky' => 1, 'iwan' => 0],
            'd&g'         => ['rizky' => 3, 'iwan' => 3],
            'swy'         => ['rizky' => 0, 'iwan' => 1],
            'scandal'     => ['rizky' => 2, 'iwan' => 0],
            'libre'       => ['rizky' => 0, 'iwan' => 2],
            'y'           => ['rizky' => 5, 'iwan' => 1],
            'scandalous'  => ['rizky' => 2, 'iwan' => 1],
            'kilian'      => ['rizky' => 1, 'iwan' => 1],
            'lv ombre'    => ['rizky' => 1, 'iwan' => 4],
            'hermes'      => ['rizky' => 3, 'iwan' => 2],
            'zwitsal'     => ['rizky' => 2, 'iwan' => 2],
            'aqua'        => ['rizky' => 0, 'iwan' => 0],
            'rose vanila' => ['rizky' => 0, 'iwan' => 3],
            'hawas'       => ['rizky' => 4, 'iwan' => 3],
            'annasui'     => ['rizky' => 2, 'iwan' => 1],
            'dyptique'    => ['rizky' => 1, 'iwan' => 2],
            'tomford'     => ['rizky' => 1, 'iwan' => 2],
            'bloom'       => ['rizky' => 0, 'iwan' => 2],
            'dior'        => ['rizky' => 3, 'iwan' => 3],
            'erba'        => ['rizky' => 2, 'iwan' => 0],
            'english'     => ['rizky' => 0, 'iwan' => 3],
            'baccarat'    => ['rizky' => 1, 'iwan' => 1],
            'escada'      => ['rizky' => 0, 'iwan' => 2],
        ];

        foreach ($stockTable as $code => $counts) {
            $product = Product::where('code', $code)->first();
            if (! $product) {
                continue;
            }

            Stock::updateOrCreate(
                ['location_id' => $rizky->id, 'product_id' => $product->id],
                ['qty' => $counts['rizky']]
            );

            Stock::updateOrCreate(
                ['location_id' => $iwan->id, 'product_id' => $product->id],
                ['qty' => $counts['iwan']]
            );
        }
    }
}
