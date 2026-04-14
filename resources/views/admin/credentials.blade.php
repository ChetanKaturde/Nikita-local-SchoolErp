@extends('layouts.app')

@section('title', 'User Credentials Management')
@section('page-title', 'Credentials')

@push('styles')
<style>
    /* ── Page Header ── */
    .cred-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 24px;
    }
    .cred-header-title { font-size: 20px; font-weight: 600; color: var(--ink-900); margin: 0; letter-spacing: -.3px; }
    .cred-header-sub   { font-size: 13px; color: var(--sb-muted); margin: 3px 0 0; }

    /* ── Tab bar ── */
    .tab-bar {
        display: flex;
        gap: 4px;
        border-bottom: 1px solid var(--sb-border);
        margin-bottom: 20px;
    }
    .tab-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 16px;
        font-size: 13.5px;
        font-weight: 500;
        color: var(--ink-500);
        border: none;
        border-bottom: 2px solid transparent;
        background: none;
        cursor: pointer;
        transition: color .15s, border-color .15s;
        font-family: var(--font);
        margin-bottom: -1px;
    }
    .tab-btn:hover { color: var(--ink-900); }
    .tab-btn.active { color: var(--accent); border-bottom-color: var(--accent); }
    .tab-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 20px;
        height: 20px;
        padding: 0 5px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 600;
    }
    .tab-btn.active .tab-count { background: var(--accent-light); color: var(--accent); }
    .tab-btn:not(.active) .tab-count { background: var(--ink-100); color: var(--ink-500); }

    /* ── Tab pane ── */
    .tab-pane { display: none; }
    .tab-pane.active { display: block; }

    /* ── Filter card ── */
    .filter-card {
        background: var(--card-bg);
        border: 1px solid var(--sb-border);
        border-radius: var(--r-lg);
        padding: 16px 20px;
        margin-bottom: 16px;
    }

    /* ── Results info strip ── */
    .results-strip {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12.5px;
        color: var(--ink-500);
        padding: 8px 12px;
        background: var(--accent-light);
        border: 1px solid var(--accent-mid);
        border-radius: var(--r-md);
        margin-bottom: 14px;
    }
    .results-strip i { color: var(--accent); }

    /* ── Data card ── */
    .data-card {
        background: var(--card-bg);
        border: 1px solid var(--sb-border);
        border-radius: var(--r-lg);
        overflow: hidden;
    }
    .data-card-header {
        padding: 14px 20px;
        border-bottom: 1px solid var(--sb-border);
        background: var(--ink-50);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }
    .data-card-title {
        font-size: 13.5px;
        font-weight: 600;
        color: var(--ink-900);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 7px;
    }
    .data-card-title i { color: var(--accent); }

    /* ── Table ── */
    .cred-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .cred-table thead th {
        background: var(--ink-50);
        padding: 10px 16px;
        font-size: 10.5px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--ink-500);
        border-bottom: 1px solid var(--sb-border);
        white-space: nowrap;
        text-align: left;
    }
    .cred-table tbody td {
        padding: 12px 16px;
        border-bottom: 1px solid var(--sb-border);
        vertical-align: middle;
        color: var(--ink-700);
    }
    .cred-table tbody tr:last-child td { border-bottom: none; }
    .cred-table tbody tr:hover { background: var(--ink-50); }

    /* ── User chip ── */
    .user-chip { display: flex; align-items: center; gap: 10px; }
    .u-avatar {
        width: 32px; height: 32px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 600;
        flex-shrink: 0;
        overflow: hidden;
    }
    .u-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .u-name  { font-size: 13px; font-weight: 500; color: var(--ink-900); margin: 0; }
    .u-email { font-size: 11.5px; color: var(--ink-500); margin: 1px 0 0; }

    /* ── Tag / badge ── */
    .tag {
        display: inline-flex; align-items: center;
        padding: 2px 9px;
        border-radius: 20px;
        font-size: 11px; font-weight: 500;
        white-space: nowrap;
    }
    .tag-blue   { background: var(--accent-light); color: var(--accent); }
    .tag-teal   { background: #f0fdfa; color: #0d9488; }
    .tag-gray   { background: var(--ink-100); color: var(--ink-500); }
    .tag-amber  { background: #fffbeb; color: #d97706; }
    .tag-purple { background: #f5f3ff; color: #7c3aed; }

    /* ── Password input ── */
    .pw-wrap { display: flex; align-items: center; gap: 8px; }
    .pw-group { display: flex; width: 190px; }
    .pw-group input {
        flex: 1;
        border: 1px solid #d1d5db;
        border-right: none;
        border-radius: var(--r-sm) 0 0 var(--r-sm);
        padding: 5px 10px;
        font-size: 12px;
        font-family: monospace;
        letter-spacing: 1.5px;
        background: var(--ink-50);
        color: var(--ink-900);
        min-width: 0;
    }
    .pw-btn {
        border: 1px solid #d1d5db;
        border-left: none;
        background: var(--card-bg);
        padding: 0 9px;
        cursor: pointer;
        font-size: 11.5px;
        color: var(--ink-500);
        transition: background .12s, color .12s;
        display: flex; align-items: center;
    }
    .pw-btn:last-child { border-radius: 0 var(--r-sm) var(--r-sm) 0; }
    .pw-btn:hover { background: var(--ink-50); color: var(--ink-900); }
    .pw-sub { font-size: 11px; color: var(--ink-300); margin-top: 3px; }

    /* ── Action buttons ── */
    .act-btns { display: flex; gap: 5px; align-items: center; }

    /* ── Empty state ── */
    .empty-state { text-align: center; padding: 48px 20px; }
    .empty-state i { font-size: 36px; color: var(--ink-300); display: block; margin-bottom: 10px; }
    .empty-state h5 { font-size: 15px; color: var(--ink-500); margin: 0 0 6px; }
    .empty-state p { font-size: 13px; color: var(--ink-300); margin: 0 0 14px; }

    /* ── Pagination ── */
    .card-footer-bar {
        padding: 12px 20px;
        border-top: 1px solid var(--sb-border);
        background: var(--ink-50);
    }

    /* ── Modal ── */
    .modal-content {
        border: 1px solid var(--sb-border);
        border-radius: var(--r-lg);
        box-shadow: var(--shadow-md);
        font-family: var(--font);
    }
    .modal-header {
        padding: 15px 20px;
        border-bottom: 1px solid var(--sb-border);
        border-radius: var(--r-lg) var(--r-lg) 0 0;
    }
    .modal-header.accent { background: var(--accent); }
    .modal-header.accent .modal-title { color: #fff; }
    .modal-header.accent .btn-close { filter: brightness(10); }
    .modal-title { font-size: 14.5px; font-weight: 600; }
    .modal-body  { padding: 20px; }
    .modal-footer { border-top: 1px solid var(--sb-border); padding: 14px 20px; gap: 8px; }
    .modal-field-label { font-size: 11.5px; color: var(--ink-500); margin: 0 0 3px; }
    .modal-field-value { font-size: 14px; font-weight: 500; color: var(--ink-900); margin: 0; }

    /* ── Modal pw-group fullwidth ── */
    .pw-group-full { display: flex; width: 100%; }
    .pw-group-full input {
        flex: 1;
        border: 1px solid #d1d5db;
        border-right: none;
        border-radius: var(--r-md) 0 0 var(--r-md);
        padding: 8px 12px;
        font-family: monospace;
        font-size: 14px;
        letter-spacing: 2px;
        background: var(--ink-50);
        color: var(--ink-900);
        min-width: 0;
    }
    .pw-group-full .pw-btn {
        padding: 0 13px;
        border: 1px solid #d1d5db;
        border-left: none;
    }
    .pw-group-full .pw-btn:last-child { border-radius: 0 var(--r-md) var(--r-md) 0; }

    /* ── Info strip in modal ── */
    .info-strip {
        display: flex; align-items: center; gap: 8px;
        font-size: 12.5px;
        padding: 10px 14px;
        background: var(--accent-light);
        border: 1px solid var(--accent-mid);
        border-radius: var(--r-md);
        color: var(--accent);
        margin-top: 16px;
    }

    /* ── Toast ── */
    #toastContainer { position: fixed; bottom: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 6px; }
    .toast-item {
        padding: 10px 16px;
        border-radius: var(--r-md);
        font-size: 13px;
        font-family: var(--font);
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: var(--shadow-sm);
        animation: slideIn .2s ease;
    }
    @keyframes slideIn { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
    .toast-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; }
    .toast-danger  { background: #fff1f1; border: 1px solid #fecaca; color: #dc2626; }
</style>
@endpush

@section('content')

{{-- ── Page Header ── --}}
<div class="cred-header">
    <div>
        <h1 class="cred-header-title">User Credentials</h1>
        <p class="cred-header-sub">View and manage user login passwords</p>
    </div>
    <div style="display:flex; gap:8px; flex-wrap:wrap;">
        <a href="{{ route('dashboard.students.index') }}" class="btn btn-outline">
            <i class="fas fa-user-graduate me-1"></i> Students
        </a>
        <a href="{{ route('dashboard.teachers.index') }}" class="btn btn-outline">
            <i class="fas fa-chalkboard-user me-1"></i> Teachers
        </a>
    </div>
</div>

{{-- ── Tab Bar ── --}}
<div class="tab-bar">
    <button class="tab-btn active" data-tab="students" onclick="switchTab('students', this)">
        <i class="fas fa-user-graduate"></i>
        Students
        <span class="tab-count">{{ $students->total() }}</span>
    </button>
    <button class="tab-btn" data-tab="staff" onclick="switchTab('staff', this)">
        <i class="fas fa-users"></i>
        Staff
        <span class="tab-count">{{ $staff->total() }}</span>
    </button>
    <button class="tab-btn" data-tab="teachers" onclick="switchTab('teachers', this)">
        <i class="fas fa-chalkboard-user"></i>
        Teachers
        <span class="tab-count">{{ $teachers->total() }}</span>
    </button>
</div>

{{-- ══════════════════════════════
     STUDENTS TAB
══════════════════════════════ --}}
<div class="tab-pane active" id="pane-students">

    {{-- Filter --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('admin.credentials.index') }}">
            <input type="hidden" name="tab" value="students">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Search Students</label>
                    <div class="input-group">
                        <span class="input-group-text" style="background:var(--ink-50); border-color:#d1d5db; color:var(--ink-500);">
                            <i class="fas fa-search" style="font-size:12px;"></i>
                        </span>
                        <input type="text" name="student_search" class="form-control"
                               placeholder="Name, Email, Admission No, Roll No…"
                               value="{{ $studentSearch }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Filter by Division</label>
                    <select name="division_filter" class="form-select">
                        <option value="">All Divisions</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}" {{ $divisionFilter == $division->id ? 'selected' : '' }}>
                                {{ $division->program->name ?? '' }} – {{ $division->division_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-auto">
                    <div style="display:flex; gap:6px;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('admin.credentials.index', ['tab' => 'students']) }}" class="btn btn-outline" title="Clear">
                            <i class="fas fa-xmark"></i>
                        </a>
                    </div>
                </div>
                <div class="col-md-auto ms-auto">
                    <button type="button" class="btn btn-outline" onclick="exportTable('students')">
                        <i class="fas fa-download me-1"></i> Export CSV
                    </button>
                </div>
            </div>
        </form>
    </div>

    @if($studentSearch || $divisionFilter)
    <div class="results-strip">
        <i class="fas fa-circle-info"></i>
        Showing <strong>{{ $students->count() }}</strong> of <strong>{{ $students->total() }}</strong> students
        @if($studentSearch) matching "<strong>{{ $studentSearch }}</strong>" @endif
        @if($divisionFilter) in selected division @endif
    </div>
    @endif

    {{-- Table --}}
    <div class="data-card">
        <div class="data-card-header">
            <p class="data-card-title"><i class="fas fa-table-list"></i> Student Credentials</p>
        </div>
        <div style="overflow-x:auto;">
            <table class="cred-table">
                <thead>
                    <tr>
                        <th style="width:44px; padding-left:20px;">#</th>
                        <th>Student</th>
                        <th>Email</th>
                        <th>Admission No</th>
                        <th>Roll No</th>
                        <th>Division</th>
                        <th>Password</th>
                        <th>Generated On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $index => $student)
                    <tr>
                        <td style="padding-left:20px; color:var(--ink-300); font-size:12px;">{{ $students->firstItem() + $index }}</td>
                        <td>
                            <div class="user-chip">
                                @if($student->photo_path)
                                    <div class="u-avatar">
                                        <img src="{{ route('documents.students.document', [$student, 'photo']) }}" alt="Photo">
                                    </div>
                                @else
                                    <div class="u-avatar" style="background:var(--accent-light); color:var(--accent);">
                                        {{ strtoupper(substr($student->first_name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="u-name">{{ $student->first_name }} {{ $student->last_name }}</p>
                                    <p class="u-email">{{ $student->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="tag tag-teal">{{ $student->user->email ?? '—' }}</span>
                        </td>
                        <td><span class="tag tag-blue">{{ $student->admission_number }}</span></td>
                        <td><span class="tag tag-gray">{{ $student->roll_number }}</span></td>
                        <td style="color:var(--ink-700);">{{ $student->division->division_name ?? '—' }}</td>
                        <td>
                            <div class="pw-wrap">
                                <div>
                                    <div class="pw-group">
                                        <input type="password" value="{{ $student->user->temp_password ?? 'Not Set' }}"
                                               id="password-{{ $student->id }}" readonly>
                                        <button class="pw-btn" type="button"
                                                onclick="togglePw('password-{{ $student->id }}', 'eye-{{ $student->id }}')"
                                                title="Show / Hide">
                                            <i class="fas fa-eye" id="eye-{{ $student->id }}"></i>
                                        </button>
                                    </div>
                                    <p class="pw-sub">
                                        {{ $student->user->password_generated_at ? $student->user->password_generated_at->diffForHumans() : '—' }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:12.5px; color:var(--ink-500); white-space:nowrap;">
                            {{ $student->user->password_generated_at ? $student->user->password_generated_at->format('d M Y') : '—' }}
                        </td>
                        <td>
                            <div class="act-btns">
                                <button class="btn btn-outline btn-sm"
                                        onclick="copyPw('password-{{ $student->id }}')"
                                        title="Copy password">
                                    <i class="fas fa-copy"></i>
                                </button>
                                <button class="btn btn-outline btn-sm"
                                        onclick="viewPwModal('{{ $student->first_name }} {{ $student->last_name }}','{{ $student->user->email ?? '' }}','{{ $student->user->temp_password ?? 'Not Set' }}','{{ $student->admission_number }}')"
                                        title="View details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-outline btn-sm"
                                        onclick="sendCredentials({{ $student->user->id }}, '{{ $student->user->email }}')"
                                        title="Send credentials via email">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                                <button class="btn btn-outline btn-sm"
                                        onclick="resetPassword('student', {{ $student->user->id }})"
                                        title="Reset password">
                                    <i class="fas fa-arrows-rotate"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <h5>No students found</h5>
                                @if($studentSearch || $divisionFilter)
                                <p>Try adjusting your search or filter criteria</p>
                                <a href="{{ route('admin.credentials.index', ['tab' => 'students']) }}" class="btn btn-outline btn-sm">Clear Filters</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($students->hasPages())
        <div class="card-footer-bar">
            {{ $students->appends(['student_search' => $studentSearch, 'division_filter' => $divisionFilter])->links() }}
        </div>
        @endif
    </div>
</div>

{{-- ══════════════════════════════
     TEACHERS TAB
══════════════════════════════ --}}
{{-- STAFF TAB (Principal, Admin, Accountant, Librarian, etc.) --}}
<div class="tab-pane" id="pane-staff">

    {{-- Filter --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('admin.credentials.index') }}">
            <input type="hidden" name="tab" value="staff">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Search Staff</label>
                    <div class="input-group">
                        <span class="input-group-text" style="background:var(--ink-50); border-color:#d1d5db; color:var(--ink-500);">
                            <i class="fas fa-search" style="font-size:12px;"></i>
                        </span>
                        <input type="text" name="staff_search" class="form-control"
                               placeholder="Name or Email…"
                               value="{{ $staffSearch }}">
                    </div>
                </div>
                <div class="col-md-auto">
                    <div style="display:flex; gap:6px;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('admin.credentials.index', ['tab' => 'staff']) }}" class="btn btn-outline" title="Clear">
                            <i class="fas fa-xmark"></i>
                        </a>
                    </div>
                </div>
                <div class="col-md-auto ms-auto">
                    <button type="button" class="btn btn-outline" onclick="exportTable('staff')">
                        <i class="fas fa-download me-1"></i> Export CSV
                    </button>
                </div>
            </div>
        </form>
    </div>

    @if($staffSearch)
    <div class="results-strip">
        <i class="fas fa-circle-info"></i>
        Showing <strong>{{ $staff->count() }}</strong> of <strong>{{ $staff->total() }}</strong> staff members
        matching "<strong>{{ $staffSearch }}</strong>"
    </div>
    @endif

    {{-- Table --}}
    <div class="data-card">
        <div class="data-card-header">
            <p class="data-card-title"><i class="fas fa-table-list"></i> Staff Credentials</p>
        </div>
        <div style="overflow-x:auto;">
            <table class="cred-table">
                <thead>
                    <tr>
                        <th style="width:44px; padding-left:20px;">#</th>
                        <th>Staff Member</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Password</th>
                        <th>Generated On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staff as $index => $member)
                    <tr>
                        <td style="padding-left:20px; color:var(--ink-300); font-size:12px;">{{ $staff->firstItem() + $index }}</td>
                        <td>
                            <div class="user-chip">
                                @php
                                    $roleName = $member->roles->first()->name ?? 'staff';
                                    $bgColor = match($roleName) {
                                        'admin' => '#fef2f2',
                                        'principal' => '#fefce8',
                                        'accountant' => '#f0fdf4',
                                        'librarian' => '#f0f9ff',
                                        'office' => '#f5f3ff',
                                        default => '#fafafa'
                                    };
                                    $textColor = match($roleName) {
                                        'admin' => '#dc2626',
                                        'principal' => '#ca8a04',
                                        'accountant' => '#16a34a',
                                        'librarian' => '#0369a1',
                                        'office' => '#7c3aed',
                                        default => '#52525b'
                                    };
                                @endphp
                                <div class="u-avatar" style="background:{{ $bgColor }}; color:{{ $textColor }};">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="u-name">{{ $member->name }}</p>
                                    <p class="u-email">{{ $member->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="tag tag-teal" style="max-width:200px; overflow:hidden; text-overflow:ellipsis; display:inline-block;">
                                {{ $member->email }}
                            </span>
                        </td>
                        <td>
                            @if($member->roles->count() > 0)
                                @php
                                    $displayRole = match($member->roles->first()->name) {
                                        'admin' => 'Administrator',
                                        'principal' => 'Principal',
                                        'accountant' => 'Accountant',
                                        'librarian' => 'Librarian',
                                        'office' => 'Office Staff',
                                        'accounts_staff' => 'Accounts Staff',
                                        'admission_officer' => 'Admission Officer',
                                        'hod_commerce' => 'HOD Commerce',
                                        'hod_science' => 'HOD Science',
                                        'hod_management' => 'HOD Management',
                                        'hod_arts' => 'HOD Arts',
                                        'class_teacher' => 'Class Teacher',
                                        'subject_teacher' => 'Subject Teacher',
                                        'staff' => 'Staff',
                                        'lab_instructor' => 'Lab Instructor',
                                        default => $member->roles->first()->name
                                    };
                                @endphp
                                <span class="tag tag-amber">{{ $displayRole }}</span>
                            @else
                                <span class="tag tag-gray">No Role</span>
                            @endif
                        </td>
                        <td>
                            <div>
                                <div class="pw-group">
                                    <input type="password" value="{{ $member->temp_password ?? 'Not Set' }}"
                                           id="spw-{{ $member->id }}" readonly>
                                    <button class="pw-btn" type="button"
                                            onclick="togglePw('spw-{{ $member->id }}', 'seye-{{ $member->id }}')"
                                            title="Show / Hide">
                                        <i class="fas fa-eye" id="seye-{{ $member->id }}"></i>
                                    </button>
                                </div>
                                <p class="pw-sub">
                                    {{ $member->password_generated_at ? $member->password_generated_at->diffForHumans() : '—' }}
                                </p>
                            </div>
                        </td>
                        <td style="font-size:12.5px; color:var(--ink-500); white-space:nowrap;">
                            {{ $member->password_generated_at ? $member->password_generated_at->format('d M Y') : '—' }}
                        </td>
                        <td>
                            <div class="act-btns">
                                <button class="btn btn-outline btn-sm"
                                        onclick="copyPw('spw-{{ $member->id }}')"
                                        title="Copy password">
                                    <i class="fas fa-copy"></i>
                                </button>
                                <button class="btn btn-outline btn-sm"
                                        onclick="viewPwModal('{{ $member->name }}','{{ $member->email }}','{{ $member->temp_password ?? 'Not Set' }}','{{ $displayRole ?? 'Staff' }}')"
                                        title="View details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-outline btn-sm"
                                        onclick="sendCredentials({{ $member->id }}, '{{ $member->email }}')"
                                        title="Send credentials via email">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                                <button class="btn btn-outline btn-sm"
                                        onclick="resetPassword('staff', {{ $member->id }})"
                                        title="Reset password">
                                    <i class="fas fa-arrows-rotate"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <h5>No staff found</h5>
                                @if($staffSearch)
                                <p>Try adjusting your search criteria</p>
                                <a href="{{ route('admin.credentials.index', ['tab' => 'staff']) }}" class="btn btn-outline btn-sm">Clear Filters</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($staff->hasPages())
        <div class="card-footer-bar">
            {{ $staff->appends(['staff_search' => $staffSearch])->links() }}
        </div>
        @endif
    </div>
</div>

<div class="tab-pane" id="pane-teachers">

    {{-- Filter --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('admin.credentials.index') }}">
            <input type="hidden" name="tab" value="teachers">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Search Teachers</label>
                    <div class="input-group">
                        <span class="input-group-text" style="background:var(--ink-50); border-color:#d1d5db; color:var(--ink-500);">
                            <i class="fas fa-search" style="font-size:12px;"></i>
                        </span>
                        <input type="text" name="teacher_search" class="form-control"
                               placeholder="Name or Email…"
                               value="{{ $teacherSearch }}">
                    </div>
                </div>
                <div class="col-md-auto">
                    <div style="display:flex; gap:6px;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('admin.credentials.index', ['tab' => 'teachers']) }}" class="btn btn-outline" title="Clear">
                            <i class="fas fa-xmark"></i>
                        </a>
                    </div>
                </div>
                <div class="col-md-auto ms-auto">
                    <button type="button" class="btn btn-outline" onclick="exportTable('teachers')">
                        <i class="fas fa-download me-1"></i> Export CSV
                    </button>
                </div>
            </div>
        </form>
    </div>

    @if($teacherSearch)
    <div class="results-strip">
        <i class="fas fa-circle-info"></i>
        Showing <strong>{{ $teachers->count() }}</strong> of <strong>{{ $teachers->total() }}</strong> teachers
        matching "<strong>{{ $teacherSearch }}</strong>"
    </div>
    @endif

    {{-- Table --}}
    <div class="data-card">
        <div class="data-card-header">
            <p class="data-card-title"><i class="fas fa-table-list"></i> Teacher Credentials</p>
        </div>
        <div style="overflow-x:auto;">
            <table class="cred-table">
                <thead>
                    <tr>
                        <th style="width:44px; padding-left:20px;">#</th>
                        <th>Teacher</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Password</th>
                        <th>Generated On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teachers as $index => $teacher)
                    <tr>
                        <td style="padding-left:20px; color:var(--ink-300); font-size:12px;">{{ $teachers->firstItem() + $index }}</td>
                        <td>
                            <div class="user-chip">
                                <div class="u-avatar" style="background:#f0fdf4; color:#16a34a;">
                                    {{ strtoupper(substr($teacher->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="u-name">{{ $teacher->name }}</p>
                                    <p class="u-email">{{ $teacher->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="tag tag-teal" style="max-width:200px; overflow:hidden; text-overflow:ellipsis; display:inline-block;">
                                {{ $teacher->email }}
                            </span>
                        </td>
                        <td>
                            @if($teacher->roles->count() > 0)
                                <span class="tag tag-purple">{{ $teacher->roles->first()->name }}</span>
                            @else
                                <span class="tag tag-gray">No Role</span>
                            @endif
                        </td>
                        <td>
                            <div>
                                <div class="pw-group">
                                    <input type="password" value="{{ $teacher->temp_password ?? 'Not Set' }}"
                                           id="tpw-{{ $teacher->id }}" readonly>
                                    <button class="pw-btn" type="button"
                                            onclick="togglePw('tpw-{{ $teacher->id }}', 'teye-{{ $teacher->id }}')"
                                            title="Show / Hide">
                                        <i class="fas fa-eye" id="teye-{{ $teacher->id }}"></i>
                                    </button>
                                </div>
                                <p class="pw-sub">
                                    {{ $teacher->password_generated_at ? $teacher->password_generated_at->diffForHumans() : '—' }}
                                </p>
                            </div>
                        </td>
                        <td style="font-size:12.5px; color:var(--ink-500); white-space:nowrap;">
                            {{ $teacher->password_generated_at ? $teacher->password_generated_at->format('d M Y') : '—' }}
                        </td>
                        <td>
                            <div class="act-btns">
                                <button class="btn btn-outline btn-sm"
                                        onclick="copyPw('tpw-{{ $teacher->id }}')"
                                        title="Copy password">
                                    <i class="fas fa-copy"></i>
                                </button>
                                <button class="btn btn-outline btn-sm"
                                        onclick="viewPwModal('{{ $teacher->name }}','{{ $teacher->email }}','{{ $teacher->temp_password ?? 'Not Set' }}','{{ $teacher->roles->first()->name ?? 'Teacher' }}')"
                                        title="View details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-outline btn-sm"
                                        onclick="sendCredentials({{ $teacher->id }}, '{{ $teacher->email }}')"
                                        title="Send credentials via email">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                                <button class="btn btn-outline btn-sm"
                                        onclick="resetPassword('teacher', {{ $teacher->id }})"
                                        title="Reset password">
                                    <i class="fas fa-arrows-rotate"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <h5>No teachers found</h5>
                                @if($teacherSearch)
                                <p>Try adjusting your search criteria</p>
                                <a href="{{ route('admin.credentials.index', ['tab' => 'teachers']) }}" class="btn btn-outline btn-sm">Clear Filters</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($teachers->hasPages())
        <div class="card-footer-bar">
            {{ $teachers->appends(['teacher_search' => $teacherSearch])->links() }}
        </div>
        @endif
    </div>
</div>

{{-- ── Password View Modal ── --}}
<div class="modal fade" id="passwordViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content">
            <div class="modal-header accent">
                <h5 class="modal-title"><i class="fas fa-key me-2"></i>User Credentials</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <p class="modal-field-label">Name</p>
                        <p class="modal-field-value" id="modalUserName">—</p>
                    </div>
                    <div class="col-6">
                        <p class="modal-field-label">Admission / Role</p>
                        <p id="modalUserAdmission" style="margin:0;">—</p>
                    </div>
                    <div class="col-12">
                        <p class="modal-field-label">Email</p>
                        <p class="modal-field-value" id="modalUserEmail">—</p>
                    </div>
                </div>
                <div>
                    <p class="modal-field-label" style="margin-bottom:6px;">Password</p>
                    <div class="pw-group-full">
                        <input type="password" id="modalUserPassword" readonly>
                        <button class="pw-btn" type="button" onclick="toggleModalPw()">
                            <i class="fas fa-eye" id="modalEyeIcon"></i>
                        </button>
                        <button class="pw-btn" type="button" onclick="copyModalPw()">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                <div class="info-strip">
                    <i class="fas fa-circle-info"></i>
                    Keep this password secure. Share it only with the user.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Toast container --}}
<div id="toastContainer"></div>

@endsection

@push('scripts')
<script>
/* ── Tab switching ── */
function switchTab(tabName, btn) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('pane-' + tabName).classList.add('active');

    const url = new URL(window.location.href);
    url.searchParams.set('tab', tabName);
    window.history.replaceState({}, '', url);
}

/* ── Init: restore tab from URL ── */
document.addEventListener('DOMContentLoaded', function () {
    const tab = new URLSearchParams(window.location.search).get('tab');
    if (tab === 'teachers') {
        const btn = document.querySelector('[data-tab="teachers"]');
        if (btn) switchTab('teachers', btn);
    } else if (tab === 'staff') {
        const btn = document.querySelector('[data-tab="staff"]');
        if (btn) switchTab('staff', btn);
    }
});

/* ── Password toggle ── */
function togglePw(inputId, eyeId) {
    const inp = document.getElementById(inputId);
    const eye = document.getElementById(eyeId);
    inp.type = inp.type === 'password' ? 'text' : 'password';
    if (eye) { eye.classList.toggle('fa-eye'); eye.classList.toggle('fa-eye-slash'); }
}

/* ── Copy password ── */
function copyPw(inputId) {
    const val = document.getElementById(inputId)?.value;
    if (!val) return;
    navigator.clipboard.writeText(val)
        .then(() => showToast('Password copied to clipboard!', 'success'))
        .catch(() => showToast('Failed to copy password', 'danger'));
}

/* ── View modal ── */
function viewPwModal(name, email, password, admissionOrRole) {
    document.getElementById('modalUserName').textContent      = name;
    document.getElementById('modalUserEmail').textContent     = email;
    document.getElementById('modalUserAdmission').innerHTML   =
        `<span class="tag tag-blue" style="font-size:12px;">${admissionOrRole}</span>`;
    document.getElementById('modalUserPassword').value        = password;
    document.getElementById('modalUserPassword').type         = 'password';
    document.getElementById('modalEyeIcon').className         = 'fas fa-eye';
    new bootstrap.Modal(document.getElementById('passwordViewModal')).show();
}

function toggleModalPw() {
    const inp = document.getElementById('modalUserPassword');
    const eye = document.getElementById('modalEyeIcon');
    inp.type = inp.type === 'password' ? 'text' : 'password';
    eye.classList.toggle('fa-eye');
    eye.classList.toggle('fa-eye-slash');
}

function copyModalPw() {
    const val = document.getElementById('modalUserPassword')?.value;
    if (!val) return;
    navigator.clipboard.writeText(val)
        .then(() => showToast('Password copied!', 'success'))
        .catch(() => showToast('Failed to copy', 'danger'));
}

/* ── Reset password ── */
function resetPassword(type, userId) {
    if (!confirm('Reset this password? A new password will be generated and the old one will be lost.')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/credentials/reset-password/${userId}`;
    const tok = document.createElement('input');
    tok.type = 'hidden'; tok.name = '_token'; tok.value = '{{ csrf_token() }}';
    form.appendChild(tok);
    document.body.appendChild(form);
    form.submit();
}

/* ── Send credentials ── */
function sendCredentials(userId, email) {
    if (!confirm(`Send login credentials to ${email}?`)) return;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/credentials/send-credentials/${userId}`;
    const tok = document.createElement('input');
    tok.type = 'hidden'; tok.name = '_token'; tok.value = '{{ csrf_token() }}';
    form.appendChild(tok);
    document.body.appendChild(form);
    form.submit();
}

/* ── Export ── */
function exportTable(type) {
    window.location.href = `/admin/credentials/export?type=${type}`;
}

/* ── Toast ── */
function showToast(message, type = 'info') {
    const c = document.getElementById('toastContainer');
    const d = document.createElement('div');
    d.className = `toast-item toast-${type}`;
    d.innerHTML = `<i class="fas fa-${type === 'success' ? 'circle-check' : 'circle-xmark'}"></i>${message}`;
    c.appendChild(d);
    setTimeout(() => d.remove(), 3000);
}
</script>
@endpush