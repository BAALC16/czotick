<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SaasMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // System Settings
        if (DB::table('system_settings')->count() == 0) {
            DB::table('system_settings')->insert([
            [
                'setting_key' => 'app_name',
                'setting_value' => "C'zotick Platform",
                'setting_type' => 'string',
                'description' =>  "C'zotick Platform",
                'is_public' => true,
            ],
            [
                'setting_key' => 'trial_duration_days',
                'setting_value' => '14',
                'setting_type' => 'number',
                'description' => 'Durée de l essai gratuit en jours',
                'is_public' => false,
            ],
            [
                'setting_key' => 'max_organizations',
                'setting_value' => '1000',
                'setting_type' => 'number',
                'description' => 'Nombre maximum d organisations',
                'is_public' => false,
            ],
            [
                'setting_key' => 'maintenance_mode',
                'setting_value' => 'false',
                'setting_type' => 'boolean',
                'description' => 'Mode maintenance activé',
                'is_public' => true,
            ],
            [
                'setting_key' => 'supported_languages',
                'setting_value' => '["fr", "en"]',
                'setting_type' => 'json',
                'description' => 'Langues supportées',
                'is_public' => true,
            ],
            ]);
        }

        // Subscription Plans
        if (DB::table('subscription_plans')->count() == 0) {
            DB::table('subscription_plans')->insert([
            [
                'plan_name' => 'Essai Gratuit',
                'plan_code' => 'trial',
                'monthly_price' => 0.00,
                'yearly_price' => 0.00,
                'setup_fee' => 0.00,
                'max_events' => 1,
                'max_participants_per_event' => 50,
                'max_storage_mb' => 50,
                'max_users' => 2,
                'features' => '["basic_events", "email_support"]',
                'is_active' => true,
            ],
            [
                'plan_name' => 'Basique',
                'plan_code' => 'basic',
                'monthly_price' => 29.99,
                'yearly_price' => 299.99,
                'setup_fee' => 0.00,
                'max_events' => 5,
                'max_participants_per_event' => 200,
                'max_storage_mb' => 500,
                'max_users' => 5,
                'features' => '["basic_events", "email_support", "custom_forms"]',
                'is_active' => true,
            ],
            [
                'plan_name' => 'Premium',
                'plan_code' => 'premium',
                'monthly_price' => 79.99,
                'yearly_price' => 799.99,
                'setup_fee' => 0.00,
                'max_events' => 20,
                'max_participants_per_event' => 1000,
                'max_storage_mb' => 2000,
                'max_users' => 15,
                'features' => '["advanced_events", "priority_support", "custom_branding", "analytics"]',
                'is_active' => true,
            ],
            [
                'plan_name' => 'Entreprise',
                'plan_code' => 'enterprise',
                'monthly_price' => 199.99,
                'yearly_price' => 1999.99,
                'setup_fee' => 0.00,
                'max_events' => -1,
                'max_participants_per_event' => -1,
                'max_storage_mb' => 10000,
                'max_users' => -1,
                'features' => '["unlimited_events", "dedicated_support", "api_access", "white_label"]',
                'is_active' => true,
            ],
            ]);
        }

        // System Admin (Super Admin)
        if (DB::table('system_admins')->where('email', 'admin@votre-saas.com')->count() == 0) {
            DB::table('system_admins')->insert([
            [
                'username' => 'superadmin',
                'email' => 'admin@votre-saas.com',
                'password' => '$2y$10$gf20PcLBt2KclSoNvrZbSekQtomWWFTZR9jdc.sUWhkgV7klslmlC', // password: admin123
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'phone' => null,
                'admin_level' => 'super_admin',
                'permissions' => null,
                'is_active' => true,
                'must_change_password' => false,
                'two_factor_enabled' => false,
                'two_factor_secret' => null,
                'last_login_at' => null,
                'last_login_ip' => null,
                'login_attempts' => 0,
                'locked_until' => null,
                'created_by' => null,
            ],
            ]);
        }

        // Organization Types
        if (DB::table('organization_types')->count() == 0) {
            DB::table('organization_types')->insert([
            [
                'code' => 'organisation',
                'name' => 'Organisation',
                'name_fr' => 'Organisation',
                'description' => 'Organisation générique',
                'display_order' => 3,
                'is_active' => true,
            ],
            [
                'code' => 'association',
                'name' => 'Association',
                'name_fr' => 'Association',
                'description' => 'Association générique',
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'code' => 'entreprise',
                'name' => 'Company',
                'name_fr' => 'Entreprise',
                'description' => 'Entreprise ou société',
                'display_order' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'particulier',
                'name' => 'Particulier',
                'name_fr' => 'Particulier',
                'description' => 'Particulier',
                'display_order' => 4,
                'is_active' => true,
            ],
            [
                'code' => 'autre',
                'name' => 'Autre',
                'name_fr' => 'Autre',
                'description' => 'Autre type d\'organisation',
                'display_order' => 5,
                'is_active' => true,
            ],
            ]);
        }

        // Scheduled Tasks
        if (DB::table('scheduled_tasks')->count() == 0) {
            DB::table('scheduled_tasks')->insert([
            [
                'task_name' => 'cleanup_data',
                'task_type' => 'maintenance',
                'schedule_expression' => '0 2 * * *',
                'command' => 'php artisan admin:cleanup',
                'last_run_at' => null,
                'next_run_at' => now()->addDay()->setHour(2)->setMinute(0),
                'last_duration_seconds' => null,
                'last_status' => null,
                'last_output' => null,
                'is_active' => true,
            ],
            [
                'task_name' => 'check_expired_trials',
                'task_type' => 'billing',
                'schedule_expression' => '0 9 * * *',
                'command' => 'php artisan admin:check-trials',
                'last_run_at' => null,
                'next_run_at' => now()->addDay()->setHour(9)->setMinute(0),
                'last_duration_seconds' => null,
                'last_status' => null,
                'last_output' => null,
                'is_active' => true,
            ],
            [
                'task_name' => 'generate_daily_metrics',
                'task_type' => 'analytics',
                'schedule_expression' => '0 1 * * *',
                'command' => 'php artisan admin:daily-metrics',
                'last_run_at' => null,
                'next_run_at' => now()->addDay()->setHour(1)->setMinute(0),
                'last_duration_seconds' => null,
                'last_status' => null,
                'last_output' => null,
                'is_active' => true,
            ],
            ]);
        }
    }
}
