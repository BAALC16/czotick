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
        Schema::create('supported_countries', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 3)->unique();
            $table->string('country_name', 100);
            $table->string('country_name_fr', 100)->nullable();
            $table->string('phone_code', 10);
            $table->string('currency_code', 3);
            $table->string('currency_symbol', 5);
            $table->string('flag_emoji', 10);
            $table->json('payment_providers')->nullable();
            $table->json('phone_format')->nullable();
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
        Schema::dropIfExists('supported_countries');
    }
};
