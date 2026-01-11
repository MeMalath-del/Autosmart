<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // خدمات التركيب
        Schema::create('installation_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('workshop_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('duration_minutes');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // حجوزات التركيب
        Schema::create('installation_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->constrained('installation_services')->onDelete('cascade');
            $table->foreignId('workshop_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('booking_number')->unique();
            $table->date('booking_date');
            $table->time('booking_time');
            $table->enum('status', ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->text('user_car_details')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // جلسات الاستشارة
        Schema::create('consultation_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('expert_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('session_number')->unique();
            $table->enum('type', ['phone', 'video', 'chat']);
            $table->string('topic');
            $table->text('description')->nullable();
            $table->integer('duration_minutes')->default(30);
            $table->decimal('price', 10, 2)->default(0);
            $table->datetime('scheduled_at');
            $table->enum('status', ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->integer('rating')->nullable();
            $table->text('review')->nullable();
            $table->string('meeting_link')->nullable();
            $table->timestamps();
        });

        // خبراء الاستشارات
        Schema::create('consultation_experts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('specialty');
            $table->text('bio')->nullable();
            $table->json('certifications')->nullable();
            $table->integer('experience_years');
            $table->decimal('hourly_rate', 10, 2);
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('total_sessions')->default(0);
            $table->json('available_hours')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // تتبع عمر القطع
        Schema::create('part_lifecycles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_car_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->date('installation_date');
            $table->integer('installation_mileage')->nullable();
            $table->integer('expected_lifespan_km')->nullable();
            $table->integer('expected_lifespan_months')->nullable();
            $table->date('expected_replacement_date')->nullable();
            $table->integer('expected_replacement_mileage')->nullable();
            $table->date('actual_replacement_date')->nullable();
            $table->integer('actual_replacement_mileage')->nullable();
            $table->enum('status', ['active', 'warning', 'expired', 'replaced'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // برنامج الاستبدال
        Schema::create('trade_in_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('old_part_name');
            $table->string('old_part_brand')->nullable();
            $table->text('old_part_condition');
            $table->json('old_part_images')->nullable();
            $table->decimal('estimated_value', 10, 2)->nullable();
            $table->decimal('offered_discount', 10, 2)->nullable();
            $table->enum('status', ['pending', 'evaluated', 'approved', 'rejected', 'completed'])->default('pending');
            $table->text('evaluation_notes')->nullable();
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trade_in_requests');
        Schema::dropIfExists('part_lifecycles');
        Schema::dropIfExists('consultation_experts');
        Schema::dropIfExists('consultation_sessions');
        Schema::dropIfExists('installation_bookings');
        Schema::dropIfExists('installation_services');
    }
};
