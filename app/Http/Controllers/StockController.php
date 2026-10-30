<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferStockRequest;
use App\Models\Location;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockTransfer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StockController extends Controller
{
    /**
     * Display the stock summary matrix (Produk x Lokasi).
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        if ($user->isReseller()) {
            $userLocation = $user->location;
            $locations = $userLocation ? Location::where('id', $userLocation->id)->get() : collect([]);
        } else {
            $locations = Location::orderBy('name')->get();
        }

        $productsQuery = Product::where('is_active', true)->orderBy('name');

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $productsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('code', 'like', $search);
            });
        }

        $products = $productsQuery->get();

        // Load stocks for the displayed locations
        $locationIds = $locations->pluck('id');
        $stocks = Stock::whereIn('location_id', $locationIds)->get();

        $stockMatrix = [];
        $locationTotals = [];
        $totalSystemQty = 0;

        foreach ($locations as $loc) {
            $locationTotals[$loc->id] = 0;
        }

        foreach ($stocks as $stock) {
            $stockMatrix[$stock->product_id][$stock->location_id] = (int) $stock->qty;
            if (isset($locationTotals[$stock->location_id])) {
                $locationTotals[$stock->location_id] += (int) $stock->qty;
            }
            $totalSystemQty += (int) $stock->qty;
        }

        return view('stocks.index', compact(
            'locations',
            'products',
            'stockMatrix',
            'locationTotals',
            'totalSystemQty'
        ));
    }

    /**
     * Show the form for transferring stock between locations.
     */
    public function createTransfer(Request $request): View|RedirectResponse
    {
        if (Auth::user()->isReseller()) {
            return redirect()->route('dashboard')
                ->with('error', 'Akses ditolak. Fitur transfer stok hanya dapat dilakukan oleh Administrator.');
        }

        $locations = Location::orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        $fromLocationId = $request->query('from_location_id');
        $selectedProductId = $request->query('product_id');

        $stocks = Stock::all();
        $stocksMap = [];
        foreach ($stocks as $stk) {
            $stocksMap[$stk->location_id][$stk->product_id] = (int) $stk->qty;
            $stocksMap["{$stk->location_id}_{$stk->product_id}"] = (int) $stk->qty;
        }

        return view('stocks.transfer', compact(
            'locations',
            'products',
            'fromLocationId',
            'selectedProductId',
            'stocksMap'
        ));
    }

    /**
     * Show the form for editing an existing stock transfer.
     */
    public function editTransfer(StockTransfer $stockTransfer): View|RedirectResponse
    {
        if (Auth::user()->isReseller()) {
            return redirect()->route('dashboard')
                ->with('error', 'Akses ditolak. Fitur transfer stok hanya dapat dilakukan oleh Administrator.');
        }

        $locations = Location::orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        $stocks = Stock::all();
        $stocksMap = [];
        foreach ($stocks as $stk) {
            $stocksMap[$stk->location_id][$stk->product_id] = (int) $stk->qty;
            $stocksMap["{$stk->location_id}_{$stk->product_id}"] = (int) $stk->qty;
        }

        return view('stocks.transfer_edit', compact(
            'stockTransfer',
            'locations',
            'products',
            'stocksMap'
        ));
    }

    /**
     * Update an existing stock transfer with atomic reversal and reapplication.
     */
    public function updateTransfer(TransferStockRequest $request, StockTransfer $stockTransfer): RedirectResponse
    {
        if (Auth::user()->isReseller()) {
            return redirect()->route('dashboard')
                ->with('error', 'Akses ditolak. Fitur transfer stok hanya dapat dilakukan oleh Administrator.');
        }

        DB::transaction(function () use ($request, $stockTransfer) {
            // 1. Validasi & Balikkan efek transfer lama
            $oldDestStock = Stock::where('location_id', $stockTransfer->to_location_id)
                ->where('product_id', $stockTransfer->product_id)
                ->lockForUpdate()
                ->first();

            $oldDestQty = $oldDestStock ? (int) $oldDestStock->qty : 0;
            if ($oldDestQty < $stockTransfer->qty) {
                $toLocName = $stockTransfer->toLocation?->name ?? 'Lokasi Tujuan Lama';
                $prodName = $stockTransfer->product?->name ?? 'Produk';
                throw ValidationException::withMessages([
                    'qty' => "Tidak dapat mengubah transfer: stok {$prodName} di lokasi tujuan lama ({$toLocName}) saat ini tersisa {$oldDestQty} pcs, kurang dari {$stockTransfer->qty} pcs untuk pengembalian.",
                ]);
            }

            // Kembalikan ke asal lama dan kurangi dari tujuan lama
            $oldDestStock->decrement('qty', $stockTransfer->qty);

            $oldSourceStock = Stock::firstOrCreate(
                ['location_id' => $stockTransfer->from_location_id, 'product_id' => $stockTransfer->product_id],
                ['qty' => 0]
            );
            $oldSourceStock->increment('qty', $stockTransfer->qty);

            // 2. Validasi ketersediaan stok di lokasi asal baru
            $newSourceStock = Stock::where('location_id', $request->from_location_id)
                ->where('product_id', $request->product_id)
                ->lockForUpdate()
                ->first();

            $newAvailable = $newSourceStock ? (int) $newSourceStock->qty : 0;
            if ($newAvailable < $request->qty) {
                $fromLocName = Location::find($request->from_location_id)?->name ?? 'Lokasi Asal';
                $prodName = Product::find($request->product_id)?->name ?? 'Produk';
                throw ValidationException::withMessages([
                    'qty' => "Stok {$prodName} di {$fromLocName} tidak mencukupi (Tersedia: {$newAvailable} pcs, Diminta: {$request->qty} pcs).",
                ]);
            }

            // Potong dari asal baru & tambahkan ke tujuan baru
            $newSourceStock->decrement('qty', $request->qty);

            $newDestStock = Stock::firstOrCreate(
                ['location_id' => $request->to_location_id, 'product_id' => $request->product_id],
                ['qty' => 0]
            );
            $newDestStock->increment('qty', $request->qty);

            // 3. Update record StockTransfer
            $stockTransfer->update([
                'from_location_id' => $request->from_location_id,
                'to_location_id' => $request->to_location_id,
                'product_id' => $request->product_id,
                'qty' => $request->qty,
                'notes' => $request->notes,
            ]);
        });

        return redirect()->route('stocks.transfers')
            ->with('success', "Transfer stok {$stockTransfer->transfer_no} berhasil diperbarui!");
    }

    /**
     * Delete/cancel a stock transfer and restore stock units atomically.
     */
    public function destroyTransfer(StockTransfer $stockTransfer): RedirectResponse
    {
        if (Auth::user()->isReseller()) {
            return redirect()->route('dashboard')
                ->with('error', 'Akses ditolak.');
        }

        DB::transaction(function () use ($stockTransfer) {
            $destStock = Stock::where('location_id', $stockTransfer->to_location_id)
                ->where('product_id', $stockTransfer->product_id)
                ->lockForUpdate()
                ->first();

            $destQty = $destStock ? (int) $destStock->qty : 0;
            if ($destQty < $stockTransfer->qty) {
                $toLocName = $stockTransfer->toLocation?->name ?? 'Lokasi Tujuan';
                $prodName = $stockTransfer->product?->name ?? 'Produk';
                throw ValidationException::withMessages([
                    'error' => "Transfer {$stockTransfer->transfer_no} tidak dapat dibatalkan: stok {$prodName} di lokasi tujuan ({$toLocName}) tersisa {$destQty} pcs, kurang dari kuantitas transfer ({$stockTransfer->qty} pcs) untuk dikembalikan.",
                ]);
            }

            // Kurangi dari tujuan
            $destStock->decrement('qty', $stockTransfer->qty);

            // Kembalikan ke asal
            $sourceStock = Stock::firstOrCreate(
                ['location_id' => $stockTransfer->from_location_id, 'product_id' => $stockTransfer->product_id],
                ['qty' => 0]
            );
            $sourceStock->increment('qty', $stockTransfer->qty);

            $stockTransfer->delete();
        });

        return redirect()->route('stocks.transfers')
            ->with('success', "Transfer stok {$stockTransfer->transfer_no} berhasil dibatalkan dan stok dikembalikan ke lokasi asal!");
    }

    /**
     * Process stock transfer directly inside DB::transaction with audit trail in stock_transfers table.
     */
    public function storeTransfer(TransferStockRequest $request): RedirectResponse
    {
        if (Auth::user()->isReseller()) {
            return redirect()->route('dashboard')
                ->with('error', 'Akses ditolak. Fitur transfer stok hanya dapat dilakukan oleh Administrator.');
        }

        DB::transaction(function () use ($request) {
            $sourceStock = Stock::where('location_id', $request->from_location_id)
                ->where('product_id', $request->product_id)
                ->lockForUpdate()
                ->first();

            $currentQty = $sourceStock ? (int) $sourceStock->qty : 0;
            if ($currentQty < $request->qty) {
                $locName = Location::find($request->from_location_id)?->name ?? 'Lokasi Asal';
                $prodName = Product::find($request->product_id)?->name ?? 'Produk';
                throw ValidationException::withMessages([
                    'qty' => "Stok {$prodName} di {$locName} tidak mencukupi (Tersedia: {$currentQty} pcs, Diminta: {$request->qty} pcs).",
                ]);
            }

            // 1. Deduct from source
            $sourceStock->decrement('qty', $request->qty);

            // 2. Add to destination
            $destStock = Stock::firstOrCreate(
                ['location_id' => $request->to_location_id, 'product_id' => $request->product_id],
                ['qty' => 0]
            );
            $destStock->increment('qty', $request->qty);

            // 3. Record audit trail in stock_transfers
            // Use lockForUpdate to prevent race condition on sequence number
            $transferCountToday = StockTransfer::whereDate('created_at', now()->toDateString())->lockForUpdate()->count();
            $transferNo = 'TRF/' . date('Ymd') . '/' . str_pad($transferCountToday + 1, 4, '0', STR_PAD_LEFT);

            StockTransfer::create([
                'transfer_no' => $transferNo,
                'tanggal' => now()->toDateString(),
                'from_location_id' => $request->from_location_id,
                'to_location_id' => $request->to_location_id,
                'product_id' => $request->product_id,
                'qty' => $request->qty,
                'notes' => $request->notes,
                'created_by' => Auth::id(),
            ]);
        });

        return redirect()->route('stocks.transfers')
            ->with('success', 'Transfer stok antar lokasi berhasil diproses dan dicatat!');
    }

    /**
     * Display the stock transfer audit history.
     */
    public function transfers(Request $request): View
    {
        $user = Auth::user();
        $query = StockTransfer::with(['fromLocation', 'toLocation', 'product', 'creator'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc');

        if ($user->isReseller()) {
            $userLocationId = $user->location?->id;
            $query->where(function ($q) use ($userLocationId) {
                $q->where('from_location_id', $userLocationId)
                    ->orWhere('to_location_id', $userLocationId);
            });
        }

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('transfer_no', 'like', $search)
                    ->orWhere('notes', 'like', $search)
                    ->orWhereHas('product', fn ($p) => $p->where('name', 'like', $search)->orWhere('code', 'like', $search))
                    ->orWhereHas('fromLocation', fn ($l) => $l->where('name', 'like', $search))
                    ->orWhereHas('toLocation', fn ($l) => $l->where('name', 'like', $search));
            });
        }

        $transfers = $query->paginate(20)->withQueryString();

        return view('stocks.transfers_index', compact('transfers'));
    }

    /**
     * Tambah manual stok produk parfum (sehabis produksi parfum).
     */
    public function manualRestock(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'location_id' => 'required|exists:locations,id',
            'qty' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validated) {
            $stock = Stock::firstOrCreate(
                ['product_id' => $validated['product_id'], 'location_id' => $validated['location_id']],
                ['qty' => 0]
            );
            $stock->increment('qty', $validated['qty']);
        });

        $productName = Product::find($validated['product_id'])->name ?? 'Produk';
        $locationName = Location::find($validated['location_id'])->name ?? 'Lokasi';

        return redirect()->back()->with('success', "Stok {$productName} di {$locationName} berhasil ditambah {$validated['qty']} pcs (Hasil Produksi).");
    }

    /**
     * Dashboard terpisah untuk reseller / monitoring visual sebaran stok IWAN & RIZKY, produk terlaris, dan varian ready.
     */
    public function resellerStocks(Request $request): View
    {
        $locations = Location::with(['stocks.product'])->orderBy('name')->get();
        $iwan = $locations->firstWhere('name', 'IWAN');
        $rizky = $locations->firstWhere('name', 'RIZKY');

        $query = Product::where('is_active', true)->with(['stocks.location'])->orderBy('name');

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)->orWhere('code', 'like', $search);
            });
        }

        $allProducts = $query->get();

        // 1. Data stok per produk di setiap pemegang stok
        $productStockData = $allProducts->map(function ($product) {
            $totalStock = (int) $product->stocks->sum('qty');

            return [
                'product' => $product,
                'total_stock' => $totalStock,
                'is_ready' => $totalStock > 0,
                'is_low_stock' => $totalStock > 0 && $totalStock <= 2,
                'is_out_of_stock' => $totalStock === 0,
            ];
        });

        // 2. Filter Status: 'ready' (Hanya Stok Ready), 'low' (Stok Menipis <= 2 pcs), 'empty' (Hanya Stok Habis), atau 'all'
        $filterStatus = $request->get('filter_status', 'all');
        if ($filterStatus === 'ready' || $request->boolean('ready_only')) {
            $productStockData = $productStockData->filter(fn ($item) => $item['is_ready']);
        } elseif ($filterStatus === 'low' || $filterStatus === 'menipis') {
            $productStockData = $productStockData->filter(fn ($item) => $item['is_low_stock']);
        } elseif ($filterStatus === 'empty' || $request->boolean('out_of_stock')) {
            $productStockData = $productStockData->filter(fn ($item) => $item['is_out_of_stock']);
        }

        // 3. Sebaran Stok: Siapa saja & Dimana saja
        $holdersDistribution = $locations->map(function ($loc) {
            $totalQty = (int) $loc->stocks->sum('qty');
            $readySkus = $loc->stocks->where('qty', '>', 0)->count();

            return [
                'id' => $loc->id,
                'name' => $loc->name,
                'type' => $loc->type,
                'total_qty' => $totalQty,
                'ready_skus' => $readySkus,
                'location' => $loc,
                'palette' => $loc->getColorPalette(),
            ];
        });

        // 4. Stok Parfum Paling Laris (Best Sellers dari Invoice)
        $bestSellers = \App\Models\Invoice::select(
                'product_id',
                DB::raw('SUM(qty) as total_sold'),
                DB::raw('SUM(total_price) as total_omset')
            )
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->with('product')
            ->take(5)
            ->get();

        $outOfStockProducts = $allProducts->filter(function ($p) {
            return (int) $p->stocks->sum('qty') === 0;
        })->values();

        $metrics = [
            'total_iwan_stock' => $iwan ? (int) Stock::where('location_id', $iwan->id)->sum('qty') : 0,
            'total_rizky_stock' => $rizky ? (int) Stock::where('location_id', $rizky->id)->sum('qty') : 0,
            'total_combined_stock' => Stock::sum('qty'),
            'ready_variants_count' => $allProducts->filter(function ($p) {
                return (int) $p->stocks->sum('qty') > 0;
            })->count(),
            'low_stock_count' => $allProducts->filter(function ($p) {
                $qty = (int) $p->stocks->sum('qty');
                return $qty > 0 && $qty <= 2;
            })->count(),
            'out_of_stock_count' => $outOfStockProducts->count(),
        ];

        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('stocks.reseller_dashboard', compact(
            'productStockData',
            'holdersDistribution',
            'bestSellers',
            'metrics',
            'filterStatus',
            'iwan',
            'rizky',
            'locations',
            'products',
            'outOfStockProducts'
        ));
    }

    /**
     * Dashboard publik khusus reseller (dapat diakses tanpa login).
     * Berisi: Peringatan Stok Menipis (<3 pcs), Distribusi Stok (Iwan & Rizky),
     * Varian Parfum Terlaris, Daftar Stok Ready, dan Antrean Produk PO (Stok Pending),
     * tanpa mengekspos data laporan keuangan.
     */
    public function publicResellerDashboard(Request $request): View
    {
        $locations = Location::with(['stocks.product'])->orderBy('name')->get();
        $iwan = $locations->firstWhere('name', 'IWAN');
        $rizky = $locations->firstWhere('name', 'RIZKY');

        $query = Product::where('is_active', true)->with(['stocks.location'])->orderBy('name');

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)->orWhere('code', 'like', $search);
            });
        }

        $allProducts = $query->get();

        // 1. Data Stok Parfum & Ketersediaan Ready
        $productStockData = $allProducts->map(function ($product) {
            $totalStock = (int) $product->stocks->sum('qty');

            return [
                'product' => $product,
                'total_stock' => $totalStock,
                'is_ready' => $totalStock > 0,
                'is_low_stock' => $totalStock > 0 && $totalStock <= 2,
                'is_out_of_stock' => $totalStock === 0,
            ];
        });

        // Filter tab: 'ready' (Hanya Ready), 'low' (Stok Menipis <= 2 pcs), 'empty' (Hanya Habis), 'all' (Semua)
        $filterStatus = $request->get('filter_status', 'all');
        if ($filterStatus === 'ready' || $request->boolean('ready_only')) {
            $productStockData = $productStockData->filter(fn ($item) => $item['is_ready']);
        } elseif ($filterStatus === 'low' || $filterStatus === 'menipis') {
            $productStockData = $productStockData->filter(fn ($item) => $item['is_low_stock']);
        } elseif ($filterStatus === 'empty' || $request->boolean('out_of_stock')) {
            $productStockData = $productStockData->filter(fn ($item) => $item['is_out_of_stock']);
        }

        // 2. Sebaran Stok Fisik: Siapa saja & Dimana saja
        $holdersDistribution = $locations->map(function ($loc) {
            $totalQty = (int) $loc->stocks->sum('qty');
            $readySkus = $loc->stocks->where('qty', '>', 0)->count();

            return [
                'id' => $loc->id,
                'name' => $loc->name,
                'type' => $loc->type,
                'total_qty' => $totalQty,
                'ready_skus' => $readySkus,
                'location' => $loc,
                'palette' => $loc->getColorPalette(),
            ];
        });

        // 3. Peringatan Stok Menipis (< 3 pcs)
        $criticalStocks = Stock::with(['product', 'location'])
            ->where('qty', '<', 3)
            ->whereHas('product', fn ($p) => $p->where('is_active', true))
            ->orderBy('qty', 'asc')
            ->take(12)
            ->get();

        // 4. Varian Parfum Terlaris (Best Seller) - hanya volume penjualan pcs, tanpa data keuangan
        $bestSellers = \App\Models\Invoice::select(
                'product_id',
                DB::raw('SUM(qty) as total_sold')
            )
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->with('product')
            ->take(5)
            ->get();

        // 5. Antrean Produk PO (Stok Pending yang sedang diproduksi)
        $pendingPreOrders = \App\Models\PreOrder::with(['product', 'location'])
            ->where('status', \App\Models\PreOrder::STATUS_PENDING)
            ->latest()
            ->get();

        $outOfStockProducts = $allProducts->filter(function ($p) {
            return (int) $p->stocks->sum('qty') === 0;
        })->values();

        // 6. Waktu Aktivitas / Mutasi Stok Terakhir Riil di Sistem
        $lastStockActivityTimestamp = collect([
            Stock::max('updated_at'),
            \App\Models\Transaction::where('stock_effect', '!=', \App\Models\Transaction::EFFECT_NONE)->max('created_at'),
            \App\Models\StockTransfer::max('created_at'),
            \App\Models\Invoice::max('created_at'),
        ])->filter()->max();

        $lastStockActivity = $lastStockActivityTimestamp ? \Carbon\Carbon::parse($lastStockActivityTimestamp) : now();

        $metrics = [
            'total_iwan_stock' => $iwan ? (int) Stock::where('location_id', $iwan->id)->sum('qty') : 0,
            'total_rizky_stock' => $rizky ? (int) Stock::where('location_id', $rizky->id)->sum('qty') : 0,
            'total_combined_stock' => Stock::sum('qty'),
            'ready_variants_count' => $allProducts->filter(fn ($p) => (int) $p->stocks->sum('qty') > 0)->count(),
            'low_stock_count' => $allProducts->filter(function ($p) {
                $qty = (int) $p->stocks->sum('qty');
                return $qty > 0 && $qty <= 2;
            })->count(),
            'critical_count' => $criticalStocks->count(),
            'total_po_pending_qty' => (int) $pendingPreOrders->sum('qty'),
            'out_of_stock_count' => $outOfStockProducts->count(),
        ];

        return view('stocks.public_reseller_dashboard', compact(
            'productStockData',
            'holdersDistribution',
            'criticalStocks',
            'bestSellers',
            'pendingPreOrders',
            'metrics',
            'filterStatus',
            'locations',
            'outOfStockProducts',
            'iwan',
            'rizky',
            'lastStockActivity'
        ));
    }
}
