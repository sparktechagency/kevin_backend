<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('plan_id')->nullable()->constrained()->onDelete('set null');
            $table->string('stripe_payment_intent_id')->nullable()->unique();
            $table->string('stripe_invoice_id')->nullable()->index();
            $table->string('type')->default('subscription'); // subscription, one_time, refund, credit
            $table->string('status'); // pending, processing, completed, failed, refunded, disputed
            $table->decimal('total_amount', 10, 2);
            $table->string('currency', 3)->default('usd');
            $table->string('payment_method')->nullable()->default('card'); // card, bank_transfer, etc.
            $table->timestamp('paid_at')->nullable();

            $table->index(['user_id', 'created_at']);
            $table->index(['status', 'created_at']);
            $table->index(['type', 'created_at']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
