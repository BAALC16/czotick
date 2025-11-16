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
        if (!Schema::hasTable('registrations')) {
            Schema::create('registrations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('event_id');
                $table->unsignedBigInteger('ticket_type_id'); // CRITIQUE - utilisé par le modèle
                $table->string('registration_number', 50)->unique();
                
                // Champs de base (legacy)
                $table->string('fullname', 191)->nullable();
                $table->string('phone', 191)->nullable();
                $table->string('phone_country', 10)->nullable();
                $table->string('formatted_phone', 191)->nullable();
                $table->string('email', 191)->nullable();
                $table->string('organization', 191)->nullable();
                $table->string('position', 191)->nullable();
                $table->text('dietary_requirements')->nullable();
                $table->text('special_needs')->nullable();
                $table->text('question_1')->nullable();
                $table->text('question_2')->nullable();
                $table->text('question_3')->nullable();
                
                // Champs JSON pour données structurées
                $table->json('form_data')->default('{}');
                $table->json('participant_data')->nullable();
                $table->json('contact_data')->nullable();
                $table->json('preferences_data')->nullable();
                $table->json('additional_data')->nullable();
                
                // Statut et paiement
                $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
                $table->enum('payment_status', ['pending', 'paid', 'partial', 'failed', 'refunded'])->default('pending');
                $table->decimal('ticket_price', 10, 2)->default(0);
                $table->decimal('amount_paid', 10, 2)->default(0);
                $table->string('currency', 3)->default('XOF');
                
                // Dates
                $table->timestamp('registration_date')->useCurrent();
                $table->timestamp('confirmation_date')->nullable();
                $table->timestamp('cancellation_date')->nullable();
                
                // Champs de suivi d'utilisation (zones)
                $table->boolean('used_opening')->default(false);
                $table->boolean('used_conference')->default(false);
                $table->boolean('used_networking')->default(false);
                $table->boolean('used_photos')->default(false);
                
                // Métadonnées
                $table->string('payment_reference', 100)->nullable();
                $table->text('notes')->nullable();
                $table->enum('registration_source', ['legacy', 'dynamic_form'])->default('legacy');
                $table->integer('data_version')->default(1);
                
                $table->timestamps();

                // Clés étrangères
                $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
                $table->foreign('ticket_type_id')->references('id')->on('ticket_types')->onDelete('restrict');
                
                // Index
                $table->index(['event_id', 'status'], 'idx_event_status');
                $table->index(['event_id', 'payment_status'], 'idx_event_payment_status');
                $table->index('email', 'idx_email');
                $table->index('registration_number', 'idx_registration_number');
                $table->index('phone_country', 'idx_phone_country');
                $table->index('formatted_phone', 'idx_formatted_phone');
                $table->index(['phone', 'event_id'], 'unique_phone_event');
                $table->index('registration_source', 'idx_registration_source');
                $table->index('data_version', 'idx_data_version');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};