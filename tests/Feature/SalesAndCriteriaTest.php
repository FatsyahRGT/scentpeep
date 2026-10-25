<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesAndCriteriaTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $resellerA;
    protected User $resellerB;
    protected Location $iwan;
    protected Location $rizky;
    protected Location $tokoA;
    protected Location $tokoB;
    protected Product $eros;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Admin Utama',
            'email' => 'admin@scentpeep.com',
            'role' => User::ROLE_ADMIN,
            'status' => User::STATUS_ACTIVE,
            'is_active' => true,
        ]);

        $this->resellerA = User::factory()->create([
            'name' => 'Reseller A',
            'email' => 'resellerA@test.com',
            'role' => User::ROLE_RESELLER,
            'status' => User::STATUS_ACTIVE,
            'is_active' => true,
        ]);

        $this->resellerB = User::factory()->create([
            'name' => 'Reseller B',
            'email' => 'resellerB@test.com',
            'role' => User::ROLE_RESELLER,
            'status' => User::STATUS_ACTIVE,
            'is_active' => true,
        ]);

        $this->iwan = Location::create([
            'name' => 'IWAN',
            'type' => Location::TYPE_WAREHOUSE,
        ]);

        $this->rizky = Location::create([
            'name' => 'RIZKY',
            'type' => Location::TYPE_WAREHOUSE,
        ]);

        $this->tokoA = Location::create([
            'name' => 'Toko Reseller A',
            'type' => Location::TYPE_RESELLER,
            'user_id' => $this->resellerA->id,
        ]);

        $this->tokoB = Location::create([
            'name' => 'Toko Reseller B',
            'type' => Location::TYPE_RESELLER,
            'user_id' => $this->resellerB->id,
        ]);

        $this->eros = Product::create([
            'name' => 'Versace Eros',
            'code' => 'eros',
            'cost_price' => 105000,
            'sell_price' => 180000,
            'is_active' => true,
        ]);
    }

    /**
     * Kriteria 1: Penjualan "sopyan" 1 pcs Eros dari IWAN: stok IWAN 3 menjadi 2,
     * transaksi pemasukan tercatat dengan nama pembeli.
     */
    public function test_kriteria_1_penjualan_sopyan_eros_dari_iwan(): void
    {
        Stock::create([
            'location_id' => $this->iwan->id,
            'product_id' => $this->eros->id,
            'qty' => 3,
        ]);

        $response = $this->actingAs($this->admin)->post(route('transactions.store'), [
            'tanggal' => now()->toDateString(),
            'type' => Transaction::TYPE_INCOME,
            'category' => 'Penjualan',
            'customer_name' => 'sopyan',
            'channel' => 'WhatsApp',
            'amount' => 180000,
            'location_id' => $this->iwan->id,
            'product_id' => $this->eros->id,
            'qty' => 1,
            'stock_effect' => Transaction::EFFECT_OUT,
            'note' => 'sopyan',
        ]);

        $response->assertRedirect(route('transactions.index'));

        // Stok Iwan dari 3 menjadi 2
        $this->assertEquals(2, Stock::where('location_id', $this->iwan->id)->where('product_id', $this->eros->id)->first()->qty);

        // Transaksi tercatat dengan nama pembeli
        $this->assertDatabaseHas('transactions', [
            'category' => 'Penjualan',
            'customer_name' => 'sopyan',
            'channel' => 'WhatsApp',
            'amount' => 180000,
            'location_id' => $this->iwan->id,
            'product_id' => $this->eros->id,
            'qty' => 1,
            'stock_effect' => Transaction::EFFECT_OUT,
        ]);
    }

    /**
     * Kriteria 2: Penjualan melebihi stok ditolak dengan pesan jelas; stok tidak pernah negatif.
     */
    public function test_kriteria_2_penjualan_melebihi_stok_ditolak(): void
    {
        Stock::create([
            'location_id' => $this->iwan->id,
            'product_id' => $this->eros->id,
            'qty' => 2,
        ]);

        $response = $this->actingAs($this->admin)->post(route('transactions.store'), [
            'tanggal' => now()->toDateString(),
            'type' => Transaction::TYPE_INCOME,
            'category' => 'Penjualan',
            'customer_name' => 'budi',
            'amount' => 540000,
            'location_id' => $this->iwan->id,
            'product_id' => $this->eros->id,
            'qty' => 3, // exceeds available stock 2
            'stock_effect' => Transaction::EFFECT_OUT,
        ]);

        $response->assertSessionHasErrors(['qty']);

        // Stok tetap 2, tidak pernah minus
        $this->assertEquals(2, Stock::where('location_id', $this->iwan->id)->where('product_id', $this->eros->id)->first()->qty);
    }

    /**
     * Kriteria 7: Nomor invoice ganda ditolak.
     */
    public function test_kriteria_7_duplicate_invoice_number_is_rejected(): void
    {
        Transaction::create([
            'tanggal' => now()->toDateString(),
            'type' => Transaction::TYPE_INCOME,
            'category' => 'Penjualan',
            'amount' => 180000,
            'invoice_no' => 'INV/SP/0125-060',
            'stock_effect' => Transaction::EFFECT_NONE,
        ]);

        $response = $this->actingAs($this->admin)->post(route('transactions.store'), [
            'tanggal' => now()->toDateString(),
            'type' => Transaction::TYPE_INCOME,
            'category' => 'Penjualan',
            'amount' => 180000,
            'invoice_no' => 'INV/SP/0125-060', // DUPLICATE
            'stock_effect' => Transaction::EFFECT_NONE,
        ]);

        $response->assertSessionHasErrors(['invoice_no']);
    }

    /**
     * Kriteria 5: Reseller tidak bisa melihat atau mengedit transaksi milik orang lain.
     */
    public function test_kriteria_5_reseller_cannot_access_others_transactions(): void
    {
        // Transaksi dibuat oleh Reseller B di Toko B
        $txB = Transaction::create([
            'tanggal' => now()->toDateString(),
            'type' => Transaction::TYPE_INCOME,
            'category' => 'Penjualan',
            'amount' => 200000,
            'location_id' => $this->tokoB->id,
            'created_by' => $this->resellerB->id,
            'stock_effect' => Transaction::EFFECT_NONE,
        ]);

        // Reseller A mencoba edit transaksi milik Reseller B
        $response = $this->actingAs($this->resellerA)->get(route('transactions.edit', $txB));
        $response->assertRedirect(route('transactions.index'));
        $response->assertSessionHas('error');

        // Reseller A mencoba hapus transaksi milik Reseller B
        $deleteResponse = $this->actingAs($this->resellerA)->delete(route('transactions.destroy', $txB));
        $deleteResponse->assertRedirect(route('transactions.index'));
        $deleteResponse->assertSessionHas('error');

        // Transaksi B tetap ada
        $this->assertDatabaseHas('transactions', ['id' => $txB->id]);
    }
}
