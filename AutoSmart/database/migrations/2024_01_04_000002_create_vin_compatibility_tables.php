<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // بيانات VIN
        Schema::create('vin_lookups', function (Blueprint $table) {
            $table->id();
            $table->string('vin', 17)->unique();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->integer('year')->nullable();
            $table->string('engine')->nullable();
            $table->string('transmission')->nullable();
            $table->string('body_type')->nullable();
            $table->string('fuel_type')->nullable();
            $table->json('raw_data')->nullable();
            $table->timestamps();
        });

        // سجل البحث بالـ VIN
        Schema::create('vin_searches', function (Blueprint $table) {
            $table->id();
            $table->string('vin', 17);
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('vin_lookup_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('results_count')->default(0);
            $table->timestamps();
        });

        // توافق المنتجات مع VIN
        Schema::create('product_vin_compatibility', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('vin_pattern'); // pattern للمطابقة
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->integer('year_from')->nullable();
            $table->integer('year_to')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_vin_compatibility');
        Schema::dropIfExists('vin_searches');
        Schema::dropIfExists('vin_lookups');
    }
};
