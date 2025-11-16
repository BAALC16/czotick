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
            CREATE OR REPLACE VIEW v_active_organizations AS 
            SELECT 
                o.id,
                o.org_key,
                o.org_name,
                o.org_type,
                u.email AS owner_email,
                (SELECT COUNT(*) FROM saas_users WHERE saas_users.organization_id = o.id AND saas_users.is_active = 1) AS user_count,
                o.created_at
            FROM organizations o
            LEFT JOIN saas_users u ON o.id = u.organization_id AND u.role = 'owner'
            WHERE o.id IN (SELECT DISTINCT organization_id FROM saas_users WHERE is_active = 1)
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_active_organizations');
    }
};
