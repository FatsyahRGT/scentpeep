<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('order_type', 20)->default('REGULAR')->after('payment_status')->index();
            $table->string('po_status', 20)->default('NONE')->after('order_type')->index();
            $table->timestamp('po_completed_at')->nullable()->after('po_status');
        });

        Schema::dropIfExists('pre_orders');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['order_type', 'po_status', 'po_completed_at']);
        });

        if (!Schema::hasTable('pre_orders')) {
            Schema::create('pre_orders', function (Blueprint $table) {
                $table->id();
                $table->string('po_number')->nullable()->index();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->foreignId('location_id')->constrained('locations')->cascadeOnDelete();
                $table->integer('qty')->default(1);
                $table->string('status', 20)->default('PENDING')->index();
                $table->string('customer_name')->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
            });
        }
    }
};
