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
        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                // Option de paiement partiel
                $table->boolean('allow_partial_payment')->default(false)->after('requires_payment');
                $table->decimal('partial_payment_amount', 10, 2)->nullable()->after('allow_partial_payment');
                
                // Option de réservation avec montant défini
                $table->boolean('allow_reservation')->default(false)->after('partial_payment_amount');
                $table->decimal('reservation_amount', 10, 2)->nullable()->after('allow_reservation');
                $table->text('reservation_terms')->nullable()->after('reservation_amount');
                
                // Utiliser plusieurs tarifs
                $table->boolean('use_multiple_tickets')->default(false)->after('reservation_terms');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn([
                    'allow_partial_payment',
                    'partial_payment_amount',
                    'allow_reservation',
                    'reservation_amount',
                    'reservation_terms',
                    'use_multiple_tickets'
                ]);
            });
        }
    }
};

