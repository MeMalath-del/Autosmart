<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Fraud Detection
    Route::prefix('fraud')->name('fraud.')->group(function () {
        Route::get('/alerts', [\App\Http\Controllers\Admin\FraudController::class, 'alerts'])->name('alerts');
        Route::get('/alerts/{alert}', [\App\Http\Controllers\Admin\FraudController::class, 'showAlert'])->name('show-alert');
        Route::post('/alerts/{alert}/resolve', [\App\Http\Controllers\Admin\FraudController::class, 'resolveAlert'])->name('resolve-alert');
        Route::get('/rules', [\App\Http\Controllers\Admin\FraudController::class, 'rules'])->name('rules');
        Route::get('/rules/create', [\App\Http\Controllers\Admin\FraudController::class, 'createRule'])->name('create-rule');
        Route::post('/rules', [\App\Http\Controllers\Admin\FraudController::class, 'storeRule'])->name('store-rule');
        Route::post('/rules/{rule}/toggle', [\App\Http\Controllers\Admin\FraudController::class, 'toggleRule'])->name('toggle-rule');
        Route::delete('/rules/{rule}', [\App\Http\Controllers\Admin\FraudController::class, 'deleteRule'])->name('delete-rule');
    });
    
    // KYC Verification
    Route::prefix('kyc')->name('kyc.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\KycController::class, 'index'])->name('index');
        Route::get('/{verification}', [\App\Http\Controllers\Admin\KycController::class, 'show'])->name('show');
        Route::post('/{verification}/start-review', [\App\Http\Controllers\Admin\KycController::class, 'startReview'])->name('start-review');
        Route::post('/{verification}/approve', [\App\Http\Controllers\Admin\KycController::class, 'approve'])->name('approve');
        Route::post('/{verification}/reject', [\App\Http\Controllers\Admin\KycController::class, 'reject'])->name('reject');
    });
    
    // Installment Management
    Route::prefix('installments')->name('installments.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\InstallmentAdminController::class, 'index'])->name('index');
        Route::get('/plans', [\App\Http\Controllers\Admin\InstallmentAdminController::class, 'plans'])->name('plans');
        Route::get('/plans/create', [\App\Http\Controllers\Admin\InstallmentAdminController::class, 'createPlan'])->name('create-plan');
        Route::post('/plans', [\App\Http\Controllers\Admin\InstallmentAdminController::class, 'storePlan'])->name('store-plan');
        Route::post('/plans/{plan}/toggle', [\App\Http\Controllers\Admin\InstallmentAdminController::class, 'togglePlan'])->name('toggle-plan');
        Route::get('/overdue', [\App\Http\Controllers\Admin\InstallmentAdminController::class, 'overduePayments'])->name('overdue');
        Route::get('/{installment}', [\App\Http\Controllers\Admin\InstallmentAdminController::class, 'show'])->name('show');
        Route::post('/{installment}/approve', [\App\Http\Controllers\Admin\InstallmentAdminController::class, 'approve'])->name('approve');
        Route::post('/{installment}/reject', [\App\Http\Controllers\Admin\InstallmentAdminController::class, 'reject'])->name('reject');
    });
});
