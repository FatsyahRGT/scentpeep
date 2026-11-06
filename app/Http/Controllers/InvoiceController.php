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

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
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

        return view('invoices.index', compact(
            'invoices',
            'locations',
            'salesOptions',
            'availableYears',
            'months'
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

        DB::transaction(function () use ($validated, $request) {
            $stock = Stock::firstOrCreate(
                ['product_id' => $validated['product_id'], 'location_id' => $validated['location_id']],
                ['qty' => 0]
            );

            if ($stock->qty < $validated['qty']) {
                $productName = Product::find($validated['product_id'])->name ?? 'Produk';
                $locName = Location::find($validated['location_id'])->name ?? 'Lokasi';
                throw ValidationException::withMessages([
                    'qty' => "Stok '{$productName}' di lokasi {$locName} tidak mencukupi! Tersedia: {$stock->qty} pcs, diminta: {$validated['qty']} pcs.",
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
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);
        });

        return redirect()->route('invoices.index')->with('success', 'Invoice berhasil diterbitkan dan stok telah terpotong otomatis.');
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
            $oldProductId = $invoice->product_id;
            $oldLocationId = $invoice->location_id;
            $oldQty = $invoice->qty;

            // 1. Kembalikan stok lama sebelum memeriksa stok baru
            $oldStock = Stock::where('product_id', $oldProductId)
                ->where('location_id', $oldLocationId)
                ->first();
            if ($oldStock) {
                $oldStock->increment('qty', $oldQty);
            }

            // 2. Kunci dan potong stok baru
            $newStock = Stock::where('product_id', $validated['product_id'])
                ->where('location_id', $validated['location_id'])
                ->lockForUpdate()
                ->first();

            if (! $newStock || $newStock->qty < $validated['qty']) {
                $available = $newStock ? $newStock->qty : 0;
                throw ValidationException::withMessages([
                    'qty' => "Stok di lokasi terpilih tidak mencukupi. Sisa stok tersedia: {$available} pcs.",
                ]);
            }

            $newStock->decrement('qty', $validated['qty']);

            if (empty($validated['tanggal'])) {
                $validated['tanggal'] = $invoice->tanggal ? $invoice->tanggal->toDateString() : now()->toDateString();
            }

            // 3. Update data invoice
            $invoice->update($validated);
        });

        return redirect()->route('invoices.index')->with('success', "Invoice {$invoice->invoice_no} berhasil diperbarui.");
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

    public function destroy(Invoice $invoice): RedirectResponse
    {
        $user = Auth::user();
        if ($user->isReseller()) {
            if ($invoice->created_by !== $user->id && $invoice->sales_name !== $user->name && $invoice->location_id !== $user->location?->id) {
                abort(403, 'Akses ditolak. Anda hanya dapat menghapus invoice Anda sendiri.');
            }
        }

        DB::transaction(function () use ($invoice) {
            $stock = Stock::where('product_id', $invoice->product_id)
                ->where('location_id', $invoice->location_id)
                ->first();

            if ($stock) {
                $stock->increment('qty', $invoice->qty);
            }

            $invoice->delete();
        });

        return redirect()->route('invoices.index')->with('success', 'Invoice berhasil dihapus dan stok telah dikembalikan ke lokasi asal.');
    }
}
