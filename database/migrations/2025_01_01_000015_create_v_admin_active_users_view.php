<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            CREATE OR REPLACE VIEW v_admin_active_users AS 
            SELECT 
                CAST(u.last_login_at AS DATE) AS login_date,
                COUNT(DISTINCT u.id) AS unique_users,
                COUNT(DISTINCT u.organization_id) AS active_organizations
            FROM saas_users u
            WHERE u.is_active = 1 
                AND u.last_login_at >= CURRENT_TIMESTAMP() - INTERVAL 30 DAY
            GROUP BY CAST(u.last_login_at AS DATE)
            ORDER BY CAST(u.last_login_at AS DATE) DESC
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_admin_active_users');
    }
};
