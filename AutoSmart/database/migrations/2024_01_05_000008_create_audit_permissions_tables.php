<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // سجل التدقيق الشامل
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('user_type')->default('user');
            $table->string('event'); // created, updated, deleted, login, etc.
            $table->string('auditable_type');
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('url')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();
            
            $table->index(['auditable_type', 'auditable_id']);
        });

        // مجموعات الصلاحيات
        Schema::create('permission_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // الصلاحيات التفصيلية
        Schema::create('granular_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('permission_groups')->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // صلاحيات الأدوار
        Schema::create('role_granular_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('granular_permissions')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['role_id', 'permission_id']);
        });

        // صلاحيات المستخدمين المباشرة
        Schema::create('user_granular_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('granular_permissions')->onDelete('cascade');
            $table->boolean('is_granted')->default(true); // true = grant, false = deny
            $table->timestamps();
            
            $table->unique(['user_id', 'permission_id']);
        });

        // النسخ الاحتياطية
        Schema::create('backups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('disk');
            $table->string('path');
            $table->bigInteger('size');
            $table->enum('type', ['full', 'database', 'files']);
            $table->enum('status', ['pending', 'in_progress', 'completed', 'failed']);
            $table->text('error_message')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // جدولة النسخ الاحتياطي
        Schema::create('backup_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('frequency', ['daily', 'weekly', 'monthly']);
            $table->time('time')->default('02:00');
            $table->integer('day_of_week')->nullable();
            $table->integer('day_of_month')->nullable();
            $table->enum('type', ['full', 'database', 'files']);
            $table->integer('retention_days')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('next_run_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_schedules');
        Schema::dropIfExists('backups');
        Schema::dropIfExists('user_granular_permissions');
        Schema::dropIfExists('role_granular_permissions');
        Schema::dropIfExists('granular_permissions');
        Schema::dropIfExists('permission_groups');
        Schema::dropIfExists('audit_logs');
    }
};
