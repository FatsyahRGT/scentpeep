@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="row justify-content-center py-5">
    <div class="col-md-5 col-lg-4">
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle mb-3" style="background-color: var(--scent-amber-subtle, rgba(179,139,77,0.1)); color: var(--scent-amber, #b38b4d); width: 64px; height: 64px;">
                <i class="bi bi-droplet-fill fs-2"></i>
            </div>
            <h2 class="font-serif fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">SCENTPEEP</h2>
            <p class="text-muted small">Sistem Rekap & Manajemen Stok Parfum</p>
        </div>

        <div class="card border-0 shadow-sm" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 14px;">
            <div class="card-body p-4 p-md-5">
                <h4 class="font-serif fw-semibold mb-1 text-center text-dark">Masuk ke Sistem</h4>
                <p class="text-muted small text-center mb-4">Akses panel inventori & rekap transaksi</p>

                <form method="POST" action="{{ route('login.submit') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label small fw-semibold text-muted">Alamat Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="off" placeholder="name@scentpeep.com">
                        </div>
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label small fw-semibold text-muted">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="off" placeholder="Masukkan password">
                        </div>
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label small text-muted" for="remember">Ingat Sesi Saya</label>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-scent py-2 fw-semibold">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Sekarang
                        </button>
                    </div>
                </form>

                <div class="text-center mt-4 pt-2 border-top" style="border-color: var(--color-border-subtle, #f0eee9) !important;">
                    <span class="text-muted small">Ingin bermitra sebagai reseller?</span>
                    <a href="{{ route('register') }}" class="small fw-semibold text-dark text-decoration-none ms-1">Daftar Reseller &rarr;</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
