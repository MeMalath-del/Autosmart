<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // المزادات
        Schema::create('auctions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('starting_price', 12, 2);
            $table->decimal('reserve_price', 12, 2)->nullable();
            $table->decimal('buy_now_price', 12, 2)->nullable();
            $table->decimal('current_bid', 12, 2)->nullable();
            $table->decimal('bid_increment', 10, 2)->default(10);
            $table->integer('bids_count')->default(0);
            $table->foreignId('winner_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->enum('status', ['draft', 'active', 'ended', 'sold', 'cancelled'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        // المزايدات
        Schema::create('auction_bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auction_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->decimal('max_auto_bid', 12, 2)->nullable();
            $table->boolean('is_winning')->default(false);
            $table->boolean('is_auto_bid')->default(false);
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });

        // مراقبة المزادات
        Schema::create('auction_watchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auction_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('notify_outbid')->default(true);
            $table->boolean('notify_ending')->default(true);
            $table->timestamps();
            
            $table->unique(['auction_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auction_watchers');
        Schema::dropIfExists('auction_bids');
        Schema::dropIfExists('auctions');
    }
};
