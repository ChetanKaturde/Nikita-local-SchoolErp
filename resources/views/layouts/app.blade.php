<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'School ERP System')</title>

    <!-- Google Fonts: DM Sans + DM Serif Display -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Serif+Display&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* ─── Design Tokens ─── */
        :root {
            --sidebar-w: 256px;

            /* Palette */
            --ink-900: #111318;
            --ink-700: #2d3139;
            --ink-500: #6b7280;
            --ink-300: #b0b7c3;
            --ink-100: #f0f2f5;
            --ink-50:  #f7f8fa;

            --accent:        #2563eb;
            --accent-light:  #eff4ff;
            --accent-mid:    #bfdbfe;
            --accent-dark:   #1d4ed8;

            --success:       #16a34a;
            --success-light: #f0fdf4;
            --warning:       #d97706;
            --warning-light: #fffbeb;
            --danger:        #dc2626;
            --danger-light:  #fff1f1;

            /* Sidebar specific */
            --sb-bg:         #ffffff;
            --sb-border:     #e5e7eb;
            --sb-text:       #374151;
            --sb-muted:      #9ca3af;
            --sb-hover-bg:   #f3f4f6;
            --sb-active-bg:  #eff4ff;
            --sb-active-txt: #2563eb;

            /* Surfaces */
            --card-bg:       #ffffff;
            --page-bg:       #f7f8fa;

            /* Radii */
            --r-sm: 6px;
            --r-md: 10px;
            --r-lg: 14px;

            /* Shadows */
            --shadow-xs: 0 1px 2px rgba(0,0,0,.05);
            --shadow-sm: 0 2px 6px rgba(0,0,0,.06);
            --shadow-md: 0 4px 16px rgba(0,0,0,.08);

            /* Font */
            --font: 'DM Sans', system-ui, sans-serif;
        }

        * { box-sizing: border-box; }

        body {
            font-family: var(--font);
            font-size: 14px;
            background: var(--page-bg);
            color: var(--ink-700);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* ═══════════════════════════════════════════
           SIDEBAR
        ═══════════════════════════════════════════ */
        .sidebar {
            position: fixed;
            top: 0; left: 0; bottom: 0;
            width: var(--sidebar-w);
            background: var(--sb-bg);
            border-right: 1px solid var(--sb-border);
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: transform .28s cubic-bezier(.4,0,.2,1);
            overflow: hidden;
        }

        /* Logo bar */
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 20px 20px 16px;
            border-bottom: 1px solid var(--sb-border);
            flex-shrink: 0;
        }
        .brand-icon {
            width: 36px; height: 36px;
            background: var(--accent);
            border-radius: var(--r-md);
            display: flex; align-items: center; justify-content: center;
            color: #fff;
            font-size: 16px;
            flex-shrink: 0;
        }
        .brand-text {
            font-size: 15px;
            font-weight: 600;
            color: var(--ink-900);
            letter-spacing: -.2px;
            line-height: 1.2;
        }
        .brand-text small {
            display: block;
            font-size: 11px;
            font-weight: 400;
            color: var(--sb-muted);
            letter-spacing: 0;
        }

        /* Scrollable nav area */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 12px 0 20px;
        }
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: var(--ink-100); border-radius: 4px; }

        /* Section label */
        .nav-label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--sb-muted);
            padding: 14px 20px 4px;
            display: block;
        }

        /* Nav items */
        .sidebar .nav-item { padding: 0 10px; }

        .sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: var(--r-md);
            color: var(--sb-text);
            font-size: 13.5px;
            font-weight: 400;
            text-decoration: none;
            transition: background .15s, color .15s;
            position: relative;
            white-space: nowrap;
            border: none;
            background: transparent;
            width: 100%;
            text-align: left;
        }
        .sidebar .nav-link .nav-icon {
            width: 18px; height: 18px;
            opacity: .65;
            flex-shrink: 0;
            transition: opacity .15s;
            font-size: 14px;
            display: flex; align-items: center; justify-content: center;
        }
        .sidebar .nav-link:hover {
            background: var(--sb-hover-bg);
            color: var(--ink-900);
        }
        .sidebar .nav-link:hover .nav-icon { opacity: 1; }
        .sidebar .nav-link.active {
            background: var(--sb-active-bg);
            color: var(--sb-active-txt);
            font-weight: 500;
        }
        .sidebar .nav-link.active .nav-icon { opacity: 1; }

        /* Active left bar */
        .sidebar .nav-link.active::before {
            content: '';
            position: absolute;
            left: -10px; top: 50%;
            transform: translateY(-50%);
            width: 3px; height: 18px;
            background: var(--accent);
            border-radius: 0 3px 3px 0;
        }

        /* Collapse toggle arrow */
        .nav-link[data-bs-toggle="collapse"] .toggle-arrow {
            margin-left: auto;
            font-size: 12px;
            opacity: .7;
            transition: transform .2s ease;
            color: var(--sb-muted);
        }
        .nav-link[data-bs-toggle="collapse"]:hover .toggle-arrow {
            opacity: 1;
            color: var(--ink-700);
        }
        .nav-link[data-bs-toggle="collapse"]:not(.collapsed) .toggle-arrow {
            transform: rotate(180deg);
            opacity: 1;
            color: var(--accent);
        }

        /* Submenu */
        .sidebar-submenu {
            padding: 2px 0 4px 38px;
            list-style: none;
            margin: 0;
        }
        .sidebar-submenu li { padding: 0 10px 0 0; }
        .sidebar-submenu a {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            border-radius: var(--r-sm);
            color: var(--sb-text);
            font-size: 13px;
            text-decoration: none;
            transition: background .15s, color .15s;
        }
        .sidebar-submenu a:hover { background: var(--sb-hover-bg); color: var(--ink-900); }
        .sidebar-submenu a.active {
            background: var(--sb-active-bg);
            color: var(--sb-active-txt);
            font-weight: 500;
        }

        /* Divider in sidebar */
        .sidebar-divider {
            height: 1px;
            background: var(--sb-border);
            margin: 8px 20px;
        }

        /* Logout at bottom */
        .sidebar-footer {
            flex-shrink: 0;
            border-top: 1px solid var(--sb-border);
            padding: 10px;
        }
        .sidebar-footer .nav-link { color: var(--danger); }
        .sidebar-footer .nav-link:hover { background: var(--danger-light); color: var(--danger); }

        /* ═══════════════════════════════════════════
           MAIN CONTENT
        ═══════════════════════════════════════════ */
        .main-content {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left .28s cubic-bezier(.4,0,.2,1);
        }

        /* ─── Top Navbar ─── */
        .top-navbar {
            position: sticky;
            top: 0;
            z-index: 900;
            background: rgba(247,248,250,.88);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--sb-border);
            padding: 0 28px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .top-navbar .page-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--ink-900);
            margin: 0;
            letter-spacing: -.2px;
        }

        /* Mobile hamburger */
        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            padding: 6px;
            cursor: pointer;
            color: var(--ink-700);
            border-radius: var(--r-sm);
            transition: background .15s;
        }
        .mobile-toggle:hover { background: var(--ink-100); }

        /* User pill */
        .user-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 5px 12px 5px 6px;
            border-radius: 40px;
            border: 1px solid var(--sb-border);
            background: var(--card-bg);
            cursor: pointer;
            transition: box-shadow .15s, border-color .15s;
            font-size: 13px;
            color: var(--ink-700);
            font-family: var(--font);
        }
        .user-pill:hover {
            box-shadow: var(--shadow-sm);
            border-color: var(--ink-300);
        }
        .user-avatar {
            width: 28px; height: 28px;
            border-radius: 50%;
            background: var(--accent-light);
            color: var(--accent);
            font-size: 11px;
            font-weight: 600;
            display: flex; align-items: center; justify-content: center;
        }
        .user-pill .caret {
            color: var(--sb-muted);
            font-size: 10px;
            margin-left: 2px;
        }

        /* Dropdown override */
        .dropdown-menu {
            border: 1px solid var(--sb-border);
            box-shadow: var(--shadow-md);
            border-radius: var(--r-lg);
            padding: 6px;
            font-family: var(--font);
            font-size: 13.5px;
            min-width: 200px;
        }
        .dropdown-item {
            border-radius: var(--r-sm);
            padding: 7px 12px;
            color: var(--ink-700);
            display: flex; align-items: center; gap: 8px;
            transition: background .12s;
        }
        .dropdown-item:hover { background: var(--ink-50); color: var(--ink-900); }
        .dropdown-item.text-danger { color: var(--danger) !important; }
        .dropdown-item.text-danger:hover { background: var(--danger-light); }
        .dropdown-divider { border-color: var(--sb-border); margin: 4px 0; }
        .dropdown-header {
            padding: 8px 12px 4px;
            font-size: 12px;
            color: var(--sb-muted);
        }

        /* ─── Content Area ─── */
        .content-area {
            padding: 28px;
            flex: 1;
        }

        /* ─── Page Header ─── */
        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 24px;
        }
        .page-header-title { font-size: 20px; font-weight: 600; color: var(--ink-900); margin: 0; letter-spacing: -.3px; }
        .page-header-sub { font-size: 13px; color: var(--sb-muted); margin: 2px 0 0; }

        /* ─── Cards ─── */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--sb-border);
            border-radius: var(--r-lg);
            box-shadow: var(--shadow-xs);
            margin-bottom: 20px;
        }
        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--sb-border);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }
        .card-header-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--ink-900);
            margin: 0;
        }
        .card-body { padding: 20px; }

        /* Stat cards */
        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--sb-border);
            border-radius: var(--r-lg);
            padding: 20px;
            box-shadow: var(--shadow-xs);
        }
        .stat-label {
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--sb-muted);
            margin: 0 0 8px;
        }
        .stat-value {
            font-size: 28px;
            font-weight: 600;
            color: var(--ink-900);
            letter-spacing: -.5px;
            line-height: 1;
            margin: 0;
        }
        .stat-delta {
            font-size: 12px;
            margin-top: 6px;
            display: flex; align-items: center; gap: 4px;
        }
        .stat-delta.up { color: var(--success); }
        .stat-delta.down { color: var(--danger); }
        .stat-icon {
            width: 40px; height: 40px;
            border-radius: var(--r-md);
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        /* ─── Tables ─── */
        .table-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 0 0 var(--r-lg) var(--r-lg);
        }
        .table {
            font-size: 13.5px;
            margin: 0;
            color: var(--ink-700);
        }
        .table thead th {
            background: var(--ink-50);
            border-bottom: 1px solid var(--sb-border);
            border-top: none;
            padding: 10px 16px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--sb-muted);
            white-space: nowrap;
        }
        .table tbody td {
            padding: 12px 16px;
            border-color: var(--sb-border);
            vertical-align: middle;
            color: var(--ink-700);
        }
        .table tbody tr { transition: background .1s; }
        .table-hover tbody tr:hover { background: var(--ink-50); }
        .table tbody tr:last-child td { border-bottom: none; }

        /* ─── Badges ─── */
        .badge {
            font-family: var(--font);
            font-weight: 500;
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 20px;
            letter-spacing: .02em;
        }
        .badge-success { background: var(--success-light); color: var(--success); }
        .badge-warning { background: var(--warning-light); color: var(--warning); }
        .badge-danger  { background: var(--danger-light);  color: var(--danger);  }
        .badge-primary { background: var(--accent-light);  color: var(--accent);  }
        .badge-neutral { background: var(--ink-100);       color: var(--ink-500); }

        /* ─── Buttons ─── */
        .btn {
            font-family: var(--font);
            font-size: 13.5px;
            font-weight: 500;
            border-radius: var(--r-md);
            padding: 7px 14px;
            transition: all .15s;
            display: inline-flex; align-items: center; gap: 6px;
            line-height: 1.4;
            cursor: pointer;
            white-space: nowrap;
        }
        .btn:focus { box-shadow: 0 0 0 3px rgba(37,99,235,.18); outline: none; }

        .btn-primary {
            background: var(--accent);
            border: 1px solid var(--accent);
            color: #fff;
        }
        .btn-primary:hover { background: var(--accent-dark); border-color: var(--accent-dark); color: #fff; }

        .btn-outline {
            background: var(--card-bg);
            border: 1px solid var(--sb-border);
            color: var(--ink-700);
        }
        .btn-outline:hover { background: var(--ink-50); border-color: var(--ink-300); color: var(--ink-900); }

        .btn-danger {
            background: var(--danger);
            border: 1px solid var(--danger);
            color: #fff;
        }
        .btn-danger:hover { background: #b91c1c; border-color: #b91c1c; color: #fff; }

        .btn-success {
            background: var(--success);
            border: 1px solid var(--success);
            color: #fff;
        }
        .btn-success:hover { background: #15803d; border-color: #15803d; color: #fff; }

        .btn-sm {
            font-size: 12px;
            padding: 5px 10px;
            border-radius: var(--r-sm);
        }
        .btn-lg {
            font-size: 15px;
            padding: 10px 20px;
        }
        .btn-icon {
            width: 34px; height: 34px;
            padding: 0;
            justify-content: center;
        }
        .btn-icon.btn-sm { width: 28px; height: 28px; }

        /* Bootstrap overrides */
        .btn-outline-primary { background: var(--card-bg); border-color: var(--accent); color: var(--accent); }
        .btn-outline-primary:hover { background: var(--accent); color: #fff; }
        .btn-outline-secondary { background: var(--card-bg); border-color: var(--sb-border); color: var(--ink-700); }
        .btn-outline-secondary:hover { background: var(--ink-50); color: var(--ink-900); border-color: var(--ink-300); }
        .btn-outline-danger { background: var(--card-bg); border-color: var(--danger); color: var(--danger); }
        .btn-outline-danger:hover { background: var(--danger); color: #fff; }

        /* ─── Forms ─── */
        .form-label {
            font-size: 12.5px;
            font-weight: 500;
            color: var(--ink-700);
            margin-bottom: 5px;
        }
        .form-control, .form-select {
            font-family: var(--font);
            font-size: 13.5px;
            color: var(--ink-900);
            border: 1px solid #d1d5db;
            border-radius: var(--r-md);
            padding: 8px 12px;
            background: var(--card-bg);
            transition: border-color .15s, box-shadow .15s;
            height: auto;
        }
        .form-control::placeholder { color: var(--sb-muted); }
        .form-control:focus, .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(37,99,235,.12);
            outline: none;
        }
        .form-text { font-size: 11.5px; color: var(--sb-muted); margin-top: 4px; }
        .invalid-feedback { font-size: 11.5px; }

        .input-group .form-control:first-child { border-radius: var(--r-md) 0 0 var(--r-md); }
        .input-group .form-control:last-child  { border-radius: 0 var(--r-md) var(--r-md) 0; }
        .input-group-text {
            background: var(--ink-50);
            border: 1px solid #d1d5db;
            color: var(--sb-muted);
            font-size: 13px;
            font-family: var(--font);
        }

        /* ─── Alerts / Flash ─── */
        .alert {
            border-radius: var(--r-md);
            font-size: 13.5px;
            padding: 12px 16px;
            border-width: 1px;
            display: flex; align-items: flex-start; gap: 10px;
        }
        .alert-success { background: var(--success-light); border-color: #bbf7d0; color: #166534; }
        .alert-danger  { background: var(--danger-light);  border-color: #fecaca; color: #991b1b; }
        .alert-warning { background: var(--warning-light); border-color: #fde68a; color: #92400e; }
        .alert-info    { background: var(--accent-light);  border-color: var(--accent-mid); color: #1e40af; }
        .btn-close { opacity: .5; }

        /* ─── Pagination ─── */
        .pagination { gap: 4px; margin: 0; }
        .page-link {
            font-family: var(--font);
            font-size: 13px;
            font-weight: 500;
            padding: 6px 12px;
            border-radius: var(--r-md) !important;
            border: 1px solid var(--sb-border);
            color: var(--ink-700);
            background: var(--card-bg);
            transition: all .15s;
        }
        .page-link:hover { background: var(--ink-50); border-color: var(--ink-300); color: var(--ink-900); }
        .page-item.active .page-link {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
            box-shadow: none;
        }
        .page-item.disabled .page-link { color: var(--ink-300); background: var(--ink-50); }

        /* ─── Overlay ─── */
        .sidebar-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,.35);
            z-index: 999;
            backdrop-filter: blur(2px);
        }

        /* ─── Responsive ─── */
        @media (max-width: 991px) {
            .sidebar { transform: translateX(-100%); box-shadow: none; }
            .sidebar.open { transform: translateX(0); box-shadow: var(--shadow-md); }
            .sidebar-overlay.open { display: block; }
            .main-content { margin-left: 0; }
            .mobile-toggle { display: flex; align-items: center; justify-content: center; }
        }
        @media (max-width: 767px) {
            .content-area { padding: 16px; }
            .top-navbar { padding: 0 16px; }
        }
    </style>

    @stack('styles')
</head>
<body>

<!-- ── Overlay (mobile) ── -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- ══════════════════════════════════════════════
     SIDEBAR
══════════════════════════════════════════════ -->
<aside class="sidebar" id="sidebar">

    <!-- Brand -->
    <div class="sidebar-brand">
        <div class="brand-icon">
            <i class="fas fa-graduation-cap"></i>
        </div>
        <div class="brand-text">
            School ERP
            <small>Management System</small>
        </div>
    </div>

    <!-- Nav -->
    <nav class="sidebar-nav" id="sidebarNav">
        @php
            $user = auth()->check() ? auth()->user() : null;
            $role = $user ? ($user->roles->first()->name ?? 'student') : 'student';
            if ($role === 'student' && $user && $user->email === 'librarian@schoolerp.com') {
                $role = 'librarian';
            }
            $dashboardRoutes = [
                'principal'       => 'dashboard.principal',
                'admin'           => 'dashboard.admin',
                'teacher'         => 'teacher.dashboard',
                'class_teacher'   => 'teacher.dashboard',
                'subject_teacher' => 'teacher.dashboard',
                'hod_commerce'    => 'teacher.dashboard',
                'hod_science'     => 'teacher.dashboard',
                'hod_management'  => 'teacher.dashboard',
                'hod_arts'        => 'teacher.dashboard',
                'student'         => 'dashboard.student',
                'accountant'      => 'dashboard.accountant',
                'accounts_staff'  => 'dashboard.accounts_staff',
                'office'          => 'dashboard.office',
                'librarian'       => 'dashboard.librarian',
            ];
            $dashboardRoute = $dashboardRoutes[$role] ?? 'dashboard.student';
            $isTeacher = in_array($role, ['teacher','class_teacher','subject_teacher','hod_commerce','hod_science','hod_management','hod_arts']);
        @endphp

        <!-- Dashboard -->
        <span class="nav-label">Main</span>
        <ul class="nav flex-column mb-0">
            <li class="nav-item">
                @if($isTeacher)
                <a class="nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}" href="{{ route('teacher.dashboard') }}">
                @else
                <a class="nav-link {{ request()->routeIs('dashboard.*') ? 'active' : '' }}" href="{{ route($dashboardRoute) }}">
                @endif
                    <span class="nav-icon"><i class="fas fa-home"></i></span>
                    Dashboard
                </a>
            </li>
        </ul>

        {{-- ── ADMIN ── --}}
        @if($role === 'admin')

        <div class="sidebar-divider"></div>
        <span class="nav-label">Administration</span>
        <ul class="nav flex-column mb-0">

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard.students*','dashboard.teachers*','staff.*') ? '' : 'collapsed' }}"
                   data-bs-toggle="collapse" data-bs-target="#nav-system" href="#">
                    <span class="nav-icon"><i class="fas fa-shield-halved"></i></span>
                    System Admin
                    <i class="fas fa-chevron-down toggle-arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('dashboard.students*','dashboard.teachers*','staff.*') ? 'show' : '' }}" id="nav-system">
                    <ul class="sidebar-submenu">
                        <li><a class="{{ request()->routeIs('dashboard.students*') ? 'active' : '' }}" href="{{ route('dashboard.students.index') }}"><i class="fas fa-user-graduate fa-fw"></i> Students</a></li>
                        <li><a class="{{ request()->routeIs('dashboard.teachers*') ? 'active' : '' }}" href="{{ route('dashboard.teachers.index') }}"><i class="fas fa-chalkboard-user fa-fw"></i> Teachers</a></li>
                        <li><a class="{{ request()->routeIs('staff.*') ? 'active' : '' }}" href="{{ route('staff.index') }}"><i class="fas fa-users fa-fw"></i> Staff</a></li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('academic.*','fees.structures.*','web.departments.*') ? '' : 'collapsed' }}"
                   data-bs-toggle="collapse" data-bs-target="#nav-config" href="#">
                    <span class="nav-icon"><i class="fas fa-sliders"></i></span>
                    Configuration
                    <i class="fas fa-chevron-down toggle-arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('academic.*','fees.structures.*','web.departments.*') ? 'show' : '' }}" id="nav-config">
                    <ul class="sidebar-submenu">
                        <li><a class="{{ request()->routeIs('web.departments.*') ? 'active' : '' }}" href="{{ route('web.departments.index') }}"><i class="fas fa-building fa-fw"></i> Departments</a></li>
                        <li><a class="{{ request()->routeIs('academic.programs.*') ? 'active' : '' }}" href="{{ route('academic.programs.index') }}"><i class="fas fa-graduation-cap fa-fw"></i> Programs</a></li>
                        <li><a class="{{ request()->routeIs('academic.subjects.*') ? 'active' : '' }}" href="{{ route('academic.subjects.index') }}"><i class="fas fa-book-open fa-fw"></i> Subjects</a></li>
                        <li><a class="{{ request()->routeIs('academic.divisions.*') ? 'active' : '' }}" href="{{ route('academic.divisions.index') }}"><i class="fas fa-table-cells fa-fw"></i> Divisions</a></li>
                        <li><a class="{{ request()->routeIs('academic.sessions.*') ? 'active' : '' }}" href="{{ route('academic.sessions.index') }}"><i class="fas fa-calendar-days fa-fw"></i> Sessions</a></li>
                        <li><a class="{{ request()->routeIs('academic.rules.*') ? 'active' : '' }}" href="{{ route('academic.rules.index') }}"><i class="fas fa-gavel fa-fw"></i> Academic Rules</a></li>
                        <li><a class="{{ request()->routeIs('academic.promotions.*') ? 'active' : '' }}" href="{{ route('academic.promotions.index') }}"><i class="fas fa-arrow-up-right-from-square fa-fw"></i> Promotions</a></li>
                        <li><a class="{{ request()->routeIs('fees.structures.*') ? 'active' : '' }}" href="{{ route('fees.structures.index') }}"><i class="fas fa-gear fa-fw"></i> Fee Structures</a></li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('examinations.*','library.*','principal.results') ? '' : 'collapsed' }}"
                   data-bs-toggle="collapse" data-bs-target="#nav-ops" href="#">
                    <span class="nav-icon"><i class="fas fa-clipboard-list"></i></span>
                    Operations
                    <i class="fas fa-chevron-down toggle-arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('examinations.*','library.*','principal.results') ? 'show' : '' }}" id="nav-ops">
                    <ul class="sidebar-submenu">
                        <li><a class="{{ request()->routeIs('examinations.*') ? 'active' : '' }}" href="{{ route('examinations.index') }}"><i class="fas fa-pencil fa-fw"></i> Examinations</a></li>
                        <li><a class="{{ request()->routeIs('principal.results') ? 'active' : '' }}" href="{{ route('principal.results') }}"><i class="fas fa-chart-bar fa-fw"></i> Results</a></li>
                        <li><a class="{{ request()->routeIs('library.books.*') ? 'active' : '' }}" href="{{ route('library.books.index') }}"><i class="fas fa-book fa-fw"></i> Library</a></li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('academic.timetable.*','academic.attendance.*','academic.holidays.*') ? '' : 'collapsed' }}"
                   data-bs-toggle="collapse" data-bs-target="#nav-tt" href="#">
                    <span class="nav-icon"><i class="fas fa-calendar-week"></i></span>
                    Timetable &amp; Attendance
                    <i class="fas fa-chevron-down toggle-arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('academic.timetable.*','academic.attendance.*','academic.holidays.*') ? 'show' : '' }}" id="nav-tt">
                    <ul class="sidebar-submenu">
                        <li><a class="{{ request()->routeIs('academic.timetable.*') ? 'active' : '' }}" href="{{ route('academic.timetable.grid') }}"><i class="fas fa-table fa-fw"></i> Timetable</a></li>
                        <li><a class="{{ request()->routeIs('academic.attendance.*') ? 'active' : '' }}" href="{{ route('academic.attendance.index') }}"><i class="fas fa-clipboard-check fa-fw"></i> Attendance</a></li>
                        <li><a class="{{ request()->routeIs('academic.holidays.*') ? 'active' : '' }}" href="{{ route('academic.holidays.index') }}"><i class="fas fa-calendar-xmark fa-fw"></i> Holidays</a></li>
                    </ul>
                </div>
            </li>
        </ul>
        @endif

        {{-- ── PRINCIPAL / OFFICE ── --}}
        @if(in_array($role, ['principal', 'office']))

        <div class="sidebar-divider"></div>
        <span class="nav-label">Management</span>
        <ul class="nav flex-column mb-0">

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admissions.*') ? 'active' : '' }}" href="{{ route('admissions.index') }}">
                    <span class="nav-icon"><i class="fas fa-user-plus"></i></span> Admissions
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard.students*','dashboard.teachers*') ? '' : 'collapsed' }}"
                   data-bs-toggle="collapse" data-bs-target="#nav-users" href="#">
                    <span class="nav-icon"><i class="fas fa-users"></i></span>
                    Users
                    <i class="fas fa-chevron-down toggle-arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('dashboard.students*','dashboard.teachers*') ? 'show' : '' }}" id="nav-users">
                    <ul class="sidebar-submenu">
                        <li><a class="{{ request()->routeIs('dashboard.students*') ? 'active' : '' }}" href="{{ route('dashboard.students.index') }}"><i class="fas fa-user-graduate fa-fw"></i> Students</a></li>
                        <li><a class="{{ request()->routeIs('dashboard.teachers*') ? 'active' : '' }}" href="{{ route('dashboard.teachers.index') }}"><i class="fas fa-chalkboard-user fa-fw"></i> Teachers</a></li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('academic.*') ? '' : 'collapsed' }}"
                   data-bs-toggle="collapse" data-bs-target="#nav-acad" href="#">
                    <span class="nav-icon"><i class="fas fa-book"></i></span>
                    Academic
                    <i class="fas fa-chevron-down toggle-arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('academic.*') ? 'show' : '' }}" id="nav-acad">
                    <ul class="sidebar-submenu">
                        <li><a class="{{ request()->routeIs('web.departments.*') ? 'active' : '' }}" href="{{ route('web.departments.index') }}"><i class="fas fa-building fa-fw"></i> Departments</a></li>
                        <li><a class="{{ request()->routeIs('academic.programs.*') ? 'active' : '' }}" href="{{ route('academic.programs.index') }}"><i class="fas fa-graduation-cap fa-fw"></i> Programs</a></li>
                        <li><a class="{{ request()->routeIs('academic.subjects.*') ? 'active' : '' }}" href="{{ route('academic.subjects.index') }}"><i class="fas fa-book-open fa-fw"></i> Subjects</a></li>
                        <li><a class="{{ request()->routeIs('academic.divisions.*') ? 'active' : '' }}" href="{{ route('academic.divisions.index') }}"><i class="fas fa-table-cells fa-fw"></i> Divisions</a></li>
                        <li><a class="{{ request()->routeIs('academic.sessions.*') ? 'active' : '' }}" href="{{ route('academic.sessions.index') }}"><i class="fas fa-calendar-days fa-fw"></i> Sessions</a></li>
                        <li><a class="{{ request()->routeIs('academic.rules.*') ? 'active' : '' }}" href="{{ route('academic.rules.index') }}"><i class="fas fa-gavel fa-fw"></i> Rules</a></li>
                        <li><a class="{{ request()->routeIs('academic.promotions.*') ? 'active' : '' }}" href="{{ route('academic.promotions.index') }}"><i class="fas fa-arrow-up fa-fw"></i> Promotions</a></li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('academic.timetable.*','academic.attendance.*','academic.holidays.*') ? '' : 'collapsed' }}"
                   data-bs-toggle="collapse" data-bs-target="#nav-ttb" href="#">
                    <span class="nav-icon"><i class="fas fa-calendar-week"></i></span>
                    Timetable &amp; Attendance
                    <i class="fas fa-chevron-down toggle-arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('academic.timetable.*','academic.attendance.*','academic.holidays.*') ? 'show' : '' }}" id="nav-ttb">
                    <ul class="sidebar-submenu">
                        <li><a class="{{ request()->routeIs('academic.timetable.*') ? 'active' : '' }}" href="{{ route('academic.timetable.index') }}"><i class="fas fa-table fa-fw"></i> Timetable</a></li>
                        <li><a class="{{ request()->routeIs('academic.attendance.*') ? 'active' : '' }}" href="{{ route('academic.attendance.index') }}"><i class="fas fa-clipboard-check fa-fw"></i> Attendance</a></li>
                        <li><a class="{{ request()->routeIs('academic.holidays.*') ? 'active' : '' }}" href="{{ route('academic.holidays.index') }}"><i class="fas fa-calendar-xmark fa-fw"></i> Holidays</a></li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('fees.*') ? '' : 'collapsed' }}"
                   data-bs-toggle="collapse" data-bs-target="#nav-fees" href="#">
                    <span class="nav-icon"><i class="fas fa-credit-card"></i></span>
                    Fees
                    <i class="fas fa-chevron-down toggle-arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('fees.*') ? 'show' : '' }}" id="nav-fees">
                    <ul class="sidebar-submenu">
                        <li><a class="{{ request()->routeIs('fees.structures.*') ? 'active' : '' }}" href="{{ route('fees.structures.index') }}"><i class="fas fa-gear fa-fw"></i> Structures</a></li>
                        <li><a class="{{ request()->routeIs('fees.assignments.*') ? 'active' : '' }}" href="{{ route('fees.assignments.index') }}"><i class="fas fa-user-tag fa-fw"></i> Assign Fees</a></li>
                        <li><a class="{{ request()->routeIs('fees.payments.*') ? 'active' : '' }}" href="{{ route('fees.payments.index') }}"><i class="fas fa-coins fa-fw"></i> Payments</a></li>
                        <li><a class="{{ request()->routeIs('fees.outstanding.*') ? 'active' : '' }}" href="{{ route('fees.outstanding.index') }}"><i class="fas fa-triangle-exclamation fa-fw"></i> Outstanding</a></li>
                        <li><a class="{{ request()->routeIs('fees.scholarships.*') ? 'active' : '' }}" href="{{ route('fees.scholarships.index') }}"><i class="fas fa-award fa-fw"></i> Scholarships</a></li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('examinations.*') ? 'active' : '' }}" href="{{ route('examinations.index') }}">
                    <span class="nav-icon"><i class="fas fa-clipboard-check"></i></span> Examinations
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('principal.results') ? 'active' : '' }}" href="{{ route('principal.results') }}">
                    <span class="nav-icon"><i class="fas fa-chart-bar"></i></span> Results
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('library.*') ? '' : 'collapsed' }}"
                   data-bs-toggle="collapse" data-bs-target="#nav-lib" href="#">
                    <span class="nav-icon"><i class="fas fa-book-bookmark"></i></span>
                    Library
                    <i class="fas fa-chevron-down toggle-arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('library.*') ? 'show' : '' }}" id="nav-lib">
                    <ul class="sidebar-submenu">
                        <li><a class="{{ request()->routeIs('library.books.*') ? 'active' : '' }}" href="{{ route('library.books.index') }}"><i class="fas fa-book fa-fw"></i> Books</a></li>
                        <li><a class="{{ request()->routeIs('library.issues.*') ? 'active' : '' }}" href="{{ route('library.issues.index') }}"><i class="fas fa-arrow-right-arrow-left fa-fw"></i> Issue / Return</a></li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('staff.*') ? 'active' : '' }}" href="{{ route('staff.index') }}">
                    <span class="nav-icon"><i class="fas fa-id-badge"></i></span> Staff
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.index') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                    <span class="nav-icon"><i class="fas fa-chart-line"></i></span> Reports
                </a>
            </li>
        </ul>
        @endif

        {{-- ── STUDENT ── --}}
        @if($role === 'student')
        <div class="sidebar-divider"></div>
        <span class="nav-label">My Account</span>
        <ul class="nav flex-column mb-0">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('student.fees.*') ? 'active' : '' }}" href="{{ route('student.fees.index') }}">
                    <span class="nav-icon"><i class="fas fa-credit-card"></i></span> My Fees
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('academic.timetable.*','academic.attendance.*','academic.holidays.*') ? '' : 'collapsed' }}"
                   data-bs-toggle="collapse" data-bs-target="#nav-stu-tt" href="#">
                    <span class="nav-icon"><i class="fas fa-calendar-week"></i></span>
                    Schedule
                    <i class="fas fa-chevron-down toggle-arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('academic.timetable.*','academic.attendance.*','academic.holidays.*') ? 'show' : '' }}" id="nav-stu-tt">
                    <ul class="sidebar-submenu">
                        <li><a class="{{ request()->routeIs('academic.timetable.*') ? 'active' : '' }}" href="{{ route('academic.timetable.index') }}"><i class="fas fa-table fa-fw"></i> My Timetable</a></li>
                        <li><a class="{{ request()->routeIs('academic.attendance.*') ? 'active' : '' }}" href="{{ route('academic.attendance.index') }}"><i class="fas fa-clipboard-check fa-fw"></i> Attendance</a></li>
                        <li><a class="{{ request()->routeIs('academic.holidays.*') ? 'active' : '' }}" href="{{ route('academic.holidays.index') }}"><i class="fas fa-calendar-xmark fa-fw"></i> Holidays</a></li>
                    </ul>
                </div>
            </li>
        </ul>
        @endif

        {{-- ── TEACHER ── --}}
        @if($isTeacher)
        <div class="sidebar-divider"></div>
        <span class="nav-label">Teaching</span>
        <ul class="nav flex-column mb-0">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('teacher.profile*') ? 'active' : '' }}" href="{{ route('teacher.profile') }}">
                    <span class="nav-icon"><i class="fas fa-user-circle"></i></span> My Profile
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('teacher.divisions*') ? 'active' : '' }}" href="{{ route('teacher.divisions.index') }}">
                    <span class="nav-icon"><i class="fas fa-users-rectangle"></i></span> My Divisions
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('academic.timetable.*','academic.attendance.*','academic.holidays.*') ? '' : 'collapsed' }}"
                   data-bs-toggle="collapse" data-bs-target="#nav-tch-tt" href="#">
                    <span class="nav-icon"><i class="fas fa-calendar-week"></i></span>
                    Schedule
                    <i class="fas fa-chevron-down toggle-arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('academic.timetable.*','academic.attendance.*','academic.holidays.*') ? 'show' : '' }}" id="nav-tch-tt">
                    <ul class="sidebar-submenu">
                        <li><a class="{{ request()->routeIs('academic.timetable.*') ? 'active' : '' }}" href="{{ route('academic.timetable.teacher') }}"><i class="fas fa-table fa-fw"></i> My Timetable</a></li>
                        <li><a class="{{ request()->routeIs('academic.attendance.*') ? 'active' : '' }}" href="{{ route('academic.attendance.create') }}"><i class="fas fa-clipboard-check fa-fw"></i> Mark Attendance</a></li>
                        <li><a class="{{ request()->routeIs('academic.holidays.*') ? 'active' : '' }}" href="{{ route('academic.holidays.index') }}"><i class="fas fa-calendar-xmark fa-fw"></i> Holidays</a></li>
                    </ul>
                </div>
            </li>
        </ul>
        @endif

        {{-- ── LIBRARIAN ── --}}
        @if($role === 'librarian')
        <div class="sidebar-divider"></div>
        <span class="nav-label">Library</span>
        <ul class="nav flex-column mb-0">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('library.*') ? '' : 'collapsed' }}"
                   data-bs-toggle="collapse" data-bs-target="#nav-libr" href="#">
                    <span class="nav-icon"><i class="fas fa-book-bookmark"></i></span>
                    Books & Issues
                    <i class="fas fa-chevron-down toggle-arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('library.*') ? 'show' : '' }}" id="nav-libr">
                    <ul class="sidebar-submenu">
                        <li><a class="{{ request()->routeIs('library.books.index') ? 'active' : '' }}" href="{{ route('library.books.index') }}"><i class="fas fa-book fa-fw"></i> Books</a></li>
                        <li><a class="{{ request()->routeIs('library.issues.create') ? 'active' : '' }}" href="{{ route('library.issues.create') }}"><i class="fas fa-circle-plus fa-fw"></i> Issue Book</a></li>
                        <li><a class="{{ request()->routeIs('library.issues.index') ? 'active' : '' }}" href="{{ route('library.issues.index') }}"><i class="fas fa-arrow-turn-left fa-fw"></i> Returns</a></li>
                        <li><a class="{{ request()->routeIs('library.students') ? 'active' : '' }}" href="{{ route('library.students') }}"><i class="fas fa-users fa-fw"></i> Students</a></li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('academic.holidays.*') ? 'active' : '' }}" href="{{ route('academic.holidays.index') }}">
                    <span class="nav-icon"><i class="fas fa-calendar-xmark"></i></span> Holidays
                </a>
            </li>
        </ul>
        @endif

        {{-- ── ACCOUNTANT ── --}}
        @if($role === 'accountant')
        <div class="sidebar-divider"></div>
        <span class="nav-label">Fee Management</span>
        <ul class="nav flex-column mb-0">

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('fees.structures.*') ? 'active' : '' }}" href="{{ route('fees.structures.index') }}">
                    <span class="nav-icon"><i class="fas fa-gear"></i></span> Fee Structures
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('fees.assignments.*') ? 'active' : '' }}" href="{{ route('fees.assignments.index') }}">
                    <span class="nav-icon"><i class="fas fa-user-tag"></i></span> Fee Assignment
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('fees.payments.*') ? 'active' : '' }}" href="{{ route('fees.payments.index') }}">
                    <span class="nav-icon"><i class="fas fa-coins"></i></span> Fee Collection
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('fees.outstanding.*') ? 'active' : '' }}" href="{{ route('fees.outstanding.index') }}">
                    <span class="nav-icon"><i class="fas fa-triangle-exclamation"></i></span> Outstanding Fees
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('fees.reports') ? 'active' : '' }}" href="{{ route('fees.reports') }}">
                    <span class="nav-icon"><i class="fas fa-chart-line"></i></span> Fee Reports
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('fees.scholarships.*') ? '' : 'collapsed' }}"
                   data-bs-toggle="collapse" data-bs-target="#nav-schol" href="#">
                    <span class="nav-icon"><i class="fas fa-award"></i></span>
                    Scholarships
                    <i class="fas fa-chevron-down toggle-arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('fees.scholarships.*','fees.scholarship-applications.*') ? 'show' : '' }}" id="nav-schol">
                    <ul class="sidebar-submenu">
                        <li><a class="{{ request()->routeIs('fees.scholarships.*') ? 'active' : '' }}" href="{{ route('fees.scholarships.index') }}"><i class="fas fa-award fa-fw"></i> Manage Scholarships</a></li>
                        <li><a class="{{ request()->routeIs('fees.scholarship-applications.*') ? 'active' : '' }}" href="{{ route('fees.scholarship-applications.index') }}"><i class="fas fa-file-circle-check fa-fw"></i> Applications</a></li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('academic.holidays.*') ? 'active' : '' }}" href="{{ route('academic.holidays.index') }}">
                    <span class="nav-icon"><i class="fas fa-calendar-xmark"></i></span> Holidays
                </a>
            </li>
        </ul>
        @endif

    </nav>

    <!-- Logout footer -->
    <div class="sidebar-footer">
        <ul class="nav flex-column mb-0">
            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="nav-link">
                        <span class="nav-icon"><i class="fas fa-arrow-right-from-bracket"></i></span>
                        Sign out
                    </button>
                </form>
            </li>
        </ul>
    </div>

