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
        Schema::create('multi_ticket_purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->string('purchase_reference', 50)->unique();
            $table->string('customer_email', 255);
            $table->string('customer_phone', 20)->nullable();
            $table->string('customer_name', 255)->nullable();
            $table->string('customer_country', 3)->default('CI');
            $table->json('ticket_details'); // Détails de chaque ticket
            $table->decimal('total_amount', 10, 2);
            $table->string('currency', 3)->default('XOF');
            $table->enum('payment_status', ['en attente', 'payé', 'échoué'])->default('en attente');
            $table->string('payment_method', 50)->nullable();
            $table->string('payment_reference', 100)->nullable();
            $table->json('payment_details')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->json('notifications_sent')->nullable(); // Email, WhatsApp, SMS
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->index(['event_id', 'payment_status']);
            $table->index(['customer_email']);
            $table->index(['purchase_reference']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('multi_ticket_purchases');
    }
};
