<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // خطط التقسيط
        if (!Schema::hasTable('installment_plans')) {
            Schema::create('installment_plans', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('name_ar')->nullable();
                $table->integer('months');
                $table->decimal('interest_rate', 5, 2)->default(0);
                $table->decimal('min_amount', 12, 2);
                $table->decimal('max_amount', 12, 2);
                $table->decimal('down_payment_percentage', 5, 2)->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // طلبات التقسيط
        if (!Schema::hasTable('installment_requests')) {
            Schema::create('installment_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
                $table->foreignId('plan_id')->constrained('installment_plans')->onDelete('cascade');
                $table->decimal('total_amount', 12, 2);
                $table->decimal('down_payment', 12, 2)->default(0);
                $table->decimal('financed_amount', 12, 2);
                $table->decimal('monthly_payment', 12, 2);
                $table->decimal('total_with_interest', 12, 2);
                $table->enum('status', ['pending', 'approved', 'rejected', 'active', 'completed', 'defaulted'])->default('pending');
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->integer('payments_made')->default(0);
                $table->text('rejection_reason')->nullable();
                $table->timestamps();
            });
        }

        // أقساط التقسيط
        if (!Schema::hasTable('installment_payments')) {
            Schema::create('installment_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('installment_id')->constrained('installment_requests')->onDelete('cascade');
                $table->integer('payment_number');
                $table->decimal('amount', 12, 2);
                $table->date('due_date');
                $table->date('paid_date')->nullable();
                $table->enum('status', ['pending', 'paid', 'overdue', 'waived'])->default('pending');
                $table->string('payment_method')->nullable();
                $table->string('transaction_id')->nullable();
                $table->decimal('late_fee', 10, 2)->default(0);
                $table->timestamps();
            });
        }

        // تأمين القطع
        if (!Schema::hasTable('part_insurances')) {
            Schema::create('part_insurances', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->text('coverage')->nullable();
                $table->integer('duration_months');
                $table->decimal('price_percentage', 5, 2);
                $table->decimal('min_price', 10, 2)->default(0);
                $table->decimal('max_coverage', 12, 2)->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // بوالص التأمين
        if (!Schema::hasTable('insurance_policies')) {
            Schema::create('insurance_policies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('order_item_id')->constrained()->onDelete('cascade');
                $table->foreignId('insurance_id')->constrained('part_insurances')->onDelete('cascade');
                $table->string('policy_number')->unique();
                $table->decimal('premium_paid', 10, 2);
                $table->decimal('coverage_amount', 12, 2);
                $table->date('start_date');
                $table->date('end_date');
                $table->enum('status', ['active', 'expired', 'claimed', 'cancelled'])->default('active');
                $table->timestamps();
            });
        }

        // مطالبات التأمين
        if (!Schema::hasTable('insurance_claims')) {
            Schema::create('insurance_claims', function (Blueprint $table) {
                $table->id();
                $table->foreignId('policy_id')->constrained('insurance_policies')->onDelete('cascade');
                $table->string('claim_number')->unique();
                $table->text('description');
                $table->json('evidence_images')->nullable();
                $table->decimal('claimed_amount', 12, 2);
                $table->decimal('approved_amount', 12, 2)->nullable();
                $table->enum('status', ['pending', 'under_review', 'approved', 'rejected', 'paid'])->default('pending');
                $table->text('review_notes')->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->timestamps();
            });
        }

        // الضمان الممتد
        if (!Schema::hasTable('extended_warranties')) {
            Schema::create('extended_warranties', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->integer('extra_months');
                $table->decimal('price_percentage', 5, 2);
                $table->json('coverage_details')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // ضمانات ممتدة مشتراة
        if (!Schema::hasTable('purchased_warranties')) {
            Schema::create('purchased_warranties', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('order_item_id')->constrained()->onDelete('cascade');
                $table->foreignId('warranty_id')->constrained('extended_warranties')->onDelete('cascade');
                $table->decimal('price_paid', 10, 2);
                $table->date('original_warranty_end');
                $table->date('extended_warranty_end');
                $table->enum('status', ['active', 'expired', 'used'])->default('active');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('purchased_warranties');
        Schema::dropIfExists('extended_warranties');
        Schema::dropIfExists('insurance_claims');
        Schema::dropIfExists('insurance_policies');
        Schema::dropIfExists('part_insurances');
        Schema::dropIfExists('installment_payments');
        Schema::dropIfExists('installment_requests');
        Schema::dropIfExists('installment_plans');
    }
};
