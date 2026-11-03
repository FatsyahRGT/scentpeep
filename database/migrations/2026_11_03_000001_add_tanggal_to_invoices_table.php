<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->date('tanggal')->nullable()->after('invoice_no')->index();
        });

        // Isi data tanggal dari created_at untuk invoice lama
        DB::table('invoices')->whereNull('tanggal')->update([
            'tanggal' => DB::raw('DATE(created_at)'),
        ]);
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('tanggal');
        });
    }
};
