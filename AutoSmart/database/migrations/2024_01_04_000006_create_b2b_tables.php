<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // حسابات الشركات
        Schema::create('business_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('company_name');
            $table->string('company_name_ar')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('commercial_register')->nullable();
            $table->enum('business_type', ['workshop', 'dealer', 'wholesaler', 'retailer', 'other']);
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_phone')->nullable();
            $table->decimal('credit_limit', 12, 2)->default(0);
            $table->decimal('current_balance', 12, 2)->default(0);
            $table->integer('payment_terms_days')->default(30);
            $table->enum('status', ['pending', 'approved', 'suspended'])->default('pending');
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });

        // أسعار الجملة
        Schema::create('wholesale_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('min_quantity');
            $table->integer('max_quantity')->nullable();
            $table->decimal('price', 12, 2);
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->timestamps();
        });

        // طلبات عروض الأسعار RFQ
        Schema::create('quote_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_account_id')->constrained()->onDelete('cascade');
            $table->string('request_number')->unique();
            $table->text('requirements')->nullable();
            $table->date('needed_by')->nullable();
            $table->enum('status', ['open', 'quoted', 'accepted', 'rejected', 'expired'])->default('open');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        // عناصر طلب عرض السعر
        Schema::create('quote_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->string('product_name');
            $table->string('part_number')->nullable();
            $table->integer('quantity');
            $table->text('specifications')->nullable();
            $table->timestamps();
        });

        // عروض الأسعار من البائعين
        Schema::create('seller_quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->decimal('total_amount', 12, 2);
            $table->integer('delivery_days')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->timestamp('valid_until')->nullable();
            $table->timestamps();
        });

        // فواتير آجلة
        Schema::create('credit_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_account_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('invoice_number')->unique();
            $table->decimal('amount', 12, 2);
            $table->date('due_date');
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->enum('status', ['pending', 'partial', 'paid', 'overdue'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_invoices');
        Schema::dropIfExists('seller_quotes');
        Schema::dropIfExists('quote_request_items');
        Schema::dropIfExists('quote_requests');
        Schema::dropIfExists('wholesale_prices');
        Schema::dropIfExists('business_accounts');
    }
};
