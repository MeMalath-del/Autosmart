<?php

use Illuminate\Support\Facades\Route;

// ===============================
// Phase 5 Admin Routes
// ===============================

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {

    // Affiliates
    Route::prefix('affiliates')->name('affiliates.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AffiliateController::class, 'index'])->name('index');
        Route::get('/{affiliate}', [App\Http\Controllers\Admin\AffiliateController::class, 'show'])->name('show');
        Route::post('/{affiliate}/approve', [App\Http\Controllers\Admin\AffiliateController::class, 'approve'])->name('approve');
        Route::post('/{affiliate}/commission', [App\Http\Controllers\Admin\AffiliateController::class, 'updateCommission'])->name('commission');
        Route::get('/payouts/list', [App\Http\Controllers\Admin\AffiliateController::class, 'payouts'])->name('payouts');
        Route::post('/payouts/{payout}/process', [App\Http\Controllers\Admin\AffiliateController::class, 'processPayout'])->name('payouts.process');
    });

    // Returns
    Route::prefix('returns')->name('returns.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ReturnController::class, 'index'])->name('index');
        Route::get('/{return}', [App\Http\Controllers\Admin\ReturnController::class, 'show'])->name('show');
        Route::post('/{return}/approve', [App\Http\Controllers\Admin\ReturnController::class, 'approve'])->name('approve');
        Route::post('/{return}/reject', [App\Http\Controllers\Admin\ReturnController::class, 'reject'])->name('reject');
        Route::post('/{return}/received', [App\Http\Controllers\Admin\ReturnController::class, 'markReceived'])->name('received');
        Route::post('/{return}/complete', [App\Http\Controllers\Admin\ReturnController::class, 'complete'])->name('complete');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\NotificationController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\NotificationController::class, 'store'])->name('store');
        Route::get('/{notification}', [App\Http\Controllers\Admin\NotificationController::class, 'show'])->name('show');
        Route::post('/{notification}/send', [App\Http\Controllers\Admin\NotificationController::class, 'send'])->name('send');
    });
});
