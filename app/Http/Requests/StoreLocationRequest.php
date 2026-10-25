<?php

namespace App\Http\Requests;

use App\Models\Location;
use Illuminate\Foundation\Http\FormRequest;

class StoreLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:locations,name'],
            'type' => ['required', 'in:' . Location::TYPE_WAREHOUSE . ',' . Location::TYPE_RESELLER],
            'user_id' => ['nullable', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama lokasi wajib diisi.',
            'name.unique' => 'Nama lokasi ini sudah ada dalam sistem.',
            'type.required' => 'Tipe lokasi wajib dipilih.',
            'type.in' => 'Tipe lokasi harus gudang (warehouse) atau reseller.',
            'user_id.exists' => 'Pengguna reseller yang dipilih tidak valid.',
        ];
    }
}
