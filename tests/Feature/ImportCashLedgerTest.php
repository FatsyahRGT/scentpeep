<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportCashLedgerTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_cash_command_dry_run_does_not_insert_to_db(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);

        // Create temporary sample CSV
        $tempPath = sys_get_temp_dir() . '/test_kas.csv';
        $csvContent = "NO,TANGGAL,KODE,NAMA TOKO,KETERANGAN,JUMLAH,SATUAN,HARGA,DEBIT,KREDIT,SALDO,CATATAN\n"
                    . "1,16/12/2024,INV-01,PENJUALAN SCENTPEEP,VANYA,1,PCS,185000,185000,0,185000,Lunas\n"
                    . "2,17/12/2024,PO-01,TOKO GPI,PEMBELIAN BIBIT,10,BTL,100000,0,1000000,0,Bahan baku\n";
        file_put_contents($tempPath, $csvContent);

        $this->artisan('scentpeep:import-cash', [
            'file' => $tempPath,
            '--dry-run' => true,
        ])
        ->assertSuccessful()
        ->expectsOutputToContain('MODE: DRY-RUN')
        ->expectsOutputToContain('Valid Siap Impor     : 2');

        // Database must remain empty in dry-run
        $this->assertEquals(0, Transaction::count());

        @unlink($tempPath);
    }

    public function test_import_cash_command_real_inserts_and_prevents_duplicate(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $tempPath = sys_get_temp_dir() . '/test_kas_real.csv';
        $csvContent = "NO,TANGGAL,KODE,NAMA TOKO,KETERANGAN,JUMLAH,SATUAN,HARGA,DEBIT,KREDIT,SALDO,CATATAN\n"
                    . "1,16/12/2024,INV-01,PENJUALAN SCENTPEEP,VANYA,1,PCS,185000,185000,0,185000,Lunas\n";
        file_put_contents($tempPath, $csvContent);

        // Run 1: insert
        $this->artisan('scentpeep:import-cash', [
            'file' => $tempPath,
        ])->assertSuccessful();

        $this->assertEquals(1, Transaction::count());
        $this->assertDatabaseHas('transactions', [
            'category' => 'Penjualan',
            'customer_name' => 'VANYA',
            'amount' => 185000,
        ]);

        // Run 2: duplicate should be skipped
        $this->artisan('scentpeep:import-cash', [
            'file' => $tempPath,
        ])
        ->assertSuccessful()
        ->expectsOutputToContain('Dilewati (Duplikat)  : 1');

        $this->assertEquals(1, Transaction::count());

        @unlink($tempPath);
    }
}
