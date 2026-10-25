@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="row justify-content-center py-5">
    <div class="col-md-5 col-lg-4">
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle bg-blue-lt text-primary mb-3 shadow-sm">
                <i class="bi bi-droplet-fill fs-1"></i>
            </div>
            <h2 class="fw-bold text-dark">SCENTPEEP</h2>
            <p class="text-muted small">Sistem Rekap & Manajemen Stok Parfum</p>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-3 text-center text-dark">Masuk ke Sistem</h4>

                <form method="POST" action="{{ route('login.submit') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label small fw-bold text-muted">Alamat Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-envelope text-muted"></i></span>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', 'admin@scentpeep.com') }}" required autofocus placeholder="name@scentpeep.com">
                        </div>
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label small fw-bold text-muted">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-lock text-muted"></i></span>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" value="password" required placeholder="Masukkan password">
                        </div>
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label small" for="remember">Ingat Sesi Saya</label>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg fw-semibold shadow-sm">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Sekarang
                        </button>
                    </div>
                </form>

                <div class="text-center mt-3">
                    <span class="text-muted small">Ingin bermitra sebagai reseller?</span>
                    <a href="{{ route('register') }}" class="small fw-semibold text-decoration-none ms-1">Daftar Reseller Baru &rarr;</a>
                </div>

                <div class="mt-4 p-3 bg-light rounded-3 text-muted small border">
                    <div class="fw-bold text-dark mb-1"><i class="bi bi-info-circle text-primary me-1"></i> Akun Demo Default:</div>
                    <div>&bull; <strong>Admin:</strong> admin@scentpeep.com / password</div>
                    <div>&bull; <strong>Staff:</strong> staff@scentpeep.com / password</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
