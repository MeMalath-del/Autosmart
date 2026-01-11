<?php

use Illuminate\Support\Facades\Route;

// ===============================
// Phase 5 Extended Routes
// ===============================

// Trade-In
Route::prefix('trade-in')->name('trade-in.')->middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\TradeInController::class, 'index'])->name('index');
    Route::get('/products/{product}/create', [App\Http\Controllers\TradeInController::class, 'create'])->name('create');
    Route::post('/products/{product}', [App\Http\Controllers\TradeInController::class, 'store'])->name('store');
    Route::get('/{tradeIn}', [App\Http\Controllers\TradeInController::class, 'show'])->name('show');
});

// Part Lifecycle
Route::prefix('parts')->name('parts.')->middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\PartLifecycleController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\PartLifecycleController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\PartLifecycleController::class, 'store'])->name('store');
    Route::put('/{part}', [App\Http\Controllers\PartLifecycleController::class, 'update'])->name('update');
});

// Comparison Page
Route::get('/compare', function () {
    return view('compare');
})->name('compare');
