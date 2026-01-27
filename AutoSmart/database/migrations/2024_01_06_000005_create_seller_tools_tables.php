<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('product_imports')) {
            Schema::create('product_imports', function (Blueprint $table) {
                $table->id();
                $table->foreignId('store_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('file_path');
                $table->string('file_name');
                $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
                $table->integer('total_rows')->default(0);
                $table->integer('processed_rows')->default(0);
                $table->integer('success_count')->default(0);
                $table->integer('error_count')->default(0);
                $table->json('errors')->nullable();
                $table->json('mapping')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('import_logs')) {
            Schema::create('import_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('import_id')->constrained('product_imports')->onDelete('cascade');
                $table->integer('row_number');
                $table->enum('status', ['success', 'error', 'skipped']);
                $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
                $table->text('error_message')->nullable();
                $table->json('row_data')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('external_store_connections')) {
            Schema::create('external_store_connections', function (Blueprint $table) {
                $table->id();
                $table->foreignId('store_id')->constrained()->onDelete('cascade');
                $table->string('platform');
                $table->string('external_store_id')->nullable();
                $table->json('credentials')->nullable();
                $table->json('settings')->nullable();
                $table->enum('status', ['pending', 'connected', 'error', 'disconnected'])->default('pending');
                $table->timestamp('last_sync_at')->nullable();
                $table->integer('products_synced')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('synced_products')) {
            Schema::create('synced_products', function (Blueprint $table) {
                $table->id();
                $table->foreignId('connection_id')->constrained('external_store_connections')->onDelete('cascade');
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->string('external_product_id');
                $table->enum('sync_direction', ['import', 'export', 'both'])->default('both');
                $table->boolean('sync_price')->default(true);
                $table->boolean('sync_stock')->default(true);
                $table->timestamp('last_synced_at')->nullable();
                $table->timestamps();
                $table->unique(['connection_id', 'product_id']);
            });
        }

        if (! Schema::hasTable('seasonal_campaigns')) {
            Schema::create('seasonal_campaigns', function (Blueprint $table) {
                $table->id();
                $table->foreignId('store_id')->nullable()->constrained()->onDelete('cascade');
                $table->string('name');
                $table->string('name_ar')->nullable();
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('banner_image')->nullable();
                $table->string('theme_color')->nullable();
                $table->enum('type', ['ramadan', 'eid', 'national_day', 'black_friday', 'back_to_school', 'summer', 'winter', 'custom']);
                $table->decimal('discount_percentage', 5, 2)->nullable();
                $table->json('applicable_categories')->nullable();
                $table->json('applicable_products')->nullable();
                $table->datetime('starts_at');
                $table->datetime('ends_at');
                $table->boolean('is_active')->default(true);
                $table->boolean('is_featured')->default(false);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('competitor_prices')) {
            Schema::create('competitor_prices', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->string('competitor_name');
                $table->string('competitor_url')->nullable();
                $table->decimal('price', 12, 2);
                $table->decimal('shipping_cost', 10, 2)->nullable();
                $table->boolean('in_stock')->default(true);
                $table->timestamp('checked_at');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('competitive_reports')) {
            Schema::create('competitive_reports', function (Blueprint $table) {
                $table->id();
                $table->foreignId('store_id')->constrained()->onDelete('cascade');
                $table->date('report_date');
                $table->decimal('avg_price_vs_market', 5, 2)->nullable();
                $table->integer('products_cheaper')->default(0);
                $table->integer('products_expensive')->default(0);
                $table->integer('products_same')->default(0);
                $table->decimal('market_share_estimate', 5, 2)->nullable();
                $table->json('category_breakdown')->nullable();
                $table->json('recommendations')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('competitive_reports');
        Schema::dropIfExists('competitor_prices');
        Schema::dropIfExists('seasonal_campaigns');
        Schema::dropIfExists('synced_products');
        Schema::dropIfExists('external_store_connections');
        Schema::dropIfExists('import_logs');
        Schema::dropIfExists('product_imports');
    }
};
