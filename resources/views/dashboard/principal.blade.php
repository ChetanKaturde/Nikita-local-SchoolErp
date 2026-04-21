@extends('layouts.app')

@section('title', 'Principal Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<style>
    /* ── Welcome Banner ── */
    .welcome-banner {
        background: linear-gradient(120deg, #1d4ed8 0%, #2563eb 55%, #3b82f6 100%);
        border-radius: var(--r-lg);
        padding: 28px 32px;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    }
    .welcome-banner::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 220px; height: 220px;
        border-radius: 50%;
        background: rgba(255,255,255,.06);
        pointer-events: none;
    }
    .welcome-banner::after {
        content: '';
        position: absolute;
        bottom: -60px; right: 80px;
        width: 160px; height: 160px;
        border-radius: 50%;
        background: rgba(255,255,255,.04);
        pointer-events: none;
    }
    .welcome-avatar {
        width: 52px; height: 52px;
        border-radius: 50%;
        background: rgba(255,255,255,.18);
        border: 2px solid rgba(255,255,255,.3);
        display: flex; align-items: center; justify-content: center;
        font-size: 22px;
        color: #fff;
        flex-shrink: 0;
    }
    .welcome-name {
        font-size: 20px;
        font-weight: 600;
        color: #fff;
        margin: 0;
        letter-spacing: -.3px;
    }
    .welcome-sub {
        font-size: 13px;
        color: rgba(255,255,255,.72);
        margin: 3px 0 0;
    }
    .welcome-actions {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
    }
    .btn-banner {
        padding: 7px 16px;
        border-radius: var(--r-md);
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all .15s;
        font-family: var(--font);
    }
    .btn-banner-solid {
        background: rgba(255,255,255,.95);
        border: 1px solid transparent;
        color: var(--accent);
    }
    .btn-banner-solid:hover { background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,.12); color: var(--accent-dark); }
    .btn-banner-ghost {
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.25);
        color: #fff;
    }
    .btn-banner-ghost:hover { background: rgba(255,255,255,.2); }

    /* ── Stat Cards ── */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
        margin-bottom: 24px;
    }
    @media (max-width: 991px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 575px)  { .stats-grid { grid-template-columns: 1fr; } }

    .stat-card {
        background: var(--card-bg);
        border: 1px solid var(--sb-border);
        border-radius: var(--r-lg);
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: box-shadow .2s, transform .2s;
    }
    .stat-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }
    .stat-icon-wrap {
        width: 46px; height: 46px;
        border-radius: var(--r-md);
        display: flex; align-items: center; justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .si-blue   { background: #eff4ff; color: #2563eb; }
    .si-green  { background: #f0fdf4; color: #16a34a; }
    .si-red    { background: #fff1f1; color: #dc2626; }
    .si-amber  { background: #fffbeb; color: #d97706; }
    .si-purple { background: #f5f3ff; color: #7c3aed; }
    .si-teal   { background: #f0fdfa; color: #0d9488; }

    .stat-label {
        font-size: 11.5px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--ink-500);
        margin: 0 0 4px;
    }
    .stat-value {
        font-size: 24px;
        font-weight: 600;
        color: var(--ink-900);
        letter-spacing: -.5px;
        margin: 0;
        line-height: 1;
    }
    .stat-sub {
        font-size: 11.5px;
        color: var(--ink-300);
        margin: 3px 0 0;
    }

    /* ── Dashboard Card ── */
    .d-card {
        background: var(--card-bg);
        border: 1px solid var(--sb-border);
        border-radius: var(--r-lg);
        overflow: hidden;
        margin-bottom: 20px;
    }
    .d-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--sb-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        background: var(--ink-50);
    }
    .d-card-title {
        font-size: 13.5px;
        font-weight: 600;
        color: var(--ink-900);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .d-card-title i { color: var(--accent); font-size: 14px; }
    .d-card-body { padding: 20px; }
    .d-card-body-flush { padding: 0; }

    /* ── Attendance / Fee mini-cards ── */
    .mini-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 14px;
        border-radius: var(--r-md);
        background: var(--ink-50);
        margin-bottom: 10px;
    }
    .mini-row:last-child { margin-bottom: 0; }
    .mini-icon {
        width: 36px; height: 36px;
        border-radius: var(--r-sm);
        display: flex; align-items: center; justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }
    .mini-label { font-size: 11.5px; color: var(--ink-500); margin: 0; }
    .mini-value { font-size: 17px; font-weight: 600; color: var(--ink-900); margin: 0; line-height: 1; }

    /* ── Timetable Grid ── */
    .timetable-table th {
        background: var(--ink-50);
        font-size: 10.5px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--ink-500);
        padding: 9px 12px;
        border: 1px solid var(--sb-border);
        white-space: nowrap;
    }
    .timetable-table td {
        border: 1px solid var(--sb-border);
        padding: 8px 10px;
        vertical-align: top;
        min-width: 130px;
        font-size: 12.5px;
    }
    .timetable-table td.time-cell {
        background: var(--ink-50);
        font-weight: 600;
        color: var(--ink-700);
        font-size: 12px;
        white-space: nowrap;
        min-width: 72px;
    }
    .tt-class {
        background: var(--accent-light);
        border: 1px solid var(--accent-mid);
        border-radius: var(--r-sm);
        padding: 7px 9px;
    }
    .tt-code  { font-weight: 600; color: var(--accent); font-size: 11.5px; }
    .tt-name  { font-weight: 500; color: var(--ink-900); font-size: 12px; margin-top: 1px; }
    .tt-teacher { color: var(--ink-500); font-size: 11px; margin-top: 2px; }
    .tt-room  { color: var(--ink-500); font-size: 11px; }
    .tt-actions { display: flex; gap: 4px; margin-top: 6px; }
    .tt-btn {
        width: 24px; height: 24px;
        border-radius: 5px;
        border: 1px solid;
        display: flex; align-items: center; justify-content: center;
        font-size: 10px;
        cursor: pointer;
        transition: all .12s;
        background: #fff;
    }
    .tt-btn-edit  { border-color: var(--accent-mid); color: var(--accent); }
    .tt-btn-edit:hover  { background: var(--accent); color: #fff; }
    .tt-btn-del   { border-color: #fecaca; color: var(--danger); }
    .tt-btn-del:hover   { background: var(--danger); color: #fff; }
    .tt-empty { color: var(--ink-300); font-size: 12px; }

    /* ── Quick Actions Grid ── */
    .quick-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }
    .q-btn {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 11px 14px;
        border-radius: var(--r-md);
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        border: 1px solid var(--sb-border);
        background: var(--card-bg);
        color: var(--ink-700);
        transition: all .15s;
        font-family: var(--font);
    }
    .q-btn:hover { background: var(--ink-50); border-color: var(--ink-300); color: var(--ink-900); box-shadow: var(--shadow-xs); }
    .q-btn .q-icon {
        width: 30px; height: 30px;
        border-radius: 7px;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }
    .q-btn.full { grid-column: span 2; }

    /* ── Activity Timeline ── */
    .activity-list { display: flex; flex-direction: column; gap: 0; }
    .activity-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 13px 0;
        border-bottom: 1px solid var(--sb-border);
    }
    .activity-item:last-child { border-bottom: none; }
    .act-dot {
        width: 34px; height: 34px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
        margin-top: 1px;
    }
    .act-title { font-size: 13px; font-weight: 500; color: var(--ink-900); margin: 0; }
    .act-desc  { font-size: 12px; color: var(--ink-500); margin: 2px 0 0; }
    .act-time  { font-size: 11px; color: var(--ink-300); margin: 3px 0 0; }

    /* ── Credentials Table ── */
    .cred-table th {
        background: var(--ink-50);
        font-size: 10.5px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--ink-500);
        padding: 10px 16px;
        border-bottom: 1px solid var(--sb-border);
        border-top: none;
    }
    .cred-table td {
        padding: 11px 16px;
        border-bottom: 1px solid var(--sb-border);
        vertical-align: middle;
        font-size: 13px;
        color: var(--ink-700);
    }
    .cred-table tbody tr:last-child td { border-bottom: none; }
    .cred-table tbody tr:hover { background: var(--ink-50); }
    .user-chip {
        display: flex; align-items: center; gap: 9px;
    }
    .user-initials {
        width: 32px; height: 32px;
        border-radius: 50%;
        background: var(--accent-light);
        color: var(--accent);
        font-size: 11.5px;
        font-weight: 600;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    /* ── Badges ── */
    .tag {
        display: inline-flex;
        align-items: center;
        padding: 2px 9px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 500;
        white-space: nowrap;
    }
    .tag-blue   { background: var(--accent-light); color: var(--accent); }
    .tag-green  { background: #f0fdf4; color: #16a34a; }
    .tag-amber  { background: #fffbeb; color: #d97706; }
    .tag-gray   { background: var(--ink-100); color: var(--ink-500); }
    .tag-red    { background: #fff1f1; color: #dc2626; }

    /* ── Modal refinements ── */
    .modal-content {
        border: 1px solid var(--sb-border);
        border-radius: var(--r-lg);
        box-shadow: var(--shadow-md);
        font-family: var(--font);
    }
    .modal-header {
        border-bottom: 1px solid var(--sb-border);
        padding: 16px 20px;
        border-radius: var(--r-lg) var(--r-lg) 0 0;
    }
    .modal-header.accent { background: var(--accent); }
    .modal-header.accent .modal-title,
    .modal-header.accent .btn-close { color: #fff; filter: brightness(10); }
    .modal-header.warn-bg { background: #fffbeb; }
    .modal-header.danger-bg { background: #fff1f1; }
    .modal-title { font-size: 14.5px; font-weight: 600; color: var(--ink-900); }
    .modal-body  { padding: 20px; }
    .modal-footer { border-top: 1px solid var(--sb-border); padding: 14px 20px; gap: 8px; }

    /* ── Empty State ── */
    .empty-state { text-align: center; padding: 36px 20px; color: var(--ink-300); }
    .empty-state i { font-size: 36px; display: block; margin-bottom: 10px; }
    .empty-state p { font-size: 13px; margin: 0; }

    /* ── Password input group ── */
    .pw-group { display: flex; max-width: 200px; }
    .pw-group .form-control {
        border-radius: var(--r-sm) 0 0 var(--r-sm) !important;
        font-family: monospace;
        font-size: 12.5px;
        letter-spacing: 1.5px;
        background: var(--ink-50);
        flex: 1;
    }
    .pw-group .pw-btn {
        border: 1px solid #d1d5db;
        border-left: none;
        background: var(--card-bg);
        padding: 0 9px;
        cursor: pointer;
        font-size: 12px;
        color: var(--ink-500);
        transition: background .12s;
        display: flex; align-items: center;
    }
    .pw-group .pw-btn:last-child { border-radius: 0 var(--r-sm) var(--r-sm) 0; }
    .pw-group .pw-btn:hover { background: var(--ink-50); color: var(--ink-900); }

    /* ── Stagger animation ── */
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .stat-card { animation: fadeUp .35s ease both; }
    .stat-card:nth-child(1) { animation-delay: .04s; }
    .stat-card:nth-child(2) { animation-delay: .08s; }
    .stat-card:nth-child(3) { animation-delay: .12s; }
    .stat-card:nth-child(4) { animation-delay: .16s; }
    .stat-card:nth-child(5) { animation-delay: .20s; }
    .stat-card:nth-child(6) { animation-delay: .24s; }
</style>
@endpush

@section('content')

{{-- ══════════════════════════
     WELCOME BANNER
══════════════════════════ --}}
<div class="welcome-banner">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center gap-14" style="gap:14px;">
            <div class="welcome-avatar">
                <i class="fas fa-user-tie"></i>
            </div>
            <div>
                <p class="welcome-name">Welcome back, {{ auth()->user()->name }}!</p>
                <p class="welcome-sub">
                    <i class="fas fa-shield-halved me-1"></i>Principal &nbsp;·&nbsp;
                    <i class="fas fa-calendar me-1"></i>{{ now()->format('l, F j, Y') }}
                </p>
            </div>
        </div>
        <div class="welcome-actions">
            <button class="btn-banner btn-banner-solid" data-bs-toggle="modal" data-bs-target="#assignDivisionModal">
                <i class="fas fa-user-plus"></i> Assign Division
            </button>
            <button class="btn-banner btn-banner-ghost" id="refreshDashboard" title="Refresh">
                <i class="fas fa-arrows-rotate"></i>
            </button>
        </div>
    </div>
</div>

{{-- ══════════════════════════
     STATS GRID — ROW 1
══════════════════════════ --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon-wrap si-blue"><i class="fas fa-users"></i></div>
        <div>
            <p class="stat-label">Total Students</p>
            <p class="stat-value" data-count="{{ $totalStudents }}">{{ number_format($totalStudents) }}</p>
            <p class="stat-sub">Active enrolments</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-wrap si-green"><i class="fas fa-chalkboard-user"></i></div>
        <div>
            <p class="stat-label">Total Teachers</p>
            <p class="stat-value" data-count="{{ $totalTeachers }}">{{ number_format($totalTeachers) }}</p>
            <p class="stat-sub">Active faculty</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-wrap si-red"><i class="fas fa-building-columns"></i></div>
        <div>
            <p class="stat-label">Departments</p>
            <p class="stat-value" data-count="{{ $totalDepartments }}">{{ number_format($totalDepartments) }}</p>
            <p class="stat-sub">Departments</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-wrap si-amber"><i class="fas fa-graduation-cap"></i></div>
        <div>
            <p class="stat-label">Programs</p>
            <p class="stat-value" data-count="{{ $totalPrograms }}">{{ number_format($totalPrograms) }}</p>
            <p class="stat-sub">Academic programs</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-wrap si-purple"><i class="fas fa-book-open"></i></div>
        <div>
            <p class="stat-label">Subjects</p>
            <p class="stat-value" data-count="{{ $totalSubjects }}">{{ number_format($totalSubjects) }}</p>
            <p class="stat-sub">Available subjects</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-wrap si-teal"><i class="fas fa-clipboard-list"></i></div>
        <div>
            <p class="stat-label">Examinations</p>
            <p class="stat-value" data-count="{{ $totalExaminations }}">{{ number_format($totalExaminations) }}</p>
            <p class="stat-sub">Scheduled exams</p>
        </div>
    </div>
</div>

{{-- ══════════════════════════
     CREDENTIALS TABLE (admin only)
══════════════════════════ --}}
@if(auth()->user()->hasRole('admin') && isset($recentUsersWithPasswords) && $recentUsersWithPasswords->count() > 0)
<div class="d-card mb-4">
    <div class="d-card-header">
        <h2 class="d-card-title"><i class="fas fa-key"></i> Recently Generated Passwords</h2>
        <a href="{{ route('admin.credentials.index') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-table me-1"></i> View All
        </a>
    </div>
    <div class="d-card-body-flush">
        <div class="table-responsive">
            <table class="table mb-0 cred-table">
                <thead>
                    <tr>
                        <th>#</th><th>User</th><th>Email</th><th>Role</th>
                        <th>Password</th><th>Generated</th><th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentUsersWithPasswords as $index => $user)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <div class="user-chip">
                                <div class="user-initials">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                <span style="font-weight:500; color:var(--ink-900);">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td style="color:var(--ink-500);">{{ $user->email }}</td>
                        <td>
                            @if($user->roles->count() > 0)
                                <span class="tag tag-blue">{{ $user->roles->first()->name }}</span>
                            @else
                                <span class="tag tag-gray">User</span>
                            @endif
                        </td>
                        <td>
                            <div class="pw-group">
                                <input type="password" class="form-control" value="{{ $user->temp_password ?? 'Not Set' }}"
                                       id="dashboard-password-{{ $user->id }}" readonly>
                                <button class="pw-btn" type="button"
                                        onclick="toggleDashboardPassword('dashboard-password-{{ $user->id }}', 'dashboard-eye-{{ $user->id }}')">
                                    <i class="fas fa-eye" id="dashboard-eye-{{ $user->id }}"></i>
                                </button>
                                <button class="pw-btn" type="button"
                                        onclick="copyDashboardPassword('dashboard-password-{{ $user->id }}')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </td>
                        <td style="color:var(--ink-500); font-size:12px;">
                            {{ $user->password_generated_at ? $user->password_generated_at->diffForHumans() : '—' }}
                        </td>
                        <td class="text-end">
                            <button class="btn btn-outline btn-sm"
                                    onclick="viewDashboardPasswordModal('{{ $user->name }}','{{ $user->email }}','{{ $user->temp_password ?? 'Not Set' }}','{{ $user->roles->first()->name ?? 'User' }}')">
                                <i class="fas fa-eye me-1"></i> View
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- ══════════════════════════
     MAIN 2-COLUMN LAYOUT
══════════════════════════ --}}
<div class="row g-4">

    {{-- LEFT COLUMN --}}
    <div class="col-lg-8">

        {{-- Attendance + Fee side-by-side --}}
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="d-card h-100 mb-0">
                    <div class="d-card-header">
                        <h2 class="d-card-title"><i class="fas fa-calendar-check"></i> Today's Attendance</h2>
                        <span class="tag tag-green">{{ $attendancePercentage }}%</span>
                    </div>
                    <div class="d-card-body">
                        <div class="mini-row">
                            <div class="mini-icon" style="background:#f0fdf4; color:#16a34a;"><i class="fas fa-circle-check"></i></div>
                            <div>
                                <p class="mini-label">Present</p>
                                <p class="mini-value">{{ $attendanceToday->present ?? 0 }}</p>
                            </div>
                        </div>
                        <div class="mini-row">
                            <div class="mini-icon" style="background:#fff1f1; color:#dc2626;"><i class="fas fa-circle-xmark"></i></div>
                            <div>
                                <p class="mini-label">Absent</p>
                                <p class="mini-value">{{ $attendanceToday->absent ?? 0 }}</p>
                            </div>
                        </div>
                        <div class="mini-row">
                            <div class="mini-icon" style="background:var(--accent-light); color:var(--accent);"><i class="fas fa-users"></i></div>
                            <div>
                                <p class="mini-label">Total</p>
                                <p class="mini-value">{{ $attendanceToday->total ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-card h-100 mb-0">
                    <div class="d-card-header">
                        <h2 class="d-card-title"><i class="fas fa-coins"></i> Fee Collection</h2>
                        <span class="tag tag-blue">This Month</span>
                    </div>
                    <div class="d-card-body">
                        <div class="mini-row">
                            <div class="mini-icon" style="background:#f0fdf4; color:#16a34a;"><i class="fas fa-arrow-trend-down"></i></div>
                            <div>
                                <p class="mini-label">Collected</p>
                                <p class="mini-value" style="font-size:16px; color:#16a34a;">₹{{ number_format($feeCollection->total_collected ?? 0, 2) }}</p>
                                <p class="mini-label" style="margin:2px 0 0;">{{ $feeCollection->total_transactions ?? 0 }} transactions</p>
                            </div>
                        </div>
                        <div class="mini-row">
                            <div class="mini-icon" style="background:#fff1f1; color:#dc2626;"><i class="fas fa-triangle-exclamation"></i></div>
                            <div>
                                <p class="mini-label">Pending</p>
                                <p class="mini-value" style="font-size:16px; color:#dc2626;">₹{{ number_format($pendingFees, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Timetable --}}
        <div class="d-card mb-4">
            <div class="d-card-header">
                <h2 class="d-card-title"><i class="fas fa-calendar-week"></i> Timetable Management</h2>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTimetableModal">
                    <i class="fas fa-plus me-1"></i> Add Class
                </button>
            </div>
            <div class="d-card-body">
                {{-- Division Selector --}}
                <form method="GET" action="{{ route('dashboard.principal') }}" class="mb-4">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label">Select Division</label>
                            <select name="division_id" class="form-select" onchange="this.form.submit()">
                                <option value="">— Select Division —</option>
                                @foreach($divisions as $division)
                                    <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                        {{ $division->division_name }} – {{ $division->program->name ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @if($selectedDivision)
                        <div class="col-md-7">
                            <p class="mb-0" style="font-size:13px; color:var(--ink-500);">
                                <i class="fas fa-info-circle me-1" style="color:var(--accent);"></i>
                                Viewing <strong style="color:var(--ink-900);">{{ $selectedDivision->division_name }}</strong>
                                &nbsp;·&nbsp; {{ $selectedDivision->timetables->count() }} classes scheduled
                            </p>
                        </div>
                        @endif
                    </div>
                </form>

                @if($selectedDivision)
                    <div class="table-responsive">
                        <table class="table timetable-table mb-0" style="border-collapse:collapse;">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    @foreach(['monday','tuesday','wednesday','thursday','friday','saturday'] as $day)
                                        <th class="text-center">{{ ucfirst($day) }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @php $timeSlots = ['09:00','10:00','11:00','12:00','14:00','15:00','16:00']; @endphp
                                @foreach($timeSlots as $time)
                                <tr>
                                    <td class="time-cell">{{ $time }}</td>
                                    @foreach(['monday','tuesday','wednesday','thursday','friday','saturday'] as $day)
                                    <td>
                                        @php
                                            $dayClasses = $timetables[$day] ?? collect();
                                            $cls = $dayClasses->first(fn($c) => substr($c->start_time,0,5) === $time);
                                        @endphp
                                        @if($cls)
                                        <div class="tt-class">
                                            <div class="tt-code">{{ $cls->subject->code ?? 'N/A' }}</div>
                                            <div class="tt-name">{{ $cls->subject->name ?? 'N/A' }}</div>
                                            <div class="tt-teacher"><i class="fas fa-user fa-xs me-1"></i>{{ $cls->teacher->name ?? 'N/A' }}</div>
                                            <div class="tt-room"><i class="fas fa-location-dot fa-xs me-1"></i>{{ $cls->room_number ?? 'N/A' }}</div>
                                            <div class="tt-actions">
                                                <button class="tt-btn tt-btn-edit btn-edit-timetable"
                                                    data-id="{{ $cls->id }}"
                                                    data-division_id="{{ $cls->division_id }}"
                                                    data-subject_id="{{ $cls->subject_id }}"
                                                    data-teacher_id="{{ $cls->teacher_id }}"
                                                    data-day_of_week="{{ $cls->day_of_week }}"
                                                    data-date="{{ $cls->date ? $cls->date->format('Y-m-d') : '' }}"
                                                    data-start_time="{{ $cls->start_time }}"
                                                    data-end_time="{{ $cls->end_time }}"
                                                    data-room_number="{{ $cls->room_number }}"
                                                    data-academic_year_id="{{ $cls->academic_year_id }}"
                                                    title="Edit">
                                                    <i class="fas fa-pencil"></i>
                                                </button>
                                                <button class="tt-btn tt-btn-del btn-delete-timetable"
                                                    data-id="{{ $cls->id }}"
                                                    data-name="{{ $cls->subject->name ?? 'Class' }} on {{ $cls->day_of_week }}"
                                                    title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        @else
                                        <span class="tt-empty">—</span>
                                        @endif
                                    </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($selectedDivision->timetables->isEmpty())
                    <div class="empty-state mt-3">
                        <i class="fas fa-calendar-xmark"></i>
                        <p>No classes scheduled for this division</p>
                        <button class="btn btn-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#addTimetableModal">
                            <i class="fas fa-plus me-1"></i> Add First Class
                        </button>
                    </div>
                    @endif
                @else
                    <div class="empty-state">
                        <i class="fas fa-hand-pointer"></i>
                        <p>Select a division above to view its timetable</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="d-card">
            <div class="d-card-header">
                <h2 class="d-card-title"><i class="fas fa-bolt"></i> Quick Actions</h2>
            </div>
            <div class="d-card-body">
                <div class="quick-grid">
                    <a href="{{ route('dashboard.students.index') }}#create" class="q-btn">
                        <span class="q-icon" style="background:#eff4ff; color:#2563eb;"><i class="fas fa-user-plus"></i></span>
                        Add Student
                    </a>
                    <a href="{{ route('dashboard.teachers.index') }}#create" class="q-btn">
                        <span class="q-icon" style="background:#f0fdf4; color:#16a34a;"><i class="fas fa-chalkboard-user"></i></span>
                        Add Teacher
                    </a>
                    <a href="{{ route('web.departments.create') }}" class="q-btn">
                        <span class="q-icon" style="background:#f0fdfa; color:#0d9488;"><i class="fas fa-building"></i></span>
                        Add Department
                    </a>
                    <a href="{{ route('academic.programs.create') }}" class="q-btn">
                        <span class="q-icon" style="background:#fffbeb; color:#d97706;"><i class="fas fa-graduation-cap"></i></span>
                        Add Program
                    </a>
                    <a href="{{ route('academic.subjects.create') }}" class="q-btn">
                        <span class="q-icon" style="background:#f5f3ff; color:#7c3aed;"><i class="fas fa-book"></i></span>
                        Add Subject
                    </a>
                    <a href="{{ route('examinations.create') }}" class="q-btn">
                        <span class="q-icon" style="background:#fff1f1; color:#dc2626;"><i class="fas fa-clipboard"></i></span>
                        Create Exam
                    </a>
                    <a href="{{ route('principal.results') }}" class="q-btn full">
                        <span class="q-icon" style="background:var(--ink-100); color:var(--ink-700);"><i class="fas fa-chart-line"></i></span>
                        View Results
                    </a>
                </div>
            </div>
        </div>

    </div>

    {{-- RIGHT COLUMN --}}
    <div class="col-lg-4">

        {{-- Recent Activities --}}
        <div class="d-card">
            <div class="d-card-header">
                <h2 class="d-card-title"><i class="fas fa-clock-rotate-left"></i> Recent Activities</h2>
            </div>
            <div class="d-card-body" style="padding-top:8px; padding-bottom:8px;">
                <div class="activity-list">
                    @forelse($recentActivities as $activity)
                    <div class="activity-item">
                        <div class="act-dot" style="background:var(--accent-light); color:var(--accent);">
                            <i class="{{ $activity['icon'] ?? 'fas fa-circle-dot' }}"></i>
                        </div>
                        <div>
                            <p class="act-title">{{ $activity['title'] }}</p>
                            <p class="act-desc">{{ $activity['description'] }}</p>
                            <p class="act-time">{{ $activity['time'] }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>No recent activities</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ══════════════════════════
     MODALS
══════════════════════════ --}}

{{-- Assign Division Modal --}}
<div class="modal fade" id="assignDivisionModal" tabindex="-1" aria-labelledby="assignDivisionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header accent">
                <h5 class="modal-title" id="assignDivisionModalLabel">
                    <i class="fas fa-user-plus me-2"></i>Assign Division to Teacher
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('principal.assign-division') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Select Teacher <span class="text-danger">*</span></label>
                            <select class="form-select" name="teacher_id" required>
                                <option value="">Choose a teacher…</option>
                                @php $teachers = \App\Models\User::role('teacher')->orderBy('name')->get(); @endphp
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }} ({{ $teacher->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Select Division <span class="text-danger">*</span></label>
                            <select class="form-select" name="division_id" required>
                                <option value="">Choose a division…</option>
                                @php $divs = \App\Models\Academic\Division::with('program')->where('is_active', true)->get(); @endphp
                                @foreach($divs as $div)
                                    <option value="{{ $div->id }}">{{ $div->division_name }} – {{ $div->program->name ?? 'N/A' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Assignment Type <span class="text-danger">*</span></label>
                            <select class="form-select" name="assignment_type" required>
                                <option value="division">Class Teacher</option>
                                <option value="subject">Subject Teacher</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" name="is_active" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes <span style="color:var(--ink-300);">(optional)</span></label>
                            <textarea class="form-control" name="notes" rows="3" placeholder="Add any additional notes…"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-check me-1"></i> Assign Division</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Add Timetable Modal --}}
<div class="modal fade" id="addTimetableModal" tabindex="-1" aria-labelledby="addTimetableModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header accent">
                <h5 class="modal-title" id="addTimetableModalLabel">
                    <i class="fas fa-calendar-plus me-2"></i>Add Class to Timetable
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('principal.timetable.store') }}" method="POST" id="addTimetableForm">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Division <span class="text-danger">*</span></label>
                            <select class="form-select" id="timetable_division_id" name="division_id" required onchange="updateSelectedDivision()">
                                <option value="">Choose a division…</option>
                                @foreach($divisions as $division)
                                    <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                        {{ $division->division_name }} – {{ $division->program->name ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Subject <span class="text-danger">*</span></label>
                            <select class="form-select" name="subject_id" required>
                                <option value="">Choose a subject…</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->code }} – {{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teacher <span class="text-danger">*</span></label>
                            <select class="form-select" name="teacher_id" required>
                                <option value="">Choose a teacher…</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Day of Week <span class="text-danger">*</span></label>
                            <select class="form-select" id="day_of_week" name="day_of_week" required>
                                <option value="">Select a day…</option>
                                @foreach($days as $key => $day)
                                    <option value="{{ $key }}">{{ $day }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="timetable_date" name="date" required min="{{ date('Y-m-d') }}">
                            <div id="holidayWarning" class="alert alert-warning mt-2 d-none py-2 px-3" style="font-size:12.5px;">
                                <i class="fas fa-triangle-exclamation me-1"></i>
                                <span id="holidayWarningText"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                            <select class="form-select" name="academic_year_id" required>
                                <option value="">Choose year…</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}">{{ $year->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Start Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="start_time" name="start_time" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="end_time" name="end_time" required>
                            <div class="form-text">Must be after start time</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Room Number</label>
                            <input type="text" class="form-control" name="room_number" placeholder="e.g. Room 101">
                        </div>
                    </div>
                    <div id="conflictWarning" class="alert alert-danger mt-3 d-none py-2 px-3" style="font-size:12.5px;">
                        <i class="fas fa-triangle-exclamation me-1"></i><strong>Schedule Conflict Detected!</strong>
                        <ul id="conflictList" class="mb-0 mt-1 ps-3"></ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="addClassSubmitBtn">
                        <i class="fas fa-check me-1"></i> Add Class
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Timetable Modal --}}
<div class="modal fade" id="editTimetableModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header warn-bg">
                <h5 class="modal-title"><i class="fas fa-pencil me-2"></i>Edit Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editTimetableForm" method="POST">
                @csrf @method('PUT')
                <input type="hidden" name="id" id="editTimetableId">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Division <span class="text-danger">*</span></label>
                            <select class="form-select" id="editDivisionId" name="division_id" required>
                                @foreach($divisions as $d)
                                    <option value="{{ $d->id }}">{{ $d->division_name }} – {{ $d->program->name ?? 'N/A' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Subject <span class="text-danger">*</span></label>
                            <select class="form-select" id="editSubjectId" name="subject_id" required>
                                @foreach($subjects as $s)
                                    <option value="{{ $s->id }}">{{ $s->code }} – {{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teacher <span class="text-danger">*</span></label>
                            <select class="form-select" id="editTeacherId" name="teacher_id" required>
                                @foreach($teachers as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Day of Week <span class="text-danger">*</span></label>
                            <select class="form-select" id="editDayOfWeek" name="day_of_week" required>
                                @foreach($days as $key => $day)
                                    <option value="{{ $key }}">{{ $day }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="editDate" name="date" required min="{{ date('Y-m-d') }}">
                            <div id="editHolidayWarning" class="alert alert-warning mt-2 d-none py-2 px-3" style="font-size:12.5px;">
                                <i class="fas fa-triangle-exclamation me-1"></i><span id="editHolidayWarningText"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                            <select class="form-select" id="editAcademicYearId" name="academic_year_id" required>
                                @foreach($academicYears as $y)
                                    <option value="{{ $y->id }}">{{ $y->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Start Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="editStartTime" name="start_time" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="editEndTime" name="end_time" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Room Number</label>
                            <input type="text" class="form-control" id="editRoomNumber" name="room_number" placeholder="e.g. Room 101">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="editClassSubmitBtn">
                        <i class="fas fa-check me-1"></i> Update Class
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteClassModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content">
            <div class="modal-header danger-bg">
                <h5 class="modal-title" style="color:#dc2626;"><i class="fas fa-triangle-exclamation me-2"></i>Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p style="font-size:14px; color:var(--ink-700); margin:0;">
                    Are you sure you want to remove <strong id="deleteClassName" style="color:var(--ink-900);"></strong> from the timetable?
                </p>
                <p style="font-size:12.5px; color:var(--ink-500); margin-top:8px;">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteClassForm" method="POST" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-1"></i> Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Credentials View Modal --}}
<div class="modal fade" id="dashboardPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content">
            <div class="modal-header accent">
                <h5 class="modal-title"><i class="fas fa-key me-2"></i>User Credentials</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <p class="form-label" style="margin-bottom:2px;">Name</p>
                    <p style="font-weight:500; color:var(--ink-900); margin:0;" id="dashModalUserName"></p>
                </div>
                <div class="mb-3">
                    <p class="form-label" style="margin-bottom:2px;">Email</p>
                    <p style="font-weight:500; color:var(--ink-900); margin:0;" id="dashModalUserEmail"></p>
                </div>
                <div class="mb-3">
                    <p class="form-label" style="margin-bottom:2px;">Role</p>
                    <p id="dashModalUserRole" style="margin:0;"></p>
                </div>
                <div class="mb-3">
                    <p class="form-label" style="margin-bottom:6px;">Password</p>
                    <div class="pw-group" style="max-width:100%;">
                        <input type="password" class="form-control" id="dashModalUserPassword" readonly>
                        <button class="pw-btn" type="button" onclick="toggleDashModalPassword()">
                            <i class="fas fa-eye" id="dashModalEyeIcon"></i>
                        </button>
                        <button class="pw-btn" type="button" onclick="copyDashModalPassword()">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                <div class="alert alert-info mb-0 py-2 px-3" style="font-size:12.5px; background:var(--accent-light); border-color:var(--accent-mid); color:var(--accent);">
                    <i class="fas fa-circle-info me-1"></i>Keep this password secure. Share only with the user.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Toast Container --}}
<div id="toastContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index:9999;"></div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Refresh ── */
    document.getElementById('refreshDashboard')?.addEventListener('click', () => location.reload());

    /* ── Stat counter animation ── */
    document.querySelectorAll('.stat-value[data-count]').forEach(el => {
        const end = parseInt(el.dataset.count);
        if (isNaN(end)) return;
        let start = 0, t0 = null, dur = 1200;
        function step(t) {
            if (!t0) t0 = t;
            const p = Math.min((t - t0) / dur, 1);
            const ease = 1 - Math.pow(1 - p, 4);
            el.textContent = Math.floor(start + (end - start) * ease).toLocaleString();
            if (p < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    });

    /* ── Date → Day auto-select in Add modal ── */
    const dateInput = document.getElementById('timetable_date');
    if (dateInput) {
        dateInput.addEventListener('change', function () {
            if (!this.value) return;
            const d = new Date(this.value + 'T00:00:00');
            const dayVal = d.toLocaleDateString('en-US', {weekday:'long'}).toLowerCase();
            const sel = document.getElementById('day_of_week');
            if (sel) sel.value = dayVal;
            checkHoliday(this.value, 'holidayWarning', 'holidayWarningText', 'addClassSubmitBtn');
        });
    }

    /* ── End-time validation ── */
    const endTime = document.getElementById('end_time');
    const startTime = document.getElementById('start_time');
    if (endTime && startTime) {
        endTime.addEventListener('change', function () {
            if (startTime.value && this.value <= startTime.value) {
                showToast('End time must be after start time', 'danger');
                this.value = '';
            }
        });
    }

    /* ── Add form submit guard ── */
    document.getElementById('addTimetableForm')?.addEventListener('submit', function (e) {
        if (startTime?.value && endTime?.value && endTime.value <= startTime.value) {
            e.preventDefault();
            showToast('End time must be after start time', 'danger');
        }
        if (!document.getElementById('holidayWarning')?.classList.contains('d-none')) {
            e.preventDefault();
            showToast('Cannot schedule a class on a holiday', 'danger');
        }
    });

    /* ── Delete buttons ── */
    document.querySelectorAll('.btn-delete-timetable').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault(); e.stopPropagation();
            document.getElementById('deleteClassName').textContent = this.dataset.name || 'this class';
            document.getElementById('deleteClassForm').action = '/dashboard/principal/timetable/delete/' + this.dataset.id;
            new bootstrap.Modal(document.getElementById('deleteClassModal')).show();
        });
    });

    /* ── Edit buttons ── */
    document.querySelectorAll('.btn-edit-timetable').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault(); e.stopPropagation();
            const d = this.dataset;
            document.getElementById('editTimetableId').value    = d.id;
            document.getElementById('editDivisionId').value     = d.division_id;
            document.getElementById('editSubjectId').value      = d.subject_id;
            document.getElementById('editTeacherId').value      = d.teacher_id;
            document.getElementById('editDayOfWeek').value      = d.day_of_week;
            document.getElementById('editDate').value           = d.date;
            document.getElementById('editStartTime').value      = d.start_time ? d.start_time.substring(0,5) : '';
            document.getElementById('editEndTime').value        = d.end_time   ? d.end_time.substring(0,5)   : '';
            document.getElementById('editRoomNumber').value     = d.room_number || '';
            document.getElementById('editAcademicYearId').value = d.academic_year_id;
            document.getElementById('editTimetableForm').action = '/dashboard/principal/timetable/update/' + d.id;
            new bootstrap.Modal(document.getElementById('editTimetableModal')).show();
        });
    });

    /* ── Auto-select division in Add modal ── */
    document.getElementById('addTimetableModal')?.addEventListener('show.bs.modal', updateSelectedDivision);
});

function updateSelectedDivision() {
    const divId = new URLSearchParams(window.location.search).get('division_id');
    if (divId) {
        const sel = document.getElementById('timetable_division_id');
        if (sel) sel.value = divId;
    }
}

function checkHoliday(date, warningId, textId, btnId) {
    fetch("{{ route('academic.timetable.ajax.check-holiday') }}?date=" + date)
        .then(r => r.json())
        .then(data => {
            const w = document.getElementById(warningId);
            const b = document.getElementById(btnId);
            if (data.is_holiday) {
                document.getElementById(textId).textContent = data.holiday_title || 'This date is a holiday';
                w?.classList.remove('d-none');
                if (b) b.disabled = true;
            } else {
                w?.classList.add('d-none');
                if (b) b.disabled = false;
            }
        })
        .catch(() => {});
}

/* ── Password helpers ── */
function toggleDashboardPassword(inputId, eyeId) {
    const inp = document.getElementById(inputId);
    const eye = document.getElementById(eyeId);
    inp.type = inp.type === 'password' ? 'text' : 'password';
    if (eye) {
        eye.classList.toggle('fa-eye');
        eye.classList.toggle('fa-eye-slash');
    }
}

function copyDashboardPassword(inputId) {
    const val = document.getElementById(inputId)?.value;
    if (!val) return;
    navigator.clipboard.writeText(val)
        .then(() => showToast('Password copied!', 'success'))
        .catch(() => showToast('Failed to copy', 'danger'));
}

function viewDashboardPasswordModal(name, email, password, role) {
    document.getElementById('dashModalUserName').textContent  = name;
    document.getElementById('dashModalUserEmail').textContent = email;
    document.getElementById('dashModalUserRole').innerHTML    = `<span class="tag tag-blue">${role}</span>`;
    document.getElementById('dashModalUserPassword').value    = password;
    document.getElementById('dashModalUserPassword').type     = 'password';
    document.getElementById('dashModalEyeIcon').className     = 'fas fa-eye';
    new bootstrap.Modal(document.getElementById('dashboardPasswordModal')).show();
}

function toggleDashModalPassword() {
    const inp = document.getElementById('dashModalUserPassword');
    const eye = document.getElementById('dashModalEyeIcon');
    inp.type = inp.type === 'password' ? 'text' : 'password';
    eye.classList.toggle('fa-eye');
    eye.classList.toggle('fa-eye-slash');
}

function copyDashModalPassword() {
    const val = document.getElementById('dashModalUserPassword')?.value;
    if (!val) return;
    navigator.clipboard.writeText(val)
        .then(() => showToast('Password copied!', 'success'))
        .catch(() => showToast('Failed to copy', 'danger'));
}

function showToast(message, type = 'info') {
    const container = document.getElementById('toastContainer');
    const colors = { success:'#f0fdf4', danger:'#fff1f1', info:'var(--accent-light)', warning:'#fffbeb' };
    const borders = { success:'#bbf7d0', danger:'#fecaca', info:'var(--accent-mid)', warning:'#fde68a' };
    const textCol = { success:'#16a34a', danger:'#dc2626', info:'var(--accent)', warning:'#d97706' };
    const div = document.createElement('div');
    div.style.cssText = `background:${colors[type]};border:1px solid ${borders[type]};color:${textCol[type]};
        padding:10px 16px;border-radius:8px;font-size:13px;font-family:var(--font);
        margin-top:6px;box-shadow:0 2px 8px rgba(0,0,0,.08);display:flex;align-items:center;gap:8px;`;
    div.innerHTML = `<i class="fas fa-${type==='success'?'circle-check':type==='danger'?'circle-xmark':'circle-info'}"></i>${message}`;
    container.appendChild(div);
    setTimeout(() => div.remove(), 3000);
}
</script>
@endpush