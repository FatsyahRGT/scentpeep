<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Location;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TransactionController extends Controller
{
    /**
     * Display a listing of transactions (Riwayat Buku Kas & Stok).
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = Transaction::with(['product', 'location', 'creator'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc');

        // Scoping for reseller: only transactions from reseller location or created by reseller
        if ($user->isReseller()) {
            $userLocId = $user->location?->id;
            $query->where(function ($q) use ($user, $userLocId) {
                $q->where('created_by', $user->id);
                if ($userLocId) {
                    $q->orWhere('location_id', $userLocId);
                }
            });
        } elseif ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', $search)
                    ->orWhere('invoice_no', 'like', $search)
                    ->orWhere('customer_name', 'like', $search)
                    ->orWhere('channel', 'like', $search)
                    ->orWhere('note', 'like', $search)
                    ->orWhereHas('product', fn ($p) => $p->where('name', 'like', $search)->orWhere('code', 'like', $search))
                    ->orWhereHas('location', fn ($l) => $l->where('name', 'like', $search));
            });
        }

        // Summary Statistics (dynamically calculated with optimized single query)
        $summaries = (clone $query)
            ->selectRaw('type, SUM(amount) as total')
            ->groupBy('type')
            ->reorder() // Remove existing ORDER BY from base query
            ->pluck('total', 'type');
        
        $totalIncome = (float) ($summaries[Transaction::TYPE_INCOME] ?? 0);
        $totalOutcome = (float) ($summaries[Transaction::TYPE_OUTCOME] ?? 0);
        $netSaldo = $totalIncome - $totalOutcome;

        $transactions = $query->paginate(20)->withQueryString();

        $categories = Transaction::select('category')->distinct()->pluck('category');
        $locations = $user->isReseller() ? collect([$user->location])->filter() : Location::orderBy('name')->get();

        return view('transactions.index', compact(
            'transactions',
            'totalIncome',
            'totalOutcome',
            'netSaldo',
            'categories',
            'locations'
        ));
    }

    /**
     * Show the form for creating a new transaction.
     */
    public function create(Request $request): View
    {
        $user = Auth::user();
        $mode = $user->isReseller() ? 'stock' : $request->query('mode', 'cash'); // 'cash' or 'stock'
        $products = Product::where('is_active', true)->orderBy('name')->get();

        if ($user->isReseller()) {
            $userLocation = $user->location;
            $locations = $userLocation ? Location::where('id', $userLocation->id)->get() : collect([]);
        } else {
            $locations = Location::orderBy('name')->get();
        }

        // Stocks mapping for realtime client-side calculations
        $stocks = Stock::all();
        $stocksMap = [];
        foreach ($stocks as $stk) {
            $stocksMap["{$stk->location_id}_{$stk->product_id}"] = (int) $stk->qty;
        }

        return view('transactions.create', compact('products', 'locations', 'stocksMap', 'mode'));
    }

    /**
     * Store a newly created transaction in storage with atomic direct stock adjustment.
     */
    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $user = Auth::user();

        // If reseller, enforce location_id to their assigned location
        $locationId = $request->location_id;
        if ($user->isReseller()) {
            $locationId = $user->location?->id;
            if (! $locationId) {
                return redirect()->route('dashboard')->with('error', 'Akun reseller Anda belum terhubung dengan lokasi toko inventori.');
            }
        }

        DB::transaction(function () use ($request, $user, $locationId) {
            // Apply direct stock effect if applicable
            if ($request->stock_effect !== Transaction::EFFECT_NONE) {
                $this->applyStockEffect(
                    (int) $locationId,
                    (int) $request->product_id,
                    (int) $request->qty,
                    $request->stock_effect
                );
            }

            Transaction::create([
                'tanggal' => $request->tanggal,
                'type' => $request->type,
                'category' => $request->category,
                'description' => $request->description,
                'amount' => $request->boolean('is_free') ? 0 : $request->amount,
                'customer_name' => $request->customer_name,
                'channel' => $request->channel,
                'is_free' => $request->boolean('is_free'),
                'product_id' => $request->filled('product_id') ? $request->product_id : null,
                'location_id' => $request->stock_effect !== Transaction::EFFECT_NONE ? $locationId : ($request->filled('location_id') ? $request->location_id : null),
                'qty' => $request->stock_effect !== Transaction::EFFECT_NONE ? $request->qty : ($request->filled('qty') ? $request->qty : ($request->filled('product_id') ? 1 : null)),
                'stock_effect' => $request->stock_effect,
                'invoice_no' => $request->invoice_no,
                'note' => $request->note,
                'created_by' => $user->id,
            ]);
        });

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil disimpan!');
    }

    /**
     * Show the form for editing the specified transaction.
     */
    public function edit(Transaction $transaction): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user->isReseller() && $transaction->created_by !== $user->id && $transaction->location_id !== $user->location?->id) {
            return redirect()->route('transactions.index')->with('error', 'Akses ditolak. Anda tidak berwenang mengedit transaksi ini.');
        }

        $products = Product::where('is_active', true)->orderBy('name')->get();
        if ($user->isReseller()) {
            $locations = $user->location ? Location::where('id', $user->location->id)->get() : collect([]);
        } else {
            $locations = Location::orderBy('name')->get();
        }

        $stocks = Stock::all();
        $stocksMap = [];
        foreach ($stocks as $stk) {
            $stocksMap["{$stk->location_id}_{$stk->product_id}"] = (int) $stk->qty;
        }

        return view('transactions.edit', compact('transaction', 'products', 'locations', 'stocksMap'));
    }

    /**
     * Update the specified transaction in storage with atomic reversal of old stock effect.
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        $user = Auth::user();

        if ($user->isReseller() && $transaction->created_by !== $user->id && $transaction->location_id !== $user->location?->id) {
            return redirect()->route('transactions.index')->with('error', 'Akses ditolak. Anda tidak berwenang memperbarui transaksi ini.');
        }

        $locationId = $request->location_id;
        if ($user->isReseller()) {
            $locationId = $user->location?->id ?? $transaction->location_id;
        }

        DB::transaction(function () use ($request, $transaction, $locationId) {
            // 1. Lock and validate stock before any changes
            if ($request->stock_effect === Transaction::EFFECT_OUT) {
                $currentStock = Stock::where('location_id', $locationId)
                    ->where('product_id', $request->product_id)
                    ->lockForUpdate()
                    ->first();
                
                $currentQty = $currentStock?->qty ?? 0;
                $oldQty = ($transaction->stock_effect === Transaction::EFFECT_OUT && 
                          $transaction->location_id == $locationId && 
                          $transaction->product_id == $request->product_id) 
                    ? (int) $transaction->qty 
                    : 0;
                $qtyDelta = (int) $request->qty - $oldQty;
                
                // Check if available stock (after reversing old effect) is sufficient for new qty
                if ($qtyDelta > 0 && $currentQty < $qtyDelta) {
                    $location = Location::find($locationId)?->name ?? 'Lokasi';
                    $product = Product::find($request->product_id)?->name ?? 'Produk';
                    throw ValidationException::withMessages([
                        'qty' => "Stok {$product} di {$location} tidak mencukupi untuk perubahan ini (Tersedia: {$currentQty} pcs, Perlu tambahan: {$qtyDelta} pcs).",
                    ]);
                }
            }
            
            // 2. Revert previous stock effect
            if ($transaction->stock_effect !== Transaction::EFFECT_NONE && $transaction->location_id && $transaction->product_id) {
                $this->reverseStockEffect(
                    (int) $transaction->location_id,
                    (int) $transaction->product_id,
                    (int) $transaction->qty,
                    $transaction->stock_effect
                );
            }

            // 3. Apply new stock effect
            if ($request->stock_effect !== Transaction::EFFECT_NONE) {
                $this->applyStockEffect(
                    (int) $locationId,
                    (int) $request->product_id,
                    (int) $request->qty,
                    $request->stock_effect
                );
            }

            // 4. Update transaction record
            $transaction->update([
                'tanggal' => $request->tanggal,
                'type' => $request->type,
                'category' => $request->category,
                'description' => $request->description,
                'amount' => $request->boolean('is_free') ? 0 : $request->amount,
                'customer_name' => $request->customer_name,
                'channel' => $request->channel,
                'is_free' => $request->boolean('is_free'),
                'product_id' => $request->filled('product_id') ? $request->product_id : null,
                'location_id' => $request->stock_effect !== Transaction::EFFECT_NONE ? $locationId : ($request->filled('location_id') ? $request->location_id : null),
                'qty' => $request->stock_effect !== Transaction::EFFECT_NONE ? $request->qty : ($request->filled('qty') ? $request->qty : ($request->filled('product_id') ? 1 : null)),
                'stock_effect' => $request->stock_effect,
                'invoice_no' => $request->invoice_no,
                'note' => $request->note,
            ]);
        });

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil diperbarui dan penyesuaian stok disinkronkan!');
    }

    /**
     * Remove the specified transaction from storage with atomic stock reversal.
     */
    public function destroy(Transaction $transaction): RedirectResponse
    {
        $user = Auth::user();

        if ($user->isReseller() && $transaction->created_by !== $user->id && $transaction->location_id !== $user->location?->id) {
            return redirect()->route('transactions.index')->with('error', 'Akses ditolak. Anda tidak berwenang menghapus transaksi ini.');
        }

        DB::transaction(function () use ($transaction) {
            // Revert stock effect before deleting
            if ($transaction->stock_effect !== Transaction::EFFECT_NONE && $transaction->location_id && $transaction->product_id) {
                $this->reverseStockEffect(
                    (int) $transaction->location_id,
                    (int) $transaction->product_id,
                    (int) $transaction->qty,
                    $transaction->stock_effect
                );
            }

            $transaction->delete();
        });

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil dihapus dan efek stok telah dibatalkan!');
    }

    /**
     * Apply stock effect directly to stocks table.
     */
    private function applyStockEffect(int $locationId, int $productId, int $qty, string $effect): void
    {
        $stock = Stock::firstOrCreate(
            ['location_id' => $locationId, 'product_id' => $productId],
            ['qty' => 0]
        );

        if ($effect === Transaction::EFFECT_IN) {
            $stock->increment('qty', $qty);
        } elseif ($effect === Transaction::EFFECT_OUT) {
            if ($stock->qty < $qty) {
                $location = Location::find($locationId)?->name ?? 'Lokasi';
                $product = Product::find($productId)?->name ?? 'Produk';
                throw ValidationException::withMessages([
                    'qty' => "Stok {$product} di {$location} tidak mencukupi (Stok saat ini: {$stock->qty} pcs, dibutuhkan: {$qty} pcs).",
                ]);
            }
            $stock->decrement('qty', $qty);
        }
    }

    /**
     * Reverse previously applied stock effect.
     */
    private function reverseStockEffect(int $locationId, int $productId, int $qty, string $effect): void
    {
        $stock = Stock::firstOrCreate(
            ['location_id' => $locationId, 'product_id' => $productId],
            ['qty' => 0]
        );

        if ($effect === Transaction::EFFECT_IN) {
            // Reversing an IN means decrementing - validate stock is sufficient
            if ($stock->qty < $qty) {
                $location = Location::find($locationId)?->name ?? 'Lokasi';
                $product = Product::find($productId)?->name ?? 'Produk';
                throw ValidationException::withMessages([
                    'stock' => "Tidak bisa membatalkan/mengubah transaksi ini. Stok {$product} di {$location} sudah berkurang (tersedia: {$stock->qty} pcs, perlu dikembalikan: {$qty} pcs). Stok mungkin sudah terjual atau dipindahkan.",
                ]);
            }
            $stock->decrement('qty', $qty);
        } elseif ($effect === Transaction::EFFECT_OUT) {
            // Reversing an OUT means incrementing back
            $stock->increment('qty', $qty);
        }
    }
}
