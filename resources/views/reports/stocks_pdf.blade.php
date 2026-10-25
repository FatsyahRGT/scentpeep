<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Matriks Stok Parfum - Scentpeep</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css">
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
            <h4 class="mb-0 fw-bold">Pratinjau Cetak / Ekspor PDF Ringkasan Stok</h4>
            <small class="text-muted">Simpan sebagai dokumen PDF resmi untuk laporan gudang & reseller</small>
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
            <div class="text-muted small">Laporan Matriks Inventori Stok Fisik</div>
        </div>
        <div class="text-end small">
            <div><strong>Tanggal Cetak:</strong> {{ date('d/m/Y H:i') }}</div>
            <div><strong>Total Unit Sistem:</strong> {{ number_format($totalSystemQty, 0, ',', '.') }} pcs</div>
        </div>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th style="width: 30px;">#</th>
                <th style="width: 70px;">Kode</th>
                <th>Nama Varian Produk</th>
                <th class="text-end" style="width: 90px;">Modal (Rp)</th>
                <th class="text-end" style="width: 90px;">Jual (Rp)</th>
                @foreach($locations as $loc)
                    <th class="text-center">{{ $loc->name }}</th>
                @endforeach
                <th class="text-center fw-bold" style="width: 80px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $index => $prod)
            @php
                $rowTotal = 0;
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><span class="badge bg-light text-dark border">{{ $prod->code ?: '-' }}</span></td>
                <td class="fw-semibold">{{ $prod->name }}</td>
                <td class="text-end">Rp {{ number_format($prod->cost_price ?? 0, 0, ',', '.') }}</td>
                <td class="text-end">Rp {{ number_format($prod->sell_price ?? 0, 0, ',', '.') }}</td>
                @foreach($locations as $loc)
                    @php
                        $qty = $stockMatrix[$prod->id][$loc->id] ?? 0;
                        $rowTotal += $qty;
                    @endphp
                    <td class="text-center {{ $qty < 3 ? 'text-danger fw-bold' : '' }}">
                        {{ $qty }}
                    </td>
                @endforeach
                <td class="text-center fw-bold {{ $rowTotal < 3 ? 'bg-warning-lt text-danger' : 'bg-light' }}">
                    {{ $rowTotal }} pcs
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ 6 + count($locations) }}" class="text-center text-muted py-3">Tidak ada data produk.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
