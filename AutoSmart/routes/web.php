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

// Sitemap
Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index']);
Route::get('/sitemap-main.xml', [App\Http\Controllers\SitemapController::class, 'main']);
Route::get('/sitemap-products.xml', [App\Http\Controllers\SitemapController::class, 'products']);
Route::get('/sitemap-stores.xml', [App\Http\Controllers\SitemapController::class, 'stores']);

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

// صفحات المستخدم الإضافية
Route::middleware('auth')->group(function () {
    // العناوين
    Route::get('/addresses', [App\Http\Controllers\AddressController::class, 'index'])->name('addresses.index');
    Route::post('/addresses', [App\Http\Controllers\AddressController::class, 'store'])->name('addresses.store');
    Route::put('/addresses/{address}', [App\Http\Controllers\AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{address}', [App\Http\Controllers\AddressController::class, 'destroy'])->name('addresses.destroy');
    Route::post('/addresses/{address}/default', [App\Http\Controllers\AddressController::class, 'setDefault'])->name('addresses.default');
    
    // المحادثات
    Route::get('/conversations', [App\Http\Controllers\ConversationController::class, 'index'])->name('conversations.index');
    Route::get('/conversations/{conversation}', [App\Http\Controllers\ConversationController::class, 'show'])->name('conversations.show');
    Route::get('/stores/{store}/contact', [App\Http\Controllers\ConversationController::class, 'startWithStore'])->name('conversations.start');
    Route::post('/conversations/{conversation}/messages', [App\Http\Controllers\ConversationController::class, 'sendMessage'])->name('conversations.send');
    
    // المحفظة
    Route::get('/wallet', [App\Http\Controllers\WalletController::class, 'index'])->name('wallet.index');
    Route::post('/wallet/withdraw', [App\Http\Controllers\WalletController::class, 'withdraw'])->name('wallet.withdraw');
    
    // الضمان
    Route::get('/warranty', [App\Http\Controllers\WarrantyController::class, 'index'])->name('warranty.index');
    Route::get('/warranty/create/{orderItem}', [App\Http\Controllers\WarrantyController::class, 'create'])->name('warranty.create');
    Route::post('/warranty', [App\Http\Controllers\WarrantyController::class, 'store'])->name('warranty.store');
    Route::get('/warranty/{warrantyClaim}', [App\Http\Controllers\WarrantyController::class, 'show'])->name('warranty.show');
    
    // طلبات قطع الغيار
    Route::get('/part-requests', [App\Http\Controllers\PartRequestController::class, 'index'])->name('part-requests.index');
    Route::get('/part-requests/create', [App\Http\Controllers\PartRequestController::class, 'create'])->name('part-requests.create');
    Route::post('/part-requests', [App\Http\Controllers\PartRequestController::class, 'store'])->name('part-requests.store');
    Route::get('/part-requests/{partRequest}', [App\Http\Controllers\PartRequestController::class, 'show'])->name('part-requests.show');
    Route::post('/part-requests/{partRequest}/close', [App\Http\Controllers\PartRequestController::class, 'close'])->name('part-requests.close');
    Route::post('/quotes/{quote}/accept', [App\Http\Controllers\PartRequestController::class, 'acceptQuote'])->name('quotes.accept');
    Route::post('/quotes/{quote}/reject', [App\Http\Controllers\PartRequestController::class, 'rejectQuote'])->name('quotes.reject');
});

// المقارنة (عام)
Route::get('/compare', [App\Http\Controllers\ComparisonController::class, 'index'])->name('compare.index');
Route::post('/compare/add/{productId}', [App\Http\Controllers\ComparisonController::class, 'add'])->name('compare.add');
Route::delete('/compare/remove/{productId}', [App\Http\Controllers\ComparisonController::class, 'remove'])->name('compare.remove');
Route::delete('/compare/clear', [App\Http\Controllers\ComparisonController::class, 'clear'])->name('compare.clear');

// لوحة تحكم البائع - إضافات
Route::prefix('seller')->name('seller.')->middleware(['auth', 'role:seller,admin'])->group(function () {
    Route::middleware('seller')->group(function () {
        // الكوبونات
        Route::resource('coupons', App\Http\Controllers\Seller\CouponController::class);
        
        // طلبات قطع الغيار
        Route::get('/part-requests', [App\Http\Controllers\Seller\PartRequestController::class, 'index'])->name('part-requests.index');
        Route::get('/part-requests/{partRequest}', [App\Http\Controllers\Seller\PartRequestController::class, 'show'])->name('part-requests.show');
        Route::post('/part-requests/{partRequest}/quote', [App\Http\Controllers\Seller\PartRequestController::class, 'submitQuote'])->name('part-requests.quote');
        
        // المحادثات
        Route::get('/conversations', [App\Http\Controllers\Seller\ConversationController::class, 'index'])->name('conversations.index');
        Route::get('/conversations/{conversation}', [App\Http\Controllers\Seller\ConversationController::class, 'show'])->name('conversations.show');
        Route::post('/conversations/{conversation}/messages', [App\Http\Controllers\Seller\ConversationController::class, 'sendMessage'])->name('conversations.send');
        
        // الضمان
        Route::get('/warranty', [App\Http\Controllers\Seller\WarrantyController::class, 'index'])->name('warranty.index');
        Route::get('/warranty/{warrantyClaim}', [App\Http\Controllers\Seller\WarrantyController::class, 'show'])->name('warranty.show');
        Route::post('/warranty/{warrantyClaim}/respond', [App\Http\Controllers\Seller\WarrantyController::class, 'respond'])->name('warranty.respond');
        
        // التقارير
        Route::get('/reports', [App\Http\Controllers\Seller\ReportsController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [App\Http\Controllers\Seller\ReportsController::class, 'export'])->name('reports.export');
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

// Include Phase 3 routes
require __DIR__ . '/web_phase3.php';

// Include Admin Phase 3 routes
require __DIR__ . '/admin_phase3.php';
