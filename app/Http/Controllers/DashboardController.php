<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Location;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\Stock;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard overview with role-based scoping and location filters.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $isReseller = $user->isReseller();
        $selectedLocationId = $isReseller ? $user->location?->id : $request->query('location_id');

        // Base Query for Transactions
        $txQuery = Transaction::query();
        if ($isReseller) {
            $userLocId = $user->location?->id;
            $txQuery->where(function ($q) use ($user, $userLocId) {
                $q->where('created_by', $user->id);
                if ($userLocId) {
                    $q->orWhere('location_id', $userLocId);
                }
            });
        } elseif ($selectedLocationId) {
            $txQuery->where('location_id', $selectedLocationId);
        }

        // 1. Dynamic Financial Totals (All Time & Current Month) - Optimized with GROUP BY
        $summaries = (clone $txQuery)
            ->selectRaw('type, SUM(amount) as total')
            ->groupBy('type')
            ->reorder() // Remove existing ORDER BY from base query
            ->pluck('total', 'type');
        
        $totalIncome = (float) ($summaries[Transaction::TYPE_INCOME] ?? 0);
        $totalOutcome = (float) ($summaries[Transaction::TYPE_OUTCOME] ?? 0);
        $netSaldo = $totalIncome - $totalOutcome;

        $startOfMonth = Carbon::now()->startOfMonth();
        $monthSummaries = (clone $txQuery)
            ->whereDate('tanggal', '>=', $startOfMonth)
            ->selectRaw('type, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('type')
            ->reorder() // Remove existing ORDER BY from base query
            ->get()
            ->keyBy('type');
        
        $thisMonthIncome = (float) ($monthSummaries[Transaction::TYPE_INCOME]->total ?? 0);
        $thisMonthOutcome = (float) ($monthSummaries[Transaction::TYPE_OUTCOME]->total ?? 0);
        $thisMonthTxCount = $monthSummaries->sum('count');

        // 2. Stock Summary by Location
        if ($isReseller) {
            $locations = $user->location ? Location::where('id', $user->location->id)->with('stocks')->get() : collect([]);
        } else {
            $locations = Location::with(['stocks'])->orderBy('name')->get();
        }

        $locationStockSummaries = [];
        $totalSystemStock = 0;

        foreach ($locations as $loc) {
            $sumQty = (int) $loc->stocks->sum('qty');
            $locationStockSummaries[] = [
                'id' => $loc->id,
                'name' => $loc->name,
                'type' => $loc->type,
                'total_qty' => $sumQty,
                'sku_count' => $loc->stocks->where('qty', '>', 0)->count(),
                'palette' => $loc->getColorPalette(),
            ];
            $totalSystemStock += $sumQty;
        }

        // 3. Low Stock / Critical Warning (< 3 pcs)
        $criticalStocksQuery = Stock::with(['product', 'location'])
            ->where('qty', '<', 3)
            ->whereHas('product', fn ($p) => $p->where('is_active', true));

        if ($isReseller) {
            $criticalStocksQuery->where('location_id', $user->location?->id ?? 0);
        } elseif ($selectedLocationId) {
            $criticalStocksQuery->where('location_id', $selectedLocationId);
        }

        $criticalStocks = $criticalStocksQuery->orderBy('qty', 'asc')->take(10)->get();

        // 4. Best Selling Products (Varian Parfum Terlaris berdasarkan transaksi penjualan jenis parfum)
        $bestSellersQuery = Transaction::whereNotNull('product_id')
            ->where('stock_effect', '!=', Transaction::EFFECT_IN)
            ->where('category', '!=', 'Pembelian Stok')
            ->where(function ($q) {
                $q->where('category', 'Penjualan')
                  ->orWhere('stock_effect', Transaction::EFFECT_OUT)
                  ->orWhere('type', Transaction::TYPE_INCOME);
            })
            ->select('product_id', DB::raw('SUM(COALESCE(qty, 1)) as total_sold'), DB::raw('SUM(amount) as total_revenue'))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->with('product');

        if ($isReseller) {
            $userLocId = $user->location?->id;
            $bestSellersQuery->where(function ($q) use ($user, $userLocId) {
                $q->where('created_by', $user->id);
                if ($userLocId) $q->orWhere('location_id', $userLocId);
            });
        } elseif ($selectedLocationId) {
            $bestSellersQuery->where('location_id', $selectedLocationId);
        }

        $bestSellers = $bestSellersQuery->take(5)->get();

        // 5. Recent Transactions
        $recentTransactions = (clone $txQuery)->with(['product', 'location', 'creator'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->take(8)
            ->get();

        // 6. Monthly Cashflow Trend (Last 6 Months) - Optimized single query with cross-DB compatibility
        $sixMonthsAgo = Carbon::now()->subMonths(5)->startOfMonth();
        $dateFormat = DB::connection()->getDriverName() === 'sqlite' 
            ? "strftime('%Y-%m', tanggal)" 
            : "DATE_FORMAT(tanggal, '%Y-%m')";
        
        $trendData = (clone $txQuery)
            ->where('tanggal', '>=', $sixMonthsAgo)
            ->selectRaw("{$dateFormat} as month, type, SUM(amount) as total")
            ->groupBy('month', 'type')
            ->reorder() // Remove existing ORDER BY from base query
            ->get()
            ->groupBy('month');
        
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = Carbon::now()->subMonths($i);
            $monthKey = $monthDate->format('Y-m');
            $monthGroup = $trendData->get($monthKey, collect());
            
            $inc = (float) $monthGroup->where('type', Transaction::TYPE_INCOME)->first()?->total ?? 0;
            $out = (float) $monthGroup->where('type', Transaction::TYPE_OUTCOME)->first()?->total ?? 0;

            $monthlyTrend[] = [
                'label' => $monthDate->translatedFormat('M Y'),
                'income' => $inc,
                'outcome' => $out,
            ];
        }

        $totalProductsCount = Product::where('is_active', true)->count();
        $allLocations = $isReseller ? collect([]) : Location::orderBy('name')->get();

        // 7. Monitoring Pengadaan Barang Mentah (Box & Botol)
        $rawMaterialBoxStock = (int) RawMaterial::where('category', RawMaterial::CATEGORY_BOX)->sum('stock_qty');
        $rawMaterialBotolStock = (int) RawMaterial::where('category', RawMaterial::CATEGORY_BOTOL)->sum('stock_qty');
        $rawMaterialsList = RawMaterial::orderBy('category')->orderBy('name')->get();

        // 8. Distribusi Penjualan Sales (Dinamis dari User aktif, Lokasi, & Invoice)
        $salesStats = Invoice::selectRaw('sales_name, SUM(qty) as total_qty, SUM(total_price) as total_omset, COUNT(*) as invoice_count')
            ->groupBy('sales_name')
            ->get()
            ->keyBy('sales_name');

        // Prioritas nama sales: tim lapangan / reseller / staff non-admin + nama invoice aktif
        $staffUserNames = User::where('is_active', true)
            ->where('role', '!=', User::ROLE_ADMIN)
            ->pluck('name');

        $locationTeamNames = Location::pluck('name');

        $allSalesNames = $staffUserNames
            ->merge($locationTeamNames)
            ->merge($salesStats->keys())
            ->unique()
            ->values();

        // Jika admin belum punya penjualan, jangan penuhi kartu dengan akun admin sistem
        if (!$salesStats->has('Admin Scentpeep') && $allSalesNames->count() > 1) {
            $allSalesNames = $allSalesNames->reject(fn($n) => $n === 'Admin Scentpeep' || $n === 'admin')->values();
        }

        $salesDistribution = $allSalesNames->map(function ($name) use ($salesStats) {
            $stat = $salesStats->get($name);
            return [
                'name' => $name,
                'total_qty' => (int) ($stat->total_qty ?? 0),
                'total_omset' => (float) ($stat->total_omset ?? 0),
                'invoice_count' => (int) ($stat->invoice_count ?? 0),
                'palette' => Location::paletteForName($name),
            ];
        })->sortByDesc('total_qty')->values();

        // 9. Tren Penjualan per Seller (6 Bulan Terakhir untuk Komparasi Performa Multi-Seller)
        $invoiceDateFormat = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', created_at)"
            : "DATE_FORMAT(created_at, '%Y-%m')";

        $monthlyInvoices = Invoice::where('created_at', '>=', $sixMonthsAgo->toDateTimeString())
            ->selectRaw("{$invoiceDateFormat} as ym, sales_name, SUM(qty) as total_qty, SUM(total_price) as total_omset")
            ->groupBy('ym', 'sales_name')
            ->orderBy('ym')
            ->get();

        $sellerTrendMonths = [];
        $sellerYmKeys = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i);
            $ym = $m->format('Y-m');
            $sellerYmKeys[] = $ym;
            $sellerTrendMonths[] = $m->translatedFormat('M Y');
        }

        // Tampilkan penjual utama (maksimal 6 seller teratas)
        $chartSellers = $salesDistribution->take(6);
        $sellerDatasets = [];

        foreach ($chartSellers as $seller) {
            $sellerName = $seller['name'];
            $palette = $seller['palette'];
            $qtySeries = [];
            $omsetSeries = [];
            $totalPeriodQty = 0;
            $totalPeriodOmset = 0;

            foreach ($sellerYmKeys as $ym) {
                $rec = $monthlyInvoices->first(fn ($item) => $item->ym === $ym && strcasecmp($item->sales_name, $sellerName) === 0);
                $qty = (int) ($rec?->total_qty ?? 0);
                $omset = (float) ($rec?->total_omset ?? 0);
                $qtySeries[] = $qty;
                $omsetSeries[] = $omset;
                $totalPeriodQty += $qty;
                $totalPeriodOmset += $omset;
            }

            $sellerDatasets[] = [
                'name' => $sellerName,
                'qty' => $qtySeries,
                'omset' => $omsetSeries,
                'total_qty' => $totalPeriodQty,
                'total_omset' => $totalPeriodOmset,
                'color' => $palette['pill_bg'],
                'text_color' => $palette['badge_text'],
                'badge_bg' => $palette['badge_bg'],
                'border' => $palette['border'],
                'bg' => $palette['bg'],
            ];
        }

        $topSeller = $chartSellers->first();

        $sellerTrendData = [
            'labels' => $sellerTrendMonths,
            'datasets' => $sellerDatasets,
            'has_data' => $monthlyInvoices->isNotEmpty(),
            'total_volume' => (int) $monthlyInvoices->sum('total_qty'),
        ];

        // 10. Riwayat Invoice Terbaru
        $recentInvoices = Invoice::with(['product', 'location'])
            ->latest()
            ->take(6)
            ->get();

        return view('dashboard', compact(
            'totalIncome',
            'totalOutcome',
            'netSaldo',
            'thisMonthIncome',
            'thisMonthOutcome',
            'thisMonthTxCount',
            'locationStockSummaries',
            'totalSystemStock',
            'criticalStocks',
            'bestSellers',
            'recentTransactions',
            'monthlyTrend',
            'totalProductsCount',
            'allLocations',
            'selectedLocationId',
            'isReseller',
            'rawMaterialBoxStock',
            'rawMaterialBotolStock',
            'rawMaterialsList',
            'salesDistribution',
            'recentInvoices',
            'sellerTrendData',
            'topSeller'
        ));
    }
}
