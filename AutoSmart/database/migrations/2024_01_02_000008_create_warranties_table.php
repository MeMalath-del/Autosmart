<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warranty_claims', function (Blueprint $table) {
            $table->id();
            $table->string('claim_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->text('issue_description');
            $table->json('images')->nullable();
            $table->enum('status', [
                'pending',
                'under_review',
                'approved',
                'rejected',
                'in_repair',
                'replaced',
                'refunded',
                'closed'
            ])->default('pending');
            $table->text('store_response')->nullable();
            $table->text('admin_notes')->nullable();
            $table->enum('resolution', ['repair', 'replace', 'refund'])->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warranty_claims');
    }
};
