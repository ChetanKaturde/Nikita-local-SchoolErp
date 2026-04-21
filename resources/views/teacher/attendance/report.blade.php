@extends('layouts.teacher')

@section('title', 'Attendance Report')
@section('page-title', 'Attendance Report')

@push('styles')
<style>
    .report-page { padding: 28px; max-width: 1400px; }

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

    .page-title    { font-size: 26px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px; margin: 0 0 4px; }
    .page-subtitle { font-size: 13.5px; color: #64748b; font-weight: 500; }

    .hero-actions { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }

    .btn-soft {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 16px;
        border-radius: 9px;
        border: 1.5px solid #e8eaf0;
        background: #fff;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        text-decoration: none;
        font-family: 'Plus Jakarta Sans', sans-serif;
        transition: background 0.18s, border-color 0.18s;
        cursor: pointer;
        white-space: nowrap;
    }

    .btn-soft:hover { background: #f8fafc; border-color: #cbd5e1; color: #0f172a; }

    .btn-soft.info { color: #0ea5e9; border-color: rgba(14,165,233,0.25); background: rgba(14,165,233,0.04); }
    .btn-soft.info:hover { background: rgba(14,165,233,0.1); border-color: rgba(14,165,233,0.4); }

    /* ── Filter card ── */
    .filter-card {
        background: #fff;
        border: 1.5px solid #e8eaf0;
        border-radius: 14px;
        padding: 20px 22px;
        margin-bottom: 24px;
    }

    .filter-card-header {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 16px;
    }

    .filter-hdr-icon {
        width: 28px; height: 28px;
        border-radius: 7px;
        background: rgba(79,142,247,0.1);
        color: #4f8ef7;
        display: grid; place-items: center;
        font-size: 13px;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr auto;
        gap: 12px;
        align-items: end;
    }

    .filter-group label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    .btn-apply {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 10px 20px;
        height: 42px;
        border-radius: 9px;
        background: linear-gradient(135deg, #4f8ef7, #2f6de0);
        color: #fff;
        border: none;
        font-size: 13.5px;
        font-weight: 600;
        font-family: 'Plus Jakarta Sans', sans-serif;
        cursor: pointer;
        white-space: nowrap;
        box-shadow: 0 2px 10px rgba(79,142,247,0.25);
        transition: opacity 0.18s, transform 0.18s;
    }

    .btn-apply:hover { opacity: 0.9; transform: translateY(-1px); }

    /* ── Stat cards ── */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: #fff;
        border: 1.5px solid #e8eaf0;
        border-radius: 14px;
        padding: 20px;
        position: relative;
        overflow: hidden;
        transition: border-color 0.18s, box-shadow 0.18s;
    }

    .stat-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    }

    /* Subtle colored left border accent */
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; bottom: 0;
        width: 4px;
        border-radius: 4px 0 0 4px;
    }

    .stat-card.blue::before   { background: #4f8ef7; }
    .stat-card.green::before  { background: #22c55e; }
    .stat-card.red::before    { background: #ef4444; }
    .stat-card.amber::before  { background: #f59e0b; }

    .stat-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 14px;
    }

    .stat-icon {
        width: 42px; height: 42px;
        border-radius: 11px;
        display: grid; place-items: center;
        font-size: 19px;
        flex-shrink: 0;
    }

    .stat-icon.blue  { background: rgba(79,142,247,0.1);  color: #4f8ef7; }
    .stat-icon.green { background: rgba(34,197,94,0.1);   color: #22c55e; }
    .stat-icon.red   { background: rgba(239,68,68,0.1);   color: #ef4444; }
    .stat-icon.amber { background: rgba(245,158,11,0.1);  color: #f59e0b; }

    .stat-pct-pill {
        display: inline-flex;
        align-items: center;
        padding: 3px 9px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
    }

    .stat-pct-pill.green { background: rgba(34,197,94,0.1);  color: #16a34a; }
    .stat-pct-pill.red   { background: rgba(239,68,68,0.1);  color: #dc2626; }
    .stat-pct-pill.amber { background: rgba(245,158,11,0.1); color: #d97706; }
    .stat-pct-pill.blue  { background: rgba(79,142,247,0.1); color: #2563eb; }

    .stat-value { font-size: 32px; font-weight: 800; color: #0f172a; letter-spacing: -1px; line-height: 1; margin-bottom: 4px; }
    .stat-label { font-size: 13px; font-weight: 600; color: #64748b; }

    /* Progress bar */
    .stat-bar-track {
        height: 4px;
        background: #f1f5f9;
        border-radius: 4px;
        margin-top: 14px;
        overflow: hidden;
    }

    .stat-bar-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 0.6s ease;
    }

    .stat-bar-fill.green { background: linear-gradient(90deg, #22c55e, #16a34a); }
    .stat-bar-fill.red   { background: linear-gradient(90deg, #ef4444, #dc2626); }
    .stat-bar-fill.amber { background: linear-gradient(90deg, #f59e0b, #d97706); }
    .stat-bar-fill.blue  { background: linear-gradient(90deg, #4f8ef7, #2f6de0); }

    /* ── Table card ── */
    .table-card {
        background: #fff;
        border: 1.5px solid #e8eaf0;
        border-radius: 14px;
        overflow: hidden;
    }

    .table-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 22px;
        border-bottom: 1.5px solid #f1f5f9;
        gap: 12px;
        flex-wrap: wrap;
    }

    .table-card-title {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-print {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 14px;
        border-radius: 8px;
        border: 1.5px solid #e8eaf0;
        background: #f8fafc;
        font-size: 12.5px;
        font-weight: 600;
        color: #374151;
        font-family: 'Plus Jakarta Sans', sans-serif;
        cursor: pointer;
        transition: background 0.18s, border-color 0.18s;
    }

    .btn-print:hover { background: #f1f5f9; border-color: #cbd5e1; }

    /* ── Table ── */
    .report-table { width: 100%; border-collapse: collapse; }

    .report-table thead tr { background: #f8fafc; border-bottom: 1.5px solid #e8eaf0; }
    .report-table thead th {
        padding: 11px 16px;
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        color: #64748b;
        white-space: nowrap;
        text-align: left;
    }

    .report-table thead th.center { text-align: center; }

    .report-table tbody tr { border-bottom: 1px solid #f1f5f9; transition: background 0.15s; }
    .report-table tbody tr:hover { background: #f8fafc; }
    .report-table tbody tr:last-child { border-bottom: none; }

    .report-table tbody td {
        padding: 13px 16px;
        font-size: 13.5px;
        color: #374151;
        vertical-align: middle;
    }

    .report-table tbody td.center { text-align: center; }

    /* Roll chip */
    .roll-chip {
        display: inline-flex;
        align-items: center;
        background: #f1f5f9;
        border-radius: 6px;
        padding: 3px 9px;
        font-size: 11.5px;
        font-weight: 700;
        color: #374151;
        font-family: 'JetBrains Mono', monospace;
    }

    /* Student cell */
    .student-cell { display: flex; align-items: center; gap: 11px; }

    .student-avatar {
        width: 34px; height: 34px;
        border-radius: 9px;
        background: linear-gradient(135deg, #4f8ef7, #7b5ea7);
        display: grid; place-items: center;
        font-size: 13px; font-weight: 700; color: #fff;
        flex-shrink: 0;
    }

    .student-name  { font-weight: 600; color: #0f172a; font-size: 13.5px; }
    .student-email { font-size: 11.5px; color: #94a3b8; margin-top: 1px; }

    /* Count badges */
    .count-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        padding: 3px 9px;
        border-radius: 7px;
        font-size: 12.5px;
        font-weight: 700;
    }

    .count-badge.green { background: rgba(34,197,94,0.1);  color: #16a34a; }
    .count-badge.red   { background: rgba(239,68,68,0.1);  color: #dc2626; }
    .count-badge.amber { background: rgba(245,158,11,0.1); color: #d97706; }
    .count-badge.gray  { background: #f1f5f9; color: #374151; }

    /* Percentage indicator */
    .pct-wrap { display: flex; align-items: center; gap: 10px; justify-content: center; }

    .pct-bar-track {
        width: 60px; height: 5px;
        background: #f1f5f9;
        border-radius: 5px;
        overflow: hidden;
        flex-shrink: 0;
    }

    .pct-bar-fill { height: 100%; border-radius: 5px; }
    .pct-bar-fill.green { background: #22c55e; }
    .pct-bar-fill.amber { background: #f59e0b; }
    .pct-bar-fill.red   { background: #ef4444; }

    .pct-badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 9px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        min-width: 52px;
        justify-content: center;
    }

    .pct-badge.green { background: rgba(34,197,94,0.1);  color: #16a34a; }
    .pct-badge.amber { background: rgba(245,158,11,0.1); color: #d97706; }
    .pct-badge.red   { background: rgba(239,68,68,0.1);  color: #dc2626; }

    /* ── State cards ── */
    .state-card {
        background: #fff;
        border: 1.5px solid #e8eaf0;
        border-radius: 14px;
        padding: 64px 24px;
        text-align: center;
    }

    .state-icon {
        width: 64px; height: 64px;
        border-radius: 16px;
        background: #f1f5f9;
        display: grid; place-items: center;
        font-size: 28px;
        color: #94a3b8;
        margin: 0 auto 16px;
    }

    .state-title { font-size: 16px; font-weight: 700; color: #374151; margin-bottom: 6px; }
    .state-sub   { font-size: 13.5px; color: #94a3b8; }

    /* ── Print ── */
    @media print {
        .hero-actions, .btn-print, .btn-apply, .filter-card { display: none !important; }
        .stat-card { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
        .table-card { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
    }

    @media (max-width: 1024px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 900px) {
        .filter-grid { grid-template-columns: 1fr 1fr; }
    }

    @media (max-width: 640px) {
        .report-page { padding: 16px; }
        .page-hero { flex-direction: column; }
        .filter-grid { grid-template-columns: 1fr; }
        .stats-grid { grid-template-columns: 1fr 1fr; }
        .pct-bar-track { display: none; }
    }
</style>
@endpush

@section('content')
<div class="report-page">

    <!-- ── Page hero ── -->
    <div class="page-hero">
        <div>
            <div class="page-eyebrow"><i class="bi bi-graph-up"></i> Analytics</div>
            <h1 class="page-title">Attendance Report</h1>
            <p class="page-subtitle">Detailed attendance statistics and student-wise analysis.</p>
        </div>
        <div class="hero-actions">
            <a href="{{ route('teacher.attendance.index') }}" class="btn-soft">
                <i class="bi bi-house"></i> Dashboard
            </a>
            <a href="{{ route('teacher.attendance.history') }}" class="btn-soft info">
                <i class="bi bi-clock-history"></i> History
            </a>
        </div>
    </div>

    <!-- ── Filters ── -->
    <div class="filter-card">
        <div class="filter-card-header">
            <div class="filter-hdr-icon"><i class="bi bi-funnel"></i></div>
            Filter Report
        </div>
        <form method="GET">
            <div class="filter-grid">
                <div class="filter-group">
                    <label>Division</label>
                    <select name="division_id" class="form-select" onchange="this.form.submit()">
                        <option value="">— Select Division —</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                {{ $division->division_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label>From Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date', $startDate) }}">
                </div>
                <div class="filter-group">
                    <label>To Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date', $endDate) }}">
                </div>
                <div class="filter-group">
                    <button type="submit" class="btn-apply">
                        <i class="bi bi-search"></i> Apply
                    </button>
                </div>
            </div>
        </form>
    </div>

    @if($stats)

        <!-- ── Summary stats ── -->
        <div class="stats-grid">
            <!-- Total Lectures -->
            <div class="stat-card blue">
                <div class="stat-card-top">
                    <div class="stat-icon blue"><i class="bi bi-calendar-check"></i></div>
                    <span class="stat-pct-pill blue">All</span>
                </div>
                <div class="stat-value">{{ $stats['total_lectures'] }}</div>
                <div class="stat-label">Total Lectures</div>
                <div class="stat-bar-track">
                    <div class="stat-bar-fill blue" style="width:100%;"></div>
                </div>
            </div>

            <!-- Present -->
            <div class="stat-card green">
                <div class="stat-card-top">
                    <div class="stat-icon green"><i class="bi bi-check-circle"></i></div>
                    <span class="stat-pct-pill green">{{ $stats['present_percentage'] }}%</span>
                </div>
                <div class="stat-value">{{ $stats['total_present'] }}</div>
                <div class="stat-label">Present</div>
                <div class="stat-bar-track">
                    <div class="stat-bar-fill green" style="width: {{ $stats['present_percentage'] }}%;"></div>
                </div>
            </div>

            <!-- Absent -->
            <div class="stat-card red">
                <div class="stat-card-top">
                    <div class="stat-icon red"><i class="bi bi-x-circle"></i></div>
                    <span class="stat-pct-pill red">{{ $stats['absent_percentage'] }}%</span>
                </div>
                <div class="stat-value">{{ $stats['total_absent'] }}</div>
                <div class="stat-label">Absent</div>
                <div class="stat-bar-track">
                    <div class="stat-bar-fill red" style="width: {{ $stats['absent_percentage'] }}%;"></div>
                </div>
            </div>

            <!-- Late -->
            <div class="stat-card amber">
                <div class="stat-card-top">
                    <div class="stat-icon amber"><i class="bi bi-exclamation-circle"></i></div>
                    <span class="stat-pct-pill amber">{{ $stats['late_percentage'] }}%</span>
                </div>
                <div class="stat-value">{{ $stats['total_late'] }}</div>
                <div class="stat-label">Late</div>
                <div class="stat-bar-track">
                    <div class="stat-bar-fill amber" style="width: {{ $stats['late_percentage'] }}%;"></div>
                </div>
            </div>
        </div>

        <!-- ── Student-wise table ── -->
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-title">
                    <i class="bi bi-people" style="color:#4f8ef7;"></i>
                    Student-wise Attendance
                </div>
                <button class="btn-print" onclick="window.print()">
                    <i class="bi bi-printer"></i> Print Report
                </button>
            </div>

            <div style="overflow-x:auto;">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Roll No.</th>
                            <th>Student</th>
                            <th class="center">Present</th>
                            <th class="center">Absent</th>
                            <th class="center">Late</th>
                            <th class="center">Total</th>
                            <th class="center">Attendance %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($studentWiseStats as $student)
                            @php
                                $total      = $student->total ?? 0;
                                $percentage = $total > 0 ? round(($student->present_count / $total) * 100, 1) : 0;
                                $pctClass   = $percentage >= 75 ? 'green' : ($percentage >= 50 ? 'amber' : 'red');
                            @endphp
                            <tr>
                                <td>
                                    <span class="roll-chip">{{ $student->roll_number ?? '—' }}</span>
                                </td>
                                <td>
                                    <div class="student-cell">
                                        <div class="student-avatar">
                                            {{ strtoupper(substr($student->name ?? 'S', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="student-name">{{ $student->name }}</div>
                                            <div class="student-email">{{ $student->email ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="center">
                                    <span class="count-badge green">{{ $student->present_count ?? 0 }}</span>
                                </td>
                                <td class="center">
                                    <span class="count-badge red">{{ $student->absent_count ?? 0 }}</span>
                                </td>
                                <td class="center">
                                    <span class="count-badge amber">{{ $student->late_count ?? 0 }}</span>
                                </td>
                                <td class="center">
                                    <span class="count-badge gray">{{ $total }}</span>
                                </td>
                                <td class="center">
                                    <div class="pct-wrap">
                                        <div class="pct-bar-track">
                                            <div class="pct-bar-fill {{ $pctClass }}" style="width:{{ $percentage }}%;"></div>
                                        </div>
                                        <span class="pct-badge {{ $pctClass }}">{{ $percentage }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    @elseif(request('division_id'))
        <div class="state-card">
            <div class="state-icon"><i class="bi bi-inbox"></i></div>
            <div class="state-title">No data available</div>
            <div class="state-sub">No attendance records found for the selected period.</div>
        </div>

    @else
        <div class="state-card">
            <div class="state-icon"><i class="bi bi-bar-chart"></i></div>
            <div class="state-title">Select a division to view the report</div>
            <div class="state-sub">Choose a division from the filter above to load statistics.</div>
        </div>
    @endif

</div>
@endsection