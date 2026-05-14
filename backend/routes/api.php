<?php

use App\Http\Controllers\Api\V1\Admin\ClaimModerationController;
use App\Http\Controllers\Api\V1\Admin\ReportModerationController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ClaimController;
use App\Http\Controllers\Api\V1\DeviceController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\ReportController;
use Illuminate\Support\Facades\Route;

$lostFoundRoutes = function (): void {
    Route::prefix('auth')->group(function (): void {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);

        Route::middleware('auth:api')->group(function (): void {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
        });
    });

    Route::get('/categories', [CategoryController::class, 'index']);

    Route::middleware('auth:api')->group(function (): void {
        Route::apiResource('reports', ReportController::class);
        Route::apiResource('claims', ClaimController::class)->only(['index', 'store', 'show']);

        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);

        // Platform-specific mobile/web push token storage.
        Route::post('/devices', [DeviceController::class, 'store']);

        Route::middleware('role:admin')->group(function (): void {
            Route::post('/categories', [CategoryController::class, 'store']);
            Route::put('/categories/{category}', [CategoryController::class, 'update']);
            Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

            // Endpoint sesuai laporan teknis.
            Route::put('/reports/{report}/verify', [ReportModerationController::class, 'approve']);
            Route::put('/reports/{report}/reject', [ReportModerationController::class, 'reject']);
            Route::put('/claims/{claim}/approve', [ClaimModerationController::class, 'approve']);
            Route::put('/claims/{claim}/reject', [ClaimModerationController::class, 'reject']);

            // Backward compatibility dengan route admin repo lama.
            Route::patch('/admin/reports/{report}/approve', [ReportModerationController::class, 'approve']);
            Route::patch('/admin/reports/{report}/reject', [ReportModerationController::class, 'reject']);
            Route::patch('/admin/claims/{claim}/approve', [ClaimModerationController::class, 'approve']);
            Route::patch('/admin/claims/{claim}/reject', [ClaimModerationController::class, 'reject']);
        });
    });
};

// Endpoint sesuai laporan: /api/auth/login, /api/reports, dst.
Route::group([], $lostFoundRoutes);

// Backward compatibility untuk mobile lama: /api/v1/...
Route::prefix('v1')->group($lostFoundRoutes);
