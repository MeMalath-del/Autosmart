<?php

use Illuminate\Support\Facades\Route;

// ===============================
// Phase 5 Extended Admin Routes
// ===============================

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    
    // Dynamic Pricing
    Route::prefix('pricing')->name('pricing.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\DynamicPricingController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\DynamicPricingController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\DynamicPricingController::class, 'store'])->name('store');
        Route::post('/apply', [App\Http\Controllers\Admin\DynamicPricingController::class, 'apply'])->name('apply');
        Route::get('/history', [App\Http\Controllers\Admin\DynamicPricingController::class, 'history'])->name('history');
        Route::delete('/{rule}', [App\Http\Controllers\Admin\DynamicPricingController::class, 'destroy'])->name('destroy');
    });

    // Inventory Alerts
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/alerts', [App\Http\Controllers\Admin\InventoryAlertController::class, 'index'])->name('alerts');
        Route::post('/alerts/{alert}/resolve', [App\Http\Controllers\Admin\InventoryAlertController::class, 'resolve'])->name('resolve');
        Route::post('/forecast', [App\Http\Controllers\Admin\InventoryAlertController::class, 'generateForecasts'])->name('forecast');
    });

    // Audit Logs
    Route::prefix('audit')->name('audit.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AuditController::class, 'index'])->name('index');
        Route::get('/{log}', [App\Http\Controllers\Admin\AuditController::class, 'show'])->name('show');
    });
});

// Digital Downloads
Route::prefix('digital')->name('digital.')->middleware('auth')->group(function () {
    Route::get('/downloads', [App\Http\Controllers\DigitalProductController::class, 'downloads'])->name('downloads');
    Route::get('/download/{token}', [App\Http\Controllers\DigitalProductController::class, 'download'])->name('download');
    Route::get('/preview/{digital}', [App\Http\Controllers\DigitalProductController::class, 'preview'])->name('preview');
});
