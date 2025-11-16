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
        Schema::create('database_templates', function (Blueprint $table) {
            $table->id();
            $table->string('template_name', 100);
            $table->enum('org_type', ['association', 'entreprise', 'organisation', 'particulier', 'autre']);
            $table->string('template_version', 10)->default('1.0');
            $table->longText('sql_structure');
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('database_templates');
    }
};
