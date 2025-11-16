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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('org_key', 50)->unique();
            $table->string('org_name', 255);
            $table->string('organization_logo', 255)->default('default-logo.png');
            $table->string('org_type', 50); // Référence au code dans organization_types
            $table->string('contact_name', 255);
            $table->string('contact_email', 255);
            $table->string('contact_phone', 20)->nullable();
            $table->string('database_name', 100);
            $table->string('subdomain', 50)->nullable();
            $table->string('custom_domain', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
