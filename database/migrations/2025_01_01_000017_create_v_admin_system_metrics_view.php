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
            CREATE OR REPLACE VIEW v_admin_system_metrics AS 
            SELECT 
                CAST(organizations.created_at AS DATE) AS metric_date,
                COUNT(*) AS total_orgs,
                COUNT(CASE WHEN organizations.created_at >= CURRENT_TIMESTAMP() - INTERVAL 7 DAY THEN 1 END) AS new_orgs_week,
                COUNT(CASE WHEN organizations.created_at >= CURRENT_TIMESTAMP() - INTERVAL 30 DAY THEN 1 END) AS new_orgs_month
            FROM organizations
            GROUP BY CAST(organizations.created_at AS DATE)
            ORDER BY CAST(organizations.created_at AS DATE) DESC
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_admin_system_metrics');
    }
};
