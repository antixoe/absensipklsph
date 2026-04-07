<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\LogbookEntryController;
use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\DocumentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Authentication routes (public)
    Route::post('/auth/register-student', [AuthController::class, 'registerStudent']);
    Route::post('/auth/register-instructor', [AuthController::class, 'registerInstructor']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Protected routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        // Auth routes
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/refresh', [AuthController::class, 'refresh']);

        // Attendance routes
        Route::get('/attendance', [AttendanceController::class, 'index']);
        Route::post('/attendance', [AttendanceController::class, 'store']);
        Route::get('/attendance/{attendance}', [AttendanceController::class, 'show']);
        Route::put('/attendance/{attendance}', [AttendanceController::class, 'update']);
        Route::delete('/attendance/{attendance}', [AttendanceController::class, 'destroy']);
        Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn']);
        Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut']);
        Route::get('/attendance/report/{student}', [AttendanceController::class, 'report']);

        // Logbook routes
        Route::get('/logbook', [LogbookEntryController::class, 'index']);
        Route::post('/logbook', [LogbookEntryController::class, 'store']);
        Route::get('/logbook/{logbookEntry}', [LogbookEntryController::class, 'show']);
        Route::put('/logbook/{logbookEntry}', [LogbookEntryController::class, 'update']);
        Route::delete('/logbook/{logbookEntry}', [LogbookEntryController::class, 'destroy']);
        Route::post('/logbook/{logbookEntry}/submit', [LogbookEntryController::class, 'submit']);
        Route::post('/logbook/{logbookEntry}/approve', [LogbookEntryController::class, 'approve']);
        Route::post('/logbook/{logbookEntry}/reject', [LogbookEntryController::class, 'reject']);

        // Activity routes
        Route::get('/activities', [ActivityController::class, 'index']);
        Route::post('/activities', [ActivityController::class, 'store']);
        Route::get('/activities/{activity}', [ActivityController::class, 'show']);
        Route::put('/activities/{activity}', [ActivityController::class, 'update']);
        Route::delete('/activities/{activity}', [ActivityController::class, 'destroy']);
        Route::post('/activities/{activity}/complete', [ActivityController::class, 'complete']);

        // Document routes
        Route::get('/documents', [DocumentController::class, 'index']);
        Route::post('/documents', [DocumentController::class, 'store']);
        Route::get('/documents/{document}', [DocumentController::class, 'show']);
        Route::put('/documents/{document}', [DocumentController::class, 'update']);
        Route::delete('/documents/{document}', [DocumentController::class, 'destroy']);
        Route::post('/documents/{document}/approve', [DocumentController::class, 'approve']);
        Route::post('/documents/{document}/reject', [DocumentController::class, 'reject']);
        Route::get('/documents/{document}/download', [DocumentController::class, 'download']);
    });
});
