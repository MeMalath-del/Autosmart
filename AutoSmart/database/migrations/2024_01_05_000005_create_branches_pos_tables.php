<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // فروع المتجر
        Schema::create('store_branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('code')->unique();
            $table->text('address');
            $table->string('city');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->json('working_hours')->nullable();
            $table->boolean('is_pickup_point')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // مخزون الفرع
        Schema::create('branch_inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('store_branches')->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(0);
            $table->integer('reserved_quantity')->default(0);
            $table->string('location')->nullable(); // shelf location
            $table->timestamps();
            
            $table->unique(['branch_id', 'product_id']);
        });

        // جلسات نقطة البيع
        Schema::create('pos_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('store_branches')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('opening_balance', 12, 2)->default(0);
            $table->decimal('closing_balance', 12, 2)->nullable();
            $table->decimal('expected_balance', 12, 2)->nullable();
            $table->decimal('difference', 12, 2)->nullable();
            $table->integer('transactions_count')->default(0);
            $table->decimal('total_sales', 12, 2)->default(0);
            $table->decimal('total_refunds', 12, 2)->default(0);
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // معاملات نقطة البيع
        Schema::create('pos_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('pos_sessions')->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->string('transaction_number')->unique();
            $table->enum('type', ['sale', 'refund', 'exchange', 'void']);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->string('payment_method');
            $table->decimal('amount_paid', 12, 2);
            $table->decimal('change_given', 10, 2)->default(0);
            $table->foreignId('customer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // عناصر معاملة نقطة البيع
        Schema::create('pos_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('pos_transactions')->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('product_name');
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_transaction_items');
        Schema::dropIfExists('pos_transactions');
        Schema::dropIfExists('pos_sessions');
        Schema::dropIfExists('branch_inventory');
        Schema::dropIfExists('store_branches');
    }
};
