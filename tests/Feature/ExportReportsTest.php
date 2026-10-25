<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportReportsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Location $gudang;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'status' => User::STATUS_ACTIVE,
            'is_active' => true,
        ]);

        $this->gudang = Location::create([
            'name' => 'Gudang Pusat',
            'type' => Location::TYPE_WAREHOUSE,
        ]);

        $this->product = Product::create([
            'name' => 'Sauvage Test',
            'code' => 'dior',
            'cost_price' => 120000,
            'sell_price' => 200000,
            'is_active' => true,
        ]);

        Stock::create([
            'location_id' => $this->gudang->id,
            'product_id' => $this->product->id,
            'qty' => 10,
        ]);

        Transaction::create([
            'tanggal' => now()->toDateString(),
            'type' => Transaction::TYPE_INCOME,
            'category' => 'Penjualan',
            'customer_name' => 'Budi',
            'channel' => 'Shopee',
            'amount' => 200000,
            'location_id' => $this->gudang->id,
            'product_id' => $this->product->id,
            'qty' => 1,
            'stock_effect' => Transaction::EFFECT_OUT,
        ]);
    }

    /**
     * Kriteria 9: Ekspor Excel dan PDF berhasil untuk laporan transaksi dan stok.
     */
    public function test_kriteria_9_export_transactions_excel(): void
    {
        $response = $this->actingAs($this->admin)->get(route('reports.transactions.excel'));
        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
    }

    public function test_kriteria_9_export_transactions_pdf(): void
    {
        $response = $this->actingAs($this->admin)->get(route('reports.transactions.pdf'));
        $response->assertOk();
        $response->assertSee('Laporan Transaksi Kas');
        $response->assertSee('Sauvage Test');
    }

    public function test_kriteria_9_export_stocks_excel(): void
    {
        $response = $this->actingAs($this->admin)->get(route('reports.stocks.excel'));
        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
    }

    public function test_kriteria_9_export_stocks_pdf(): void
    {
        $response = $this->actingAs($this->admin)->get(route('reports.stocks.pdf'));
        $response->assertOk();
        $response->assertSee('Laporan Matriks Inventori Stok Fisik');
        $response->assertSee('Sauvage Test');
    }
}
