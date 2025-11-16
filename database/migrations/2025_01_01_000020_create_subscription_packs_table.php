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
        Schema::create('subscription_packs', function (Blueprint $table) {
            $table->id();
            $table->string('pack_key', 50)->unique();
            $table->string('pack_name', 100);
            $table->text('pack_description')->nullable();
            $table->enum('pack_type', ['standard', 'premium', 'custom'])->default('standard');
            $table->decimal('commission_percentage', 5, 2)->default(0); // Pourcentage de commission sur les tickets
            $table->decimal('setup_fee', 10, 2)->default(0); // Frais d'installation
            $table->decimal('monthly_fee', 10, 2)->default(0); // Frais mensuels fixes
            $table->string('currency', 3)->default('XOF');
            
            // Fonctionnalités incluses
            $table->boolean('email_tickets')->default(true);
            $table->boolean('whatsapp_tickets')->default(false);
            $table->boolean('custom_tickets')->default(false);
            $table->boolean('multi_ticket_purchase')->default(false);
            $table->boolean('multi_country_support')->default(false);
            $table->boolean('custom_domain')->default(false);
            $table->boolean('advanced_analytics')->default(false);
            $table->boolean('api_access')->default(false);
            $table->boolean('priority_support')->default(false);
            
            // Limites
            $table->integer('max_events')->default(1);
            $table->integer('max_participants_per_event')->default(100);
            $table->integer('max_storage_mb')->default(100);
            $table->integer('max_ticket_types_per_event')->default(3);
            
            // Configuration
            $table->json('supported_countries')->nullable();
            $table->json('payment_methods')->nullable();
            $table->json('ticket_templates')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_packs');
    }
};
