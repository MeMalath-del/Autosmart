<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // بوابات الدفع
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('logo')->nullable();
            $table->json('config')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // المدفوعات
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('gateway_code');
            $table->string('transaction_id')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('currency')->default('SAR');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'refunded'])->default('pending');
            $table->json('gateway_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        // التقسيط
        Schema::create('installment_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('provider'); // tabby, tamara
            $table->integer('months');
            $table->decimal('min_amount', 10, 2)->default(0);
            $table->decimal('max_amount', 10, 2)->nullable();
            $table->decimal('interest_rate', 5, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('order_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('installment_plan_id')->constrained()->onDelete('cascade');
            $table->decimal('total_amount', 12, 2);
            $table->decimal('monthly_amount', 12, 2);
            $table->integer('total_months');
            $table->integer('paid_months')->default(0);
            $table->enum('status', ['active', 'completed', 'defaulted'])->default('active');
            $table->timestamps();
        });

        // الفواتير الإلكترونية
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('invoice_number')->unique();
            $table->string('qr_code')->nullable();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax_amount', 12, 2);
            $table->decimal('total', 12, 2);
            $table->string('tax_number')->nullable();
            $table->json('seller_info')->nullable();
            $table->json('buyer_info')->nullable();
            $table->timestamp('issued_at');
            $table->timestamps();
        });

        // طلبات الاسترداد
        Schema::create('refund_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('reason');
            $table->enum('refund_type', ['wallet', 'bank', 'original_method'])->default('wallet');
            $table->decimal('amount', 12, 2);
            $table->enum('status', ['pending', 'approved', 'rejected', 'processed'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('iban')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refund_requests');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('order_installments');
        Schema::dropIfExists('installment_plans');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('payment_gateways');
    }
};
