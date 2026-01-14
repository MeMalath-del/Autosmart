<?php

use Illuminate\Support\Facades\Route;

// ===============================
// Phase 5 Seller Routes
// ===============================

Route::prefix('seller')->name('seller.')->middleware(['auth', 'seller'])->group(function () {

    // Branches
    Route::prefix('branches')->name('branches.')->group(function () {
        Route::get('/', [App\Http\Controllers\Seller\BranchController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Seller\BranchController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Seller\BranchController::class, 'store'])->name('store');
        Route::get('/{branch}/edit', [App\Http\Controllers\Seller\BranchController::class, 'edit'])->name('edit');
        Route::put('/{branch}', [App\Http\Controllers\Seller\BranchController::class, 'update'])->name('update');
        Route::get('/{branch}/inventory', [App\Http\Controllers\Seller\BranchController::class, 'inventory'])->name('inventory');
    });

    // POS
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/', [App\Http\Controllers\Seller\PosController::class, 'index'])->name('index');
        Route::post('/sessions', [App\Http\Controllers\Seller\PosController::class, 'openSession'])->name('open');
        Route::get('/sessions/{session}', [App\Http\Controllers\Seller\PosController::class, 'terminal'])->name('terminal');
        Route::post('/sessions/{session}/sale', [App\Http\Controllers\Seller\PosController::class, 'sale'])->name('sale');
        Route::post('/sessions/{session}/close', [App\Http\Controllers\Seller\PosController::class, 'closeSession'])->name('close');
        Route::get('/history', [App\Http\Controllers\Seller\PosController::class, 'history'])->name('history');
    });

    // Bundles
    Route::prefix('bundles')->name('bundles.')->group(function () {
        Route::get('/', [App\Http\Controllers\Seller\BundleController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Seller\BundleController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Seller\BundleController::class, 'store'])->name('store');
        Route::get('/{bundle}/edit', [App\Http\Controllers\Seller\BundleController::class, 'edit'])->name('edit');
        Route::delete('/{bundle}', [App\Http\Controllers\Seller\BundleController::class, 'destroy'])->name('destroy');
    });
});
