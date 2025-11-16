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
        Schema::table('organizations', function (Blueprint $table) {
            // Ajouter les nouveaux champs pour les packs et fonctionnalités
            $table->unsignedBigInteger('subscription_pack_id')->nullable()->after('custom_domain');
            $table->json('enabled_event_types')->nullable()->after('subscription_pack_id');
            $table->json('enabled_countries')->nullable()->after('enabled_event_types');
            $table->json('payment_methods')->nullable()->after('enabled_countries');
            $table->boolean('multi_ticket_purchase')->default(false)->after('payment_methods');
            $table->integer('max_tickets_per_purchase')->default(1)->after('multi_ticket_purchase');
            $table->boolean('whatsapp_integration')->default(false)->after('max_tickets_per_purchase');
            $table->boolean('custom_ticket_design')->default(false)->after('whatsapp_integration');
            $table->json('ticket_templates')->nullable()->after('custom_ticket_design');
            $table->string('whatsapp_api_key', 255)->nullable()->after('ticket_templates');
            $table->string('whatsapp_phone_number', 20)->nullable()->after('whatsapp_api_key');
            
            // Ajouter les contraintes
            $table->foreign('subscription_pack_id')->references('id')->on('subscription_packs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropForeign(['subscription_pack_id']);
            $table->dropColumn([
                'subscription_pack_id',
                'enabled_event_types',
                'enabled_countries',
                'payment_methods',
                'multi_ticket_purchase',
                'max_tickets_per_purchase',
                'whatsapp_integration',
                'custom_ticket_design',
                'ticket_templates',
                'whatsapp_api_key',
                'whatsapp_phone_number'
            ]);
        });
    }
};
