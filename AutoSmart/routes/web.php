<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Seller\DashboardController as SellerDashboardController;
use App\Http\Controllers\Seller\ProductController as SellerProductController;
use App\Http\Controllers\Seller\OrderController as SellerOrderController;
use App\Http\Controllers\Seller\StoreController as SellerStoreController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\StoreController as AdminStoreController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CarBrandController as AdminCarBrandController;

// الصفحات العامة
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/terms', [HomeController::class, 'terms'])->name('terms');
Route::get('/privacy', [HomeController::class, 'privacy'])->name('privacy');

// المنتجات
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/category/{category:slug}', [ProductController::class, 'category'])->name('products.category');
Route::get('/search', [ProductController::class, 'search'])->name('products.search');

// المتاجر
Route::get('/stores', [StoreController::class, 'index'])->name('stores.index');
Route::get('/stores/{store:slug}', [StoreController::class, 'show'])->name('stores.show');

// السلة والدفع
Route::get('/cart', fn() => view('cart'))->name('cart');
Route::get('/checkout', fn() => view('checkout'))->middleware('auth')->name('checkout');

// المصادقة
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// صفحات المستخدم
Route::middleware('auth')->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('/wishlist', fn() => view('wishlist'))->name('wishlist');
    Route::get('/profile', fn() => view('profile'))->name('profile');
});

// لوحة تحكم البائع
Route::prefix('seller')->name('seller.')->middleware(['auth', 'role:seller,admin'])->group(function () {
    // إنشاء المتجر
    Route::get('/store/create', [SellerStoreController::class, 'create'])->name('store.create');
    Route::post('/store', [SellerStoreController::class, 'store'])->name('store.store');
    Route::get('/store/pending', [SellerStoreController::class, 'pending'])->name('store.pending');
    
    // يتطلب متجر معتمد
    Route::middleware('seller')->group(function () {
        Route::get('/dashboard', [SellerDashboardController::class, 'index'])->name('dashboard');
        
        // إدارة المتجر
        Route::get('/store/edit', [SellerStoreController::class, 'edit'])->name('store.edit');
        Route::put('/store', [SellerStoreController::class, 'update'])->name('store.update');
        
        // المنتجات
        Route::resource('products', SellerProductController::class);
        Route::delete('/products/images/{image}', [SellerProductController::class, 'deleteImage'])->name('products.images.delete');
        
        // الطلبات
        Route::get('/orders', [SellerOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [SellerOrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/confirm', [SellerOrderController::class, 'confirm'])->name('orders.confirm');
        Route::post('/orders/{order}/process', [SellerOrderController::class, 'process'])->name('orders.process');
        Route::post('/orders/{order}/ship', [SellerOrderController::class, 'ship'])->name('orders.ship');
        Route::post('/orders/{order}/deliver', [SellerOrderController::class, 'deliver'])->name('orders.deliver');
        Route::post('/orders/{order}/cancel', [SellerOrderController::class, 'cancel'])->name('orders.cancel');
        Route::put('/orders/{order}/notes', [SellerOrderController::class, 'updateNotes'])->name('orders.notes');
    });
});

// لوحة تحكم الإدارة
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // إدارة المتاجر
    Route::get('/stores', [AdminStoreController::class, 'index'])->name('stores.index');
    Route::get('/stores/{store}', [AdminStoreController::class, 'show'])->name('stores.show');
    Route::post('/stores/{store}/approve', [AdminStoreController::class, 'approve'])->name('stores.approve');
    Route::post('/stores/{store}/reject', [AdminStoreController::class, 'reject'])->name('stores.reject');
    Route::post('/stores/{store}/suspend', [AdminStoreController::class, 'suspend'])->name('stores.suspend');
    Route::post('/stores/{store}/activate', [AdminStoreController::class, 'activate'])->name('stores.activate');
    Route::post('/stores/{store}/toggle-featured', [AdminStoreController::class, 'toggleFeatured'])->name('stores.toggle-featured');
    Route::post('/stores/{store}/toggle-verified', [AdminStoreController::class, 'toggleVerified'])->name('stores.toggle-verified');
    
    // التصنيفات
    Route::resource('categories', AdminCategoryController::class);
    
    // ماركات السيارات
    Route::resource('car-brands', AdminCarBrandController::class);
    Route::post('/car-brands/{carBrand}/models', [AdminCarBrandController::class, 'storeModel'])->name('car-brands.models.store');
    Route::delete('/car-models/{carModel}', [AdminCarBrandController::class, 'destroyModel'])->name('car-models.destroy');
});
