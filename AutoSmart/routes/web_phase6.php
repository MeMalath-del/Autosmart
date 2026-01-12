<?php

use Illuminate\Support\Facades\Route;

// QR Code & Barcode
Route::get('/qr/{code}', [\App\Http\Controllers\QrController::class, 'scan'])->name('qr.scan');
Route::get('/scanner', [\App\Http\Controllers\QrController::class, 'showScanner'])->name('scanner');
Route::post('/barcode/scan', [\App\Http\Controllers\QrController::class, 'scanBarcode'])->name('barcode.scan');

// Chatbot
Route::get('/chatbot', [\App\Http\Controllers\ChatbotController::class, 'index'])->name('chatbot.index');
Route::post('/chatbot/send', [\App\Http\Controllers\ChatbotController::class, 'sendMessage'])->name('chatbot.send');
Route::post('/chatbot/start', [\App\Http\Controllers\ChatbotController::class, 'startSession'])->name('chatbot.start');
Route::post('/chatbot/end', [\App\Http\Controllers\ChatbotController::class, 'endSession'])->name('chatbot.end');
Route::post('/chatbot/transfer', [\App\Http\Controllers\ChatbotController::class, 'transferToHuman'])->name('chatbot.transfer');
Route::post('/chatbot/feedback', [\App\Http\Controllers\ChatbotController::class, 'feedback'])->name('chatbot.feedback');

// Image Search
Route::get('/search/image', [\App\Http\Controllers\ImageSearchController::class, 'index'])->name('search.image');
Route::post('/search/image', [\App\Http\Controllers\ImageSearchController::class, 'search'])->name('search.image.submit');

// Authenticity Check
Route::get('/products/{product}/authenticity', [\App\Http\Controllers\AuthenticityController::class, 'check'])->name('products.authenticity');
Route::post('/products/{product}/authenticity/verify', [\App\Http\Controllers\AuthenticityController::class, 'verify'])->name('products.authenticity.verify');
Route::post('/products/{product}/authenticity/report', [\App\Http\Controllers\AuthenticityController::class, 'report'])->name('products.authenticity.report')->middleware('auth');

// Forum
Route::prefix('forum')->name('forum.')->group(function () {
    Route::get('/', [\App\Http\Controllers\ForumController::class, 'index'])->name('index');
    Route::get('/search', [\App\Http\Controllers\ForumController::class, 'search'])->name('search');
    Route::get('/category/{category:slug}', [\App\Http\Controllers\ForumController::class, 'category'])->name('category');
    Route::get('/topic/{topic:slug}', [\App\Http\Controllers\ForumController::class, 'topic'])->name('topic');
    
    Route::middleware('auth')->group(function () {
        Route::get('/category/{category:slug}/create', [\App\Http\Controllers\ForumController::class, 'createTopic'])->name('create-topic');
        Route::post('/category/{category:slug}/store', [\App\Http\Controllers\ForumController::class, 'storeTopic'])->name('store-topic');
        Route::post('/topic/{topic:slug}/reply', [\App\Http\Controllers\ForumController::class, 'storeReply'])->name('store-reply');
        Route::post('/reply/{reply}/solution', [\App\Http\Controllers\ForumController::class, 'markSolution'])->name('mark-solution');
    });
});

// Blog
Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [\App\Http\Controllers\BlogController::class, 'index'])->name('index');
    Route::get('/search', [\App\Http\Controllers\BlogController::class, 'search'])->name('search');
    Route::get('/category/{category:slug}', [\App\Http\Controllers\BlogController::class, 'category'])->name('category');
    Route::get('/tag/{tag}', [\App\Http\Controllers\BlogController::class, 'tag'])->name('tag');
    Route::get('/{post:slug}', [\App\Http\Controllers\BlogController::class, 'show'])->name('show');
    Route::post('/{post:slug}/comment', [\App\Http\Controllers\BlogController::class, 'storeComment'])->name('comment')->middleware('auth');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Wallet
    Route::prefix('wallet')->name('wallet.')->group(function () {
        Route::get('/', [\App\Http\Controllers\WalletController::class, 'index'])->name('index');
        Route::post('/topup', [\App\Http\Controllers\WalletController::class, 'topup'])->name('topup');
        Route::post('/withdraw', [\App\Http\Controllers\WalletController::class, 'withdraw'])->name('withdraw');
        Route::get('/transactions', [\App\Http\Controllers\WalletController::class, 'transactions'])->name('transactions');
    });
    
    // Installments
    Route::prefix('installments')->name('installments.')->group(function () {
        Route::get('/', [\App\Http\Controllers\InstallmentController::class, 'index'])->name('index');
        Route::get('/calculator', [\App\Http\Controllers\InstallmentController::class, 'calculator'])->name('calculator');
        Route::post('/calculate', [\App\Http\Controllers\InstallmentController::class, 'calculate'])->name('calculate');
        Route::post('/orders/{order}/apply', [\App\Http\Controllers\InstallmentController::class, 'apply'])->name('apply');
        Route::get('/{installment}', [\App\Http\Controllers\InstallmentController::class, 'show'])->name('show');
        Route::post('/payments/{payment}/pay', [\App\Http\Controllers\InstallmentController::class, 'pay'])->name('pay');
    });
    
    // Image Search History
    Route::get('/search/image/history', [\App\Http\Controllers\ImageSearchController::class, 'history'])->name('search.image.history');
});
