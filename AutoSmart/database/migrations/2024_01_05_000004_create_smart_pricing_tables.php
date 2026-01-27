<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // قواعد التسعير الديناميكي
        Schema::create('dynamic_pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['demand', 'time', 'inventory', 'competitor', 'customer_segment']);
            $table->enum('action', ['increase', 'decrease', 'set']);
            $table->enum('value_type', ['percentage', 'fixed']);
            $table->decimal('value', 10, 2);
            $table->decimal('min_price', 12, 2)->nullable();
            $table->decimal('max_price', 12, 2)->nullable();
            $table->json('conditions')->nullable();
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });

        // سجل تغييرات الأسعار
        Schema::create('price_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->decimal('old_price', 12, 2);
            $table->decimal('new_price', 12, 2);
            $table->string('reason')->nullable();
            $table->foreignId('rule_id')->nullable()->constrained('dynamic_pricing_rules')->onDelete('set null');
            $table->foreignId('changed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // تنبيهات المخزون
        Schema::create('inventory_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['low_stock', 'out_of_stock', 'overstock', 'expiring']);
            $table->integer('threshold');
            $table->integer('current_quantity');
            $table->integer('predicted_days_left')->nullable();
            $table->integer('suggested_reorder_qty')->nullable();
            $table->boolean('is_resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        // توقعات الطلب
        Schema::create('demand_forecasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->date('forecast_date');
            $table->integer('predicted_demand');
            $table->integer('actual_demand')->nullable();
            $table->decimal('confidence', 5, 2)->nullable();
            $table->json('factors')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demand_forecasts');
        Schema::dropIfExists('inventory_alerts');
        Schema::dropIfExists('price_history');
        Schema::dropIfExists('dynamic_pricing_rules');
    }
};
