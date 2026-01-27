<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['customer', 'seller', 'workshop', 'admin'])->default('customer')->after('email');
            $table->string('phone')->nullable()->after('role');
            $table->string('avatar')->nullable()->after('phone');
            $table->text('address')->nullable()->after('avatar');
            $table->string('city')->nullable()->after('address');
            $table->string('country')->default('Saudi Arabia')->after('city');
            $table->boolean('is_active')->default(true)->after('country');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'avatar', 'address', 'city', 'country', 'is_active', 'last_login_at']);
        });
    }
};
