<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // طلبات البحث عن قطع غيار
        Schema::create('part_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('car_brand_id')->nullable()->constrained('car_brands')->onDelete('set null');
            $table->foreignId('car_model_id')->nullable()->constrained('car_models')->onDelete('set null');
            $table->integer('car_year')->nullable();
            $table->string('part_name');
            $table->text('description')->nullable();
            $table->string('part_number')->nullable();
            $table->json('images')->nullable();
            $table->enum('urgency', ['low', 'medium', 'high'])->default('medium');
            $table->enum('status', ['open', 'quoted', 'closed', 'expired'])->default('open');
            $table->decimal('budget_min', 10, 2)->nullable();
            $table->decimal('budget_max', 10, 2)->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        // عروض الأسعار على طلبات القطع
        Schema::create('part_request_quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('part_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->decimal('price', 10, 2);
            $table->enum('condition', ['new', 'used', 'refurbished'])->default('new');
            $table->string('warranty')->nullable();
            $table->text('notes')->nullable();
            $table->integer('delivery_days')->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('part_request_quotes');
        Schema::dropIfExists('part_requests');
    }
};
