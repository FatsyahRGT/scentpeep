<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Get list of regular customers ordered A-Z.
     */
    public function indexJson(): JsonResponse
    {
        $customers = Customer::orderBy('name', 'asc')->get(['id', 'name']);
        return response()->json($customers);
    }

    /**
     * Store a new regular customer via AJAX.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:customers,name',
        ], [
            'name.required' => 'Nama pelanggan wajib diisi.',
            'name.unique' => 'Nama pelanggan ini sudah terdaftar sebagai pelanggan tetap.',
        ]);

        $customer = Customer::create([
            'name' => trim($validated['name']),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Pelanggan '{$customer->name}' berhasil ditambahkan ke Pelanggan Tetap.",
            'data' => $customer,
        ], 201);
    }

    /**
     * Update an existing regular customer name via AJAX.
     */
    public function update(Request $request, Customer $customer): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:customers,name,' . $customer->id,
        ], [
            'name.required' => 'Nama pelanggan wajib diisi.',
            'name.unique' => 'Nama pelanggan ini sudah digunakan oleh pelanggan lain.',
        ]);

        $customer->update([
            'name' => trim($validated['name']),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Nama pelanggan berhasil diperbarui menjadi '{$customer->name}'.",
            'data' => $customer,
        ]);
    }

    /**
     * Delete a regular customer from database via AJAX.
     */
    public function destroy(Customer $customer): JsonResponse
    {
        $name = $customer->name;
        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => "Pelanggan '{$name}' berhasil dihapus dari daftar Pelanggan Tetap.",
        ]);
    }
}
