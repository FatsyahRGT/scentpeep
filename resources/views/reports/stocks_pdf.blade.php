@php
    $logoEmblemPath = public_path('images/New_Project_4K_1000DPI.png');
    $logoEmblem = file_exists($logoEmblemPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoEmblemPath)) : asset('images/New_Project_4K_1000DPI.png');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Matriks Stok Parfum - SCENTPEEP PARFUM</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm 15mm;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #1c1917;
            background: #ffffff;
            margin: 0;
            padding: 20px;
        }
        .font-brand-serif {
            font-family: "Playfair Display", Georgia, "Times New Roman", serif;
            letter-spacing: -0.01em;
        }
        .font-mono-num {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-feature-settings: "tnum" 1;
        }
        .table-custom {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
            font-size: 10px;
        }
        .table-custom th {
            background-color: #165b3d !important;
            color: #ffffff !important;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 7px 8px;
            border: 1px solid #124d33;
            font-size: 9px;
        }
        .table-custom td {
            padding: 6px 8px;
            border: 1px solid #e7e5e4;
            vertical-align: middle;
        }
        .table-custom tbody tr:nth-child(even) {
            background-color: #fafaf9;
        }
        .table-custom tfoot tr {
            background-color: #f5f5f4;
            font-weight: 700;
            border-top: 2px solid #165b3d;
        }
        .table-custom tfoot td {
            padding: 8px 8px;
        }
        .avoid-break {
            page-break-inside: avoid;
            break-inside: avoid;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
            .avoid-break { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <!-- Action Toolbar (Hidden when Printed) -->
    <div class="no-print mb-4 p-3 bg-light rounded-3 border d-flex justify-content-between align-items-center shadow-xs">
        <div>
            <h5 class="mb-0 fw-bold text-dark">Pratinjau Cetak / Ekspor PDF - Matriks Stok Fisik</h5>
            <small class="text-muted">Simpan sebagai dokumen PDF resmi untuk laporan gudang pusat & reseller.</small>
        </div>
        <div class="d-flex gap-2">
            <button type="button" onclick="window.print()" class="btn btn-sm btn-dark fw-semibold px-3 d-inline-flex align-items-center gap-2" style="background: #165b3d; border-color: #165b3d;">
                <i class="bi bi-printer"></i> Cetak / Simpan PDF
            </button>
            <button type="button" onclick="window.close()" class="btn btn-sm btn-outline-secondary fw-semibold px-3">
                Tutup
            </button>
        </div>
    </div>

    <!-- Kop Surat Resmi SCENTPEEP PARFUM -->
    <div class="mb-3 pb-2">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 72px; vertical-align: middle; padding-right: 14px;">
                    <img src="{{ $logoEmblem }}" alt="Logo Scentpeep" style="width: 62px; height: 62px; object-fit: contain; border-radius: 8px;">
                </td>
                <td style="vertical-align: middle;">
                    <div class="font-brand-serif fw-bold" style="font-size: 21px; color: #165b3d; letter-spacing: 0.04em; line-height: 1.15;">
                        LAPORAN STOK
                    </div>
                    <div class="fw-bold text-uppercase text-secondary" style="font-size: 11px; letter-spacing: 0.08em; margin-top: 2px;">
                        Laporan Matriks Inventori Stok Fisik
                    </div>
                    <div class="text-muted" style="font-size: 9.5px; margin-top: 2px;">
                        Authentic Inspired Fragrance - Retail Supply Chain
                    </div>
                </td>
                <td style="width: 220px; vertical-align: middle; text-align: right; font-size: 9px; line-height: 1.45;" class="font-mono-num">
                    <div><span class="text-muted">No. Dokumen:</span> <strong>DOC-SP/STK/{{ date('Ymd') }}-{{ str_pad($products->count(), 3, '0', STR_PAD_LEFT) }}</strong></div>
                    <div><span class="text-muted">Tgl Cetak:</span> <strong>{{ date('d/m/Y H:i') }} WIB</strong></div>
                    <div><span class="text-muted">Total Unit Sistem:</span> <strong>{{ number_format($totalSystemQty, 0, ',', '.') }} pcs</strong></div>
                    <div><span class="text-muted">Varian Produk:</span> <strong>{{ $products->count() }} Produk Aktif</strong></div>
                </td>
            </tr>
        </table>
        <!-- Garis Ganda Resmi Kop Surat -->
        <div style="border-top: 2px solid #165b3d; border-bottom: 0.75px solid #165b3d; height: 2px; margin-top: 10px;"></div>
    </div>

    <!-- Data Table -->
    <table class="table-custom">
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">#</th>
                <th style="width: 65px;">Kode</th>
                <th>Nama Varian Produk</th>
                <th class="text-end" style="width: 85px;">Modal (Rp)</th>
                <th class="text-end" style="width: 85px;">Jual (Rp)</th>
                @foreach($locations as $loc)
                    <th class="text-center">{{ $loc->name }}</th>
                @endforeach
                <th class="text-center fw-bold" style="width: 75px;">Total Unit</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $index => $prod)
            @php $rowTotal = 0; @endphp
            <tr>
                <td class="text-center text-muted font-mono-num">{{ $index + 1 }}</td>
                <td class="font-mono-num"><span class="badge bg-light text-dark border">{{ $prod->code ?: '-' }}</span></td>
                <td class="fw-semibold text-dark">{{ $prod->name }}</td>
                <td class="text-end font-mono-num">Rp {{ number_format($prod->cost_price ?? 0, 0, ',', '.') }}</td>
                <td class="text-end font-mono-num fw-bold">Rp {{ number_format($prod->sell_price ?? 0, 0, ',', '.') }}</td>
                @foreach($locations as $loc)
                    @php
                        $qty = $stockMatrix[$prod->id][$loc->id] ?? 0;
                        $rowTotal += $qty;
                    @endphp
                    <td class="text-center font-mono-num {{ $qty < 3 ? 'text-danger fw-bold' : '' }}">
                        {{ $qty }}
                    </td>
                @endforeach
                <td class="text-center font-mono-num fw-bold {{ $rowTotal < 3 ? 'text-danger bg-warning-subtle' : 'bg-light' }}">
                    {{ $rowTotal }} pcs
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ 6 + count($locations) }}" class="text-center text-muted py-4">Tidak ada data produk.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4 pt-2 border-top d-flex justify-content-between align-items-center text-muted" style="font-size: 8.5px;">
        <div>Scentpeep Management System &bull; Dokumen inventori stok fisik resmi.</div>
        <div>Dicetak otomatis pada {{ date('d F Y, H:i') }} WIB</div>
    </div>
</body>
</html>
