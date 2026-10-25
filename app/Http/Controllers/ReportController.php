<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Export Transactions as Excel-compatible CSV with UTF-8 BOM.
     */
    public function exportTransactionsExcel(Request $request): StreamedResponse
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

        $filename = 'Laporan_Transaksi_Scentpeep_' . date('Ymd_His') . '.csv';

        $callback = function () use ($query) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Microsoft Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header row
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
                        $tx->tanggal->format('Y-m-d'),
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
     * Export Transactions as printable PDF view with automatic print triggers.
     */
    public function exportTransactionsPdf(Request $request): Response
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

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        $transactions = $query->get();
        $totalIncome = (float) $transactions->where('type', Transaction::TYPE_INCOME)->sum('amount');
        $totalOutcome = (float) $transactions->where('type', Transaction::TYPE_OUTCOME)->sum('amount');
        $netSaldo = $totalIncome - $totalOutcome;

        return response()->view('reports.transactions_pdf', compact(
            'transactions',
            'totalIncome',
            'totalOutcome',
            'netSaldo'
        ));
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
