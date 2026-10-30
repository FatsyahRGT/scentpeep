<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use App\Models\RawMaterialCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RawMaterialCategoryController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:raw_material_categories,name',
            'description' => 'nullable|string|max:255',
        ]);

        $validated['name'] = trim(strtoupper($validated['name']));

        RawMaterialCategory::create($validated);

        return redirect()->route('raw-materials.index')
            ->with('success', "Kategori material '{$validated['name']}' berhasil ditambahkan.");
    }

    public function update(Request $request, RawMaterialCategory $rawMaterialCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('raw_material_categories', 'name')->ignore($rawMaterialCategory->id),
            ],
            'description' => 'nullable|string|max:255',
        ]);

        $oldName = $rawMaterialCategory->name;
        $newName = trim(strtoupper($validated['name']));
        $validated['name'] = $newName;

        $rawMaterialCategory->update($validated);

        // Sinkronkan nama kategori di tabel raw_materials terkait
        RawMaterial::where('category_id', $rawMaterialCategory->id)
            ->orWhere('category', $oldName)
            ->update([
                'category' => $newName,
                'category_id' => $rawMaterialCategory->id,
            ]);

        return redirect()->route('raw-materials.index')
            ->with('success', "Kategori material '{$newName}' berhasil diperbarui.");
    }

    public function destroy(RawMaterialCategory $rawMaterialCategory): RedirectResponse
    {
        $associatedMaterialsCount = RawMaterial::where('category_id', $rawMaterialCategory->id)
            ->orWhere('category', $rawMaterialCategory->name)
            ->count();

        if ($associatedMaterialsCount > 0) {
            return redirect()->route('raw-materials.index')
                ->with('error', "Kategori '{$rawMaterialCategory->name}' tidak dapat dihapus karena masih digunakan oleh {$associatedMaterialsCount} item bahan mentah.");
        }

        $categoryName = $rawMaterialCategory->name;
        $rawMaterialCategory->delete();

        return redirect()->route('raw-materials.index')
            ->with('success', "Kategori material '{$categoryName}' berhasil dihapus.");
    }
}
