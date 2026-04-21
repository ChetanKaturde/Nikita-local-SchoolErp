@extends('layouts.teacher')

@section('title', 'My Students')
@section('page-title', 'My Students')

@push('styles')
<style>
    /* ─── Page wrapper ───────────────────────────── */
    .students-page {
        padding: 28px;
        max-width: 1400px;
    }

    /* ─── Page header ────────────────────────────── */
    .page-hero {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 28px;
        flex-wrap: wrap;
    }

    .page-hero-left {}

    .page-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11.5px;
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

    .page-title {
        font-size: 26px;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.5px;
        margin: 0 0 4px;
        line-height: 1.2;
    }

    .page-subtitle {
        font-size: 13.5px;
        color: #64748b;
        font-weight: 500;
    }

    /* ─── Stat pills ─────────────────────────────── */
    .stat-pills {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
    }

    .stat-pill {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #fff;
        border: 1.5px solid #e8eaf0;
        border-radius: 12px;
        padding: 10px 16px;
        min-width: 120px;
    }

    .stat-pill-icon {
        width: 34px; height: 34px;
        border-radius: 9px;
        display: grid; place-items: center;
        font-size: 15px;
        flex-shrink: 0;
    }

    .stat-pill-icon.blue  { background: rgba(79,142,247,0.1);  color: #4f8ef7; }
    .stat-pill-icon.green { background: rgba(34,197,94,0.1);   color: #22c55e; }
    .stat-pill-icon.amber { background: rgba(245,158,11,0.1);  color: #f59e0b; }

    .stat-pill-val  { font-size: 18px; font-weight: 800; color: #0f172a; line-height: 1; }
    .stat-pill-lbl  { font-size: 11px; color: #94a3b8; font-weight: 600; margin-top: 1px; }

    /* ─── Search & filter bar ─────────────────────── */
    .filter-bar {
        background: #fff;
        border: 1.5px solid #e8eaf0;
        border-radius: 14px;
        padding: 16px 20px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .search-wrap {
        position: relative;
        flex: 1;
        min-width: 220px;
    }

    .search-wrap .search-icon {
        position: absolute;
        left: 13px;
        top: 50%; transform: translateY(-50%);
        color: #94a3b8;
        font-size: 14px;
        pointer-events: none;
    }

    .search-wrap .form-control {
        padding-left: 38px;
    }

    .btn-search {
        background: linear-gradient(135deg, #4f8ef7, #2f6de0);
        color: #fff;
        border: none;
        border-radius: 9px;
        padding: 9px 20px;
        font-size: 13.5px;
        font-weight: 600;
        font-family: 'Plus Jakarta Sans', sans-serif;
        cursor: pointer;
        transition: opacity 0.18s, transform 0.18s;
        display: flex; align-items: center; gap: 7px;
        white-space: nowrap;
    }

    .btn-search:hover { opacity: 0.9; transform: translateY(-1px); }

    .btn-clear {
        background: #f8fafc;
        color: #64748b;
        border: 1.5px solid #e8eaf0;
        border-radius: 9px;
        padding: 9px 16px;
        font-size: 13.5px;
        font-weight: 600;
        font-family: 'Plus Jakarta Sans', sans-serif;
        cursor: pointer;
        text-decoration: none;
        display: flex; align-items: center; gap: 7px;
        white-space: nowrap;
        transition: background 0.18s, border-color 0.18s;
    }

    .btn-clear:hover { background: #f1f5f9; border-color: #cbd5e1; color: #374151; }

    /* ─── Table card ─────────────────────────────── */
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

    .table-card-title .count-chip {
        font-size: 11px;
        font-weight: 700;
        background: rgba(79,142,247,0.1);
        color: #4f8ef7;
        border-radius: 20px;
        padding: 2px 9px;
    }

    .result-info {
        font-size: 12.5px;
        color: #94a3b8;
        font-weight: 500;
    }

    /* ─── Table ──────────────────────────────────── */
    .student-table { width: 100%; border-collapse: collapse; }

    .student-table thead tr {
        background: #f8fafc;
        border-bottom: 1.5px solid #e8eaf0;
    }

    .student-table thead th {
        padding: 11px 18px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        color: #64748b;
        white-space: nowrap;
    }

    .student-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.15s;
    }

    .student-table tbody tr:hover { background: #f8fafc; }
    .student-table tbody tr:last-child { border-bottom: none; }

    .student-table tbody td {
        padding: 14px 18px;
        font-size: 13.5px;
        color: #374151;
        vertical-align: middle;
    }

    /* Roll number chip */
    .roll-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 46px;
        padding: 4px 10px;
        background: #f1f5f9;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 700;
        color: #374151;
        font-family: 'JetBrains Mono', monospace;
        letter-spacing: 0.3px;
    }

    /* Student name cell */
    .student-name-cell { display: flex; align-items: center; gap: 11px; }

    .student-mini-avatar {
        width: 34px; height: 34px;
        border-radius: 9px;
        background: linear-gradient(135deg, #4f8ef7, #7b5ea7);
        display: grid; place-items: center;
        font-size: 13px; font-weight: 700; color: #fff;
        flex-shrink: 0;
    }

    /* Cycle avatar colors for variety */
    .student-mini-avatar:nth-child(4n+1) { background: linear-gradient(135deg, #4f8ef7, #7b5ea7); }
    .student-mini-avatar:nth-child(4n+2) { background: linear-gradient(135deg, #22c55e, #16a34a); }
    .student-mini-avatar:nth-child(4n+3) { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .student-mini-avatar:nth-child(4n+4) { background: linear-gradient(135deg, #ef4444, #dc2626); }

    .student-name-text { font-weight: 600; color: #0f172a; font-size: 13.5px; }

    /* Email */
    .email-text {
        color: #64748b;
        font-size: 13px;
    }

    /* Status badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 600;
        letter-spacing: 0.2px;
    }

    .status-badge.active {
        background: rgba(34,197,94,0.1);
        color: #16a34a;
    }

    .status-badge.inactive {
        background: rgba(148,163,184,0.1);
        color: #64748b;
    }

    .status-dot {
        width: 6px; height: 6px;
        border-radius: 50%;
    }

    .status-badge.active .status-dot   { background: #22c55e; }
    .status-badge.inactive .status-dot { background: #94a3b8; }

    /* Date text */
    .date-text { font-size: 13px; color: #64748b; }
    .date-na   { font-size: 13px; color: #cbd5e1; font-style: italic; }

    /* ─── Empty state ────────────────────────────── */
    .empty-state {
        padding: 64px 24px;
        text-align: center;
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

    /* ─── Table footer (pagination) ──────────────── */
    .table-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 22px;
        border-top: 1.5px solid #f1f5f9;
        gap: 12px;
        flex-wrap: wrap;
    }

    .showing-text {
        font-size: 12.5px;
        color: #94a3b8;
        font-weight: 500;
    }

    .showing-text strong { color: #374151; font-weight: 700; }

    @media (max-width: 640px) {
        .students-page { padding: 16px; }
        .page-hero { flex-direction: column; }
        .stat-pills { width: 100%; }
        .stat-pill { flex: 1; min-width: 100px; }
        .table-footer { justify-content: center; flex-direction: column; align-items: center; }
    }
</style>
@endpush

@section('content')
<div class="students-page">

    <!-- ── Page hero ── -->
    <div class="page-hero">
        <div class="page-hero-left">
            <div class="page-eyebrow">
                <i class="bi bi-collection"></i>
                {{ $assignedDivision->division_name }}
            </div>
            <h1 class="page-title">My Students</h1>
            <p class="page-subtitle">Manage and review students assigned to your division.</p>
        </div>

        <div class="stat-pills">
            <div class="stat-pill">
                <div class="stat-pill-icon blue"><i class="bi bi-people-fill"></i></div>
                <div>
                    <div class="stat-pill-val">{{ $students->total() }}</div>
                    <div class="stat-pill-lbl">Total</div>
                </div>
            </div>
            <div class="stat-pill">
                <div class="stat-pill-icon green"><i class="bi bi-person-check-fill"></i></div>
                <div>
                    <div class="stat-pill-val">{{ $students->where('student_status','active')->count() }}</div>
                    <div class="stat-pill-lbl">Active</div>
                </div>
            </div>
            <div class="stat-pill">
                <div class="stat-pill-icon amber"><i class="bi bi-layers"></i></div>
                <div>
                    <div class="stat-pill-val">{{ $students->lastPage() }}</div>
                    <div class="stat-pill-lbl">Pages</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Search / filter bar ── -->
    <form method="GET" action="{{ route('teacher.students.index') }}">
        <div class="filter-bar">
            <div class="search-wrap">
                <i class="bi bi-search search-icon"></i>
                <input type="text"
                       name="search"
                       class="form-control"
                       placeholder="Search by name, roll number, or email…"
                       value="{{ request('search') }}">
            </div>

            <button type="submit" class="btn-search">
                <i class="bi bi-search"></i> Search
            </button>

            @if(request('search'))
                <a href="{{ route('teacher.students.index') }}" class="btn-clear">
                    <i class="bi bi-x-circle"></i> Clear
                </a>
            @endif
        </div>
    </form>

    <!-- ── Table card ── -->
    <div class="table-card">

        <div class="table-card-header">
            <div class="table-card-title">
                <i class="bi bi-table" style="color:#4f8ef7;"></i>
                Student List
                <span class="count-chip">{{ $students->count() }} shown</span>
            </div>
            @if(request('search'))
                <div class="result-info">
                    Results for "<strong>{{ request('search') }}</strong>"
                </div>
            @endif
        </div>

        <div style="overflow-x:auto;">
            <table class="student-table">
                <thead>
                    <tr>
                        <th>Roll No.</th>
                        <th>Student</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Admission Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td>
                                <span class="roll-chip">{{ $student->roll_number }}</span>
                            </td>
                            <td>
                                <div class="student-name-cell">
                                    <div class="student-mini-avatar">
                                        {{ substr($student->name, 0, 1) }}
                                    </div>
                                    <span class="student-name-text">{{ $student->name }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="email-text">{{ $student->email }}</span>
                            </td>
                            <td>
                                @if($student->phone)
                                    <span style="font-size:13px;color:#374151;">{{ $student->phone }}</span>
                                @else
                                    <span class="date-na">—</span>
                                @endif
                            </td>
                            <td>
                                @if($student->student_status == 'active')
                                    <span class="status-badge active">
                                        <span class="status-dot"></span>Active
                                    </span>
                                @else
                                    <span class="status-badge inactive">
                                        <span class="status-dot"></span>{{ ucfirst($student->student_status) }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($student->admission_date)
                                    <span class="date-text">
                                        {{ \Carbon\Carbon::parse($student->admission_date)->format('d M Y') }}
                                    </span>
                                @else
                                    <span class="date-na">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-icon">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <div class="empty-title">
                                        @if(request('search'))
                                            No students found
                                        @else
                                            No students assigned
                                        @endif
                                    </div>
                                    <div class="empty-sub">
                                        @if(request('search'))
                                            Try a different name, roll number, or email.
                                        @else
                                            No students have been assigned to your division yet.
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table footer -->
        @if($students->hasPages() || $students->total() > 0)
        <div class="table-footer">
            <div class="showing-text">
                Showing <strong>{{ $students->firstItem() }}–{{ $students->lastItem() }}</strong>
                of <strong>{{ $students->total() }}</strong> students
            </div>

            @if($students->hasPages())
                <div>
                    {{ $students->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
        @endif

    </div><!-- /.table-card -->

</div><!-- /.students-page -->
@endsection