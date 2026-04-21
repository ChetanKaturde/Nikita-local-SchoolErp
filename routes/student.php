<?php

use App\Http\Controllers\Student\AuthController;
use App\Http\Controllers\Student\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Student Routes
|--------------------------------------------------------------------------
|
| These routes are for the student dashboard and are protected by
| the student auth middleware.
|
| Note: Guest routes (login) are in routes/web.php to avoid duplication
*/

// Authenticated student routes
Route::middleware('auth:student')->prefix('student')->name('student.')->group(function () {
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
    Route::get('/profile/edit', [DashboardController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');
    Route::get('/profile/change-password', [DashboardController::class, 'changePassword'])->name('profile.change-password');
    Route::post('/profile/change-password', [DashboardController::class, 'updatePassword'])->name('profile.update-password');
    
    // Timetable
    Route::get('/timetable', [DashboardController::class, 'timetable'])->name('timetable');
    
    // Attendance
    Route::get('/attendance', [DashboardController::class, 'attendance'])->name('attendance');
    
    // Fees
    Route::get('/fees', [DashboardController::class, 'fees'])->name('fees');
    Route::get('/fees/payment/{studentFee}', [DashboardController::class, 'feesPayment'])->name('fees.payment');
    Route::post('/fees/process-payment', [DashboardController::class, 'processPayment'])->name('fees.process-payment');
    
    // Results
    Route::get('/results', [DashboardController::class, 'results'])->name('results');
    
    // Library
    Route::get('/library', [DashboardController::class, 'library'])->name('library');
    
    // Notifications
    Route::get('/notifications', [DashboardController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/{id}/read', [DashboardController::class, 'markNotificationAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [DashboardController::class, 'markAllNotificationsAsRead'])->name('notifications.read-all');
});
