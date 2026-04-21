<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Student Dashboard') — School ERP</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 268px;
            --sidebar-bg: #0d0f14;
            --sidebar-surface: #161922;
            --sidebar-border: rgba(255,255,255,0.06);
            --sidebar-text: #8b93a7;
            --sidebar-text-hover: #e8eaf0;
            --accent: #6c63ff;
            --accent-2: #22d3a0;
            --accent-glow: rgba(108,99,255,0.25);
            --surface: #ffffff;
            --bg: #f0f2f8;
            --text-primary: #141824;
            --text-muted: #7a8499;
            --border: #e4e8f0;
            --radius: 14px;
            --shadow-sm: 0 1px 4px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.04);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.08), 0 12px 32px rgba(0,0,0,0.06);
            --transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg);
            color: var(--text-primary);
            overflow-x: hidden;
            min-height: 100vh;
        }

        .wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* ─── SIDEBAR ─────────────────────────────── */
        #sidebar {
            position: fixed;
            top: 0; left: 0; bottom: 0;
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            z-index: 1050;
            display: flex;
            flex-direction: column;
            transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        #sidebar::before {
            content: '';
            position: absolute;
            top: -80px; left: -60px;
            width: 260px; height: 260px;
            background: radial-gradient(circle, var(--accent-glow) 0%, transparent 70%);
            pointer-events: none;
        }

        .sidebar-brand {
            padding: 24px 22px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--sidebar-border);
            flex-shrink: 0;
        }

        .brand-icon {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, var(--accent), #a78bfa);
            border-radius: 11px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; color: #fff;
            box-shadow: 0 0 16px var(--accent-glow);
            flex-shrink: 0;
        }

        .brand-text .brand-name {
            font-size: 0.95rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.01em;
            line-height: 1.1;
        }

        .brand-text .brand-sub {
            font-size: 0.68rem;
            color: var(--sidebar-text);
            font-weight: 500;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .sidebar-scroll {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 10px 0 8px;
            scrollbar-width: none;
        }
        .sidebar-scroll::-webkit-scrollbar { display: none; }

        .nav-section-label {
            padding: 14px 20px 6px;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #4a5068;
        }

        #sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        #sidebar ul li a {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 10px 14px 10px 16px;
            margin: 1px 10px;
            border-radius: 10px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: var(--transition);
            position: relative;
        }

        #sidebar ul li a .nav-icon {
            width: 34px; height: 34px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 8px;
            font-size: 1rem;
            background: transparent;
            transition: var(--transition);
            flex-shrink: 0;
        }

        #sidebar ul li a:hover {
            color: var(--sidebar-text-hover);
            background: rgba(255,255,255,0.05);
        }

        #sidebar ul li a:hover .nav-icon {
            background: rgba(108,99,255,0.15);
            color: var(--accent);
        }

        #sidebar ul li a.active {
            color: #fff;
            background: linear-gradient(135deg, rgba(108,99,255,0.2) 0%, rgba(108,99,255,0.08) 100%);
        }

        #sidebar ul li a.active .nav-icon {
            background: var(--accent);
            color: #fff;
            box-shadow: 0 4px 12px var(--accent-glow);
        }

        #sidebar ul li a.active::before {
            content: '';
            position: absolute;
            left: 0; top: 50%;
            transform: translateY(-50%);
            width: 3px; height: 60%;
            background: var(--accent);
            border-radius: 0 3px 3px 0;
        }

        #sidebar ul li a .badge {
            margin-left: auto;
            font-size: 0.65rem;
            font-family: 'DM Mono', monospace;
            padding: 3px 8px;
            border-radius: 20px;
        }

        .sidebar-footer {
            padding: 14px 12px;
            border-top: 1px solid var(--sidebar-border);
            flex-shrink: 0;
        }

        .sidebar-user-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            background: rgba(255,255,255,0.04);
            border-radius: 11px;
            margin-bottom: 10px;
        }

        .sidebar-user-avatar {
            width: 34px; height: 34px;
            border-radius: 9px;
            background: linear-gradient(135deg, var(--accent), #a78bfa);
            color: #fff;
            font-size: 0.8rem;
            font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .sidebar-user-info small:first-child {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            color: #d0d5e8;
        }

        .sidebar-user-info small:last-child {
            font-size: 0.68rem;
            color: var(--sidebar-text);
        }

        .btn-logout {
            width: 100%;
            padding: 9px;
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.2);
            color: #f87171;
            border-radius: 10px;
            font-size: 0.82rem;
            font-weight: 600;
            transition: var(--transition);
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }

        .btn-logout:hover {
            background: rgba(239,68,68,0.2);
            color: #fca5a5;
            border-color: rgba(239,68,68,0.4);
        }

        /* ─── CONTENT AREA ────────────────────────── */
        #content {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: var(--transition);
        }

        /* ─── TOP NAVBAR ──────────────────────────── */
        .top-navbar {
            position: sticky;
            top: 0;
            z-index: 900;
            background: rgba(240, 242, 248, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            padding: 10px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-toggle-btn {
            width: 38px; height: 38px;
            border: none;
            background: var(--surface);
            border-radius: 10px;
            color: var(--text-muted);
            font-size: 1.1rem;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            flex-shrink: 0;
        }

        .sidebar-toggle-btn:hover {
            background: var(--accent);
            color: #fff;
            box-shadow: 0 4px 12px var(--accent-glow);
        }

        .navbar-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .navbar-icon-btn {
            width: 38px; height: 38px;
            border: none;
            background: var(--surface);
            border-radius: 10px;
            color: var(--text-muted);
            font-size: 1rem;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            text-decoration: none;
            position: relative;
        }

        .navbar-icon-btn:hover { color: var(--accent); }

        .notif-dot {
            position: absolute;
            top: 6px; right: 6px;
            width: 8px; height: 8px;
            background: #ef4444;
            border-radius: 50%;
            border: 2px solid var(--bg);
        }

        .user-dropdown-trigger {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 5px 10px 5px 5px;
            background: var(--surface);
            border-radius: 12px;
            border: none;
            cursor: pointer;
            box-shadow: var(--shadow-sm);
            text-decoration: none;
            transition: var(--transition);
        }

        .user-dropdown-trigger:hover { box-shadow: var(--shadow-md); }

        .user-dropdown-avatar {
            width: 30px; height: 30px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--accent), #a78bfa);
            color: #fff;
            font-size: 0.75rem;
            font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
        }

        .user-dropdown-avatar img {
            width: 100%; height: 100%;
            object-fit: cover;
        }

        .user-dropdown-info .name {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-primary);
            line-height: 1.2;
        }

        .user-dropdown-info .role {
            font-size: 0.68rem;
            color: var(--text-muted);
        }

        .dropdown-menu {
            border: 1px solid var(--border);
            box-shadow: var(--shadow-md);
            border-radius: var(--radius);
            padding: 6px;
            font-size: 0.875rem;
        }

        .dropdown-item {
            border-radius: 8px;
            padding: 8px 12px;
            color: var(--text-primary);
            font-weight: 500;
            transition: var(--transition);
            display: flex; align-items: center; gap: 8px;
        }

        .dropdown-item:hover { background: var(--bg); }

        /* ─── MAIN CONTENT ────────────────────────── */
        .main-content {
            padding: 24px;
            flex: 1;
        }

        /* ─── CARDS ───────────────────────────────── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }

        .card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border);
            padding: 16px 20px;
            font-weight: 700;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* ─── FLASH ALERTS ────────────────────────── */
        .alert {
            border-radius: var(--radius);
            border: none;
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* ─── TABLE ───────────────────────────────── */
        .table thead th {
            background: var(--bg);
            color: var(--text-muted);
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            border-bottom: 2px solid var(--border);
            padding: 11px 14px;
        }

        .table tbody td {
            padding: 12px 14px;
            vertical-align: middle;
            font-size: 0.875rem;
            border-bottom: 1px solid #f0f2f8;
        }

        .table-hover tbody tr:hover { background: #f8f9fd; }

        /* ─── FORM CONTROLS ───────────────────────── */
        .form-control, .form-select {
            border-radius: 9px;
            border-color: var(--border);
            font-size: 0.875rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(108,99,255,0.12);
        }

        /* ─── RESPONSIVE ──────────────────────────── */
        @media (max-width: 991px) {
            #sidebar {
                transform: translateX(calc(-1 * var(--sidebar-width)));
            }

            #sidebar.open {
                transform: translateX(0);
                box-shadow: 4px 0 40px rgba(0,0,0,0.35);
            }

            #content {
                margin-left: 0;
                width: 100%;
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.5);
                z-index: 1040;
                backdrop-filter: blur(2px);
            }

            .sidebar-overlay.show { display: block; }
        }

        @media (max-width: 576px) {
            .main-content { padding: 16px; }
            .top-navbar { padding: 10px 16px; }
            .user-dropdown-info { display: none; }
        }
    </style>

    @stack('styles')
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="wrapper">
    <!-- ── SIDEBAR ── -->
    <nav id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon"><i class="bi bi-mortarboard-fill"></i></div>
            <div class="brand-text">
                <div class="brand-name">School ERP</div>
                <div class="brand-sub">Student Portal</div>
            </div>
        </div>

        <div class="sidebar-scroll">
            <ul>
                <li>
                    <a href="{{ route('student.dashboard') }}" class="{{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="bi bi-grid-1x2-fill"></i></span>
                        Dashboard
                    </a>
                </li>
            </ul>

            <div class="nav-section-label">Academic</div>
            <ul>
                <li>
                    <a href="{{ route('student.timetable') }}" class="{{ request()->routeIs('student.timetable*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="bi bi-calendar-week"></i></span>
                        Timetable
                    </a>
                </li>
                <li>
                    <a href="{{ route('student.attendance') }}" class="{{ request()->routeIs('student.attendance*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="bi bi-calendar-check"></i></span>
                        Attendance
                    </a>
                </li>
                <li>
                    <a href="{{ route('student.results') }}" class="{{ request()->routeIs('student.results*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="bi bi-bar-chart-line-fill"></i></span>
                        Results
                    </a>
                </li>
                <li>
                    <a href="{{ route('student.library') }}" class="{{ request()->routeIs('student.library*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="bi bi-book-half"></i></span>
                        Library
                    </a>
                </li>
            </ul>

            <div class="nav-section-label">Payments</div>
            <ul>
                <li>
                    <a href="{{ route('student.fees') }}" class="{{ request()->routeIs('student.fees*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="bi bi-receipt"></i></span>
                        Fees
                        @if(isset($pendingFees) && $pendingFees > 0)
                            <span class="badge bg-warning text-dark">{{ $pendingFees }}</span>
                        @endif
                    </a>
                </li>
            </ul>

            <div class="nav-section-label">Personal</div>
            <ul>
                <li>
                    <a href="{{ route('student.profile') }}" class="{{ request()->routeIs('student.profile*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="bi bi-person-fill"></i></span>
                        My Profile
                    </a>
                </li>
                <li>
                    @php $authStudent = \Illuminate\Support\Facades\Auth::guard('student')->user(); @endphp
                    <a href="{{ route('student.notifications') }}" class="{{ request()->routeIs('student.notifications') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="bi bi-bell-fill"></i></span>
                        Notifications
                        @if($authStudent && $authStudent->unreadNotificationsCount() > 0)
                            <span class="badge bg-danger">{{ $authStudent->unreadNotificationsCount() }}</span>
                        @endif
                    </a>
                </li>
            </ul>
        </div>

        <div class="sidebar-footer">
            <div class="sidebar-user-card">
                <div class="sidebar-user-avatar">{{ substr($student->name ?? 'S', 0, 1) }}</div>
                <div class="sidebar-user-info">
                    <small>{{ $student->name ?? 'Student' }}</small>
                    <small>{{ $student->roll_number ?? 'Student Account' }}</small>
                </div>
            </div>
            <form action="{{ route('student.logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </div>
    </nav>

    <!-- ── PAGE CONTENT ── -->
    <div id="content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <button class="sidebar-toggle-btn" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>

            <nav aria-label="breadcrumb" class="d-none d-md-flex align-items-center">
                <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item text-muted">ERP</li>
                    <li class="breadcrumb-item active fw-600">@yield('title', 'Dashboard')</li>
                </ol>
            </nav>

            <div class="navbar-right">
                <a href="{{ route('student.notifications') }}" class="navbar-icon-btn">
                    <i class="bi bi-bell"></i>
                    @if($student->unreadNotificationsCount() > 0)
                        <span class="notif-dot"></span>
                    @endif
                </a>

                <div class="dropdown">
                    <a href="#" class="user-dropdown-trigger dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-dropdown-avatar">
                            @if($student->photo)
                                <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->name }}">
                            @else
                                {{ substr($student->name, 0, 1) }}
                            @endif
                        </div>
                        <div class="user-dropdown-info d-none d-md-block">
                            <div class="name">{{ $student->name }}</div>
                            <div class="role">{{ $student->roll_number ?? 'Student' }}</div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('student.profile') }}">
                            <i class="bi bi-person"></i> My Profile
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('student.profile.change-password') }}">
                            <i class="bi bi-shield-lock"></i> Change Password
                        </a></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form action="{{ route('student.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        <div class="main-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ session('success') }}</span>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <span>{{ session('error') }}</span>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');

    function isMobile() { return window.innerWidth < 992; }

    toggleBtn.addEventListener('click', () => {
        if (isMobile()) {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
        } else {
            // Desktop: collapse sidebar
            document.getElementById('content').style.marginLeft =
                sidebar.classList.toggle('d-none') ? '0' : 'var(--sidebar-width)';
        }
    });

    overlay.addEventListener('click', () => {
        sidebar.classList.remove('open');
        overlay.classList.remove('show');
    });
</script>

@stack('scripts')
</body>
</html>