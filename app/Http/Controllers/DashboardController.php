<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Transaction;
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

        // 1. Dynamic Financial Totals (All Time & Current Month)
        $totalIncome = (float) (clone $txQuery)->where('type', Transaction::TYPE_INCOME)->sum('amount');
        $totalOutcome = (float) (clone $txQuery)->where('type', Transaction::TYPE_OUTCOME)->sum('amount');
        $netSaldo = $totalIncome - $totalOutcome;

        $startOfMonth = Carbon::now()->startOfMonth();
        $thisMonthIncome = (float) (clone $txQuery)->where('type', Transaction::TYPE_INCOME)->whereDate('tanggal', '>=', $startOfMonth)->sum('amount');
        $thisMonthOutcome = (float) (clone $txQuery)->where('type', Transaction::TYPE_OUTCOME)->whereDate('tanggal', '>=', $startOfMonth)->sum('amount');
        $thisMonthTxCount = (clone $txQuery)->whereDate('tanggal', '>=', $startOfMonth)->count();

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

        // 6. Monthly Cashflow Trend (Last 6 Months)
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = Carbon::now()->subMonths($i);
            $mStart = $monthDate->copy()->startOfMonth();
            $mEnd = $monthDate->copy()->endOfMonth();

            $inc = (float) (clone $txQuery)->where('type', Transaction::TYPE_INCOME)
                ->whereBetween('tanggal', [$mStart, $mEnd])
                ->sum('amount');

            $out = (float) (clone $txQuery)->where('type', Transaction::TYPE_OUTCOME)
                ->whereBetween('tanggal', [$mStart, $mEnd])
                ->sum('amount');

            $monthlyTrend[] = [
                'label' => $monthDate->translatedFormat('M Y'),
                'income' => $inc,
                'outcome' => $out,
            ];
        }

        $totalProductsCount = Product::where('is_active', true)->count();
        $allLocations = $isReseller ? collect([]) : Location::orderBy('name')->get();

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
            'isReseller'
        ));
    }
}
