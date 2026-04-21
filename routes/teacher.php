<?php

use App\Http\Controllers\Teacher\DashboardController;
use App\Http\Controllers\Teacher\AttendanceController;
use App\Http\Controllers\Web\Teacher\StudentsController;
use App\Http\Controllers\Web\ExaminationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Teacher Dashboard Routes
|--------------------------------------------------------------------------
|
| These routes are for the teacher dashboard and are protected by
| the 'auth' and role middleware.
|
*/

Route::middleware(['auth', 'role:admin|teacher|class_teacher|subject_teacher|hod_commerce|hod_science|hod_management|hod_arts'])->prefix('teacher')->name('teacher.')->group(function () {

    // Dashboard Home
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Teacher Profile
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
    Route::get('/profile/edit', [DashboardController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');

    // Teacher Settings
    Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
    Route::put('/settings', [DashboardController::class, 'updateSettings'])->name('settings.update');

    // Assigned Divisions
    Route::get('/divisions', [DashboardController::class, 'divisions'])->name('divisions.index');
    Route::get('/divisions/{divisionId}/students', [DashboardController::class, 'divisionStudents'])->name('divisions.students');

    // Student Details
    Route::get('/students/{studentId}', [DashboardController::class, 'studentDetails'])->name('students.details');

    // Examinations & Marks Entry
    Route::get('/examinations', [ExaminationController::class, 'teacherExaminations'])->name('examinations');

    // Results Management
    Route::prefix('results')->name('results.')->group(function () {
        // View all students' results for teacher's divisions
        Route::get('/', [ExaminationController::class, 'teacherResults'])->name('index');
        // View results by division
        Route::get('/division/{divisionId}', [ExaminationController::class, 'divisionResults'])->name('division');
        // Enter/Edit marks
        Route::get('/enter/{examinationId}/{divisionId}', [ExaminationController::class, 'enterMarks'])->name('enter');
        Route::post('/store-marks', [ExaminationController::class, 'storeMarks'])->name('store-marks');
    });

    // My Students (Resource routes)
    Route::get('/students', [StudentsController::class, 'index'])->name('students.index');
    Route::get('/students/{student}', [StudentsController::class, 'show'])->name('students.show');

    // Attendance Management
    Route::prefix('attendance')->name('attendance.')->group(function () {
        // Attendance Dashboard
        Route::get('/', [AttendanceController::class, 'index'])->name('index');

        // Mark Attendance (for specific timetable) - must be before wildcard routes
        Route::get('/create/{timetableId}', [AttendanceController::class, 'create'])->name('create');
        Route::post('/store/{timetableId}', [AttendanceController::class, 'store'])->name('store');
        // PUT route for updating attendance (uses same store method)
        Route::match(['put', 'post'], '/{timetableId}', [AttendanceController::class, 'store'])->name('update');

        // Attendance History
        Route::get('/history', [AttendanceController::class, 'history'])->name('history');
        
        // Download Reports
        Route::get('/history/download-excel', [AttendanceController::class, 'downloadExcel'])->name('history.download-excel');
        Route::get('/history/download-pdf', [AttendanceController::class, 'downloadPDF'])->name('history.download-pdf');

        // Attendance Report
        Route::get('/report', [AttendanceController::class, 'report'])->name('report');

        // Edit single attendance record - wildcard routes at the end
        Route::get('/record/{attendanceId}/edit', [AttendanceController::class, 'edit'])->name('record.edit');
        Route::put('/record/{attendanceId}', [AttendanceController::class, 'update'])->name('record.update');
    });
});
