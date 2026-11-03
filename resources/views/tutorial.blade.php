@extends('layouts.app')

@section('title', 'Panduan & Tutorial Sistem')
@section('page-header', 'Panduan & Tutorial Penggunaan Sistem')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <!-- Hero Header -->
        <div class="card border-0 shadow-sm mb-4" style="border: 1px solid var(--color-border) !important; border-radius: 12px; background: #ffffff;">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-dark text-white d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 52px; height: 52px;">
                            <i class="bi bi-question-lg fs-2"></i>
                        </div>
                        <div>
                            <h2 class="font-serif fw-bold text-dark m-0 fs-4">Buku Panduan &amp; Tutorial Alur Operasional</h2>
                            <p class="text-muted small m-0 mt-1">Petunjuk komprehensif langkah demi langkah penggunaan seluruh fitur sidebar pada Scentpeep Management System.</p>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-light border px-3 py-2 font-mono shadow-xs d-inline-flex align-items-center gap-2 text-nowrap rounded-3 text-dark text-decoration-none" data-bs-toggle="modal" data-bs-target="#changelogModal" style="font-size: 0.82rem; cursor: pointer; transition: all 0.2s;" title="Klik untuk melihat Histori Versi &amp; Catatan Rilis">
                        <i class="bi bi-shield-check text-success fs-6"></i>
                        <span class="fw-semibold">Scentpeep v2.6 Ready</span>
                        <span class="badge bg-dark text-white rounded-pill px-2 py-1" style="font-size: 0.65rem;">Histori Versi</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Navigation Pills -->
        <ul class="nav nav-pills flex-nowrap overflow-x-auto pb-2 mb-4 gap-2 border-bottom" id="guideTabs" role="tablist" style="scrollbar-width: thin;">
            <li class="nav-item" role="presentation">
                <button class="nav-link active rounded-pill px-3 py-2 fw-semibold text-nowrap d-inline-flex align-items-center gap-2" id="flow-tab" data-bs-toggle="pill" data-bs-target="#tab-flow" type="button" role="tab">
                    <i class="bi bi-diagram-3-fill"></i> Peta Alur Bisnis
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-3 py-2 fw-semibold text-nowrap d-inline-flex align-items-center gap-2" id="invoice-tab" data-bs-toggle="pill" data-bs-target="#tab-invoice" type="button" role="tab">
                    <i class="bi bi-receipt-cutoff"></i> Penjualan &amp; Invoice
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-3 py-2 fw-semibold text-nowrap d-inline-flex align-items-center gap-2" id="stock-tab" data-bs-toggle="pill" data-bs-target="#tab-stock" type="button" role="tab">
                    <i class="bi bi-box-seam-fill"></i> Stok, Restock &amp; PO
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-3 py-2 fw-semibold text-nowrap d-inline-flex align-items-center gap-2" id="cash-tab" data-bs-toggle="pill" data-bs-target="#tab-cash" type="button" role="tab">
                    <i class="bi bi-wallet2"></i> Buku Kas &amp; Keuangan
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-3 py-2 fw-semibold text-nowrap d-inline-flex align-items-center gap-2" id="raw-tab" data-bs-toggle="pill" data-bs-target="#tab-raw" type="button" role="tab">
                    <i class="bi bi-boxes"></i> Bahan Mentah &amp; Packaging
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-3 py-2 fw-semibold text-nowrap d-inline-flex align-items-center gap-2" id="dash-tab" data-bs-toggle="pill" data-bs-target="#tab-dash" type="button" role="tab">
                    <i class="bi bi-grid-1x2-fill"></i> Pusat Dashboard
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-3 py-2 fw-semibold text-nowrap d-inline-flex align-items-center gap-2" id="admin-tab" data-bs-toggle="pill" data-bs-target="#tab-admin" type="button" role="tab">
                    <i class="bi bi-gear-wide-connected"></i> Master Data &amp; Reseller
                </button>
            </li>
        </ul>

        <!-- Tab Contents -->
        <div class="tab-content" id="guideTabsContent">

            <!-- TAB 1: PETA ALUR BISNIS -->
            <div class="tab-pane fade show active" id="tab-flow" role="tabpanel">
                <div class="card border-0 shadow-sm p-4 mb-4" style="border: 1px solid var(--color-border) !important; border-radius: 12px;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-compass-fill fs-4 text-dark"></i>
                        <h4 class="font-serif fw-bold m-0 fs-5">Peta Alur Siklus Operasional Scentpeep</h4>
                    </div>
                    <p class="text-muted small">Scentpeep Management System memisahkan secara tegas dan aman antara <strong>siklus inventori fisik parfum</strong> dan <strong>siklus pembukuan kas riil</strong> guna mencegah kerancuan keuangan.</p>

                    <div class="row g-3 my-2">
                        <div class="col-md-3">
                            <div class="border rounded-3 p-3 h-100 bg-light">
                                <span class="badge bg-dark text-white mb-2">Tahap 1</span>
                                <h6 class="fw-bold mb-1"><i class="bi bi-boxes me-1 text-primary"></i> Pengadaan Bahan</h6>
                                <p class="text-muted small m-0">Beli kardus (Box) &amp; Botol kaca. Dicatat di menu <strong>Barang Mentah</strong>. Biaya belanja dapat disinkronkan ke pengeluaran kas.</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded-3 p-3 h-100 bg-light">
                                <span class="badge bg-dark text-white mb-2">Tahap 2</span>
                                <h6 class="fw-bold mb-1"><i class="bi bi-box-arrow-in-down me-1 text-success"></i> Produksi &amp; Restock</h6>
                                <p class="text-muted small m-0">Parfum selesai diracik atau barang PO selesai. Ditambahkan ke stok fisik melalui menu <strong>Tambah Stock</strong> atau konfirmasi PO.</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded-3 p-3 h-100 bg-light">
                                <span class="badge bg-dark text-white mb-2">Tahap 3</span>
                                <h6 class="fw-bold mb-1"><i class="bi bi-receipt me-1 text-warning"></i> Penjualan (Invoice)</h6>
                                <p class="text-muted small m-0">Terbitkan Invoice per varian. <strong>Stok fisik langsung terpotong otomatis</strong> dari lokasi terpilih. Penanda piutang tercatat.</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded-3 p-3 h-100 bg-light">
                                <span class="badge bg-dark text-white mb-2">Tahap 4</span>
                                <h6 class="fw-bold mb-1"><i class="bi bi-wallet2 me-1 text-success"></i> Penerimaan Kas Riil</h6>
                                <p class="text-muted small m-0">Saat uang pembayaran benar-benar masuk ke kasir/rekening, catat secara manual di menu <strong>Buku Kas</strong>. Kas utama terjaga 100% akurat.</p>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-secondary mt-3 mb-0 d-flex align-items-center gap-2 small" role="alert">
                        <i class="bi bi-info-circle-fill text-dark fs-5"></i>
                        <div>
                            <strong>Prinsip Inti Sistem:</strong> Setiap mutasi kuantitas stok dijamin atomik (bebas minus) dengan <em>database transaction</em>. Seluruh fitur laporan terhubung otomatis ke tanggal transaksi kustom yang Anda tentukan.
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: PENJUALAN & INVOICE -->
            <div class="tab-pane fade" id="tab-invoice" role="tabpanel">
                <div class="card border-0 shadow-sm p-4 mb-4" style="border: 1px solid var(--color-border) !important; border-radius: 12px;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-receipt-cutoff fs-4 text-dark"></i>
                        <h4 class="font-serif fw-bold m-0 fs-5">Panduan Menu Penjualan (Invoice)</h4>
                    </div>
                    <p class="text-muted small">Menu Invoice digunakan untuk mencatat penjualan parfum ke pelanggan. Invoice memotong persediaan fisik secara langsung tanpa menunggu verifikasi manual.</p>

                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-dark d-flex align-items-center gap-2">
                                    <span class="badge bg-dark rounded-circle">1</span> Cara Menerbitkan Invoice Baru
                                </h6>
                                <ol class="small text-muted ps-3 mb-0" style="line-height: 1.8;">
                                    <li>Buka menu sidebar <strong>Penjualan (Invoice) &rarr; Buat Invoice Baru</strong>.</li>
                                    <li><strong>Nomor Invoice</strong>: Isi manual jika ada nomor nota/faktur toko fisik (opsional, jika dikosongkan sistem membuat otomatis contoh <code>INV-XXXX</code>).</li>
                                    <li><strong>Tanggal Invoice</strong>: Tentukan tanggal transaksi penjualan (bisa tanggal hari ini atau tanggal lampau/<em>backdate</em> jika merekap nota kemarin).</li>
                                    <li><strong>Nama Sales &amp; Customer</strong>: Pilih sales penanggung jawab (IWAN, RIZKY, VANNY, dll.) dan ketik nama pembeli.</li>
                                    <li><strong>Varian Parfum &amp; Qty</strong>: Pilih parfum yang dijual dan tentukan jumlah botolnya. Harga standar akan otomatis terhitung.</li>
                                    <li><strong>Potong dari Stok Lokasi</strong>: Tentukan dari gudang mana stok akan dikurangi (misal: Lokasi IWAN atau RIZKY).</li>
                                    <li><strong>Total Harga</strong>: Bisa Anda ubah/kustom jika memberikan diskon khusus kepada pelanggan.</li>
                                    <li><strong>Status Pembayaran</strong>: Pilih <code>BELUM LUNAS (Piutang)</code> jika pembayaran tertunda, atau <code>LUNAS (Sudah Dibayar)</code> jika pelanggan sudah melunasi.</li>
                                    <li>Klik tombol <strong>Terbitkan Invoice</strong>. Sistem memverifikasi stok dan memotong unit botol seketika.</li>
                                </ol>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-dark d-flex align-items-center gap-2">
                                    <span class="badge bg-dark rounded-circle">2</span> Fitur &amp; Aksi di Daftar Invoice
                                </h6>
                                <ul class="small text-muted ps-3 mb-3" style="line-height: 1.8;">
                                    <li><strong>Pencarian Cepat</strong>: Cari invoice berdasarkan nomor faktur atau nama pembeli di kolom pencarian.</li>
                                    <li><strong>Filter Fleksibel</strong>: Filter data berdasarkan Nama Sales, Status Pembayaran, Lokasi Stok Asal, serta Tahun &amp; Bulan transaksi.</li>
                                    <li><strong>Aksi Edit (Ikon Pensil)</strong>: Mengubah varian, lokasi, kuantitas, atau tanggal. Sistem secara cerdas mengembalikan stok lama terlebih dahulu, memverifikasi ketersediaan stok baru, lalu memotong stok lokasi baru.</li>
                                    <li><strong>Aksi Tandai Lunas (Ikon Centang)</strong>: Menandai invoice piutang menjadi lunas hanya dengan 1 klik.</li>
                                    <li><strong>Aksi Hapus (Ikon Sampah)</strong>: Menghapus invoice. <strong>Stok unit parfum otomatis dikembalikan utuh ke lokasi asal</strong>.</li>
                                </ul>

                                <div class="p-2 bg-light border rounded small">
                                    <strong class="text-dark"><i class="bi bi-shield-lock-fill me-1 text-primary"></i> Aturan Pemisahan Kas:</strong>
                                    <div class="text-muted mt-1">Invoice lunas berfungsi murni sebagai penanda piutang telah diselesaikan. Uang kas masuk tidak otomatis digabung ke saldo kas utama, melainkan diinput manual di modul <strong>Buku Kas</strong> saat uang fisik/transfer diterima di rekening perusahaan.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: STOK, RESTOCK & PO -->
            <div class="tab-pane fade" id="tab-stock" role="tabpanel">
                <div class="card border-0 shadow-sm p-4 mb-4" style="border: 1px solid var(--color-border) !important; border-radius: 12px;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-box-seam-fill fs-4 text-dark"></i>
                        <h4 class="font-serif fw-bold m-0 fs-5">Panduan Menu Stok Parfum, Tambah Stock &amp; PO</h4>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6 col-lg-3">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-dark"><i class="bi bi-plus-circle me-1 text-success"></i> Tambah Stock (Restock)</h6>
                                <p class="text-muted small">Halaman katalog khusus ketersediaan parfum untuk mengeksekusi penambahan stok fisik sehabis produksi.</p>
                                <ol class="small text-muted ps-3 mb-0" style="line-height: 1.6;">
                                    <li>Gunakan filter cepat: <em>Semua, Ready, Menipis, Habis</em>.</li>
                                    <li>Klik tombol <strong>+ Restock</strong> pada baris varian parfum.</li>
                                    <li>Pilih lokasi masuk stok (IWAN/RIZKY) dan jumlah kuantitas hasil produksi.</li>
                                    <li>Klik Simpan; stok fisik bertambah seketika.</li>
                                </ol>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-dark"><i class="bi bi-arrow-left-right me-1 text-primary"></i> Transfer Stok</h6>
                                <p class="text-muted small">Memindahkan persediaan botol parfum antar gudang utama atau mengirim stok ke toko mitra reseller.</p>
                                <ol class="small text-muted ps-3 mb-0" style="line-height: 1.6;">
                                    <li>Klik <strong>+ Transfer Baru</strong>.</li>
                                    <li>Pilih lokasi asal; stok riil akan otomatis termuat.</li>
                                    <li>Pilih lokasi tujuan dan kuantitas unit.</li>
                                    <li>Nomor <code>TRF/YYYYMMDD/XXXX</code> terbit otomatis, stok asal berkurang dan stok tujuan bertambah.</li>
                                </ol>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-dark"><i class="bi bi-clock-history me-1 text-warning"></i> Pre-Order (PO)</h6>
                                <p class="text-muted small">Mencatat pesanan inden pelanggan yang masih dalam proses peracikan/produksi di pabrik.</p>
                                <ol class="small text-muted ps-3 mb-0" style="line-height: 1.6;">
                                    <li>Input PO dengan status awal <code>PENDING</code>.</li>
                                    <li>Stok PO diisolasi terpisah dan tidak tercampur stok fisik ready.</li>
                                    <li>Saat batch parfum selesai diracik, klik <strong>Tandai Selesai</strong> &rarr; kuantitas PO otomatis dilebur ke stok utama.</li>
                                </ol>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-dark"><i class="bi bi-boxes me-1 text-danger"></i> Ringkasan Stok</h6>
                                <p class="text-muted small">Tinjauan cepat inventori fisik aktual, total botol di seluruh sistem, dan tabel peringatan stok kritis (&lt; 3 pcs).</p>
                                <p class="small text-muted m-0">Membantu tim logistik mengetahui varian parfum apa saja yang harus segera diproduksi ulang.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 4: BUKU KAS & KEUANGAN -->
            <div class="tab-pane fade" id="tab-cash" role="tabpanel">
                <div class="card border-0 shadow-sm p-4 mb-4" style="border: 1px solid var(--color-border) !important; border-radius: 12px;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-wallet2 fs-4 text-dark"></i>
                        <h4 class="font-serif fw-bold m-0 fs-5">Panduan Menu Keuangan (Buku Kas)</h4>
                    </div>
                    <p class="text-muted small">Modul Buku Kas berfungsi sebagai buku besar operasional kas kantor (arus uang masuk riil dan pengeluaran beban usaha).</p>

                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-plus-circle me-1 text-success"></i> Cara Mencatat Transaksi Kas</h6>
                                <ol class="small text-muted ps-3 mb-0" style="line-height: 1.8;">
                                    <li>Buka menu <strong>Keuangan (Buku Kas) &rarr; Catat Kas</strong>.</li>
                                    <li>Tentukan tanggal transaksi kas dan pilih tipe:
                                        <ul>
                                            <li><strong class="text-success">Pemasukan (Income)</strong>: Untuk penerimaan uang penjualan invoice, setoran tunai, atau modal kas.</li>
                                            <li><strong class="text-danger">Pengeluaran (Outcome)</strong>: Untuk biaya operasional, iklan digital (Shopee/TikTok Ads), belanja bahan baku, gaji, sewa, dll.</li>
                                        </ul>
                                    </li>
                                    <li>Pilih Kategori Pos Kas dan masukkan nominal rupiah.</li>
                                    <li>Isi keterangan/deskripsi pengeluaran agar rapi saat diaudit.</li>
                                    <li>Klik <strong>Simpan Transaksi</strong>. Saldo kas dinamis akan otomatis diperbarui.</li>
                                </ol>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-file-earmark-spreadsheet me-1 text-primary"></i> Ekspor Laporan Resmi (Excel &amp; PDF)</h6>
                                <p class="small text-muted">Seluruh mutasi kas di halaman <strong>Riwayat Cashflow</strong> dapat diekspor menjadi laporan resmi pembukuan:</p>
                                <ul class="small text-muted ps-3 mb-3" style="line-height: 1.8;">
                                    <li><strong>Ekspor Excel (.csv)</strong>: Dilengkapi teknologi UTF-8 BOM agar angka nominal rupiah langsung terbaca rapi di Microsoft Excel tanpa kendala karakter aneh.</li>
                                    <li><strong>Ekspor PDF</strong>: Template pratinjau cetak bersih standar A4 siap tanda tangan manajemen.</li>
                                    <li>Filter rentang tanggal sebelum ekspor untuk menghasilkan laporan mingguan, bulanan, atau tahunan.</li>
                                </ul>
                                <div class="badge bg-light text-dark border p-2 text-start d-block small">
                                    <i class="bi bi-calculator me-1 text-success"></i> Saldo Kas dihitung real-time: <code>SUM(Pemasukan) - SUM(Pengeluaran)</code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 5: BAHAN MENTAH & PACKAGING -->
            <div class="tab-pane fade" id="tab-raw" role="tabpanel">
                <div class="card border-0 shadow-sm p-4 mb-4" style="border: 1px solid var(--color-border) !important; border-radius: 12px;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-boxes fs-4 text-dark"></i>
                        <h4 class="font-serif fw-bold m-0 fs-5">Panduan Menu Barang Mentah (Packaging)</h4>
                    </div>
                    <p class="text-muted small">Modul ini digunakan untuk memantau inventori logistik bahan mentah non-parfum, seperti kardus kemasan (Box), botol kaca kosong, sprayer, seal plastik, dan stiker merek.</p>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 h-100 bg-light">
                                <h6 class="fw-bold mb-1"><i class="bi bi-tags-fill me-1 text-primary"></i> Kategori Dinamis</h6>
                                <p class="small text-muted m-0">Admin dapat membuat, mengedit, atau menghapus kategori bahan mentah baru (contoh: BOX, BOTOL, STIKER) secara dinamis sesuai kebutuhan perlengkapan kemasan.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 h-100 bg-light">
                                <h6 class="fw-bold mb-1"><i class="bi bi-arrow-down-circle-fill me-1 text-success"></i> Pengadaan (Mutasi IN)</h6>
                                <p class="small text-muted m-0">Saat membeli kardus atau botol dari supplier, catat di tombol <strong>+ Catat Pengadaan (IN)</strong>. Kuantitas stok bertambah dan biaya belanja dapat langsung dicatat ke buku kas.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 h-100 bg-light">
                                <h6 class="fw-bold mb-1"><i class="bi bi-arrow-up-circle-fill me-1 text-danger"></i> Pemakaian (Mutasi OUT)</h6>
                                <p class="small text-muted m-0">Saat proses perakitan/pengemasan botol parfum siap jual berlangsung, catat di tombol <strong>- Catat Pemakaian (OUT)</strong> untuk mengurangi stok fisik bahan secara akurat.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 6: PUSAT DASHBOARD -->
            <div class="tab-pane fade" id="tab-dash" role="tabpanel">
                <div class="card border-0 shadow-sm p-4 mb-4" style="border: 1px solid var(--color-border) !important; border-radius: 12px;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-grid-1x2-fill fs-4 text-dark"></i>
                        <h4 class="font-serif fw-bold m-0 fs-5">Panduan Menu Pusat Dashboard</h4>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-dark"><i class="bi bi-wallet2 me-1 text-primary"></i> 1. Dashboard Utama (/dashboard)</h6>
                                <p class="small text-muted">Ringkasan eksekutif bisnis harian. Menampilkan saldo kas bersih, pemasukan &amp; pengeluaran bulan ini, total unit stok di seluruh sistem, peringatan stok kritis (&lt; 3 pcs), ketersediaan kardus box &amp; botol, serta grafik tren cashflow 6 bulan.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-dark"><i class="bi bi-boxes me-1 text-success"></i> 2. Dashboard Stok (/stocks/reseller)</h6>
                                <p class="small text-muted">Monitoring sebaran fisik parfum. Menampilkan kartu persediaan di tangan reseller, kartu <em>Varian Stok Habis</em> (dengan tombol pintas langsung ke restock), grafik sebaran persentase stok, dan leaderboard varian terlaris.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-dark"><i class="bi bi-graph-up-arrow me-1 text-warning"></i> 3. Dashboard Penjualan YoY (/invoices/evaluation)</h6>
                                <p class="small text-muted">Analisis evaluasi pertumbuhan Year-on-Year. Membandingkan volume botol dan omset penjualan tahun target vs tahun pembanding, persentase pertumbuhan, breakdown 12 bulan, dan daftar varian paling laku.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-dark"><i class="bi bi-people-fill me-1 text-info"></i> 4. Dashboard Kontrol Sales (/dashboard/sales)</h6>
                                <p class="small text-muted">Monitoring tim lapangan dan reseller. Menampilkan distribusi kontribusi penjualan tiap sales, grafik komparasi performa 6 bulan, dan toggle metrik Volume (Pcs) vs Omset (Rp).</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 7: MASTER DATA & RESELLER -->
            <div class="tab-pane fade" id="tab-admin" role="tabpanel">
                <div class="card border-0 shadow-sm p-4 mb-4" style="border: 1px solid var(--color-border) !important; border-radius: 12px;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-gear-wide-connected fs-4 text-dark"></i>
                        <h4 class="font-serif fw-bold m-0 fs-5">Panduan Master Data &amp; Alur Reseller</h4>
                    </div>

                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-person-check-fill me-1 text-success"></i> Alur Pendaftaran &amp; Persetujuan Reseller</h6>
                                <ol class="small text-muted ps-3 mb-0" style="line-height: 1.8;">
                                    <li>Mitra reseller mendaftar secara mandiri melalui halaman registrasi publik <code>/register</code>.</li>
                                    <li>Akun baru akan berstatus <code>Pending Approval</code> dan belum dapat melakukan login.</li>
                                    <li>Administrator membuka menu <strong>Master Data &rarr; Manajemen User</strong>.</li>
                                    <li>Admin meninjau nama reseller dan mengklik tombol <strong>Setujui (Approve)</strong>.</li>
                                    <li>Sistem secara otomatis mengaktifkan akun dan membuatkan entitas toko lokasi baru khusus reseller tersebut.</li>
                                    <li>Reseller kini dapat login dan mengakses dashboard katalog mandiri mereka.</li>
                                </ol>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-shield-lock-fill me-1 text-primary"></i> Kelola Master Produk &amp; Lokasi (Admin Only)</h6>
                                <ul class="small text-muted ps-3 mb-0" style="line-height: 1.8;">
                                    <li><strong>Produk Parfum (/products)</strong>: Menambah varian baru, mengatur kode SKU, harga modal (HPP), dan harga jual resmi. Varian dapat di-nonaktifkan sementara tanpa menghapus riwayat transaksi.</li>
                                    <li><strong>Lokasi Stok (/locations)</strong>: Mengelola titik penyimpanan fisik parfum (Gudang IWAN, Gudang RIZKY, maupun toko reseller). Setiap lokasi memiliki palet warna matematis HSL Golden Angle yang konsisten.</li>
                                    <li><strong>Portal Publik Bebas Login (/stok-reseller)</strong>: Link katalog publik yang bisa dibagikan ke dropshipper/reseller untuk melihat stok ready tanpa mengekspos margin keuntungan internal.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Histori Versi & Catatan Rilis -->
