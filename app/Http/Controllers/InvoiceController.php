<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Location;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $isReseller = $user->isReseller();

        $query = Invoice::with(['product', 'location', 'creator'])
            ->orderByDesc(DB::raw('COALESCE(tanggal, DATE(created_at))'))
            ->orderByDesc('id');

        // Isolasi Data: Reseller HANYA bisa mengakses invoice miliknya sendiri
        if ($isReseller) {
            $userLocId = $user->location?->id;
            $query->where(function ($q) use ($user, $userLocId) {
                $q->where('sales_name', $user->name)
                  ->orWhere('created_by', $user->id);
                if ($userLocId) {
                    $q->orWhere('location_id', $userLocId);
                }
            });
        } elseif ($request->filled('sales_name')) {
            $query->where('sales_name', $request->sales_name);
        }

        // Tab Filter Cepat (Semua, Penjualan Langsung, PO Menunggu, PO Selesai)
        $tab = $request->get('tab', 'all');
        if ($tab === 'regular') {
            $query->where('order_type', Invoice::ORDER_TYPE_REGULAR);
        } elseif ($tab === 'po_pending') {
            $query->where('order_type', Invoice::ORDER_TYPE_PREORDER)
                  ->where('po_status', Invoice::PO_STATUS_PENDING);
        } elseif ($tab === 'po_completed') {
            $query->where('order_type', Invoice::ORDER_TYPE_PREORDER)
                  ->where('po_status', Invoice::PO_STATUS_COMPLETED);
        } elseif ($request->filled('order_type')) {
            $query->where('order_type', $request->order_type);
            if ($request->filled('po_status')) {
                $query->where('po_status', $request->po_status);
            }
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('customer_name')) {
            $query->where('customer_name', $request->customer_name);
        }

        $dateCol = DB::raw('COALESCE(tanggal, DATE(created_at))');
        if ($request->filled('year')) {
            $query->whereYear($dateCol, $request->year);
        }

        if ($request->filled('month')) {
            $query->whereMonth($dateCol, $request->month);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('invoice_no', 'like', "%{$search}%");
            });
        }

        // Hitung Counter Badge untuk Tab Filter
        $countQuery = Invoice::query();
        if ($isReseller) {
            $userLocId = $user->location?->id;
            $countQuery->where(function ($q) use ($user, $userLocId) {
                $q->where('sales_name', $user->name)
                  ->orWhere('created_by', $user->id);
                if ($userLocId) {
                    $q->orWhere('location_id', $userLocId);
                }
            });
        } elseif ($request->filled('sales_name')) {
            $countQuery->where('sales_name', $request->sales_name);
        }
        if ($request->filled('year')) {
            $countQuery->whereYear($dateCol, $request->year);
        }
        if ($request->filled('month')) {
            $countQuery->whereMonth($dateCol, $request->month);
        }
        if ($request->filled('customer_name')) {
            $countQuery->where('customer_name', $request->customer_name);
        }
        if ($request->filled('location_id')) {
            $countQuery->where('location_id', $request->location_id);
        }

        $countAll = (clone $countQuery)->count();
        $countRegular = (clone $countQuery)->where('order_type', Invoice::ORDER_TYPE_REGULAR)->count();
        $countPoPending = (clone $countQuery)->where('order_type', Invoice::ORDER_TYPE_PREORDER)->where('po_status', Invoice::PO_STATUS_PENDING)->count();
        $countPoCompleted = (clone $countQuery)->where('order_type', Invoice::ORDER_TYPE_PREORDER)->where('po_status', Invoice::PO_STATUS_COMPLETED)->count();

        $yearExtractSql = DB::connection()->getDriverName() === 'sqlite' 
            ? "strftime('%Y', COALESCE(tanggal, created_at)) as y" 
            : "YEAR(COALESCE(tanggal, created_at)) as y";

        $availableYears = Invoice::selectRaw($yearExtractSql)
            ->distinct()
            ->pluck('y')
            ->filter()
            ->map(fn($y) => (int)$y)
            ->push(Carbon::now()->year)
            ->push(Carbon::now()->year - 1)
            ->unique()
            ->sortDesc()
            ->values();

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $invoices = $query->paginate(15)->withQueryString();

        if ($isReseller) {
            $locations = $user->location ? Location::where('id', $user->location->id)->get() : collect([]);
            $salesOptions = collect([$user->name]);
        } else {
            $locations = Location::orderBy('name')->get();
            $salesOptions = Invoice::getSalesOptions();
        }

        $customers = Customer::orderBy('name')->get();

        return view('invoices.index', compact(
            'invoices',
            'locations',
            'salesOptions',
            'availableYears',
            'months',
            'customers',
            'tab',
            'countAll',
            'countRegular',
            'countPoPending',
            'countPoCompleted'
        ));
    }

    /**
     * Dashboard khusus Evaluasi & Komparasi Penjualan (Year-on-Year dan Perbandingan Bulanan).
     */
    public function evaluation(Request $request): View
    {
        $user = Auth::user();
        $isReseller = $user->isReseller();

        // 1. Tahun Target dan Tahun Pembanding
        $targetYear = (int) ($request->filled('year') ? $request->year : Carbon::now()->year);
        $compareYear = (int) ($request->filled('compare_year') ? $request->compare_year : ($targetYear - 1));
        $selectedMonth = $request->filled('month') ? (int) $request->month : null;
        $selectedSales = $request->sales_name;

        // Distinct available years for filter
        $yearExtractSql = DB::connection()->getDriverName() === 'sqlite' 
            ? "strftime('%Y', COALESCE(tanggal, created_at)) as y" 
            : "YEAR(COALESCE(tanggal, created_at)) as y";

        $availableYears = Invoice::selectRaw($yearExtractSql)
            ->distinct()
            ->pluck('y')
            ->filter()
            ->map(fn($y) => (int)$y)
            ->push(Carbon::now()->year)
            ->push(Carbon::now()->year - 1)
            ->unique()
            ->sortDesc()
            ->values();

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // Scope helper
        $applySalesScope = function ($query) use ($isReseller, $user, $selectedSales) {
            if ($isReseller) {
                $userLocId = $user->location?->id;
                $query->where(function ($q) use ($user, $userLocId) {
                    $q->where('sales_name', $user->name)
                      ->orWhere('created_by', $user->id);
                    if ($userLocId) {
                        $q->orWhere('location_id', $userLocId);
                    }
                });
            } elseif ($selectedSales) {
                $query->where('sales_name', $selectedSales);
            }
        };

        // Query Utama untuk KPI Ringkasan
        $dateCol = DB::raw('COALESCE(tanggal, DATE(created_at))');
        $targetQuery = Invoice::whereYear($dateCol, $targetYear);
        $compareQuery = Invoice::whereYear($dateCol, $compareYear);

        if ($selectedMonth) {
            $targetQuery->whereMonth($dateCol, $selectedMonth);
            $compareQuery->whereMonth($dateCol, $selectedMonth);
        }

        $applySalesScope($targetQuery);
        $applySalesScope($compareQuery);

        $targetQty = (int) (clone $targetQuery)->sum('qty');
        $targetOmset = (float) (clone $targetQuery)->sum('total_price');
        $targetInvoices = (int) (clone $targetQuery)->count();

        $compareQty = (int) (clone $compareQuery)->sum('qty');
        $compareOmset = (float) (clone $compareQuery)->sum('total_price');
        $compareInvoices = (int) (clone $compareQuery)->count();

        $qtyDiff = $targetQty - $compareQty;
        $qtyGrowth = $compareQty > 0 
            ? round(($qtyDiff / $compareQty) * 100, 1) 
            : ($targetQty > 0 ? 100.0 : 0.0);

        $omsetDiff = $targetOmset - $compareOmset;
        $omsetGrowth = $compareOmset > 0 
            ? round(($omsetDiff / $compareOmset) * 100, 1) 
            : ($targetOmset > 0 ? 100.0 : 0.0);

        $yoyMetrics = [
            'target_year' => $targetYear,
            'compare_year' => $compareYear,
            'selected_month' => $selectedMonth,
            'month_name' => $selectedMonth ? ($months[$selectedMonth] ?? '') : null,
            'target_qty' => $targetQty,
            'target_omset' => $targetOmset,
            'target_invoices' => $targetInvoices,
            'compare_qty' => $compareQty,
            'compare_omset' => $compareOmset,
            'compare_invoices' => $compareInvoices,
            'qty_diff' => $qtyDiff,
            'qty_growth' => $qtyGrowth,
            'omset_diff' => $omsetDiff,
            'omset_growth' => $omsetGrowth,
        ];

        // Rincian Bulanan 12 Bulan
        $monthExtractSql = DB::connection()->getDriverName() === 'sqlite'
            ? "CAST(strftime('%m', COALESCE(tanggal, created_at)) AS INTEGER) as m"
            : "MONTH(COALESCE(tanggal, created_at)) as m";

        $targetMonthlyDataQuery = Invoice::whereYear($dateCol, $targetYear)
            ->selectRaw("{$monthExtractSql}, SUM(qty) as total_qty, SUM(total_price) as total_omset, COUNT(*) as inv_count")
            ->groupBy('m');
        $applySalesScope($targetMonthlyDataQuery);
        $targetMonthly = $targetMonthlyDataQuery->get()->keyBy('m');

        $compareMonthlyDataQuery = Invoice::whereYear($dateCol, $compareYear)
            ->selectRaw("{$monthExtractSql}, SUM(qty) as total_qty, SUM(total_price) as total_omset, COUNT(*) as inv_count")
            ->groupBy('m');
        $applySalesScope($compareMonthlyDataQuery);
        $compareMonthly = $compareMonthlyDataQuery->get()->keyBy('m');

        $monthlyBreakdown = [];
        for ($m = 1; $m <= 12; $m++) {
            $tRec = $targetMonthly->get($m);
            $cRec = $compareMonthly->get($m);

            $tQ = (int) ($tRec?->total_qty ?? 0);
            $tO = (float) ($tRec?->total_omset ?? 0);
            $tI = (int) ($tRec?->inv_count ?? 0);

            $cQ = (int) ($cRec?->total_qty ?? 0);
            $cO = (float) ($cRec?->total_omset ?? 0);
            $cI = (int) ($cRec?->inv_count ?? 0);

            $qDiff = $tQ - $cQ;
            $qGrowth = $cQ > 0 ? round(($qDiff / $cQ) * 100, 1) : ($tQ > 0 ? 100.0 : 0.0);

            $oDiff = $tO - $cO;
            $oGrowth = $cO > 0 ? round(($oDiff / $cO) * 100, 1) : ($tO > 0 ? 100.0 : 0.0);

            $monthlyBreakdown[$m] = [
                'month_num' => $m,
                'month_name' => $months[$m],
                'target_qty' => $tQ,
                'target_omset' => $tO,
                'target_invoices' => $tI,
                'compare_qty' => $cQ,
                'compare_omset' => $cO,
                'compare_invoices' => $cI,
                'qty_diff' => $qDiff,
                'qty_growth' => $qGrowth,
                'omset_diff' => $oDiff,
                'omset_growth' => $oGrowth,
            ];
        }

        // Top Varian Terlaris di Periode Terpilih
        $topProductsQuery = Invoice::whereYear($dateCol, $targetYear)
            ->select('product_id', DB::raw('SUM(qty) as total_sold'), DB::raw('SUM(total_price) as total_omset'))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->with('product');

        if ($selectedMonth) {
            $topProductsQuery->whereMonth($dateCol, $selectedMonth);
        }
        $applySalesScope($topProductsQuery);
        $topProducts = $topProductsQuery->take(8)->get();

        $salesOptions = $isReseller ? collect([$user->name]) : Invoice::getSalesOptions();

        return view('invoices.evaluation', compact(
            'yoyMetrics',
            'monthlyBreakdown',
            'topProducts',
            'availableYears',
            'months',
            'salesOptions',
            'targetYear',
            'compareYear',
            'selectedMonth',
            'selectedSales',
            'isReseller'
        ));
    }

    public function create(): View
    {
        $user = Auth::user();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $customers = Customer::orderBy('name', 'asc')->get(['id', 'name']);

        if ($user->isReseller()) {
            $locations = $user->location ? Location::where('id', $user->location->id)->get() : collect([]);
            $salesOptions = collect([$user->name]);
        } else {
            $locations = Location::orderBy('name')->get();
            $salesOptions = Invoice::getSalesOptions();
        }

        return view('invoices.create', compact('products', 'locations', 'salesOptions', 'customers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'invoice_no' => 'nullable|string|max:100',
            'tanggal' => 'nullable|date',
            'order_type' => 'nullable|in:REGULAR,PREORDER',
            'sales_name' => 'required|string|max:100',
            'customer_name' => 'required|string|max:255',
            'customer_id' => 'nullable|integer',
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1',
            'location_id' => 'required|exists:locations,id',
            'total_price' => 'required|numeric|min:0',
            'payment_status' => 'required|in:BELUM LUNAS,LUNAS',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        if ($user->isReseller()) {
            $validated['sales_name'] = $user->name;
            if ($user->location) {
                $validated['location_id'] = $user->location->id;
            }
        }

        $orderType = $validated['order_type'] ?? Invoice::ORDER_TYPE_REGULAR;
        $isPreOrder = ($orderType === Invoice::ORDER_TYPE_PREORDER);

        DB::transaction(function () use ($validated, $isPreOrder, $orderType) {
            $stock = Stock::firstOrCreate(
                ['product_id' => $validated['product_id'], 'location_id' => $validated['location_id']],
                ['qty' => 0]
            );

            if ($isPreOrder) {
                // Pre-Order (PO): Mengunci alokasi pesanan tanpa memotong stok fisik saat ini
                Invoice::create([
                    'invoice_no' => $validated['invoice_no'] ?: 'INV-PO-' . strtoupper(uniqid()),
                    'tanggal' => $validated['tanggal'] ?? now()->toDateString(),
                    'sales_name' => $validated['sales_name'],
                    'customer_name' => $validated['customer_name'],
                    'product_id' => $validated['product_id'],
                    'qty' => $validated['qty'],
                    'location_id' => $validated['location_id'],
                    'total_price' => $validated['total_price'],
                    'payment_status' => $validated['payment_status'],
                    'order_type' => Invoice::ORDER_TYPE_PREORDER,
                    'po_status' => Invoice::PO_STATUS_PENDING,
                    'notes' => $validated['notes'] ?? null,
                    'created_by' => Auth::id(),
                ]);
            } else {
                // Penjualan Langsung (Ready Stock): Validasi terhadap stok siap jual bebas
                $reservedPo = $stock->getReservedPoQty();
                $available = max(0, (int) $stock->qty - $reservedPo);

                if ($available < $validated['qty']) {
                    $productName = Product::find($validated['product_id'])->name ?? 'Produk';
                    $locName = Location::find($validated['location_id'])->name ?? 'Lokasi';
                    throw ValidationException::withMessages([
                        'qty' => "Stok siap jual untuk '{$productName}' di {$locName} tidak mencukupi! Fisik di rak: {$stock->qty} pcs, Terkunci jatah PO: {$reservedPo} pcs, Siap jual: {$available} pcs, Diminta: {$validated['qty']} pcs.",
                    ]);
                }

                $stock->decrement('qty', $validated['qty']);

                Invoice::create([
                    'invoice_no' => $validated['invoice_no'] ?: 'INV-' . strtoupper(uniqid()),
                    'tanggal' => $validated['tanggal'] ?? now()->toDateString(),
                    'sales_name' => $validated['sales_name'],
                    'customer_name' => $validated['customer_name'],
                    'product_id' => $validated['product_id'],
                    'qty' => $validated['qty'],
                    'location_id' => $validated['location_id'],
                    'total_price' => $validated['total_price'],
                    'payment_status' => $validated['payment_status'],
                    'order_type' => Invoice::ORDER_TYPE_REGULAR,
                    'po_status' => Invoice::PO_STATUS_NONE,
                    'notes' => $validated['notes'] ?? null,
                    'created_by' => Auth::id(),
                ]);
            }
        });

        $message = $isPreOrder
            ? 'Pesanan Pre-Order (PO) berhasil dicatat dan masuk antrean PO. Stok fisik belum terpotong.'
            : 'Invoice berhasil diterbitkan dan stok telah terpotong otomatis.';

        return redirect()->route('invoices.index', $isPreOrder ? ['tab' => 'po_pending'] : [])->with('success', $message);
    }

    public function edit(Invoice $invoice): View
    {
        $user = Auth::user();
        if ($user->isReseller()) {
            if ($invoice->created_by !== $user->id && $invoice->sales_name !== $user->name && $invoice->location_id !== $user->location?->id) {
                abort(403, 'Akses ditolak. Anda hanya dapat melihat invoice Anda sendiri.');
            }
            $locations = $user->location ? Location::where('id', $user->location->id)->get() : collect([]);
            $salesOptions = collect([$user->name]);
        } else {
            $locations = Location::orderBy('name')->get();
            $salesOptions = Invoice::getSalesOptions();
        }

        $products = Product::where('is_active', true)->orWhere('id', $invoice->product_id)->orderBy('name')->get();
        $customers = Customer::orderBy('name', 'asc')->get(['id', 'name']);

        return view('invoices.edit', compact('invoice', 'products', 'locations', 'salesOptions', 'customers'));
    }

    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
        $user = Auth::user();
        if ($user->isReseller()) {
            if ($invoice->created_by !== $user->id && $invoice->sales_name !== $user->name && $invoice->location_id !== $user->location?->id) {
                abort(403, 'Akses ditolak. Anda hanya dapat mengedit invoice Anda sendiri.');
            }
        }

        $validated = $request->validate([
            'invoice_no' => 'nullable|string|max:100',
            'tanggal' => 'nullable|date',
            'order_type' => 'nullable|in:REGULAR,PREORDER',
            'sales_name' => 'required|string|max:100',
            'customer_name' => 'required|string|max:255',
            'customer_id' => 'nullable|integer',
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1',
            'location_id' => 'required|exists:locations,id',
            'total_price' => 'required|numeric|min:0',
            'payment_status' => 'required|in:BELUM LUNAS,LUNAS',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($user->isReseller()) {
            $validated['sales_name'] = $user->name;
            if ($user->location) {
                $validated['location_id'] = $user->location->id;
            }
        }

        DB::transaction(function () use ($validated, $invoice) {
            $oldDeducted = ($invoice->order_type === Invoice::ORDER_TYPE_REGULAR)
                || ($invoice->order_type === Invoice::ORDER_TYPE_PREORDER && $invoice->po_status === Invoice::PO_STATUS_COMPLETED);

            // 1. Kembalikan stok fisik lama jika sebelumnya pernah dipotong
            if ($oldDeducted) {
                $oldStock = Stock::where('product_id', $invoice->product_id)
                    ->where('location_id', $invoice->location_id)
                    ->first();
                if ($oldStock) {
                    $oldStock->increment('qty', $invoice->qty);
                }
            }

            $newOrderType = $validated['order_type'] ?? $invoice->order_type ?? Invoice::ORDER_TYPE_REGULAR;

            if ($newOrderType === Invoice::ORDER_TYPE_PREORDER) {
                // Pre-Order: Jika sebelumnya sudah COMPLETED, tetap potong fisik. Jika PENDING, jangan potong fisik.
                $newPoStatus = $invoice->isPoCompleted() ? Invoice::PO_STATUS_COMPLETED : Invoice::PO_STATUS_PENDING;
                if ($newPoStatus === Invoice::PO_STATUS_COMPLETED) {
                    $newStock = Stock::where('product_id', $validated['product_id'])
                        ->where('location_id', $validated['location_id'])
                        ->lockForUpdate()
                        ->first();
                    if (! $newStock || $newStock->qty < $validated['qty']) {
                        $available = $newStock ? $newStock->qty : 0;
                        throw ValidationException::withMessages([
                            'qty' => "Stok di lokasi terpilih tidak mencukupi untuk menyelesaikan PO. Sisa stok fisik: {$available} pcs.",
                        ]);
                    }
                    $newStock->decrement('qty', $validated['qty']);
                }
                $validated['order_type'] = Invoice::ORDER_TYPE_PREORDER;
                $validated['po_status'] = $newPoStatus;
            } else {
                // Penjualan Langsung (REGULAR): Kunci dan potong stok baru
                $newStock = Stock::where('product_id', $validated['product_id'])
                    ->where('location_id', $validated['location_id'])
                    ->lockForUpdate()
                    ->first();

                $available = $newStock ? max(0, (int)$newStock->qty - $newStock->getReservedPoQty()) : 0;
                if ($available < $validated['qty']) {
                    throw ValidationException::withMessages([
                        'qty' => "Stok siap jual di lokasi terpilih tidak mencukupi. Sisa stok siap jual: {$available} pcs.",
                    ]);
                }

                $newStock->decrement('qty', $validated['qty']);
                $validated['order_type'] = Invoice::ORDER_TYPE_REGULAR;
                $validated['po_status'] = Invoice::PO_STATUS_NONE;
            }

            if (empty($validated['tanggal'])) {
                $validated['tanggal'] = $invoice->tanggal ? $invoice->tanggal->toDateString() : now()->toDateString();
            }

            $invoice->update($validated);
        });

        return redirect()->route('invoices.index')->with('success', "Invoice {$invoice->invoice_no} berhasil diperbarui.");
    }

    /**
     * Selesaikan pesanan Pre-Order (PO): Potong stok fisik dan serahkan barang ke pelanggan.
     */
    public function completePo(Invoice $invoice): RedirectResponse
    {
        $user = Auth::user();
        if ($user->isReseller()) {
            if ($invoice->created_by !== $user->id && $invoice->sales_name !== $user->name && $invoice->location_id !== $user->location?->id) {
                abort(403, 'Akses ditolak. Anda hanya dapat memproses invoice Anda sendiri.');
            }
        }

        if (! $invoice->isPoPending()) {
            return redirect()->back()->with('error', 'Hanya invoice Pre-Order berstatus PENDING yang dapat diselesaikan.');
        }

        DB::transaction(function () use ($invoice) {
            $stock = Stock::where('product_id', $invoice->product_id)
                ->where('location_id', $invoice->location_id)
                ->lockForUpdate()
                ->first();

            $currentQty = $stock ? (int) $stock->qty : 0;
            if ($currentQty < $invoice->qty) {
                $productName = $invoice->product?->name ?? 'Produk';
                $locName = $invoice->location?->name ?? 'Lokasi';
                throw ValidationException::withMessages([
                    'stock' => "Stok fisik '{$productName}' di {$locName} belum mencukupi untuk serah terima barang PO ini! Stok fisik saat ini: {$currentQty} pcs, dibutuhkan: {$invoice->qty} pcs. Silakan lakukan Restock terlebih dahulu.",
                ]);
            }

            $stock->decrement('qty', $invoice->qty);

            $invoice->update([
                'po_status' => Invoice::PO_STATUS_COMPLETED,
                'po_completed_at' => now(),
            ]);
        });

        return redirect()->back()->with('success', "Pesanan Pre-Order {$invoice->invoice_no} berhasil diselesaikan. Stok fisik telah terpotong {$invoice->qty} pcs dan barang resmi diserahkan ke pelanggan.");
    }

    public function markPaid(Invoice $invoice): RedirectResponse
    {
        $user = Auth::user();
        if ($user->isReseller()) {
            if ($invoice->created_by !== $user->id && $invoice->sales_name !== $user->name && $invoice->location_id !== $user->location?->id) {
                abort(403, 'Akses ditolak. Anda hanya dapat memproses invoice Anda sendiri.');
            }
        }

        if ($invoice->payment_status === Invoice::STATUS_PAID) {
            return redirect()->back()->with('info', 'Invoice ini sudah berstatus LUNAS.');
        }

        $invoice->update(['payment_status' => Invoice::STATUS_PAID]);

        return redirect()->back()->with('success', "Invoice {$invoice->invoice_no} berhasil ditandai LUNAS.");
    }

    public function print(Invoice $invoice): View
    {
        $user = Auth::user();
        if ($user->isReseller()) {
            if ($invoice->created_by !== $user->id && $invoice->sales_name !== $user->name && $invoice->location_id !== $user->location?->id) {
                abort(403, 'Akses ditolak. Anda hanya dapat mencetak invoice Anda sendiri.');
            }
        }

        $invoice->load(['product', 'location', 'creator']);

        return view('reports.invoice_single', compact('invoice'));
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        $user = Auth::user();
        if ($user->isReseller()) {
            if ($invoice->created_by !== $user->id && $invoice->sales_name !== $user->name && $invoice->location_id !== $user->location?->id) {
                abort(403, 'Akses ditolak. Anda hanya dapat menghapus invoice Anda sendiri.');
            }
        }

        // Stok fisik hanya dikembalikan jika sebelumnya pernah dipotong (REGULAR atau PO COMPLETED)
        $shouldRestorePhysicalStock = ($invoice->order_type === Invoice::ORDER_TYPE_REGULAR)
            || ($invoice->order_type === Invoice::ORDER_TYPE_PREORDER && $invoice->po_status === Invoice::PO_STATUS_COMPLETED);

        DB::transaction(function () use ($invoice, $shouldRestorePhysicalStock) {
            if ($shouldRestorePhysicalStock) {
                $stock = Stock::where('product_id', $invoice->product_id)
                    ->where('location_id', $invoice->location_id)
                    ->first();

                if ($stock) {
                    $stock->increment('qty', $invoice->qty);
                }
            }

            $invoice->delete();
        });

        $message = $shouldRestorePhysicalStock
            ? 'Invoice berhasil dihapus dan stok telah dikembalikan ke lokasi asal.'
            : 'Pesanan Pre-Order berhasil dibatalkan dan kuota antrean PO telah dilepaskan.';

        return redirect()->route('invoices.index')->with('success', $message);
    }
}
