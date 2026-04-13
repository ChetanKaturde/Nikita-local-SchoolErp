@php
    $user = Auth::user();
    $userRole = $user->roles->first();
    $role = $userRole ? $userRole->name : 'student';
    
    // Debug: Show role (remove in production)
    // dd($role);
    
    // Role-based menu items
    $menuByRole = [
        'admin' => [
            ['name' => 'Dashboard', 'route' => 'dashboard.admin', 'icon' => 'speedometer2'],
            ['name' => 'Users', 'route' => 'admin.users', 'icon' => 'people'],
            ['name' => 'Admissions', 'route' => 'admissions.index', 'icon' => 'person-plus-fill'],
            ['name' => 'Students', 'route' => 'dashboard.students.index', 'icon' => 'people-fill'],
            ['name' => 'Teachers', 'route' => 'dashboard.teachers.index', 'icon' => 'person-badge-fill'],
            
            // Academic Management Section
            ['name' => 'Departments', 'route' => 'web.departments.index', 'icon' => 'building'],
            ['name' => 'Programs', 'route' => 'academic.programs.index', 'icon' => 'mortarboard'],
            ['name' => 'Subjects', 'route' => 'academic.subjects.index', 'icon' => 'book'],
            ['name' => 'Divisions', 'route' => 'academic.divisions.index', 'icon' => 'diagram-3'],
            ['name' => 'Academic Sessions', 'route' => 'academic.sessions.index', 'icon' => 'calendar-event'],
            
            // Timetable & Attendance Section
            ['name' => 'Timetable', 'route' => 'academic.timetable.grid', 'icon' => 'calendar-week'],
            ['name' => 'Attendance', 'route' => 'academic.attendance.index', 'icon' => 'clipboard-check'],
            ['name' => 'Holidays', 'route' => 'academic.holidays.index', 'icon' => 'calendar-event'],
            
            // Fee Management
            ['name' => 'Fee Structures', 'route' => 'fees.structures.index', 'icon' => 'list-columns'],
            ['name' => 'Fee Assignment', 'route' => 'fees.assignments.index', 'icon' => 'clipboard-plus'],
            ['name' => 'Fee Collection', 'route' => 'fees.payments.index', 'icon' => 'cash-stack'],
            ['name' => 'Outstanding Fees', 'route' => 'fees.outstanding.index', 'icon' => 'exclamation-triangle'],
            
            ['name' => 'Reports', 'route' => 'reports.index', 'icon' => 'graph-up'],
        ],
        'office' => [
            ['name' => 'Dashboard', 'route' => 'dashboard.office', 'icon' => 'speedometer2'],
            ['name' => 'Users', 'route' => 'admin.users', 'icon' => 'people'],
            ['name' => 'Admissions', 'route' => 'admissions.index', 'icon' => 'person-plus-fill'],
            ['name' => 'Students', 'route' => 'dashboard.students.index', 'icon' => 'people-fill'],
            
            // Fee Management
            ['name' => 'Fee Structures', 'route' => 'fees.structures.index', 'icon' => 'list-columns'],
            ['name' => 'Fee Assignment', 'route' => 'fees.assignments.index', 'icon' => 'clipboard-plus'],
            ['name' => 'Fee Collection', 'route' => 'fees.payments.index', 'icon' => 'cash-stack'],
            ['name' => 'Outstanding Fees', 'route' => 'fees.outstanding.index', 'icon' => 'exclamation-triangle'],
            
            ['name' => 'Reports', 'route' => 'reports.index', 'icon' => 'graph-up'],
        ],
        'principal' => [
            ['name' => 'Dashboard', 'route' => 'dashboard.principal', 'icon' => 'speedometer2'],
            ['name' => 'Admissions', 'route' => 'admissions.index', 'icon' => 'person-plus-fill'],
            ['name' => 'Students', 'route' => 'dashboard.students.index', 'icon' => 'people-fill'],
            ['name' => 'Teachers', 'route' => 'dashboard.teachers.index', 'icon' => 'person-badge-fill'],
            
            // Academic Management Section
            ['name' => 'Departments', 'route' => 'web.departments.index', 'icon' => 'building'],
            ['name' => 'Programs', 'route' => 'academic.programs.index', 'icon' => 'mortarboard'],
            ['name' => 'Subjects', 'route' => 'academic.subjects.index', 'icon' => 'book'],
            ['name' => 'Divisions', 'route' => 'academic.divisions.index', 'icon' => 'diagram-3'],
            ['name' => 'Academic Sessions', 'route' => 'academic.sessions.index', 'icon' => 'calendar-event'],
            
            // Timetable & Attendance Section
            ['name' => 'Timetable', 'route' => 'academic.timetable.grid', 'icon' => 'calendar-week'],
            ['name' => 'Attendance', 'route' => 'academic.attendance.index', 'icon' => 'clipboard-check'],
            ['name' => 'Holidays', 'route' => 'academic.holidays.index', 'icon' => 'calendar-event'],
            
            ['name' => 'Reports', 'route' => 'principal.reports', 'icon' => 'graph-up'],
        ],
        'teacher' => [
            ['name' => 'Dashboard', 'route' => 'teacher.dashboard', 'icon' => 'speedometer2'],
            ['name' => 'Students', 'route' => 'teacher.students', 'icon' => 'people-fill'],
            ['name' => 'Assignments', 'route' => 'teacher.assignments', 'icon' => 'clipboard-check'],
            
            // Timetable & Attendance Section
            ['name' => 'Timetable', 'route' => 'academic.timetable.teacher', 'icon' => 'calendar-week'],
            ['name' => 'Mark Attendance', 'route' => 'academic.attendance.create', 'icon' => 'clipboard-check'],
            ['name' => 'Holidays', 'route' => 'academic.holidays.index', 'icon' => 'calendar-event'],
        ],
        'student' => [
            ['name' => 'Dashboard', 'route' => 'student.dashboard', 'icon' => 'speedometer2'],
            ['name' => 'Profile', 'route' => 'student.profile', 'icon' => 'person'],
            ['name' => 'Fees', 'route' => 'student.fees', 'icon' => 'cash-stack'],
            ['name' => 'Library', 'route' => 'student.library', 'icon' => 'book'],
            
            // View Only Section
            ['name' => 'My Timetable', 'route' => 'academic.timetable.index', 'icon' => 'calendar-week'],
            ['name' => 'My Attendance', 'route' => 'academic.attendance.index', 'icon' => 'clipboard-check'],
            ['name' => 'Holidays', 'route' => 'academic.holidays.index', 'icon' => 'calendar-event'],
        ],
        'accountant' => [
            ['name' => 'Dashboard', 'route' => 'dashboard.accountant', 'icon' => 'speedometer2'],
            ['name' => 'Fee Structures', 'route' => 'fees.structures.index', 'icon' => 'list-columns'],
            ['name' => 'Fee Assignment', 'route' => 'fees.assignments.index', 'icon' => 'clipboard-plus'],
            ['name' => 'Fee Collection', 'route' => 'fees.payments.index', 'icon' => 'cash-stack'],
            ['name' => 'Outstanding Fees', 'route' => 'fees.outstanding.index', 'icon' => 'exclamation-triangle'],
            ['name' => 'Scholarships', 'route' => 'fees.scholarships.index', 'icon' => 'award'],
            ['name' => 'Scholarship Applications', 'route' => 'fees.scholarship-applications.index', 'icon' => 'file-earmark-check'],
            ['name' => 'Fee Reports', 'route' => 'fees.reports', 'icon' => 'graph-up'],
            
            // View Only Section
            ['name' => 'Holidays', 'route' => 'academic.holidays.index', 'icon' => 'calendar-event'],
        ],
        'accounts_staff' => [
            ['name' => 'Dashboard', 'route' => 'dashboard.accounts_staff', 'icon' => 'speedometer2'],
            ['name' => 'Fee Collection', 'route' => 'fees.payments.index', 'icon' => 'cash-stack'],
            ['name' => 'Outstanding Fees', 'route' => 'fees.outstanding.index', 'icon' => 'exclamation-triangle'],
            ['name' => 'Reports', 'route' => 'fees.reports', 'icon' => 'graph-up'],
            
            // View Only Section
            ['name' => 'Holidays', 'route' => 'academic.holidays.index', 'icon' => 'calendar-event'],
        ],
        'office' => [
            ['name' => 'Dashboard', 'route' => 'dashboard.office', 'icon' => 'speedometer2'],
            ['name' => 'Admissions', 'route' => 'admissions.index', 'icon' => 'people-fill'],
            ['name' => 'Students', 'route' => 'dashboard.students.index', 'icon' => 'people-fill'],
            
            // Timetable & Attendance Section
            ['name' => 'Timetable', 'route' => 'academic.timetable.grid', 'icon' => 'calendar-week'],
            ['name' => 'Attendance', 'route' => 'academic.attendance.index', 'icon' => 'clipboard-check'],
            ['name' => 'Holidays', 'route' => 'academic.holidays.index', 'icon' => 'calendar-event'],
        ],
        'librarian' => [
            ['name' => 'Dashboard', 'route' => 'dashboard.librarian', 'icon' => 'speedometer2'],
            ['name' => 'Books', 'route' => 'library.books.index', 'icon' => 'book'],
            ['name' => 'Issue Book', 'route' => 'library.issues.create', 'icon' => 'plus-circle'],
            ['name' => 'Return Books', 'route' => 'library.issues.index', 'icon' => 'arrow-return-left'],
            ['name' => 'Students', 'route' => 'library.students', 'icon' => 'people'],
            // View Only Section
            ['name' => 'Holidays', 'route' => 'academic.holidays.index', 'icon' => 'calendar-event'],
        ],
    ];
    
    // Fallback for librarian if not found
    if ($role === 'student' && $user->email === 'librarian@schoolerp.com') {
        $role = 'librarian';
    }
    
    $menuItems = $menuByRole[$role] ?? [];
    
    // Double check for librarian - if still empty, show librarian menu
    if (empty($menuItems) && ($user->hasRole('librarian') || $user->email === 'librarian@schoolerp.com')) {
        $menuItems = $menuByRole['librarian'];
        $role = 'librarian';
    }
