<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // خيارات التخصيص للمنتج
        Schema::create('product_customizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('name'); // engraving, color, size
            $table->string('name_ar')->nullable();
            $table->enum('type', ['text', 'select', 'color', 'image'])->default('text');
            $table->json('options')->nullable(); // for select type
            $table->decimal('extra_price', 10, 2)->default(0);
            $table->boolean('is_required')->default(false);
            $table->integer('max_length')->nullable(); // for text
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // تخصيصات الطلب
        Schema::create('order_item_customizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('customization_id')->constrained('product_customizations')->onDelete('cascade');
            $table->text('value');
            $table->decimal('price', 10, 2)->default(0);
            $table->timestamps();
        });

        // المنتجات الرقمية
        Schema::create('digital_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type');
            $table->bigInteger('file_size');
            $table->integer('download_limit')->nullable();
            $table->integer('expiry_days')->nullable();
            $table->string('preview_path')->nullable();
            $table->timestamps();
        });

        // تحميلات المنتجات الرقمية
        Schema::create('digital_downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('digital_product_id')->constrained()->onDelete('cascade');
            $table->string('download_token')->unique();
            $table->integer('download_count')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('first_download_at')->nullable();
            $table->timestamp('last_download_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digital_downloads');
        Schema::dropIfExists('digital_products');
        Schema::dropIfExists('order_item_customizations');
        Schema::dropIfExists('product_customizations');
    }
};
