<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // طلبات الإرجاع
        Schema::create('return_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('return_number')->unique();
            $table->enum('type', ['return', 'exchange', 'warranty'])->default('return');
            $table->enum('reason', ['defective', 'wrong_item', 'not_as_described', 'changed_mind', 'other']);
            $table->text('description')->nullable();
            $table->json('images')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'shipped', 'received', 'inspected', 'completed', 'cancelled'])->default('pending');
            $table->decimal('refund_amount', 12, 2)->nullable();
            $table->enum('refund_method', ['original', 'wallet', 'bank'])->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->string('return_shipping_label')->nullable();
            $table->string('return_tracking_number')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->text('inspection_notes')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();
        });

        // عناصر الإرجاع
        Schema::create('return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_item_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->enum('condition', ['unopened', 'like_new', 'used', 'damaged'])->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('exchange_product_id')->nullable()->constrained('products')->onDelete('set null');
            $table->timestamps();
        });

        // سياسات الإرجاع
        Schema::create('return_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->integer('return_window_days')->default(14);
            $table->boolean('allow_exchange')->default(true);
            $table->boolean('require_receipt')->default(true);
            $table->boolean('require_original_packaging')->default(false);
            $table->decimal('restocking_fee_percentage', 5, 2)->default(0);
            $table->json('non_returnable_conditions')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // تحليل مشاعر المراجعات
        Schema::create('review_sentiments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained()->onDelete('cascade');
            $table->enum('sentiment', ['positive', 'negative', 'neutral']);
            $table->decimal('confidence', 5, 4);
            $table->json('keywords')->nullable();
            $table->json('aspects')->nullable(); // quality, price, shipping, etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_sentiments');
        Schema::dropIfExists('return_policies');
        Schema::dropIfExists('return_items');
        Schema::dropIfExists('return_requests');
    }
};
