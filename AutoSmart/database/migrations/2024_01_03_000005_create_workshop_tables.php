<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ورش الصيانة
        Schema::create('workshops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('whatsapp')->nullable();
            $table->text('address');
            $table->string('city');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->json('working_hours')->nullable();
            $table->json('specialties')->nullable(); // تخصصات الورشة
            $table->json('car_brands')->nullable(); // الماركات المتخصص بها
            $table->enum('status', ['pending', 'approved', 'suspended'])->default('pending');
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('reviews_count')->default(0);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        // خدمات الورشة
        Schema::create('workshop_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price_from', 10, 2)->nullable();
            $table->decimal('price_to', 10, 2)->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // طلبات الصيانة
        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_car_id')->nullable()->constrained()->onDelete('set null');
            $table->string('car_info')->nullable(); // معلومات السيارة للزوار
            $table->text('issue_description');
            $table->json('images')->nullable();
            $table->enum('urgency', ['low', 'medium', 'high'])->default('medium');
            $table->enum('status', ['open', 'quoted', 'booked', 'in_progress', 'completed', 'cancelled'])->default('open');
            $table->timestamp('preferred_date')->nullable();
            $table->string('preferred_time')->nullable();
            $table->string('city')->nullable();
            $table->timestamps();
        });

        // عروض أسعار الورش
        Schema::create('maintenance_quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('workshop_id')->constrained()->onDelete('cascade');
            $table->decimal('labor_cost', 10, 2);
            $table->decimal('parts_cost', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->text('description')->nullable();
            $table->integer('estimated_hours')->nullable();
            $table->date('available_date')->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected', 'expired'])->default('pending');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        // حجوزات الصيانة
        Schema::create('maintenance_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_request_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('maintenance_quote_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('workshop_id')->constrained()->onDelete('cascade');
            $table->foreignId('workshop_service_id')->nullable()->constrained()->onDelete('set null');
            $table->string('booking_number')->unique();
            $table->date('booking_date');
            $table->time('booking_time');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->decimal('final_cost', 10, 2)->nullable();
            $table->timestamps();
        });

        // تقييمات الورش
        Schema::create('workshop_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('maintenance_booking_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('rating');
            $table->integer('service_rating')->nullable();
            $table->integer('price_rating')->nullable();
            $table->integer('time_rating')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('is_approved')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workshop_reviews');
        Schema::dropIfExists('maintenance_bookings');
        Schema::dropIfExists('maintenance_quotes');
        Schema::dropIfExists('maintenance_requests');
        Schema::dropIfExists('workshop_services');
        Schema::dropIfExists('workshops');
    }
};
