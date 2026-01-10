<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // المندوبين
        Schema::create('delivery_drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('store_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->string('phone');
            $table->string('vehicle_type')->nullable();
            $table->string('vehicle_number')->nullable();
            $table->string('license_number')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->enum('status', ['available', 'busy', 'offline'])->default('offline');
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('deliveries_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_location_at')->nullable();
            $table->timestamps();
        });

        // تتبع التوصيل
        Schema::create('delivery_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained('delivery_drivers')->onDelete('set null');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('status');
            $table->text('notes')->nullable();
            $table->integer('estimated_minutes')->nullable();
            $table->timestamps();
        });

        // سجل مواقع المندوب
        Schema::create('driver_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('delivery_drivers')->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('speed', 5, 2)->nullable();
            $table->decimal('heading', 5, 2)->nullable();
            $table->timestamps();
        });

        // نقاط الاستلام
        Schema::create('pickup_points', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->text('address');
            $table->string('city');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('phone')->nullable();
            $table->json('working_hours')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pickup_points');
        Schema::dropIfExists('driver_locations');
        Schema::dropIfExists('delivery_tracking');
        Schema::dropIfExists('delivery_drivers');
    }
};
