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
        Schema::create('organization_pack_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('subscription_pack_id');
            $table->json('custom_settings')->nullable();
            $table->json('enabled_countries')->nullable();
            $table->json('enabled_payment_methods')->nullable();
            $table->json('ticket_customization')->nullable();
            $table->boolean('multi_ticket_enabled')->default(false);
            $table->integer('max_tickets_per_purchase')->default(1);
            $table->boolean('whatsapp_notifications')->default(false);
            $table->boolean('email_notifications')->default(true);
            $table->boolean('sms_notifications')->default(false);
            $table->timestamps();

            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreign('subscription_pack_id')->references('id')->on('subscription_packs')->onDelete('cascade');
            $table->unique(['organization_id', 'subscription_pack_id'], 'org_pack_settings_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_pack_settings');
    }
};
