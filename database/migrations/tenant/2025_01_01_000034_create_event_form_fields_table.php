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
        if (!Schema::hasTable('event_form_fields')) {
            Schema::create('event_form_fields', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('event_id');
                $table->string('field_key', 100);
                $table->string('field_label', 255);
                $table->text('field_description')->nullable();
                $table->text('field_help_text')->nullable();
                $table->enum('field_type', [
                    'text', 'textarea', 'email', 'phone', 'number', 'date', 'time', 
                    'datetime', 'select', 'radio', 'checkbox', 'checkbox_group', 
                    'file', 'url', 'password', 'country_phone', 'rating', 'range', 'color'
                ]);
                $table->json('field_config')->default('{}');
                $table->boolean('is_required')->default(false);
                $table->boolean('is_visible')->default(true);
                $table->boolean('is_readonly')->default(false);
                $table->boolean('is_unique')->default(false);
                $table->json('show_conditions')->nullable();
                $table->string('section_name', 100)->default('main');
                $table->integer('display_order')->default(0);
                $table->enum('field_width', ['full', 'half', 'third', 'quarter'])->default('full');
                $table->timestamps();

                $table->unique(['event_id', 'field_key'], 'unique_event_field');
                $table->index(['event_id', 'section_name'], 'idx_event_section');
                $table->index('display_order');
                $table->index('is_visible');
                $table->index('field_type');
                $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_form_fields');
    }
};

