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
        if (!Schema::hasTable('verifiers')) {
            Schema::create('verifiers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('event_id');
                $table->string('name', 255);
                $table->string('email', 255);
                $table->string('phone', 20)->nullable();
                $table->string('access_code', 5);
                $table->enum('role', ['admin', 'verifier', 'zone_specific'])->default('verifier');
                $table->text('allowed_zones')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamp('last_login')->nullable();
                $table->timestamps();

                $table->unique(['email', 'event_id'], 'unique_email_event');
                $table->index('event_id', 'event_id');
                $table->index('access_code', 'idx_access_code');
                $table->index('is_active', 'idx_active');
                $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verifiers');
    }
};


