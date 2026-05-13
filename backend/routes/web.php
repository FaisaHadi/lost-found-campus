<?php

use App\Http\Controllers\Web\Admin\CategoryController;
use App\Http\Controllers\Web\Admin\ClaimController;
use App\Http\Controllers\Web\Admin\DashboardController;
use App\Http\Controllers\Web\Admin\NotificationController;
use App\Http\Controllers\Web\Admin\ReportController;
use App\Http\Controllers\Web\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin');
Route::redirect('/login', '/admin/login')->name('login');
Route::redirect('/dashboard', '/admin')->name('dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AuthenticatedSessionController::class, 'create'])->name('admin.login');
    Route::post('/admin/login', [AuthenticatedSessionController::class, 'store'])->name('admin.login.store');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
    Route::put('/reports/{report}', [ReportController::class, 'update'])->name('reports.update');
    Route::patch('/reports/{report}/approve', [ReportController::class, 'approve'])->name('reports.approve');
    Route::patch('/reports/{report}/reject', [ReportController::class, 'reject'])->name('reports.reject');

    Route::get('/claims', [ClaimController::class, 'index'])->name('claims.index');
    Route::get('/claims/{claim}', [ClaimController::class, 'show'])->name('claims.show');
    Route::patch('/claims/{claim}/approve', [ClaimController::class, 'approve'])->name('claims.approve');
    Route::patch('/claims/{claim}/reject', [ClaimController::class, 'reject'])->name('claims.reject');

    Route::resource('categories', CategoryController::class)->except(['show']);

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
});
