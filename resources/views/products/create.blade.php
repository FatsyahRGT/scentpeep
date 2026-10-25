@extends('layouts.app')

@section('title', 'Tambah Produk Baru')
@section('page-header', 'Tambah Master Produk Parfum')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                <h4 class="card-title mb-0 fw-bold">Formulir Produk Baru</h4>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('products.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label small fw-bold text-muted text-uppercase">Nama Parfum <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Scentpeep Sauvage EDP" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="code" class="form-label small fw-bold text-muted text-uppercase">Kode SKU / Barcode</label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" placeholder="Contoh: SP-SVG-01">
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="cost_price" class="form-label small fw-bold text-muted text-uppercase">Harga Modal (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="number" min="0" step="any" class="form-control @error('cost_price') is-invalid @enderror" id="cost_price" name="cost_price" value="{{ old('cost_price') }}" placeholder="110000">
                            </div>
                            @error('cost_price')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="sell_price" class="form-label small fw-bold text-muted text-uppercase">Harga Jual (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rp</span>
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

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('products.index') }}" class="btn btn-light border px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm">
                            <i class="bi bi-save me-1"></i> Simpan Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
