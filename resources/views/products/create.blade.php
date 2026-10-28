@extends('layouts.app')

@section('title', 'Tambah Produk Baru')
@section('page-header', 'Tambah Master Produk Parfum')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm mb-4" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px;">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3" style="border-bottom: 1px solid var(--color-border-subtle, #f0eee9);">
                <h4 class="font-serif card-title mb-0 fw-bold fs-5">Formulir Produk Baru</h4>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('products.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label small fw-semibold text-muted text-uppercase" style="letter-spacing: 0.05em;">Nama Parfum <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Scentpeep Sauvage EDP" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="code" class="form-label small fw-semibold text-muted text-uppercase" style="letter-spacing: 0.05em;">Kode SKU / Barcode</label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" placeholder="Contoh: SP-SVG-01">
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="cost_price" class="form-label small fw-semibold text-muted text-uppercase" style="letter-spacing: 0.05em;">Harga Modal (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted">Rp</span>
                                <input type="number" min="0" step="any" class="form-control @error('cost_price') is-invalid @enderror" id="cost_price" name="cost_price" value="{{ old('cost_price') }}" placeholder="110000">
                            </div>
                            @error('cost_price')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="sell_price" class="form-label small fw-semibold text-muted text-uppercase" style="letter-spacing: 0.05em;">Harga Jual (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted">Rp</span>
                                <input type="number" min="0" step="any" class="form-control @error('sell_price') is-invalid @enderror" id="sell_price" name="sell_price" value="{{ old('sell_price') }}" placeholder="195000">
                            </div>
                            @error('sell_price')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold text-dark" for="is_active">Status Produk Aktif</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-2 border-top" style="border-color: var(--color-border-subtle, #f0eee9) !important;">
                        <a href="{{ route('products.index') }}" class="btn btn-light border px-4 rounded-pill">Batal</a>
                        <button type="submit" class="btn btn-scent px-4 fw-semibold">
                            <i class="bi bi-save me-1"></i> Simpan Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
