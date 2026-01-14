<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('forum_categories')) {
            Schema::create('forum_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('name_ar')->nullable();
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('icon')->nullable();
                $table->integer('sort_order')->default(0);
                $table->integer('topics_count')->default(0);
                $table->integer('posts_count')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('forum_topics')) {
            Schema::create('forum_topics', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->constrained('forum_categories')->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('content');
                $table->json('tags')->nullable();
                $table->boolean('is_pinned')->default(false);
                $table->boolean('is_locked')->default(false);
                $table->boolean('is_solved')->default(false);
                $table->integer('views_count')->default(0);
                $table->integer('replies_count')->default(0);
                $table->integer('likes_count')->default(0);
                $table->timestamp('last_reply_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('forum_replies')) {
            Schema::create('forum_replies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('topic_id')->constrained('forum_topics')->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('parent_id')->nullable()->constrained('forum_replies')->onDelete('cascade');
                $table->text('content');
                $table->boolean('is_solution')->default(false);
                $table->integer('likes_count')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('blog_posts')) {
            Schema::create('blog_posts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('excerpt')->nullable();
                $table->longText('content');
                $table->string('featured_image')->nullable();
                $table->json('tags')->nullable();
                $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
                $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
                $table->integer('views_count')->default(0);
                $table->integer('likes_count')->default(0);
                $table->integer('comments_count')->default(0);
                $table->integer('reading_time')->default(5);
                $table->timestamp('published_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('blog_comments')) {
            Schema::create('blog_comments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('post_id')->constrained('blog_posts')->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('parent_id')->nullable()->constrained('blog_comments')->onDelete('cascade');
                $table->text('content');
                $table->boolean('is_approved')->default(true);
                $table->integer('likes_count')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('tutorial_videos')) {
            Schema::create('tutorial_videos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('video_url');
                $table->string('video_id')->nullable();
                $table->string('thumbnail')->nullable();
                $table->integer('duration')->nullable();
                $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
                $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
                $table->json('car_models')->nullable();
                $table->json('tags')->nullable();
                $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
                $table->integer('views_count')->default(0);
                $table->integer('likes_count')->default(0);
                $table->boolean('is_featured')->default(false);
                $table->boolean('is_approved')->default(false);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('user_badges')) {
            Schema::create('user_badges', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('name_ar')->nullable();
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('icon')->nullable();
                $table->string('color')->default('#3b82f6');
                $table->json('criteria')->nullable();
                $table->integer('points_value')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('user_earned_badges')) {
            Schema::create('user_earned_badges', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('badge_id')->constrained('user_badges')->onDelete('cascade');
                $table->timestamp('earned_at');
                $table->timestamps();
                $table->unique(['user_id', 'badge_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_earned_badges');
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('tutorial_videos');
        Schema::dropIfExists('blog_comments');
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('forum_replies');
        Schema::dropIfExists('forum_topics');
        Schema::dropIfExists('forum_categories');
    }
};
