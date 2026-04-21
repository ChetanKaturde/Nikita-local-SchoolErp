<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Teacher Dashboard') — School ERP</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 268px;

            /* Sidebar palette */
            --sb-bg:        #0d0f12;
            --sb-surface:   #13161b;
            --sb-border:    rgba(255,255,255,0.06);
            --sb-muted:     #5a6270;
            --sb-text:      #9aa3b0;
            --sb-text-active: #ffffff;

            /* Accent */
            --accent:       #4f8ef7;
            --accent-dark:  #2f6de0;
            --accent-glow:  rgba(79,142,247,0.22);

            /* Content area */
            --content-bg:   #f0f2f7;
            --card-bg:      #ffffff;
            --topbar-bg:    #ffffff;

            /* Typography */
            --font-main: 'Plus Jakarta Sans', sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: var(--font-main);
            background: var(--content-bg);
            overflow-x: hidden;
            margin: 0;
        }

        /* ─── SCROLLBAR ─────────────────────────────── */
        #sidebar::-webkit-scrollbar { width: 4px; }
        #sidebar::-webkit-scrollbar-track { background: transparent; }
        #sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.08); border-radius: 4px; }

        /* ─── LAYOUT WRAPPER ────────────────────────── */
        .wrapper { display: flex; min-height: 100vh; }

        /* ─── SIDEBAR ───────────────────────────────── */
        #sidebar {
            position: fixed;
            top: 0; left: 0; bottom: 0;
            width: var(--sidebar-width);
            background: var(--sb-bg);
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: transform 0.3s cubic-bezier(0.4,0,0.2,1);
            overflow-y: auto;
            overflow-x: hidden;
            border-right: 1px solid var(--sb-border);
        }

        #sidebar.collapsed { transform: translateX(-100%); }

        /* Brand */
        .sb-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 24px 20px 20px;
            border-bottom: 1px solid var(--sb-border);
            text-decoration: none;
        }

        .sb-brand-icon {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            border-radius: 10px;
            display: grid; place-items: center;
            font-size: 18px; color: #fff;
            flex-shrink: 0;
            box-shadow: 0 4px 14px var(--accent-glow);
        }

        .sb-brand-text { line-height: 1.2; }
        .sb-brand-name { font-size: 15px; font-weight: 800; color: #fff; letter-spacing: -0.3px; }
        .sb-brand-sub  { font-size: 11px; color: var(--sb-muted); font-weight: 500; letter-spacing: 0.3px; }

        /* Teacher card */
        .sb-teacher-card {
            margin: 16px 14px;
            padding: 14px;
            background: var(--sb-surface);
            border: 1px solid var(--sb-border);
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 11px;
        }

        .sb-avatar {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, #4f8ef7 0%, #7b5ea7 100%);
            border-radius: 50%;
            display: grid; place-items: center;
            font-size: 15px; font-weight: 700; color: #fff;
            flex-shrink: 0;
        }

        .sb-teacher-name { font-size: 13px; font-weight: 600; color: #e2e8f0; line-height: 1.2; }
        .sb-teacher-role { font-size: 11px; color: var(--sb-muted); margin-top: 2px; }

        /* Nav sections */
        .sb-nav { flex: 1; padding: 8px 0 16px; }

        .sb-section-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: var(--sb-muted);
            padding: 18px 20px 6px;
        }

        .sb-link {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 9px 14px;
            margin: 2px 10px;
            border-radius: 9px;
            color: var(--sb-text);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            transition: background 0.18s, color 0.18s, transform 0.18s;
            position: relative;
            border: 1px solid transparent;
        }

        .sb-link:hover {
            background: rgba(255,255,255,0.05);
            color: var(--sb-text-active);
        }

        .sb-link.active {
            background: var(--accent-glow);
            color: var(--accent);
            border-color: rgba(79,142,247,0.18);
            font-weight: 600;
        }

        .sb-link.active .sb-link-icon { color: var(--accent); }

        .sb-link-icon {
            width: 32px; height: 32px;
            display: grid; place-items: center;
            border-radius: 7px;
            font-size: 15px;
            background: rgba(255,255,255,0.04);
            flex-shrink: 0;
            transition: background 0.18s;
        }

        .sb-link.active .sb-link-icon {
            background: rgba(79,142,247,0.15);
        }

        .sb-link:hover .sb-link-icon {
            background: rgba(255,255,255,0.08);
        }

        .sb-chevron {
            margin-left: auto;
            font-size: 11px;
            color: var(--sb-muted);
            transition: transform 0.25s;
        }

        .sb-link[aria-expanded="true"] .sb-chevron { transform: rotate(180deg); }

        /* Submenu */
        .sb-submenu {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .sb-submenu .sb-link {
            margin-left: 22px;
            margin-right: 10px;
            padding: 8px 12px 8px 36px;
            font-size: 13px;
            position: relative;
        }

        .sb-submenu .sb-link::before {
            content: '';
            position: absolute;
            left: 18px;
            top: 50%; transform: translateY(-50%);
            width: 5px; height: 5px;
            border-radius: 50%;
            background: var(--sb-muted);
            transition: background 0.18s;
        }

        .sb-submenu .sb-link.active::before,
        .sb-submenu .sb-link:hover::before { background: var(--accent); }

        /* Sidebar footer */
        .sb-footer {
            padding: 14px;
            border-top: 1px solid var(--sb-border);
        }

        .sb-logout {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 10px 14px;
            border-radius: 9px;
            background: rgba(239,68,68,0.08);
            border: 1px solid rgba(239,68,68,0.12);
            color: #f87171;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, border-color 0.2s;
            font-family: var(--font-main);
        }

        .sb-logout:hover {
            background: rgba(239,68,68,0.16);
            border-color: rgba(239,68,68,0.25);
        }

        /* ─── CONTENT ────────────────────────────────── */
        #content {
            margin-left: var(--sidebar-width);
            flex: 1;
            min-height: 100vh;
            transition: margin-left 0.3s cubic-bezier(0.4,0,0.2,1);
            display: flex;
            flex-direction: column;
        }

        #content.expanded { margin-left: 0; }

        /* ─── TOPBAR ─────────────────────────────────── */
        .topbar {
            position: sticky;
            top: 0;
            z-index: 900;
            background: var(--topbar-bg);
            border-bottom: 1px solid #e8eaf0;
            padding: 0 28px;
            height: 62px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            box-shadow: 0 1px 0 #e8eaf0, 0 2px 12px rgba(0,0,0,0.04);
        }

        .topbar-left { display: flex; align-items: center; gap: 14px; }

        .toggle-btn {
            width: 38px; height: 38px;
            border-radius: 9px;
            border: 1.5px solid #e8eaf0;
            background: transparent;
            display: grid; place-items: center;
            font-size: 17px;
            color: #64748b;
            cursor: pointer;
            transition: background 0.18s, border-color 0.18s, color 0.18s;
        }

        .toggle-btn:hover { background: #f1f5f9; border-color: #cbd5e1; color: #1e293b; }

        .topbar-breadcrumb {
            font-size: 13px;
            color: #94a3b8;
            font-weight: 500;
        }

        .topbar-breadcrumb span { color: #1e293b; font-weight: 600; }

        .topbar-right { display: flex; align-items: center; gap: 8px; }

        .topbar-icon-btn {
            width: 38px; height: 38px;
            border-radius: 9px;
            border: 1.5px solid #e8eaf0;
            background: transparent;
            display: grid; place-items: center;
            font-size: 16px;
            color: #64748b;
            cursor: pointer;
            position: relative;
            transition: background 0.18s, color 0.18s;
            text-decoration: none;
        }

        .topbar-icon-btn:hover { background: #f1f5f9; color: #1e293b; }

        .topbar-badge {
            position: absolute;
            top: 5px; right: 5px;
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #ef4444;
            border: 1.5px solid #fff;
        }

        /* User dropdown */
        .topbar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 5px 10px 5px 5px;
            border-radius: 10px;
            border: 1.5px solid #e8eaf0;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            transition: background 0.18s, border-color 0.18s;
        }

        .topbar-user:hover { background: #f8fafc; border-color: #cbd5e1; }

        .topbar-avatar {
            width: 30px; height: 30px;
            border-radius: 8px;
            background: linear-gradient(135deg, #4f8ef7, #7b5ea7);
            display: grid; place-items: center;
            font-size: 13px; font-weight: 700; color: #fff;
        }

        .topbar-user-name { font-size: 13px; font-weight: 600; color: #1e293b; }
        .topbar-user-role { font-size: 11px; color: #94a3b8; }

        /* Dropdown menu */
        .dropdown-menu {
            border: 1px solid #e8eaf0;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
            padding: 6px;
            min-width: 180px;
        }

        .dropdown-item {
            border-radius: 8px;
            font-size: 13.5px;
            padding: 8px 12px;
            font-family: var(--font-main);
            font-weight: 500;
            color: #374151;
        }

        .dropdown-item:hover { background: #f1f5f9; }
        .dropdown-item.text-danger { color: #ef4444 !important; }
        .dropdown-item.text-danger:hover { background: #fef2f2; }
        .dropdown-divider { border-color: #f1f5f9; margin: 4px 0; }

        /* ─── ALERTS ─────────────────────────────────── */
        .alert {
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 500;
            border: none;
        }

        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border-left: 4px solid #22c55e !important;
        }

        .alert-danger {
            background: #fef2f2;
            color: #991b1b;
            border-left: 4px solid #ef4444 !important;
        }

        /* ─── MAIN CONTENT ───────────────────────────── */
        .main-content {
            flex: 1;
            padding: 0;
        }

        /* ─── CARD DEFAULTS ──────────────────────────── */
        .card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.04);
            background: var(--card-bg);
        }

        .card-header {
            border-bottom: 1px solid #f1f5f9;
            background: transparent;
            padding: 18px 22px;
            border-radius: 14px 14px 0 0 !important;
        }

        /* ─── BUTTONS ────────────────────────────────── */
        .btn-primary {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-family: var(--font-main);
            font-size: 13.5px;
            box-shadow: 0 2px 10px var(--accent-glow);
            transition: opacity 0.18s, transform 0.18s, box-shadow 0.18s;
        }

        .btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-1px);
            box-shadow: 0 4px 18px var(--accent-glow);
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
        }

        /* ─── TABLES ─────────────────────────────────── */
        .table { margin-bottom: 0; font-size: 14px; }
        .table thead th {
            background: #f8fafc;
            border-bottom: 1.5px solid #e8eaf0;
            font-weight: 700;
            font-size: 11.5px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #64748b;
            padding: 12px 16px;
        }

        .table tbody td {
            padding: 13px 16px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            color: #374151;
        }

        .table-hover tbody tr:hover td { background: #f8fafc; }

        /* ─── PAGINATION ─────────────────────────────── */
        .pagination { gap: 4px; margin: 0; }

        .pagination .page-link {
            border: 1.5px solid #e8eaf0;
            border-radius: 8px;
            color: #374151;
            font-size: 13px;
            font-weight: 600;
            padding: 6px 12px;
            transition: all 0.18s;
            background: #fff;
            font-family: var(--font-main);
        }

        .pagination .page-link:hover {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent);
            box-shadow: 0 3px 10px var(--accent-glow);
            transform: translateY(-1px);
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            border-color: var(--accent);
            color: #fff;
            box-shadow: 0 3px 10px var(--accent-glow);
        }

        .pagination .page-item.disabled .page-link {
            color: #cbd5e1;
            background: #f8fafc;
            border-color: #f1f5f9;
        }

        /* ─── FORMS ──────────────────────────────────── */
        .form-control, .form-select {
            border: 1.5px solid #e8eaf0;
            border-radius: 9px;
            font-size: 13.5px;
            font-family: var(--font-main);
            padding: 9px 14px;
            color: #1e293b;
            transition: border-color 0.18s, box-shadow 0.18s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(79,142,247,0.12);
            outline: none;
        }

        /* ─── BADGES ─────────────────────────────────── */
        .badge {
            font-family: var(--font-main);
            font-weight: 600;
            font-size: 11px;
            padding: 4px 9px;
            border-radius: 6px;
        }

        /* ─── MOBILE ─────────────────────────────────── */
        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.mobile-open { transform: translateX(0); }
            #content { margin-left: 0 !important; }
            .topbar { padding: 0 16px; }
            .topbar-breadcrumb { display: none; }
        }
    </style>

    @yield('styles')
    @stack('styles')
</head>
<body>
<div class="wrapper">

    <!-- ═══════════ SIDEBAR ═══════════ -->
    <nav id="sidebar">

        <!-- Brand -->
        <a class="sb-brand" href="{{ route('teacher.dashboard') }}">
            <div class="sb-brand-icon"><i class="bi bi-mortarboard-fill"></i></div>
            <div class="sb-brand-text">
                <div class="sb-brand-name">School ERP</div>
                <div class="sb-brand-sub">Teacher Portal</div>
            </div>
        </a>

        <!-- Teacher card -->
        <div class="sb-teacher-card">
            <div class="sb-avatar">{{ substr(auth()->user()->name ?? 'T', 0, 1) }}</div>
            <div>
                <div class="sb-teacher-name">{{ auth()->user()->name ?? 'Teacher' }}</div>
                <div class="sb-teacher-role">{{ auth()->user()->roles->first()->name ?? 'Teacher' }}</div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="sb-nav">

            <!-- Core -->
            <div class="sb-section-label">Core</div>

            <a href="{{ route('teacher.dashboard') }}"
               class="sb-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
                <span class="sb-link-icon"><i class="bi bi-grid-1x2"></i></span>
                Dashboard
            </a>

            <a href="{{ route('teacher.profile') }}"
               class="sb-link {{ request()->routeIs('teacher.profile*') ? 'active' : '' }}">
                <span class="sb-link-icon"><i class="bi bi-person-circle"></i></span>
                My Profile
            </a>

            <!-- Academic -->
            <div class="sb-section-label">Academic</div>

            <a href="{{ route('teacher.divisions.index') }}"
               class="sb-link {{ request()->routeIs('teacher.divisions.*') ? 'active' : '' }}">
                <span class="sb-link-icon"><i class="bi bi-collection"></i></span>
                My Divisions
            </a>

            <!-- Assessment -->
            <div class="sb-section-label">Assessment</div>

            <!-- Attendance -->
            <a href="#attendanceMenu"
               class="sb-link sidebar-dropdown-toggle {{ request()->routeIs('teacher.attendance.*') ? 'active' : '' }}"
               data-bs-toggle="collapse"
               aria-expanded="{{ request()->routeIs('teacher.attendance.*') ? 'true' : 'false' }}">
                <span class="sb-link-icon"><i class="bi bi-calendar-check"></i></span>
                Attendance
                <i class="bi bi-chevron-down sb-chevron"></i>
            </a>
            <ul class="sb-submenu collapse {{ request()->routeIs('teacher.attendance.*') ? 'show' : '' }}" id="attendanceMenu">
                <li>
                    <a href="{{ route('teacher.attendance.index') }}"
                       class="sb-link {{ request()->routeIs('teacher.attendance.index') ? 'active' : '' }}">
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('teacher.attendance.history') }}"
                       class="sb-link {{ request()->routeIs('teacher.attendance.history') ? 'active' : '' }}">
                        History
                    </a>
                </li>
                <li>
                    <a href="{{ route('teacher.attendance.report') }}"
                       class="sb-link {{ request()->routeIs('teacher.attendance.report') ? 'active' : '' }}">
                        Reports
                    </a>
                </li>
            </ul>

            <!-- Marks Entry -->
            <a href="#marksMenu"
               class="sb-link sidebar-dropdown-toggle {{ request()->routeIs('examinations.*') || request()->routeIs('teacher.results.*') ? 'active' : '' }}"
               data-bs-toggle="collapse"
               aria-expanded="{{ request()->routeIs('examinations.*') || request()->routeIs('teacher.results.*') ? 'true' : 'false' }}">
                <span class="sb-link-icon"><i class="bi bi-clipboard-data"></i></span>
                Marks Entry
                <i class="bi bi-chevron-down sb-chevron"></i>
            </a>
            <ul class="sb-submenu collapse {{ request()->routeIs('examinations.*') || request()->routeIs('teacher.results.*') ? 'show' : '' }}" id="marksMenu">
                <li>
                    <a href="{{ route('teacher.results.index') }}"
                       class="sb-link {{ request()->routeIs('teacher.results.index') ? 'active' : '' }}">
                        Teacher Results
                    </a>
                </li>
            </ul>

        </div><!-- /.sb-nav -->

        <!-- Footer / Logout -->
        <div class="sb-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="sb-logout">
                    <i class="bi bi-box-arrow-right" style="font-size:15px;"></i>
                    Sign Out
                </button>
            </form>
        </div>

    </nav><!-- /#sidebar -->


    <!-- ═══════════ CONTENT ═══════════ -->
    <div id="content">

        <!-- Topbar -->
        <header class="topbar">
            <div class="topbar-left">
                <button class="toggle-btn" id="sidebarToggle" title="Toggle sidebar">
                    <i class="bi bi-list"></i>
                </button>
                <div class="topbar-breadcrumb d-none d-md-block">
                    School ERP &nbsp;/&nbsp; <span>@yield('page-title', 'Dashboard')</span>
                </div>
            </div>

            <div class="topbar-right">
                <!-- Notifications -->
                <button class="topbar-icon-btn" title="Notifications">
                    <i class="bi bi-bell"></i>
                    <span class="topbar-badge"></span>
                </button>

                <!-- User dropdown -->
                <div class="dropdown">
                    <a href="#" class="topbar-user" data-bs-toggle="dropdown">
                        <div class="topbar-avatar">{{ substr(auth()->user()->name ?? 'T', 0, 1) }}</div>
                        <div class="d-none d-md-block">
                            <div class="topbar-user-name">{{ auth()->user()->name ?? 'Teacher' }}</div>
                            <div class="topbar-user-role">{{ auth()->user()->roles->first()->name ?? 'Teacher' }}</div>
                        </div>
                        <i class="bi bi-chevron-down ms-1" style="font-size:11px;color:#94a3b8;"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('teacher.profile') }}">
                                <i class="bi bi-person me-2 text-primary"></i>My Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-gear me-2 text-secondary"></i>Settings
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger w-100 text-start">
                                    <i class="bi bi-box-arrow-right me-2"></i>Sign Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mx-4 mt-3" role="alert" id="flash-success" style="border-left: 4px solid #22c55e;">
                <i class="bi bi-check-circle-fill me-2"></i>
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <script>
                setTimeout(() => {
                    const el = document.getElementById('flash-success');
                    if (el) bootstrap.Alert.getOrCreateInstance(el).close();
                }, 5000);
            </script>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mx-4 mt-3" role="alert" id="flash-error" style="border-left: 4px solid #ef4444;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Error!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <script>
                setTimeout(() => {
                    const el = document.getElementById('flash-error');
                    if (el) bootstrap.Alert.getOrCreateInstance(el).close();
                }, 7000);
            </script>
        @endif

        <!-- Page Content -->
        <main class="main-content">
            @yield('content')
        </main>

    </div><!-- /#content -->

</div><!-- /.wrapper -->

<!-- Mobile overlay -->
<div id="sidebarOverlay" onclick="closeSidebar()"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:999;backdrop-filter:blur(2px);"></div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const sidebar  = document.getElementById('sidebar');
    const content  = document.getElementById('content');
    const overlay  = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');
    const isMobile = () => window.innerWidth <= 768;

    function closeSidebar() {
        sidebar.classList.remove('mobile-open');
        overlay.style.display = 'none';
    }

    toggleBtn.addEventListener('click', () => {
        if (isMobile()) {
            const open = sidebar.classList.toggle('mobile-open');
            overlay.style.display = open ? 'block' : 'none';
        } else {
            sidebar.classList.toggle('collapsed');
            content.classList.toggle('expanded');
        }
    });

    // Start collapsed on mobile
    if (isMobile()) {
        sidebar.classList.remove('mobile-open');
    }
</script>

@stack('scripts')
</body>
</html>