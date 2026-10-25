<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Lokasi awal: RIZKY dan IWAN (tipe gudang/pemilik). TOKO GPI adalah pemasok, bukan lokasi stok.
     */
    public function run(): void
    {
        $locations = [
            [
                'name' => 'RIZKY',
                'type' => Location::TYPE_WAREHOUSE,
            ],
            [
                'name' => 'IWAN',
                'type' => Location::TYPE_WAREHOUSE,
            ],
        ];

        foreach ($locations as $loc) {
            Location::updateOrCreate(
                ['name' => $loc['name']],
                ['type' => $loc['type']]
            );
        }
    }
}
