<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Invoice Penjualan Parfum - Scentpeep</title>
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
            <h4 class="mb-0 fw-bold">Pratinjau Cetak / Ekspor PDF Laporan Invoice Penjualan</h4>
            <small class="text-muted">Gunakan tombol cetak untuk menyimpan sebagai PDF dokumen resmi</small>
        </div>
        <div class="d-flex gap-2">
            <button type="button" onclick="window.print()" class="btn btn-dark btn-sm fw-semibold" style="background: #2b7b5b; border-color: #2b7b5b;">
                Cetak / Simpan PDF
            </button>
            <button type="button" onclick="window.close()" class="btn btn-outline-secondary btn-sm">
                Tutup
            </button>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
        <div>
            <h2 class="fw-bold mb-0 text-dark">SCENTPEEP PARFUM</h2>
            <div class="text-muted small">Laporan Rekapitulasi Invoice Penjualan Resmi</div>
        </div>
        <div class="text-end small">
            <div><strong>Tanggal Cetak:</strong> {{ date('d/m/Y H:i') }}</div>
            <div><strong>Dicetak Oleh:</strong> {{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})</div>
        </div>
    </div>

    <!-- Summary KPI Box -->
    <div class="row g-2 mb-3">
        <div class="col-3">
            <div class="p-2 border rounded bg-light">
                <div class="text-muted small">Total Invoice:</div>
                <div class="fw-bold fs-5 text-dark">{{ $invoices->count() }} Faktur</div>
                <small class="text-muted">{{ number_format($totalQty, 0, ',', '.') }} Botol (Pcs)</small>
            </div>
        </div>
        <div class="col-3">
            <div class="p-2 border rounded bg-light">
                <div class="text-muted small">Total Omset Penjualan:</div>
                <div class="fw-bold fs-5 text-dark">Rp {{ number_format($totalAmount, 0, ',', '.') }}</div>
                <small class="text-muted">Nilai kotor transaksi</small>
            </div>
        </div>
        <div class="col-3">
            <div class="p-2 border rounded bg-light">
                <div class="text-muted small">Total Terbayar (Lunas):</div>
                <div class="fw-bold fs-5 text-success">Rp {{ number_format($totalPaid, 0, ',', '.') }}</div>
                <small class="text-muted">{{ $invoices->where('payment_status', 'LUNAS')->count() }} Faktur Lunas</small>
            </div>
        </div>
        <div class="col-3">
            <div class="p-2 border rounded bg-light">
                <div class="text-muted small">Total Piutang (Belum Lunas):</div>
                <div class="fw-bold fs-5 text-danger">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</div>
                <small class="text-muted">{{ $invoices->where('payment_status', 'BELUM LUNAS')->count() }} Faktur Tertunda</small>
            </div>
        </div>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-dark" style="background-color: #2b7b5b !important;">
            <tr>
                <th style="width: 30px;">#</th>
                <th style="width: 85px;">No. Invoice</th>
                <th style="width: 75px;">Tanggal</th>
                <th>Sales</th>
                <th>Customer / Pembeli</th>
                <th>Varian Parfum (Lokasi)</th>
                <th class="text-center" style="width: 45px;">Qty</th>
                <th class="text-end" style="width: 105px;">Total Harga (Rp)</th>
                <th class="text-center" style="width: 80px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $index => $inv)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="fw-bold font-monospace">{{ $inv->invoice_no }}</td>
                <td>{{ $inv->tanggal ? $inv->tanggal->format('d/m/Y') : ($inv->created_at ? $inv->created_at->format('d/m/Y') : '-') }}</td>
                <td>{{ $inv->sales_name ?: '-' }}</td>
                <td>{{ $inv->customer_name ?: '-' }}</td>
                <td>
                    {{ $inv->product?->name ?: '-' }}
                    <span class="text-muted small">({{ $inv->location?->name ?: '-' }})</span>
                </td>
                <td class="text-center">{{ $inv->qty }}</td>
                <td class="text-end fw-semibold">Rp {{ number_format((float) $inv->total_price, 0, ',', '.') }}</td>
                <td class="text-center">
                    @if($inv->payment_status === 'LUNAS')
                        <span class="badge bg-success">LUNAS</span>
                    @else
                        <span class="badge bg-danger">BELUM LUNAS</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center text-muted py-4">Tidak ada data invoice yang sesuai dengan kriteria filter.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot class="table-light fw-bold">
            <tr>
                <td colspan="6" class="text-end">TOTAL KESELURUHAN:</td>
                <td class="text-center">{{ number_format($totalQty, 0, ',', '.') }} pcs</td>
                <td class="text-end">Rp {{ number_format($totalAmount, 0, ',', '.') }}</td>
                <td class="text-center">-</td>
            </tr>
        </tfoot>
    </table>

    <div class="d-flex justify-content-between align-items-center mt-4 pt-2 border-top small text-muted">
        <div>Scentpeep Management System &bull; Dokumen rekapitulasi data penjualan resmi.</div>
        <div>Dicetak otomatis pada {{ date('d F Y, H:i') }} WIB</div>
    </div>
</body>
</html>