<div class="modal fade" id="changelogModal" tabindex="-1" aria-labelledby="changelogModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header border-bottom py-3 px-4 bg-light">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-dark text-white d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="bi bi-clock-history fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title font-serif fw-bold text-dark m-0 fs-5" id="changelogModalLabel">Catatan Rilis &amp; Histori Versi</h5>
                        <p class="text-muted small m-0">Rekam jejak pembaruan, penambahan fitur, dan optimasi Scentpeep</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="max-height: 70vh;">
                <!-- Timeline Container -->
                <div class="timeline-changelog d-flex flex-column gap-4">

                    <!-- v2.6 (Current) -->
                    <div class="card border rounded-3 p-3 shadow-xs position-relative" style="border-left: 4px solid var(--color-dark, #111827) !important;">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2 pb-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-dark text-white px-2 py-1 font-mono fw-bold" style="font-size: 0.8rem;">v2.6</span>
                                <h6 class="fw-bold text-dark m-0">Buku Panduan Sistem &amp; Optimasi Performa</h6>
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill font-mono" style="font-size: 0.72rem;">
                                <i class="bi bi-check-circle me-1"></i> Versi Aktif
                            </span>
                        </div>
                        <ul class="small text-muted ps-3 mb-0" style="line-height: 1.8;">
                            <li><strong class="text-dark">Halaman Panduan &amp; Tutorial (/tutorial)</strong>: Pusat edukasi langkah demi langkah dengan lambang tanda tanya (<i class="bi bi-question-circle-fill"></i>) di sidebar &amp; topbar berisikan 7 tab panduan alur bisnis interaktif.</li>
                            <li><strong class="text-dark">Optimasi Kueri Database &amp; Anti N+1</strong>: Penyatuan logika distribusi sales di <code class="font-mono">DashboardController</code> serta perampingan pembacaan kolom spesifik pada <code class="font-mono">StockController</code>.</li>
                            <li><strong class="text-dark">Standar Kualitas Bersih (Clean Code)</strong>: Pembersihan komentar repetitif (anti-slop) dan pengujian otomatis 100% lulus (74 tests passed).</li>
                        </ul>
                    </div>

                    <!-- v2.5 -->
                    <div class="card border rounded-3 p-3 shadow-xs position-relative" style="border-left: 4px solid #4b5563 !important;">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2 pb-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-secondary text-white px-2 py-1 font-mono fw-bold" style="font-size: 0.8rem;">v2.5</span>
                                <h6 class="fw-bold text-dark m-0">Pemisahan Kas dari Invoice &amp; Kustom Tanggal</h6>
                            </div>
                            <span class="badge bg-light text-muted border rounded-pill font-mono" style="font-size: 0.72rem;">Stabil</span>
                        </div>
                        <ul class="small text-muted ps-3 mb-0" style="line-height: 1.8;">
                            <li><strong class="text-dark">Pemisahan Kas Murni (Decoupled Cash Flow)</strong>: Status LUNAS / BELUM LUNAS pada invoice murni sebagai penanda piutang &amp; operasional pesanan. Uang lunas tidak otomatis masuk kas utama agar pencatatan kas riil diinput manual saat dana benar-benar masuk ke kasir/rekening.</li>
                            <li><strong class="text-dark">Kustom Tanggal Invoice (CRUD Tanggal)</strong>: Penambahan kolom <code class="font-mono">tanggal</code> di nota invoice dengan datepicker di formulir create/edit dan dukungan pencatatan nota tanggal lampau (backdating).</li>
                            <li><strong class="text-dark">Pembersihan Ringkasan Redundan</strong>: Menghapus 5 kartu ringkasan ganda di daftar invoice untuk memangkas beban query agregat.</li>
                        </ul>
                    </div>

                    <!-- v2.4 -->
                    <div class="card border rounded-3 p-3 shadow-xs position-relative" style="border-left: 4px solid #6b7280 !important;">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2 pb-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-secondary text-white px-2 py-1 font-mono fw-bold" style="font-size: 0.8rem;">v2.4</span>
                                <h6 class="fw-bold text-dark m-0">Pemisahan Menu Tambah Stock &amp; Deep-Link Restock</h6>
                            </div>
                            <span class="badge bg-light text-muted border rounded-pill font-mono" style="font-size: 0.72rem;">Stabil</span>
                        </div>
                        <ul class="small text-muted ps-3 mb-0" style="line-height: 1.8;">
                            <li><strong class="text-dark">Menu Baru Tambah Stock (/stocks/restock)</strong>: Memindahkan tabel katalog stok fisik lengkap 15 item/halaman dari dashboard ke menu mandiri di atas Ringkasan Stok.</li>
                            <li><strong class="text-dark">Filter Status Ketersediaan</strong>: Tombol filter cepat (Semua, Ready, Menipis, Habis) dan modal penambahan stok sehabis produksi.</li>
                            <li><strong class="text-dark">Pembersihan Dashboard Stok</strong>: Tampilan dashboard difokuskan pada kartu KPI dan tombol varian habis terhubung langsung (deep-link) ke halaman Tambah Stock.</li>
                        </ul>
                    </div>

                    <!-- v2.3 -->
                    <div class="card border rounded-3 p-3 shadow-xs position-relative" style="border-left: 4px solid #9ca3af !important;">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2 pb-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-secondary text-white px-2 py-1 font-mono fw-bold" style="font-size: 0.8rem;">v2.3</span>
                                <h6 class="fw-bold text-dark m-0">Transfer Stok Multi-Lokasi &amp; Isolasi Kas Murni</h6>
                            </div>
                            <span class="badge bg-light text-muted border rounded-pill font-mono" style="font-size: 0.72rem;">Stabil</span>
                        </div>
                        <ul class="small text-muted ps-3 mb-0" style="line-height: 1.8;">
                            <li><strong class="text-dark">Full CRUD Transfer Stok (/stocks/transfers)</strong>: Mutasi stok antar-lokasi dengan nomor audit otomatis (<code class="font-mono">TRF/YYYYMMDD/xxxx</code>), kalkulator stok real-time, dan fitur edit/cancel transfer atomik.</li>
                            <li><strong class="text-dark">Isolasi Penuh Buku Kas (/transactions)</strong>: Formulir kas dikhususkan 100% pada kas operasional murni (iklan, operasional, gaji, packaging) tanpa tumpang-tindih mutasi parfum.</li>
                            <li><strong class="text-dark">CRUD Dinamis Kategori Bahan Mentah</strong>: Pengelolaan fleksibel kategori material kemasan di luar box dan botol standar.</li>
                        </ul>
                    </div>

                    <!-- v2.2 -->
                    <div class="card border rounded-3 p-3 shadow-xs position-relative" style="border-left: 4px solid #d1d5db !important;">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2 pb-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light text-dark border px-2 py-1 font-mono fw-bold" style="font-size: 0.8rem;">v2.2</span>
                                <h6 class="fw-bold text-dark m-0">Manajemen Reseller, Evaluasi YoY, &amp; Sub-Dashboard</h6>
                            </div>
                            <span class="badge bg-light text-muted border rounded-pill font-mono" style="font-size: 0.72rem;">Stabil</span>
                        </div>
                        <ul class="small text-muted ps-3 mb-0" style="line-height: 1.8;">
                            <li><strong class="text-dark">Manajemen Akun Reseller (/users)</strong>: CRUD user reseller internal dengan pembatasan hak akses rute internal.</li>
                            <li><strong class="text-dark">Evaluasi Penjualan Tahunan (YoY)</strong>: Analisis performa omset bulanan dan tahunan per sales.</li>
                            <li><strong class="text-dark">Pusat Dashboard Terorganisir</strong>: 4 tab sub-dashboard terarah (Utama, Stok, Penjualan, dan Bahan Mentah).</li>
                        </ul>
                    </div>

                    <!-- v2.0 -->
                    <div class="card border rounded-3 p-3 shadow-xs position-relative" style="border-left: 4px solid #e5e7eb !important;">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2 pb-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light text-dark border px-2 py-1 font-mono fw-bold" style="font-size: 0.8rem;">v2.0</span>
                                <h6 class="fw-bold text-dark m-0">Invoice Penjualan, Pre-Order, Bahan Mentah &amp; Palet HSL</h6>
                            </div>
                            <span class="badge bg-light text-muted border rounded-pill font-mono" style="font-size: 0.72rem;">Mayor</span>
                        </div>
                        <ul class="small text-muted ps-3 mb-0" style="line-height: 1.8;">
                            <li><strong class="text-dark">Penerbitan Invoice Penjualan</strong>: Invoice per varian dengan pemotongan stok otomatis dari lokasi pemegang barang (<code class="font-mono">IWAN</code> atau <code class="font-mono">RIZKY</code>).</li>
                            <li><strong class="text-dark">Pre-Order (PO) &amp; Stok Pending</strong>: Segmen antrean produksi barang di luar stok utama sebelum siap dipindahkan.</li>
                            <li><strong class="text-dark">Pengadaan Bahan Mentah</strong>: Inventaris material packaging (Box Packaging &amp; Botol Kaca) lengkap dengan riwayat mutasi masuk/keluar.</li>
                            <li><strong class="text-dark">Katalog Publik Reseller (/stok-reseller)</strong>: Monitoring ketersediaan stok tanpa login untuk dropshipper/reseller.</li>
                            <li><strong class="text-dark">Palet Warna HSL Sudut Emas</strong>: Warna identitas deterministik otomatis untuk tiap pemegang stok (Iwan hijau, Rizky biru, Vanny crimson).</li>
                        </ul>
                    </div>

                    <!-- v1.5 -->
                    <div class="card border rounded-3 p-3 shadow-xs position-relative" style="border-left: 4px solid #f3f4f6 !important;">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2 pb-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light text-dark border px-2 py-1 font-mono fw-bold" style="font-size: 0.8rem;">v1.5</span>
                                <h6 class="fw-bold text-dark m-0">Modernisasi UI Luxury Monochrome &amp; Chart.js</h6>
                            </div>
                            <span class="badge bg-light text-muted border rounded-pill font-mono" style="font-size: 0.72rem;">Stabil</span>
                        </div>
                        <ul class="small text-muted ps-3 mb-0" style="line-height: 1.8;">
                            <li><strong class="text-dark">Desain Luxury Monochrome</strong>: Standarisasi antarmuka berbasis Bootstrap 5.3.3 + AdminLTE 4 Components + Bootstrap Icons via CDN.</li>
                            <li><strong class="text-dark">Grafik Interaktif</strong>: Visualisasi tren arus kas dan pergerakan stok parfum berbasis Chart.js.</li>
                        </ul>
                    </div>

                    <!-- v1.0 -->
                    <div class="card border rounded-3 p-3 shadow-xs position-relative" style="border-left: 4px solid #f9fafb !important;">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2 pb-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light text-dark border px-2 py-1 font-mono fw-bold" style="font-size: 0.8rem;">v1.0</span>
                                <h6 class="fw-bold text-dark m-0">Fondasi Awal Sistem &amp; Manajemen Stok Multi-Lokasi</h6>
                            </div>
                            <span class="badge bg-light text-muted border rounded-pill font-mono" style="font-size: 0.72rem;">Rilis Awal</span>
                        </div>
                        <ul class="small text-muted ps-3 mb-0" style="line-height: 1.8;">
                            <li><strong class="text-dark">Struktur Data Inti</strong>: Master produk parfum, master lokasi toko/gudang, dan matriks stok fisik.</li>
                            <li><strong class="text-dark">Autentikasi &amp; Kas Harian</strong>: Manajemen user Admin/Staff dan pencatatan kas masuk/keluar dasar.</li>
                        </ul>
                    </div>

                </div>
            </div>
            <div class="modal-footer py-2 px-4 bg-light border-top d-flex justify-content-between align-items-center">
                <span class="text-muted small font-mono" style="font-size: 0.75rem;">
                    <i class="bi bi-cpu me-1"></i> Laravel 12 &bull; PHP 8.2+ &bull; MySQL &bull; Bootstrap 5.3.3
                </span>
                <button type="button" class="btn btn-sm btn-dark px-3 py-1 rounded-2" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection
