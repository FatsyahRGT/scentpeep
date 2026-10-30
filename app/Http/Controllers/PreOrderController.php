<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\PreOrder;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PreOrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = PreOrder::with(['product', 'location', 'creator'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        $preOrders = $query->paginate(15)->withQueryString();

        $metrics = [
            'total_pending_qty' => (int) PreOrder::where('status', PreOrder::STATUS_PENDING)->sum('qty'),
            'total_pending_count' => PreOrder::where('status', PreOrder::STATUS_PENDING)->count(),
            'total_completed_qty' => (int) PreOrder::where('status', PreOrder::STATUS_COMPLETED)->sum('qty'),
        ];

        $locations = Location::orderBy('name')->get();

        return view('pre_orders.index', compact('preOrders', 'metrics', 'locations'));
    }

    public function create(): View
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('pre_orders.create', compact('products', 'locations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'po_number' => 'nullable|string|max:100',
            'product_id' => 'required|exists:products,id',
            'location_id' => 'required|exists:locations,id',
            'qty' => 'required|integer|min:1',
            'customer_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        PreOrder::create([
            'po_number' => $validated['po_number'] ?: 'PO-' . strtoupper(uniqid()),
            'product_id' => $validated['product_id'],
            'location_id' => $validated['location_id'],
            'qty' => $validated['qty'],
            'status' => PreOrder::STATUS_PENDING,
            'customer_name' => $validated['customer_name'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('pre-orders.index')->with('success', 'Data Pre-Order (PO) berhasil dicatat sebagai Stok Pending di luar stok utama.');
    }

    public function complete(PreOrder $preOrder): RedirectResponse
    {
        if ($preOrder->status === PreOrder::STATUS_COMPLETED) {
            return redirect()->back()->with('info', 'PO ini sudah berstatus selesai sebelumnya.');
        }

        DB::transaction(function () use ($preOrder) {
            $preOrder->update([
                'status' => PreOrder::STATUS_COMPLETED,
                'completed_at' => now(),
            ]);

            $stock = Stock::firstOrCreate(
                ['product_id' => $preOrder->product_id, 'location_id' => $preOrder->location_id],
                ['qty' => 0]
            );

            $stock->increment('qty', $preOrder->qty);
        });

        return redirect()->route('pre-orders.index')->with('success', "PO {$preOrder->po_number} telah selesai. Sebanyak {$preOrder->qty} pcs parfum telah ditambahkan ke Stok Utama ({$preOrder->location->name}).");
    }

    public function destroy(PreOrder $preOrder): RedirectResponse
    {
        DB::transaction(function () use ($preOrder) {
            if ($preOrder->status === PreOrder::STATUS_COMPLETED) {
                $stock = Stock::where('product_id', $preOrder->product_id)
                    ->where('location_id', $preOrder->location_id)
                    ->first();

                if ($stock && $stock->qty >= $preOrder->qty) {
                    $stock->decrement('qty', $preOrder->qty);
                }
            }

            $preOrder->delete();
        });

        return redirect()->route('pre-orders.index')->with('success', 'Data Pre-Order berhasil dihapus.');
    }
}
