<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds with 25 real Scentpeep products and manual alias codes.
     */
    public function run(): void
    {
        $products = [
            ['code' => 'eros', 'name' => 'Versace Eros', 'cost_price' => 105000, 'sell_price' => 180000],
            ['code' => '1mill', 'name' => '1 Million Lucky', 'cost_price' => 110000, 'sell_price' => 185000],
            ['code' => 'creed', 'name' => 'Creed Aventus', 'cost_price' => 135000, 'sell_price' => 240000],
            ['code' => 'd&g', 'name' => "D&G L'Imperatrice", 'cost_price' => 110000, 'sell_price' => 185000],
            ['code' => 'swy', 'name' => 'Stronger With You', 'cost_price' => 115000, 'sell_price' => 190000],
            ['code' => 'scandal', 'name' => 'Scandal JPG', 'cost_price' => 115000, 'sell_price' => 190000],
            ['code' => 'libre', 'name' => 'YSL Libre', 'cost_price' => 125000, 'sell_price' => 210000],
            ['code' => 'y', 'name' => 'YSL Y', 'cost_price' => 120000, 'sell_price' => 205000],
            ['code' => 'scandalous', 'name' => 'VS Scandalous', 'cost_price' => 100000, 'sell_price' => 175000],
            ['code' => 'kilian', 'name' => 'Killian Angels Share', 'cost_price' => 140000, 'sell_price' => 250000],
            ['code' => 'lv ombre', 'name' => 'LV Ombre Nomade', 'cost_price' => 145000, 'sell_price' => 260000],
            ['code' => 'hermes', 'name' => 'Hermes (perlu konfirmasi)', 'cost_price' => 120000, 'sell_price' => 200000],
            ['code' => 'zwitsal', 'name' => 'Zwitsal', 'cost_price' => 85000, 'sell_price' => 150000],
            ['code' => 'aqua', 'name' => 'Aqua (perlu konfirmasi)', 'cost_price' => 100000, 'sell_price' => 175000],
            ['code' => 'rose vanila', 'name' => 'Rose Vanilla', 'cost_price' => 105000, 'sell_price' => 180000],
            ['code' => 'hawas', 'name' => 'Rasasi Hawas Ice', 'cost_price' => 115000, 'sell_price' => 195000],
            ['code' => 'annasui', 'name' => 'Anna Sui Dreams', 'cost_price' => 105000, 'sell_price' => 180000],
            ['code' => 'dyptique', 'name' => 'Diptyque (perlu konfirmasi)', 'cost_price' => 130000, 'sell_price' => 230000],
            ['code' => 'tomford', 'name' => 'Tom Ford (perlu konfirmasi)', 'cost_price' => 140000, 'sell_price' => 250000],
            ['code' => 'bloom', 'name' => 'Gucci Bloom', 'cost_price' => 115000, 'sell_price' => 195000],
            ['code' => 'dior', 'name' => 'Dior Sauvage', 'cost_price' => 120000, 'sell_price' => 205000],
            ['code' => 'erba', 'name' => 'Erba Pura', 'cost_price' => 135000, 'sell_price' => 240000],
            ['code' => 'english', 'name' => 'English (perlu konfirmasi)', 'cost_price' => 110000, 'sell_price' => 185000],
            ['code' => 'baccarat', 'name' => 'Baccarat (perlu konfirmasi)', 'cost_price' => 135000, 'sell_price' => 245000],
            ['code' => 'escada', 'name' => 'Escada (perlu konfirmasi)', 'cost_price' => 105000, 'sell_price' => 180000],
        ];

        foreach ($products as $prod) {
            Product::updateOrCreate(
                ['code' => $prod['code']],
                [
                    'name' => $prod['name'],
                    'cost_price' => $prod['cost_price'],
                    'sell_price' => $prod['sell_price'],
                    'is_active' => true,
                ]
            );
        }
    }
}
