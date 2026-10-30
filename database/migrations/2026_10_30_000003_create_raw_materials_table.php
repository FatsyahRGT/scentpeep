<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raw_materials', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('category', 30)->default('BOX')->index(); // BOX, BOTOL, LAINNYA
            $table->integer('stock_qty')->default(0);
            $table->integer('min_stock')->default(10);
            $table->timestamps();
        });

        Schema::create('raw_material_mutations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raw_material_id')->constrained('raw_materials')->cascadeOnDelete();
            $table->string('type', 10)->index(); // IN (Pengadaan), OUT (Pemakaian)
            $table->integer('qty');
            $table->decimal('amount', 15, 2)->nullable();
            $table->string('supplier')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raw_material_mutations');
        Schema::dropIfExists('raw_materials');
    }
};
