<?php

use Illuminate\Support\Facades\Route;

// ===============================
// Phase 5 Final Routes
// ===============================

// Loyalty
Route::prefix('loyalty')->name('loyalty.')->middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\LoyaltyController::class, 'index'])->name('index');
    Route::post('/redeem', [App\Http\Controllers\LoyaltyController::class, 'redeem'])->name('redeem');
    Route::post('/earn', [App\Http\Controllers\LoyaltyController::class, 'earnFromAction'])->name('earn');
});

// Admin Accounting Routes
Route::prefix('admin/accounting')->name('admin.accounting.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\AccountingController::class, 'index'])->name('index');
    Route::get('/sales', [App\Http\Controllers\Admin\AccountingController::class, 'salesReport'])->name('sales');
    Route::get('/pos', [App\Http\Controllers\Admin\AccountingController::class, 'posReport'])->name('pos');
    Route::get('/vat', [App\Http\Controllers\Admin\AccountingController::class, 'vatReport'])->name('vat');
    Route::get('/export', [App\Http\Controllers\Admin\AccountingController::class, 'exportSales'])->name('export');
});
