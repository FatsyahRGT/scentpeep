<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raw_material_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Seed kategori standar awal
        $defaultCategories = [
            ['name' => 'BOX', 'description' => 'Kemasan dus luar & box packaging parfum'],
            ['name' => 'BOTOL', 'description' => 'Botol kaca, vial, dan wadah parfum'],
            ['name' => 'LAINNYA', 'description' => 'Bahan pendukung dan aksesoris packaging lainnya'],
        ];

        foreach ($defaultCategories as $cat) {
            DB::table('raw_material_categories')->updateOrInsert(
                ['name' => $cat['name']],
                [
                    'description' => $cat['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // Tambah kolom category_id ke raw_materials
        Schema::table('raw_materials', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('name')->constrained('raw_material_categories')->nullOnDelete();
        });

        // Sinkronisasi kategori yang sudah ada di raw_materials
        $existingMaterials = DB::table('raw_materials')->get();
        foreach ($existingMaterials as $mat) {
            if ($mat->category) {
                $catId = DB::table('raw_material_categories')->where('name', $mat->category)->value('id');
                if (! $catId) {
                    $catId = DB::table('raw_material_categories')->insertGetId([
                        'name' => strtoupper($mat->category),
                        'description' => 'Kategori bahan mentah',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                DB::table('raw_materials')->where('id', $mat->id)->update(['category_id' => $catId]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('raw_materials', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });

        Schema::dropIfExists('raw_material_categories');
    }
};
