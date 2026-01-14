<?php

use Illuminate\Support\Facades\Route;

// ===============================
// Admin Routes - Phase 3
// ===============================

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {

    // حملات البريد
    Route::prefix('campaigns')->name('campaigns.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\EmailCampaignController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\EmailCampaignController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\EmailCampaignController::class, 'store'])->name('store');
        Route::post('/{emailCampaign}/send', [App\Http\Controllers\Admin\EmailCampaignController::class, 'send'])->name('send');
        Route::delete('/{emailCampaign}', [App\Http\Controllers\Admin\EmailCampaignController::class, 'destroy'])->name('destroy');
    });

    // طلبات الاسترداد
    Route::prefix('refunds')->name('refunds.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\RefundController::class, 'index'])->name('index');
        Route::get('/{refundRequest}', [App\Http\Controllers\Admin\RefundController::class, 'show'])->name('show');
        Route::post('/{refundRequest}/approve', [App\Http\Controllers\Admin\RefundController::class, 'approve'])->name('approve');
        Route::post('/{refundRequest}/reject', [App\Http\Controllers\Admin\RefundController::class, 'reject'])->name('reject');
        Route::post('/{refundRequest}/process', [App\Http\Controllers\Admin\RefundController::class, 'process'])->name('process');
    });

    // ورش الصيانة
    Route::prefix('workshops')->name('workshops.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\WorkshopController::class, 'index'])->name('index');
        Route::get('/{workshop}', [App\Http\Controllers\Admin\WorkshopController::class, 'show'])->name('show');
        Route::post('/{workshop}/approve', [App\Http\Controllers\Admin\WorkshopController::class, 'approve'])->name('approve');
        Route::post('/{workshop}/suspend', [App\Http\Controllers\Admin\WorkshopController::class, 'suspend'])->name('suspend');
        Route::post('/{workshop}/toggle-featured', [App\Http\Controllers\Admin\WorkshopController::class, 'toggleFeatured'])->name('toggle-featured');
        Route::post('/{workshop}/toggle-verified', [App\Http\Controllers\Admin\WorkshopController::class, 'toggleVerified'])->name('toggle-verified');
    });

    // التحليلات
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('index');
        Route::get('/sales', [App\Http\Controllers\Admin\AnalyticsController::class, 'sales'])->name('sales');
    });
});
