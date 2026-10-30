<?php

use App\Models\Location;
use App\Models\Stock;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $gpi = Location::where('name', 'TOKO GPI')->first();
        if ($gpi) {
            Stock::where('location_id', $gpi->id)->delete();
            $gpi->delete();
        }
    }

    public function down(): void
    {
        Location::firstOrCreate(['name' => 'TOKO GPI'], ['type' => Location::TYPE_WAREHOUSE]);
    }
};
