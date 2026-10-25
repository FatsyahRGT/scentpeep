<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with('stocks')->orderBy('name');

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('code', 'like', $search);
            });
        }

        if ($request->filled('status')) {
            $status = $request->status === 'active';
            $query->where('is_active', $status);
        }

        $products = $query->paginate(20)->withQueryString();

        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        return view('products.create');
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        Product::create([
            'name' => $request->name,
            'code' => $request->code,
            'cost_price' => $request->cost_price,
            'sell_price' => $request->sell_price,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : true,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Produk parfum baru berhasil ditambahkan!');
    }

    public function edit(Product $product): View
    {
        return view('products.edit', compact('product'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $product->update([
            'name' => $request->name,
            'code' => $request->code,
            'cost_price' => $request->cost_price,
            'sell_price' => $request->sell_price,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : $product->is_active,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Data produk berhasil diperbarui!');
    }

    public function toggle(Product $product): RedirectResponse
    {
        $product->update(['is_active' => ! $product->is_active]);

        $statusText = $product->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('products.index')
            ->with('success', "Status produk {$product->name} berhasil {$statusText}!");
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->stocks()->where('qty', '>', 0)->exists()) {
            return redirect()->route('products.index')
                ->with('error', 'Produk tidak dapat dihapus karena masih memiliki stok di lokasi!');
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produk parfum berhasil dihapus!');
    }
}
