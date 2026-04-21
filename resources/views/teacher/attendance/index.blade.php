@extends('layouts.teacher')

@section('title', 'Attendance Dashboard')
@section('page-title', 'Attendance')

@push('styles')
<style>
    .att-page { padding: 28px; max-width: 1400px; }

    /* ── Page hero ── */
    .page-hero {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 28px;
        flex-wrap: wrap;
    }

    .page-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #4f8ef7;
        background: rgba(79,142,247,0.08);
        border: 1px solid rgba(79,142,247,0.18);
        padding: 4px 10px;
        border-radius: 20px;
        margin-bottom: 10px;
    }

    .page-title   { font-size: 26px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px; margin: 0 0 4px; }
    .page-subtitle{ font-size: 13.5px; color: #64748b; font-weight: 500; }

    .hero-actions { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }

    .btn-outline-soft {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 18px;
        border-radius: 9px;
        border: 1.5px solid #e8eaf0;
        background: #fff;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        text-decoration: none;
        font-family: 'Plus Jakarta Sans', sans-serif;
        transition: background 0.18s, border-color 0.18s, color 0.18s;
        cursor: pointer;
    }

    .btn-outline-soft:hover { background: #f8fafc; border-color: #cbd5e1; color: #0f172a; }

    .btn-outline-soft.info { color: #0ea5e9; border-color: rgba(14,165,233,0.25); background: rgba(14,165,233,0.04); }
    .btn-outline-soft.info:hover { background: rgba(14,165,233,0.1); border-color: rgba(14,165,233,0.4); }

    /* ── Section header ── */
    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 18px;
        gap: 12px;
        flex-wrap: wrap;
    }

    .section-title {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 9px;
    }

    .section-title-icon {
        width: 32px; height: 32px;
        border-radius: 8px;
        background: rgba(79,142,247,0.1);
        color: #4f8ef7;
        display: grid; place-items: center;
        font-size: 15px;
    }

    .lecture-count-chip {
        font-size: 11px;
        font-weight: 700;
        background: rgba(79,142,247,0.1);
        color: #4f8ef7;
        border-radius: 20px;
        padding: 3px 10px;
    }

    /* ── Lecture cards ── */
    .lectures-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 16px;
        margin-bottom: 28px;
    }

    .lecture-card {
        background: #fff;
        border: 1.5px solid #e8eaf0;
        border-radius: 14px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 14px;
        transition: border-color 0.18s, box-shadow 0.18s;
        position: relative;
        overflow: hidden;
    }

    .lecture-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, #4f8ef7, #7b5ea7);
        opacity: 0;
        transition: opacity 0.18s;
    }

    .lecture-card:hover { border-color: #cbd5e1; box-shadow: 0 4px 20px rgba(0,0,0,0.06); }
    .lecture-card:hover::before { opacity: 1; }

    .lecture-card.marked { border-color: rgba(34,197,94,0.25); }
    .lecture-card.marked::before { background: linear-gradient(90deg, #22c55e, #16a34a); opacity: 1; }

    .lc-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
    }

    .lc-subject { font-size: 15px; font-weight: 700; color: #0f172a; margin-bottom: 3px; }
    .lc-code    { font-size: 11.5px; color: #94a3b8; font-weight: 500; }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 600;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .status-pill.marked   { background: rgba(34,197,94,0.1);  color: #16a34a; }
    .status-pill.pending  { background: rgba(245,158,11,0.1); color: #d97706; }

    .status-pill-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

    .lc-meta { display: flex; flex-direction: column; gap: 7px; }

    .lc-meta-row {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #374151;
    }

    .lc-meta-icon {
        width: 26px; height: 26px;
        border-radius: 6px;
        background: #f1f5f9;
        display: grid; place-items: center;
        font-size: 12px;
        color: #64748b;
        flex-shrink: 0;
    }

    .lc-action { margin-top: auto; }

    .btn-mark {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 10px;
        border-radius: 9px;
        background: linear-gradient(135deg, #4f8ef7, #2f6de0);
        color: #fff;
        border: none;
        font-size: 13.5px;
        font-weight: 600;
        font-family: 'Plus Jakarta Sans', sans-serif;
        cursor: pointer;
        text-decoration: none;
        transition: opacity 0.18s, transform 0.18s, box-shadow 0.18s;
        box-shadow: 0 2px 10px rgba(79,142,247,0.25);
    }

    .btn-mark:hover {
        opacity: 0.9;
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(79,142,247,0.35);
        color: #fff;
    }

    .marked-info {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 9px 12px;
        border-radius: 9px;
        background: rgba(34,197,94,0.08);
        border: 1px solid rgba(34,197,94,0.18);
        color: #16a34a;
        font-size: 13px;
        font-weight: 600;
    }

    /* ── Empty state ── */
    .empty-state {
        background: #fff;
        border: 1.5px solid #e8eaf0;
        border-radius: 14px;
        padding: 64px 24px;
        text-align: center;
        margin-bottom: 28px;
    }

    .empty-icon {
        width: 64px; height: 64px;
        border-radius: 16px;
        background: #f1f5f9;
        display: grid; place-items: center;
        font-size: 28px;
        color: #94a3b8;
        margin: 0 auto 16px;
    }

    .empty-title { font-size: 16px; font-weight: 700; color: #374151; margin-bottom: 6px; }
    .empty-sub   { font-size: 13.5px; color: #94a3b8; }

    /* ── Quick action cards ── */
    .quick-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 16px;
    }

    .quick-card {
        background: #fff;
        border: 1.5px solid #e8eaf0;
        border-radius: 14px;
        padding: 28px 24px;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 14px;
        transition: border-color 0.18s, box-shadow 0.18s;
        text-decoration: none;
    }

    .quick-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        text-decoration: none;
    }

    .quick-card-icon {
        width: 48px; height: 48px;
        border-radius: 12px;
        display: grid; place-items: center;
        font-size: 22px;
    }

    .quick-card-icon.blue  { background: rgba(79,142,247,0.1);  color: #4f8ef7; }
    .quick-card-icon.teal  { background: rgba(14,165,233,0.1);  color: #0ea5e9; }

    .quick-card-title { font-size: 15px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
    .quick-card-sub   { font-size: 13px; color: #64748b; font-weight: 500; }

    .quick-card-cta {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 600;
        color: #4f8ef7;
        margin-top: 4px;
    }

    @media (max-width: 640px) {
        .att-page { padding: 16px; }
        .page-hero { flex-direction: column; }
        .lectures-grid { grid-template-columns: 1fr; }
        .quick-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="att-page">

    <!-- ── Page hero ── -->
    <div class="page-hero">
        <div>
            <div class="page-eyebrow"><i class="bi bi-calendar-check"></i> Today</div>
            <h1 class="page-title">Attendance Management</h1>
            <p class="page-subtitle">Mark and track attendance for your lectures.</p>
        </div>
        <div class="hero-actions">
            <a href="{{ route('teacher.attendance.history') }}" class="btn-outline-soft">
                <i class="bi bi-clock-history"></i> History
            </a>
            <a href="{{ route('teacher.attendance.report') }}" class="btn-outline-soft info">
                <i class="bi bi-graph-up"></i> Reports
            </a>
        </div>
    </div>

    <!-- ── Today's lectures ── -->
    <div class="section-header">
        <div class="section-title">
            <div class="section-title-icon"><i class="bi bi-calendar-day"></i></div>
            Today's Lectures
            <span class="lecture-count-chip">{{ $todaySchedule->count() }} scheduled</span>
        </div>
    </div>

    @if($todaySchedule->count() > 0)
        <div class="lectures-grid">
            @foreach($todaySchedule as $lecture)
                <div class="lecture-card {{ $lecture->attendance_marked ? 'marked' : '' }}">
                    <div class="lc-top">
                        <div>
                            <div class="lc-subject">{{ $lecture->subject->name ?? 'N/A' }}</div>
                            <div class="lc-code">{{ $lecture->subject->code ?? '' }}</div>
                        </div>
                        @if($lecture->attendance_marked)
                            <span class="status-pill marked">
                                <span class="status-pill-dot"></span>Marked
                            </span>
                        @else
                            <span class="status-pill pending">
                                <span class="status-pill-dot"></span>Pending
                            </span>
                        @endif
                    </div>

                    <div class="lc-meta">
                        <div class="lc-meta-row">
                            <span class="lc-meta-icon"><i class="bi bi-people"></i></span>
                            {{ $lecture->division->division_name }}
                        </div>
                        <div class="lc-meta-row">
                            <span class="lc-meta-icon"><i class="bi bi-clock"></i></span>
                            {{ $lecture->formatted_time_range }}
                        </div>
                        <div class="lc-meta-row">
                            <span class="lc-meta-icon"><i class="bi bi-geo-alt"></i></span>
                            {{ $lecture->room_number ?? 'TBA' }}
                        </div>
                    </div>

                    <div class="lc-action">
                        @if($lecture->attendance_marked)
                            <div class="marked-info">
                                <i class="bi bi-check-circle-fill"></i>
                                {{ $lecture->attendance_count }} students marked
                            </div>
                        @else
                            <a href="{{ route('teacher.attendance.create', $lecture->id) }}" class="btn-mark">
                                <i class="bi bi-calendar-check"></i> Mark Attendance
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon"><i class="bi bi-calendar-x"></i></div>
            <div class="empty-title">No lectures scheduled today</div>
            <div class="empty-sub">Enjoy your free time! ☕</div>
        </div>
    @endif

    <!-- ── Quick actions ── -->
    <div class="section-header">
        <div class="section-title">
            <div class="section-title-icon"><i class="bi bi-lightning"></i></div>
            Quick Actions
        </div>
    </div>

    <div class="quick-grid">
        <a href="{{ route('teacher.attendance.history') }}" class="quick-card">
            <div class="quick-card-icon blue"><i class="bi bi-clock-history"></i></div>
            <div>
                <div class="quick-card-title">View History</div>
                <div class="quick-card-sub">Browse past attendance records across all divisions.</div>
            </div>
            <span class="quick-card-cta">Go to History <i class="bi bi-arrow-right"></i></span>
        </a>

        <a href="{{ route('teacher.attendance.report') }}" class="quick-card">
            <div class="quick-card-icon teal"><i class="bi bi-graph-up"></i></div>
            <div>
                <div class="quick-card-title">Attendance Reports</div>
                <div class="quick-card-sub">View detailed statistics, trends, and exportable reports.</div>
            </div>
            <span class="quick-card-cta">View Reports <i class="bi bi-arrow-right"></i></span>
        </a>
    </div>

</div>
@endsection