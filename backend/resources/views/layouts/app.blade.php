<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Agency Crew Accommodation') — CPPL</title>
    
    <!-- PWA Setup -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0e9ae0">
    <link rel="apple-touch-icon" href="/icon-192x192.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --navy:    #0d1b2a;
            --navy2:   #1b2d42;
            --accent:  #1a78c2;
            --accent2: #0e9ae0;
            --teal:    #0cb8a8;
            --green:   #22c55e;
            --red:     #ef4444;
            --amber:   #f59e0b;
            --text:    #e2e8f0;
            --muted:   #94a3b8;
            --card:    rgba(27,45,66,0.85);
            --border:  rgba(30,58,95,0.6);
            --radius:  14px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--navy);
            color: var(--text);
            min-height: 100vh;
            background-image:
                radial-gradient(ellipse at 20% 10%, rgba(26,120,194,0.18) 0%, transparent 55%),
                radial-gradient(ellipse at 80% 90%, rgba(12,184,168,0.12) 0%, transparent 55%);
        }

        /* ── Sidebar ─────────────────────────────────────────────── */
        .sidebar {
            position: fixed; left: 0; top: 0; bottom: 0; width: 240px;
            background: var(--navy2);
            border-right: 1px solid var(--border);
            display: flex; flex-direction: column;
            padding: 0;
            z-index: 100;
        }
        .sidebar-brand {
            padding: 22px 22px 18px;
            border-bottom: 1px solid var(--border);
        }
        .sidebar-brand .logo-top {
            font-size: 11px; font-weight: 600; letter-spacing: 2px;
            color: var(--teal); text-transform: uppercase; margin-bottom: 6px;
        }
        .sidebar-brand h2 {
            font-size: 15px; font-weight: 800; color: #fff; line-height: 1.3;
        }
        .nav-section { padding: 16px 12px 8px; font-size: 10px; font-weight: 600;
            letter-spacing: 1.5px; color: var(--muted); text-transform: uppercase; }
        .sidebar nav a {
            display: flex; align-items: center; gap: 11px;
            padding: 11px 20px; color: var(--muted); text-decoration: none;
            font-size: 13.5px; font-weight: 500; border-radius: 10px;
            margin: 2px 10px; transition: all .2s;
        }
        .sidebar nav a:hover, .sidebar nav a.active {
            background: rgba(26,120,194,0.2); color: #fff;
        }
        .sidebar nav a.active { border-left: 3px solid var(--accent2); }
        .sidebar nav a i { width: 18px; text-align: center; font-size: 15px; }
        .sidebar-footer {
            margin-top: auto; padding: 16px;
            border-top: 1px solid var(--border);
        }
        .sidebar-footer .user-info { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
        .sidebar-footer .avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--teal));
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 13px; color: #fff;
        }
        .sidebar-footer .user-name { font-size: 13px; font-weight: 600; }
        .sidebar-footer .user-role { font-size: 11px; color: var(--muted); }
        .btn-logout {
            display: flex; align-items: center; gap: 8px;
            width: 100%; padding: 9px 14px; background: rgba(239,68,68,0.1);
            color: var(--red); border: 1px solid rgba(239,68,68,0.2);
            border-radius: 8px; cursor: pointer; font-size: 13px;
            font-weight: 500; transition: all .2s; text-decoration: none;
        }
        .btn-logout:hover { background: rgba(239,68,68,0.2); }

        /* ── Main content ────────────────────────────────────────── */
        .main { margin-left: 240px; padding: 28px 30px; min-height: 100vh; }

        .page-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 28px;
        }
        .page-header h1 { font-size: 22px; font-weight: 800; }
        .page-header p { color: var(--muted); font-size: 13px; margin-top: 3px; }

        /* ── Cards ───────────────────────────────────────────────── */
        .card {
            background: var(--card);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 22px 24px;
        }

        /* ── Buttons ─────────────────────────────────────────────── */
        .btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 9px 18px; border-radius: 9px; font-size: 13.5px;
            font-weight: 600; text-decoration: none; border: none;
            cursor: pointer; transition: all .2s;
        }
        .btn-primary { background: linear-gradient(135deg, var(--accent), var(--accent2)); color: #fff; }
        .btn-primary:hover { opacity: .88; transform: translateY(-1px); }
        .btn-secondary { background: rgba(255,255,255,0.07); color: var(--text); border: 1px solid var(--border); }
        .btn-secondary:hover { background: rgba(255,255,255,0.12); }
        .btn-danger { background: rgba(239,68,68,0.15); color: var(--red); border: 1px solid rgba(239,68,68,0.2); }
        .btn-danger:hover { background: rgba(239,68,68,0.25); }
        .btn-success { background: rgba(34,197,94,0.15); color: var(--green); border: 1px solid rgba(34,197,94,0.2); }
        .btn-success:hover { background: rgba(34,197,94,0.25); }
        .btn-sm { padding: 5px 12px; font-size: 12px; }

        /* ── Badges ──────────────────────────────────────────────── */
        .badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 11px; border-radius: 20px; font-size: 11.5px; font-weight: 600;
        }
        .badge-green { background: rgba(34,197,94,0.15); color: var(--green); }
        .badge-amber { background: rgba(245,158,11,0.15); color: var(--amber); }
        .badge-gray  { background: rgba(148,163,184,0.15); color: var(--muted); }

        /* ── Stats ───────────────────────────────────────────────── */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 32px;
        }

        /* Tooltip styles */
        .tooltip {
            position: relative;
            display: inline-block;
            cursor: help;
            margin-left: 5px;
            color: var(--muted);
            font-size: 12px;
        }
        .tooltip .tooltip-text {
            visibility: hidden;
            width: 200px;
            background-color: #1e293b;
            color: #fff;
            text-align: left;
            border-radius: 8px;
            padding: 10px;
            position: absolute;
            z-index: 100;
            bottom: 125%;
            left: 50%;
            margin-left: -100px;
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 11px;
            line-height: 1.4;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255,255,255,0.1);
        }
        .tooltip:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }
        .stat-card { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); padding: 20px 22px; }
        .stat-card .stat-label { font-size: 12px; color: var(--muted); font-weight: 500; text-transform: uppercase; letter-spacing: 1px; }
        .stat-card .stat-value { font-size: 34px; font-weight: 800; margin: 8px 0 4px; }
        .stat-card .stat-icon {
            width: 42px; height: 42px; border-radius: 11px;
            display: flex; align-items: center; justify-content: center; font-size: 18px;
            margin-bottom: 14px;
        }
        .icon-blue  { background: rgba(26,120,194,0.2); color: var(--accent2); }
        .icon-green { background: rgba(34,197,94,0.2);  color: var(--green); }
        .icon-gray  { background: rgba(148,163,184,0.15); color: var(--muted); }

        /* ── Grid of booking cards ───────────────────────────────── */
        .booking-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 18px;
        }
        .booking-card {
            background: var(--card); border: 1px solid var(--border);
            border-radius: var(--radius); padding: 20px 22px;
            transition: transform .2s, box-shadow .2s;
            text-decoration: none; color: inherit;
        }
        .booking-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.35);
        }
        .booking-card .card-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px; }
        .booking-card .crew-avatar {
            width: 44px; height: 44px; border-radius: 50%;
            object-fit: cover; border: 2px solid var(--accent);
        }
        .booking-card .crew-avatar-placeholder {
            width: 44px; height: 44px; border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--teal));
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 16px; color: #fff; flex-shrink: 0;
        }
        .booking-card .crew-name { font-size: 15px; font-weight: 700; margin-bottom: 2px; }
        .booking-card .crew-title-text { font-size: 12px; color: var(--muted); }
        .booking-card .meta-row {
            display: flex; align-items: center; gap: 8px;
            font-size: 12px; color: var(--muted); margin-top: 10px;
        }
        .booking-card .meta-row i { color: var(--accent2); width: 14px; }
        .booking-card .divider { border: none; border-top: 1px solid var(--border); margin: 14px 0; }

        /* ── Forms ───────────────────────────────────────────────── */
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 12.5px; font-weight: 600; color: var(--muted); margin-bottom: 7px; text-transform: uppercase; letter-spacing: .5px; }
        .form-control {
            width: 100%; padding: 10px 14px;
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border); border-radius: 9px;
            color: var(--text); font-size: 14px; font-family: 'Inter', sans-serif;
            transition: border .2s;
        }
        .form-control:focus { outline: none; border-color: var(--accent); background: rgba(255,255,255,0.08); }
        .form-control::placeholder { color: rgba(148,163,184,0.5); }
        select.form-control option { background: var(--navy2); color: var(--text); }
        textarea.form-control { resize: vertical; min-height: 90px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
        .invalid-feedback { color: var(--red); font-size: 12px; margin-top: 5px; }
        .is-invalid { border-color: var(--red) !important; }

        /* ── Table ───────────────────────────────────────────────── */
        .table-wrap { overflow-x: auto; border-radius: var(--radius); }
        table { width: 100%; border-collapse: collapse; }
        table th { padding: 12px 16px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; color: var(--muted); background: rgba(255,255,255,0.03); text-align: left; white-space: nowrap; }
        table td { padding: 14px 16px; border-top: 1px solid var(--border); font-size: 13.5px; vertical-align: middle; white-space: nowrap; }
        table tr:hover td { background: rgba(255,255,255,0.02); }

        /* ── Alert toast ─────────────────────────────────────────── */
        .alert {
            display: flex; align-items: center; gap: 10px;
            padding: 13px 18px; border-radius: 10px; margin-bottom: 20px;
            font-size: 14px; font-weight: 500;
            animation: slideIn .3s ease;
        }
        .alert-success { background: rgba(34,197,94,0.12); border: 1px solid rgba(34,197,94,0.25); color: var(--green); }
        .alert-error   { background: rgba(239,68,68,0.12);  border: 1px solid rgba(239,68,68,0.25);  color: var(--red); }
        @keyframes slideIn { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }

        /* ── Filter bar ──────────────────────────────────────────── */
        .filter-bar { display: flex; gap: 12px; margin-bottom: 22px; flex-wrap: wrap; }
        .filter-bar .form-control { width: auto; flex: 1; min-width: 180px; }

        /* ── Pagination ──────────────────────────────────────────── */
        .pagination { display: flex; gap: 6px; justify-content: center; margin-top: 24px; }
        .pagination a, .pagination span {
            padding: 7px 13px; border-radius: 8px; font-size: 13px; font-weight: 500;
            background: rgba(255,255,255,0.05); border: 1px solid var(--border);
            color: var(--text); text-decoration: none; transition: .2s;
        }
        .pagination a:hover { background: rgba(26,120,194,0.2); }
        .pagination span.active { background: var(--accent); border-color: var(--accent); color: #fff; }

        /* ── Responsive ──────────────────────────────────────────── */
        @media (max-width: 768px) {
            .sidebar { width: 60px; }
            .sidebar-brand h2, .sidebar-brand .logo-top,
            .sidebar nav a span, .nav-section, .sidebar-footer .user-name,
            .sidebar-footer .user-role, .sidebar-footer .btn-logout span { display: none; }
            .main { margin-left: 60px; padding: 18px; }
            .form-row { grid-template-columns: 1fr; }
            .stats-row { grid-template-columns: 1fr; }
        }
    </style>
    @stack('styles')
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand">
        <div class="logo-top">CPPL Agency</div>
        <h2>Crew Accommodation</h2>
    </div>

    <div class="nav-section">Main</div>
    <nav>
        <a href="{{ route('bookings.index') }}" class="{{ request()->routeIs('bookings.*') ? 'active' : '' }}">
            <i class="fas fa-calendar-check"></i> <span>Bookings</span>
        </a>
        <a href="{{ route('crews.index') }}" class="{{ request()->routeIs('crews.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i> <span>Crew</span>
        </a>
    </nav>

    <div class="nav-section">Management</div>
    <nav>
        <a href="{{ route('hotels.index') }}" class="{{ request()->routeIs('hotels.*') ? 'active' : '' }}">
            <i class="fas fa-hotel"></i> <span>Hotels</span>
        </a>
        <a href="{{ route('companies.index') }}" class="{{ request()->routeIs('companies.*') ? 'active' : '' }}">
            <i class="fas fa-ship"></i> <span>Companies</span>
        </a>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="fas fa-user-shield"></i> <span>Users</span>
        </a>
        <a href="{{ route('admin.activity-log') }}" class="{{ request()->routeIs('admin.activity-log') ? 'active' : '' }}">
            <i class="fas fa-list-check"></i> <span>Activity Log</span>
        </a>
        @endif
    </nav>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</div>
            <div>
                <div class="user-name">{{ auth()->user()->name ?? 'Admin' }}</div>
                <div class="user-role">{{ auth()->user()->isAdmin() ? 'Administrator' : 'Agency Staff' }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i> <span>Sign Out</span>
            </button>
        </form>
    </div>
</div>

<main class="main">
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error') || $errors->any())
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') ?? $errors->first() }}
        </div>
    @endif

    @yield('content')
</main>

<script>
    // Auto-dismiss alerts
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(a => {
            a.style.opacity = '0'; a.style.transition = 'opacity .5s';
            setTimeout(() => a.remove(), 500);
        });
    }, 4000);
</script>
@stack('scripts')
<script>
    // PWA Service Worker Registration
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js')
                .then(registration => {
                    console.log('SW registered: ', registration);
                })
                .catch(registrationError => {
                    console.log('SW registration failed: ', registrationError);
                });
        });
    }
</script>
</body>
</html>
