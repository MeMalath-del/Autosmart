<?php

use Illuminate\Support\Facades\Route;

// ===============================
// Phase 4 Routes
// ===============================

// VIN Search
Route::get('/vin-search', [App\Http\Controllers\VinController::class, 'index'])->name('vin.search');
Route::post('/vin-search', [App\Http\Controllers\VinController::class, 'search'])->name('vin.search.submit');

// Auctions
Route::prefix('auctions')->name('auctions.')->group(function () {
    Route::get('/', [App\Http\Controllers\AuctionController::class, 'index'])->name('index');
    Route::get('/{auction}', [App\Http\Controllers\AuctionController::class, 'show'])->name('show');
    
    Route::middleware('auth')->group(function () {
        Route::post('/{auction}/bid', [App\Http\Controllers\AuctionController::class, 'bid'])->name('bid');
        Route::post('/{auction}/watch', [App\Http\Controllers\AuctionController::class, 'watch'])->name('watch');
        Route::get('/my/bids', [App\Http\Controllers\AuctionController::class, 'myBids'])->name('my-bids');
    });
});

// Support & Tickets
Route::prefix('support')->name('support.')->group(function () {
    Route::get('/faq', [App\Http\Controllers\SupportTicketController::class, 'faq'])->name('faq');
    Route::get('/help', [App\Http\Controllers\SupportTicketController::class, 'knowledgeBase'])->name('help');
    Route::get('/help/{article:slug}', [App\Http\Controllers\SupportTicketController::class, 'article'])->name('article');
    
    Route::middleware('auth')->group(function () {
        Route::get('/', [App\Http\Controllers\SupportTicketController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\SupportTicketController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\SupportTicketController::class, 'store'])->name('store');
        Route::get('/{supportTicket}', [App\Http\Controllers\SupportTicketController::class, 'show'])->name('show');
        Route::post('/{supportTicket}/reply', [App\Http\Controllers\SupportTicketController::class, 'reply'])->name('reply');
    });
});

// Gift Cards
Route::prefix('gift-cards')->name('gift-cards.')->group(function () {
    Route::get('/', [App\Http\Controllers\GiftCardController::class, 'index'])->name('index');
    Route::post('/check', [App\Http\Controllers\GiftCardController::class, 'check'])->name('check');
    
    Route::middleware('auth')->group(function () {
        Route::post('/purchase', [App\Http\Controllers\GiftCardController::class, 'purchase'])->name('purchase');
        Route::get('/success/{giftCard}', [App\Http\Controllers\GiftCardController::class, 'success'])->name('success');
        Route::get('/my-cards', [App\Http\Controllers\GiftCardController::class, 'myCards'])->name('my-cards');
    });
});

// Subscriptions
Route::prefix('subscriptions')->name('subscriptions.')->middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\SubscriptionController::class, 'index'])->name('index');
    Route::post('/{plan}/subscribe', [App\Http\Controllers\SubscriptionController::class, 'subscribe'])->name('subscribe');
    Route::get('/{subscription}', [App\Http\Controllers\SubscriptionController::class, 'show'])->name('show');
    Route::post('/{subscription}/cancel', [App\Http\Controllers\SubscriptionController::class, 'cancel'])->name('cancel');
    Route::post('/{subscription}/pause', [App\Http\Controllers\SubscriptionController::class, 'pause'])->name('pause');
    Route::post('/{subscription}/resume', [App\Http\Controllers\SubscriptionController::class, 'resume'])->name('resume');
});

// Community
Route::prefix('community')->name('community.')->group(function () {
    Route::get('/', [App\Http\Controllers\CommunityController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\CommunityController::class, 'create'])->name('create')->middleware('auth');
    Route::post('/', [App\Http\Controllers\CommunityController::class, 'store'])->name('store')->middleware('auth');
    Route::get('/{group:slug}', [App\Http\Controllers\CommunityController::class, 'show'])->name('show');
    
    Route::middleware('auth')->group(function () {
        Route::post('/{group}/join', [App\Http\Controllers\CommunityController::class, 'join'])->name('join');
        Route::post('/{group}/leave', [App\Http\Controllers\CommunityController::class, 'leave'])->name('leave');
        Route::post('/{group}/post', [App\Http\Controllers\CommunityController::class, 'createPost'])->name('post');
        Route::post('/posts/{post}/comment', [App\Http\Controllers\CommunityController::class, 'comment'])->name('comment');
    });
});

// Influencer
Route::prefix('influencer')->name('influencer.')->middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\InfluencerController::class, 'index'])->name('index');
    Route::post('/apply', [App\Http\Controllers\InfluencerController::class, 'apply'])->name('apply');
});
Route::get('/ref/{code}', [App\Http\Controllers\InfluencerController::class, 'link'])->name('influencer.link');

// Q&A
Route::middleware('auth')->group(function () {
    Route::post('/products/{product}/questions', [App\Http\Controllers\QuestionController::class, 'store'])->name('questions.store');
    Route::post('/questions/{question}/answer', [App\Http\Controllers\QuestionController::class, 'answer'])->name('questions.answer');
    Route::post('/answers/{answer}/best', [App\Http\Controllers\QuestionController::class, 'markBest'])->name('answers.best');
    Route::post('/qa/{type}/{id}/vote', [App\Http\Controllers\QuestionController::class, 'vote'])->name('qa.vote');
});

// Chatbot
Route::get('/chatbot', [App\Http\Controllers\ChatbotController::class, 'widget'])->name('chatbot.widget');
Route::post('/chatbot/message', [App\Http\Controllers\ChatbotController::class, 'message'])->name('chatbot.message');
