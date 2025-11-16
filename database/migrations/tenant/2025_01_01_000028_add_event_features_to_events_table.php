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
        Schema::table('events', function (Blueprint $table) {
            // Ajouter les nouveaux champs pour les types d'événements et packs
            $table->unsignedBigInteger('event_type_id')->nullable()->after('id');
            $table->enum('pack_type', ['standard', 'premium', 'custom'])->default('standard')->after('event_type_id');
            $table->boolean('multi_ticket_purchase')->default(false)->after('pack_type');
            $table->integer('max_tickets_per_purchase')->default(1)->after('multi_ticket_purchase');
            $table->json('enabled_countries')->nullable()->after('max_tickets_per_purchase');
            $table->json('payment_methods')->nullable()->after('enabled_countries');
            $table->boolean('whatsapp_notifications')->default(false)->after('payment_methods');
            $table->boolean('email_notifications')->default(true)->after('whatsapp_notifications');
            $table->boolean('sms_notifications')->default(false)->after('email_notifications');
            $table->json('ticket_customization')->nullable()->after('sms_notifications');
            $table->string('whatsapp_template_id', 100)->nullable()->after('ticket_customization');
            
            // Ajouter les contraintes
            $table->foreign('event_type_id')->references('id')->on('event_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['event_type_id']);
            $table->dropColumn([
                'event_type_id',
                'pack_type',
                'multi_ticket_purchase',
                'max_tickets_per_purchase',
                'enabled_countries',
                'payment_methods',
                'whatsapp_notifications',
                'email_notifications',
                'sms_notifications',
                'ticket_customization',
                'whatsapp_template_id'
            ]);
        });
    }
};
