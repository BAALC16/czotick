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
            CREATE OR REPLACE VIEW v_pending_invoices AS 
            SELECT 
                i.id,
                i.invoice_number,
                o.org_name,
                o.contact_email,
                i.total_amount,
                i.billing_period_end AS due_date,
                DATEDIFF(CURDATE(), i.billing_period_end) AS days_overdue
            FROM invoices i
            JOIN organizations o ON i.organization_id = o.id
            WHERE i.status = 'envoyée' AND i.billing_period_end < CURDATE()
            ORDER BY i.billing_period_end ASC
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_pending_invoices');
    }
};
