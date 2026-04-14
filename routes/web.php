<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\StudentController;
use App\Http\Controllers\Web\AdmissionController;
use App\Http\Controllers\Web\GuardianController;
use App\Http\Controllers\Web\Academic\AcademicSessionController;
use App\Http\Controllers\Web\DepartmentController;
use App\Http\Controllers\Web\PrincipalDashboardController;
use App\Http\Controllers\Web\PrincipalStudentController;
use App\Http\Controllers\Web\PrincipalTeacherController;
use App\Http\Controllers\Web\HolidayController;
use App\Http\Controllers\Web\ExaminationController;
use App\Http\Controllers\Web\LibraryController;
use App\Http\Controllers\Web\StaffController;
use App\Http\Controllers\Web\TimeSlotController;
use App\Http\Controllers\Web\LeaveController;
use App\Http\Controllers\Teacher\AttendanceController;
use App\Http\Controllers\Web\AcademicRuleController;
use App\Http\Controllers\Student\AuthController as StudentAuthController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\LibrarianDashboardController;
use App\Http\Controllers\Web\DocumentDownloadController;

// Bulk action route - moved inside auth middleware
// Route::post('/dashboard/students/bulk-action', [StudentController::class, 'bulkAction'])->name('dashboard.students.bulkAction');
// Route::get('/dashboard/students/bulk-action', function() {
//     return redirect()->route('dashboard.students.index');
// });

// ============================================
// STUDENT AUTH ROUTES (Guest)
// ============================================
Route::middleware('guest:student')->group(function () {
    Route::get('/student/login', [StudentAuthController::class, 'showLogin'])->name('student.login');
    Route::post('/student/login', [StudentAuthController::class, 'login']);
});

// ============================================
// STUDENT AUTHENTICATED ROUTES
// ============================================
// Note: Student authenticated routes are now in routes/student.php
// to avoid route name conflicts and maintain consistency

