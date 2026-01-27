<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // بطاقات الهدايا
        Schema::create('gift_cards', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->decimal('initial_balance', 10, 2);
            $table->decimal('current_balance', 10, 2);
            $table->foreignId('purchased_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('recipient_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('recipient_email')->nullable();
            $table->string('recipient_name')->nullable();
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'active', 'used', 'expired', 'cancelled'])->default('pending');
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        // سجل استخدام البطاقات
        Schema::create('gift_card_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gift_card_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type', ['purchase', 'redeem', 'refund']);
            $table->decimal('amount', 10, 2);
            $table->decimal('balance_after', 10, 2);
            $table->timestamps();
        });

        // قوالب بطاقات الهدايا
        Schema::create('gift_card_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image');
            $table->string('occasion')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gift_card_templates');
        Schema::dropIfExists('gift_card_transactions');
        Schema::dropIfExists('gift_cards');
    }
};
