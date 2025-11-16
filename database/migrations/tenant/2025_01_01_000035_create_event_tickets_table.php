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
        if (!Schema::hasTable('event_tickets')) {
            Schema::create('event_tickets', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('event_id');
                $table->string('ticket_name', 255);
                $table->text('ticket_description')->nullable();
                $table->decimal('ticket_price', 10, 2);
                $table->string('currency', 3)->default('XOF');
                $table->integer('quantity_available')->nullable(); // null = illimité
                $table->integer('quantity_sold')->default(0);
                $table->boolean('is_active')->default(true);
                $table->integer('display_order')->default(0);
                $table->dateTime('sale_start_date')->nullable();
                $table->dateTime('sale_end_date')->nullable();
                $table->timestamps();

                $table->index('event_id');
                $table->index('is_active');
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
        Schema::dropIfExists('event_tickets');
    }
};

