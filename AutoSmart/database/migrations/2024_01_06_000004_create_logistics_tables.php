<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('express_delivery_zones')) {
            Schema::create('express_delivery_zones', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('city');
                $table->json('districts')->nullable();
                $table->decimal('same_day_fee', 10, 2);
                $table->decimal('express_fee', 10, 2);
                $table->time('cutoff_time')->default('14:00');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('express_deliveries')) {
            Schema::create('express_deliveries', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained()->onDelete('cascade');
                $table->foreignId('zone_id')->constrained('express_delivery_zones')->onDelete('cascade');
                $table->enum('type', ['same_day', 'express', 'scheduled']);
                $table->decimal('fee', 10, 2);
                $table->datetime('promised_delivery_time');
                $table->datetime('actual_delivery_time')->nullable();
                $table->enum('status', ['pending', 'assigned', 'picked_up', 'in_transit', 'delivered', 'failed'])->default('pending');
                $table->unsignedBigInteger('driver_id')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('smart_lockers')) {
            Schema::create('smart_lockers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->text('address');
                $table->string('city');
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
                $table->integer('total_compartments');
                $table->integer('available_compartments');
                $table->json('compartment_sizes')->nullable();
                $table->json('working_hours')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('locker_reservations')) {
            Schema::create('locker_reservations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained()->onDelete('cascade');
                $table->foreignId('locker_id')->constrained('smart_lockers')->onDelete('cascade');
                $table->string('compartment_number');
                $table->string('compartment_size');
                $table->string('access_code');
                $table->datetime('reserved_until');
                $table->datetime('picked_up_at')->nullable();
                $table->enum('status', ['reserved', 'stored', 'picked_up', 'expired'])->default('reserved');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('order_bundles')) {
            Schema::create('order_bundles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('bundle_code')->unique();
                $table->decimal('original_shipping_total', 10, 2);
                $table->decimal('bundled_shipping_cost', 10, 2);
                $table->decimal('savings', 10, 2);
                $table->datetime('bundle_deadline');
                $table->enum('status', ['open', 'closed', 'shipped'])->default('open');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('bundled_orders')) {
            Schema::create('bundled_orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('bundle_id')->constrained('order_bundles')->onDelete('cascade');
                $table->foreignId('order_id')->constrained()->onDelete('cascade');
                $table->decimal('original_shipping', 10, 2);
                $table->decimal('allocated_shipping', 10, 2);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('international_shipping_zones')) {
            Schema::create('international_shipping_zones', function (Blueprint $table) {
                $table->id();
                $table->string('country_code', 2);
                $table->string('country_name');
                $table->string('country_name_ar')->nullable();
                $table->string('zone');
                $table->decimal('base_rate', 10, 2);
                $table->decimal('per_kg_rate', 10, 2);
                $table->integer('estimated_days_min');
                $table->integer('estimated_days_max');
                $table->json('restricted_items')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('international_shipments')) {
            Schema::create('international_shipments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained()->onDelete('cascade');
                $table->foreignId('zone_id')->constrained('international_shipping_zones')->onDelete('cascade');
                $table->decimal('weight', 10, 3);
                $table->decimal('shipping_cost', 12, 2);
                $table->decimal('customs_fee', 10, 2)->default(0);
                $table->string('customs_declaration_number')->nullable();
                $table->string('tracking_number')->nullable();
                $table->enum('status', ['pending', 'customs_processing', 'in_transit', 'delivered', 'returned'])->default('pending');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('international_shipments');
        Schema::dropIfExists('international_shipping_zones');
        Schema::dropIfExists('bundled_orders');
        Schema::dropIfExists('order_bundles');
        Schema::dropIfExists('locker_reservations');
        Schema::dropIfExists('smart_lockers');
        Schema::dropIfExists('express_deliveries');
        Schema::dropIfExists('express_delivery_zones');
    }
};
