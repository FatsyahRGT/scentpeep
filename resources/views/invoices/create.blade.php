@extends('layouts.app')

@section('title', 'Buat Invoice Penjualan')
@section('page-header', 'Penerbitan Invoice Penjualan Parfum')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm" style="border: 1px solid var(--color-border) !important;">
            <div class="card-header bg-white py-3 border-bottom">
                <h3 class="card-title font-serif fw-bold text-dark m-0">Formulir Invoice Penjualan</h3>
                <small class="text-muted d-block">Pilih varian parfum, sales, kuantitas, serta lokasi untuk otomatis memotong stok fisik.</small>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('invoices.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <!-- Nomor Invoice Custom -->
                        <div class="col-md-6">
                            <label for="invoice_no" class="form-label small fw-semibold text-muted">Nomor Invoice (Custom)</label>
                            <input type="text" name="invoice_no" id="invoice_no" class="form-control font-mono @error('invoice_no') is-invalid @enderror" placeholder="Contoh: INV-001 / SC-88 (Opsional)" value="{{ old('invoice_no') }}">
                            <small class="form-text text-muted">Bisa diisi manual sesuai nomor nota/struk Anda.</small>
                            @error('invoice_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Nama Sales -->
                        <div class="col-md-6">
                            <label for="sales_name" class="form-label small fw-semibold text-muted">Nama Sales <span class="text-danger">*</span></label>
                            <select name="sales_name" id="sales_name" class="form-select @error('sales_name') is-invalid @enderror" required>
                                <option value="">-- Pilih Sales --</option>
                                @foreach($salesOptions as $sales)
                                <option value="{{ $sales }}" {{ old('sales_name') === $sales ? 'selected' : '' }}>{{ $sales }}</option>
                                @endforeach
                            </select>
                            @error('sales_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Nama Customer -->
                        <div class="col-md-12">
                            <label for="customer_name" class="form-label small fw-semibold text-muted">Nama Customer <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" id="customer_name" class="form-control @error('customer_name') is-invalid @enderror" placeholder="Masukkan nama pembeli..." value="{{ old('customer_name') }}" required>
                            @error('customer_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Varian Parfum -->
                        <div class="col-md-8">
                            <label for="product_id" class="form-label small fw-semibold text-muted">Varian Parfum <span class="text-danger">*</span></label>
                            <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" required onchange="calculateSuggestedPrice()">
                                <option value="">-- Pilih Varian Parfum --</option>
                                @foreach($products as $prod)
                                <option value="{{ $prod->id }}" data-price="{{ $prod->sell_price ?? 0 }}" {{ (old('product_id', request('product_id')) == $prod->id) ? 'selected' : '' }}>
                                    {{ $prod->name }} ({{ $prod->code ?? '-' }}) - Rp {{ number_format($prod->sell_price ?? 0, 0, ',', '.') }}
                                </option>
                                @endforeach
                            </select>
                            @error('product_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Qty yang dijual -->
                        <div class="col-md-4">
                            <label for="qty" class="form-label small fw-semibold text-muted">Qty Terjual <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="qty" id="qty" min="1" class="form-control @error('qty') is-invalid @enderror" value="{{ old('qty', 1) }}" required oninput="calculateSuggestedPrice()">
                                <span class="input-group-text bg-light text-muted small">pcs</span>
                            </div>
                            @error('qty')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Pilihan Potong Stok (IWAN / RIZKY) -->
                        <div class="col-md-6">
                            <label for="location_id" class="form-label small fw-semibold text-muted">Potong dari Stok Lokasi <span class="text-danger">*</span></label>
                            <select name="location_id" id="location_id" class="form-select @error('location_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Lokasi Stok --</option>
                                @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>
                                    {{ $loc->name }}
                                </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Stok parfum akan otomatis berkurang dari lokasi ini.</small>
                            @error('location_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Nominal Harga (Custom) -->
                        <div class="col-md-6">
                            <label for="total_price" class="form-label small fw-semibold text-muted">Total Harga (Bisa Custom / Diskon) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted">Rp</span>
                                <input type="number" name="total_price" id="total_price" step="0.01" min="0" class="form-control font-mono fw-bold @error('total_price') is-invalid @enderror" placeholder="0" value="{{ old('total_price') }}" required>
                            </div>
                            <small class="form-text text-muted">Nominal bisa disesuaikan jika ada diskon khusus.</small>
                            @error('total_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status Pembayaran -->
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Status Pembayaran <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3 mt-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_status" id="statusUnpaid" value="BELUM LUNAS" {{ old('payment_status', 'BELUM LUNAS') === 'BELUM LUNAS' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold text-danger" for="statusUnpaid">
                                        BELUM LUNAS (Piutang)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_status" id="statusPaid" value="LUNAS" {{ old('payment_status') === 'LUNAS' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold text-success" for="statusPaid">
                                        LUNAS (Masuk Kas)
                                    </label>
                                </div>
                            </div>
                            @error('payment_status')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Catatan -->
                        <div class="col-md-6">
                            <label for="notes" class="form-label small fw-semibold text-muted">Catatan Tambahan (Opsional)</label>
                            <input type="text" name="notes" id="notes" class="form-control" placeholder="Keterangan pengiriman, kontak, dll." value="{{ old('notes') }}">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-outline-secondary px-3">Batal</a>
                        <button type="submit" class="btn btn-sm btn-scent px-4 fw-semibold">
                            <i class="bi bi-check2-circle me-1"></i> Terbitkan Invoice
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function calculateSuggestedPrice() {
    const productSelect = document.getElementById('product_id');
    const qtyInput = document.getElementById('qty');
    const priceInput = document.getElementById('total_price');

    const selectedOption = productSelect.options[productSelect.selectedIndex];
    const unitPrice = parseFloat(selectedOption.getAttribute('data-price') || 0);
    const qty = parseInt(qtyInput.value || 1);

    if (unitPrice > 0 && (!priceInput.value || priceInput.dataset.autoFilled === "true")) {
        priceInput.value = unitPrice * qty;
        priceInput.dataset.autoFilled = "true";
    }
}

document.getElementById('total_price').addEventListener('input', function() {
    this.dataset.autoFilled = "false";
});
</script>
@endsection
