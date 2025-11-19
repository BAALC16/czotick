<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modifier l'enum pour ajouter les nouveaux rôles
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('owner', 'admin', 'organizer', 'referrer', 'manager', 'user') DEFAULT 'admin'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('owner', 'admin', 'manager', 'user') DEFAULT 'admin'");
    }
};

