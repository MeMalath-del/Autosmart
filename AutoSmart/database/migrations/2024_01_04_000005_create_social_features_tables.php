<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // المتابعات
        Schema::create('follows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('follower_id')->constrained('users')->onDelete('cascade');
            $table->morphs('followable');
            $table->timestamps();

            $table->unique(['follower_id', 'followable_type', 'followable_id']);
        });

        // مراجعات الفيديو
        Schema::create('video_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('video_path');
            $table->string('thumbnail')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->integer('rating')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });

        // المؤثرين
        Schema::create('influencers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('code')->unique();
            $table->text('bio')->nullable();
            $table->string('youtube')->nullable();
            $table->string('instagram')->nullable();
            $table->string('twitter')->nullable();
            $table->string('tiktok')->nullable();
            $table->decimal('commission_rate', 5, 2)->default(5);
            $table->decimal('total_earnings', 12, 2)->default(0);
            $table->integer('total_sales')->default(0);
            $table->enum('status', ['pending', 'approved', 'suspended'])->default('pending');
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });

        // مبيعات المؤثرين
        Schema::create('influencer_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('influencer_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->decimal('order_total', 12, 2);
            $table->decimal('commission', 12, 2);
            $table->enum('status', ['pending', 'confirmed', 'paid'])->default('pending');
            $table->timestamps();
        });

        // المجموعات
        Schema::create('community_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->foreignId('car_brand_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['public', 'private'])->default('public');
            $table->integer('members_count')->default(0);
            $table->integer('posts_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // أعضاء المجموعات
        Schema::create('group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('community_groups')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['member', 'moderator', 'admin'])->default('member');
            $table->timestamps();

            $table->unique(['group_id', 'user_id']);
        });

        // منشورات المجموعات
        Schema::create('group_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('community_groups')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('content');
            $table->json('images')->nullable();
            $table->integer('likes_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->boolean('is_pinned')->default(false);
            $table->timestamps();
        });

        // تعليقات المنشورات
        Schema::create('post_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('group_posts')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('post_comments')->onDelete('cascade');
            $table->text('content');
            $table->integer('likes_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_comments');
        Schema::dropIfExists('group_posts');
        Schema::dropIfExists('group_members');
        Schema::dropIfExists('community_groups');
        Schema::dropIfExists('influencer_sales');
        Schema::dropIfExists('influencers');
        Schema::dropIfExists('video_reviews');
        Schema::dropIfExists('follows');
    }
};
