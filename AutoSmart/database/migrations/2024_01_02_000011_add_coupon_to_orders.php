<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->after('discount')->constrained()->onDelete('set null');
            $table->foreignId('address_id')->nullable()->after('shipping_postal_code')->constrained()->onDelete('set null');
            $table->string('coupon_code')->nullable()->after('coupon_id');
            $table->decimal('coupon_discount', 10, 2)->default(0)->after('coupon_code');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
            $table->dropForeign(['address_id']);
            $table->dropColumn(['coupon_id', 'coupon_code', 'coupon_discount', 'address_id']);
        });
    }
};
