<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(Request $request): View
    {
        $query = Location::with(['stocks', 'user'])->orderBy('name');

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where('name', 'like', $search);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $locations = $query->paginate(15)->withQueryString();

        return view('locations.index', compact('locations'));
    }

    public function create(): View
    {
        $resellers = User::where('role', User::ROLE_RESELLER)->orderBy('name')->get();

        return view('locations.create', compact('resellers'));
    }

    public function store(StoreLocationRequest $request): RedirectResponse
    {
        Location::create([
            'name' => $request->name,
            'type' => $request->type,
            'user_id' => $request->type === Location::TYPE_RESELLER ? $request->user_id : null,
        ]);

        return redirect()->route('locations.index')
            ->with('success', 'Lokasi inventori baru berhasil ditambahkan!');
    }

    public function edit(Location $location): View
    {
        $resellers = User::where('role', User::ROLE_RESELLER)->orderBy('name')->get();

        return view('locations.edit', compact('location', 'resellers'));
    }

    public function update(UpdateLocationRequest $request, Location $location): RedirectResponse
    {
        $location->update([
            'name' => $request->name,
            'type' => $request->type,
            'user_id' => $request->type === Location::TYPE_RESELLER ? $request->user_id : null,
        ]);

        return redirect()->route('locations.index')
            ->with('success', 'Data lokasi berhasil diperbarui!');
    }

    public function destroy(Location $location): RedirectResponse
    {
        if ($location->stocks()->where('qty', '>', 0)->exists()) {
            return redirect()->route('locations.index')
                ->with('error', 'Lokasi tidak dapat dihapus karena masih memiliki unit stok aktif!');
        }

        $location->delete();

        return redirect()->route('locations.index')
            ->with('success', 'Lokasi stok berhasil dihapus!');
    }
}
