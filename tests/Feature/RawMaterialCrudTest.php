<?php

namespace Tests\Feature;

use App\Models\RawMaterial;
use App\Models\RawMaterialCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RawMaterialCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected RawMaterialCategory $boxCat;
    protected RawMaterialCategory $botolCat;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->boxCat = RawMaterialCategory::firstOrCreate(
            ['name' => 'BOX'],
            ['description' => 'Packaging box dus kemasan parfum']
        );

        $this->botolCat = RawMaterialCategory::firstOrCreate(
            ['name' => 'BOTOL'],
            ['description' => 'Botol kaca parfum']
        );
    }

    public function test_can_view_raw_materials_index_page(): void
    {
        RawMaterial::create([
            'name' => 'Botol Spray 30ml Frosted',
            'category_id' => $this->botolCat->id,
            'category' => $this->botolCat->name,
            'stock_qty' => 50,
            'min_stock' => 10,
        ]);

        $response = $this->actingAs($this->admin)->get(route('raw-materials.index'));

        $response->assertOk();
        $response->assertSee('Status Persediaan Bahan Mentah');
        $response->assertSee('Botol Spray 30ml Frosted');
        $response->assertSee('BOTOL');
    }

    public function test_can_create_category(): void
    {
        $response = $this->actingAs($this->admin)->post(route('raw-materials.categories.store'), [
            'name' => 'stiker segel',
            'description' => 'Stiker hologram segel box parfum',
        ]);

        $response->assertRedirect(route('raw-materials.index'));
        $this->assertDatabaseHas('raw_material_categories', [
            'name' => 'STIKER SEGEL',
            'description' => 'Stiker hologram segel box parfum',
        ]);
    }

    public function test_can_update_category_and_synchronizes_associated_materials(): void
    {
        $cat = RawMaterialCategory::create([
            'name' => 'SEAL',
            'description' => 'Seal plastik',
        ]);

        $material = RawMaterial::create([
            'name' => 'Plastik Seal Heatshrink',
            'category_id' => $cat->id,
            'category' => 'SEAL',
            'stock_qty' => 100,
            'min_stock' => 20,
        ]);

        $response = $this->actingAs($this->admin)->put(route('raw-materials.categories.update', $cat), [
            'name' => 'SHRINK SEAL',
            'description' => 'Plastik segel pres panas',
        ]);

        $response->assertRedirect(route('raw-materials.index'));
        $this->assertDatabaseHas('raw_material_categories', [
            'id' => $cat->id,
            'name' => 'SHRINK SEAL',
        ]);

        // Pastikan nama category di raw_materials ikut tersinkronisasi
        $this->assertEquals('SHRINK SEAL', $material->fresh()->category);
    }

    public function test_cannot_delete_category_that_is_currently_in_use(): void
    {
        RawMaterial::create([
            'name' => 'Box Hardcover 50ml',
            'category_id' => $this->boxCat->id,
            'category' => $this->boxCat->name,
            'stock_qty' => 25,
            'min_stock' => 5,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('raw-materials.categories.destroy', $this->boxCat));

        $response->assertRedirect(route('raw-materials.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('raw_material_categories', ['id' => $this->boxCat->id]);
    }

    public function test_can_delete_unused_category(): void
    {
        $emptyCat = RawMaterialCategory::create([
            'name' => 'RIBBON',
            'description' => 'Pita satin hiasan hampers',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('raw-materials.categories.destroy', $emptyCat));

        $response->assertRedirect(route('raw-materials.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('raw_material_categories', ['id' => $emptyCat->id]);
    }

    public function test_can_create_raw_material_with_category(): void
    {
        $response = $this->actingAs($this->admin)->post(route('raw-materials.store'), [
            'name' => 'Botol Tester 5ml Roll-On',
            'category_id' => $this->botolCat->id,
            'stock_qty' => 100,
            'min_stock' => 15,
        ]);

        $response->assertRedirect(route('raw-materials.index'));
        $this->assertDatabaseHas('raw_materials', [
            'name' => 'Botol Tester 5ml Roll-On',
            'category_id' => $this->botolCat->id,
            'category' => 'BOTOL',
            'stock_qty' => 100,
            'min_stock' => 15,
        ]);
    }

    public function test_can_update_raw_material(): void
    {
        $material = RawMaterial::create([
            'name' => 'Dus Kemasan 30ml Lama',
            'category_id' => $this->boxCat->id,
            'category' => 'BOX',
            'stock_qty' => 40,
            'min_stock' => 10,
        ]);

        $response = $this->actingAs($this->admin)->put(route('raw-materials.update', $material), [
            'name' => 'Dus Kemasan 30ml Gold Edition',
            'category_id' => $this->boxCat->id,
            'stock_qty' => 60,
            'min_stock' => 12,
        ]);

        $response->assertRedirect(route('raw-materials.index'));
        $this->assertDatabaseHas('raw_materials', [
            'id' => $material->id,
            'name' => 'Dus Kemasan 30ml Gold Edition',
            'stock_qty' => 60,
            'min_stock' => 12,
        ]);
    }

    public function test_can_delete_raw_material(): void
    {
        $material = RawMaterial::create([
            'name' => 'Bahan Uji Coba Hapus',
            'category_id' => $this->boxCat->id,
            'category' => 'BOX',
            'stock_qty' => 5,
            'min_stock' => 2,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('raw-materials.destroy', $material));

        $response->assertRedirect(route('raw-materials.index'));
        $this->assertDatabaseMissing('raw_materials', ['id' => $material->id]);
    }

    public function test_filtering_materials_by_category_and_search(): void
    {
        RawMaterial::create([
            'name' => 'Box Mewah Hitam',
            'category_id' => $this->boxCat->id,
            'category' => 'BOX',
            'stock_qty' => 3,
            'min_stock' => 10,
        ]);

        RawMaterial::create([
            'name' => 'Botol Kaca Bening',
            'category_id' => $this->botolCat->id,
            'category' => 'BOTOL',
            'stock_qty' => 50,
            'min_stock' => 10,
        ]);

        // Filter category BOX
        $responseBox = $this->actingAs($this->admin)->get(route('raw-materials.index', ['category' => 'BOX']));
        $responseBox->assertOk();
        $responseBox->assertSee('Box Mewah Hitam');
        $responseBox->assertDontSee('Botol Kaca Bening');

        // Filter status low
        $responseLow = $this->actingAs($this->admin)->get(route('raw-materials.index', ['status' => 'low']));
        $responseLow->assertOk();
        $responseLow->assertSee('Box Mewah Hitam');
        $responseLow->assertDontSee('Botol Kaca Bening');

        // Search
        $responseSearch = $this->actingAs($this->admin)->get(route('raw-materials.index', ['search' => 'Bening']));
        $responseSearch->assertOk();
        $responseSearch->assertSee('Botol Kaca Bening');
        $responseSearch->assertDontSee('Box Mewah Hitam');
    }
}
