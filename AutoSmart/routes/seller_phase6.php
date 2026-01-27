<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'seller'])->prefix('seller')->name('seller.')->group(function () {
    // Product Imports
    Route::prefix('imports')->name('imports.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Seller\ImportController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Seller\ImportController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Seller\ImportController::class, 'store'])->name('store');
        Route::get('/{import}/mapping', [\App\Http\Controllers\Seller\ImportController::class, 'mapping'])->name('mapping');
        Route::post('/{import}/process', [\App\Http\Controllers\Seller\ImportController::class, 'process'])->name('process');
        Route::get('/{import}', [\App\Http\Controllers\Seller\ImportController::class, 'show'])->name('show');
        Route::get('/template/download', [\App\Http\Controllers\Seller\ImportController::class, 'downloadTemplate'])->name('template');
    });
    
    // QR Code Generation
    Route::post('/products/{product}/generate-qr', [\App\Http\Controllers\QrController::class, 'generate'])->name('products.generate-qr');
});
