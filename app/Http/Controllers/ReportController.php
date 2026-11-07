<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Location;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Build base query for Transactions with role isolation & request filters.
     */
    protected function getTransactionsQuery(Request $request)
    {
        $user = Auth::user();
        $query = Transaction::with(['product', 'location', 'creator'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc');

        if ($user->isReseller()) {
            $userLocId = $user->location?->id;
            $query->where(function ($q) use ($user, $userLocId) {
                $q->where('created_by', $user->id);
                if ($userLocId) $q->orWhere('location_id', $userLocId);
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
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('invoice_no', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Export Transactions as CSV with UTF-8 BOM.
     */
    public function exportTransactionsCsv(Request $request): StreamedResponse
    {
        $query = $this->getTransactionsQuery($request);
        $filename = 'Laporan_Transaksi_Scentpeep_' . date('Ymd_His') . '.csv';

        $callback = function () use ($query) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'ID Transaksi',
                'Tanggal',
                'Tipe (INCOME/OUTCOME)',
                'Kategori',
                'Keterangan',
                'Nama Pembeli',
                'Saluran Penjualan',
                'Sampel Gratis',
                'Produk Parfum',
                'Lokasi Stok',
                'Jumlah Unit (Qty)',
                'Efek Stok',
                'Nominal (Rp)',
                'No. Invoice',
                'Catatan',
                'Dicatat Oleh',
            ]);

            $query->chunk(200, function ($transactions) use ($handle) {
                foreach ($transactions as $tx) {
                    fputcsv($handle, [
                        $tx->id,
                        $tx->tanggal ? $tx->tanggal->format('Y-m-d') : '-',
                        $tx->type,
                        $tx->category,
                        $tx->description,
                        $tx->customer_name ?: '-',
                        $tx->channel ?: '-',
                        $tx->is_free ? 'YA' : 'TIDAK',
                        $tx->product?->name ?: '-',
                        $tx->location?->name ?: '-',
                        $tx->qty ?? 0,
                        $tx->stock_effect,
                        $tx->amount,
                        $tx->invoice_no ?: '-',
                        $tx->note ?: '-',
                        $tx->creator?->name ?: 'System',
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Build human-readable filter description string for Transactions report.
     */
    protected function getTransactionsFilterDescription(Request $request): string
    {
        $parts = [];

        if ($request->filled('type')) {
            $parts[] = 'Tipe: ' . ($request->type === 'INCOME' ? 'Pemasukan (Masuk)' : 'Pengeluaran (Keluar)');
        }

        if ($request->filled('category')) {
            $parts[] = 'Kategori: ' . $request->category;
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $parts[] = 'Periode: ' . date('d/m/Y', strtotime($request->start_date)) . ' s/d ' . date('d/m/Y', strtotime($request->end_date));
        } elseif ($request->filled('start_date')) {
            $parts[] = 'Mulai: ' . date('d/m/Y', strtotime($request->start_date));
        } elseif ($request->filled('end_date')) {
            $parts[] = 'Sampai: ' . date('d/m/Y', strtotime($request->end_date));
        }

        if ($request->filled('location_id')) {
            $loc = Location::find($request->location_id);
            if ($loc) {
                $parts[] = 'Lokasi: ' . $loc->name;
            }
        }

        if ($request->filled('search')) {
            $parts[] = 'Pencarian: "' . $request->search . '"';
        }

        return !empty($parts) ? implode(' • ', $parts) : 'Semua Data Kas (Tanpa Filter)';
    }

    /**
     * Export Transactions as Excel (.xls).
     */
    public function exportTransactionsExcel(Request $request): Response
    {
        $transactions = $this->getTransactionsQuery($request)->get();
        $totalIncome = (float) $transactions->where('type', Transaction::TYPE_INCOME)->sum('amount');
        $totalOutcome = (float) $transactions->where('type', Transaction::TYPE_OUTCOME)->sum('amount');
        $netSaldo = $totalIncome - $totalOutcome;
        $filterDescription = $this->getTransactionsFilterDescription($request);

        $filename = 'Laporan_Transaksi_Scentpeep_' . date('Ymd_His') . '.xls';
        $content = view('reports.transactions_excel', compact(
            'transactions',
            'totalIncome',
            'totalOutcome',
            'netSaldo',
            'filterDescription'
        ))->render();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Export Transactions as Word (.doc).
     */
    public function exportTransactionsWord(Request $request): Response
    {
        $transactions = $this->getTransactionsQuery($request)->get();
        $totalIncome = (float) $transactions->where('type', Transaction::TYPE_INCOME)->sum('amount');
        $totalOutcome = (float) $transactions->where('type', Transaction::TYPE_OUTCOME)->sum('amount');
        $netSaldo = $totalIncome - $totalOutcome;
        $filterDescription = $this->getTransactionsFilterDescription($request);

        $filename = 'Laporan_Transaksi_Scentpeep_' . date('Ymd_His') . '.doc';
        $content = view('reports.transactions_word', compact(
            'transactions',
            'totalIncome',
            'totalOutcome',
            'netSaldo',
            'filterDescription'
        ))->render();

        return response($content, 200, [
            'Content-Type' => 'application/msword; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Export Transactions as printable PDF view with automatic print triggers.
     */
    public function exportTransactionsPdf(Request $request): Response
    {
        $transactions = $this->getTransactionsQuery($request)->get();
        $totalIncome = (float) $transactions->where('type', Transaction::TYPE_INCOME)->sum('amount');
        $totalOutcome = (float) $transactions->where('type', Transaction::TYPE_OUTCOME)->sum('amount');
        $netSaldo = $totalIncome - $totalOutcome;
        $filterDescription = $this->getTransactionsFilterDescription($request);

        return response()->view('reports.transactions_pdf', compact(
            'transactions',
            'totalIncome',
            'totalOutcome',
            'netSaldo',
            'filterDescription'
        ));
    }

    /**
     * Build base query for Invoices with role isolation & request filters.
     */
    protected function getInvoicesQuery(Request $request)
    {
        $user = Auth::user();
        $isReseller = $user->isReseller();

        $query = Invoice::with(['product', 'location', 'creator'])
            ->orderByDesc(DB::raw('COALESCE(tanggal, DATE(created_at))'))
            ->orderByDesc('id');

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

        return $query;
    }

    /**
     * Export Invoices as CSV with UTF-8 BOM.
     */
    public function exportInvoicesCsv(Request $request): StreamedResponse
    {
        $query = $this->getInvoicesQuery($request);
        $filename = 'Laporan_Invoice_Penjualan_Scentpeep_' . date('Ymd_His') . '.csv';

        $callback = function () use ($query) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'ID Invoice',
                'No. Invoice',
                'Tanggal',
                'Nama Sales',
                'Customer / Pembeli',
                'Varian Parfum',
                'Lokasi Stok',
                'Kuantitas (Qty Pcs)',
                'Total Harga (Rp)',
                'Status Pembayaran',
                'Catatan',
                'Diterbitkan Oleh',
            ]);

            $query->chunk(200, function ($invoices) use ($handle) {
                foreach ($invoices as $inv) {
                    fputcsv($handle, [
                        $inv->id,
                        $inv->invoice_no,
                        $inv->tanggal ? $inv->tanggal->format('Y-m-d') : ($inv->created_at ? $inv->created_at->format('Y-m-d') : '-'),
                        $inv->sales_name ?: '-',
                        $inv->customer_name ?: '-',
                        $inv->product?->name ?: '-',
                        $inv->location?->name ?: '-',
                        $inv->qty ?? 0,
                        $inv->total_price ?? 0,
                        $inv->payment_status,
                        $inv->notes ?: '-',
                        $inv->creator?->name ?: 'System',
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Build human-readable filter description string for Invoices report.
     */
    protected function getInvoicesFilterDescription(Request $request): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $parts = [];

        if ($request->filled('month') && isset($months[(int)$request->month])) {
            $parts[] = 'Bulan: ' . $months[(int)$request->month];
        }

        if ($request->filled('year')) {
            $parts[] = 'Tahun: ' . $request->year;
        }

        if ($request->filled('customer_name')) {
            $parts[] = 'Pelanggan: ' . $request->customer_name;
        }

        if ($request->filled('sales_name')) {
            $parts[] = 'Sales: ' . $request->sales_name;
        }

        if ($request->filled('payment_status')) {
            $parts[] = 'Status: ' . $request->payment_status;
        }

        if ($request->filled('location_id')) {
            $loc = Location::find($request->location_id);
            if ($loc) {
                $parts[] = 'Lokasi: ' . $loc->name;
            }
        }

        if ($request->filled('search')) {
            $parts[] = 'Pencarian: "' . $request->search . '"';
        }

        return !empty($parts) ? implode(' • ', $parts) : 'Semua Data Transaksi (Tanpa Filter)';
    }

    /**
     * Export Invoices as Excel (.xls).
     */
    public function exportInvoicesExcel(Request $request): Response
    {
        $invoices = $this->getInvoicesQuery($request)->get();
        $totalQty = (int) $invoices->sum('qty');
        $totalAmount = (float) $invoices->sum('total_price');
        $totalPaid = (float) $invoices->where('payment_status', 'LUNAS')->sum('total_price');
        $totalUnpaid = (float) $invoices->where('payment_status', 'BELUM LUNAS')->sum('total_price');
        $filterDescription = $this->getInvoicesFilterDescription($request);

        $filename = 'Laporan_Invoice_Penjualan_Scentpeep_' . date('Ymd_His') . '.xls';
        $content = view('reports.invoices_excel', compact(
            'invoices',
            'totalQty',
            'totalAmount',
            'totalPaid',
            'totalUnpaid',
            'filterDescription'
        ))->render();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Export Invoices as Word (.doc).
     */
    public function exportInvoicesWord(Request $request): Response
    {
        $invoices = $this->getInvoicesQuery($request)->get();
        $totalQty = (int) $invoices->sum('qty');
        $totalAmount = (float) $invoices->sum('total_price');
        $totalPaid = (float) $invoices->where('payment_status', 'LUNAS')->sum('total_price');
        $totalUnpaid = (float) $invoices->where('payment_status', 'BELUM LUNAS')->sum('total_price');
        $filterDescription = $this->getInvoicesFilterDescription($request);

        $filename = 'Laporan_Invoice_Penjualan_Scentpeep_' . date('Ymd_His') . '.doc';
        $content = view('reports.invoices_word', compact(
            'invoices',
            'totalQty',
            'totalAmount',
            'totalPaid',
            'totalUnpaid',
            'filterDescription'
        ))->render();

        return response($content, 200, [
            'Content-Type' => 'application/msword; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Export Invoices as printable PDF view with automatic print triggers.
     */
    public function exportInvoicesPdf(Request $request): Response
    {
        $invoices = $this->getInvoicesQuery($request)->get();
        $totalQty = (int) $invoices->sum('qty');
        $totalAmount = (float) $invoices->sum('total_price');
        $totalPaid = (float) $invoices->where('payment_status', 'LUNAS')->sum('total_price');
        $totalUnpaid = (float) $invoices->where('payment_status', 'BELUM LUNAS')->sum('total_price');
        $filterDescription = $this->getInvoicesFilterDescription($request);

        return response()->view('reports.invoices_pdf', compact(
            'invoices',
            'totalQty',
            'totalAmount',
            'totalPaid',
            'totalUnpaid',
            'filterDescription'
        ));
    }

    /**
     * Export Stocks as Excel-compatible CSV with UTF-8 BOM.
     */
    public function exportStocksExcel(Request $request): StreamedResponse
    {
        $user = Auth::user();
        if ($user->isReseller()) {
            $userLoc = $user->location;
            $locations = $userLoc ? Location::where('id', $userLoc->id)->get() : collect([]);
        } else {
            $locations = Location::orderBy('name')->get();
        }

        $products = Product::where('is_active', true)->orderBy('name')->get();
        $stocks = Stock::whereIn('location_id', $locations->pluck('id'))->get();

        $stockMatrix = [];
        foreach ($stocks as $stk) {
            $stockMatrix[$stk->product_id][$stk->location_id] = (int) $stk->qty;
        }

        $filename = 'Laporan_Matriks_Stok_Scentpeep_' . date('Ymd_His') . '.csv';

        $callback = function () use ($products, $locations, $stockMatrix) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Build dynamic headers based on locations
            $headers = ['Kode Produk', 'Nama Produk', 'Harga Modal (Rp)', 'Harga Jual (Rp)'];
            foreach ($locations as $loc) {
                $headers[] = 'Stok ' . $loc->name;
            }
            $headers[] = 'Total Unit Fisik';

            fputcsv($handle, $headers);

            foreach ($products as $prod) {
                $row = [
                    $prod->code ?: '-',
                    $prod->name,
                    $prod->cost_price ?? 0,
                    $prod->sell_price ?? 0,
                ];

                $totalQty = 0;
                foreach ($locations as $loc) {
                    $qty = $stockMatrix[$prod->id][$loc->id] ?? 0;
                    $row[] = $qty;
                    $totalQty += $qty;
                }
                $row[] = $totalQty;

                fputcsv($handle, $row);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Export Stocks as printable PDF view.
     */
    public function exportStocksPdf(Request $request): Response
    {
        $user = Auth::user();
        if ($user->isReseller()) {
            $userLoc = $user->location;
            $locations = $userLoc ? Location::where('id', $userLoc->id)->get() : collect([]);
        } else {
            $locations = Location::orderBy('name')->get();
        }

        $products = Product::where('is_active', true)->orderBy('name')->get();
        $stocks = Stock::whereIn('location_id', $locations->pluck('id'))->get();

        $stockMatrix = [];
        $totalSystemQty = 0;
        foreach ($stocks as $stk) {
            $stockMatrix[$stk->product_id][$stk->location_id] = (int) $stk->qty;
            $totalSystemQty += (int) $stk->qty;
        }

        return response()->view('reports.stocks_pdf', compact(
            'products',
            'locations',
            'stockMatrix',
            'totalSystemQty'
        ));
    }
}
