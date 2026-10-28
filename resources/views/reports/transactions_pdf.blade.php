<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi Kas & Penjualan - Scentpeep</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; background: #fff; color: #1e293b; padding: 20px; font-size: 11px; }
        .table th, .table td { padding: 6px 8px !important; font-size: 11px; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print mb-3 d-flex justify-content-between align-items-center p-3 bg-light rounded border">
        <div>
            <h4 class="mb-0 fw-bold">Pratinjau Cetak / Ekspor PDF Laporan Transaksi</h4>
            <small class="text-muted">Gunakan tombol cetak untuk menyimpan sebagai PDF dokumen resmi</small>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary btn-sm">
                Cetak / Simpan PDF
            </button>
            <button onclick="window.close()" class="btn btn-outline-secondary btn-sm">
                Tutup
            </button>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
        <div>
            <h2 class="fw-bold mb-0 text-dark">SCENTPEEP PARFUM</h2>
            <div class="text-muted small">Laporan Arus Kas, Penjualan & Inventori</div>
        </div>
        <div class="text-end small">
            <div><strong>Tanggal Cetak:</strong> {{ date('d/m/Y H:i') }}</div>
            <div><strong>Dicetak Oleh:</strong> {{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})</div>
        </div>
    </div>

    <!-- Summary Box -->
    <div class="row g-2 mb-3">
        <div class="col-4">
            <div class="p-2 border rounded bg-light">
                <div class="text-muted small">Total Pemasukan:</div>
                <div class="fw-bold text-success fs-5">+Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-4">
            <div class="p-2 border rounded bg-light">
                <div class="text-muted small">Total Pengeluaran:</div>
                <div class="fw-bold text-danger fs-5">-Rp {{ number_format($totalOutcome, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-4">
            <div class="p-2 border rounded bg-light">
                <div class="text-muted small">Saldo Bersih Kas:</div>
                <div class="fw-bold fs-5 {{ $netSaldo >= 0 ? 'text-primary' : 'text-danger' }}">
                    Rp {{ number_format($netSaldo, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th style="width: 30px;">#</th>
                <th style="width: 75px;">Tanggal</th>
                <th>Tipe & Kategori</th>
                <th>Keterangan / Pembeli</th>
                <th>Varian Parfum (Lokasi)</th>
                <th class="text-center" style="width: 50px;">Qty</th>
                <th class="text-end" style="width: 100px;">Nominal (Rp)</th>
                <th style="width: 90px;">No. Invoice</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $index => $tx)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $tx->tanggal->format('d/m/Y') }}</td>
                <td>
                    <strong>{{ $tx->type }}</strong> - {{ $tx->category }}
                    @if($tx->is_free)<span class="badge bg-teal text-white ms-1">GRATIS</span>@endif
                </td>
                <td>
                    <div>{{ $tx->description ?: '-' }}</div>
                    @if($tx->customer_name)
                        <div class="text-primary small">Pembeli: {{ $tx->customer_name }} @if($tx->channel)({{ $tx->channel }})@endif</div>
                    @endif
                </td>
                <td>
                    @if($tx->product)
                        {{ $tx->product->name }} ({{ $tx->location?->name }})
                    @else
                        -
                    @endif
                </td>
                <td class="text-center">
                    {{ $tx->qty ? ($tx->stock_effect === 'IN' ? "+{$tx->qty}" : "-{$tx->qty}") : '-' }}
                </td>
                <td class="text-end fw-bold {{ $tx->type === 'INCOME' ? 'text-success' : 'text-danger' }}">
                    Rp {{ number_format($tx->amount, 0, ',', '.') }}
                </td>
                <td>{{ $tx->invoice_no ?: '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center text-muted py-3">Tidak ada data transaksi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
