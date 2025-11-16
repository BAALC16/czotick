<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Changer org_type de ENUM vers VARCHAR(50) pour accepter tous les codes de organization_types
        Schema::table('organization_registrations', function (Blueprint $table) {
            $table->string('org_type', 50)->change();
        });

        // Rendre subscription_pack_id nullable car il peut être null lors de la création
        Schema::table('organization_registrations', function (Blueprint $table) {
            $table->unsignedBigInteger('subscription_pack_id')->nullable()->change();
        });

        // Supprimer la clé étrangère si elle existe, puis la recréer avec nullable
        Schema::table('organization_registrations', function (Blueprint $table) {
            $table->dropForeign(['subscription_pack_id']);
        });

        Schema::table('organization_registrations', function (Blueprint $table) {
            $table->foreign('subscription_pack_id')
                  ->references('id')
                  ->on('subscription_packs')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revenir à l'ENUM original
        Schema::table('organization_registrations', function (Blueprint $table) {
            $table->dropForeign(['subscription_pack_id']);
        });

        Schema::table('organization_registrations', function (Blueprint $table) {
            $table->enum('org_type', ['jci', 'rotary', 'lions', 'association', 'company', 'other'])->change();
            $table->unsignedBigInteger('subscription_pack_id')->nullable(false)->change();
        });

        Schema::table('organization_registrations', function (Blueprint $table) {
            $table->foreign('subscription_pack_id')
                  ->references('id')
                  ->on('subscription_packs')
                  ->onDelete('cascade');
        });
    }
};
