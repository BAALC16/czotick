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
            CREATE OR REPLACE VIEW v_admin_organizations_overview AS 
            SELECT 
                o.id,
                o.org_key,
                o.org_name,
                o.org_type,
                o.contact_email,
                o.created_at,
                COUNT(DISTINCT u.id) AS user_count
            FROM organizations o
            LEFT JOIN saas_users u ON o.id = u.organization_id AND u.is_active = 1
            GROUP BY o.id, o.org_key, o.org_name, o.org_type, o.contact_email, o.created_at
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_admin_organizations_overview');
    }
};
