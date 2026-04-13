<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LogbookEntryController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\ReportController;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
    
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/attendance', [DashboardController::class, 'attendance'])->name('dashboard.attendance');
    Route::get('/dashboard/logbook', [DashboardController::class, 'logbook'])->name('dashboard.logbook');
    Route::get('/dashboard/activities', [DashboardController::class, 'activities'])->name('dashboard.activities');
    Route::get('/dashboard/documents', [DashboardController::class, 'documents'])->name('dashboard.documents');
    Route::get('/dashboard/reports', [DashboardController::class, 'reports'])->name('dashboard.reports');
    
    // Shortcut route for reports
    Route::get('/reports', [DashboardController::class, 'reports'])->name('reports');
    
    // Attendance CRUD
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/create', [AttendanceController::class, 'create'])->name('attendance.create');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/{attendance}', [AttendanceController::class, 'show'])->name('attendance.show');
    Route::get('/attendance/{attendance}/edit', [AttendanceController::class, 'edit'])->name('attendance.edit');
    Route::put('/attendance/{attendance}', [AttendanceController::class, 'update'])->name('attendance.update');
    Route::delete('/attendance/{attendance}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');
    
    // Absence CRUD
    Route::get('/absence', [AbsenceController::class, 'index'])->name('absence.index');
    Route::post('/absence', [AbsenceController::class, 'store'])->name('absence.store');
    Route::get('/absence/pending', [AbsenceController::class, 'pending'])->name('absence.pending');
    Route::post('/absence/bulk-action', [AbsenceController::class, 'bulkAction'])->name('absence.bulkAction');
    Route::get('/absence/{student}', [AbsenceController::class, 'show'])->name('absence.show');
    Route::patch('/absence/{absence}/approve', [AbsenceController::class, 'approve'])->name('absence.approve');
    Route::patch('/absence/{absence}/reject', [AbsenceController::class, 'reject'])->name('absence.reject');
    
    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    
    // Logbook CRUD
    Route::get('/logbook', [LogbookEntryController::class, 'index'])->name('logbook.index');
    Route::get('/logbook/create', [LogbookEntryController::class, 'create'])->name('logbook.create');
    Route::post('/logbook', [LogbookEntryController::class, 'store'])->name('logbook.store');
    Route::get('/logbook/{entry}', [LogbookEntryController::class, 'show'])->name('logbook.show');
    Route::get('/logbook/{entry}/edit', [LogbookEntryController::class, 'edit'])->name('logbook.edit');
    Route::put('/logbook/{entry}', [LogbookEntryController::class, 'update'])->name('logbook.update');
    Route::delete('/logbook/{entry}', [LogbookEntryController::class, 'destroy'])->name('logbook.destroy');
    Route::post('/logbook/{entry}/submit', [LogbookEntryController::class, 'submit'])->name('logbook.submit');
    
    // Activities CRUD
    Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
    Route::get('/activities/create', [ActivityController::class, 'create'])->name('activities.create');
    Route::post('/activities', [ActivityController::class, 'store'])->name('activities.store');
    Route::get('/activities/{activity}', [ActivityController::class, 'show'])->name('activities.show');
    Route::get('/activities/{activity}/edit', [ActivityController::class, 'edit'])->name('activities.edit');
    Route::put('/activities/{activity}', [ActivityController::class, 'update'])->name('activities.update');
    Route::delete('/activities/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy');
    Route::post('/activities/{activity}/complete', [ActivityController::class, 'complete'])->name('activities.complete');
    
    // Admin Routes - Role & Feature Management
    Route::middleware('admin')->group(function () {
        // Roles Management
        Route::get('/admin/roles', [RoleController::class, 'index'])->name('admin.roles');
        Route::get('/admin/roles/create', [RoleController::class, 'create'])->name('admin.roles.create');
        Route::post('/admin/roles', [RoleController::class, 'store'])->name('admin.roles.store');
        Route::get('/admin/roles/{role}/edit', [RoleController::class, 'edit'])->name('admin.roles.edit');
        Route::put('/admin/roles/{role}', [RoleController::class, 'update'])->name('admin.roles.update');
        Route::delete('/admin/roles/{role}', [RoleController::class, 'destroy'])->name('admin.roles.destroy');
        
        // Features Management
        Route::get('/admin/features', [RoleController::class, 'features'])->name('admin.features');
        Route::post('/admin/features/{feature}/toggle', [RoleController::class, 'toggleFeature'])->name('admin.features.toggle');
        
        // Users Management
        Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users');
        Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
        Route::get('/admin/users/get-roles', [UserController::class, 'getRoles'])->name('admin.users.get-roles');
        Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::get('/admin/users/{user}', [UserController::class, 'show'])->name('admin.users.show');
        Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
        Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
        
        // API endpoints for modal
        Route::get('/admin/users/{user}/details', [UserController::class, 'getDetails'])->name('admin.users.details');
        Route::get('/admin/users/{user}/edit-data', [UserController::class, 'getEditData'])->name('admin.users.edit-data');
        Route::post('/admin/users/{user}/update-modal', [UserController::class, 'updateViaModal'])->name('admin.users.update-modal');
        
        // Users Import
        Route::get('/admin/users/import/form', [UserController::class, 'importForm'])->name('admin.users.import-form');
        Route::post('/admin/users/import', [UserController::class, 'import'])->name('admin.users.import');
    });
});

// Home/login redirect
Route::get('/', function () {
    return redirect('/dashboard');
});
