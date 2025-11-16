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
        if (!Schema::hasTable('ticket_hash_mappings')) {
            Schema::create('ticket_hash_mappings', function (Blueprint $table) {
                $table->id();
                $table->string('ticket_hash', 255)->unique();
                $table->unsignedBigInteger('registration_id');
                $table->unsignedBigInteger('event_id')->nullable();
                $table->string('access_zone', 100);
                $table->unsignedBigInteger('access_control_id')->nullable();
                $table->string('zone_name', 255)->nullable();
                $table->text('ticket_data')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index('ticket_hash', 'idx_hash');
                $table->index('registration_id', 'idx_registration');
                $table->index('access_zone', 'idx_zone');
                $table->index(['event_id', 'access_zone'], 'idx_event_zone');
                $table->foreign('registration_id')->references('id')->on('registrations')->onDelete('cascade');
                $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
                $table->foreign('access_control_id')->references('id')->on('event_access_controls')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_hash_mappings');
    }
};

