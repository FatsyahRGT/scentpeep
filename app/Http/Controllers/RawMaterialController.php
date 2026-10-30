<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use App\Models\RawMaterialCategory;
use App\Models\RawMaterialMutation;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RawMaterialController extends Controller
{
    public function index(Request $request): View
    {
        $categories = RawMaterialCategory::withCount('rawMaterials')->orderBy('name')->get();

        $materialsQuery = RawMaterial::with('materialCategory');

        if ($request->filled('category')) {
            $catFilter = $request->category;
            $materialsQuery->where(function ($q) use ($catFilter) {
                $q->where('category', $catFilter)
                    ->orWhere('category_id', $catFilter);
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'low') {
                $materialsQuery->whereColumn('stock_qty', '<=', 'min_stock');
            } elseif ($request->status === 'ready') {
                $materialsQuery->whereColumn('stock_qty', '>', 'min_stock');
            }
        }

        if ($request->filled('search')) {
            $searchTerm = '%' . trim($request->search) . '%';
            $materialsQuery->where('name', 'like', $searchTerm);
        }

        $materials = $materialsQuery->orderBy('category')->orderBy('name')->get();

        $mutations = RawMaterialMutation::with(['rawMaterial', 'creator'])
            ->latest()
            ->paginate(15);

        $allMaterials = RawMaterial::all();
        $metrics = [
            'total_box_stock' => (int) RawMaterial::where('category', RawMaterial::CATEGORY_BOX)->sum('stock_qty'),
            'total_botol_stock' => (int) RawMaterial::where('category', RawMaterial::CATEGORY_BOTOL)->sum('stock_qty'),
            'low_stock_count' => $allMaterials->filter->isLowStock()->count(),
            'total_items' => $allMaterials->count(),
            'total_categories' => $categories->count(),
        ];

        return view('raw_materials.index', compact('materials', 'mutations', 'metrics', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:raw_materials,name',
            'category_id' => 'required|exists:raw_material_categories,id',
            'stock_qty' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
        ]);

        $cat = RawMaterialCategory::findOrFail($validated['category_id']);
        $validated['category'] = $cat->name;

        RawMaterial::create($validated);

        return redirect()->route('raw-materials.index')
            ->with('success', "Master bahan mentah '{$validated['name']}' berhasil ditambahkan.");
    }

    public function update(Request $request, RawMaterial $rawMaterial): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('raw_materials', 'name')->ignore($rawMaterial->id),
            ],
            'category_id' => 'required|exists:raw_material_categories,id',
            'stock_qty' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
        ]);

        $cat = RawMaterialCategory::findOrFail($validated['category_id']);
        $validated['category'] = $cat->name;

        $rawMaterial->update($validated);

        return redirect()->route('raw-materials.index')
            ->with('success', "Master bahan mentah '{$rawMaterial->name}' berhasil diperbarui.");
    }

    public function destroy(RawMaterial $rawMaterial): RedirectResponse
    {
        $name = $rawMaterial->name;
        $rawMaterial->delete();

        return redirect()->route('raw-materials.index')
            ->with('success', "Master bahan mentah '{$name}' berhasil dihapus.");
    }

    public function procure(Request $request, RawMaterial $rawMaterial): RedirectResponse
    {
        $validated = $request->validate([
            'qty' => 'required|integer|min:1',
            'amount' => 'nullable|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($rawMaterial, $validated) {
            $rawMaterial->increment('stock_qty', $validated['qty']);

            RawMaterialMutation::create([
                'raw_material_id' => $rawMaterial->id,
                'type' => RawMaterialMutation::TYPE_IN,
                'qty' => $validated['qty'],
                'amount' => $validated['amount'] ?? null,
                'supplier' => $validated['supplier'] ?? null,
                'note' => $validated['note'] ?? 'Pengadaan stok masuk',
                'created_by' => Auth::id(),
            ]);

            // Jika ada pengeluaran kas dicatat, sinkronkan ke Buku Kas (Pengeluaran Bahan Baku)
            if (! empty($validated['amount']) && $validated['amount'] > 0) {
                Transaction::create([
                    'tanggal' => now()->toDateString(),
                    'type' => Transaction::TYPE_OUTCOME,
                    'category' => 'Bahan Baku',
                    'amount' => $validated['amount'],
                    'qty' => $validated['qty'],
                    'description' => "Pengadaan {$rawMaterial->name} x {$validated['qty']} pcs" . ($validated['supplier'] ? " dari {$validated['supplier']}" : ''),
                    'stock_effect' => Transaction::EFFECT_NONE,
                    'created_by' => Auth::id(),
                ]);
            }
        });

        return redirect()->route('raw-materials.index')
            ->with('success', "Pengadaan {$validated['qty']} pcs {$rawMaterial->name} berhasil dicatat.");
    }

    public function consume(Request $request, RawMaterial $rawMaterial): RedirectResponse
    {
        $validated = $request->validate([
            'qty' => 'required|integer|min:1',
            'note' => 'nullable|string|max:500',
        ]);

        if ($rawMaterial->stock_qty < $validated['qty']) {
            throw ValidationException::withMessages([
                'qty' => "Stok '{$rawMaterial->name}' tidak mencukupi untuk pemakaian! Tersedia: {$rawMaterial->stock_qty} pcs, diminta: {$validated['qty']} pcs.",
            ]);
        }

        DB::transaction(function () use ($rawMaterial, $validated) {
            $rawMaterial->decrement('stock_qty', $validated['qty']);

            RawMaterialMutation::create([
                'raw_material_id' => $rawMaterial->id,
                'type' => RawMaterialMutation::TYPE_OUT,
                'qty' => $validated['qty'],
                'note' => $validated['note'] ?? 'Pemakaian produksi / packaging',
                'created_by' => Auth::id(),
            ]);
        });

        return redirect()->route('raw-materials.index')
            ->with('success', "Pemakaian {$validated['qty']} pcs {$rawMaterial->name} berhasil dicatat.");
    }
}
