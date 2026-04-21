@extends('student.layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    /* ── WELCOME BANNER ── */
    .welcome-banner {
        background: linear-gradient(135deg, #1a1d28 0%, #232739 50%, #1a1d28 100%);
        border-radius: 18px;
        padding: 28px 32px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(108,99,255,0.15);
        box-shadow: 0 8px 32px rgba(0,0,0,0.12);
        margin-bottom: 28px;
    }

    .welcome-banner::before {
        content: '';
        position: absolute;
        top: -60px; right: -40px;
        width: 280px; height: 280px;
        background: radial-gradient(circle, rgba(108,99,255,0.18) 0%, transparent 65%);
        pointer-events: none;
    }

    .welcome-banner::after {
        content: '';
        position: absolute;
        bottom: -50px; left: 30%;
        width: 180px; height: 180px;
        background: radial-gradient(circle, rgba(34,211,160,0.1) 0%, transparent 65%);
        pointer-events: none;
    }

    .welcome-text h2 {
        font-size: 1.5rem;
        font-weight: 800;
        color: #fff;
        margin: 0 0 6px;
        line-height: 1.2;
    }

    .welcome-text p {
        color: #8b93a7;
        font-size: 0.85rem;
        margin: 0;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    .welcome-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: rgba(255,255,255,0.06);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 20px;
        padding: 3px 10px;
        font-size: 0.75rem;
        color: #d0d5e8;
    }

    .time-widget { text-align: right; flex-shrink: 0; }

    .time-widget .time-val {
        font-family: 'DM Mono', monospace;
        font-size: 2rem;
        font-weight: 500;
        color: #fff;
        line-height: 1;
        letter-spacing: -0.02em;
    }

    .time-widget .time-label {
        font-size: 0.72rem;
        color: #4a5068;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-top: 4px;
    }

    /* ── STAT CARDS ── */
    .stat-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 22px;
        display: flex;
        flex-direction: column;
        gap: 14px;
        box-shadow: var(--shadow-sm);
        transition: all 0.24s cubic-bezier(0.4,0,0.2,1);
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
    }

    .stat-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
    }

    .stat-icon {
        width: 46px; height: 46px;
        border-radius: 13px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }

    .stat-icon.purple { background: rgba(108,99,255,0.12); color: #6c63ff; }
    .stat-icon.pink   { background: rgba(236,72,153,0.12);  color: #ec4899; }
    .stat-icon.teal   { background: rgba(34,211,160,0.12);  color: #22d3a0; }
    .stat-icon.amber  { background: rgba(251,191,36,0.12);  color: #fbbf24; }

    .stat-trend {
        font-size: 0.72rem;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 20px;
    }

    .stat-trend.good { background: rgba(34,197,94,0.1);  color: #22c55e; }
    .stat-trend.warn { background: rgba(251,191,36,0.12); color: #d97706; }
    .stat-trend.bad  { background: rgba(239,68,68,0.1);   color: #ef4444; }
    .stat-trend.info { background: rgba(99,102,241,0.1);  color: #6366f1; }

    .stat-value {
        font-size: 2rem;
        font-weight: 800;
        color: var(--text-primary);
        line-height: 1;
        letter-spacing: -0.03em;
    }

    .stat-label {
        font-size: 0.8rem;
        color: var(--text-muted);
        font-weight: 500;
        margin-top: 3px;
    }

    .stat-bar {
        height: 4px;
        background: var(--bg);
        border-radius: 10px;
        overflow: hidden;
    }

    .stat-bar-fill {
        height: 100%;
        border-radius: 10px;
        transition: width 0.8s cubic-bezier(0.4,0,0.2,1);
    }

    /* ── SECTION TITLE ── */
    .section-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-title .title-icon {
        width: 30px; height: 30px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.9rem;
    }

    .view-all-link {
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--accent);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 4px;
        transition: gap 0.2s;
    }

    .view-all-link:hover { gap: 8px; color: var(--accent); }

    /* ── SCHEDULE TABLE ── */
    .schedule-card { border-radius: 16px; overflow: hidden; }

    .period-row td:first-child {
        font-family: 'DM Mono', monospace;
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .subject-chip {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.68rem;
        font-weight: 700;
        font-family: 'DM Mono', monospace;
        letter-spacing: 0.02em;
        background: rgba(108,99,255,0.1);
        color: #6c63ff;
    }

    /* ── NOTIFICATION ITEMS ── */
    .notif-item {
        padding: 12px 0;
        border-bottom: 1px solid #f0f2f8;
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }

    .notif-item:last-child { border-bottom: none; }

    .notif-dot-indicator {
        width: 8px; height: 8px;
        border-radius: 50%;
        background: var(--accent);
        margin-top: 6px;
        flex-shrink: 0;
        box-shadow: 0 0 6px rgba(108,99,255,0.5);
    }

    .notif-dot-indicator.read { background: var(--border); box-shadow: none; }

    .notif-msg {
        font-size: 0.82rem;
        color: var(--text-primary);
        font-weight: 500;
        line-height: 1.4;
    }

    .notif-time {
        font-size: 0.7rem;
        color: var(--text-muted);
        margin-top: 3px;
    }

    /* ── QUICK ACTIONS ── */
    .quick-action-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 12px;
    }

    .quick-action-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 18px 10px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 14px;
        text-decoration: none;
        color: var(--text-primary);
        font-size: 0.78rem;
        font-weight: 600;
        transition: all 0.22s cubic-bezier(0.4,0,0.2,1);
        box-shadow: var(--shadow-sm);
    }

    .quick-action-btn:hover {
        border-color: var(--accent);
        color: var(--accent);
        background: rgba(108,99,255,0.04);
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(108,99,255,0.12);
    }

    .qa-icon {
        width: 44px; height: 44px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem;
        transition: all 0.22s;
    }

    .quick-action-btn:hover .qa-icon { transform: scale(1.1); }

    .qa-purple { background: rgba(108,99,255,0.1); color: #6c63ff; }
    .qa-green  { background: rgba(34,197,94,0.1);  color: #22c55e; }
    .qa-sky    { background: rgba(14,165,233,0.1);  color: #0ea5e9; }
    .qa-amber  { background: rgba(251,191,36,0.1);  color: #f59e0b; }
    .qa-red    { background: rgba(239,68,68,0.1);   color: #ef4444; }
    .qa-slate  { background: rgba(100,116,139,0.1); color: #64748b; }

    /* ── EMPTY STATE ── */
    .empty-state { text-align: center; padding: 40px 20px; }

    .empty-state-icon {
        width: 64px; height: 64px;
        background: var(--bg);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.8rem;
        color: var(--text-muted);
        margin: 0 auto 16px;
    }

    .empty-state p     { font-size: 0.875rem; color: var(--text-muted); margin: 0; }
    .empty-state small { color: #b0b8cc; font-size: 0.78rem; }

    /* ── RESPONSIVE ── */
    @media (max-width: 991px) {
        .quick-action-grid { grid-template-columns: repeat(3, 1fr); }
    }

    @media (max-width: 576px) {
        .welcome-banner { flex-direction: column; align-items: flex-start; }
        .time-widget { text-align: left; }
        .quick-action-grid { grid-template-columns: repeat(3, 1fr); gap: 8px; }
        .stat-value { font-size: 1.6rem; }
    }
</style>
@endpush

@section('content')

<!-- Welcome Banner -->
<div class="welcome-banner">
    <div class="welcome-text">
        <h2>Hello, {{ $student->first_name }} 👋</h2>
        <p>
            <span class="welcome-pill"><i class="bi bi-building"></i> {{ $student->division->division_name ?? 'N/A' }}</span>
            <span class="welcome-pill"><i class="bi bi-person-badge"></i> {{ $student->roll_number ?? 'N/A' }}</span>
            <span class="welcome-pill"><i class="bi bi-calendar3"></i> {{ now()->format('D, d M Y') }}</span>
        </p>
    </div>
    <div class="time-widget">
        <div class="time-val" id="currentTime">{{ now()->format('h:i A') }}</div>
        <div class="time-label">Current Time</div>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <!-- Classes Today -->
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-top">
                <div class="stat-icon purple"><i class="bi bi-calendar-week-fill"></i></div>
                <span class="stat-trend info"><i class="bi bi-clock me-1"></i>Today</span>
            </div>
            <div>
                <div class="stat-value">{{ $todayClasses->count() }}</div>
                <div class="stat-label">Classes Today</div>
            </div>
            <div class="stat-bar">
                <div class="stat-bar-fill" style="width: {{ min(100, $todayClasses->count() * 14) }}%; background: #6c63ff;"></div>
            </div>
        </div>
    </div>

    <!-- Attendance % -->
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-top">
                <div class="stat-icon pink"><i class="bi bi-clipboard-pulse"></i></div>
                @if($attendanceSummary['percentage'] >= 75)
                    <span class="stat-trend good"><i class="bi bi-arrow-up me-1"></i>Good</span>
                @elseif($attendanceSummary['percentage'] >= 65)
                    <span class="stat-trend warn"><i class="bi bi-dash me-1"></i>Average</span>
                @else
                    <span class="stat-trend bad"><i class="bi bi-arrow-down me-1"></i>Low</span>
                @endif
            </div>
            <div>
                <div class="stat-value">{{ $attendanceSummary['percentage'] }}<small style="font-size:1rem;font-weight:600;">%</small></div>
                <div class="stat-label">Attendance This Month</div>
            </div>
            <div class="stat-bar">
                <div class="stat-bar-fill" style="width: {{ $attendanceSummary['percentage'] }}%; background: {{ $attendanceSummary['percentage'] >= 75 ? '#22c55e' : ($attendanceSummary['percentage'] >= 65 ? '#f59e0b' : '#ef4444') }};"></div>
            </div>
        </div>
    </div>

    <!-- Days Present -->
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-top">
                <div class="stat-icon teal"><i class="bi bi-check-circle-fill"></i></div>
                <span class="stat-trend info">/ {{ $attendanceSummary['total'] }} days</span>
            </div>
            <div>
                <div class="stat-value">{{ $attendanceSummary['present'] }}</div>
                <div class="stat-label">Days Present</div>
            </div>
            <div class="stat-bar">
                <div class="stat-bar-fill" style="width: {{ $attendanceSummary['total'] > 0 ? round(($attendanceSummary['present'] / $attendanceSummary['total']) * 100) : 0 }}%; background: #22d3a0;"></div>
            </div>
        </div>
    </div>

    <!-- Unread Notifications -->
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-top">
                <div class="stat-icon amber"><i class="bi bi-bell-fill"></i></div>
                @if($student->unreadNotificationsCount() > 0)
                    <span class="stat-trend bad">{{ $student->unreadNotificationsCount() }} new</span>
                @else
                    <span class="stat-trend good">All read</span>
                @endif
            </div>
            <div>
                <div class="stat-value">{{ $student->unreadNotificationsCount() }}</div>
                <div class="stat-label">Unread Notifications</div>
            </div>
            <div class="stat-bar">
                <div class="stat-bar-fill" style="width: {{ min(100, $student->unreadNotificationsCount() * 10) }}%; background: #fbbf24;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Schedule + Notifications -->
<div class="row g-3 mb-4">
    <!-- Today's Schedule -->
    <div class="col-12 col-lg-7">
        <div class="card schedule-card h-100">
            <div class="card-header">
                <div class="section-title">
                    <span class="title-icon" style="background: rgba(108,99,255,0.1); color: #6c63ff;">
                        <i class="bi bi-calendar-week-fill"></i>
                    </span>
                    Today's Schedule
                </div>
                <a href="{{ route('student.timetable') }}" class="view-all-link">
                    Full Timetable <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="card-body p-0">
                @if($todayClasses->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Subject</th>
                                    <th>Teacher</th>
                                    <th>Room</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($todayClasses as $class)
                                    <tr class="period-row">
                                        <td>{{ substr($class->start_time, 0, 5) }}–{{ substr($class->end_time, 0, 5) }}</td>
                                        <td>
                                            <div style="font-weight:600; font-size:0.875rem;">{{ $class->subject->name ?? 'N/A' }}</div>
                                            @if($class->subject->code ?? false)
                                                <span class="subject-chip">{{ $class->subject->code }}</span>
                                            @endif
                                        </td>
                                        <td style="font-size:0.83rem;">{{ $class->teacher->name ?? 'N/A' }}</td>
                                        <td>
                                            <span style="font-size:0.78rem; background:rgba(14,165,233,0.1); color:#0ea5e9; padding:3px 10px; border-radius:20px; font-weight:600;">
                                                {{ $class->room ?? 'TBA' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon"><i class="bi bi-calendar-x"></i></div>
                        <p>No classes today</p>
                        <small>Enjoy your free time ☕</small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Notifications Panel -->
    <div class="col-12 col-lg-5">
        <div class="card h-100">
            <div class="card-header">
                <div class="section-title">
                    <span class="title-icon" style="background: rgba(251,191,36,0.12); color: #f59e0b;">
                        <i class="bi bi-bell-fill"></i>
                    </span>
                    Notifications
                    @if($student->unreadNotificationsCount() > 0)
                        <span style="font-size:0.7rem; background:rgba(239,68,68,0.1); color:#ef4444; padding:2px 8px; border-radius:20px; font-weight:700;">
                            {{ $student->unreadNotificationsCount() }} new
                        </span>
                    @endif
                </div>
                <a href="{{ route('student.notifications') }}" class="view-all-link">
                    All <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="card-body" style="padding: 8px 20px;">
                @if($notifications->count() > 0)
                    @foreach($notifications as $notification)
                        <div class="notif-item">
                            <div class="notif-dot-indicator {{ $notification->is_read ? 'read' : '' }}"></div>
                            <div style="flex:1; min-width:0;">
                                <div class="notif-msg">{{ Str::limit($notification->message, 65) }}</div>
                                <div class="notif-time">{{ $notification->created_at->diffForHumans() }}</div>
                            </div>
                            @if(!$notification->is_read)
                                <span style="font-size:0.65rem; background:rgba(108,99,255,0.1); color:#6c63ff; padding:2px 8px; border-radius:20px; font-weight:700; flex-shrink:0; align-self:center;">New</span>
                            @endif
                        </div>
                    @endforeach
                    <a href="{{ route('student.notifications') }}" class="btn w-100 mt-3"
                       style="background:rgba(108,99,255,0.08); color:#6c63ff; border:1px solid rgba(108,99,255,0.2); border-radius:10px; font-size:0.82rem; font-weight:600; padding:9px;">
                        View All Notifications
                    </a>
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon"><i class="bi bi-bell-slash"></i></div>
                        <p>No new notifications</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card">
    <div class="card-header">
        <div class="section-title">
            <span class="title-icon" style="background: rgba(34,211,160,0.1); color: #22d3a0;">
                <i class="bi bi-lightning-charge-fill"></i>
            </span>
            Quick Actions
        </div>
    </div>
    <div class="card-body">
        <div class="quick-action-grid">
            <a href="{{ route('student.timetable') }}" class="quick-action-btn">
                <div class="qa-icon qa-purple"><i class="bi bi-calendar-week-fill"></i></div>
                Timetable
            </a>
            <a href="{{ route('student.attendance') }}" class="quick-action-btn">
                <div class="qa-icon qa-green"><i class="bi bi-calendar-check-fill"></i></div>
                Attendance
            </a>
            <a href="{{ route('student.profile') }}" class="quick-action-btn">
                <div class="qa-icon qa-sky"><i class="bi bi-person-fill"></i></div>
                Profile
            </a>
            <a href="{{ route('student.notifications') }}" class="quick-action-btn">
                <div class="qa-icon qa-amber"><i class="bi bi-bell-fill"></i></div>
                Notifications
            </a>
            <a href="{{ route('student.fees') }}" class="quick-action-btn">
                <div class="qa-icon qa-red"><i class="bi bi-receipt-cutoff"></i></div>
                Fees
            </a>
            <a href="{{ route('student.results') }}" class="quick-action-btn">
                <div class="qa-icon qa-slate"><i class="bi bi-bar-chart-line-fill"></i></div>
                Results
            </a>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function updateClock() {
        const el = document.getElementById('currentTime');
        if (!el) return;
        el.textContent = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>
@endpush