<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        // Votre directive existante pour l'argent
        Blade::directive('money', function ($money) {
            return "<?php echo empty($money) ? '---' : number_format($money, 0, ',', '.'); ?>";
        });

        // Directives Super Admin
        $this->registerSuperAdminBladeDirectives();

        // Votre pagination personnalisée
        Paginator::defaultView('layouts.pagination.custom');
    }

    /**
     * Enregistrer les directives Blade pour le super admin
     */
    private function registerSuperAdminBladeDirectives(): void
    {
        // Directive pour vérifier l'authentification super admin
        Blade::directive('superAdminAuth', function () {
            return "<?php if(session()->has('super_admin_logged_in')): ?>";
        });

        Blade::directive('endSuperAdminAuth', function () {
            return "<?php endif; ?>";
        });

        // Directive pour vérifier les niveaux d'accès
        Blade::directive('superAdminLevel', function ($level) {
            return "<?php if(session('super_admin_level') === $level): ?>";
        });

        Blade::directive('endSuperAdminLevel', function () {
            return "<?php endif; ?>";
        });

        // Directive pour afficher le nom de l'admin
        Blade::directive('superAdminName', function () {
            return "<?php echo session('super_admin_name', session('super_admin_username', 'Admin')); ?>";
        });

        // Directive pour afficher le username
        Blade::directive('superAdminUsername', function () {
            return "<?php echo session('super_admin_username', 'admin'); ?>";
        });

        // Directive pour formater les grands nombres (compatible avec votre @money)
        Blade::directive('formatNumber', function ($number) {
            return "<?php 
                if ($number >= 1000000) {
                    echo number_format($number / 1000000, 1) . 'M';
                } elseif ($number >= 1000) {
                    echo number_format($number / 1000, 1) . 'K';
                } else {
                    echo number_format($number);
                }
            ?>";
        });

        // Directive pour les badges de statut
        Blade::directive('statusBadge', function ($expression) {
            return "<?php 
                \$status = $expression;
                \$classes = [
                    'active' => 'bg-success',
                    'inactive' => 'bg-secondary', 
                    'suspended' => 'bg-warning',
                    'cancelled' => 'bg-danger',
                    'expired' => 'bg-secondary',
                    'trial' => 'bg-info',
                    'pending' => 'bg-warning',
                    'confirmed' => 'bg-success',
                    'paid' => 'bg-success',
                    'unpaid' => 'bg-danger',
                ];
                \$class = \$classes[\$status] ?? 'bg-secondary';
                echo '<span class=\"badge ' . \$class . '\">' . ucfirst(\$status) . '</span>';
            ?>";
        });

        // Directive pour afficher la durée depuis une date
        Blade::directive('timeAgo', function ($expression) {
            return "<?php echo \Carbon\Carbon::parse($expression)->diffForHumans(); ?>";
        });

        // Directive pour les alertes de session (compatible avec vos messages)
        Blade::directive('sessionAlerts', function () {
            return "<?php 
                if(session('success')): 
                    echo '<div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">' . session('success') . '<button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button></div>';
                endif;
                if(session('error')): 
                    echo '<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">' . session('error') . '<button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button></div>';
                endif;
                if(session('warning')): 
                    echo '<div class=\"alert alert-warning alert-dismissible fade show\" role=\"alert\">' . session('warning') . '<button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button></div>';
                endif;
                if(session('info')): 
                    echo '<div class=\"alert alert-info alert-dismissible fade show\" role=\"alert\">' . session('info') . '<button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button></div>';
                endif;
            ?>";
        });

        // Directive pour vérifier si on est sur une route spécifique
        Blade::directive('activeRoute', function ($expression) {
            return "<?php echo request()->routeIs($expression) ? 'active' : ''; ?>";
        });

        // Directive pour les icônes Font Awesome
        Blade::directive('icon', function ($expression) {
            return "<?php echo '<i class=\"fas fa-' . $expression . '\"></i>'; ?>";
        });

        // Directive pour les confirmations JavaScript
        Blade::directive('confirmDelete', function ($message = "'Êtes-vous sûr de vouloir supprimer cet élément ?'") {
            return "<?php echo 'onclick=\"return confirm(' . $message . ')\"'; ?>";
        });

        // Directive pour afficher les erreurs de validation
        Blade::directive('validationError', function ($field) {
            return "<?php if(\$errors->has($field)): ?>
                <div class=\"invalid-feedback d-block\">
                    <?php echo \$errors->first($field); ?>
                </div>
            <?php endif; ?>";
        });

        // Directive pour vérifier l'autorisation (simple)
        Blade::directive('superAdminCan', function ($permission) {
            return "<?php if(session('super_admin_level') === 'super_admin' || in_array($permission, json_decode(session('super_admin_permissions', '[]'), true))): ?>";
        });

        Blade::directive('endSuperAdminCan', function () {
            return "<?php endif; ?>";
        });
    }
}