</aside>

<!-- ══════════════════════════════════════════════
     MAIN CONTENT
══════════════════════════════════════════════ -->
<div class="main-content" id="mainContent">

    <!-- Top Navbar -->
    <header class="top-navbar">
        <div class="d-flex align-items-center gap-3">
            <button class="mobile-toggle" id="mobileToggle" onclick="toggleSidebar()">
                <i class="fas fa-bars" style="font-size:16px;"></i>
            </button>
            <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
        </div>

        <!-- User dropdown -->
        <div class="dropdown">
            <button class="user-pill dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"
                    style="background:none; border:1px solid var(--sb-border); font-family:var(--font);">
                <div class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}{{ strtoupper(substr(explode(' ', auth()->user()->name ?? 'User')[1] ?? '', 0, 1)) }}
                </div>
                <span class="d-none d-sm-inline">{{ auth()->user()->name ?? 'User' }}</span>
                <i class="fas fa-chevron-down caret"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <div class="dropdown-header">
                        <div style="font-weight:600; color:var(--ink-900);">{{ auth()->user()->name ?? 'User' }}</div>
                        <div>{{ ucfirst(str_replace('_', ' ', auth()->user()->roles->first()->name ?? 'User')) }}</div>
                    </div>
                </li>
                <li><hr class="dropdown-divider"></li>
                @php
                    // Profile routes by role (only set routes that actually exist)
                    $profileUrl = match($role) {
                        'admin'           => route('admin.profile'),
                        'accountant'      => route('accountant.profile'),
                        'teacher',
                        'class_teacher',
                        'subject_teacher',
                        'hod_commerce',
                        'hod_science',
                        'hod_management',
                        'hod_arts'        => route('teacher.profile'),
                        'student'         => route('student.profile'),
                        'librarian'       => route('librarian.profile'),
                        default           => '#',
                    };

                    // Settings routes by role
                    $settingsUrl = match($role) {
                        'admin'      => route('admin.settings'),
                        'teacher',
                        'class_teacher',
                        'subject_teacher' => route('teacher.settings'),
                        default      => '#',
                    };
                @endphp
                <li><a class="dropdown-item" href="{{ $profileUrl }}"><i class="fas fa-user fa-fw"></i> Profile</a></li>
                <li><a class="dropdown-item" href="{{ $settingsUrl }}"><i class="fas fa-gear fa-fw"></i> Settings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="fas fa-arrow-right-from-bracket fa-fw"></i> Sign out
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </header>

    <!-- Content Area -->
    <div class="content-area">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" id="flash-success">
                <i class="fas fa-circle-check"></i>
                <span>{{ session('success') }}</span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
            <script>
                setTimeout(function(){
                    var el = document.getElementById('flash-success');
                    if(el){ new bootstrap.Alert(el).close(); }
                }, 3500);
            </script>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-circle-exclamation"></i>
                <span>{{ session('error') }}</span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-circle-exclamation"></i>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
        document.getElementById('sidebarOverlay').classList.toggle('open');
    }
    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebarOverlay').classList.remove('open');
    }

    // Close on resize to desktop
    window.addEventListener('resize', function () {
        if (window.innerWidth > 991) closeSidebar();
    });

    // Fix Bootstrap collapse caret for sidebar
    document.querySelectorAll('.sidebar [data-bs-toggle="collapse"]').forEach(function(el) {
        var target = document.querySelector(el.getAttribute('data-bs-target'));
        if (target) {
            target.addEventListener('show.bs.collapse', function() { el.classList.remove('collapsed'); });
            target.addEventListener('hide.bs.collapse', function() { el.classList.add('collapsed'); });
        }
    });
</script>

@stack('scripts')
@yield('scripts')
</body>
</html>