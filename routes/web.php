<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LogbookEntryController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\RoleController;

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
    
    // Attendance CRUD
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/create', [AttendanceController::class, 'create'])->name('attendance.create');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/{attendance}', [AttendanceController::class, 'show'])->name('attendance.show');
    Route::get('/attendance/{attendance}/edit', [AttendanceController::class, 'edit'])->name('attendance.edit');
    Route::put('/attendance/{attendance}', [AttendanceController::class, 'update'])->name('attendance.update');
    Route::delete('/attendance/{attendance}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');
    
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
        Route::get('/admin/roles/{role}/edit', [RoleController::class, 'edit'])->name('admin.roles.edit');
        Route::put('/admin/roles/{role}', [RoleController::class, 'update'])->name('admin.roles.update');
        
        // Features Management
        Route::get('/admin/features', [RoleController::class, 'features'])->name('admin.features');
        Route::post('/admin/features/{feature}/toggle', [RoleController::class, 'toggleFeature'])->name('admin.features.toggle');
    });
    
    // Documents CRUD
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/create', [DocumentController::class, 'create'])->name('documents.create');
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.show');
    Route::get('/documents/{document}/edit', [DocumentController::class, 'edit'])->name('documents.edit');
    Route::put('/documents/{document}', [DocumentController::class, 'update'])->name('documents.update');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
});

// Home/login redirect
Route::get('/', function () {
    return redirect('/dashboard');
});
