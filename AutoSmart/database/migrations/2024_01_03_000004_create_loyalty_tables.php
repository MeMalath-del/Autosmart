<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // نقاط الولاء
        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('points')->default(0);
            $table->integer('lifetime_points')->default(0);
            $table->enum('tier', ['bronze', 'silver', 'gold', 'platinum'])->default('bronze');
            $table->timestamps();
        });

        Schema::create('loyalty_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['earned', 'redeemed', 'expired', 'bonus']);
            $table->integer('points');
            $table->string('description');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        // إعدادات الولاء
        Schema::create('loyalty_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('points_per_sar')->default(1); // نقطة لكل ريال
            $table->decimal('sar_per_point', 8, 4)->default(0.01); // قيمة النقطة
            $table->integer('min_redeem_points')->default(100);
            $table->integer('points_expiry_days')->default(365);
            $table->json('tier_thresholds')->nullable();
            $table->json('tier_multipliers')->nullable();
            $table->timestamps();
        });

        // الإحالات
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('referred_id')->constrained('users')->onDelete('cascade');
            $table->string('referral_code');
            $table->enum('status', ['pending', 'qualified', 'rewarded'])->default('pending');
            $table->decimal('referrer_reward', 10, 2)->default(0);
            $table->decimal('referred_reward', 10, 2)->default(0);
            $table->timestamp('qualified_at')->nullable();
            $table->timestamps();
        });

        // أكواد الإحالة
        Schema::table('users', function (Blueprint $table) {
            $table->string('referral_code')->nullable()->unique()->after('role');
            $table->foreignId('referred_by')->nullable()->after('referral_code');
        });

        // حملات البريد
        Schema::create('email_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subject');
            $table->text('body');
            $table->enum('status', ['draft', 'scheduled', 'sending', 'sent'])->default('draft');
            $table->string('segment')->nullable(); // all, customers, sellers, inactive
            $table->integer('recipients_count')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('opened_count')->default(0);
            $table->integer('clicked_count')->default(0);
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('email_campaign_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_campaign_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('email');
            $table->enum('status', ['sent', 'failed', 'opened', 'clicked']);
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->timestamps();
        });

        // السلات المتروكة
        Schema::create('abandoned_cart_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('reminder_number')->default(1);
            $table->boolean('email_sent')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->boolean('recovered')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abandoned_cart_reminders');
        Schema::dropIfExists('email_campaign_logs');
        Schema::dropIfExists('email_campaigns');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['referral_code', 'referred_by']);
        });

        Schema::dropIfExists('referrals');
        Schema::dropIfExists('loyalty_settings');
        Schema::dropIfExists('loyalty_transactions');
        Schema::dropIfExists('loyalty_points');
    }
};
