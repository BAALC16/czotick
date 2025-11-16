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
        Schema::create('organization_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('registration_token', 100)->unique();
            $table->string('org_name', 255);
            $table->string('org_key', 50)->unique();
            $table->string('org_type', 50); // Référence au code dans organization_types
            $table->string('contact_name', 255);
            $table->string('contact_email', 255);
            $table->string('contact_phone', 20)->nullable();
            $table->string('subdomain', 50)->nullable();
            $table->string('custom_domain', 255)->nullable();
            $table->unsignedBigInteger('subscription_pack_id')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->json('pack_settings')->nullable();
            $table->json('enabled_countries')->nullable();
            $table->json('enabled_event_types')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->unsignedBigInteger('created_organization_id')->nullable();
            $table->string('created_database_name', 100)->nullable();
            $table->timestamps();

            $table->foreign('subscription_pack_id')->references('id')->on('subscription_packs')->onDelete('set null');
            $table->index(['status', 'created_at']);
            $table->index(['registration_token']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_registrations');
    }
};
