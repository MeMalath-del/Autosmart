<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // الشركاء التسويقيين
        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('code')->unique();
            $table->string('website')->nullable();
            $table->text('bio')->nullable();
            $table->decimal('commission_rate', 5, 2)->default(5);
            $table->decimal('total_earnings', 12, 2)->default(0);
            $table->decimal('pending_earnings', 12, 2)->default(0);
            $table->decimal('paid_earnings', 12, 2)->default(0);
            $table->integer('total_clicks')->default(0);
            $table->integer('total_orders')->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0);
            $table->enum('status', ['pending', 'approved', 'suspended'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->json('payment_details')->nullable();
            $table->decimal('minimum_payout', 10, 2)->default(100);
            $table->timestamps();
        });

        // روابط التسويق
        Schema::create('affiliate_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained()->onDelete('cascade');
            $table->string('name')->nullable();
            $table->string('code')->unique();
            $table->string('destination_url');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('clicks')->default(0);
            $table->integer('conversions')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // زيارات الروابط
        Schema::create('affiliate_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained()->onDelete('cascade');
            $table->foreignId('link_id')->nullable()->constrained('affiliate_links')->onDelete('set null');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('referer')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->boolean('converted')->default(false);
            $table->timestamps();
        });

        // مبيعات الشركاء
        Schema::create('affiliate_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('click_id')->nullable()->constrained('affiliate_clicks')->onDelete('set null');
            $table->decimal('order_total', 12, 2);
            $table->decimal('commission', 12, 2);
            $table->enum('status', ['pending', 'approved', 'paid', 'rejected'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        // طلبات السحب
        Schema::create('affiliate_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('payment_method');
            $table->json('payment_details')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->string('transaction_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_payouts');
        Schema::dropIfExists('affiliate_sales');
        Schema::dropIfExists('affiliate_clicks');
        Schema::dropIfExists('affiliate_links');
        Schema::dropIfExists('affiliates');
    }
};
