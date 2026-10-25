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
            $transferCountToday = StockTransfer::whereDate('created_at', now()->toDateString())->count();
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
}
