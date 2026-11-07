@php
    $logoEmblemPath = public_path('images/New_Project_4K_1000DPI.png');
    $logoEmblem = file_exists($logoEmblemPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoEmblemPath)) : asset('images/New_Project_4K_1000DPI.png');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi Kas - LAPORAN KAS SCENTPEEP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        @page {
            size: A4 portrait;
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
            font-size: 9.5px;
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
        .badge-type-in {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 8.5px;
        }
        .badge-type-out {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 8.5px;
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
            <h5 class="mb-0 fw-bold text-dark">Pratinjau Cetak / Ekspor PDF - Buku Kas &amp; Transaksi</h5>
            <small class="text-muted">Gunakan opsi cetak atau simpan sebagai PDF untuk pembukuan akuntansi resmi.</small>
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

    <!-- Kop Surat Resmi LAPORAN KAS -->
    <div class="mb-3 pb-2">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 72px; vertical-align: middle; padding-right: 14px;">
                    <img src="{{ $logoEmblem }}" alt="Logo Scentpeep" style="width: 62px; height: 62px; object-fit: contain; border-radius: 8px;">
                </td>
                <td style="vertical-align: middle;">
                    <div class="font-brand-serif fw-bold" style="font-size: 21px; color: #165b3d; letter-spacing: 0.04em; line-height: 1.15;">
                        LAPORAN KAS SCENTPEEP
                    </div>
                    <div class="fw-bold text-uppercase text-secondary" style="font-size: 11px; letter-spacing: 0.08em; margin-top: 2px;">
                        Laporan Transaksi Kas &amp; Rekapitulasi Arus Kas
                    </div>
                    <div class="fw-bold text-uppercase text-dark" style="font-size: 11px; letter-spacing: 0.08em; margin-top: 2px;">
                        Laporan Berdasarkan : {{ $filterDescription ?? 'Semua Data' }}
                    </div>
                    <div class="text-muted" style="font-size: 9.5px; margin-top: 2px;">
                        Authentic Inspired Fragrance - Retail Supply Chain
                    </div>
                </td>
                <td style="width: 220px; vertical-align: middle; text-align: right; font-size: 9px; line-height: 1.45;" class="font-mono-num">
                    <div><span class="text-muted">No. Dokumen:</span> <strong>DOC-SP/FIN/{{ date('Ymd') }}-{{ str_pad($transactions->count(), 3, '0', STR_PAD_LEFT) }}</strong></div>
                    <div><span class="text-muted">Tgl Cetak:</span> <strong>{{ date('d/m/Y H:i') }} WIB</strong></div>
                    <div><span class="text-muted">Dicetak Oleh:</span> <strong>{{ Auth::user()->name }}</strong></div>
                    <div><span class="text-muted">Otoritas / Hak:</span> <strong class="text-uppercase">{{ Auth::user()->role }}</strong></div>
                </td>
            </tr>
        </table>
        <!-- Garis Ganda Resmi Kop Surat -->
        <div style="border-top: 2px solid #165b3d; border-bottom: 0.75px solid #165b3d; height: 2px; margin-top: 10px;"></div>
    </div>

    <!-- Cashflow KPI Metrics -->
    <div class="row g-2 mb-3">
        <div class="col-4">
            <div class="p-2 border rounded-2 bg-light">
                <div class="text-muted small" style="font-size: 9px; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Total Pemasukan (Inflow)</div>
                <div class="fw-bold text-success fs-6 font-mono-num">+Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
                <div class="text-success font-mono-num" style="font-size: 9px;">Penerimaan Kas &amp; Pelunasan</div>
            </div>
        </div>
        <div class="col-4">
            <div class="p-2 border rounded-2 bg-light">
                <div class="text-muted small" style="font-size: 9px; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Total Pengeluaran (Outflow)</div>
                <div class="fw-bold text-danger fs-6 font-mono-num">-Rp {{ number_format($totalOutcome, 0, ',', '.') }}</div>
                <div class="text-danger font-mono-num" style="font-size: 9px;">Operasional &amp; Biaya Terkait</div>
            </div>
        </div>
        <div class="col-4">
            <div class="p-2 border rounded-2 bg-light">
                <div class="text-muted small" style="font-size: 9px; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Saldo Kas Bersih (Net Cash)</div>
                <div class="fw-bold fs-6 font-mono-num {{ $netSaldo >= 0 ? 'text-dark' : 'text-danger' }}">
                    Rp {{ number_format($netSaldo, 0, ',', '.') }}
                </div>
                <div class="text-secondary font-mono-num" style="font-size: 9px;">{{ $transactions->count() }} Baris Transaksi Terpilih</div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <table class="table-custom">
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">#</th>
                <th style="width: 70px;">Tanggal</th>
                <th style="width: 85px;">Tipe &amp; Kategori</th>
                <th>Keterangan / Pembeli</th>
                <th>Varian Parfum (Lokasi)</th>
                <th style="width: 45px;" class="text-center">Qty</th>
                <th style="width: 95px;" class="text-end">Nominal (Rp)</th>
                <th style="width: 80px;">No. Invoice</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $idx => $tx)
            <tr>
                <td class="text-center text-muted font-mono-num">{{ $idx + 1 }}</td>
                <td class="font-mono-num">{{ $tx->tanggal->format('d/m/Y') }}</td>
                <td>
                    @if($tx->type === 'INCOME')
                        <span class="badge-type-in">INCOME</span>
                    @else
                        <span class="badge-type-out">OUTCOME</span>
                    @endif
                    <div class="text-muted" style="font-size: 8.5px;">{{ $tx->category }}</div>
                </td>
                <td>
                    <div class="fw-semibold">{{ $tx->description ?: '-' }}</div>
                    @if($tx->customer_name)
                        <div class="text-muted" style="font-size: 8.5px;">Pelanggan: {{ $tx->customer_name }}</div>
                    @endif
                </td>
                <td>
                    @if($tx->product)
                        <strong>{{ $tx->product->name }}</strong>
                        <span class="text-muted" style="font-size: 8.5px;">({{ $tx->location?->name ?: '-' }})</span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td class="text-center font-mono-num">
                    {{ $tx->qty ? $tx->qty . ' pcs' : '-' }}
                </td>
                <td class="text-end font-mono-num fw-bold {{ $tx->type === 'INCOME' ? 'text-success' : 'text-danger' }}">
                    {{ $tx->type === 'INCOME' ? '+' : '-' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                </td>
                <td class="font-mono-num text-muted" style="font-size: 9px;">
                    {{ $tx->invoice_no ?: '-' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center text-muted py-4">Tidak ada data transaksi kas yang sesuai kriteria filter.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-end text-uppercase">Saldo Kas Bersih:</td>
                <td class="text-end font-mono-num {{ $netSaldo >= 0 ? 'text-dark' : 'text-danger' }}">
                    Rp {{ number_format($netSaldo, 0, ',', '.') }}
                </td>
                <td>-</td>
            </tr>
        </tfoot>
    </table>

    <div class="mt-4 pt-2 border-top d-flex justify-content-between align-items-center text-muted" style="font-size: 8.5px;">
        <div>Scentpeep Management System &bull; Dokumen rekapitulasi arus kas &amp; transaksi resmi.</div>
        <div>Dicetak otomatis pada {{ date('d F Y, H:i') }} WIB</div>
    </div>
</body>
</html>
