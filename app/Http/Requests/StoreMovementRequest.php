<?php

namespace App\Http\Requests;

use App\Models\StockMovement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'location_id' => ['required', 'exists:locations,id'],
            'product_id' => ['required', 'exists:products,id'],
            'movement_type' => ['required', Rule::in([StockMovement::TYPE_IN, StockMovement::TYPE_OUT])],
            'reason' => ['required', Rule::in([
                StockMovement::REASON_PEMBELIAN,
                StockMovement::REASON_PENJUALAN,
                StockMovement::REASON_TESTER,
                StockMovement::REASON_SAMPLE,
                StockMovement::REASON_ADJUSTMENT,
            ])],
            'qty' => ['required', 'integer', 'min:1'],
            'invoice_no' => ['nullable', 'string', 'max:255'],
            'buyer_note' => ['nullable', 'string', 'max:255'],
            'movement_date' => ['required', 'date'],
            'allow_negative' => ['nullable', 'boolean'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                // For PENJUALAN, at least invoice_no or buyer_note should be filled
                if ($this->reason === StockMovement::REASON_PENJUALAN) {
                    if (empty($this->invoice_no) && empty($this->buyer_note)) {
                        $validator->errors()->add(
                            'buyer_note',
                            'Untuk transaksi PENJUALAN, harap isi minimal salah satu: No Invoice atau Catatan Pembeli.'
                        );
                    }
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'location_id.required' => 'Lokasi wajib dipilih.',
            'product_id.required' => 'Produk wajib dipilih.',
            'movement_type.required' => 'Tipe pergerakan (IN/OUT) wajib dipilih.',
            'reason.required' => 'Alasan pergerakan stok wajib dipilih.',
            'qty.required' => 'Jumlah stok (qty) wajib diisi.',
            'qty.min' => 'Jumlah stok minimal 1.',
            'movement_date.required' => 'Tanggal transaksi wajib diisi.',
        ];
    }
}
