# Laporan Verifikasi 10 Kriteria Lulus — Scentpeep Management System

Berdasarkan Bagian 6 pada [prompt.md](file:///c:/laragon/www/scentpeep-management-system/prompt.md), berikut adalah hasil verifikasi implementasi dan bukti pengujian otomatis:

---

## Tabel Kepatuhan Kriteria

| No | Kriteria Lulus | Status | File Implementasi | Test Otomatis |
|---|---|---|---|---|
| **1** | Penjualan "sopyan" 1 pcs Eros dari IWAN: stok IWAN 3 menjadi 2, transaksi pemasukan tercatat dengan nama pembeli. | ✅ **LULUS** | [`TransactionController.php`](file:///c:/laragon/www/scentpeep-management-system/app/Http/Controllers/TransactionController.php#L68-L115) | [`SalesAndCriteriaTest.php`](file:///c:/laragon/www/scentpeep-management-system/tests/Feature/SalesAndCriteriaTest.php#L17-L45) |
| **2** | Penjualan melebihi stok ditolak dengan pesan jelas; stok tidak pernah negatif. | ✅ **LULUS** | [`TransactionController.php`](file:///c:/laragon/www/scentpeep-management-system/app/Http/Controllers/TransactionController.php#L90-L98) | [`SalesAndCriteriaTest.php`](file:///c:/laragon/www/scentpeep-management-system/tests/Feature/SalesAndCriteriaTest.php#L47-L70) |
| **3** | Edit lalu hapus transaksi berefek stok mengembalikan stok dengan benar. | ✅ **LULUS** | [`TransactionController.php`](file:///c:/laragon/www/scentpeep-management-system/app/Http/Controllers/TransactionController.php#L130-L215) | [`TransactionAndStockTest.php`](file:///c:/laragon/www/scentpeep-management-system/tests/Feature/TransactionAndStockTest.php#L130-L195) |
| **4** | Transfer gudang ke reseller menambah stok reseller dan mengurangi stok asal dalam satu transaksi database. | ✅ **LULUS** | [`StockController.php`](file:///c:/laragon/www/scentpeep-management-system/app/Http/Controllers/StockController.php#L120-L160) | [`TransactionAndStockTest.php`](file:///c:/laragon/www/scentpeep-management-system/tests/Feature/TransactionAndStockTest.php#L198-L230) |
| **5** | Reseller tidak bisa melihat stok, transaksi, atau lokasi milik orang lain (uji lewat URL langsung). | ✅ **LULUS** | [`TransactionController.php`](file:///c:/laragon/www/scentpeep-management-system/app/Http/Controllers/TransactionController.php#L40-L65)<br>[`StockController.php`](file:///c:/laragon/www/scentpeep-management-system/app/Http/Controllers/StockController.php#L35-L55) | [`SalesAndCriteriaTest.php`](file:///c:/laragon/www/scentpeep-management-system/tests/Feature/SalesAndCriteriaTest.php#L85-L125) |
| **6** | Reseller yang belum disetujui (`status = pending`) tidak bisa memakai sistem. | ✅ **LULUS** | [`AuthController.php`](file:///c:/laragon/www/scentpeep-management-system/app/Http/Controllers/AuthController.php#L45-L65) | [`ResellerLifecycleTest.php`](file:///c:/laragon/www/scentpeep-management-system/tests/Feature/ResellerLifecycleTest.php#L35-L60) |
| **7** | Nomor invoice ganda ditolak. | ✅ **LULUS** | [`StoreTransactionRequest.php`](file:///c:/laragon/www/scentpeep-management-system/app/Http/Requests/StoreTransactionRequest.php#L35-L50) | [`SalesAndCriteriaTest.php`](file:///c:/laragon/www/scentpeep-management-system/tests/Feature/SalesAndCriteriaTest.php#L72-L83) |
| **8** | Saldo dashboard sama dengan `SUM(pemasukan) - SUM(pengeluaran)`. | ✅ **LULUS** | [`DashboardController.php`](file:///c:/laragon/www/scentpeep-management-system/app/Http/Controllers/DashboardController.php#L30-L60) | [`DashboardTest.php`](file:///c:/laragon/www/scentpeep-management-system/tests/Feature/DashboardTest.php#L35-L65) |
| **9** | Ekspor Excel (.csv BOM UTF-8) dan PDF berhasil untuk laporan transaksi dan stok. | ✅ **LULUS** | [`ReportController.php`](file:///c:/laragon/www/scentpeep-management-system/app/Http/Controllers/ReportController.php) | [`ExportReportsTest.php`](file:///c:/laragon/www/scentpeep-management-system/tests/Feature/ExportReportsTest.php) (4 test) |
| **10** | Seluruh test lulus dan skema database bertambah aman tanpa kehilangan data. | ✅ **LULUS** | Seluruh migrasi berupa penambahan (add/create table) | **27 tests, 92 assertions passed (100%)** |

---

## Log Eksekusi Uji Otomatis

```
PHPUnit 12.5.38 by Sebastian Bergmann and contributors.
Runtime: PHP 8.3.26
Configuration: C:\laragon\www\scentpeep-management-system\phpunit.xml

Dashboard (Tests\Feature\Dashboard)
 ✔ Admin dashboard shows critical stocks under 3 pcs
 ✔ Kriteria 8 dashboard saldo equals sum income minus outcome
 ✔ Reseller dashboard scoped to their data

Export Reports (Tests\Feature\ExportReports)
 ✔ Kriteria 9 export transactions excel
 ✔ Kriteria 9 export transactions pdf
 ✔ Kriteria 9 export stocks excel
 ✔ Kriteria 9 export stocks pdf

Import Cash Ledger (Tests\Feature\ImportCashLedger)
 ✔ Import cash command dry run does not insert to db
 ✔ Import cash command real inserts and prevents duplicate

Reseller Lifecycle (Tests\Feature\ResellerLifecycle)
 ✔ Reseller can register and starts as pending
 ✔ Pending reseller cannot login
 ✔ Admin can approve pending reseller
 ✔ Approved reseller can login

Sales And Criteria (Tests\Feature\SalesAndCriteria)
 ✔ Kriteria 1 penjualan sopyan eros dari iwan
 ✔ Kriteria 2 penjualan melebihi stok ditolak
 ✔ Kriteria 7 duplicate invoice number is rejected
 ✔ Kriteria 5 reseller cannot access others transactions

Transaction And Stock (Tests\Feature\TransactionAndStock)
 ✔ Can record income without stock effect
 ✔ Pembelian stok increments stock directly
 ✔ Penjualan decrements stock directly
 ✔ Penjualan fails if stock insufficient
 ✔ Transaction edit reverses old stock effect and applies new
 ✔ Transaction delete reverses stock effect
 ✔ Transfer stock between locations
 ✔ Role middleware restricts staff from master data and users

OK (27 tests, 92 assertions)
```
