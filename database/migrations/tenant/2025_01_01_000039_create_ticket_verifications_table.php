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
        if (!Schema::hasTable('ticket_verifications')) {
            Schema::create('ticket_verifications', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('event_id');
                $table->unsignedBigInteger('registration_id')->nullable();
                $table->string('ticket_hash', 255);
                $table->string('access_zone', 100);
                $table->enum('status', [
                    'success', 'already_used', 'not_paid', 'not_confirmed', 
                    'invalid_hash', 'registration_not_found', 'zone_access_denied', 
                    'event_not_active', 'access_time_restricted', 'access_not_started', 'access_ended'
                ]);
                $table->string('attempt_reason', 255)->nullable();
                $table->time('access_window_start')->nullable();
                $table->time('access_window_end')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->unsignedBigInteger('verifier_id')->nullable();
                $table->timestamp('verified_at')->useCurrent();
                $table->timestamp('created_at')->useCurrent();

                $table->index('event_id', 'idx_event_id');
                $table->index('registration_id', 'idx_registration_id');
                $table->index('ticket_hash', 'idx_ticket_hash');
                $table->index('access_zone', 'idx_access_zone');
                $table->index('status', 'idx_status');
                $table->index('verified_at', 'idx_verified_at');
                $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
                $table->foreign('registration_id')->references('id')->on('registrations')->onDelete('set null');
                $table->foreign('verifier_id')->references('id')->on('verifiers')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_verifications');
    }
};