@endphp

<!-- Desktop Sidebar -->
<div class="sidebar d-none d-md-flex flex-column text-white p-3">
    <div class="mb-4">
        <h5 class="d-flex align-items-center text-white">
            <i class="bi bi-mortarboard me-2"></i>
            School ERP
        </h5>
        <p class="small mb-0 text-white opacity-75">{{ ucfirst($role) }} Portal</p>
    </div>

    <hr class="my-3 border-white opacity-50">

    @foreach($menuItems as $item)
        <a href="{{ route($item['route']) }}" 
           class="d-flex align-items-center text-white mb-3 text-decoration-none p-2 rounded {{ request()->routeIs($item['route']) ? 'bg-white bg-opacity-20' : '' }}">
            <i class="bi bi-{{ $item['icon'] }} me-2"></i>
            {{ $item['name'] }}
        </a>
    @endforeach

    <div class="mt-auto pt-4">
        <hr class="my-3 border-white opacity-50">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-light w-100">
                <i class="bi bi-box-arrow-right me-2"></i>
                Logout
            </button>
        </form>
    </div>
</div>

<!-- Mobile Sidebar -->
<div x-show="sidebarOpen" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform -translate-x-full"
     x-transition:enter-end="opacity-100 transform translate-x-0"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="opacity-100 transform translate-x-0"
     x-transition:leave-end="opacity-0 transform -translate-x-full"
     class="mobile-sidebar position-fixed top-0 start-0 text-white p-3"
     style="width: 250px; height: 100vh; z-index: 1050;">
    
    <button class="btn btn-light btn-sm mb-3" @click="sidebarOpen = false">
        <i class="bi bi-x me-1"></i> Close
    </button>

    @foreach($menuItems as $item)
        <a href="{{ route($item['route']) }}" 
           class="d-flex align-items-center text-white d-block mb-3 p-2 rounded {{ request()->routeIs($item['route']) ? 'bg-white bg-opacity-20' : '' }}"
           @click="sidebarOpen = false">
            <i class="bi bi-{{ $item['icon'] }} me-2"></i>
            {{ $item['name'] }}
        </a>
    @endforeach
</div>

<!-- Mobile Overlay -->
<div x-show="sidebarOpen" 
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="position-fixed inset-0 bg-black bg-opacity-50 d-md-none"
     style="z-index: 1040;"
     @click="sidebarOpen = false"></div>
