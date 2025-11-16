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
        if (!Schema::hasTable('payment_transactions')) {
            Schema::create('payment_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('registration_id')->nullable();
                $table->string('transaction_reference', 255)->unique();
                $table->string('external_reference', 255)->nullable();
                $table->decimal('amount', 10, 2);
                $table->string('currency', 10)->default('FCFA');
                $table->decimal('fees', 10, 2)->default(0.00);
                $table->enum('payment_method', ['mobile_money', 'bank_card', 'bank_transfer', 'cash', 'check']);
                $table->string('payment_provider', 100)->nullable();
                $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded'])->default('pending');
                $table->timestamp('payment_date')->nullable();
                $table->timestamp('processed_date')->nullable();
                $table->text('metadata')->nullable();
                $table->timestamps();

                $table->index('status', 'idx_status');
                $table->index('payment_date', 'idx_payment_date');
                $table->foreign('registration_id')->references('id')->on('registrations')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};

