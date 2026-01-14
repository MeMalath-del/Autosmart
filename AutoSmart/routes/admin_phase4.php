<?php

use Illuminate\Support\Facades\Route;

// ===============================
// Phase 4 Admin Routes
// ===============================

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {

    // Advanced Reports
    Route::get('/reports/advanced', [App\Http\Controllers\Admin\AdvancedReportsController::class, 'index'])->name('reports.advanced');
    Route::post('/reports/generate', [App\Http\Controllers\Admin\AdvancedReportsController::class, 'generate'])->name('reports.generate');
    Route::post('/reports/export', [App\Http\Controllers\Admin\AdvancedReportsController::class, 'export'])->name('reports.export');

    // Support Tickets
    Route::prefix('support')->name('support.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\SupportController::class, 'index'])->name('index');
        Route::get('/{ticket}', [App\Http\Controllers\Admin\SupportController::class, 'show'])->name('show');
        Route::post('/{ticket}/reply', [App\Http\Controllers\Admin\SupportController::class, 'reply'])->name('reply');
        Route::post('/{ticket}/assign', [App\Http\Controllers\Admin\SupportController::class, 'assign'])->name('assign');

        Route::get('/content/faqs', [App\Http\Controllers\Admin\SupportController::class, 'faqs'])->name('faqs');
        Route::post('/content/faqs', [App\Http\Controllers\Admin\SupportController::class, 'storeFaq'])->name('faqs.store');
        Route::get('/content/knowledge-base', [App\Http\Controllers\Admin\SupportController::class, 'knowledgeBase'])->name('kb');
        Route::post('/content/knowledge-base', [App\Http\Controllers\Admin\SupportController::class, 'storeArticle'])->name('kb.store');
    });

    // B2B Accounts
    Route::prefix('b2b')->name('b2b.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\B2BController::class, 'index'])->name('index');
        Route::get('/{account}', [App\Http\Controllers\Admin\B2BController::class, 'show'])->name('show');
        Route::post('/{account}/approve', [App\Http\Controllers\Admin\B2BController::class, 'approve'])->name('approve');
        Route::post('/{account}/reject', [App\Http\Controllers\Admin\B2BController::class, 'reject'])->name('reject');
        Route::post('/{account}/credit', [App\Http\Controllers\Admin\B2BController::class, 'updateCredit'])->name('credit');
    });

    // Auctions Management
    Route::prefix('auctions')->name('auctions.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AuctionController::class, 'index'])->name('index');
        Route::get('/{auction}', [App\Http\Controllers\Admin\AuctionController::class, 'show'])->name('show');
        Route::post('/{auction}/feature', [App\Http\Controllers\Admin\AuctionController::class, 'feature'])->name('feature');
        Route::post('/{auction}/cancel', [App\Http\Controllers\Admin\AuctionController::class, 'cancel'])->name('cancel');
    });

    // Influencers
    Route::prefix('influencers')->name('influencers.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\InfluencerController::class, 'index'])->name('index');
        Route::get('/{influencer}', [App\Http\Controllers\Admin\InfluencerController::class, 'show'])->name('show');
        Route::post('/{influencer}/approve', [App\Http\Controllers\Admin\InfluencerController::class, 'approve'])->name('approve');
        Route::post('/{influencer}/reject', [App\Http\Controllers\Admin\InfluencerController::class, 'reject'])->name('reject');
        Route::post('/{influencer}/commission', [App\Http\Controllers\Admin\InfluencerController::class, 'updateCommission'])->name('commission');
        Route::post('/{influencer}/verify', [App\Http\Controllers\Admin\InfluencerController::class, 'verify'])->name('verify');
    });

    // Gift Cards
    Route::prefix('gift-cards')->name('gift-cards.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\GiftCardController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\GiftCardController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\GiftCardController::class, 'store'])->name('store');
        Route::get('/{giftCard}', [App\Http\Controllers\Admin\GiftCardController::class, 'show'])->name('show');
        Route::post('/{giftCard}/deactivate', [App\Http\Controllers\Admin\GiftCardController::class, 'deactivate'])->name('deactivate');
        Route::get('/settings/templates', [App\Http\Controllers\Admin\GiftCardController::class, 'templates'])->name('templates');
    });
});

// ===============================
// Phase 4 Seller Routes
// ===============================

Route::prefix('seller')->name('seller.')->middleware(['auth', 'seller'])->group(function () {

    // Staff Management
    Route::resource('staff', App\Http\Controllers\Seller\StaffController::class);

    // Auctions
    Route::prefix('auctions')->name('auctions.')->group(function () {
        Route::get('/', [App\Http\Controllers\Seller\AuctionController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Seller\AuctionController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Seller\AuctionController::class, 'store'])->name('store');
        Route::get('/{auction}', [App\Http\Controllers\Seller\AuctionController::class, 'show'])->name('show');
        Route::post('/{auction}/activate', [App\Http\Controllers\Seller\AuctionController::class, 'activate'])->name('activate');
        Route::post('/{auction}/cancel', [App\Http\Controllers\Seller\AuctionController::class, 'cancel'])->name('cancel');
    });
});

// Additional Customer Routes
Route::middleware('auth')->group(function () {
    // B2B
    Route::prefix('b2b')->name('b2b.')->group(function () {
        Route::get('/', [App\Http\Controllers\B2BController::class, 'index'])->name('index');
        Route::post('/register', [App\Http\Controllers\B2BController::class, 'register'])->name('register');
        Route::get('/request-quote', [App\Http\Controllers\B2BController::class, 'requestQuote'])->name('request-quote');
        Route::post('/submit-quote', [App\Http\Controllers\B2BController::class, 'submitQuote'])->name('submit-quote');
        Route::get('/quotes/{quoteRequest}', [App\Http\Controllers\B2BController::class, 'showQuote'])->name('quotes.show');
    });

    // Tracking
    Route::get('/orders/{order}/track', [App\Http\Controllers\TrackingController::class, 'track'])->name('orders.track');
    Route::get('/orders/{order}/location', [App\Http\Controllers\TrackingController::class, 'getLocation'])->name('orders.location');

    // Video Reviews
    Route::get('/video-reviews', [App\Http\Controllers\VideoReviewController::class, 'index'])->name('video-reviews.index');
    Route::get('/video-reviews/{videoReview}', [App\Http\Controllers\VideoReviewController::class, 'show'])->name('video-reviews.show');
    Route::get('/products/{product}/video-review', [App\Http\Controllers\VideoReviewController::class, 'create'])->name('video-reviews.create');
    Route::post('/products/{product}/video-review', [App\Http\Controllers\VideoReviewController::class, 'store'])->name('video-reviews.store');
});

// Wholesale Products (Public)
Route::get('/wholesale', [App\Http\Controllers\B2BController::class, 'wholesale'])->name('wholesale');
