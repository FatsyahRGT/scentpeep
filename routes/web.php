<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

    // 1. Transactions (Buku Kas & Stok)
    Route::resource('transactions', TransactionController::class);

    // 2. Stocks (Ringkasan Matriks Stok & Transfer Antar Lokasi)
    Route::prefix('stocks')->name('stocks.')->group(function () {
        Route::get('/', [StockController::class, 'index'])->name('index');
        Route::get('/transfers', [StockController::class, 'transfers'])->name('transfers');
        Route::get('/transfer', [StockController::class, 'createTransfer'])->name('transfer.create');
        Route::post('/transfer', [StockController::class, 'storeTransfer'])->name('transfer.store');
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
