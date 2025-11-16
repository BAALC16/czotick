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
        if (!Schema::hasTable('events')) {
            Schema::create('events', function (Blueprint $table) {
                $table->id();
                $table->string('event_title', 255);
                $table->text('event_description')->nullable();
                $table->date('event_date');
                $table->time('event_start_time');
                $table->time('event_end_time')->nullable();
                $table->string('event_location', 255);
                $table->string('event_address', 500)->nullable();
                $table->decimal('event_price', 10, 2)->default(0);
                $table->string('currency', 3)->default('XOF');
                $table->integer('max_participants')->default(100);
                $table->integer('current_participants')->default(0);
                $table->boolean('is_published')->default(false);
                $table->boolean('registration_open')->default(true);
                $table->dateTime('registration_start_date')->nullable();
                $table->dateTime('registration_end_date')->nullable();
                $table->json('form_fields')->nullable();
                $table->string('event_image', 255)->nullable();
                $table->text('terms_conditions')->nullable();
                $table->text('cancellation_policy')->nullable();
                $table->boolean('requires_payment')->default(true);
                $table->enum('payment_status', ['en attente', 'payé', 'partiel', 'échoué'])->default('en attente');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};