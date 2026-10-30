@extends('layouts.app')

@section('title', 'Catat Pre-Order Baru')
@section('page-header', 'Pencatatan Pre-Order (Stok Pending)')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm" style="border: 1px solid var(--color-border) !important;">
            <div class="card-header bg-white py-3 border-bottom">
                <h3 class="card-title font-serif fw-bold text-dark m-0">Formulir Pre-Order (PO)</h3>
                <small class="text-muted d-block">Barang yang di-input di sini akan berada di segmen "Stok Pending" dan belum menambah stok fisik utama sampai statusnya diselesaikan.</small>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('pre-orders.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="po_number" class="form-label small fw-semibold text-muted">Nomor PO (Custom / Opsional)</label>
                            <input type="text" name="po_number" id="po_number" class="form-control font-mono @error('po_number') is-invalid @enderror" placeholder="Contoh: PO-PROD-01" value="{{ old('po_number') }}">
                            @error('po_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="customer_name" class="form-label small fw-semibold text-muted">Nama Pemesan / Pelanggan PO</label>
                            <input type="text" name="customer_name" id="customer_name" class="form-control @error('customer_name') is-invalid @enderror" placeholder="Nama pemesan..." value="{{ old('customer_name') }}">
                            @error('customer_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-8">
                            <label for="product_id" class="form-label small fw-semibold text-muted">Varian Parfum yang Di-PO <span class="text-danger">*</span></label>
                            <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Varian Parfum --</option>
                                @foreach($products as $prod)
                                <option value="{{ $prod->id }}" {{ old('product_id') == $prod->id ? 'selected' : '' }}>
                                    {{ $prod->name }} ({{ $prod->code ?? '-' }})
                                </option>
                                @endforeach
                            </select>
                            @error('product_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="qty" class="form-label small fw-semibold text-muted">Qty PO <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="qty" id="qty" min="1" class="form-control @error('qty') is-invalid @enderror" value="{{ old('qty', 1) }}" required>
                                <span class="input-group-text bg-light text-muted small">pcs</span>
                            </div>
                            @error('qty')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="location_id" class="form-label small fw-semibold text-muted">Target Lokasi Masuk Stok <span class="text-danger">*</span></label>
                            <select name="location_id" id="location_id" class="form-select @error('location_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Lokasi (IWAN / RIZKY) --</option>
                                @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>
                                    {{ $loc->name }}
                                </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Ketika barang PO telah siap dan diselesaikan, stok fisik akan otomatis ditambahkan ke lokasi ini.</small>
                            @error('location_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="notes" class="form-label small fw-semibold text-muted">Catatan PO (Opsional)</label>
                            <textarea name="notes" id="notes" rows="2" class="form-control" placeholder="Estimasi selesai, kontak supplier/pemesan, dll.">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('pre-orders.index') }}" class="btn btn-sm btn-outline-secondary px-3">Batal</a>
                        <button type="submit" class="btn btn-sm btn-scent px-4 fw-semibold">
                            <i class="bi bi-hourglass me-1"></i> Simpan Stok Pending PO
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
