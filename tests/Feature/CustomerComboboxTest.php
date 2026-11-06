<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Location;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerComboboxTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;
    protected Product $product;
    protected Location $location;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        $this->staff = User::factory()->create([
            'role' => User::ROLE_STAFF,
            'is_active' => true,
        ]);

        $this->location = Location::create([
            'name' => 'IWAN',
            'type' => 'INTERNAL',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'name' => 'Dior Sauvage',
            'code' => 'DS-01',
            'buy_price' => 100000,
            'sell_price' => 185000,
            'is_active' => true,
        ]);

        Stock::create([
            'product_id' => $this->product->id,
            'location_id' => $this->location->id,
            'qty' => 50,
        ]);
    }

    public function test_get_customers_json_returns_alphabetically_ordered_list(): void
    {
        Customer::query()->delete();

        Customer::create(['name' => 'Yanto Padmanagara']);
        Customer::create(['name' => 'Andi Wijaya']);
        Customer::create(['name' => 'Egi']);

        $response = $this->actingAs($this->admin)->getJson(route('customers.json'));

        $response->assertOk()
            ->assertJsonCount(3)
            ->assertJsonPath('0.name', 'Andi Wijaya')
            ->assertJsonPath('1.name', 'Egi')
            ->assertJsonPath('2.name', 'Yanto Padmanagara');
    }

    public function test_post_customer_creates_new_regular_customer_via_ajax(): void
    {
        $response = $this->actingAs($this->admin)->postJson(route('customers.store'), [
            'name' => 'Budi Parfum',
        ]);

        $response->assertCreated()
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Budi Parfum',
                ],
            ]);

        $this->assertDatabaseHas('customers', [
            'name' => 'Budi Parfum',
        ]);
    }

    public function test_post_customer_rejects_duplicate_name(): void
    {
        Customer::create(['name' => 'Eda']);

        $response = $this->actingAs($this->admin)->postJson(route('customers.store'), [
            'name' => 'Eda',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_put_customer_updates_name_via_ajax(): void
    {
        $customer = Customer::create(['name' => 'Ega']);

        $response = $this->actingAs($this->admin)->putJson(route('customers.update', $customer), [
            'name' => 'Ega Updated',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $customer->id,
                    'name' => 'Ega Updated',
                ],
            ]);

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Ega Updated',
        ]);
    }

    public function test_delete_customer_removes_regular_customer_via_ajax(): void
    {
        $customer = Customer::create(['name' => 'Pelanggan Hapus']);

        $response = $this->actingAs($this->admin)->deleteJson(route('customers.destroy', $customer));

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('customers', [
            'id' => $customer->id,
        ]);
    }

    public function test_store_invoice_with_regular_customer_and_custom_customer(): void
    {
        $regularCustomer = Customer::create(['name' => 'Toko Parfum Berkah']);

        // 1. Submit invoice dengan Pelanggan Tetap (customer_id terisi)
        $response1 = $this->actingAs($this->admin)->post(route('invoices.store'), [
            'invoice_no' => 'INV-REG-01',
            'sales_name' => 'RIZKY',
            'customer_name' => 'Toko Parfum Berkah',
            'customer_id' => $regularCustomer->id,
            'product_id' => $this->product->id,
            'qty' => 2,
            'location_id' => $this->location->id,
            'total_price' => 370000,
            'payment_status' => 'LUNAS',
        ]);

        $response1->assertRedirect(route('invoices.index'));
        $this->assertDatabaseHas('invoices', [
            'invoice_no' => 'INV-REG-01',
            'customer_name' => 'Toko Parfum Berkah',
            'qty' => 2,
        ]);

        // 2. Submit invoice dengan Customer Custom Sekali Beli (customer_id null)
        $response2 = $this->actingAs($this->admin)->post(route('invoices.store'), [
            'invoice_no' => 'INV-CUSTOM-02',
            'sales_name' => 'IWAN',
            'customer_name' => 'Pembeli Mampir Sekali',
            'customer_id' => null,
            'product_id' => $this->product->id,
            'qty' => 1,
            'location_id' => $this->location->id,
            'total_price' => 185000,
            'payment_status' => 'BELUM LUNAS',
        ]);

        $response2->assertRedirect(route('invoices.index'));
        $this->assertDatabaseHas('invoices', [
            'invoice_no' => 'INV-CUSTOM-02',
            'customer_name' => 'Pembeli Mampir Sekali',
            'qty' => 1,
        ]);
    }

    public function test_invoice_create_and_edit_views_render_customer_combobox(): void
    {
        $customer = Customer::create(['name' => 'Eda']);

        $invoice = Invoice::create([
            'invoice_no' => 'INV-VIEW-01',
            'tanggal' => now()->toDateString(),
            'sales_name' => 'RIZKY',
            'customer_name' => 'Eda',
            'product_id' => $this->product->id,
            'qty' => 1,
            'location_id' => $this->location->id,
            'total_price' => 185000,
            'payment_status' => 'LUNAS',
            'created_by' => $this->admin->id,
        ]);

        $createRes = $this->actingAs($this->admin)->get(route('invoices.create'));
        $createRes->assertOk();
        $createRes->assertSee('id="customerCombobox"', false);
        $createRes->assertSee('id="customerInput"', false);
        $createRes->assertSee('id="btnSaveRegular"', false);

        $editRes = $this->actingAs($this->admin)->get(route('invoices.edit', $invoice));
        $editRes->assertOk();
        $editRes->assertSee('id="customerCombobox"', false);
        $editRes->assertSee('value="Eda"', false);
    }
}
