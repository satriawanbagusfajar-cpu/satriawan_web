<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'PKL SMK Fatahillah' }}</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo-yaspat.jpg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-yaspat.jpg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #FF8C42;
            --primary-dark: #FF6B35;
            --primary-light: #FFB347;
            --primary-gradient: linear-gradient(135deg, #FF8C42 0%, #FF6B35 50%, #FF5722 100%);
            --success-gradient: linear-gradient(135deg, #10b981 0%, #34d399 100%);
            --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
            --info-gradient: linear-gradient(135deg, #06b6d4 0%, #22d3ee 100%);
            --danger-gradient: linear-gradient(135deg, #ef4444 0%, #f87171 100%);
            --surface: #ffffff;
            --surface-hover: #f8fafc;
            --bg: #f1f5f9;
            --text: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --shadow-sm: 0 1px 3px rgba(0,0,0,.04), 0 1px 2px rgba(0,0,0,.06);
            --shadow: 0 4px 6px -1px rgba(0,0,0,.07), 0 2px 4px -2px rgba(0,0,0,.05);
            --shadow-lg: 0 10px 25px -3px rgba(0,0,0,.08), 0 4px 6px -4px rgba(0,0,0,.04);
            --shadow-xl: 0 20px 40px -4px rgba(0,0,0,.1);
            --radius: 16px;
            --radius-sm: 10px;
            --radius-xs: 8px;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            min-height: 100vh;
            color: var(--text);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            display: flex;
            flex-direction: column;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        /* ═══════ NAVBAR ═══════ */
        .navbar-modern {
            background: linear-gradient(135deg, #FF8C42 0%, #FF6B35 50%, #FF5722 100%);
            padding: 0.6rem 0;
            border: none;
            box-shadow: 0 4px 20px rgba(255,107,53,.3);
        }
        .navbar-modern .navbar-brand {
            font-weight: 800;
            font-size: 1.2rem;
            color: #fff !important;
            letter-spacing: -0.3px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .navbar-modern .navbar-brand .brand-icon {
            width: 38px;
            height: 38px;
            background: rgba(255,255,255,.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            backdrop-filter: blur(10px);
            padding: 4px;
            overflow: hidden;
        }
        .navbar-modern .navbar-brand .brand-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .navbar-modern .nav-link {
            color: rgba(255,255,255,.8) !important;
            font-weight: 500;
            font-size: .875rem;
            border-radius: var(--radius-xs);
            padding: 8px 14px !important;
            transition: all .25s cubic-bezier(.4,0,.2,1);
            position: relative;
        }
        .navbar-modern .nav-link:hover {
            color: #fff !important;
            background: rgba(255,255,255,.15);
            transform: translateY(-1px);
        }
        .navbar-modern .nav-link.active {
            color: #fff !important;
            background: rgba(255,255,255,.2);
            box-shadow: 0 2px 8px rgba(0,0,0,.15);
        }
        .navbar-modern .nav-link i { font-size: .95rem; }
        .btn-logout {
            background: rgba(255,255,255,.15);
            color: #fff;
            border: 1px solid rgba(255,255,255,.25);
            border-radius: 50px;
            padding: 6px 16px;
            font-size: .8rem;
            font-weight: 600;
            transition: all .25s;
            backdrop-filter: blur(10px);
        }
        .btn-logout:hover {
            background: rgba(255,255,255,.25);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,.2);
        }
        .user-badge {
            background: rgba(255,255,255,.1);
            border-radius: 50px;
            padding: 4px 14px 4px 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .user-badge .avatar {
            width: 28px;
            height: 28px;
            background: rgba(255,255,255,.25);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .75rem;
            color: #fff;
        }
        .user-badge span { color: rgba(255,255,255,.9); font-size: .8rem; font-weight: 500; }

        /* ═══════ LAYOUT ═══════ */
        main {
            flex: 1;
        }
        main.container { max-width: 1200px; }

        /* ═══════ PAGE HEADER ═══════ */
        .page-header {
            margin-bottom: 1.75rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e2e8f0;
        }
        .page-header h3 {
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--text);
            letter-spacing: -0.5px;
        }
        .page-header h3 i { color: var(--primary); }
        .page-header p { color: var(--text-muted); font-size: .9rem; }

        /* ═══════ CARDS ═══════ */
        .card-modern {
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            background: var(--surface);
            transition: box-shadow .3s cubic-bezier(.4,0,.2,1), transform .3s cubic-bezier(.4,0,.2,1);
        }
        .card-modern:hover { box-shadow: var(--shadow); }

        /* ═══════ STAT CARDS ═══════ */
        .stat-card {
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            transition: all .3s cubic-bezier(.4,0,.2,1);
            background: var(--surface);
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: transparent;
        }
        .stat-card .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }
        .stat-card .fs-4 { font-size: 1.6rem !important; letter-spacing: -0.5px; }

        /* ═══════ TABLES ═══════ */
        .table-modern { margin-bottom: 0; }
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .table-modern thead {
            background: linear-gradient(135deg, #FF8C42 0%, #FF6B35 100%);
            color: #fff;
        }
        .table-modern thead th {
            border: none;
            font-weight: 600;
            padding: 14px 16px;
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .8px;
            white-space: nowrap;
        }
        .table-modern tbody td {
            padding: 13px 16px;
            vertical-align: middle;
            border-color: var(--border);
            color: var(--text);
            font-size: .9rem;
        }
        .table-modern tbody tr { transition: background .15s; }
        .table-modern tbody tr:hover { background: #f8fafc; }
        .table-modern tbody tr:last-child td { border-bottom: none; }

        .table-pagination {
            padding-top: 1rem;
            border-top: 1px solid var(--border);
        }

        .table-pagination .pagination {
            margin-bottom: 0;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* ═══════ BADGES ═══════ */
        .badge-status {
            font-size: .75rem;
            padding: 5px 14px;
            border-radius: 50px;
            font-weight: 600;
            letter-spacing: .3px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .badge-success { background: #ecfdf5; color: #059669; }
        .badge-warning { background: #fffbeb; color: #d97706; }
        .badge-info { background: #ecfeff; color: #0891b2; }
        .badge-danger { background: #fef2f2; color: #dc2626; }
        .badge-secondary { background: #f1f5f9; color: #64748b; }

        /* ═══════ BUTTONS ═══════ */
        .btn-gradient {
            background: var(--primary-gradient);
            color: #fff;
            border: none;
            border-radius: var(--radius-sm);
            font-weight: 600;
            padding: 10px 22px;
            font-size: .875rem;
            transition: all .25s cubic-bezier(.4,0,.2,1);
            box-shadow: 0 2px 8px rgba(255,107,53,.25);
        }
        .btn-gradient:hover {
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255,107,53,.35);
        }
        .btn-gradient:active { transform: translateY(0); }
        .btn-outline-primary {
            border-color: var(--primary);
            color: var(--primary);
            border-radius: var(--radius-sm);
            font-weight: 500;
        }
        .btn-outline-primary:hover {
            background: var(--primary);
            border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(255,107,53,.25);
        }

        /* ═══════ FORMS ═══════ */
        .form-control, .form-select {
            border-radius: var(--radius-sm);
            border: 1.5px solid var(--border);
            padding: 10px 14px;
            font-size: .9rem;
            transition: all .25s cubic-bezier(.4,0,.2,1);
            background: var(--surface);
            color: var(--text);
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99,102,241,.1);
        }
        .form-control::placeholder { color: #94a3b8; }
        .form-label { font-weight: 600; font-size: .85rem; color: var(--text); margin-bottom: 6px; }

        /* ═══════ ALERTS ═══════ */
        .alert-modern {
            border: none;
            border-radius: var(--radius);
            padding: 14px 20px;
            font-size: .9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown .4s cubic-bezier(.4,0,.2,1);
        }
        .alert-modern.alert-success {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            color: #065f46;
            border-left: 4px solid #10b981;
        }
        .alert-modern.alert-danger {
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }

        /* ═══════ PAGINATION ═══════ */
        .pagination { gap: 4px; }
        .pagination .page-link {
            border-radius: var(--radius-xs);
            border: 1px solid var(--border);
            color: var(--primary);
            font-weight: 500;
            font-size: .85rem;
            padding: 8px 14px;
            transition: all .2s;
        }
        .pagination .page-link:hover {
            background: #eef2ff;
            border-color: var(--primary-light);
        }
        .pagination .page-item.active .page-link {
            background: var(--primary-gradient);
            border-color: transparent;
            box-shadow: 0 2px 8px rgba(99,102,241,.3);
        }

        .pagination-clean-wrap {
            background: #fff;
            border: 1px solid #e8edf3;
            border-radius: 10px;
            box-shadow: 0 4px 14px rgba(15, 23, 42, .06);
            padding: 10px 14px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
        }

        .pagination-clean {
            gap: 8px;
            margin: 0;
            align-items: center;
        }

        .pagination-clean .page-item .page-link {
            border: none;
            background: transparent;
            color: #9aa3af;
            font-weight: 700;
            min-width: 34px;
            height: 34px;
            border-radius: 7px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 10px;
            line-height: 1;
            box-shadow: none;
        }

        .pagination-clean .page-item .page-link:hover {
            background: #f3f6fa;
            color: #4b5563;
        }

        .pagination-clean .page-item.active .page-link,
        .pagination-clean .page-item.active .page-link:hover {
            background: #2f8ef3;
            color: #fff;
            box-shadow: 0 6px 14px rgba(47, 142, 243, .35);
        }

        .pagination-clean .page-item.disabled .page-link {
            color: #c2c8d0;
            background: transparent;
            opacity: 1;
        }

        .pagination-clean .page-item.icon .page-link {
            background: #f1f3f6;
            color: #9aa3af;
            min-width: 34px;
            padding: 0;
        }

        .pagination-clean .page-item.icon .page-link:hover {
            background: #e8edf3;
            color: #6b7280;
        }

        .pagination-clean .page-item.ellipsis .page-link {
            cursor: default;
            pointer-events: none;
        }

        /* ═══════ MODALS ═══════ */
        .modal-content {
            border: none;
            border-radius: var(--radius);
            box-shadow: var(--shadow-xl);
        }
        .modal-header { padding: 1.25rem 1.5rem .5rem; }
        .modal-body { padding: 1rem 1.5rem 1.5rem; }

        /* ═══════ FOOTER ═══════ */
        .footer-modern {
            background: var(--surface);
            border-top: 1px solid var(--border);
            padding: 1.25rem 0;
            margin-top: 3rem;
            text-align: center;
            color: var(--text-muted);
            font-size: .82rem;
            font-weight: 500;
            word-break: break-word;
        }

        /* ═══════ ANIMATIONS ═══════ */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in { animation: fadeInUp .5s cubic-bezier(.4,0,.2,1) both; }
        .fade-in-delay-1 { animation-delay: .05s; }
        .fade-in-delay-2 { animation-delay: .1s; }
        .fade-in-delay-3 { animation-delay: .15s; }
        .fade-in-delay-4 { animation-delay: .2s; }

        /* ═══════ EMPTY STATE ═══════ */
        .empty-state {
            padding: 3rem 1rem;
            text-align: center;
            color: var(--text-muted);
        }
        .empty-state i { font-size: 2.5rem; opacity: .4; margin-bottom: .75rem; display: block; }
        .empty-state p { font-size: .9rem; margin: 0; }

        /* ═══════ SCROLLBAR ═══════ */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* ═══════ RESPONSIVE ═══════ */
        @media (max-width: 991px) {
            .navbar-modern .navbar-collapse {
                background: rgba(49,46,129,.95);
                border-radius: var(--radius);
                padding: 1rem;
                margin-top: .75rem;
                backdrop-filter: blur(20px);
            }
            .navbar-modern .navbar-brand {
                font-size: 1rem;
                gap: 8px;
            }
            .navbar-modern .navbar-brand .brand-icon {
                width: 34px;
                height: 34px;
            }
            .navbar-modern .nav-link { padding: 10px 14px !important; }
            .page-header h3 { font-size: 1.25rem; }
        }
        @media (max-width: 767px) {
            main.container {
                padding-left: .75rem;
                padding-right: .75rem;
            }
            .page-header {
                margin-bottom: 1rem;
                padding-bottom: .75rem;
            }
            .card-modern {
                border-radius: 12px;
            }
            .card-modern .card-body {
                padding: .9rem;
            }
            .table-modern thead th,
            .table-modern tbody td {
                padding: 10px 12px;
                font-size: .82rem;
            }
            .btn-gradient,
            .btn-outline-primary,
            .btn-logout {
                font-size: .82rem;
                padding: 8px 12px;
            }
            .alert-modern {
                padding: 12px 14px;
                font-size: .85rem;
            }
        }
        @media (max-width: 575px) {
            .navbar-modern {
                padding: .5rem 0;
            }
            .navbar-modern .navbar-brand {
                font-size: .92rem;
                max-width: calc(100vw - 96px);
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .stat-card .card-body { padding: .75rem !important; }
            .stat-card .stat-icon { width: 44px; height: 44px; font-size: 18px; border-radius: 12px; }
            .stat-card .fs-4 { font-size: 1.3rem !important; }
            .footer-modern {
                font-size: .75rem;
                padding: .9rem .6rem;
            }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark navbar-modern sticky-top">
    <div class="container">
        <a class="navbar-brand" href="@auth {{ auth()->user()->role === 'admin' ? route('admin.dashboard') : (in_array(auth()->user()->role, ['guru_pembimbing', 'pembimbing_perusahaan'], true) ? route('pembimbing.dashboard') : route('siswa.dashboard')) }} @endauth">
            <div class="brand-icon"><img src="{{ asset('images/logo-yaspat.jpg') }}" alt="Logo SMK Fatahillah"></div>
            PKL SMK Fatahillah
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarMain">
            @auth
                @if(auth()->user()->role === 'admin')
                    <ul class="navbar-nav me-auto ms-lg-3 gap-1">
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-grid-1x2-fill me-1"></i>Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}" href="{{ route('admin.siswa.index') }}"><i class="bi bi-people-fill me-1"></i>Siswa</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.perusahaan.*') ? 'active' : '' }}" href="{{ route('admin.perusahaan.index') }}"><i class="bi bi-building me-1"></i>Perusahaan</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.absensi.*') ? 'active' : '' }}" href="{{ route('admin.absensi.index') }}"><i class="bi bi-calendar-check me-1"></i>Absensi</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.jurnal.*') ? 'active' : '' }}" href="{{ route('admin.jurnal.index') }}"><i class="bi bi-journal-text me-1"></i>Jurnal</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.chart') ? 'active' : '' }}" href="{{ route('admin.chart') }}"><i class="bi bi-bar-chart-line-fill me-1"></i>Grafik</a></li>
                    </ul>
                @elseif(in_array(auth()->user()->role, ['guru_pembimbing', 'pembimbing_perusahaan'], true))
                    <ul class="navbar-nav me-auto ms-lg-3 gap-1">
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('pembimbing.dashboard') ? 'active' : '' }}" href="{{ route('pembimbing.dashboard') }}"><i class="bi bi-grid-1x2-fill me-1"></i>Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('pembimbing.absensi.*') ? 'active' : '' }}" href="{{ route('pembimbing.absensi.index') }}"><i class="bi bi-calendar-check me-1"></i>Absensi @if(auth()->user()->role === 'pembimbing_perusahaan' && ($pendingAbsensiNavbarCount ?? 0) > 0)<span class="badge rounded-pill bg-danger ms-1">{{ $pendingAbsensiNavbarCount }}</span>@endif</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('pembimbing.jurnal.*') ? 'active' : '' }}" href="{{ route('pembimbing.jurnal.index') }}"><i class="bi bi-journal-text me-1"></i>Jurnal @if(auth()->user()->role === 'pembimbing_perusahaan' && ($pendingJurnalNavbarCount ?? 0) > 0)<span class="badge rounded-pill bg-danger ms-1">{{ $pendingJurnalNavbarCount }}</span>@endif</a></li>
                        @if(auth()->user()->role === 'pembimbing_perusahaan')
                            <li class="nav-item"><a class="nav-link {{ request()->routeIs('pembimbing.siswa.*') ? 'active' : '' }}" href="{{ route('pembimbing.siswa.create') }}"><i class="bi bi-person-plus-fill me-1"></i>Tambah Siswa</a></li>
                        @endif
                    </ul>
                @else
                    <ul class="navbar-nav me-auto ms-lg-3 gap-1">
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}" href="{{ route('siswa.dashboard') }}"><i class="bi bi-grid-1x2-fill me-1"></i>Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('siswa.absensi.*') ? 'active' : '' }}" href="{{ route('siswa.absensi.index') }}"><i class="bi bi-calendar-check me-1"></i>Absensi</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('siswa.jurnal.*') ? 'active' : '' }}" href="{{ route('siswa.jurnal.index') }}"><i class="bi bi-journal-text me-1"></i>Jurnal</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('siswa.chart') ? 'active' : '' }}" href="{{ route('siswa.chart') }}"><i class="bi bi-bar-chart-line-fill me-1"></i>Grafik</a></li>
                    </ul>
                @endif
                <div class="d-flex align-items-center gap-2">
                    <div class="user-badge d-none d-lg-flex">
                        <div class="avatar"><i class="bi bi-person-fill"></i></div>
                        <span>{{ auth()->user()->name }}</span>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-logout"><i class="bi bi-box-arrow-right me-1"></i>Logout</button>
                    </form>
                </div>
            @endauth
        </div>
    </div>
</nav>

<main class="container py-4">
    @if(session('success'))
        <div class="alert-modern alert-success mb-4 fade-in">
            <i class="bi bi-check-circle-fill fs-5"></i> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert-modern alert-danger mb-4 fade-in">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div>
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="fade-in">
        @yield('content')
    </div>
</main>

<footer class="footer-modern">
    <div class="container">
        <img src="{{ asset('images/logo-yaspat.jpg') }}" alt="Logo SMK Fatahillah" style="width:16px;height:16px;object-fit:contain;border-radius:2px;vertical-align:-2px;" class="me-1"> &copy; {{ date('Y') }} PKL SMK Fatahillah &mdash; Sistem Informasi Praktek Kerja Lapangan
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
