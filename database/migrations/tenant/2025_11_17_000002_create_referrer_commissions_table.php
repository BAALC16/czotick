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
        Schema::create('referrer_commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('referrer_id');
            $table->decimal('commission_rate', 5, 2)->default(0.00); // Pourcentage de commission (ex: 10.50 pour 10.5%)
            $table->decimal('fixed_amount', 10, 2)->nullable(); // Montant fixe par inscription (alternative au pourcentage)
            $table->enum('commission_type', ['percentage', 'fixed'])->default('percentage');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'referrer_id']);
            $table->index('event_id');
            $table->index('referrer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrer_commissions');
    }
};

