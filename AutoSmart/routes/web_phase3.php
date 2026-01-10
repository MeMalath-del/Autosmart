<?php

use Illuminate\Support\Facades\Route;

// ===============================
// الميزات الجديدة - المرحلة الثالثة
// ===============================

// ورش الصيانة (عام)
Route::get('/workshops', [App\Http\Controllers\WorkshopController::class, 'index'])->name('workshops.index');
Route::get('/workshops/{workshop:slug}', [App\Http\Controllers\WorkshopController::class, 'show'])->name('workshops.show');

Route::middleware('auth')->group(function () {
    
    // ===============================
    // سياراتي (My Garage)
    // ===============================
    Route::prefix('garage')->name('garage.')->group(function () {
        Route::get('/', [App\Http\Controllers\GarageController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\GarageController::class, 'store'])->name('store');
        Route::put('/{userCar}', [App\Http\Controllers\GarageController::class, 'update'])->name('update');
        Route::delete('/{userCar}', [App\Http\Controllers\GarageController::class, 'destroy'])->name('destroy');
        Route::post('/{userCar}/primary', [App\Http\Controllers\GarageController::class, 'setPrimary'])->name('primary');
        Route::get('/{userCar}/maintenance', [App\Http\Controllers\GarageController::class, 'maintenanceLog'])->name('maintenance');
        Route::post('/{userCar}/maintenance', [App\Http\Controllers\GarageController::class, 'addMaintenanceLog'])->name('maintenance.store');
    });

    // ===============================
    // القوائم المخصصة
    // ===============================
    Route::prefix('lists')->name('lists.')->group(function () {
        Route::get('/', [App\Http\Controllers\ProductListController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\ProductListController::class, 'store'])->name('store');
        Route::get('/{productList}', [App\Http\Controllers\ProductListController::class, 'show'])->name('show');
        Route::post('/{productList}/add', [App\Http\Controllers\ProductListController::class, 'addProduct'])->name('add');
        Route::delete('/{productList}/remove/{productId}', [App\Http\Controllers\ProductListController::class, 'removeProduct'])->name('remove');
        Route::post('/{productList}/cart', [App\Http\Controllers\ProductListController::class, 'addToCart'])->name('cart');
        Route::delete('/{productList}', [App\Http\Controllers\ProductListController::class, 'destroy'])->name('destroy');
    });

    // ===============================
    // نقاط الولاء
    // ===============================
    Route::prefix('loyalty')->name('loyalty.')->group(function () {
        Route::get('/', [App\Http\Controllers\LoyaltyController::class, 'index'])->name('index');
        Route::post('/redeem', [App\Http\Controllers\LoyaltyController::class, 'redeem'])->name('redeem');
    });

    // ===============================
    // الإحالات
    // ===============================
    Route::prefix('referral')->name('referral.')->group(function () {
        Route::get('/', [App\Http\Controllers\ReferralController::class, 'index'])->name('index');
        Route::post('/apply', [App\Http\Controllers\ReferralController::class, 'apply'])->name('apply');
    });

    // ===============================
    // طلبات الصيانة
    // ===============================
    Route::prefix('maintenance')->name('maintenance.')->group(function () {
        Route::get('/requests', [App\Http\Controllers\WorkshopController::class, 'maintenanceRequests'])->name('requests');
        Route::get('/requests/create', [App\Http\Controllers\WorkshopController::class, 'createRequest'])->name('requests.create');
        Route::post('/requests', [App\Http\Controllers\WorkshopController::class, 'storeRequest'])->name('requests.store');
        Route::get('/requests/{maintenanceRequest}', [App\Http\Controllers\WorkshopController::class, 'showRequest'])->name('requests.show');
        Route::post('/quotes/{quote}/accept', [App\Http\Controllers\WorkshopController::class, 'acceptQuote'])->name('quotes.accept');
        Route::get('/bookings', [App\Http\Controllers\WorkshopController::class, 'bookings'])->name('bookings');
    });

    // ===============================
    // طلبات الاسترداد
    // ===============================
    Route::prefix('refunds')->name('refunds.')->group(function () {
        Route::get('/', [App\Http\Controllers\RefundController::class, 'index'])->name('index');
        Route::get('/create/{order}', [App\Http\Controllers\RefundController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\RefundController::class, 'store'])->name('store');
        Route::get('/{refundRequest}', [App\Http\Controllers\RefundController::class, 'show'])->name('show');
    });

    // ===============================
    // الفواتير
    // ===============================
    Route::get('/invoices/{invoice}', [App\Http\Controllers\InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/download', [App\Http\Controllers\InvoiceController::class, 'download'])->name('invoices.download');

    // ===============================
    // المصادقة الثنائية
    // ===============================
    Route::prefix('two-factor')->name('two-factor.')->group(function () {
        Route::get('/', [App\Http\Controllers\TwoFactorController::class, 'index'])->name('index');
        Route::post('/enable', [App\Http\Controllers\TwoFactorController::class, 'enable'])->name('enable');
        Route::post('/disable', [App\Http\Controllers\TwoFactorController::class, 'disable'])->name('disable');
        Route::post('/verify', [App\Http\Controllers\TwoFactorController::class, 'verify'])->name('verify');
        Route::post('/send', [App\Http\Controllers\TwoFactorController::class, 'sendCode'])->name('send');
    });
});

// القوائم المشاركة (عام)
Route::get('/shared-list/{token}', [App\Http\Controllers\ProductListController::class, 'showShared'])->name('lists.shared');

// ===============================
// لوحة تحكم البائع - إضافات
// ===============================
Route::prefix('seller')->name('seller.')->middleware(['auth', 'role:seller,admin'])->group(function () {
    Route::middleware('seller')->group(function () {
        // المخزون
        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('/', [App\Http\Controllers\Seller\InventoryController::class, 'index'])->name('index');
            Route::get('/warehouses', [App\Http\Controllers\Seller\InventoryController::class, 'warehouses'])->name('warehouses');
            Route::post('/warehouses', [App\Http\Controllers\Seller\InventoryController::class, 'storeWarehouse'])->name('warehouses.store');
            Route::post('/adjust/{product}', [App\Http\Controllers\Seller\InventoryController::class, 'adjust'])->name('adjust');
            Route::get('/movements', [App\Http\Controllers\Seller\InventoryController::class, 'movements'])->name('movements');
        });

        // الموردين
        Route::prefix('suppliers')->name('suppliers.')->group(function () {
            Route::get('/', [App\Http\Controllers\Seller\SupplierController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\Seller\SupplierController::class, 'store'])->name('store');
            Route::get('/orders', [App\Http\Controllers\Seller\SupplierController::class, 'purchaseOrders'])->name('orders');
            Route::get('/orders/create', [App\Http\Controllers\Seller\SupplierController::class, 'createPurchaseOrder'])->name('orders.create');
            Route::post('/orders', [App\Http\Controllers\Seller\SupplierController::class, 'storePurchaseOrder'])->name('orders.store');
            Route::post('/orders/{purchaseOrder}/receive', [App\Http\Controllers\Seller\SupplierController::class, 'receivePurchaseOrder'])->name('orders.receive');
        });

        // التحليلات
        Route::prefix('analytics')->name('analytics.')->group(function () {
            Route::get('/', [App\Http\Controllers\Seller\AnalyticsController::class, 'index'])->name('index');
            Route::get('/products', [App\Http\Controllers\Seller\AnalyticsController::class, 'products'])->name('products');
        });
    });
});

// ===============================
// لوحة تحكم الورش
// ===============================
Route::prefix('workshop')->name('workshop.')->middleware(['auth', 'role:workshop,admin'])->group(function () {
    Route::get('/create', [App\Http\Controllers\Workshop\DashboardController::class, 'create'])->name('create');
    Route::post('/create', [App\Http\Controllers\Workshop\DashboardController::class, 'store'])->name('store');
    Route::get('/pending', [App\Http\Controllers\Workshop\DashboardController::class, 'pending'])->name('pending');

    Route::middleware('workshop')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Workshop\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/requests', [App\Http\Controllers\Workshop\DashboardController::class, 'requests'])->name('requests');
        Route::post('/requests/{maintenanceRequest}/quote', [App\Http\Controllers\Workshop\DashboardController::class, 'submitQuote'])->name('quote');
        Route::get('/bookings', [App\Http\Controllers\Workshop\DashboardController::class, 'bookings'])->name('bookings');
        Route::put('/bookings/{booking}', [App\Http\Controllers\Workshop\DashboardController::class, 'updateBooking'])->name('bookings.update');
    });
});
