@extends('layouts.app')

@section('title', 'Pendaftaran Reseller Baru')

@section('content')
<div class="row justify-content-center py-4">
    <div class="col-md-6 col-lg-5">
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle mb-3" style="background-color: var(--scent-amber-subtle, rgba(179,139,77,0.1)); color: var(--scent-amber, #b38b4d); width: 64px; height: 64px;">
                <i class="bi bi-shop fs-2"></i>
            </div>
            <h2 class="font-serif fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">SCENTPEEP</h2>
            <p class="text-muted small">Pendaftaran Kemitraan Reseller Parfum</p>
        </div>

        <div class="card border-0 shadow-sm" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 14px;">
            <div class="card-body p-4 p-md-5">
                <h4 class="font-serif fw-semibold mb-2 text-center text-dark">Formulir Registrasi Reseller</h4>
                <p class="text-muted small text-center mb-4">Akun akan diverifikasi dan disetujui oleh Administrator sebelum dapat digunakan.</p>

                <form method="POST" action="{{ route('register.submit') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label small fw-semibold text-muted">Nama Lengkap Pemilik <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0 @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autofocus placeholder="Contoh: Budi Santoso">
                        </div>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="location_name" class="form-label small fw-semibold text-muted">Nama Toko / Lokasi Reseller <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-geo-alt"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0 @error('location_name') is-invalid @enderror" id="location_name" name="location_name" value="{{ old('location_name') }}" required placeholder="Contoh: Toko Parfum Budi Bandung">
                        </div>
                        <small class="text-muted" style="font-size: 0.75rem;">Nama ini akan otomatis dijadikan lokasi inventori stok parfum Anda.</small>
                        @error('location_name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label small fw-semibold text-muted">Alamat Email <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required placeholder="budi@example.com">
                        </div>
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-2 mb-4">
                        <div class="col-sm-6">
                            <label for="password" class="form-label small fw-semibold text-muted">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" id="password" name="password" required placeholder="Minimal 6 karakter">
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-sm-6">
                            <label for="password_confirmation" class="form-label small fw-semibold text-muted">Konfirmasi Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-shield-lock"></i></span>
                                <input type="password" class="form-control border-start-0 ps-0" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi password">
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mb-3">
                        <button type="submit" class="btn btn-scent py-2 fw-semibold">
                            <i class="bi bi-check-circle me-1"></i> Kirim Pendaftaran Reseller
                        </button>
                    </div>

                    <div class="text-center mt-3 pt-2 border-top" style="border-color: var(--color-border-subtle, #f0eee9) !important;">
                        <span class="text-muted small">Sudah memiliki akun terverifikasi?</span>
                        <a href="{{ route('login') }}" class="small fw-semibold text-dark text-decoration-none ms-1">&larr; Masuk ke Sistem</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
