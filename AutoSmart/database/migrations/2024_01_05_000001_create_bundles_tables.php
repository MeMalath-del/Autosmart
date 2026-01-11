<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // المنتجات المجمعة
        Schema::create('product_bundles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->decimal('regular_price', 12, 2);
            $table->decimal('bundle_price', 12, 2);
            $table->decimal('savings', 12, 2)->virtualAs('regular_price - bundle_price');
            $table->integer('quantity')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });

        // عناصر الباقة
        Schema::create('bundle_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bundle_id')->constrained('product_bundles')->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->timestamps();
        });

        // الطلب المسبق
        Schema::create('preorders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->decimal('deposit_amount', 10, 2)->nullable();
            $table->boolean('deposit_paid')->default(false);
            $table->date('expected_date')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'ready', 'converted', 'cancelled'])->default('pending');
            $table->boolean('notify_when_available')->default(true);
            $table->timestamps();
        });

        // إعدادات الطلب المسبق للمنتج
        Schema::create('product_preorder_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->boolean('allow_preorder')->default(false);
            $table->decimal('deposit_percentage', 5, 2)->default(20);
            $table->date('expected_availability')->nullable();
            $table->integer('max_preorders')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_preorder_settings');
        Schema::dropIfExists('preorders');
        Schema::dropIfExists('bundle_items');
        Schema::dropIfExists('product_bundles');
    }
};
