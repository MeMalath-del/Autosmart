<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->integer('quality_rating')->nullable()->after('rating');
            $table->integer('price_rating')->nullable()->after('quality_rating');
            $table->integer('shipping_rating')->nullable()->after('price_rating');
            $table->json('images')->nullable()->after('comment');
            $table->integer('helpful_count')->default(0)->after('is_approved');
            $table->integer('unhelpful_count')->default(0)->after('helpful_count');
        });

        Schema::create('review_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained()->onDelete('cascade');
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->text('response');
            $table->timestamps();
        });

        Schema::create('review_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('is_helpful');
            $table->timestamps();

            $table->unique(['review_id', 'user_id']);
        });

        Schema::create('review_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('reason');
            $table->text('details')->nullable();
            $table->enum('status', ['pending', 'reviewed', 'dismissed'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_reports');
        Schema::dropIfExists('review_votes');
        Schema::dropIfExists('review_responses');

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['quality_rating', 'price_rating', 'shipping_rating', 'images', 'helpful_count', 'unhelpful_count']);
        });
    }
};