// Librarian Routes
Route::middleware(['auth', 'role:librarian'])->prefix('librarian')->name('librarian.')->group(function () {
    // Librarian Dashboard
    Route::get('/dashboard', [LibrarianDashboardController::class, 'index'])->name('dashboard');
    
    // Librarian Profile
    Route::get('/profile', [LibrarianDashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [LibrarianDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/change-password', [LibrarianDashboardController::class, 'changePassword'])->name('profile.change-password');
    
    // Issued Books
    Route::get('/issued-books', [LibrarianDashboardController::class, 'issuedBooks'])->name('issued-books');
    
    // Students List
    Route::get('/students', [LibrarianDashboardController::class, 'students'])->name('students');
    Route::get('/students/{student}', [LibrarianDashboardController::class, 'studentDetails'])->name('student-details');
    Route::post('/students/{student}/contact', [LibrarianDashboardController::class, 'contactStudent'])->name('contact-student');
});

Route::prefix('dashboard/principal')
    ->name('principal.')
    ->middleware(['auth', 'role:principal|admin'])
    ->group(function () {

        Route::get('/', [PrincipalDashboardController::class, 'index'])
            ->name('dashboard');

        Route::post('/assign-division', [PrincipalDashboardController::class, 'assignDivision'])
            ->name('assign-division');

        Route::delete('/assignment/{id}', [PrincipalDashboardController::class, 'removeAssignment'])
            ->name('assignment.remove');

        // Timetable Management
        Route::post('/timetable/store', [PrincipalDashboardController::class, 'storeTimetable'])
            ->name('timetable.store');

        Route::put('/timetable/update/{id}', [PrincipalDashboardController::class, 'updateTimetable'])
            ->name('timetable.update');

        Route::delete('/timetable/delete/{id}', [PrincipalDashboardController::class, 'deleteTimetable'])
            ->name('timetable.delete');

        Route::get('/timetable', [PrincipalDashboardController::class, 'timetableIndex'])
            ->name('timetable.index');

        Route::resource('students', PrincipalStudentController::class);

        Route::resource('teachers', PrincipalTeacherController::class); // ✅ ADD THIS
});

// In routes/web.php, inside Route::middleware(['auth', 'admin'])->group(function () { ... });

// Attendance routes removed - duplicates exist in academic prefix group with proper auth

Route::middleware(['auth', 'role:admin|principal'])->group(function () {
    // Web-specific route names with 'web.' prefix
    Route::prefix('departments')
        ->name('web.departments.') // ← 'web.' prefix add kiya
        ->group(function () {
            Route::get('/', [DepartmentController::class, 'index'])->name('index');
            Route::get('/create', [DepartmentController::class, 'create'])->name('create');
            Route::post('/', [DepartmentController::class, 'store'])->name('store');
            Route::get('/{department}', [DepartmentController::class, 'show'])->name('show');
            Route::get('/{department}/edit', [DepartmentController::class, 'edit'])->name('edit');
            Route::put('/{department}', [DepartmentController::class, 'update'])->name('update');
            Route::delete('/{department}', [DepartmentController::class, 'destroy'])->name('destroy');
        });
});

// ============================================
// Role & Permission Management Routes
// ============================================
Route::middleware(['auth', 'role:super_admin|admin'])->prefix('admin')->name('admin.')->group(function () {
    // Roles Management
    Route::resource('roles', \App\Http\Controllers\Web\RoleController::class);
    Route::get('roles/{role}/permissions', [\App\Http\Controllers\Web\RoleController::class, 'permissions'])->name('roles.permissions');
    Route::put('roles/{role}/permissions', [\App\Http\Controllers\Web\RoleController::class, 'updatePermissions'])->name('roles.permissions.update');

    // Permissions Management
    Route::resource('permissions', \App\Http\Controllers\Web\PermissionController::class);

    // Activity Logs
    Route::get('activity-logs', [\App\Http\Controllers\Web\ActivityLogController::class, 'index'])->name('activity-logs.index');

    // Notifications Management
    Route::get('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/create', [\App\Http\Controllers\Admin\NotificationController::class, 'create'])->name('notifications.create');
    Route::post('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'store'])->name('notifications.store');
    Route::get('/notifications/{notification}', [\App\Http\Controllers\Admin\NotificationController::class, 'show'])->name('notifications.show');
    Route::get('/notifications/{notification}/edit', [\App\Http\Controllers\Admin\NotificationController::class, 'edit'])->name('notifications.edit');
    Route::put('/notifications/{notification}', [\App\Http\Controllers\Admin\NotificationController::class, 'update'])->name('notifications.update');
    Route::delete('/notifications/{notification}', [\App\Http\Controllers\Admin\NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::post('/notifications/{notification}/toggle', [\App\Http\Controllers\Admin\NotificationController::class, 'toggleActive'])->name('notifications.toggle');

    // Admin Profile
    Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/edit', [\App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [\App\Http\Controllers\Admin\ProfileController::class, 'editPassword'])->name('profile.edit-password');
    Route::post('/profile/change-password', [\App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('profile.update-password');

    // Admin Settings
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
    Route::get('/settings/system', [\App\Http\Controllers\Admin\SettingsController::class, 'system'])->name('settings.system');
    Route::post('/settings/clear-cache', [\App\Http\Controllers\Admin\SettingsController::class, 'clearCache'])->name('settings.clear-cache');

    // Admin Fee Management
    Route::get('/fees', [\App\Http\Controllers\Admin\FeeManagementController::class, 'index'])->name('fees');
    Route::get('/fees/structures', [\App\Http\Controllers\Admin\FeeManagementController::class, 'structures'])->name('fees.structures');
    Route::get('/fees/structures/create', [\App\Http\Controllers\Admin\FeeManagementController::class, 'createStructure'])->name('fees.structures.create');
    Route::post('/fees/structures', [\App\Http\Controllers\Admin\FeeManagementController::class, 'storeStructure'])->name('fees.structures.store');
    Route::get('/fees/student-fees', [\App\Http\Controllers\Admin\FeeManagementController::class, 'studentFees'])->name('fees.student-fees');
    Route::get('/fees/payments', [\App\Http\Controllers\Admin\FeeManagementController::class, 'payments'])->name('fees.payments');
    Route::get('/fees/outstanding', [\App\Http\Controllers\Admin\FeeManagementController::class, 'outstanding'])->name('fees.outstanding');
    Route::get('/fees/reports', [\App\Http\Controllers\Admin\FeeManagementController::class, 'reports'])->name('fees.reports');
    Route::post('/fees/pay', [\App\Http\Controllers\Admin\FeeManagementController::class, 'processPayment'])->name('fees.pay');
});

// Academic Management
Route::middleware(['auth'])->prefix('academic')->name('academic.')->group(function () {
    // Programs
    Route::resource('programs', \App\Http\Controllers\Web\ProgramController::class);
    Route::patch('programs/{program}/toggle-status', [\App\Http\Controllers\Web\ProgramController::class, 'toggleStatus'])
        ->name('programs.toggle-status');
    
    // Subjects
    Route::resource('subjects', \App\Http\Controllers\Web\SubjectController::class);
    
    // Divisions
    Route::resource('divisions', \App\Http\Controllers\Web\DivisionController::class);
    Route::patch('divisions/{division}/toggle-status', [\App\Http\Controllers\Web\DivisionController::class, 'toggleStatus'])
        ->name('divisions.toggle-status');
    Route::post('divisions/{division}/assign-students', [\App\Http\Controllers\Web\DivisionController::class, 'assignStudents'])
        ->name('divisions.assign-students');
    Route::delete('divisions/{division}/students/{student}', [\App\Http\Controllers\Web\DivisionController::class, 'removeStudent'])
        ->name('divisions.remove-student');
    Route::get('divisions/unassigned-students', [\App\Http\Controllers\Web\DivisionController::class, 'unassignedStudents'])
        ->name('divisions.unassigned-students');
    
    // Academic Sessions
    Route::resource('sessions', \App\Http\Controllers\Web\Academic\AcademicSessionController::class);
    Route::patch('sessions/{session}/toggle-status', [\App\Http\Controllers\Web\Academic\AcademicSessionController::class, 'toggleStatus'])
        ->name('sessions.toggle-status');
    
    // Attendance
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\AttendanceController::class, 'index'])->name('index');
        Route::get('mark', [\App\Http\Controllers\Web\AttendanceController::class, 'create'])->name('create');
        Route::post('mark', [\App\Http\Controllers\Web\AttendanceController::class, 'mark'])->name('mark');
        Route::post('store', [\App\Http\Controllers\Web\AttendanceController::class, 'store'])->name('store');
        Route::get('edit', [\App\Http\Controllers\Web\AttendanceController::class, 'edit'])->name('edit');
        Route::post('edit', [\App\Http\Controllers\Web\AttendanceController::class, 'edit'])->name('edit.post');
        Route::put('update', [\App\Http\Controllers\Web\AttendanceController::class, 'update'])->name('update');
        Route::delete('delete', [\App\Http\Controllers\Web\AttendanceController::class, 'destroy'])->name('destroy');
        Route::get('report', [\App\Http\Controllers\Web\AttendanceController::class, 'report'])->name('report');
        Route::post('check-holiday', [\App\Http\Controllers\Web\AttendanceController::class, 'checkHoliday'])->name('check-holiday');
        
        // Get students by division (AJAX)
        Route::get('division/{division}/students', [\App\Http\Controllers\Web\AttendanceController::class, 'getStudentsByDivision'])
            ->name('division.students');
            
        // Download attendance report
        Route::get('report/download', [\App\Http\Controllers\Web\AttendanceController::class, 'downloadReport'])
            ->name('report.download');
        Route::get('report/excel', [\App\Http\Controllers\Web\AttendanceController::class, 'downloadExcel'])
            ->name('report.excel');
    });

    // Timetable
    Route::prefix('timetable')->name('timetable.')->group(function () {
        // Main routes
        Route::get('/', [\App\Http\Controllers\Web\TimetableController::class, 'index'])->name('index');
        Route::get('/table', [\App\Http\Controllers\Web\TimetableController::class, 'tableView'])->name('table');
        Route::get('/grid', [\App\Http\Controllers\Web\TimetableController::class, 'gridView'])->name('grid');

        // CRUD operations
        Route::get('/create', [\App\Http\Controllers\Web\TimetableController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Web\TimetableController::class, 'store'])->name('store');
        
        // Teacher timetable route (must be before wildcard routes)
        Route::get('/teacher', [\App\Http\Controllers\Web\TimetableController::class, 'teacherTimetable'])->name('teacher');
        
        // Wildcard routes for individual timetable entries
        Route::get('/{timetable}', [\App\Http\Controllers\Web\TimetableController::class, 'show'])->name('show');
        Route::get('/{timetable}/edit', [\App\Http\Controllers\Web\TimetableController::class, 'edit'])->name('edit');
        Route::put('/{timetable}', [\App\Http\Controllers\Web\TimetableController::class, 'update'])->name('update');
        Route::delete('/{timetable}', [\App\Http\Controllers\Web\TimetableController::class, 'destroy'])->name('destroy');

        // AJAX endpoints
        Route::get('/ajax/get', [\App\Http\Controllers\Web\TimetableController::class, 'ajaxGetTimetable'])->name('ajax.get');
        Route::get('/ajax/available-slots', [\App\Http\Controllers\Web\TimetableController::class, 'ajaxGetAvailableSlots'])->name('ajax.available-slots');
        Route::post('/ajax/status', [\App\Http\Controllers\Web\TimetableController::class, 'ajaxUpdateStatus'])->name('ajax.status');
        Route::get('/ajax/check-holiday', [\App\Http\Controllers\Web\TimetableController::class, 'checkHoliday'])->name('ajax.check-holiday');
        Route::get('/ajax/get-by-date', [\App\Http\Controllers\Web\TimetableController::class, 'getByDate'])->name('ajax.get-by-date');
        
        // AJAX CRUD endpoints for grid modal
        Route::post('/ajax/store', [\App\Http\Controllers\Web\TimetableController::class, 'ajaxStore'])->name('ajax.store');
        Route::put('/ajax/update/{timetable}', [\App\Http\Controllers\Web\TimetableController::class, 'ajaxUpdate'])->name('ajax.update');
        Route::delete('/ajax/destroy/{timetable}', [\App\Http\Controllers\Web\TimetableController::class, 'ajaxDestroy'])->name('ajax.destroy');

        // Import/Export
        Route::get('/import', [\App\Http\Controllers\Web\TimetableController::class, 'importForm'])->name('import.form');
        Route::post('/import', [\App\Http\Controllers\Web\TimetableController::class, 'importExcel'])->name('import');
        Route::get('/export/pdf', [\App\Http\Controllers\Web\TimetableController::class, 'exportPdf'])->name('export.pdf');

        // Copy to next session
        Route::get('/copy', [\App\Http\Controllers\Web\TimetableController::class, 'copyToNextSessionForm'])->name('copy.form');
        Route::post('/copy', [\App\Http\Controllers\Web\TimetableController::class, 'copyToNextSession'])->name('copy');

        // Legacy routes
        Route::post('/check-availability', [\App\Http\Controllers\Web\TimetableController::class, 'checkAvailability'])->name('check-availability');
        Route::get('/division/{divisionId}', [\App\Http\Controllers\Web\TimetableController::class, 'show'])->name('show-division');
        Route::get('/division/{divisionId}/print', [\App\Http\Controllers\Web\TimetableController::class, 'print'])->name('print');
    });

    // Time Slot Management
    Route::resource('time-slots', \App\Http\Controllers\Web\TimeSlotController::class)->names('time-slots');

    // Holiday Management
    Route::resource('holidays', \App\Http\Controllers\Web\HolidayController::class)->names('holidays');
    Route::post('holidays/{holiday}/toggle-status', [\App\Http\Controllers\Web\HolidayController::class, 'toggleStatus'])->name('holidays.toggle-status');
    Route::get('holidays/check-date', [\App\Http\Controllers\Web\HolidayController::class, 'checkDate'])->name('holidays.check-date');
    
    // Leave Management
    Route::resource('leaves', \App\Http\Controllers\Web\LeaveController::class)->names('leaves');
    Route::get('leaves/my-leaves', [\App\Http\Controllers\Web\LeaveController::class, 'myLeaves'])->name('leaves.my-leaves');
    Route::post('leaves/{leave}/approve', [\App\Http\Controllers\Web\LeaveController::class, 'approve'])->name('leaves.approve');
    Route::post('leaves/{leave}/reject', [\App\Http\Controllers\Web\LeaveController::class, 'reject'])->name('leaves.reject');
});

// Teacher Attendance Routes - additional routes for teacher functionality
Route::middleware(['auth', 'role:teacher|class_teacher|subject_teacher|hod_commerce|hod_science|hod_management|hod_arts'])->group(function () {
    Route::get('/teacher/attendance/create/{timetableId}', [\App\Http\Controllers\Teacher\AttendanceController::class, 'create'])->name('teacher.attendance.create');
    Route::post('/teacher/attendance/store/{timetableId}', [\App\Http\Controllers\Teacher\AttendanceController::class, 'store'])->name('teacher.attendance.store');
});

// Academic Rules Management
Route::middleware(['auth', 'role:admin|principal'])->prefix('academic')->name('academic.')->group(function () {
    Route::resource('rules', \App\Http\Controllers\Web\AcademicRuleController::class);
    Route::post('rules/{rule}/toggle-status', [\App\Http\Controllers\Web\AcademicRuleController::class, 'toggleStatus'])->name('rules.toggle-status');
    Route::get('rules/clear-cache', [\App\Http\Controllers\Web\AcademicRuleController::class, 'clearCache'])->name('rules.clear-cache');
});

// Student Promotion Management
Route::middleware(['auth', 'role:admin|principal'])->prefix('academic')->name('academic.')->group(function () {
    Route::get('promotions', [\App\Http\Controllers\Web\PromotionController::class, 'index'])->name('promotions.index');
    Route::get('promotions/get-divisions', [\App\Http\Controllers\Web\PromotionController::class, 'getDivisions'])->name('promotions.getDivisions');
    Route::get('promotions/get-next-session', [\App\Http\Controllers\Web\PromotionController::class, 'getNextSession'])->name('promotions.getNextSession');
    Route::post('promotions/preview', [\App\Http\Controllers\Web\PromotionController::class, 'preview'])->name('promotions.preview');
    Route::post('promotions/promote', [\App\Http\Controllers\Web\PromotionController::class, 'promote'])->name('promotions.promote');
    Route::post('promotions/promote/{studentId}', [\App\Http\Controllers\Web\PromotionController::class, 'promoteStudent'])->name('promotions.promoteStudent');
    Route::get('promotions/history', [\App\Http\Controllers\Web\PromotionController::class, 'history'])->name('promotions.history');
    Route::post('promotions/{logId}/rollback', [\App\Http\Controllers\Web\PromotionController::class, 'rollback'])->name('promotions.rollback');
});

// Fee Management Routes
Route::middleware(['auth', 'role:admin|principal|office|teacher|accountant'])->prefix('fees')->name('fees.')->group(function () {
    // Fee Heads
    Route::resource('fee-heads', \App\Http\Controllers\Web\FeeHeadController::class)
        ->names('fee-heads');
    
    // Custom route for getting active fee heads (AJAX)
    Route::get('fee-heads/active', [\App\Http\Controllers\Web\FeeHeadController::class, 'getActive'])
        ->name('fee-heads.active');
    
    // Custom route for storing fee head via AJAX
    Route::post('fee-heads/ajax', [\App\Http\Controllers\Web\FeeHeadController::class, 'storeAjax'])
        ->name('fee-heads.store.ajax');
    
    // Custom route for updating fee head via AJAX
    Route::match(['put', 'post'], 'fee-heads/{feeHead}/ajax', [\App\Http\Controllers\Web\FeeHeadController::class, 'updateAjax'])
        ->name('fee-heads.update.ajax.post');
    
    // Custom route for deleting fee head via AJAX
    Route::delete('fee-heads/{feeHead}/ajax', [\App\Http\Controllers\Web\FeeHeadController::class, 'destroyAjax'])
        ->name('fee-heads.destroy.ajax.post');

    // Fee Structures
    Route::resource('structures', \App\Http\Controllers\Web\FeeStructureController::class)
        ->names('structures');

    // Fee Assignments
    Route::get('assignments', [\App\Http\Controllers\Web\FeeAssignmentController::class, 'index'])->name('assignments.index');
    Route::post('assignments', [\App\Http\Controllers\Web\FeeAssignmentController::class, 'store'])->name('assignments.store');

    // Payment Collection
    Route::get('payments', [\App\Http\Controllers\Web\FeePaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/create', [\App\Http\Controllers\Web\FeePaymentController::class, 'create'])->name('payments.create');
    Route::post('payments', [\App\Http\Controllers\Web\FeePaymentController::class, 'store'])->name('payments.store');
    Route::get('payments/{payment}/receipt', [\App\Http\Controllers\Web\FeePaymentController::class, 'receipt'])->name('payments.receipt');
    Route::get('payments/{payment}/download', [\App\Http\Controllers\Web\FeePaymentController::class, 'downloadReceipt'])->name('payments.download');

    // Outstanding Fees
    Route::get('outstanding', [\App\Http\Controllers\Web\FeeOutstandingController::class, 'index'])->name('outstanding.index');

    // Fee Reports
    Route::get('reports', [\App\Http\Controllers\Web\FeeReportController::class, 'index'])->name('reports');

    // Scholarships
    Route::resource('scholarships', \App\Http\Controllers\Web\ScholarshipController::class)
        ->names('scholarships');
});


// Razorpay Payment Routes
Route::middleware(['auth'])->prefix('razorpay')->group(function () {
    Route::post('/create-order', [\App\Http\Controllers\Web\RazorpayController::class, 'createOrder']);
    Route::post('/verify-payment', [\App\Http\Controllers\Web\RazorpayController::class, 'verifyPayment']);
});
Route::post('/razorpay/webhook', [\App\Http\Controllers\Web\RazorpayController::class, 'webhook']);

// Scholarship Application Routes
Route::middleware(['auth', 'role:admin|principal|office|teacher|accountant'])->prefix('fees/scholarship-applications')->name('fees.scholarship-applications.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Web\ScholarshipApplicationController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Web\ScholarshipApplicationController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\Web\ScholarshipApplicationController::class, 'store'])->name('store');
    Route::post('/{application}/approve', [\App\Http\Controllers\Web\ScholarshipApplicationController::class, 'approve'])->name('approve');
    Route::post('/{application}/reject', [\App\Http\Controllers\Web\ScholarshipApplicationController::class, 'reject'])->name('reject');
});


Route::middleware(['auth'])->group(function () {
    // Teacher Management
    Route::resource('dashboard/teachers', \App\Http\Controllers\Web\TeacherController::class)
        ->names('dashboard.teachers');

    // Bulk action route - inside auth middleware (must be before wildcard route)
    Route::post('/dashboard/students/bulk-action', [StudentController::class, 'bulkAction'])->name('dashboard.students.bulkAction');
    Route::delete('/dashboard/students/bulk-action', [StudentController::class, 'bulkAction']);
    Route::get('/dashboard/students/bulk-action', function() {
        return redirect()->route('dashboard.students.index');
    });
    
    // Dashboard student routes
    Route::get('/dashboard/students', [StudentController::class, 'index'])->name('dashboard.students.index');
    Route::get('/dashboard/students/create', [StudentController::class, 'create'])->name('dashboard.students.create');
    Route::post('/dashboard/students', [StudentController::class, 'store'])->name('dashboard.students.store');
    Route::get('/dashboard/students/{student}', [StudentController::class, 'show'])->name('dashboard.students.show');
    Route::get('/dashboard/students/{student}/edit', [StudentController::class, 'edit'])->name('dashboard.students.edit');
    Route::put('/dashboard/students/{student}', [StudentController::class, 'update'])->name('dashboard.students.update');
    Route::delete('/dashboard/students/{student}', [StudentController::class, 'destroy'])->name('dashboard.students.destroy');
});

// Guardian CRUD under student (dashboard prefixed) - use model binding
Route::middleware(['auth'])->prefix('dashboard/students/{student}/guardians')
    ->name('dashboard.students.guardians.')
    ->group(function () {
        Route::get('/create', [GuardianController::class, 'create'])->name('create');
        Route::post('/', [GuardianController::class, 'store'])->name('store');
        Route::get('/{guardian}/edit', [GuardianController::class, 'edit'])->name('edit');
        Route::put('/{guardian}', [GuardianController::class, 'update'])->name('update');
        Route::delete('/{guardian}', [GuardianController::class, 'destroy'])->name('destroy');
    });
// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/forgot-password', [\App\Http\Controllers\Web\PasswordResetController::class, 'showResetRequestForm'])->name('password.request');
Route::post('/forgot-password', [\App\Http\Controllers\Web\PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [\App\Http\Controllers\Web\PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [\App\Http\Controllers\Web\PasswordResetController::class, 'reset'])->name('password.update');

// Public admission form
Route::get('/apply', [AdmissionController::class, 'showApplyForm'])->name('admissions.apply.form');
Route::post('/apply', [AdmissionController::class, 'apply'])->name('admissions.apply');

// Root route - Show landing page
Route::get('/', function() {
    if (auth()->check()) {
        $user = auth()->user();
        $role = $user->roles->first()->name ?? 'student';

        // Fallback for librarian by email
        if ($role === 'student' && $user->email === 'librarian@schoolerp.com') {
            $role = 'librarian';
        }
        $redirectRoutes = [
            'principal'       => 'dashboard.principal',
            'admin'           => 'dashboard.admin',
            'teacher'         => 'teacher.dashboard',
            'class_teacher'   => 'teacher.dashboard',
            'subject_teacher' => 'teacher.dashboard',
            'student'         => 'student.dashboard',  // Changed to student.dashboard (student guard)
            'accountant'      => 'dashboard.accountant',
            'accounts_staff'  => 'dashboard.accounts_staff',
            'office'          => 'dashboard.office',
            'librarian'       => 'dashboard.librarian',
            'hod_commerce'    => 'teacher.dashboard',
            'hod_science'     => 'teacher.dashboard',
            'hod_management'  => 'teacher.dashboard',
            'hod_arts'        => 'teacher.dashboard',
        ];

        $route = $redirectRoutes[$role] ?? 'dashboard.student';

        return redirect()->route($route);
    }
    return view('landing');
});

// Protected Routes
Route::middleware(['auth'])->group(function () {
    
    Route::get('/dashboard/principal', [\App\Http\Controllers\Web\PrincipalDashboardController::class, 'index'])
        ->name('dashboard.principal');

    // Principal Timetable Routes
    Route::post('/principal/timetable/store', [\App\Http\Controllers\Web\PrincipalDashboardController::class, 'storeTimetable'])
        ->name('principal.timetable.store')
        ->middleware('role:principal|admin');

    Route::delete('/principal/timetable/delete/{timetableId}', [\App\Http\Controllers\Web\PrincipalDashboardController::class, 'deleteTimetable'])
        ->name('principal.timetable.delete')
        ->middleware('role:principal|admin');

    Route::post('/principal/assign-division', [\App\Http\Controllers\Web\PrincipalDashboardController::class, 'assignDivision'])
        ->name('principal.assign-division')
        ->middleware('role:principal|admin');

    Route::delete('/principal/remove-assignment/{assignmentId}', [\App\Http\Controllers\Web\PrincipalDashboardController::class, 'removeAssignment'])
        ->name('principal.remove-assignment')
        ->middleware('role:principal|admin');

    // Principal/Admin Results Routes
    Route::get('/principal/results', [\App\Http\Controllers\Web\PrincipalDashboardController::class, 'results'])
        ->name('principal.results')
        ->middleware('role:principal|admin');
    
    Route::get('/dashboard/admin', [\App\Http\Controllers\Web\PrincipalDashboardController::class, 'index'])
        ->name('dashboard.admin')
        ->middleware('role:admin');
    
    // Admin Credentials Management (Admin Only)
    Route::middleware(['auth', 'role:admin'])->prefix('admin/credentials')->name('admin.credentials.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\AdminController::class, 'credentials'])->name('index');
        Route::post('/reset-password/{userId}', [\App\Http\Controllers\Web\AdminController::class, 'resetPassword'])->name('reset-password');
        Route::post('/send-credentials/{userId}', [\App\Http\Controllers\Web\AdminController::class, 'sendCredentials'])->name('send-credentials');
        Route::get('/export', [\App\Http\Controllers\Web\AdminController::class, 'exportCredentials'])->name('export');
    });

    // Teacher Dashboard Routes are in routes/teacher.php

    Route::get('/dashboard/student', [DashboardController::class, 'student'])->name('dashboard.student');
    Route::get('/dashboard/office', [DashboardController::class, 'office'])->name('dashboard.office')->middleware('role:office');
    Route::get('/dashboard/accounts_staff', [DashboardController::class, 'accounts_staff'])->name('dashboard.accounts_staff')->middleware('role:accounts_staff');
    Route::get('/dashboard/accountant', [DashboardController::class, 'accountant'])->name('dashboard.accountant')->middleware('role:accountant');
    Route::get('/accountant/profile', [DashboardController::class, 'accountantProfile'])->name('accountant.profile')->middleware('role:accountant');
    Route::post('/accountant/profile/change-password', [DashboardController::class, 'accountantChangePassword'])->name('accountant.change-password')->middleware('role:accountant');
    Route::get('/dashboard/librarian', [DashboardController::class, 'librarian'])->name('dashboard.librarian')->middleware('role:librarian');
    
    // Student Management
    // (handled earlier with explicit dashboard routes)
    // Route::resource('students', StudentController::class);
    // Route::get('/dashboard/students', [StudentController::class, 'index'])->name('dashboard.students');
    
    // Admission Management
    Route::get('/admissions', [AdmissionController::class, 'index'])->name('admissions.index');
    Route::get('/admissions/{admission}', [AdmissionController::class, 'show'])->name('admissions.show');
    Route::post('/admissions/{admission}/verify', [AdmissionController::class, 'verify'])->name('admissions.verify');
    Route::post('/admissions/{admission}/reject', [AdmissionController::class, 'reject'])->name('admissions.reject');
    Route::post('/admissions/{admission}/enroll', [AdmissionController::class, 'enroll'])->name('admissions.enroll');

    // Document Download Routes (Authenticated & Authorized)
    Route::prefix('documents')->name('documents.')->group(function () {
        // Student documents
        Route::get('/students/{student}/{documentType}', [DocumentDownloadController::class, 'downloadStudentDocument'])
            ->where('documentType', 'photo|signature|cast_certificate|marksheet|aadhar|income_certificate|domicile_certificate')
            ->name('students.document');

        // Guardian photo
        Route::get('/students/{student}/guardians/{guardian}/photo', [DocumentDownloadController::class, 'downloadGuardianDocument'])
            ->name('guardians.photo');
    });
});


// ============================================
// NEW MODULES - Added for Complete ERP System
// ============================================

// Examination Management
Route::middleware(['auth', 'role:admin|principal|teacher|class_teacher|subject_teacher|hod_commerce|hod_science|hod_management|hod_arts'])->prefix('examinations')->name('examinations.')->group(function () {
    Route::get('/', [ExaminationController::class, 'index'])->name('index');
    Route::get('/create', [ExaminationController::class, 'create'])->name('create');
    Route::post('/', [ExaminationController::class, 'store'])->name('store');
    Route::get('/{examination}', [ExaminationController::class, 'show'])->name('show');
    Route::get('/{examination}/edit', [ExaminationController::class, 'edit'])->name('edit');
    Route::put('/{examination}', [ExaminationController::class, 'update'])->name('update');
    Route::get('/{examination}/marks-entry', [ExaminationController::class, 'marksEntry'])->name('marks-entry');
    Route::post('/{examination}/save-marks', [ExaminationController::class, 'saveMarks'])->name('save-marks');
    Route::delete('/{examination}', [ExaminationController::class, 'destroy'])->name('destroy');

    // Draft marks endpoints for auto-save
    Route::post('/save-draft', [ExaminationController::class, 'saveDraft'])->name('save-draft');
    Route::get('/load-drafts', [ExaminationController::class, 'loadDrafts'])->name('load-drafts');
    Route::post('/clear-drafts', [ExaminationController::class, 'clearDrafts'])->name('clear-drafts');
});

// Results Management
Route::middleware(['auth'])->prefix('results')->name('results.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Web\ResultController::class, 'index'])->name('index');
    Route::get('/generate', [\App\Http\Controllers\Web\ResultController::class, 'generate'])->name('generate');
    Route::get('/pdf', [\App\Http\Controllers\Web\ResultController::class, 'pdf'])->name('pdf');
    Route::get('/student/{student}', [\App\Http\Controllers\Web\ResultController::class, 'studentResult'])->name('student');
});

// Reports Management
Route::middleware(['auth'])->prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Web\ReportController::class, 'index'])->name('index');
    Route::get('/attendance', [\App\Http\Controllers\Web\ReportController::class, 'attendance'])->name('attendance');
    Route::get('/attendance/pdf', [\App\Http\Controllers\Web\ReportController::class, 'attendancePdf'])->name('attendance.pdf');
    Route::get('/attendance/excel', [\App\Http\Controllers\Web\ReportController::class, 'attendanceExcel'])->name('attendance.excel');
});

// Library Management
Route::middleware(['auth'])->prefix('library')->name('library.')->group(function () {
    // Students - Librarian only
    Route::get('/students', [LibraryController::class, 'students'])->name('students');
    
    // Books
    Route::prefix('books')->name('books.')->group(function () {
        Route::get('/', [LibraryController::class, 'index'])->name('index');
        Route::get('/create', [LibraryController::class, 'create'])->name('create');
        Route::post('/', [LibraryController::class, 'store'])->name('store');
        Route::get('/{book}/edit', [LibraryController::class, 'edit'])->name('edit');
        Route::put('/{book}', [LibraryController::class, 'update'])->name('update');
        Route::delete('/{book}', [LibraryController::class, 'destroy'])->name('destroy');
    });
    
    // Book Issues
    Route::prefix('issues')->name('issues.')->group(function () {
        Route::get('/', [LibraryController::class, 'issuesIndex'])->name('index');
        Route::get('/create', [LibraryController::class, 'issueForm'])->name('create');
        Route::post('/', [LibraryController::class, 'issue'])->name('store');
        Route::post('/{issue}/return', [LibraryController::class, 'returnBook'])->name('return');
    });
});

// Staff Management
Route::middleware(['auth'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [StaffController::class, 'index'])->name('dashboard');
    Route::get('/', [StaffController::class, 'index'])->name('index');
    Route::get('/create', [StaffController::class, 'create'])->name('create');
    Route::post('/', [StaffController::class, 'store'])->name('store');
    Route::get('/{staff}', [StaffController::class, 'show'])->name('show');
    Route::get('/{staff}/edit', [StaffController::class, 'edit'])->name('edit');
    Route::put('/{staff}', [StaffController::class, 'update'])->name('update');
    Route::delete('/{staff}', [StaffController::class, 'destroy'])->name('destroy');
});

// Teacher Dashboard Routes
require __DIR__ . '/teacher.php';

// Student Dashboard Routes
require __DIR__ . '/student.php';

