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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('plan_name', 50);
            $table->string('plan_code', 20);
            $table->decimal('monthly_price', 10, 2)->default(0.00);
            $table->decimal('yearly_price', 10, 2)->default(0.00);
            $table->decimal('setup_fee', 10, 2)->default(0.00);
            $table->integer('max_events');
            $table->integer('max_participants_per_event');
            $table->integer('max_storage_mb');
            $table->integer('max_users');
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
