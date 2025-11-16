<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('nom');
            $table->string('prenoms');
            $table->string('photo')->nullable();
            $table->string('titre')->nullable();
            $table->text('introduction')->nullable();
            $table->string('ville')->nullable();
            $table->string('code_pays', 3)->nullable();
            $table->string('email_pro')->nullable();
            $table->string('mobile')->nullable();
            $table->string('telephone')->nullable();
            $table->string('site_web')->nullable();
            $table->string('twitter')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('provider')->default('email');
            $table->string('provider_id')->nullable();
            $table->longText('provider_token')->nullable();
            $table->string('provider_refresh_token')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('code_pays')->references('code')->on('countries')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
