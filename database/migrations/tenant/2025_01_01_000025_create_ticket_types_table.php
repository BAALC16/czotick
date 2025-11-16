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
        if (!Schema::hasTable('ticket_types')) {
            Schema::create('ticket_types', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('event_id');
                $table->string('ticket_name', 100);
                $table->text('ticket_description')->nullable();
                $table->string('ticket_code', 50)->nullable();
                $table->decimal('price', 10, 2);
                $table->string('currency', 5)->default('FCFA');
                $table->integer('max_quantity')->nullable();
                $table->integer('quantity_sold')->default(0);
                $table->dateTime('sale_start_date')->nullable();
                $table->dateTime('sale_end_date')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('requires_membership')->default(false);
                $table->string('membership_organization', 255)->nullable();
                $table->integer('min_age')->nullable();
                $table->integer('max_age')->nullable();
                $table->integer('display_order')->default(0);
                $table->timestamps();

                $table->index('event_id', 'idx_event');
                $table->index('is_active', 'idx_active');
                $table->index('display_order', 'idx_display_order');
                $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_types');
    }
};


