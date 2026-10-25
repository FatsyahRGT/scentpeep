<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from_location_id' => ['required', 'exists:locations,id'],
            'to_location_id' => ['required', 'exists:locations,id', 'different:from_location_id'],
            'product_id' => ['required', 'exists:products,id'],
            'qty' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'from_location_id.required' => 'Lokasi asal wajib dipilih.',
            'to_location_id.required' => 'Lokasi tujuan wajib dipilih.',
            'to_location_id.different' => 'Lokasi tujuan tidak boleh sama dengan lokasi asal.',
            'product_id.required' => 'Produk parfum wajib dipilih.',
            'qty.required' => 'Jumlah (Qty) transfer wajib diisi.',
            'qty.min' => 'Jumlah (Qty) transfer minimal 1 pcs.',
        ];
    }
}
