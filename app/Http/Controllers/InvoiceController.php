<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
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

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $query = Invoice::with(['product', 'location', 'creator'])->latest();

        if ($request->filled('sales_name')) {
            $query->where('sales_name', $request->sales_name);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('invoice_no', 'like', "%{$search}%");
            });
        }

        $invoices = $query->paginate(15)->withQueryString();

        $metrics = [
            'total_invoices' => Invoice::count(),
            'total_sales_amount' => (float) Invoice::sum('total_price'),
            'total_unpaid_amount' => (float) Invoice::where('payment_status', Invoice::STATUS_UNPAID)->sum('total_price'),
            'total_paid_amount' => (float) Invoice::where('payment_status', Invoice::STATUS_PAID)->sum('total_price'),
        ];

        $locations = Location::orderBy('name')->get();
        $salesOptions = Invoice::getSalesOptions();

        return view('invoices.index', compact('invoices', 'metrics', 'locations', 'salesOptions'));
    }

    public function create(): View
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        $salesOptions = Invoice::getSalesOptions();

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
        $products = Product::where('is_active', true)->orWhere('id', $invoice->product_id)->orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        $salesOptions = Invoice::getSalesOptions();

        return view('invoices.edit', compact('invoice', 'products', 'locations', 'salesOptions'));
    }

    public function update(Request $request, Invoice $invoice): RedirectResponse
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
