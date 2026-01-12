<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // لوحة البيانات التفاعلية
        if (!Schema::hasTable('')) { Schema::create('dashboard_widgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('widget_type'); // sales_chart, orders_count, etc
            $table->string('title')->nullable();
            $table->json('config')->nullable();
            $table->integer('position_x')->default(0);
            $table->integer('position_y')->default(0);
            $table->integer('width')->default(1);
            $table->integer('height')->default(1);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        }); }

        // تقارير مخصصة
        if (!Schema::hasTable('')) { Schema::create('custom_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('report_type'); // sales, inventory, customers, etc
            $table->json('columns')->nullable();
            $table->json('filters')->nullable();
            $table->json('grouping')->nullable();
            $table->json('sorting')->nullable();
            $table->string('chart_type')->nullable();
            $table->boolean('is_scheduled')->default(false);
            $table->string('schedule_frequency')->nullable();
            $table->timestamp('last_run_at')->nullable();
            $table->timestamps();
        }); }

        // تصدير التقارير
        if (!Schema::hasTable('')) { Schema::create('report_exports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->nullable()->constrained('custom_reports')->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('export_type'); // csv, excel, pdf
            $table->string('file_path')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        }); }

        // تحليل الربحية
        if (!Schema::hasTable('')) { Schema::create('profitability_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->date('record_date');
            $table->integer('units_sold')->default(0);
            $table->decimal('revenue', 12, 2)->default(0);
            $table->decimal('cost_of_goods', 12, 2)->default(0);
            $table->decimal('gross_profit', 12, 2)->default(0);
            $table->decimal('gross_margin', 5, 2)->default(0);
            $table->decimal('fees', 10, 2)->default(0); // platform fees
            $table->decimal('net_profit', 12, 2)->default(0);
            $table->timestamps();
            
            $table->unique(['product_id', 'store_id', 'record_date']);
        }); }

        // التحقق من الهوية KYC
        if (!Schema::hasTable('')) { Schema::create('kyc_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('user_type', ['seller', 'workshop', 'b2b']);
            $table->string('id_type'); // national_id, passport, commercial_register
            $table->string('id_number');
            $table->string('id_front_image')->nullable();
            $table->string('id_back_image')->nullable();
            $table->string('selfie_image')->nullable();
            $table->string('commercial_register')->nullable();
            $table->string('vat_certificate')->nullable();
            $table->enum('status', ['pending', 'under_review', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        }); }

        // كشف الاحتيال
        if (!Schema::hasTable('')) { Schema::create('fraud_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->string('alert_type'); // multiple_accounts, suspicious_payment, etc
            $table->decimal('risk_score', 5, 2);
            $table->json('risk_factors')->nullable();
            $table->enum('status', ['new', 'investigating', 'confirmed', 'false_positive'])->default('new');
            $table->text('notes')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        }); }

        // قواعد كشف الاحتيال
        if (!Schema::hasTable('')) { Schema::create('fraud_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('rule_type'); // velocity, amount, behavior
            $table->json('conditions')->nullable();
            $table->decimal('risk_weight', 5, 2)->default(1);
            $table->enum('action', ['flag', 'block', 'review'])->default('flag');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        }); }

        // التوقيع الإلكتروني
        if (!Schema::hasTable('')) { Schema::create('digital_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('document_type'); // warranty, contract, agreement
            $table->morphs('signable'); // polymorphic
            $table->string('signature_hash');
            $table->string('ip_address');
            $table->string('user_agent')->nullable();
            $table->json('signature_data')->nullable(); // drawn signature coordinates
            $table->timestamp('signed_at');
            $table->timestamps();
        }); }

        // سجل الامتثال
        if (!Schema::hasTable('')) { Schema::create('compliance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action'); // data_export, data_delete, consent_given, etc
            $table->string('data_type')->nullable();
            $table->json('details')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        }); }
    }

    public function down(): void
    {
        Schema::dropIfExists('compliance_logs');
        Schema::dropIfExists('digital_signatures');
        Schema::dropIfExists('fraud_rules');
        Schema::dropIfExists('fraud_alerts');
        Schema::dropIfExists('kyc_verifications');
        Schema::dropIfExists('profitability_records');
        Schema::dropIfExists('report_exports');
        Schema::dropIfExists('custom_reports');
        Schema::dropIfExists('dashboard_widgets');
    }
};
