<?php

namespace Database\Seeders;

use App\Models\RawMaterial;
use Illuminate\Database\Seeder;

class RawMaterialSeeder extends Seeder
{
    public function run(): void
    {
        $materials = [
            ['name' => 'Box Packaging Scentpeep', 'category' => 'BOX', 'stock_qty' => 150, 'min_stock' => 30],
            ['name' => 'Botol Kaca Parfum 30ml', 'category' => 'BOTOL', 'stock_qty' => 120, 'min_stock' => 25],
            ['name' => 'Botol Kaca Parfum 50ml', 'category' => 'BOTOL', 'stock_qty' => 80, 'min_stock' => 20],
        ];

        foreach ($materials as $m) {
            RawMaterial::updateOrCreate(
                ['name' => $m['name']],
                ['category' => $m['category'], 'stock_qty' => $m['stock_qty'], 'min_stock' => $m['min_stock']]
            );
        }
    }
}
