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
        if (!Schema::hasTable('event_access_controls')) {
            Schema::create('event_access_controls', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('event_id');
                $table->string('access_zone', 100);
                $table->string('zone_name', 255);
                $table->text('zone_description')->nullable();
                $table->boolean('requires_separate_check')->default(true);
                $table->integer('max_capacity')->nullable();
                $table->time('access_start_time')->nullable();
                $table->time('access_end_time')->nullable();
                $table->date('access_date')->nullable();
                $table->string('timezone', 50)->default('Africa/Abidjan');
                $table->integer('early_access_minutes')->default(0);
                $table->integer('late_access_minutes')->default(30);
                $table->text('allowed_ticket_types')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamp('created_at')->useCurrent();

                $table->unique(['event_id', 'access_zone'], 'unique_event_zone');
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
        Schema::dropIfExists('event_access_controls');
    }
};


