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
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('ticket_type_id');
            $table->string('registration_number', 50)->unique();
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
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('confirmed');
            $table->enum('payment_status', ['pending', 'paid', 'partial', 'refunded'])->default('paid');
            $table->decimal('ticket_price', 10, 2);
            $table->decimal('amount_paid', 10, 2)->default(0.00);
            $table->timestamp('registration_date')->useCurrent();
            $table->timestamp('confirmation_date')->nullable();
            $table->timestamp('cancellation_date')->nullable();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->json('form_data')->default('{}');
            $table->json('participant_data')->nullable();
            $table->json('contact_data')->nullable();
            $table->json('preferences_data')->nullable();
            $table->json('additional_data')->nullable();
            $table->enum('registration_source', ['legacy', 'dynamic_form'])->default('dynamic_form');
            $table->integer('data_version')->default(2);
            $table->timestamp('created_at')->useCurrent();

            // Note: Les clés étrangères vers event_id et ticket_type_id ne sont pas définies
            // car ces tables n'existent pas dans le système SaaS principal
            // Elles seraient dans les bases de données spécifiques aux organisations
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
