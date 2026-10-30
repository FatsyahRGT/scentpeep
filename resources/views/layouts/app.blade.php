<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Dashboard') - Scentpeep Management System</title>

    <!-- Google Fonts: Source Serif 4 + Plus Jakarta Sans + JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Source+Serif+4:ital,opsz,wght@0,8..60,400..700;1,8..60,400..700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3.3 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

    <style>
        /* ==========================================================
           SCENTPEEP LUXURY NICHE MONOCHROME DESIGN SYSTEM
           ========================================================== */
        :root {
            --font-serif: 'Source Serif 4', Charter, Georgia, Cambria, 'Times New Roman', serif;
            --font-sans: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --font-mono: 'JetBrains Mono', Menlo, Monaco, Consolas, monospace;
            --color-bg: #f9f9f8;
            --color-surface: #ffffff;
            --color-surface-hover: #f4f3f0;
            --color-text-main: #171717;
            --color-text-muted: #666666;
            --color-text-subtle: #8a8a86;
            --color-border: #e8e6e1;
            --color-border-subtle: #f0eee9;
            --scent-dark: #121212;
            --scent-dark-hover: #000000;
            --scent-amber: #b38b4d;
            --scent-amber-subtle: rgba(179, 139, 77, 0.08);
        }

        body {
            font-family: var(--font-sans);
            color: var(--color-text-main);
            background-color: var(--color-bg);
            letter-spacing: -0.012em;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }
        html, body { overflow-x: hidden; max-width: 100vw; }

        /* Typography Helpers */
        .font-serif { font-family: var(--font-serif); }
        .font-sans { font-family: var(--font-sans); }
        .font-mono-num { font-family: var(--font-mono); font-variant-numeric: tabular-nums; }
        .border-hairline { border-color: var(--color-border) !important; }
        .border-hairline-b { border-bottom: 1px solid var(--color-border); }

        h1, h2, h3, h4, .h1, .h2, .h3, .h4, .card-title {
            font-family: var(--font-serif);
            letter-spacing: -0.02em;
        }

        /* Luxury Button Overrides */
        .btn-primary {
            background-color: var(--scent-dark) !important;
            border-color: var(--scent-dark) !important;
            color: #ffffff !important;
        }
        .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
            background-color: var(--scent-dark-hover) !important;
            border-color: var(--scent-dark-hover) !important;
            color: #ffffff !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15) !important;
        }
        .btn-outline-primary {
            color: var(--scent-dark) !important;
            border-color: var(--color-border) !important;
        }
        .btn-outline-primary:hover, .btn-outline-primary:focus, .btn-outline-primary:active, .btn-outline-primary.active {
            background-color: var(--scent-dark) !important;
            border-color: var(--scent-dark) !important;
            color: #ffffff !important;
        }
        .nav-pills .nav-link.active {
            background-color: var(--scent-dark) !important;
            color: #ffffff !important;
        }

        /* Brand Lockup */
        .scent-brand {
            font-family: var(--font-serif);
            font-weight: 700;
            font-size: 1.35rem;
            letter-spacing: -0.035em;
            color: var(--scent-dark);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }
        .scent-brand:hover { color: #000000; }
        .scent-brand-sub {
            font-family: var(--font-sans);
            font-size: 0.65rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--color-text-muted);
            font-weight: 600;
        }

        /* ==========================================================
           SIDEBAR
           ========================================================== */
        .admin-sidebar {
            position: fixed;
            top: 0; left: 0; bottom: 0;
            width: 250px;
            background-color: #ffffff;
            border-right: 1px solid var(--color-border);
            display: flex;
            flex-direction: column;
            z-index: 1050;
            overflow-y: auto;
            transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1), transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .nav-side-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 0.85rem;
            color: var(--color-text-muted);
            font-weight: 500;
            font-size: 0.85rem;
            border-radius: 6px;
            text-decoration: none;
            transition: background 0.15s ease, color 0.15s ease;
        }
        .nav-side-link:hover { color: var(--color-text-main); background-color: var(--color-surface-hover); }
        .nav-side-link.active { color: var(--scent-dark); background-color: #f1efea; font-weight: 600; }
        .nav-side-link i { font-size: 1.1rem; }
        .nav-side-link[data-bs-toggle="collapse"] .nav-chevron {
            transition: transform 0.2s ease;
            font-size: 0.72rem;
            color: var(--color-text-subtle);
        }
        .nav-side-link[data-bs-toggle="collapse"][aria-expanded="true"] .nav-chevron {
            transform: rotate(180deg);
        }
        .nav-side-sub {
            border-left: 2px solid var(--color-border);
            margin-left: 1.35rem;
            padding-left: 0.4rem;
            margin-top: 0.2rem;
            margin-bottom: 0.25rem;
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
        }
        .nav-side-sub .nav-side-link {
            padding: 0.4rem 0.65rem;
            font-size: 0.82rem;
        }

        .sidebar-section-label {
            font-size: 0.65rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--color-text-subtle);
            font-weight: 700;
            padding: 0 0.85rem;
            margin-top: 1.25rem;
            margin-bottom: 0.5rem;
        }
        .sidebar-profile-box { border-top: 1px solid var(--color-border); padding-top: 0.75rem; }

        .user-avatar-circle {
            width: 34px; height: 34px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        /* Page Wrapper */
        body.has-sidebar .page-wrapper { margin-left: 250px; }
        .page-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ==========================================================
           LUXURY BUTTONS
           ========================================================== */
        .btn-scent {
            background-color: var(--scent-dark);
            color: #ffffff;
            border: 1px solid var(--scent-dark);
            font-weight: 500;
            font-size: 0.875rem;
            border-radius: 9999px;
            padding: 0.45rem 1.25rem;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-scent:hover { background-color: var(--scent-dark-hover); color: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.12); }
        .btn-scent-outline {
            background-color: transparent;
            color: var(--color-text-main);
            border: 1px solid var(--color-border);
            font-weight: 500;
            font-size: 0.875rem;
            border-radius: 9999px;
            padding: 0.45rem 1.2rem;
            transition: all 0.2s ease;
        }
        .btn-scent-outline:hover { background-color: #fff; border-color: var(--scent-dark); color: #000; }

        /* ==========================================================
           STAT CARDS (KPI)
           ========================================================== */
        .scent-stat-card {
            background: #ffffff;
            border: 1px solid var(--color-border);
            border-radius: 8px;
            padding: 1.15rem 1.35rem;
            height: 100%;
            transition: border-color 0.15s ease;
        }
        .scent-stat-card:hover { border-color: #c4c1b9; }
        .scent-stat-value {
            font-family: var(--font-serif);
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--scent-dark);
            line-height: 1.15;
        }
        .scent-stat-label {
            font-size: 0.72rem;
            color: var(--color-text-muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0.3rem;
            font-weight: 600;
        }

        /* ==========================================================
           DATA TABLE CARDS
           ========================================================== */
        .data-table-card {
            background: #ffffff;
            border: 1px solid var(--color-border);
            border-radius: 8px;
            overflow: hidden;
        }
        .data-row {
            padding: 0.9rem 1.25rem;
            border-bottom: 1px solid var(--color-border-subtle);
            transition: background-color 0.12s ease;
        }
        .data-row:last-child { border-bottom: none; }
        .data-row:hover { background-color: #faf9f6; }

        /* Stock Indicators */
        .stock-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            padding: 0.2rem 0.55rem;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.825rem;
            line-height: 1;
        }
        .stock-safe { background-color: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
        .stock-warning { background-color: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
        .stock-danger { background-color: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

        /* ==========================================================
           FORMS
           ========================================================== */
        .form-select, .form-control {
            border-radius: 8px;
            border: 1px solid var(--color-border);
            padding: 0.55rem 0.85rem;
            font-size: 0.9rem;
            background-color: #ffffff;
            color: var(--color-text-main);
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .form-select:focus, .form-control:focus {
            border-color: var(--scent-dark);
            box-shadow: 0 0 0 0.15rem rgba(18,18,18,0.08);
            background-color: #ffffff;
        }

        /* ==========================================================
           TABLES & CARDS
           ========================================================== */
        .table > :not(caption) > * > * { padding: 0.75rem 0.85rem; }
        .table-hover tbody tr { transition: background-color 0.12s ease; }
        .table-hover tbody tr:hover { background-color: #faf9f6; }

        .card { border-radius: 8px; border: 1px solid var(--color-border); box-shadow: none; }
        .card-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--color-border); background-color: transparent; }
        .card-title { margin-bottom: 0; font-size: 1.15rem; font-weight: 600; }
        .card-tools { display: flex; align-items: center; gap: 0.5rem; margin-left: auto; }
        .badge { font-weight: 600; letter-spacing: 0.3px; }

        /* AdminLTE 4 Pagination */
        .pagination { display: flex; padding-left: 0; list-style: none; margin: 0; }
        .pagination-sm .page-link {
            padding: 0.28rem 0.65rem;
            font-size: 0.8125rem;
            border-radius: 4px;
            margin: 0 1px;
            text-decoration: none;
        }
        .pagination .page-item .page-link {
            color: var(--color-text-main, #171717);
            border: 1px solid var(--color-border, #e8e6e1);
            background-color: #ffffff;
            transition: all 0.15s ease;
        }
        .pagination .page-item .page-link:hover {
            background-color: var(--color-surface-hover, #f4f3f0);
            color: #000000;
            border-color: #c4c1b9;
        }
        .pagination .page-item.active .page-link {
            background-color: var(--scent-dark, #121212) !important;
            border-color: var(--scent-dark, #121212) !important;
            color: #ffffff !important;
            font-weight: 600;
        }
        .pagination .page-item.disabled .page-link {
            color: var(--color-text-subtle, #8a8a86);
            background-color: #faf9f6;
            border-color: var(--color-border-subtle, #f0eee9);
            pointer-events: none;
        }

        /* Pagination */
        .pagination-luxury .page-link {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 36px; height: 36px; padding: 0 0.5rem;
            font-size: 0.85rem; font-weight: 500;
            color: var(--color-text-main);
            background: transparent;
            border: 1px solid transparent;
            border-radius: 6px;
            transition: all 0.15s ease;
        }
        .pagination-luxury .page-link:hover { background-color: #f1efea; color: #000; }
        .pagination-luxury .active .page-link { background-color: var(--scent-dark); color: #fff; font-weight: 600; border-color: var(--scent-dark); }
        .pagination-luxury .disabled .page-link { color: #c7c5be; pointer-events: none; }

        /* ==========================================================
           TABLER CLASS COMPATIBILITY LAYER
           (prevents child views from breaking during migration)
           ========================================================== */
        .bg-blue-lt { background-color: rgba(32,107,196,0.08) !important; }
        .bg-primary-lt { background-color: rgba(32,107,196,0.08) !important; }
        .bg-success-lt { background-color: rgba(47,179,68,0.08) !important; }
        .bg-danger-lt { background-color: rgba(214,57,57,0.08) !important; }
        .bg-cyan-lt { background-color: rgba(23,162,184,0.08) !important; }
        .bg-warning-lt { background-color: rgba(245,159,0,0.08) !important; }
        .text-cyan { color: #17a2b8 !important; }
        .text-teal { color: #0ca678 !important; }
        .avatar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px; height: 40px;
            border-radius: 50%;
            font-weight: 700;
        }
        .avatar-sm { width: 32px; height: 32px; font-size: 0.8rem; }
        .avatar-xs { width: 26px; height: 26px; font-size: 0.7rem; }
        .card-sm .card-body { padding: 0.75rem !important; }
        .btn-ghost-secondary { background: transparent; border: none; color: var(--color-text-muted); }
        .btn-ghost-secondary:hover { background-color: var(--color-surface-hover); color: var(--color-text-main); }
        .btn-ghost-danger { background: transparent; border: none; color: #d63939; }
        .btn-ghost-danger:hover { background-color: #fef2f2; }
        .btn-ghost-light { background: transparent; border: none; color: rgba(255,255,255,0.7); }
        .btn-ghost-light:hover { color: #fff; }
        .btn-icon { padding: 0.35rem; line-height: 1; }
        .hr-text { font-size: 0.65rem; letter-spacing: 0.1em; text-transform: uppercase; color: var(--color-text-subtle); font-weight: 700; padding: 0 0.85rem; margin-top: 1.25rem; margin-bottom: 0.5rem; }
        .text-danger-lt { color: rgba(214,57,57,0.7) !important; }
        .tracking-tight { letter-spacing: -0.02em; }

        /* ==========================================================
           RESPONSIVE: MOBILE SIDEBAR
           ========================================================== */
        @media (max-width: 991.98px) {
            .admin-sidebar {
                width: 280px !important;
                max-width: 85vw;
                transform: translateX(-110%) !important;
                visibility: hidden !important;
                display: none !important;
                pointer-events: none !important;
            }
            body.sidebar-mobile-open .admin-sidebar {
                display: flex !important;
                transform: translateX(0) !important;
                visibility: visible !important;
                pointer-events: auto !important;
                box-shadow: 4px 0 25px rgba(0,0,0,0.15);
            }
            body.has-sidebar .page-wrapper { margin-left: 0 !important; }
            body.sidebar-mobile-open { overflow: hidden; }
        }

        /* Desktop Collapse */
        @media (min-width: 992px) {
            body.sidebar-collapsed.has-sidebar .admin-sidebar { width: 72px !important; overflow-x: hidden; }
            body.sidebar-collapsed.has-sidebar .page-wrapper { margin-left: 72px !important; }

            body.sidebar-collapsed .admin-sidebar .sidebar-brand-text,
            body.sidebar-collapsed .admin-sidebar .nav-link-title,
            body.sidebar-collapsed .admin-sidebar .sidebar-section-label,
            body.sidebar-collapsed .admin-sidebar .user-info-text { display: none !important; }

            body.sidebar-collapsed .admin-sidebar .nav-side-link {
                justify-content: center !important;
                padding: 0.7rem 0 !important;
            }
            body.sidebar-collapsed .admin-sidebar .nav-side-link i { margin-right: 0 !important; font-size: 1.25rem; }
            body.sidebar-collapsed .admin-sidebar .sidebar-header-row { justify-content: center !important; }
            body.sidebar-collapsed .admin-sidebar .sidebar-footer { padding: 0.75rem 0.25rem !important; }
            body.sidebar-collapsed .admin-sidebar .sidebar-footer-row {
                flex-direction: column !important; align-items: center !important; gap: 0.5rem !important;
            }
        }

        /* Sidebar Backdrop */
        .sidebar-backdrop {
            display: none; position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: rgba(23,23,23,0.4);
            z-index: 1040; opacity: 0;
            transition: opacity 0.25s ease;
        }
        @media (max-width: 991.98px) {
            body.sidebar-mobile-open .sidebar-backdrop { display: block; opacity: 1; }
        }

        /* Print & Chart */
        @media print {
            .admin-sidebar, .page-topbar, .sidebar-backdrop { display: none !important; }
            body.has-sidebar .page-wrapper { margin-left: 0 !important; }
        }
        @media (max-width: 768px) {
            .card-body canvas { max-height: 250px !important; }
        }
    </style>
    @stack('styles')
</head>
<body @auth class="has-sidebar" @endauth>
    <div class="page">
        @auth
        <!-- Mobile Top Navbar (< 992px) -->
        <header class="d-lg-none sticky-top py-2 px-3" style="background-color: var(--scent-dark); border-bottom: 1px solid rgba(255,255,255,0.08); z-index: 1040;">
            <div class="container-fluid p-0 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-1">
                    <button class="btn text-white border-0 d-inline-flex align-items-center justify-content-center p-0" type="button" onclick="toggleMobileSidebar()" aria-label="Toggle Sidebar" style="width: 44px; height: 44px;">
                        <i class="bi bi-list fs-2"></i>
                    </button>
                    <a href="{{ route('dashboard') }}" class="scent-brand text-white text-decoration-none" style="font-size:1.15rem;">
                        Scentpeep<span class="text-white-50">.</span>
                    </a>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="user-avatar-circle bg-white text-dark" style="width:28px;height:28px;font-size:0.7rem;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </span>
                    <form action="{{ route('logout') }}" method="POST" class="m-0" onsubmit="return confirm('Keluar dari sesi Scentpeep?');">
                        @csrf
                        <button type="submit" class="btn text-white border-0 d-inline-flex align-items-center justify-content-center p-0" title="Keluar" aria-label="Keluar" style="width: 44px; height: 44px;">
                            <i class="bi bi-box-arrow-right fs-5"></i>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Sidebar Backdrop (Mobile) -->
        <div class="sidebar-backdrop" onclick="toggleMobileSidebar(false)"></div>

        <!-- Sidebar Navigation -->
        <aside class="admin-sidebar p-3">
            <div class="d-flex flex-column h-100">
                <!-- Brand -->
                <div class="sidebar-header-row d-flex align-items-center justify-content-between mb-2">
                    <a href="{{ route('dashboard') }}" class="scent-brand text-decoration-none">
                        <span>Scentpeep<span class="text-secondary">.</span></span>
                    </a>
                    <button type="button" class="btn btn-sm border-0 text-muted d-lg-none p-1" onclick="toggleMobileSidebar(false)" aria-label="Close sidebar">
                        <i class="bi bi-x-lg fs-5"></i>
                    </button>
                </div>
                <div class="sidebar-brand-text">
                    <div class="scent-brand-sub mb-3">Stock Management</div>
                </div>

                <!-- Navigation Links -->
                <div class="flex-grow-1 overflow-y-auto d-flex flex-column gap-1">
                    <a class="nav-side-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}" onclick="closeMobileSidebarIfOpen()">
                        <i class="bi bi-grid-1x2-fill"></i>
                        <span class="nav-link-title">Dashboard</span>
                    </a>

                    @if(Auth::user()->isReseller())
                    <!-- Reseller Menu: Sebaran Stok -->
                    <a class="nav-side-link {{ request()->routeIs('stocks.reseller') ? 'active' : '' }}" href="{{ route('stocks.reseller') }}" onclick="closeMobileSidebarIfOpen()">
                        <i class="bi bi-shop-window"></i>
                        <span class="nav-link-title">Katalog & Sebaran Stok</span>
                    </a>

                    <!-- Reseller Dropdown: Penjualan & Stok Saya -->
                    @php $isResellerActive = request()->routeIs('transactions.*') || request()->routeIs('stocks.index'); @endphp
                    <div>
                        <a class="nav-side-link d-flex justify-content-between align-items-center {{ $isResellerActive ? 'active' : '' }} {{ $isResellerActive ? '' : 'collapsed' }}" 
                           data-bs-toggle="collapse" 
                           href="#menuReseller" 
                           role="button" 
                           aria-expanded="{{ $isResellerActive ? 'true' : 'false' }}">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-box-seam-fill"></i>
                                <span class="nav-link-title">Penjualan & Stok Saya</span>
                            </div>
                            <i class="bi bi-chevron-down nav-chevron"></i>
                        </a>
                        <div class="collapse {{ $isResellerActive ? 'show' : '' }} nav-side-sub" id="menuReseller">
                            <a class="nav-side-link {{ request()->routeIs('transactions.create') ? 'active' : '' }}" href="{{ route('transactions.create', ['mode' => 'stock']) }}" onclick="closeMobileSidebarIfOpen()">
                                <i class="bi bi-cart-plus"></i>
                                <span>Catat Penjualan</span>
                            </a>
                            <a class="nav-side-link {{ request()->routeIs('transactions.index') || request()->routeIs('transactions.edit') ? 'active' : '' }}" href="{{ route('transactions.index') }}" onclick="closeMobileSidebarIfOpen()">
                                <i class="bi bi-journal-text"></i>
                                <span>Riwayat Penjualan</span>
                            </a>
                            <a class="nav-side-link {{ request()->routeIs('stocks.index') ? 'active' : '' }}" href="{{ route('stocks.index') }}" onclick="closeMobileSidebarIfOpen()">
                                <i class="bi bi-boxes"></i>
                                <span>Stok Fisik Saya</span>
                            </a>
                        </div>
                    </div>

                    @else
                    <!-- 1. Dropdown Penjualan (Invoice) -->
                    @php $isInvoiceActive = request()->routeIs('invoices.*'); @endphp
                    <div>
                        <a class="nav-side-link d-flex justify-content-between align-items-center {{ $isInvoiceActive ? 'active' : '' }} {{ $isInvoiceActive ? '' : 'collapsed' }}" 
                           data-bs-toggle="collapse" 
                           href="#menuInvoice" 
                           role="button" 
                           aria-expanded="{{ $isInvoiceActive ? 'true' : 'false' }}">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-receipt-cutoff"></i>
                                <span class="nav-link-title">Penjualan (Invoice)</span>
                            </div>
                            <i class="bi bi-chevron-down nav-chevron"></i>
                        </a>
                        <div class="collapse {{ $isInvoiceActive ? 'show' : '' }} nav-side-sub" id="menuInvoice">
                            <a class="nav-side-link {{ request()->routeIs('invoices.create') ? 'active' : '' }}" href="{{ route('invoices.create') }}" onclick="closeMobileSidebarIfOpen()">
                                <i class="bi bi-plus-circle"></i>
                                <span>Buat Invoice Baru</span>
                            </a>
                            <a class="nav-side-link {{ request()->routeIs('invoices.index') ? 'active' : '' }}" href="{{ route('invoices.index') }}" onclick="closeMobileSidebarIfOpen()">
                                <i class="bi bi-file-earmark-text"></i>
                                <span>Daftar Invoice</span>
                            </a>
                        </div>
                    </div>

                    <!-- 2. Dropdown Stok Parfum & PO -->
                    @php $isStockActive = request()->routeIs('stocks.*') || request()->routeIs('pre-orders.*'); @endphp
                    <div>
                        <a class="nav-side-link d-flex justify-content-between align-items-center {{ $isStockActive ? 'active' : '' }} {{ $isStockActive ? '' : 'collapsed' }}" 
                           data-bs-toggle="collapse" 
                           href="#menuStok" 
                           role="button" 
                           aria-expanded="{{ $isStockActive ? 'true' : 'false' }}">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-box-seam-fill"></i>
                                <span class="nav-link-title">Stok Parfum & PO</span>
                            </div>
                            <i class="bi bi-chevron-down nav-chevron"></i>
                        </a>
                        <div class="collapse {{ $isStockActive ? 'show' : '' }} nav-side-sub" id="menuStok">
                            <a class="nav-side-link {{ request()->routeIs('stocks.reseller') ? 'active' : '' }}" href="{{ route('stocks.reseller') }}" onclick="closeMobileSidebarIfOpen()">
                                <i class="bi bi-shop-window"></i>
                                <span>Sebaran Stok Reseller</span>
                            </a>
                            <a class="nav-side-link {{ request()->routeIs('stocks.index') ? 'active' : '' }}" href="{{ route('stocks.index') }}" onclick="closeMobileSidebarIfOpen()">
                                <i class="bi bi-grid-3x3"></i>
                                <span>Ringkasan Matriks Stok</span>
                            </a>
                            <a class="nav-side-link {{ request()->routeIs('pre-orders.*') ? 'active' : '' }}" href="{{ route('pre-orders.index') }}" onclick="closeMobileSidebarIfOpen()">
                                <i class="bi bi-hourglass-split"></i>
                                <span>Stok Pending (PO)</span>
                            </a>
                            <a class="nav-side-link {{ request()->routeIs('stocks.transfers') || request()->routeIs('stocks.transfer.*') ? 'active' : '' }}" href="{{ route('stocks.transfers') }}" onclick="closeMobileSidebarIfOpen()">
                                <i class="bi bi-arrow-left-right"></i>
                                <span>Transfer Stok Antar Lokasi</span>
                            </a>
                            <a class="nav-side-link fw-semibold" style="color: var(--scent-dark);" href="{{ route('stocks.publicReseller') }}" target="_blank" title="Buka dashboard stok versi reseller tanpa login">
                                <i class="bi bi-box-arrow-up-right"></i>
                                <span>Buka Link Publik Reseller</span>
                            </a>
                        </div>
                    </div>

                    <!-- 3. Dropdown Barang Mentah -->
                    @php $isMaterialActive = request()->routeIs('raw-materials.*'); @endphp
                    <div>
                        <a class="nav-side-link d-flex justify-content-between align-items-center {{ $isMaterialActive ? 'active' : '' }} {{ $isMaterialActive ? '' : 'collapsed' }}" 
                           data-bs-toggle="collapse" 
                           href="#menuMaterial" 
                           role="button" 
                           aria-expanded="{{ $isMaterialActive ? 'true' : 'false' }}">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-boxes"></i>
                                <span class="nav-link-title">Barang Mentah</span>
                            </div>
                            <i class="bi bi-chevron-down nav-chevron"></i>
                        </a>
                        <div class="collapse {{ $isMaterialActive ? 'show' : '' }} nav-side-sub" id="menuMaterial">
                            <a class="nav-side-link {{ request()->routeIs('raw-materials.index') ? 'active' : '' }}" href="{{ route('raw-materials.index') }}" onclick="closeMobileSidebarIfOpen()">
                                <i class="bi bi-box"></i>
                                <span>Pengadaan Box & Botol</span>
                            </a>
                        </div>
                    </div>

                    <!-- 4. Dropdown Keuangan (Buku Kas) -->
                    @php $isKasActive = request()->routeIs('transactions.*'); @endphp
                    <div>
                        <a class="nav-side-link d-flex justify-content-between align-items-center {{ $isKasActive ? 'active' : '' }} {{ $isKasActive ? '' : 'collapsed' }}" 
                           data-bs-toggle="collapse" 
                           href="#menuKas" 
                           role="button" 
                           aria-expanded="{{ $isKasActive ? 'true' : 'false' }}">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-wallet2"></i>
                                <span class="nav-link-title">Keuangan (Buku Kas)</span>
                            </div>
                            <i class="bi bi-chevron-down nav-chevron"></i>
                        </a>
                        <div class="collapse {{ $isKasActive ? 'show' : '' }} nav-side-sub" id="menuKas">
                            <a class="nav-side-link {{ request()->routeIs('transactions.create') ? 'active' : '' }}" href="{{ route('transactions.create') }}" onclick="closeMobileSidebarIfOpen()">
                                <i class="bi bi-plus-circle"></i>
                                <span>Catat Transaksi Kas</span>
                            </a>
                            <a class="nav-side-link {{ request()->routeIs('transactions.index') || request()->routeIs('transactions.edit') ? 'active' : '' }}" href="{{ route('transactions.index') }}" onclick="closeMobileSidebarIfOpen()">
                                <i class="bi bi-journal-text"></i>
                                <span>Riwayat Kas Masuk & Keluar</span>
                            </a>
                        </div>
                    </div>
                    @endif

                    @if(Auth::user()->isAdmin())
                    <!-- 5. Dropdown Master Data (Admin Only) -->
                    @php $isMasterActive = request()->routeIs('products.*') || request()->routeIs('locations.*') || request()->routeIs('users.*'); @endphp
                    <div>
                        <a class="nav-side-link d-flex justify-content-between align-items-center {{ $isMasterActive ? 'active' : '' }} {{ $isMasterActive ? '' : 'collapsed' }}" 
                           data-bs-toggle="collapse" 
                           href="#menuMaster" 
                           role="button" 
                           aria-expanded="{{ $isMasterActive ? 'true' : 'false' }}">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-gear-wide-connected"></i>
                                <span class="nav-link-title">Master Data</span>
                            </div>
                            <i class="bi bi-chevron-down nav-chevron"></i>
                        </a>
                        <div class="collapse {{ $isMasterActive ? 'show' : '' }} nav-side-sub" id="menuMaster">
                            <a class="nav-side-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}" onclick="closeMobileSidebarIfOpen()">
                                <i class="bi bi-flower1"></i>
                                <span>Produk Parfum</span>
                            </a>
                            <a class="nav-side-link {{ request()->routeIs('locations.*') ? 'active' : '' }}" href="{{ route('locations.index') }}" onclick="closeMobileSidebarIfOpen()">
                                <i class="bi bi-geo-alt-fill"></i>
                                <span>Lokasi Stok</span>
                            </a>
                            <a class="nav-side-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}" onclick="closeMobileSidebarIfOpen()">
                                <i class="bi bi-people-fill"></i>
                                <span>Manajemen User</span>
                            </a>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Sidebar Footer: User Profile & Logout -->
                <div class="sidebar-footer sidebar-profile-box mt-auto pt-2">
                    <div class="sidebar-footer-row d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2 overflow-hidden" style="min-width:0;">
                            <span class="user-avatar-circle bg-dark text-white flex-shrink-0">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            <div class="user-info-text text-truncate" style="min-width:0;">
                                <div class="fw-bold text-dark font-serif text-truncate" style="font-size:0.85rem;">{{ Auth::user()->name }}</div>
                                <div class="text-muted text-truncate" style="font-size:0.68rem;">{{ ucfirst(Auth::user()->role) }}</div>
                            </div>
                        </div>
                        <form action="{{ route('logout') }}" method="POST" class="m-0 flex-shrink-0" onsubmit="return confirm('Keluar dari sesi Scentpeep?');">
                            @csrf
                            <button type="submit" class="btn btn-sm text-danger border-0 p-1" title="Keluar" aria-label="Keluar">
                                <i class="bi bi-box-arrow-right fs-5"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>
        @endauth

        <!-- Page Wrapper -->
        <div class="page-wrapper">
            @auth
            <!-- Desktop Topbar (>= 992px) -->
            <header class="page-topbar d-none d-lg-flex align-items-center justify-content-between bg-white border-hairline-b sticky-top py-2 px-4">
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-sm border-0 text-muted p-1" type="button" onclick="toggleDesktopSidebar()" title="Perkecil / Perluas Sidebar">
                        <i class="bi bi-layout-sidebar-inset fs-4"></i>
                    </button>
                    <h4 class="mb-0 fw-bold font-serif" style="color: var(--scent-dark);">@yield('page-header', 'Scentpeep Management System')</h4>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-light text-muted border border-hairline px-3 py-2 d-none d-xl-inline-flex" style="font-size:0.78rem;">
                        <i class="bi bi-calendar3 me-1"></i> {{ date('d M Y') }}
                    </span>
                    <span class="badge px-3 py-2 fw-semibold" style="background-color: var(--scent-amber-subtle); color: var(--scent-amber); font-size:0.78rem;">
                        <i class="bi bi-shield-check me-1"></i> {{ strtoupper(Auth::user()->role) }}
                    </span>
                </div>
            </header>
            @endauth

            <!-- Page Body -->
            <div class="flex-grow-1 px-3 px-lg-4 py-3">
                <div class="container-fluid p-0">
                    @yield('content')
                </div>
            </div>

            <!-- Footer -->
            <footer class="border-top py-3 px-4 bg-white mt-auto text-muted small" style="border-color: var(--color-border) !important;">
                <div class="container-fluid p-0 d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
                    <div>&copy; {{ date('Y') }} <strong class="font-serif">Scentpeep</strong>, Sistem Rekap Stok & Ledger Transaksi UMKM Parfum</div>
                    <div>Bootstrap 5.3 + SweetAlert2 (CDN)</div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Bootstrap 5.3.3 JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- SweetAlert2 CDN (v11) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Desktop Collapse / Mini Sidebar Toggle (>= 992px)
        function toggleDesktopSidebar() {
            document.body.classList.toggle('sidebar-collapsed');
            const isCollapsed = document.body.classList.contains('sidebar-collapsed');
            try { localStorage.setItem('scentpeep_sidebar_collapsed', isCollapsed ? '1' : '0'); } catch(e) {}
        }

        // Restore sidebar collapsed state on desktop load
        document.addEventListener('DOMContentLoaded', function() {
            try {
                if (window.innerWidth >= 992 && localStorage.getItem('scentpeep_sidebar_collapsed') === '1') {
                    document.body.classList.add('sidebar-collapsed');
                }
            } catch(e) {}
        });

        // Mobile Offcanvas Drawer Sidebar (< 992px)
        function toggleMobileSidebar(forceState = null) {
            if (forceState !== null) {
                document.body.classList.toggle('sidebar-mobile-open', forceState);
            } else {
                document.body.classList.toggle('sidebar-mobile-open');
            }
        }

        function closeMobileSidebarIfOpen() {
            if (window.innerWidth < 992 && document.body.classList.contains('sidebar-mobile-open')) {
                document.body.classList.remove('sidebar-mobile-open');
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeMobileSidebarIfOpen();
        });

        // SweetAlert2 Toast Notifications
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        @if(session('success'))
            Toast.fire({ icon: 'success', title: "{{ session('success') }}" });
        @endif
        @if(session('warning'))
            Toast.fire({ icon: 'warning', title: "{{ session('warning') }}" });
        @endif
        @if(session('danger') || session('error'))
            Toast.fire({ icon: 'error', title: "{{ session('danger') ?? session('error') }}" });
        @endif
        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Terdapat Kesalahan Input',
                html: '<ul class="text-start mb-0 ps-3">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>',
                confirmButtonColor: '#121212'
            });
        @endif

        // Confirm destructive actions (SweetAlert2)
        function confirmAction(formElement, titleText, messageText, confirmBtnText = 'Ya, Lanjutkan') {
            Swal.fire({
                title: titleText || 'Apakah Anda yakin?',
                text: messageText || 'Tindakan ini tidak dapat dibatalkan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d63939',
                cancelButtonColor: '#6c757d',
                confirmButtonText: confirmBtnText,
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) formElement.submit();
            });
            return false;
        }

        // Realtime Client-side Search Helper
        function setupLiveTableSearch(inputElementId, tableBodyId, counterElementId = null) {
            const searchInput = document.getElementById(inputElementId);
            const tableBody = document.getElementById(tableBodyId);
            if (!searchInput || !tableBody) return;

            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                const rows = tableBody.querySelectorAll('tr:not(.no-search-match)');
                let visibleCount = 0;

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (query === '' || text.includes(query)) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                if (counterElementId) {
                    const counter = document.getElementById(counterElementId);
                    if (counter) counter.textContent = visibleCount;
                }
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
