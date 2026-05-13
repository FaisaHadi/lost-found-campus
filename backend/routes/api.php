<?php

use App\Http\Controllers\Api\V1\Admin\ClaimModerationController;
use App\Http\Controllers\Api\V1\Admin\ReportModerationController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ClaimController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\ReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
        });
    });

    Route::get('/categories', [CategoryController::class, 'index']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('reports', ReportController::class);
        Route::apiResource('claims', ClaimController::class)->only(['index', 'store', 'show']);

        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);

        Route::middleware('role:admin')->group(function () {
            Route::post('/categories', [CategoryController::class, 'store']);
            Route::put('/categories/{category}', [CategoryController::class, 'update']);
            Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

            Route::patch('/admin/reports/{report}/approve', [ReportModerationController::class, 'approve']);
            Route::patch('/admin/reports/{report}/reject', [ReportModerationController::class, 'reject']);
            Route::patch('/admin/claims/{claim}/approve', [ClaimModerationController::class, 'approve']);
            Route::patch('/admin/claims/{claim}/reject', [ClaimModerationController::class, 'reject']);
        });
    });
});
