<?php

namespace App\Http\Controllers;

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

        $query = Invoice::with(['product', 'location', 'creator'])->latest();

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

        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('invoice_no', 'like', "%{$search}%");
            });
        }

        // Filtered Metrics (menyesuaikan filter yang sedang aktif & role reseller)
        $filteredMetricQuery = clone $query;
        $metrics = [
            'total_invoices' => (clone $filteredMetricQuery)->count(),
            'total_qty_sold' => (int) (clone $filteredMetricQuery)->sum('qty'),
            'total_sales_amount' => (float) (clone $filteredMetricQuery)->sum('total_price'),
            'total_unpaid_amount' => (float) (clone $filteredMetricQuery)->where('payment_status', Invoice::STATUS_UNPAID)->sum('total_price'),
            'total_paid_amount' => (float) (clone $filteredMetricQuery)->where('payment_status', Invoice::STATUS_PAID)->sum('total_price'),
        ];

        // Evaluasi Penjualan Year-on-Year (Sold Comparison: Tahun Ini vs Tahun Kemarin)
        $targetYear = (int) ($request->filled('year') ? $request->year : Carbon::now()->year);
        $prevYear = $targetYear - 1;

        $thisYearQuery = Invoice::whereYear('created_at', $targetYear);
        $lastYearQuery = Invoice::whereYear('created_at', $prevYear);

        if ($isReseller) {
            $userLocId = $user->location?->id;
            $resellerScope = function ($q) use ($user, $userLocId) {
                $q->where('sales_name', $user->name)
                  ->orWhere('created_by', $user->id);
                if ($userLocId) {
                    $q->orWhere('location_id', $userLocId);
                }
            };
            $thisYearQuery->where($resellerScope);
            $lastYearQuery->where($resellerScope);
        } elseif ($request->filled('sales_name')) {
            $thisYearQuery->where('sales_name', $request->sales_name);
            $lastYearQuery->where('sales_name', $request->sales_name);
        }

        $thisYearQty = (int) (clone $thisYearQuery)->sum('qty');
        $thisYearOmset = (float) (clone $thisYearQuery)->sum('total_price');
        $thisYearInvoices = (int) (clone $thisYearQuery)->count();

        $lastYearQty = (int) (clone $lastYearQuery)->sum('qty');
        $lastYearOmset = (float) (clone $lastYearQuery)->sum('total_price');
        $lastYearInvoices = (int) (clone $lastYearQuery)->count();

        $qtyDiff = $thisYearQty - $lastYearQty;
        $qtyGrowth = $lastYearQty > 0 
            ? round(($qtyDiff / $lastYearQty) * 100, 1) 
            : ($thisYearQty > 0 ? 100.0 : 0.0);

        $omsetDiff = $thisYearOmset - $lastYearOmset;
        $omsetGrowth = $lastYearOmset > 0 
            ? round(($omsetDiff / $lastYearOmset) * 100, 1) 
            : ($thisYearOmset > 0 ? 100.0 : 0.0);

        $yoyMetrics = [
            'target_year' => $targetYear,
            'prev_year' => $prevYear,
            'this_year_qty' => $thisYearQty,
            'this_year_omset' => $thisYearOmset,
            'this_year_invoices' => $thisYearInvoices,
            'last_year_qty' => $lastYearQty,
            'last_year_omset' => $lastYearOmset,
            'last_year_invoices' => $lastYearInvoices,
            'qty_diff' => $qtyDiff,
            'qty_growth' => $qtyGrowth,
            'omset_diff' => $omsetDiff,
            'omset_growth' => $omsetGrowth,
        ];

        // Distinct available years for filter
        $yearExtractSql = DB::connection()->getDriverName() === 'sqlite' 
            ? "strftime('%Y', created_at) as y" 
            : "YEAR(created_at) as y";

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
            'metrics',
            'locations',
            'salesOptions',
            'yoyMetrics',
            'availableYears',
            'months'
        ));
    }

    public function create(): View
    {
        $user = Auth::user();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        if ($user->isReseller()) {
            $locations = $user->location ? Location::where('id', $user->location->id)->get() : collect([]);
            $salesOptions = collect([$user->name]);
        } else {
            $locations = Location::orderBy('name')->get();
            $salesOptions = Invoice::getSalesOptions();
        }

        return view('invoices.create', compact('products', 'locations', 'salesOptions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'invoice_no' => 'nullable|string|max:100',
            'sales_name' => 'required|string|max:100',
            'customer_name' => 'required|string|max:255',
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

            $invoice = Invoice::create([
                'invoice_no' => $validated['invoice_no'] ?: 'INV-' . strtoupper(uniqid()),
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

            // Jika dibuat langsung LUNAS, catat uang kas masuk secara otomatis
            if ($invoice->payment_status === Invoice::STATUS_PAID) {
                Transaction::create([
                    'tanggal' => now()->toDateString(),
                    'type' => Transaction::TYPE_INCOME,
                    'category' => 'Penjualan',
                    'customer_name' => $invoice->customer_name,
                    'amount' => $invoice->total_price,
                    'invoice_no' => $invoice->invoice_no,
                    'description' => "Penjualan Invoice {$invoice->invoice_no} ({$invoice->product->name} x {$invoice->qty}) via {$invoice->sales_name}",
                    'stock_effect' => Transaction::EFFECT_NONE,
                    'created_by' => Auth::id(),
                ]);
            }
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

        return view('invoices.edit', compact('invoice', 'products', 'locations', 'salesOptions'));
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
            'sales_name' => 'required|string|max:100',
            'customer_name' => 'required|string|max:255',
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
            $oldPaymentStatus = $invoice->payment_status;

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

            $newInvoiceNo = $validated['invoice_no'] ?: $invoice->invoice_no;

            // 3. Sinkronisasi pencatatan kas jika status pembayaran atau nominal berubah
            if ($oldPaymentStatus === Invoice::STATUS_PAID && $validated['payment_status'] === Invoice::STATUS_PAID) {
                $incomeTx = Transaction::where('invoice_no', $invoice->invoice_no)
                    ->where('type', Transaction::TYPE_INCOME)
                    ->first();
                if ($incomeTx) {
                    $incomeTx->update([
                        'invoice_no' => $newInvoiceNo,
                        'amount' => $validated['total_price'],
                        'customer_name' => $validated['customer_name'],
                        'description' => "Penjualan Invoice {$newInvoiceNo} ({$newStock->product?->name} x {$validated['qty']}) via {$validated['sales_name']}",
                    ]);
                }
            } elseif ($oldPaymentStatus === Invoice::STATUS_UNPAID && $validated['payment_status'] === Invoice::STATUS_PAID) {
                Transaction::create([
                    'tanggal' => now()->toDateString(),
                    'type' => Transaction::TYPE_INCOME,
                    'category' => 'Penjualan',
                    'customer_name' => $validated['customer_name'],
                    'amount' => $validated['total_price'],
                    'invoice_no' => $newInvoiceNo,
                    'description' => "Penjualan Invoice {$newInvoiceNo} ({$newStock->product?->name} x {$validated['qty']}) via {$validated['sales_name']}",
                    'stock_effect' => Transaction::EFFECT_NONE,
                    'created_by' => Auth::id(),
                ]);
            } elseif ($oldPaymentStatus === Invoice::STATUS_PAID && $validated['payment_status'] === Invoice::STATUS_UNPAID) {
                Transaction::where('invoice_no', $invoice->invoice_no)
                    ->where('type', Transaction::TYPE_INCOME)
                    ->delete();
            }

            // 4. Update data invoice
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

        DB::transaction(function () use ($invoice) {
            $invoice->update(['payment_status' => Invoice::STATUS_PAID]);

            Transaction::create([
                'tanggal' => now()->toDateString(),
                'type' => Transaction::TYPE_INCOME,
                'category' => 'Penjualan',
                'customer_name' => $invoice->customer_name,
                'amount' => $invoice->total_price,
                'invoice_no' => $invoice->invoice_no,
                'description' => "Pelunasan Invoice {$invoice->invoice_no} ({$invoice->product->name} x {$invoice->qty})",
                'stock_effect' => Transaction::EFFECT_NONE,
                'created_by' => Auth::id(),
            ]);
        });

        return redirect()->back()->with('success', "Invoice {$invoice->invoice_no} berhasil ditandai LUNAS dan dicatat ke Buku Kas.");
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

            // Hapus pencatatan kas terkait jika invoice dihapus
            Transaction::where('invoice_no', $invoice->invoice_no)
                ->where('type', Transaction::TYPE_INCOME)
                ->delete();

            $invoice->delete();
        });

        return redirect()->route('invoices.index')->with('success', 'Invoice berhasil dihapus dan stok telah dikembalikan ke lokasi asal.');
    }
}
