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
        Schema::create('ticket_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('registration_id')->nullable();
            $table->unsignedBigInteger('multi_purchase_id')->nullable();
            $table->enum('notification_type', ['email', 'whatsapp', 'sms']);
            $table->enum('notification_status', ['pending', 'sent', 'failed', 'delivered']);
            $table->string('recipient', 255); // Email ou numéro de téléphone
            $table->string('subject', 255)->nullable();
            $table->text('message')->nullable();
            $table->json('template_data')->nullable();
            $table->string('external_id', 100)->nullable(); // ID du message externe
            $table->json('response_data')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamps();

            $table->foreign('registration_id')->references('id')->on('registrations')->onDelete('cascade');
            $table->foreign('multi_purchase_id')->references('id')->on('multi_ticket_purchases')->onDelete('cascade');
            $table->index(['notification_type', 'notification_status']);
            $table->index(['sent_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_notifications');
    }
};
