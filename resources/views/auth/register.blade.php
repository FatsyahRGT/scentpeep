<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pendaftaran Reseller &mdash; Scentpeep Management System</title>

    <!-- Favicon (Tab Browser) -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/New_Project_4K_1000DPI.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/New_Project_4K_1000DPI.png') }}">

    <!-- Google Fonts: Plus Jakarta Sans, Source Serif 4, JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Source+Serif+4:ital,opsz,wght@0,8..60,600;0,8..60,700;1,8..60,600&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3.3 CSS & Icons (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --font-sans: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            --font-serif: 'Source Serif 4', Georgia, serif;
            --font-mono: 'JetBrains Mono', monospace;
            --scent-green: #2b7b5b;
            --scent-green-hover: #226449;
            --scent-cream: #f9efe8;
        }

        body {
            font-family: var(--font-sans);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            color: #212529;
            background-color: #0f172a;
        }

        .font-serif { font-family: var(--font-serif); }
        .font-mono { font-family: var(--font-mono); }

        .scentpeep-register-wrapper {
            min-height: 100vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
            box-sizing: border-box;
            background: linear-gradient(135deg, rgba(19, 58, 43, 0.58) 0%, rgba(15, 23, 42, 0.68) 100%),
                        url('{{ asset('images/Water.jpg') }}') center / cover no-repeat;
        }

        .scentpeep-register-card {
            width: 100%;
            max-width: 500px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.45);
            background: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: fadeInCard 0.45s ease-out;
        }

        @keyframes fadeInCard {
            from {
                opacity: 0;
                transform: translateY(14px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .scentpeep-brand-header {
            background-color: #2b7b5b !important;
            background-image: none !important;
            text-align: center;
            padding: 2.2rem 1.5rem 1.6rem;
            position: relative;
            transition: background-color 0.2s ease;
        }

        .scentpeep-text-logo {
            width: 230px;
            height: 48px;
            object-fit: cover;
            object-position: center;
            display: block;
            margin: 0 auto 0.4rem;
            transition: opacity 0.2s ease;
        }

        .btn-scent-login {
            background-color: #2b7b5b !important;
            color: #f9efe8 !important;
            border: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-scent-login:hover,
        .btn-scent-login:focus,
        .btn-scent-login:active {
            background-color: #226449 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 16px rgba(43, 123, 91, 0.4);
        }

        .form-control:focus {
            border-color: #2b7b5b;
            box-shadow: 0 0 0 0.25rem rgba(43, 123, 91, 0.18);
        }

        .input-group-text {
            background-color: #f8fafc;
        }
    </style>
</head>
<body>

<div class="scentpeep-register-wrapper">
    <div class="scentpeep-register-card">
        <!-- Bagian Atas Card (.scentpeep-brand-header) -->
        <div class="scentpeep-brand-header" id="brandHeader">
            <!-- Logo Tulisan Asli "SCENTPEEP" -->
            <img src="{{ asset('images/Logo_ijo_1000DPI.png') }}" 
                 alt="SCENTPEEP" 
                 class="scentpeep-text-logo"
                 id="brandTextLogo">

            <!-- Subjudul Kecil -->
            <p class="small mb-0 font-sans" style="color: rgba(249, 239, 232, 0.88) !important; font-size: 0.82rem; letter-spacing: 0.02em;">
                Pendaftaran Kemitraan Reseller Parfum
            </p>
        </div>

        <!-- Bagian Bawah Card (Body Form Putih) -->
        <div class="p-4 p-sm-5 bg-white">
            <h5 class="font-serif fw-bold text-dark text-center mb-1 fs-5">Formulir Registrasi Reseller</h5>
            <p class="text-muted small text-center mb-4">Akun akan diverifikasi dan disetujui oleh Administrator sebelum dapat digunakan.</p>

            @if(session('success'))
            <div class="alert alert-success py-2 px-3 small rounded-3 mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i>
                <div>{{ session('success') }}</div>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger py-2 px-3 small rounded-3 mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div>{{ session('error') }}</div>
            </div>
            @endif

            <form method="POST" action="{{ route('register.submit') }}">
                @csrf

                <!-- Nama Lengkap Pemilik -->
                <div class="mb-3">
                    <label for="name" class="form-label small fw-semibold text-muted">Nama Lengkap Pemilik <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text border-end-0 text-muted ps-3">
                            <i class="bi bi-person"></i>
                        </span>
                        <input type="text" 
                               class="form-control border-start-0 ps-2 @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}" 
                               required 
                               autofocus 
                               autocomplete="off" 
                               placeholder="Contoh: Budi Santoso">
                    </div>
                    @error('name')
                        <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                    @enderror
                </div>

                <!-- Nama Toko / Lokasi Reseller -->
                <div class="mb-3">
                    <label for="location_name" class="form-label small fw-semibold text-muted">Nama Toko / Lokasi Reseller <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text border-end-0 text-muted ps-3">
                            <i class="bi bi-geo-alt"></i>
                        </span>
                        <input type="text" 
                               class="form-control border-start-0 ps-2 @error('location_name') is-invalid @enderror" 
                               id="location_name" 
                               name="location_name" 
                               value="{{ old('location_name') }}" 
                               required 
                               autocomplete="off" 
                               placeholder="Contoh: Toko Parfum Budi Bandung">
                    </div>
                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Nama ini akan otomatis dijadikan lokasi inventori stok parfum Anda.</small>
                    @error('location_name')
                        <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                    @enderror
                </div>

                <!-- Alamat Email -->
                <div class="mb-3">
                    <label for="email" class="form-label small fw-semibold text-muted">Alamat Email <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text border-end-0 text-muted ps-3">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input type="email" 
                               class="form-control border-start-0 ps-2 @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autocomplete="off" 
                               placeholder="budi@example.com">
                    </div>
                    @error('email')
                        <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password & Konfirmasi Password -->
                <div class="row g-2 mb-4">
                    <div class="col-sm-6">
                        <label for="password" class="form-label small fw-semibold text-muted">Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0 text-muted ps-3">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password" 
                                   class="form-control border-start-0 ps-2 @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required 
                                   autocomplete="off" 
                                   placeholder="Min. 6 karakter">
                        </div>
                        @error('password')
                            <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-sm-6">
                        <label for="password_confirmation" class="form-label small fw-semibold text-muted">Konfirmasi Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0 text-muted ps-3">
                                <i class="bi bi-shield-lock"></i>
                            </span>
                            <input type="password" 
                                   class="form-control border-start-0 ps-2" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   required 
                                   autocomplete="off" 
                                   placeholder="Ulangi password">
                        </div>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-scent-login py-2.5 fw-semibold rounded-3 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-check-circle me-1"></i> Kirim Pendaftaran Reseller
                    </button>
                </div>

                <!-- Link Kembali ke Login -->
                <div class="text-center mt-3 pt-3 border-top" style="border-color: #f0eee9 !important;">
                    <span class="text-muted small">Sudah memiliki akun terverifikasi?</span>
                    <a href="{{ route('login') }}" class="small fw-semibold text-decoration-none ms-1" style="color: var(--scent-green);">&larr; Masuk ke Sistem</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap 5.3.3 JS Bundle CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script Otomatis Penghapus Latar Kotak Hijau Logo (Canvas Pixel Processor) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const textLogo = document.getElementById('brandTextLogo');
    const header = document.getElementById('brandHeader');

    if (!textLogo) return;

    function processLogoTransparency() {
        try {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const w = textLogo.naturalWidth || textLogo.width;
            const h = textLogo.naturalHeight || textLogo.height;

            if (!w || !h) return;

            canvas.width = w;
            canvas.height = h;
            ctx.drawImage(textLogo, 0, 0, w, h);

            const imgData = ctx.getImageData(0, 0, w, h);
            const data = imgData.data;

            // 1. Baca nilai warna piksel pojok (0,0)
            const bgR = data[0];
            const bgG = data[1];
            const bgB = data[2];
            const cornerColorRgb = `rgb(${bgR}, ${bgG}, ${bgB})`;

            // 2. Samakan background header kartu
            if (header) {
                header.style.backgroundColor = cornerColorRgb;
            }

            // 3. Ubah semua piksel hijau latar belakang (toleransi warna ~38) menjadi transparan
            const tolerance = 38;
            let minX = w, maxX = 0, minY = h, maxY = 0;
            let hasVisibleLetters = false;

            for (let i = 0; i < data.length; i += 4) {
                const r = data[i];
                const g = data[i + 1];
                const b = data[i + 2];

                const dist = Math.sqrt(
                    (r - bgR) ** 2 +
                    (g - bgG) ** 2 +
                    (b - bgB) ** 2
                );

                if (dist <= tolerance) {
                    data[i + 3] = 0;
                } else {
                    hasVisibleLetters = true;
                    const pixelIdx = i / 4;
                    const px = pixelIdx % w;
                    const py = Math.floor(pixelIdx / w);

                    if (px < minX) minX = px;
                    if (px > maxX) maxX = px;
                    if (py < minY) minY = py;
                    if (py > maxY) maxY = py;

                    if (dist < tolerance + 12) {
                        const feather = (dist - tolerance) / 12;
                        data[i + 3] = Math.round(data[i + 3] * feather);
                    }
                }
            }

            ctx.putImageData(imgData, 0, 0);

            // 4. Crop presisi pada batas huruf SCENTPEEP
            if (hasVisibleLetters && maxX > minX && maxY > minY) {
                const padX = 24;
                const padY = 12;
                const cropX = Math.max(0, minX - padX);
                const cropY = Math.max(0, minY - padY);
                const cropW = Math.min(w - cropX, (maxX - minX) + padX * 2);
                const cropH = Math.min(h - cropY, (maxY - minY) + padY * 2);

                const croppedCanvas = document.createElement('canvas');
                croppedCanvas.width = cropW;
                croppedCanvas.height = cropH;
                const croppedCtx = croppedCanvas.getContext('2d');
                croppedCtx.drawImage(canvas, cropX, cropY, cropW, cropH, 0, 0, cropW, cropH);

                textLogo.src = croppedCanvas.toDataURL('image/png');
                textLogo.style.objectFit = 'contain';
                textLogo.style.height = '42px';
                textLogo.style.width = 'auto';
                textLogo.style.maxWidth = '230px';
            } else {
                textLogo.src = canvas.toDataURL('image/png');
            }
        } catch (err) {
            console.warn('Canvas logo background processing error:', err);
        }
    }

    if (textLogo.complete && textLogo.naturalWidth > 0) {
        processLogoTransparency();
    } else {
        textLogo.addEventListener('load', processLogoTransparency);
    }
});
</script>
</body>
</html>
