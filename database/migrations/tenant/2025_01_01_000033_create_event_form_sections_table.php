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
        if (!Schema::hasTable('event_form_sections')) {
            Schema::create('event_form_sections', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('event_id');
                $table->string('section_key', 100);
                $table->string('section_title', 255);
                $table->text('section_description')->nullable();
                $table->integer('display_order')->default(0);
                $table->boolean('is_collapsible')->default(false);
                $table->boolean('is_expanded')->default(true);
                $table->json('show_conditions')->nullable();
                $table->timestamps();

                $table->unique(['event_id', 'section_key'], 'unique_event_section');
                $table->index('display_order');
                $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_form_sections');
    }
};

