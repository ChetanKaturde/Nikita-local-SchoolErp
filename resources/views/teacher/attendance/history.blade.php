@extends('layouts.teacher')

@section('title', 'Attendance History')
@section('page-title', 'Attendance History')

@push('styles')
<style>
    .history-page { padding: 28px; max-width: 1400px; }

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

    .btn-soft.success { color: #16a34a; border-color: rgba(34,197,94,0.25); background: rgba(34,197,94,0.04); }
    .btn-soft.success:hover { background: rgba(34,197,94,0.1); border-color: rgba(34,197,94,0.4); }

    /* ── Filter card ── */
    .filter-card {
        background: #fff;
        border: 1.5px solid #e8eaf0;
        border-radius: 14px;
        padding: 20px 22px;
        margin-bottom: 20px;
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

    .filter-card-header-icon {
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
        font-size: 11.5px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    .btn-filter {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 10px 20px;
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
        height: 42px;
    }

    .btn-filter:hover { opacity: 0.9; transform: translateY(-1px); }

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

    .count-chip {
        font-size: 11px;
        font-weight: 700;
        background: rgba(79,142,247,0.1);
        color: #4f8ef7;
        border-radius: 20px;
        padding: 3px 9px;
    }

    .per-page-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .per-page-label { font-size: 12px; color: #94a3b8; font-weight: 500; }

    .per-page-select {
        border: 1.5px solid #e8eaf0;
        border-radius: 7px;
        padding: 5px 10px;
        font-size: 12.5px;
        font-weight: 600;
        color: #374151;
        font-family: 'Plus Jakarta Sans', sans-serif;
        background: #f8fafc;
        cursor: pointer;
        outline: none;
        transition: border-color 0.18s;
    }

    .per-page-select:focus { border-color: #4f8ef7; }

    /* ── Table ── */
    .att-table { width: 100%; border-collapse: collapse; }

    .att-table thead tr { background: #f8fafc; border-bottom: 1.5px solid #e8eaf0; }

    .att-table thead th {
        padding: 11px 18px;
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        color: #64748b;
        white-space: nowrap;
        text-align: left;
    }

    .att-table thead th.text-end { text-align: right; }

    .att-table tbody tr { border-bottom: 1px solid #f1f5f9; transition: background 0.15s; }
    .att-table tbody tr:hover { background: #f8fafc; }
    .att-table tbody tr:last-child { border-bottom: none; }

    .att-table tbody td {
        padding: 13px 18px;
        font-size: 13.5px;
        color: #374151;
        vertical-align: middle;
    }

    /* Date cell */
    .date-main { font-weight: 700; color: #0f172a; font-size: 13.5px; }
    .date-sub  { font-size: 11.5px; color: #94a3b8; margin-top: 2px; }

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

    /* Subject cell */
    .subject-name { font-weight: 600; color: #0f172a; font-size: 13.5px; }
    .subject-code { font-size: 11.5px; color: #94a3b8; margin-top: 1px; }

    /* Status badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 600;
    }

    .status-badge.present  { background: rgba(34,197,94,0.1);  color: #16a34a; }
    .status-badge.absent   { background: rgba(239,68,68,0.1);  color: #dc2626; }
    .status-badge.late     { background: rgba(245,158,11,0.1); color: #d97706; }
    .status-badge.excused  { background: rgba(14,165,233,0.1); color: #0284c7; }
    .status-badge.default  { background: #f1f5f9; color: #64748b; }

    .status-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

    /* Remarks */
    .remarks-text { font-size: 12.5px; color: #64748b; font-style: italic; }
    .remarks-none { font-size: 12.5px; color: #cbd5e1; }

    /* Actions */
    .btn-edit {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 7px;
        border: 1.5px solid #e8eaf0;
        background: #fff;
        font-size: 12px;
        font-weight: 600;
        color: #d97706;
        text-decoration: none;
        font-family: 'Plus Jakarta Sans', sans-serif;
        transition: background 0.18s, border-color 0.18s;
        border-color: rgba(245,158,11,0.25);
        background: rgba(245,158,11,0.04);
        cursor: pointer;
    }

    .btn-edit:hover { background: rgba(245,158,11,0.1); border-color: rgba(245,158,11,0.4); color: #b45309; }

    .other-teacher { font-size: 12px; color: #cbd5e1; font-style: italic; }

    /* ── Table footer ── */
    .table-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 22px;
        border-top: 1.5px solid #f1f5f9;
        gap: 12px;
        flex-wrap: wrap;
    }

    .showing-text { font-size: 12.5px; color: #94a3b8; font-weight: 500; }
    .showing-text strong { color: #374151; font-weight: 700; }

    /* Pagination overrides */
    .pagination { gap: 4px; margin: 0; }
    .pagination .page-link {
        border: 1.5px solid #e8eaf0;
        border-radius: 8px !important;
        color: #374151;
        font-size: 12.5px;
        font-weight: 600;
        padding: 5px 11px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        transition: all 0.18s;
        background: #fff;
    }
    .pagination .page-link:hover { background: #4f8ef7; color: #fff; border-color: #4f8ef7; }
    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #4f8ef7, #2f6de0);
        border-color: #4f8ef7;
        color: #fff;
    }
    .pagination .page-item.disabled .page-link { color: #cbd5e1; background: #f8fafc; border-color: #f1f5f9; }

    /* ── Download dropdown ── */
    .download-dropdown .dropdown-menu {
        border: 1.5px solid #e8eaf0;
        border-radius: 11px;
        box-shadow: 0 8px 28px rgba(0,0,0,0.09);
        padding: 6px;
        min-width: 160px;
    }

    .download-dropdown .dropdown-item {
        border-radius: 7px;
        font-size: 13px;
        padding: 8px 12px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 600;
        color: #374151;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .download-dropdown .dropdown-item:hover { background: #f1f5f9; }

    .download-dropdown .dropdown-item .excel-icon { color: #16a34a; }
    .download-dropdown .dropdown-item .pdf-icon   { color: #dc2626; }

    /* ── Empty / prompt states ── */
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

    @media (max-width: 900px) {
        .filter-grid { grid-template-columns: 1fr 1fr; }
    }

    @media (max-width: 640px) {
        .history-page { padding: 16px; }
        .page-hero { flex-direction: column; }
        .filter-grid { grid-template-columns: 1fr; }
        .table-footer { justify-content: center; flex-direction: column; align-items: center; }
    }
</style>
@endpush

@section('content')
<div class="history-page">

    <!-- ── Page hero ── -->
    <div class="page-hero">
        <div>
            <div class="page-eyebrow"><i class="bi bi-clock-history"></i> Records</div>
            <h1 class="page-title">Attendance History</h1>
            <p class="page-subtitle">Browse and filter past attendance records.</p>
        </div>

        <div class="hero-actions">
            <a href="{{ route('teacher.attendance.index') }}" class="btn-soft">
                <i class="bi bi-house"></i> Dashboard
            </a>
            <a href="{{ route('teacher.attendance.report') }}" class="btn-soft info">
                <i class="bi bi-graph-up"></i> Reports
            </a>

            @if($selectedDivision)
                <div class="dropdown download-dropdown">
                    <button class="btn-soft success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-download"></i> Download
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('teacher.attendance.history.download-excel', ['division_id' => $selectedDivision->id, 'start_date' => $startDate, 'end_date' => $endDate]) }}">
                                <i class="bi bi-file-earmark-excel excel-icon"></i> Excel (.xlsx)
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('teacher.attendance.history.download-pdf', ['division_id' => $selectedDivision->id, 'start_date' => $startDate, 'end_date' => $endDate]) }}">
                                <i class="bi bi-file-earmark-pdf pdf-icon"></i> PDF
                            </a>
                        </li>
                    </ul>
                </div>
            @endif
        </div>
    </div>

    <!-- ── Filters ── -->
    <div class="filter-card">
        <div class="filter-card-header">
            <div class="filter-card-header-icon"><i class="bi bi-funnel"></i></div>
            Filter Records
        </div>
        <form method="GET">
            <div class="filter-grid">
                <div class="filter-group">
                    <label>Division</label>
                    <select name="division_id" class="form-select">
                        <option value="">All Divisions</option>
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
                    <button type="submit" class="btn-filter">
                        <i class="bi bi-search"></i> Apply
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- ── Records ── -->
    @if($selectedDivision && $attendances->count() > 0)

        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-title">
                    <i class="bi bi-list-check" style="color:#4f8ef7;"></i>
                    Attendance Records
                    <span class="count-chip">{{ $attendances->total() }} total</span>
                </div>
                <div class="per-page-wrap">
                    <span class="per-page-label">Show</span>
                    <select class="per-page-select" onchange="updatePerPage(this.value)">
                        <option value="10"  {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="20"  {{ request('per_page') == 20 || !request('per_page') ? 'selected' : '' }}>20</option>
                        <option value="50"  {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                    <span class="per-page-label">per page</span>
                </div>
            </div>

            <div style="overflow-x:auto;">
                <table class="att-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Student</th>
                            <th>Roll No.</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Remarks</th>
                            <th class="text-end" style="padding-right:22px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendances as $attendance)
                            <tr>
                                <td>
                                    <div class="date-main">{{ $attendance->date->format('d M Y') }}</div>
                                    <div class="date-sub">{{ $attendance->date->format('l') }}</div>
                                </td>
                                <td>
                                    <div class="student-cell">
                                        <div class="student-avatar">
                                            {{ strtoupper(substr($attendance->student->first_name ?? 'S', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="student-name">{{ $attendance->student->full_name ?? 'N/A' }}</div>
                                            <div class="student-email">{{ $attendance->student->email ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="roll-chip">{{ $attendance->student->roll_number ?? '—' }}</span>
                                </td>
                                <td>
                                    <div class="subject-name">{{ $attendance->timetable->subject->name ?? 'N/A' }}</div>
                                    <div class="subject-code">{{ $attendance->timetable->subject->code ?? '' }}</div>
                                </td>
                                <td>
                                    @php
                                        $statusClass = match(strtolower($attendance->status ?? '')) {
                                            'present' => 'present',
                                            'absent'  => 'absent',
                                            'late'    => 'late',
                                            'excused' => 'excused',
                                            default   => 'default',
                                        };
                                    @endphp
                                    <span class="status-badge {{ $statusClass }}">
                                        <span class="status-dot"></span>
                                        {{ ucfirst($attendance->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($attendance->remarks)
                                        <span class="remarks-text">{{ $attendance->remarks }}</span>
                                    @else
                                        <span class="remarks-none">—</span>
                                    @endif
                                </td>
                                <td style="text-align:right; padding-right:22px;">
                                    @if($attendance->marked_by === auth()->id())
                                        <a href="{{ route('teacher.attendance.record.edit', $attendance->id) }}" class="btn-edit">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                    @else
                                        <span class="other-teacher">Other teacher</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="table-footer">
                <div class="showing-text">
                    Showing <strong>{{ $attendances->firstItem() ?? 0 }}–{{ $attendances->lastItem() ?? 0 }}</strong>
                    of <strong>{{ $attendances->total() }}</strong> records
                </div>
                @if($attendances->hasPages())
                    <div>
                        {{ $attendances->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>

    @elseif($selectedDivision)
        <div class="state-card">
            <div class="state-icon"><i class="bi bi-inbox"></i></div>
            <div class="state-title">No records found</div>
            <div class="state-sub">Try adjusting the date range or division filter.</div>
        </div>

    @else
        <div class="state-card">
            <div class="state-icon"><i class="bi bi-funnel"></i></div>
            <div class="state-title">Select a division to begin</div>
            <div class="state-sub">Use the filters above to load attendance records.</div>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    function updatePerPage(perPage) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', perPage);
        window.location.href = url.toString();
    }
</script>
@endpush