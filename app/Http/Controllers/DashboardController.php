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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard overview with role-based scoping and location filters.
     */
    public function index(Request $request): View|RedirectResponse|JsonResponse
    {
        $user = Auth::user();

        // Reseller dialihkan langsung ke dashboard mandiri katalog & stok mereka
        if ($user->isReseller()) {
            return redirect()->route('stocks.reseller');
        }

        $isReseller = false;
        $selectedLocationId = $request->query('location_id');

        // Base Query for Transactions
        $txQuery = Transaction::query();
        if ($selectedLocationId) {
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

        // 3. Low Stock / Critical Warning (< 3 pcs) & Out of Stock (0 pcs)
        $criticalStocksQuery = Stock::with(['product', 'location'])
            ->where('qty', '<', 3)
            ->whereHas('product', fn ($p) => $p->where('is_active', true));

        if ($isReseller) {
            $criticalStocksQuery->where('location_id', $user->location?->id ?? 0);
        } elseif ($selectedLocationId) {
            $criticalStocksQuery->where('location_id', $selectedLocationId);
        }

        $criticalStocksList = $criticalStocksQuery->orderBy('qty', 'asc')->get();

        // Cari produk aktif yang tidak memiliki stok fisik sama sekali (sum == 0 atau tanpa record stock)
        $outOfStockProducts = Product::where('is_active', true)
            ->with(['stocks' => function ($q) use ($selectedLocationId, $isReseller, $user) {
                if ($isReseller) {
                    $q->where('location_id', $user->location?->id ?? 0);
                } elseif ($selectedLocationId) {
                    $q->where('location_id', $selectedLocationId);
                }
            }])
            ->get()
            ->filter(function ($p) {
                return (int) $p->stocks->sum('qty') === 0;
            });

        $existingProductIds = $criticalStocksList->pluck('product_id')->toArray();
        foreach ($outOfStockProducts as $oosp) {
            if (!in_array($oosp->id, $existingProductIds)) {
                $placeholderStock = new Stock([
                    'product_id' => $oosp->id,
                    'location_id' => null,
                    'qty' => 0,
                ]);
                $placeholderStock->setRelation('product', $oosp);
                $placeholderStock->setRelation('location', null);
                $criticalStocksList->push($placeholderStock);
            }
        }

        $criticalStocks = $criticalStocksList->sortBy('qty')->take(10)->values();

        // 4. Best Selling Products (Varian Parfum Terlaris berdasarkan data Invoice Penjualan, dengan fallback ke Transaksi jika belum ada invoice)
        $bestSellersQuery = Invoice::whereNotNull('product_id')
            ->select(
                'product_id',
                DB::raw('SUM(qty) as total_sold'),
                DB::raw('SUM(total_price) as total_revenue'),
                DB::raw('SUM(total_price) as total_omset')
            )
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->with('product');

        if ($isReseller) {
            $userLocId = $user->location?->id;
            $bestSellersQuery->where(function ($q) use ($user, $userLocId) {
                $q->where('created_by', $user->id);
                if ($userLocId) {
                    $q->orWhere('location_id', $userLocId);
                }
            });
        } elseif ($selectedLocationId) {
            $bestSellersQuery->where('location_id', $selectedLocationId);
        }

        $bestSellers = $bestSellersQuery->take(5)->get();

        // Fallback ke tabel Transaksi jika belum ada data Invoice
        if ($bestSellers->isEmpty()) {
            $txBestSellersQuery = Transaction::whereNotNull('product_id')
                ->where('stock_effect', '!=', Transaction::EFFECT_IN)
                ->where('category', '!=', 'Pembelian Stok')
                ->where(function ($q) {
                    $q->where('category', 'Penjualan')
                      ->orWhere('stock_effect', Transaction::EFFECT_OUT)
                      ->orWhere('type', Transaction::TYPE_INCOME);
                })
                ->select(
                    'product_id',
                    DB::raw('SUM(COALESCE(qty, 1)) as total_sold'),
                    DB::raw('SUM(amount) as total_revenue'),
                    DB::raw('SUM(amount) as total_omset')
                )
                ->groupBy('product_id')
                ->orderByDesc('total_sold')
                ->with('product');

            if ($isReseller) {
                $userLocId = $user->location?->id;
                $txBestSellersQuery->where(function ($q) use ($user, $userLocId) {
                    $q->where('created_by', $user->id);
                    if ($userLocId) {
                        $q->orWhere('location_id', $userLocId);
                    }
                });
            } elseif ($selectedLocationId) {
                $txBestSellersQuery->where('location_id', $selectedLocationId);
            }

            $bestSellers = $txBestSellersQuery->take(5)->get();
        }

        // 5. Recent Transactions
        $recentTransactions = (clone $txQuery)->with(['product', 'location', 'creator'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->take(8)
            ->get();

        // 6. Monthly Cashflow Trend (Last 6 Months) - Optimized single query with cross-DB compatibility
        $sixMonthsAgo = Carbon::now()->startOfMonth()->subMonths(5);
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
            $monthDate = Carbon::now()->startOfMonth()->subMonths($i);
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
        $salesDistribution = $this->getSalesDistribution();

        // 9. Tren Penjualan per Seller (Default 1 Tahun, Mendukung Filter Kustom Tahun & Bulan)
        $sellerTrendData = $this->getSellerTrendData($request, $salesDistribution);
        $topSeller = $sellerTrendData['period_top_seller'] ?? $salesDistribution->first();

        if ($request->ajax() || $request->has('ajax_trend') || $request->wantsJson()) {
            return response()->json($sellerTrendData);
        }

        // 10. Riwayat Invoice Terbaru
        $recentInvoices = Invoice::with(['product', 'location'])
            ->orderByDesc(DB::raw('COALESCE(tanggal, DATE(created_at))'))
            ->orderByDesc('id')
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

    /**
     * Dashboard Kontrol & Monitoring Sales: performa tim penjualan, perbandingan multi-seller, dan riwayat invoice sales.
     */
    public function sales(Request $request): View|JsonResponse
    {
        $user = Auth::user();

        // 1. Distribusi Penjualan Sales
        $salesDistribution = $this->getSalesDistribution();

        // 2. Tren Penjualan per Seller (Default 1 Tahun, Mendukung Filter Kustom Tahun & Bulan)
        $sellerTrendData = $this->getSellerTrendData($request, $salesDistribution);
        $topSeller = $sellerTrendData['period_top_seller'] ?? $salesDistribution->first();

        if ($request->ajax() || $request->has('ajax_trend') || $request->wantsJson()) {
            return response()->json($sellerTrendData);
        }

        // 3. Best Sellers Products
        $bestSellers = Invoice::select(
                'product_id',
                DB::raw('SUM(qty) as total_sold'),
                DB::raw('SUM(total_price) as total_revenue')
            )
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->with('product')
            ->take(6)
            ->get();

        // 4. Riwayat Invoice Terbaru per Sales
        $recentInvoices = Invoice::with(['product', 'location'])
            ->orderByDesc(DB::raw('COALESCE(tanggal, DATE(created_at))'))
            ->orderByDesc('id')
            ->take(10)
            ->get();

        // Metrik Ringkasan Sales
        $summary = [
            'total_sales_team' => $salesDistribution->count(),
            'active_sellers_count' => $salesDistribution->where('total_qty', '>', 0)->count(),
            'total_team_sold_qty' => (int) $salesDistribution->sum('total_qty'),
            'total_team_omset' => (float) $salesDistribution->sum('total_omset'),
            'total_team_invoices' => (int) $salesDistribution->sum('invoice_count'),
        ];

        return view('dashboards.sales', compact(
            'salesDistribution',
            'sellerTrendData',
            'topSeller',
            'bestSellers',
            'recentInvoices',
            'summary'
        ));
    }

    /**
     * Helper untuk membangun data Tren Penjualan per Seller (1 Tahun Penuh atau Harian untuk Bulan Terpilih).
     */
    protected function getSellerTrendData(Request $request, \Illuminate\Support\Collection $salesDistribution): array
    {
        $isSqlite = DB::connection()->getDriverName() === 'sqlite';
        $yearExtractSql = $isSqlite
            ? "strftime('%Y', COALESCE(tanggal, created_at))"
            : "DATE_FORMAT(COALESCE(tanggal, created_at), '%Y')";

        // Ambil daftar tahun unik yang memiliki data transaksi invoice
        $availableYears = Invoice::selectRaw("{$yearExtractSql} as y")
            ->distinct()
            ->pluck('y')
            ->filter()
            ->map(fn($y) => (int)$y)
            ->push((int) Carbon::now()->year)
            ->unique()
            ->sortDesc()
            ->values()
            ->all();

        // Tahun default: tahun transaksi invoice paling baru yang tersedia, atau tahun sekarang
        $latestInvoiceYear = Invoice::selectRaw("{$yearExtractSql} as y")->orderByDesc('y')->value('y');
        $defaultYear = $latestInvoiceYear ? (int) $latestInvoiceYear : (int) Carbon::now()->year;

        $selectedYear = $request->filled('sales_year') ? (int) $request->sales_year : $defaultYear;
        $selectedMonth = $request->filled('sales_month') && is_numeric($request->sales_month) && $request->sales_month >= 1 && $request->sales_month <= 12
            ? (int) $request->sales_month
            : null;

        $monthsList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $monthAbbr = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        $chartSellers = $salesDistribution->take(8);
        $labels = [];
        $dateKeys = [];

        if ($selectedMonth) {
            // Mode Harian: Rincian Hari 1 s/d Hari Terakhir di Bulan Terpilih
            $daysInMonth = Carbon::create($selectedYear, $selectedMonth, 1)->daysInMonth;
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $key = sprintf('%04d-%02d-%02d', $selectedYear, $selectedMonth, $d);
                $dateKeys[] = $key;
                $labels[] = sprintf('%02d %s', $d, $monthAbbr[$selectedMonth]);
            }

            $dateColFormat = $isSqlite
                ? "strftime('%Y-%m-%d', COALESCE(tanggal, created_at))"
                : "DATE_FORMAT(COALESCE(tanggal, created_at), '%Y-%m-%d')";

            $invoicesData = Invoice::whereYear(DB::raw('COALESCE(tanggal, created_at)'), $selectedYear)
                ->whereMonth(DB::raw('COALESCE(tanggal, created_at)'), $selectedMonth)
                ->selectRaw("{$dateColFormat} as dt_key, sales_name, SUM(qty) as total_qty, SUM(total_price) as total_omset")
                ->groupBy('dt_key', 'sales_name')
                ->get();

            $periodTitle = "Tren Penjualan per Seller (Bulan " . $monthsList[$selectedMonth] . " " . $selectedYear . ")";
            $periodSubtitle = "Rincian penjualan harian per tanggal antar reseller & tim sales di bulan " . $monthsList[$selectedMonth] . " " . $selectedYear;
        } else {
            // Mode 1 Tahun Penuh: 12 Bulan (Januari s/d Desember)
            for ($m = 1; $m <= 12; $m++) {
                $key = sprintf('%04d-%02d', $selectedYear, $m);
                $dateKeys[] = $key;
                $labels[] = $monthAbbr[$m] . ' ' . $selectedYear;
            }

            $dateColFormat = $isSqlite
                ? "strftime('%Y-%m', COALESCE(tanggal, created_at))"
                : "DATE_FORMAT(COALESCE(tanggal, created_at), '%Y-%m')";

            $invoicesData = Invoice::whereYear(DB::raw('COALESCE(tanggal, created_at)'), $selectedYear)
                ->selectRaw("{$dateColFormat} as dt_key, sales_name, SUM(qty) as total_qty, SUM(total_price) as total_omset")
                ->groupBy('dt_key', 'sales_name')
                ->get();

            $periodTitle = "Tren Penjualan per Seller (Tahun " . $selectedYear . " - 1 Tahun Penuh)";
            $periodSubtitle = "Komparasi volume penjualan (pcs) & omset (Rp) antar reseller & tim sales per bulan di tahun " . $selectedYear;
        }

        $sellerDatasets = [];
        $totalPeriodQtyAll = 0;
        $totalPeriodOmsetAll = 0;

        foreach ($chartSellers as $seller) {
            $sellerName = $seller['name'];
            $palette = $seller['palette'];
            $qtySeries = [];
            $omsetSeries = [];
            $sellerPeriodQty = 0;
            $sellerPeriodOmset = 0;

            foreach ($dateKeys as $k) {
                $rec = $invoicesData->first(fn ($item) => $item->dt_key === $k && strcasecmp($item->sales_name, $sellerName) === 0);
                $qty = (int) ($rec?->total_qty ?? 0);
                $omset = (float) ($rec?->total_omset ?? 0);
                $qtySeries[] = $qty;
                $omsetSeries[] = $omset;
                $sellerPeriodQty += $qty;
                $sellerPeriodOmset += $omset;
            }

            $totalPeriodQtyAll += $sellerPeriodQty;
            $totalPeriodOmsetAll += $sellerPeriodOmset;

            $sellerDatasets[] = [
                'name' => $sellerName,
                'qty' => $qtySeries,
                'omset' => $omsetSeries,
                'total_qty' => $sellerPeriodQty,
                'total_omset' => $sellerPeriodOmset,
                'color' => $palette['pill_bg'],
                'text_color' => $palette['badge_text'],
                'badge_bg' => $palette['badge_bg'],
                'border' => $palette['border'],
                'bg' => $palette['bg'],
            ];
        }

        $periodTopSeller = collect($sellerDatasets)->sortByDesc('total_qty')->first();

        return [
            'labels' => $labels,
            'datasets' => $sellerDatasets,
            'has_data' => $invoicesData->isNotEmpty(),
            'total_volume' => $totalPeriodQtyAll,
            'total_omset' => $totalPeriodOmsetAll,
            'available_years' => $availableYears,
            'selected_year' => $selectedYear,
            'selected_month' => $selectedMonth,
            'period_title' => $periodTitle,
            'period_subtitle' => $periodSubtitle,
            'period_top_seller' => $periodTopSeller,
            'months_list' => $monthsList,
        ];
    }

    /**
     * Dapatkan data distribusi penjualan per sales/seller (diurutkan berdasarkan total unit).
     */
    private function getSalesDistribution(): \Illuminate\Support\Collection
    {
        $salesStats = Invoice::selectRaw('sales_name, SUM(qty) as total_qty, SUM(total_price) as total_omset, COUNT(*) as invoice_count')
            ->groupBy('sales_name')
            ->get()
            ->keyBy('sales_name');

        $staffUserNames = User::where('is_active', true)
            ->where('role', '!=', User::ROLE_ADMIN)
            ->whereNotIn('email', ['staff@scentpeep.com', 'admin@scentpeep.com'])
            ->whereNotIn('name', ['Admin Scentpeep', 'Staff Scentpeep', 'admin', 'staff'])
            ->pluck('name');

        $allUsersByName = User::select(['id', 'name', 'role'])->get()->keyBy(fn ($u) => strtolower(trim($u->name)));

        $allSalesNames = $staffUserNames
            ->merge($salesStats->keys())
            ->reject(fn ($n) => in_array($n, ['Admin Scentpeep', 'Staff Scentpeep', 'admin', 'staff']))
            ->unique()
            ->values();

        return $allSalesNames->map(function ($name) use ($salesStats, $allUsersByName) {
            $stat = $salesStats->get($name);
            $user = $allUsersByName->get(strtolower(trim($name)));
            $role = $user ? $user->role : 'staff';
            $roleLabel = match (strtolower($role)) {
                'reseller' => 'Reseller',
                'admin' => 'Admin',
                default => 'Sales',
            };

            return [
                'name' => $name,
                'role' => $role,
                'role_label' => $roleLabel,
                'total_qty' => (int) ($stat->total_qty ?? 0),
                'total_omset' => (float) ($stat->total_omset ?? 0),
                'invoice_count' => (int) ($stat->invoice_count ?? 0),
                'palette' => Location::paletteForName($name),
            ];
        })->sortByDesc('total_qty')->values();
    }
}
