@php
    $adminMenuItems = [
        ['name' => 'Dashboard', 'route' => 'dashboard.admin', 'icon' => 'speedometer2'],
        ['name' => 'User Credentials', 'route' => 'admin.credentials', 'icon' => 'key'],
        ['name' => 'Users', 'route' => 'admin.users', 'icon' => 'people'],
        ['name' => 'Admissions', 'route' => 'admissions.index', 'icon' => 'person-plus-fill'],
        
        // Academic Management
        ['name' => 'Departments', 'route' => 'web.departments.index', 'icon' => 'building'],
        ['name' => 'Programs', 'route' => 'academic.programs.index', 'icon' => 'mortarboard'],
        ['name' => 'Subjects', 'route' => 'academic.subjects.index', 'icon' => 'book'],
        ['name' => 'Divisions', 'route' => 'academic.divisions.index', 'icon' => 'diagram-3'],
        ['name' => 'Academic Sessions', 'route' => 'academic.sessions.index', 'icon' => 'calendar-event'],
        
        // Students & Teachers
        ['name' => 'Students', 'route' => 'dashboard.students.index', 'icon' => 'people-fill'],
        ['name' => 'Teachers', 'route' => 'dashboard.teachers.index', 'icon' => 'person-badge-fill'],
        
        // Examinations
        ['name' => 'Exams', 'route' => 'academic.exams.index', 'icon' => 'clipboard-check'],
        ['name' => 'Results', 'route' => 'academic.results', 'icon' => 'bar-chart'],
        
        // Timetable & Attendance
        ['name' => 'Timetable', 'route' => 'academic.timetable.grid', 'icon' => 'calendar-week'],
        ['name' => 'Attendance', 'route' => 'academic.attendance.index', 'icon' => 'clipboard-check'],
        ['name' => 'Holidays', 'route' => 'academic.holidays.index', 'icon' => 'calendar-event'],
        
        // Library
        ['name' => 'Books', 'route' => 'library.books.index', 'icon' => 'book'],
        ['name' => 'Issues', 'route' => 'library.issues.index', 'icon' => 'clipboard-check'],
        
        // Accounts
        ['name' => 'Fee Collection', 'route' => 'accountant.fees', 'icon' => 'cash-stack'],
        ['name' => 'Expenses', 'route' => 'accountant.expenses', 'icon' => 'receipt'],
        
        // Reports & Settings
        ['name' => 'Reports', 'route' => 'reports.index', 'icon' => 'graph-up'],
        ['name' => 'System Settings', 'route' => 'admin.settings', 'icon' => 'gear'],
        ['name' => 'Roles & Permissions', 'route' => 'admin.roles', 'icon' => 'shield-check'],
    ];
@endphp

<!-- Admin Sidebar -->
<div class="sidebar d-none d-md-flex flex-column text-white p-3">
    <div class="mb-4">
        <h5 class="d-flex align-items-center text-white">
            <i class="bi bi-mortarboard me-2"></i>
            School ERP
        </h5>
        <p class="small mb-0 text-white opacity-75">Admin Portal</p>
    </div>

    <hr class="my-3 border-white opacity-50">

    @foreach($adminMenuItems as $item)
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

<!-- Admin Mobile Sidebar -->
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

    @foreach($adminMenuItems as $item)
        <a href="{{ route($item['route']) }}" 
           class="d-flex align-items-center text-white d-block mb-3 p-2 rounded {{ request()->routeIs($item['route']) ? 'bg-white bg-opacity-20' : '' }}"
           @click="sidebarOpen = false">
            <i class="bi bi-{{ $item['icon'] }} me-2"></i>
            {{ $item['name'] }}
        </a>
    @endforeach
</div>
