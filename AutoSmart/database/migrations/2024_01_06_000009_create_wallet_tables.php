<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // المحفظة الذكية
        if (! Schema::hasTable('wallets')) {
            Schema::create('wallets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->decimal('balance', 14, 2)->default(0);
                $table->decimal('pending_balance', 14, 2)->default(0);
                $table->string('currency', 3)->default('SAR');
                $table->boolean('is_active')->default(true);
                $table->timestamp('last_transaction_at')->nullable();
                $table->timestamps();

                $table->unique('user_id');
            });
        }

        // معاملات المحفظة
        if (! Schema::hasTable('wallet_transactions')) {
            Schema::create('wallet_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
                $table->string('transaction_id')->unique();
                $table->enum('type', ['credit', 'debit', 'refund', 'cashback', 'transfer', 'adjustment']);
                $table->decimal('amount', 14, 2);
                $table->decimal('balance_before', 14, 2);
                $table->decimal('balance_after', 14, 2);
                $table->string('description')->nullable();
                $table->morphs('reference');
                $table->enum('status', ['pending', 'completed', 'failed', 'reversed'])->default('completed');
                $table->timestamps();

                $table->index(['wallet_id', 'created_at']);
            });
        }

        // طلبات شحن المحفظة
        if (! Schema::hasTable('wallet_topups')) {
            Schema::create('wallet_topups', function (Blueprint $table) {
                $table->id();
                $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
                $table->decimal('amount', 14, 2);
                $table->string('payment_method');
                $table->string('payment_reference')->nullable();
                $table->decimal('fee', 10, 2)->default(0);
                $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
                $table->timestamps();
            });
        }

        // طلبات سحب المحفظة
        if (! Schema::hasTable('wallet_withdrawals')) {
            Schema::create('wallet_withdrawals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
                $table->decimal('amount', 14, 2);
                $table->string('bank_name');
                $table->string('account_number');
                $table->string('iban')->nullable();
                $table->string('account_holder_name');
                $table->decimal('fee', 10, 2)->default(0);
                $table->enum('status', ['pending', 'processing', 'completed', 'rejected'])->default('pending');
                $table->text('rejection_reason')->nullable();
                $table->string('transaction_reference')->nullable();
                $table->timestamps();
            });
        }

        // التحويلات بين المحافظ
        if (! Schema::hasTable('wallet_transfers')) {
            Schema::create('wallet_transfers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('from_wallet_id')->constrained('wallets')->onDelete('cascade');
                $table->foreignId('to_wallet_id')->constrained('wallets')->onDelete('cascade');
                $table->decimal('amount', 14, 2);
                $table->decimal('fee', 10, 2)->default(0);
                $table->text('note')->nullable();
                $table->enum('status', ['pending', 'completed', 'reversed'])->default('completed');
                $table->timestamps();
            });
        }

        // الكاش باك
        if (! Schema::hasTable('cashback_rules')) {
            Schema::create('cashback_rules', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->enum('type', ['percentage', 'fixed']);
                $table->decimal('value', 10, 2);
                $table->decimal('min_order_amount', 12, 2)->default(0);
                $table->decimal('max_cashback', 12, 2)->nullable();
                $table->json('applicable_categories')->nullable();
                $table->json('applicable_stores')->nullable();
                $table->datetime('starts_at')->nullable();
                $table->datetime('ends_at')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cashback_rules');
        Schema::dropIfExists('wallet_transfers');
        Schema::dropIfExists('wallet_withdrawals');
        Schema::dropIfExists('wallet_topups');
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('wallets');
    }
};
