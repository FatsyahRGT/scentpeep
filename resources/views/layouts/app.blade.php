<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Dashboard') - Scentpeep Management System</title>
    
    <!-- Tabler Core CSS CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css">
    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

    <style>
        :root {
            --tblr-font-sans-serif: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            --sp-navy: #0d1727;
            --sp-accent: #206bc4;
        }

        body {
            font-family: var(--tblr-font-sans-serif);
            background-color: #f8fafc;
            color: #1e293b;
        }

        /* Sidebar Styling */
        .navbar-vertical.navbar-dark {
            background-color: var(--sp-navy) !important;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            z-index: 1050;
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), width 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .navbar-brand-logo {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #206bc4, #4299e1);
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1.2rem;
            margin-right: 0.5rem;
            flex-shrink: 0;
        }

        .navbar-vertical .nav-link {
            font-weight: 500;
            padding: 0.65rem 0.9rem;
            border-radius: 8px;
            margin-bottom: 3px;
            color: #94a3b8;
            transition: all 0.15s ease;
        }

        .navbar-vertical .nav-link:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.08);
        }

        .navbar-vertical .nav-link.active {
            color: #ffffff;
            background-color: #206bc4;
            font-weight: 600;
        }

        .navbar-vertical .nav-link-icon {
            font-size: 1.15rem;
            margin-right: 0.65rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* Base Reset */
        html, body {
            overflow-x: hidden;
            max-width: 100vw;
        }

        /* Mobile Drawer rules (< 992px) */
        @media (max-width: 991.98px) {
            /* STATE TERTUTUP: seluruh sidebar disembunyikan total (tanpa sisa potongan) */
            .navbar-vertical {
                position: fixed !important;
                top: 0;
                bottom: 0;
                left: 0;
                width: 280px !important;
                max-width: 85vw;
                height: 100vh !important;
                height: 100dvh !important;
                transform: translateX(-110%) !important;
                visibility: hidden !important;
                display: none !important;
                pointer-events: none !important;
                border-right: 0 !important;
                box-shadow: none !important;
                z-index: 1050 !important;
                overflow: hidden !important;
                transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .navbar-vertical > .container-fluid {
                display: flex !important;
                flex-direction: column !important;
                height: 100% !important;
                overflow: hidden !important;
            }
            .navbar-vertical .flex-grow-1 {
                overflow-y: auto !important;
                -webkit-overflow-scrolling: touch;
                min-height: 0;
            }

            /* STATE TERBUKA */
            body.sidebar-mobile-open .navbar-vertical {
                display: flex !important;
                transform: translateX(0) !important;
                visibility: visible !important;
                pointer-events: auto !important;
                border-right: 1px solid rgba(255, 255, 255, 0.08) !important;
                box-shadow: 4px 0 25px rgba(0, 0, 0, 0.5) !important;
            }
            .page-wrapper {
                margin-left: 0 !important;
                min-height: 100vh;
            }
            body.sidebar-mobile-open {
                overflow: hidden;
            }
        }

        /* Desktop rules (>= 992px) */
        @media (min-width: 992px) {
            .navbar-vertical {
                position: fixed !important;
                top: 0;
                bottom: 0;
                left: 0;
                width: 250px;
                height: 100vh;
                transform: none !important;
                overflow-y: auto;
                transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .page-wrapper {
                margin-left: 250px;
                transition: margin-left 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            }

            /* FLEXIBLE COLLAPSED / MINI SIDEBAR ON DESKTOP */
            body.sidebar-collapsed .navbar-vertical {
                width: 76px !important;
                overflow-x: hidden;
            }
            body.sidebar-collapsed .page-wrapper {
                margin-left: 76px !important;
            }

            /* Hide text elements in collapsed mode */
            body.sidebar-collapsed .navbar-vertical .sidebar-brand-text,
            body.sidebar-collapsed .navbar-vertical .nav-link-title,
            body.sidebar-collapsed .navbar-vertical .hr-text,
            body.sidebar-collapsed .navbar-vertical .user-info-text {
                display: none !important;
            }

            body.sidebar-collapsed .navbar-vertical .navbar-brand-logo {
                margin-right: 0 !important;
            }

            body.sidebar-collapsed .navbar-vertical .sidebar-header-row {
                justify-content: center !important;
            }

            body.sidebar-collapsed .navbar-vertical .nav-link {
                justify-content: center !important;
                padding: 0.75rem 0 !important;
                text-align: center;
            }

            body.sidebar-collapsed .navbar-vertical .nav-link-icon {
                margin-right: 0 !important;
                font-size: 1.3rem;
            }

            body.sidebar-collapsed .navbar-vertical .sidebar-footer {
                padding: 0.75rem 0.25rem !important;
            }

            body.sidebar-collapsed .navbar-vertical .sidebar-footer-row {
                flex-direction: column !important;
                justify-content: center !important;
                align-items: center !important;
                gap: 0.65rem !important;
            }

            body.sidebar-collapsed .navbar-vertical .sidebar-footer-row .avatar {
                margin: 0 auto;
            }
        }

        /* Sidebar Backdrop Overlay on Mobile (Clean without blur) */
        .sidebar-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(13, 23, 39, 0.65);
            z-index: 1040;
            opacity: 0;
            transition: opacity 0.25s ease;
        }
        @media (max-width: 991.98px) {
            body.sidebar-mobile-open .sidebar-backdrop {
                display: block;
                opacity: 1;
            }
        }

        /* Stock Pill Indicators */
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

        .stock-safe {
            background-color: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }

        .stock-warning {
            background-color: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .stock-danger {
            background-color: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        /* Clean Form Controls & Dropdown Selects */
        .form-select, .form-control {
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            padding: 0.55rem 0.85rem;
            font-size: 0.9rem;
            background-color: #ffffff;
            color: #1e293b;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .form-select:focus, .form-control:focus {
            border-color: #206bc4;
            box-shadow: 0 0 0 0.2rem rgba(32, 107, 196, 0.15);
            background-color: #ffffff;
        }

        .form-select option {
            padding: 8px 12px;
            background-color: #ffffff;
            color: #1e293b;
        }

        .form-select optgroup {
            font-weight: 700;
            color: #64748b;
            background-color: #f8fafc;
            padding: 4px 8px;
        }

        /* Consistent table rows */
        .table > :not(caption) > * > * {
            padding: 0.75rem 0.85rem;
        }

        /* Card Enhancements */
        .card {
            border-radius: 12px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .card-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        /* Button Improvements */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.15s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-group-sm .btn {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }

        /* Badge Improvements */
        .badge {
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        /* Responsive Chart Container */
        @media (max-width: 768px) {
            .card-body canvas {
                max-height: 250px !important;
            }
        }

        /* KPI Card Icons */
        .avatar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .avatar.rounded-3 {
            border-radius: 10px !important;
        }

        /* Form Focus States */
        .form-control:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--sp-accent);
        }

        /* Table Hover Enhancement */
        .table-hover tbody tr {
            transition: background-color 0.15s ease;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(32, 107, 196, 0.03);
        }

        /* Smooth Transitions for Interactive Elements */
        a, button, .card, .badge, .btn {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Fix for Long Text in Tables */
        .text-truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Chart Loading State */
        .chart-loading {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 280px;
            color: #94a3b8;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="page">
        @auth
        <!-- Mobile Top Navbar Header (Visible only on < 992px) -->
        <header class="navbar navbar-dark d-lg-none sticky-top py-2 px-3" style="background-color: var(--sp-navy); border-bottom: 1px solid rgba(255,255,255,0.1); z-index: 1040;">
            <div class="container-fluid p-0 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-icon btn-ghost-light text-white" type="button" onclick="toggleMobileSidebar()" aria-label="Toggle Sidebar Navigation">
                        <i class="bi bi-list fs-2"></i>
                    </button>
                    <a href="{{ route('dashboard') }}" class="d-flex align-items-center text-decoration-none text-white">
                        <span class="navbar-brand-logo" style="width: 30px; height: 30px; font-size: 1rem;"><i class="bi bi-droplet-fill"></i></span>
                        <span class="fw-bold fs-3 text-white">Scentpeep</span>
                    </a>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="avatar avatar-xs bg-blue-lt fw-bold rounded-circle">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </span>
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-icon btn-sm btn-ghost-danger text-danger-lt" title="Keluar">
                            <i class="bi bi-box-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Sidebar Overlay Backdrop for Mobile -->
        <div class="sidebar-backdrop" onclick="toggleMobileSidebar(false)"></div>

        <!-- Sidebar Navigation (Tabler navbar-vertical) -->
        <aside id="main-sidebar" class="navbar navbar-vertical navbar-expand-lg navbar-dark">
            <div class="container-fluid d-flex flex-column h-100 p-3">
                <!-- Brand & Close Button on Mobile -->
                <div class="sidebar-header-row d-flex align-items-center justify-content-between w-100 mb-3">
                    <a href="{{ route('dashboard') }}" class="d-flex align-items-center text-decoration-none text-white overflow-hidden">
                        <span class="navbar-brand-logo"><i class="bi bi-droplet-fill"></i></span>
                        <div class="sidebar-brand-text">
                            <span class="fw-bold fs-3 tracking-tight">Scentpeep</span>
                            <div class="small text-muted" style="font-size: 0.68rem; letter-spacing: 0.5px;">STOCK MANAGEMENT</div>
                        </div>
                    </a>
                    <button type="button" class="btn btn-icon btn-sm btn-ghost-light d-lg-none text-white" onclick="toggleMobileSidebar(false)" aria-label="Close sidebar">
                        <i class="bi bi-x-lg fs-4"></i>
                    </button>
                </div>

                <!-- Sidebar Menu Nav Links -->
                <div class="w-100 flex-grow-1 overflow-y-auto">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}" onclick="closeMobileSidebarIfOpen()" title="Dashboard">
                                <span class="nav-link-icon"><i class="bi bi-grid-1x2-fill"></i></span>
                                <span class="nav-link-title">Dashboard</span>
                            </a>
                        </li>

                        @if(Auth::user()->isReseller())
                        <li class="nav-item">
                            <div class="hr-text text-uppercase text-muted my-2" style="font-size: 0.65rem;">PENJUALAN & STOK</div>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('transactions.create') ? 'active' : '' }}" href="{{ route('transactions.create', ['mode' => 'stock']) }}" onclick="closeMobileSidebarIfOpen()" title="Catat Penjualan">
                                <span class="nav-link-icon text-teal"><i class="bi bi-cart-plus-fill"></i></span>
                                <span class="nav-link-title">Catat Penjualan</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('transactions.index') || request()->routeIs('transactions.edit') ? 'active' : '' }}" href="{{ route('transactions.index') }}" onclick="closeMobileSidebarIfOpen()" title="Riwayat Penjualan">
                                <span class="nav-link-icon"><i class="bi bi-journal-text"></i></span>
                                <span class="nav-link-title">Riwayat Penjualan</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('stocks.index') ? 'active' : '' }}" href="{{ route('stocks.index') }}" onclick="closeMobileSidebarIfOpen()" title="Stok Saya">
                                <span class="nav-link-icon text-cyan"><i class="bi bi-box-seam-fill"></i></span>
                                <span class="nav-link-title">Stok Saya</span>
                            </a>
                        </li>
                        @else
                        <li class="nav-item">
                            <div class="hr-text text-uppercase text-muted my-2" style="font-size: 0.65rem;">KAS & TRANSAKSI</div>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('transactions.create') && request('mode') !== 'stock' ? 'active' : '' }}" href="{{ route('transactions.create') }}" onclick="closeMobileSidebarIfOpen()" title="Catat Transaksi Kas">
                                <span class="nav-link-icon text-warning"><i class="bi bi-wallet2"></i></span>
                                <span class="nav-link-title">Catat Transaksi Kas</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('transactions.create') && request('mode') === 'stock' ? 'active' : '' }}" href="{{ route('transactions.create', ['mode' => 'stock']) }}" onclick="closeMobileSidebarIfOpen()" title="Catat Mutasi Stok">
                                <span class="nav-link-icon text-teal"><i class="bi bi-box-arrow-in-down-right"></i></span>
                                <span class="nav-link-title">Catat Mutasi Stok</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('transactions.index') || request()->routeIs('transactions.edit') ? 'active' : '' }}" href="{{ route('transactions.index') }}" onclick="closeMobileSidebarIfOpen()" title="Riwayat Transaksi">
                                <span class="nav-link-icon"><i class="bi bi-journal-text"></i></span>
                                <span class="nav-link-title">Riwayat Transaksi</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <div class="hr-text text-uppercase text-muted my-2" style="font-size: 0.65rem;">STOK PARFUM</div>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('stocks.index') ? 'active' : '' }}" href="{{ route('stocks.index') }}" onclick="closeMobileSidebarIfOpen()" title="Ringkasan Stok">
                                <span class="nav-link-icon"><i class="bi bi-box-seam-fill"></i></span>
                                <span class="nav-link-title">Ringkasan Stok</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('stocks.transfer.*') ? 'active' : '' }}" href="{{ route('stocks.transfer.create') }}" onclick="closeMobileSidebarIfOpen()" title="Transfer Stok">
                                <span class="nav-link-icon text-info"><i class="bi bi-arrow-left-right"></i></span>
                                <span class="nav-link-title">Transfer Stok</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('stocks.transfers') ? 'active' : '' }}" href="{{ route('stocks.transfers') }}" onclick="closeMobileSidebarIfOpen()" title="Riwayat Transfer">
                                <span class="nav-link-icon text-muted"><i class="bi bi-clock-history"></i></span>
                                <span class="nav-link-title">Riwayat Transfer</span>
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->isAdmin())
                        <li class="nav-item">
                            <div class="hr-text text-uppercase text-muted my-2" style="font-size: 0.65rem;">MASTER DATA</div>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}" onclick="closeMobileSidebarIfOpen()" title="Produk Parfum">
                                <span class="nav-link-icon"><i class="bi bi-flower1"></i></span>
                                <span class="nav-link-title">Produk Parfum</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('locations.*') ? 'active' : '' }}" href="{{ route('locations.index') }}" onclick="closeMobileSidebarIfOpen()" title="Lokasi Stok">
                                <span class="nav-link-icon"><i class="bi bi-geo-alt-fill"></i></span>
                                <span class="nav-link-title">Lokasi Stok</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}" onclick="closeMobileSidebarIfOpen()" title="Manajemen User">
                                <span class="nav-link-icon"><i class="bi bi-people-fill"></i></span>
                                <span class="nav-link-title">Manajemen User</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>

                <!-- User Footer in Sidebar -->
                <div class="sidebar-footer w-100 pt-3 pb-2 mt-auto border-top border-dark-subtle flex-shrink-0">
                    <div class="sidebar-footer-row d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2 overflow-hidden" style="min-width: 0;">
                            <span class="avatar avatar-sm bg-blue-lt fw-bold rounded-circle flex-shrink-0">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            <div class="user-info-text text-truncate" style="min-width: 0;">
                                <div class="text-white small fw-bold text-truncate">{{ Auth::user()->name }}</div>
                                <div class="text-muted small" style="font-size: 0.7rem;">{{ ucfirst(Auth::user()->role) }}</div>
                            </div>
                        </div>
                        <form action="{{ route('logout') }}" method="POST" class="m-0 flex-shrink-0">
                            @csrf
                            <button type="submit" class="btn btn-icon btn-sm btn-ghost-danger" title="Keluar">
                                <i class="bi bi-box-arrow-right"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>
        @endauth

        <!-- Page Wrapper Area -->
        <div class="page-wrapper">
            @auth
            <!-- Topbar Header (Desktop) -->
            <header class="navbar navbar-expand-md navbar-light d-none d-lg-flex d-print-none sticky-top bg-white border-bottom px-4 py-2">
                <div class="container-fluid p-0">
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-icon btn-ghost-secondary border-0 text-secondary" type="button" onclick="toggleDesktopSidebar()" title="Perkecil / Perluas Sidebar">
                            <i class="bi bi-layout-sidebar-inset fs-3"></i>
                        </button>
                        <h4 class="mb-0 fw-bold text-dark">@yield('page-header', 'Scentpeep Management System')</h4>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-light text-secondary border px-3 py-2 d-none d-xl-inline-flex">
                            <i class="bi bi-calendar3 me-1"></i> {{ date('d M Y') }}
                        </span>
                        <span class="badge bg-blue-lt px-3 py-2 fw-semibold">
                            <i class="bi bi-shield-check me-1"></i> {{ strtoupper(Auth::user()->role) }}
                        </span>
                    </div>
                </div>
            </header>
            @endauth

            <!-- Page Body Content -->
            <div class="page-body px-3 px-lg-4 py-3">
                <div class="container-fluid p-0">
                    @yield('content')
                </div>
            </div>

            <!-- Minimal Footer -->
            <footer class="footer footer-transparent d-print-none border-top py-3 px-4 bg-white mt-auto text-muted small">
                <div class="container-fluid p-0 d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
                    <div>&copy; {{ date('Y') }} <strong>Scentpeep</strong>, Sistem Rekap Stok & Ledger Transaksi UMKM Parfum</div>
                    <div>Tabler + Bootstrap 5 + SweetAlert2 (CDN)</div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Tabler Core JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/js/tabler.min.js"></script>
    <!-- SweetAlert2 CDN (v11) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Desktop Collapse / Mini Sidebar Toggle (>= 992px)
        function toggleDesktopSidebar() {
            document.body.classList.toggle('sidebar-collapsed');
            const isCollapsed = document.body.classList.contains('sidebar-collapsed');
            try {
                localStorage.setItem('scentpeep_sidebar_collapsed', isCollapsed ? '1' : '0');
            } catch(e) {}
        }

        // Restore sidebar collapsed state on desktop load
        document.addEventListener('DOMContentLoaded', function() {
            try {
                if (window.innerWidth >= 992 && localStorage.getItem('scentpeep_sidebar_collapsed') === '1') {
                    document.body.classList.add('sidebar-collapsed');
                }
            } catch(e) {}
        });

        // Mobile Offcanvas Drawer Sidebar Helper (< 992px)
        function toggleMobileSidebar(forceState = null) {
            if (forceState !== null) {
                if (forceState) {
                    document.body.classList.add('sidebar-mobile-open');
                } else {
                    document.body.classList.remove('sidebar-mobile-open');
                }
            } else {
                document.body.classList.toggle('sidebar-mobile-open');
            }
        }

        function closeMobileSidebarIfOpen() {
            if (window.innerWidth < 992 && document.body.classList.contains('sidebar-mobile-open')) {
                document.body.classList.remove('sidebar-mobile-open');
            }
        }

        // Close mobile sidebar on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMobileSidebarIfOpen();
            }
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
            Toast.fire({
                icon: 'success',
                title: "{{ session('success') }}"
            });
        @endif

        @if(session('warning'))
            Toast.fire({
                icon: 'warning',
                title: "{{ session('warning') }}"
            });
        @endif

        @if(session('danger') || session('error'))
            Toast.fire({
                icon: 'error',
                title: "{{ session('danger') ?? session('error') }}"
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Terdapat Kesalahan Input',
                html: '<ul class="text-start mb-0 ps-3">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>',
                confirmButtonColor: '#206bc4'
            });
        @endif

        // Global SweetAlert2 Confirmation for Destructive Actions
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
                if (result.isConfirmed) {
                    formElement.submit();
                }
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