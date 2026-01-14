<?php

use Illuminate\Support\Facades\Route;

// ===============================
// Phase 5 Routes
// ===============================

// Bundles
Route::prefix('bundles')->name('bundles.')->group(function () {
    Route::get('/', [App\Http\Controllers\BundleController::class, 'index'])->name('index');
    Route::get('/{bundle}', [App\Http\Controllers\BundleController::class, 'show'])->name('show');
    Route::post('/{bundle}/cart', [App\Http\Controllers\BundleController::class, 'addToCart'])->name('cart');
});

// Preorders
Route::prefix('preorders')->name('preorders.')->middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\PreorderController::class, 'index'])->name('index');
    Route::post('/products/{product}', [App\Http\Controllers\PreorderController::class, 'store'])->name('store');
    Route::get('/{preorder}', [App\Http\Controllers\PreorderController::class, 'show'])->name('show');
    Route::post('/{preorder}/cancel', [App\Http\Controllers\PreorderController::class, 'cancel'])->name('cancel');
});

// Affiliate
Route::prefix('affiliate')->name('affiliate.')->group(function () {
    Route::get('/track/{code}', [App\Http\Controllers\AffiliateController::class, 'track'])->name('track');

    Route::middleware('auth')->group(function () {
        Route::get('/', [App\Http\Controllers\AffiliateController::class, 'index'])->name('index');
        Route::post('/apply', [App\Http\Controllers\AffiliateController::class, 'apply'])->name('apply');
        Route::post('/links', [App\Http\Controllers\AffiliateController::class, 'createLink'])->name('links.create');
        Route::post('/payout', [App\Http\Controllers\AffiliateController::class, 'requestPayout'])->name('payout');
    });
});

// Returns
Route::prefix('returns')->name('returns.')->middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\ReturnController::class, 'index'])->name('index');
    Route::get('/orders/{order}/create', [App\Http\Controllers\ReturnController::class, 'create'])->name('create');
    Route::post('/orders/{order}', [App\Http\Controllers\ReturnController::class, 'store'])->name('store');
    Route::get('/{return}', [App\Http\Controllers\ReturnController::class, 'show'])->name('show');
});

// Installation
Route::prefix('installation')->name('installation.')->middleware('auth')->group(function () {
    Route::get('/bookings', [App\Http\Controllers\InstallationController::class, 'bookings'])->name('bookings');
    Route::get('/orders/{order}/book', [App\Http\Controllers\InstallationController::class, 'book'])->name('book');
    Route::post('/orders/{order}', [App\Http\Controllers\InstallationController::class, 'store'])->name('store');
    Route::get('/{booking}', [App\Http\Controllers\InstallationController::class, 'show'])->name('show');
    Route::post('/{booking}/cancel', [App\Http\Controllers\InstallationController::class, 'cancel'])->name('cancel');
});

// Consultations
Route::prefix('consultations')->name('consultations.')->middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\ConsultationController::class, 'index'])->name('index');
    Route::get('/experts/{expert}/book', [App\Http\Controllers\ConsultationController::class, 'book'])->name('book');
    Route::post('/experts/{expert}', [App\Http\Controllers\ConsultationController::class, 'store'])->name('store');
    Route::get('/{session}', [App\Http\Controllers\ConsultationController::class, 'show'])->name('show');
    Route::post('/{session}/rate', [App\Http\Controllers\ConsultationController::class, 'rate'])->name('rate');
});
