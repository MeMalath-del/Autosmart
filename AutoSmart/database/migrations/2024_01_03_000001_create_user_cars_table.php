<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // سيارات المستخدم (My Garage)
        Schema::create('user_cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('car_brand_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('car_model_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('year')->nullable();
            $table->string('vin')->nullable(); // رقم الشاسيه
            $table->string('plate_number')->nullable();
            $table->string('color')->nullable();
            $table->string('nickname')->nullable(); // اسم مختصر للسيارة
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        // سجل صيانة السيارة
        Schema::create('car_maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_car_id')->constrained()->onDelete('cascade');
            $table->date('maintenance_date');
            $table->string('type'); // oil_change, tire_rotation, etc
            $table->integer('mileage')->nullable();
            $table->text('description')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->string('service_provider')->nullable();
            $table->timestamps();
        });

        // القوائم المخصصة
        Schema::create('product_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false);
            $table->string('share_token')->nullable()->unique();
            $table->timestamps();
        });

        Schema::create('product_list_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_list_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['product_list_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_list_items');
        Schema::dropIfExists('product_lists');
        Schema::dropIfExists('car_maintenance_logs');
        Schema::dropIfExists('user_cars');
    }
};
