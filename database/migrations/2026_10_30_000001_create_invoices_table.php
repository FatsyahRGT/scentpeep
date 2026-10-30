<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->nullable()->index();
            $table->string('sales_name')->index(); // RIZKY, IWAN, VANNY
            $table->string('customer_name')->index();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('qty')->default(1);
            $table->foreignId('location_id')->constrained('locations')->cascadeOnDelete(); // IWAN or RIZKY
            $table->decimal('total_price', 15, 2)->default(0);
            $table->string('payment_status', 20)->default('BELUM LUNAS')->index(); // LUNAS, BELUM LUNAS
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
