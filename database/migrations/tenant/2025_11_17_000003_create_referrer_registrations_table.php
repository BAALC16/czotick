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
        Schema::create('referrer_registrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('registration_id');
            $table->unsignedBigInteger('referrer_id');
            $table->unsignedBigInteger('event_id');
            $table->decimal('registration_amount', 10, 2); // Montant de l'inscription
            $table->decimal('commission_amount', 10, 2)->default(0.00); // Montant de la commission calculée
            $table->enum('commission_status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->unique('registration_id'); // Une inscription ne peut être liée qu'à un seul apporteur
            $table->index('referrer_id');
            $table->index('event_id');
            $table->index('commission_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrer_registrations');
    }
};

