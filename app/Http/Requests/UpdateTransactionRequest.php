<?php

namespace App\Http\Requests;

use App\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->boolean('is_free')) {
            $this->merge([
                'amount' => 0,
            ]);
        }
    }

    public function rules(): array
    {
        $transactionId = $this->route('transaction') ? $this->route('transaction')->id : null;
        $isFree = $this->boolean('is_free');

        return [
            'tanggal' => ['required', 'date'],
            'type' => ['required', 'in:' . Transaction::TYPE_INCOME . ',' . Transaction::TYPE_OUTCOME],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'amount' => [$isFree ? 'nullable' : 'required', 'numeric', 'min:0'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'channel' => ['nullable', 'string', 'max:100'],
            'is_free' => ['nullable', 'boolean'],
            'stock_effect' => ['required', 'in:' . Transaction::EFFECT_NONE . ',' . Transaction::EFFECT_IN . ',' . Transaction::EFFECT_OUT],
            'product_id' => ['nullable', 'required_unless:stock_effect,' . Transaction::EFFECT_NONE, 'exists:products,id'],
            'location_id' => ['nullable', 'required_unless:stock_effect,' . Transaction::EFFECT_NONE, 'exists:locations,id'],
            'qty' => ['nullable', 'required_unless:stock_effect,' . Transaction::EFFECT_NONE, 'integer', 'min:1'],
            'invoice_no' => ['nullable', 'string', 'max:100', 'unique:transactions,invoice_no,' . $transactionId],
            'note' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal.required' => 'Tanggal transaksi wajib diisi.',
            'type.required' => 'Tipe transaksi (INCOME/OUTCOME) wajib dipilih.',
            'category.required' => 'Kategori transaksi wajib diisi.',
            'amount.required' => 'Nominal (amount) wajib diisi (atau centang sampel gratis).',
            'amount.min' => 'Nominal transaksi tidak boleh negatif.',
            'product_id.required_unless' => 'Produk parfum wajib dipilih jika transaksi memengaruhi stok.',
            'location_id.required_unless' => 'Lokasi stok wajib dipilih jika transaksi memengaruhi stok.',
            'qty.required_unless' => 'Jumlah (Qty) wajib diisi jika transaksi memengaruhi stok.',
            'qty.min' => 'Jumlah (Qty) minimal 1 pcs.',
            'invoice_no.unique' => 'Nomor invoice ini sudah pernah dicatat dalam sistem. Gunakan nomor invoice yang berbeda.',
        ];
    }
}
