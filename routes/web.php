<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PreOrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RawMaterialCategoryController;
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public Reseller Stock Dashboard (Dapat diakses langsung tanpa login)
Route::get('/stok-reseller', [StockController::class, 'publicResellerDashboard'])->name('stocks.publicReseller');
Route::get('/katalog-stok', fn () => redirect()->route('stocks.publicReseller'));

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

// Authenticated Routes (Admin & Staff & Reseller)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 1. Invoices (Penjualan Parfum per Varian)
    Route::post('/invoices/{invoice}/mark-paid', [InvoiceController::class, 'markPaid'])->name('invoices.markPaid');
    Route::resource('invoices', InvoiceController::class);

    // 2. Pre-Orders (Stok Pending PO)
    Route::post('/pre-orders/{preOrder}/complete', [PreOrderController::class, 'complete'])->name('pre-orders.complete');
    Route::resource('pre-orders', PreOrderController::class);

    // 3. Raw Materials (Pengadaan Barang Mentah & Manajemen Kategori)
    Route::post('/raw-materials/categories', [RawMaterialCategoryController::class, 'store'])->name('raw-materials.categories.store');
    Route::put('/raw-materials/categories/{rawMaterialCategory}', [RawMaterialCategoryController::class, 'update'])->name('raw-materials.categories.update');
    Route::delete('/raw-materials/categories/{rawMaterialCategory}', [RawMaterialCategoryController::class, 'destroy'])->name('raw-materials.categories.destroy');
    Route::post('/raw-materials/{rawMaterial}/procure', [RawMaterialController::class, 'procure'])->name('raw-materials.procure');
    Route::post('/raw-materials/{rawMaterial}/consume', [RawMaterialController::class, 'consume'])->name('raw-materials.consume');
    Route::resource('raw-materials', RawMaterialController::class);

    // 4. Transactions (Buku Kas & Keuangan)
    Route::resource('transactions', TransactionController::class);

    // 5. Stocks (Ringkasan Matriks Stok, Restock Produksi, & Reseller Dashboard)
    Route::prefix('stocks')->name('stocks.')->group(function () {
        Route::get('/', [StockController::class, 'index'])->name('index');
        Route::get('/reseller', [StockController::class, 'resellerStocks'])->name('reseller');
        Route::post('/manual-restock', [StockController::class, 'manualRestock'])->name('manualRestock');
        Route::get('/transfers', [StockController::class, 'transfers'])->name('transfers');
        Route::get('/transfer', [StockController::class, 'createTransfer'])->name('transfer.create');
        Route::post('/transfer', [StockController::class, 'storeTransfer'])->name('transfer.store');
        Route::get('/transfer/{stockTransfer}/edit', [StockController::class, 'editTransfer'])->name('transfer.edit');
        Route::put('/transfer/{stockTransfer}', [StockController::class, 'updateTransfer'])->name('transfer.update');
        Route::delete('/transfer/{stockTransfer}', [StockController::class, 'destroyTransfer'])->name('transfer.destroy');
    });

    // Reports Export (Excel & PDF)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/transactions/excel', [\App\Http\Controllers\ReportController::class, 'exportTransactionsExcel'])->name('transactions.excel');
        Route::get('/transactions/pdf', [\App\Http\Controllers\ReportController::class, 'exportTransactionsPdf'])->name('transactions.pdf');
        Route::get('/stocks/excel', [\App\Http\Controllers\ReportController::class, 'exportStocksExcel'])->name('stocks.excel');
        Route::get('/stocks/pdf', [\App\Http\Controllers\ReportController::class, 'exportStocksPdf'])->name('stocks.pdf');
    });

    // 3. Admin Only Routes (Master Data & Manajemen Pengguna)
    Route::middleware('admin')->group(function () {
        // Products
        Route::post('/products/{product}/toggle', [ProductController::class, 'toggle'])->name('products.toggle');
        Route::resource('products', ProductController::class);

        // Locations
        Route::resource('locations', LocationController::class);

        // User Management & Reseller Approval
        Route::post('/users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');
        Route::post('/users/{user}/reject', [UserController::class, 'reject'])->name('users.reject');
        Route::post('/users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
        Route::resource('users', UserController::class);
    });
});
