<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('obd_diagnostics')) {
            Schema::create('obd_diagnostics', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_car_id')->constrained()->onDelete('cascade');
                $table->unsignedBigInteger('workshop_id')->nullable();
                $table->string('diagnostic_code')->unique();
                $table->json('error_codes')->nullable();
                $table->json('live_data')->nullable();
                $table->json('freeze_frame')->nullable();
                $table->text('interpretation')->nullable();
                $table->json('recommended_repairs')->nullable();
                $table->decimal('estimated_cost', 12, 2)->nullable();
                $table->enum('severity', ['low', 'medium', 'high', 'critical'])->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('obd_codes_library')) {
            Schema::create('obd_codes_library', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('category');
                $table->string('system');
                $table->string('description');
                $table->string('description_ar')->nullable();
                $table->text('possible_causes')->nullable();
                $table->text('possible_solutions')->nullable();
                $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
                $table->json('related_parts')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('maintenance_records')) {
            Schema::create('maintenance_records', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_car_id')->constrained()->onDelete('cascade');
                $table->unsignedBigInteger('workshop_id')->nullable();
                $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
                $table->string('record_type');
                $table->date('service_date');
                $table->integer('mileage_at_service')->nullable();
                $table->text('description')->nullable();
                $table->json('parts_used')->nullable();
                $table->decimal('labor_cost', 10, 2)->default(0);
                $table->decimal('parts_cost', 10, 2)->default(0);
                $table->decimal('total_cost', 10, 2)->default(0);
                $table->json('attachments')->nullable();
                $table->integer('next_service_mileage')->nullable();
                $table->date('next_service_date')->nullable();
                $table->text('technician_notes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('repair_cost_templates')) {
            Schema::create('repair_cost_templates', function (Blueprint $table) {
                $table->id();
                $table->string('repair_type');
                $table->string('repair_type_ar')->nullable();
                $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
                $table->json('applicable_car_types')->nullable();
                $table->decimal('min_labor_hours', 5, 2);
                $table->decimal('max_labor_hours', 5, 2);
                $table->decimal('avg_labor_rate', 10, 2)->default(100);
                $table->json('required_parts')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('repair_estimates')) {
            Schema::create('repair_estimates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->foreignId('user_car_id')->nullable()->constrained()->onDelete('set null');
                $table->unsignedBigInteger('workshop_id')->nullable();
                $table->string('estimate_number')->unique();
                $table->json('repair_items')->nullable();
                $table->decimal('parts_estimate', 12, 2)->default(0);
                $table->decimal('labor_estimate', 12, 2)->default(0);
                $table->decimal('total_estimate', 12, 2)->default(0);
                $table->decimal('discount', 10, 2)->default(0);
                $table->decimal('final_estimate', 12, 2)->default(0);
                $table->text('notes')->nullable();
                $table->enum('status', ['draft', 'sent', 'accepted', 'rejected', 'expired'])->default('draft');
                $table->timestamp('valid_until')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('workshop_part_reservations')) {
            Schema::create('workshop_part_reservations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('workshop_id');
                $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('user_car_id')->nullable()->constrained()->onDelete('set null');
                $table->string('reservation_number')->unique();
                $table->json('parts')->nullable();
                $table->decimal('total_amount', 12, 2);
                $table->decimal('deposit_amount', 10, 2)->default(0);
                $table->boolean('deposit_paid')->default(false);
                $table->enum('status', ['pending', 'confirmed', 'parts_ordered', 'parts_received', 'installed', 'cancelled'])->default('pending');
                $table->date('installation_date')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('workshop_part_reservations');
        Schema::dropIfExists('repair_estimates');
        Schema::dropIfExists('repair_cost_templates');
        Schema::dropIfExists('maintenance_records');
        Schema::dropIfExists('obd_codes_library');
        Schema::dropIfExists('obd_diagnostics');
    }
};
