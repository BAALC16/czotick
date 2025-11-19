<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\VerifierController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\SuperAdmin\SimpleAuthController;
use App\Http\Controllers\SuperAdmin\SimpleDashboardController;
use App\Http\Controllers\Organization\DashboardController;
use App\Http\Controllers\Organization\AuthController;
use App\Http\Controllers\OrganizationRegistrationController;
use App\Http\Controllers\EvenementsController;
use Illuminate\Http\Request;
use App\Models\Registration;
use App\Helpers\TenantHelper;

/*
|--------------------------------------------------------------------------
| ROUTES PUBLIQUES SAAS
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('saas.landing');
})->name('saas.home');

Route::get('/features', function () {
    return view('saas.features');
})->name('saas.features');

Route::get('/pricing', function () {
    return view('saas.pricing');
})->name('saas.pricing');

Route::get('/contact', function () {
    return view('saas.contact');
})->name('saas.contact');

Route::get('/terms', function () {
    return view('saas.terms');
})->name('saas.terms');

Route::get('/privacy', function () {
    return view('saas.privacy');
})->name('saas.privacy');
// Routes d'inscription d'organisation (URLs en français, contrôleurs en anglais)
Route::get('/organisateurs/inscription', [OrganizationRegistrationController::class, 'showCustomRegistrationForm'])->name('organization.register.custom.form');
Route::post('/organisateurs', [OrganizationRegistrationController::class, 'registerCustom'])->name('organization.register.custom');
Route::get('/organisateurs/connexion', [OrganizationRegistrationController::class, 'showOrganizationSelector'])->name('organization.login.selector');
Route::post('/organisateurs/connexion', [OrganizationRegistrationController::class, 'redirectToLogin'])->name('organization.login.redirect');

// Routes RESTful pour les événements (URLs en français, noms en anglais)
Route::resource('evenements', EvenementsController::class)->names([
    'index' => 'events.index',
    'create' => 'events.create',
    'store' => 'events.store',
    'show' => 'events.show',
    'edit' => 'events.edit',
    'update' => 'events.update',
    'destroy' => 'events.destroy'
]);

// Routes supplémentaires avec URLs françaises
Route::get('/accueil', function () {
    return view('saas.landing');
})->name('home');

Route::get('/fonctionnalites', function () {
    return view('saas.features');
})->name('features');

Route::get('/tarifs', function () {
    return view('saas.pricing');
})->name('pricing');

Route::get('/contact', function () {
    return view('saas.contact');
})->name('contact');

Route::get('/conditions', function () {
    return view('saas.terms');
})->name('terms');

Route::get('/confidentialite', function () {
    return view('saas.privacy');
})->name('privacy');

// Route de compatibilité (ancienne route avec ID) - DOIT être avant la route générique
Route::get('/participants/inscription/{event}', function ($event) {
    return view('participants.register', compact('event'));
})->name('participants.register.legacy');

Route::post('/participants/inscription', function () {
    // Logique d'inscription des participants
    return redirect()->back()->with('success', 'Inscription réussie !');
})->name('participants.store');

// Routes pour les tickets
Route::get('/ticket/{registration}', [EventController::class, 'downloadTicket'])->name('ticket.download');
Route::get('/qr/{registration}', [EventController::class, 'generateQRCode'])->name('qr.generate');
Route::get('/tickets/bulk/{registrations}', [EventController::class, 'downloadBulkTickets'])->name('tickets.bulk');

/*
|--------------------------------------------------------------------------
| ROUTES ORGANISATION (avec préfixe /org/{org_slug})
|--------------------------------------------------------------------------
*/
Route::prefix('org')->name('org.')->group(function () {
    Route::get('/{org_slug}/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/{org_slug}/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/{org_slug}/logout', [AuthController::class, 'logout'])->name('logout');
});

// Routes pour les tickets
Route::get('/tickets/{ticket}/verification', function ($ticket) {
    return view('tickets.verification', compact('ticket'));
})->name('tickets.verification');

Route::post('/tickets/{ticket}/verifier', function ($ticket) {
    // Logique de vérification des tickets
    return response()->json(['valid' => true]);
})->name('tickets.verify');

Route::post('/wave/callback', [PaymentController::class, 'handle']);
Route::get('/send-messages-to-participant', [PaymentController::class, 'sendMessagesToParticipant']);

Route::prefix('super-admin')->name('super-admin.')->group(function () {

    Route::get('/login', [SimpleAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [SimpleAuthController::class, 'login'])->name('login.submit');
    Route::middleware(['simple.super.admin.auth'])->group(function () {

        Route::get('/dashboard', [SimpleDashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [SimpleAuthController::class, 'logout'])->name('logout');
        Route::get('/check-auth', [SimpleAuthController::class, 'checkAuth'])->name('check-auth');
        Route::prefix('organizations')->name('organizations.')->group(function () {
            Route::get('/', [SimpleDashboardController::class, 'organizations'])->name('index');
            Route::get('/create', function() {
                return view('super-admin.organizations.create');
            })->name('create');

            Route::post('/', [SimpleDashboardController::class, 'createOrganization'])->name('store');
            Route::get('/{id}', [SimpleDashboardController::class, 'showOrganization'])->name('show');
            Route::put('/{id}', [SimpleDashboardController::class, 'updateOrganization'])->name('update');
            Route::delete('/{id}', [SimpleDashboardController::class, 'deleteOrganization'])->name('delete');
            Route::post('/{id}/toggle-status', [SimpleDashboardController::class, 'toggleOrganizationStatus'])->name('toggle-status');
            Route::post('/{id}/reset-trial', [SimpleDashboardController::class, 'resetTrial'])->name('reset-trial');
            Route::post('/{id}/upgrade-plan', [SimpleDashboardController::class, 'upgradePlan'])->name('upgrade-plan');
            Route::post('/{id}/suspend', [SimpleDashboardController::class, 'suspendOrganization'])->name('suspend');
            Route::post('/{id}/reactivate', [SimpleDashboardController::class, 'reactivateOrganization'])->name('reactivate');
            Route::get('/{id}/database', [SimpleDashboardController::class, 'showDatabaseInfo'])->name('database');
            Route::post('/{id}/database/backup', [SimpleDashboardController::class, 'backupDatabase'])->name('database.backup');
            Route::post('/{id}/database/restore', [SimpleDashboardController::class, 'restoreDatabase'])->name('database.restore');
            Route::post('/{id}/database/migrate', [SimpleDashboardController::class, 'migrateDatabase'])->name('database.migrate');
            Route::get('/{id}/stats', [SimpleDashboardController::class, 'organizationStats'])->name('stats');
            Route::get('/{id}/revenue', [SimpleDashboardController::class, 'organizationRevenue'])->name('revenue');
            Route::get('/{id}/export', [SimpleDashboardController::class, 'exportOrganizationData'])->name('export');
        });

        Route::prefix('events')->name('events.')->group(function () {
            Route::get('/', [SimpleDashboardController::class, 'events'])->name('index');
            Route::get('/calendar', [SimpleDashboardController::class, 'eventsCalendar'])->name('calendar');
            Route::get('/analytics', [SimpleDashboardController::class, 'eventsAnalytics'])->name('analytics');
            Route::get('/{organizationId}/{eventId}', [SimpleDashboardController::class, 'showEvent'])->name('show');
            Route::get('/{organizationId}/{eventId}/participants', [SimpleDashboardController::class, 'eventParticipants'])->name('participants');
            Route::get('/{organizationId}/{eventId}/revenue', [SimpleDashboardController::class, 'eventRevenue'])->name('revenue');
            Route::get('/{organizationId}/{eventId}/export', [SimpleDashboardController::class, 'exportEventData'])->name('export');
            Route::post('/{organizationId}/{eventId}/toggle-status', [SimpleDashboardController::class, 'toggleEventStatus'])->name('toggle-status');
            Route::post('/{organizationId}/{eventId}/duplicate', [SimpleDashboardController::class, 'duplicateEvent'])->name('duplicate');
            Route::get('/{organizationId}/{eventId}/verifications', [SimpleDashboardController::class, 'eventVerifications'])->name('verifications');
            Route::get('/{organizationId}/{eventId}/verifiers', [SimpleDashboardController::class, 'eventVerifiers'])->name('verifiers');
        });

        Route::prefix('forms')->name('forms.')->group(function () {
            Route::get('/', [SimpleDashboardController::class, 'forms'])->name('index');
            Route::get('/templates', [SimpleDashboardController::class, 'formTemplates'])->name('templates');
            Route::get('/{organizationId}/{eventId}', [SimpleDashboardController::class, 'eventFormConfiguration'])->name('event-config');
            Route::post('/{organizationId}/{eventId}/sections', [SimpleDashboardController::class, 'addFormSection'])->name('add-section');
            Route::put('/{organizationId}/sections/{sectionId}', [SimpleDashboardController::class, 'updateFormSection'])->name('update-section');
            Route::delete('/{organizationId}/sections/{sectionId}', [SimpleDashboardController::class, 'deleteFormSection'])->name('delete-section');
            Route::post('/fields', [SimpleDashboardController::class, 'addFormField'])->name('add-field');
            Route::put('/{organizationId}/fields/{fieldId}', [SimpleDashboardController::class, 'updateFormField'])->name('update-field');
            Route::delete('/{organizationId}/fields/{fieldId}', [SimpleDashboardController::class, 'deleteFormField'])->name('delete-field');
            Route::post('/{organizationId}/fields/reorder', [SimpleDashboardController::class, 'reorderFormFields'])->name('reorder-fields');
            Route::post('/templates', [SimpleDashboardController::class, 'createFormTemplate'])->name('create-template');
            Route::post('/{organizationId}/{eventId}/apply-template', [SimpleDashboardController::class, 'applyFormTemplate'])->name('apply-template');
            Route::get('/field-types', [SimpleDashboardController::class, 'getFieldTypes'])->name('field-types');
            Route::post('/{organizationId}/{eventId}/export', [SimpleDashboardController::class, 'exportFormConfiguration'])->name('export-config');
            Route::post('/{organizationId}/{eventId}/import', [SimpleDashboardController::class, 'importFormConfiguration'])->name('import-config');
        });

        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [SimpleDashboardController::class, 'users'])->name('index');
            Route::get('/create', function() {
                return view('super-admin.users.create');
            })->name('create');

            Route::post('/', [SimpleDashboardController::class, 'createUser'])->name('store');
            Route::get('/{id}', [SimpleDashboardController::class, 'showUser'])->name('show');
            Route::put('/{id}', [SimpleDashboardController::class, 'updateUser'])->name('update');
            Route::delete('/{id}', [SimpleDashboardController::class, 'deleteUser'])->name('delete');
            Route::post('/{id}/toggle-status', [SimpleDashboardController::class, 'toggleUserStatus'])->name('toggle-status');
            Route::post('/{id}/reset-password', [SimpleDashboardController::class, 'resetUserPassword'])->name('reset-password');
            Route::post('/{id}/send-welcome', [SimpleDashboardController::class, 'sendWelcomeEmail'])->name('send-welcome');
            Route::post('/{id}/impersonate', [SimpleDashboardController::class, 'impersonateUser'])->name('impersonate');
            Route::get('/{id}/permissions', [SimpleDashboardController::class, 'userPermissions'])->name('permissions');
            Route::post('/{id}/permissions', [SimpleDashboardController::class, 'updateUserPermissions'])->name('update-permissions');
            Route::get('/{id}/activity', [SimpleDashboardController::class, 'userActivity'])->name('activity');
            Route::get('/{id}/sessions', [SimpleDashboardController::class, 'userSessions'])->name('sessions');
        });

        Route::prefix('analytics')->name('analytics.')->group(function () {
            Route::get('/', [SimpleDashboardController::class, 'analytics'])->name('index');
            Route::get('/revenue', [SimpleDashboardController::class, 'revenueAnalytics'])->name('revenue');
            Route::get('/usage', [SimpleDashboardController::class, 'usageAnalytics'])->name('usage');
            Route::get('/performance', [SimpleDashboardController::class, 'performanceAnalytics'])->name('performance');
            Route::get('/organizations/growth', [SimpleDashboardController::class, 'organizationGrowthReport'])->name('org-growth');
            Route::get('/events/trends', [SimpleDashboardController::class, 'eventTrendsReport'])->name('event-trends');
            Route::get('/revenue/forecasting', [SimpleDashboardController::class, 'revenueForecast'])->name('revenue-forecast');
            Route::post('/export/organizations', [SimpleDashboardController::class, 'exportOrganizationsReport'])->name('export-orgs');
            Route::post('/export/revenue', [SimpleDashboardController::class, 'exportRevenueReport'])->name('export-revenue');
            Route::post('/export/usage', [SimpleDashboardController::class, 'exportUsageReport'])->name('export-usage');
            Route::get('/custom', [SimpleDashboardController::class, 'customReports'])->name('custom');
            Route::post('/custom/generate', [SimpleDashboardController::class, 'generateCustomReport'])->name('generate-custom');
        });

        Route::prefix('system')->name('system.')->group(function () {
            Route::get('/settings', [SimpleDashboardController::class, 'settings'])->name('settings');
            Route::post('/settings', [SimpleDashboardController::class, 'updateSettings'])->name('settings.update');
            Route::get('/templates', [SimpleDashboardController::class, 'databaseTemplates'])->name('templates');
            Route::post('/templates', [SimpleDashboardController::class, 'createDatabaseTemplate'])->name('templates.create');
            Route::put('/templates/{id}', [SimpleDashboardController::class, 'updateDatabaseTemplate'])->name('templates.update');
            Route::delete('/templates/{id}', [SimpleDashboardController::class, 'deleteDatabaseTemplate'])->name('templates.delete');
            Route::get('/health', [SimpleDashboardController::class, 'systemHealth'])->name('health');
            Route::get('/monitoring', [SimpleDashboardController::class, 'systemMonitoring'])->name('monitoring');
            Route::get('/performance', [SimpleDashboardController::class, 'systemPerformance'])->name('performance');
            Route::post('/maintenance/enable', [SimpleDashboardController::class, 'enableMaintenance'])->name('maintenance.enable');
            Route::post('/maintenance/disable', [SimpleDashboardController::class, 'disableMaintenance'])->name('maintenance.disable');
            Route::post('/cache/clear', [SimpleDashboardController::class, 'clearCache'])->name('cache.clear');
            Route::post('/logs/clear', [SimpleDashboardController::class, 'clearLogs'])->name('logs.clear');
            Route::get('/backups', [SimpleDashboardController::class, 'systemBackups'])->name('backups');
            Route::post('/backups/create', [SimpleDashboardController::class, 'createSystemBackup'])->name('backups.create');
            Route::post('/backups/{id}/restore', [SimpleDashboardController::class, 'restoreSystemBackup'])->name('backups.restore');
            Route::delete('/backups/{id}', [SimpleDashboardController::class, 'deleteSystemBackup'])->name('backups.delete');
        });
        Route::prefix('logs')->name('logs.')->group(function () {
            Route::get('/', [SimpleDashboardController::class, 'logs'])->name('index');
            Route::get('/admin', [SimpleDashboardController::class, 'adminLogs'])->name('admin');
            Route::get('/system', [SimpleDashboardController::class, 'systemLogs'])->name('system');
            Route::get('/errors', [SimpleDashboardController::class, 'errorLogs'])->name('errors');
            Route::get('/organizations/{id}', [SimpleDashboardController::class, 'organizationLogs'])->name('organization');
            Route::post('/export', [SimpleDashboardController::class, 'exportLogs'])->name('export');
            Route::delete('/clear/{type}', [SimpleDashboardController::class, 'clearSpecificLogs'])->name('clear');
        });

        Route::get('/alerts', [SimpleDashboardController::class, 'alerts'])->name('alerts');
        Route::post('/alerts/{id}/dismiss', [SimpleDashboardController::class, 'dismissAlert'])->name('alerts.dismiss');
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [SimpleDashboardController::class, 'notifications'])->name('index');
            Route::post('/', [SimpleDashboardController::class, 'sendNotification'])->name('send');
            Route::get('/templates', [SimpleDashboardController::class, 'notificationTemplates'])->name('templates');
            Route::post('/broadcast', [SimpleDashboardController::class, 'broadcastNotification'])->name('broadcast');
            Route::post('/organizations/{id}/notify', [SimpleDashboardController::class, 'notifyOrganization'])->name('notify-org');
        });
        Route::prefix('billing')->name('billing.')->group(function () {
            Route::get('/plans', [SimpleDashboardController::class, 'subscriptionPlans'])->name('plans');
            Route::post('/plans', [SimpleDashboardController::class, 'createSubscriptionPlan'])->name('plans.create');
            Route::put('/plans/{id}', [SimpleDashboardController::class, 'updateSubscriptionPlan'])->name('plans.update');
            Route::get('/invoices', [SimpleDashboardController::class, 'invoices'])->name('invoices');
            Route::post('/invoices/generate', [SimpleDashboardController::class, 'generateInvoices'])->name('invoices.generate');
            Route::get('/invoices/{id}', [SimpleDashboardController::class, 'showInvoice'])->name('invoices.show');

            // Paiements
            Route::get('/payments', [SimpleDashboardController::class, 'payments'])->name('payments');
            Route::get('/revenue', [SimpleDashboardController::class, 'revenueOverview'])->name('revenue');
        });
    });
});

Route::prefix('org/{org_slug}')->where(['org_slug' => '[a-z0-9\-]+'])->name('org.')->group(function () {

    Route::middleware('organization.guest')->group(function () {
        Route::get('/login', [App\Http\Controllers\Organization\AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [App\Http\Controllers\Organization\AuthController::class, 'login'])->name('login.submit');
    });

    Route::middleware(['organization.auth', 'organization.db'])->group(function () {

        Route::get('/dashboard', [App\Http\Controllers\Organization\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/filtered-registrations', [DashboardController::class, 'getFilteredRegistrations'])->name('filtered-registrations');
        Route::post('/logout', [App\Http\Controllers\Organization\AuthController::class, 'logout'])->name('logout');
        Route::get('/profile', [App\Http\Controllers\Organization\AuthController::class, 'showProfile'])->name('profile');
        Route::put('/profile', [App\Http\Controllers\Organization\AuthController::class, 'updateProfile'])->name('profile.update');
        Route::put('/password', [App\Http\Controllers\Organization\AuthController::class, 'updatePassword'])->name('password.update');
        Route::prefix('events')->name('events.')->group(function () {
            Route::get('/', [App\Http\Controllers\Organization\EventController::class, 'index'])->name('index');
            Route::get('/search', [App\Http\Controllers\Organization\EventController::class, 'search'])->name('search');
            Route::get('/create', [App\Http\Controllers\Organization\EventController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Organization\EventController::class, 'store'])->name('store');
            // Routes paramétrées doivent être après les routes spécifiques
            Route::get('/{event}/edit', [App\Http\Controllers\Organization\EventController::class, 'edit'])->name('edit')->where(['event' => '[0-9]+']);
            Route::put('/{event}', [App\Http\Controllers\Organization\EventController::class, 'update'])->name('update')->where(['event' => '[0-9]+']);
            Route::delete('/{event}', [App\Http\Controllers\Organization\EventController::class, 'destroy'])->name('destroy')->where(['event' => '[0-9]+']);
            Route::get('/{event}', [App\Http\Controllers\Organization\EventController::class, 'show'])->name('show')->where(['event' => '[0-9]+']);
        });

        Route::prefix('accounts')->name('accounts.')->group(function () {
            // Routes pour la gestion des comptes - à implémenter
            Route::get('/', function() {
                $user = session('organization_user');
                $orgSlug = request()->route('org_slug');
                $organization = \Illuminate\Support\Facades\DB::connection('saas_master')
                    ->table('organizations')
                    ->where('id', $user['org_id'])
                    ->select('id', 'org_name', 'subdomain', 'organization_logo', 'database_name')
                    ->first();

                return view('organization.accounts.index', [
                    'user' => $user,
                    'orgSlug' => $orgSlug,
                    'organization' => $organization
                ]);
            })->name('index');
        });
        Route::prefix('registrations')->name('registrations.')->group(function () {
            // Routes temporairement commentées - contrôleur manquant
            // Route::get('/', [App\Http\Controllers\Organization\RegistrationController::class, 'allRegistrations'])->name('all');
            // Route::get('/{registration}', [App\Http\Controllers\Organization\RegistrationController::class, 'show'])->name('show');
            // Route::put('/{registration}', [App\Http\Controllers\Organization\RegistrationController::class, 'update'])->name('update');
            // Route::delete('/{registration}', [App\Http\Controllers\Organization\RegistrationController::class, 'destroy'])->name('destroy');
            // Route::post('/{registration}/confirm', [App\Http\Controllers\Organization\RegistrationController::class, 'confirm'])->name('confirm');
            // Route::post('/{registration}/cancel', [App\Http\Controllers\Organization\RegistrationController::class, 'cancel'])->name('cancel');
            // Route::post('/{registration}/resend-ticket', [App\Http\Controllers\Organization\RegistrationController::class, 'resendTicket'])->name('resend-ticket');
        });

        // Routes pour la gestion des collaborateurs
        Route::prefix('collaborateurs')->name('collaborateurs.')->group(function () {
            Route::get('/', [App\Http\Controllers\Organization\ReferrerController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Organization\ReferrerController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Organization\ReferrerController::class, 'store'])->name('store');
            Route::get('/events/{eventId}/assign-commission', [App\Http\Controllers\Organization\ReferrerController::class, 'assignCommission'])->name('assign-commission')->where(['eventId' => '[0-9]+']);
            Route::post('/events/{eventId}/store-commission', [App\Http\Controllers\Organization\ReferrerController::class, 'storeCommission'])->name('store-commission')->where(['eventId' => '[0-9]+']);
            // Routes spécifiques AVANT les routes génériques (ordre important pour Laravel)
            Route::get('/{id}/edit', [App\Http\Controllers\Organization\ReferrerController::class, 'edit'])->name('edit')->where(['id' => '[0-9]+']);
            Route::post('/{id}/toggle-status', [App\Http\Controllers\Organization\ReferrerController::class, 'toggleStatus'])->name('toggle-status')->where(['id' => '[0-9]+']);
            // Routes génériques en dernier (doivent être après les routes spécifiques)
            Route::put('/{id}', [App\Http\Controllers\Organization\ReferrerController::class, 'update'])->name('update')->where(['id' => '[0-9]+']);
            Route::delete('/{id}', [App\Http\Controllers\Organization\ReferrerController::class, 'destroy'])->name('destroy')->where(['id' => '[0-9]+']);
            Route::get('/{id}', [App\Http\Controllers\Organization\ReferrerController::class, 'show'])->name('show')->where(['id' => '[0-9]+']);
        });
        // Routes verifiers - commentées temporairement (contrôleur manquant)
        // Route::prefix('verifiers')->name('verifiers.')->group(function () {
        //     Route::get('/', [App\Http\Controllers\Organization\VerifierController::class, 'index'])->name('index');
        //     Route::get('/create', [App\Http\Controllers\Organization\VerifierController::class, 'create'])->name('create');
        //     Route::post('/', [App\Http\Controllers\Organization\VerifierController::class, 'store'])->name('store');
        //     Route::get('/{verifier}/edit', [App\Http\Controllers\Organization\VerifierController::class, 'edit'])->name('edit');
        //     Route::put('/{verifier}', [App\Http\Controllers\Organization\VerifierController::class, 'update'])->name('update');
        //     Route::delete('/{verifier}', [App\Http\Controllers\Organization\VerifierController::class, 'destroy'])->name('destroy');
        // });
        // Routes analytics - commentées temporairement (contrôleur manquant)
        // Route::prefix('analytics')->name('analytics.')->group(function () {
        //     Route::get('/', [App\Http\Controllers\Organization\AnalyticsController::class, 'index'])->name('index');
        //     Route::get('/events', [App\Http\Controllers\Organization\AnalyticsController::class, 'events'])->name('events');
        //     Route::get('/revenue', [App\Http\Controllers\Organization\AnalyticsController::class, 'revenue'])->name('revenue');
        //     Route::get('/participants', [App\Http\Controllers\Organization\AnalyticsController::class, 'participants'])->name('participants');
        //     Route::get('/api/events-chart', [App\Http\Controllers\Organization\AnalyticsController::class, 'eventsChartData'])->name('api.events-chart');
        //     Route::get('/api/revenue-chart', [App\Http\Controllers\Organization\AnalyticsController::class, 'revenueChartData'])->name('api.revenue-chart');
        //     Route::get('/api/registrations-chart', [App\Http\Controllers\Organization\AnalyticsController::class, 'registrationsChartData'])->name('api.registrations-chart');
        // });
        // Routes settings - commentées temporairement (contrôleur manquant)
        // Route::prefix('settings')->name('settings.')->group(function () {
        //     Route::get('/', [App\Http\Controllers\Organization\SettingsController::class, 'index'])->name('index');
        //     Route::put('/general', [App\Http\Controllers\Organization\SettingsController::class, 'updateGeneral'])->name('general.update');
        //     Route::put('/branding', [App\Http\Controllers\Organization\SettingsController::class, 'updateBranding'])->name('branding.update');
        //     Route::put('/notifications', [App\Http\Controllers\Organization\SettingsController::class, 'updateNotifications'])->name('notifications.update');
        //     Route::put('/payments', [App\Http\Controllers\Organization\SettingsController::class, 'updatePayments'])->name('payments.update');
        // });

    });
});

Route::post('/api/webhook/handle-transaction', [PaymentController::class, 'handleAfribapayTransaction'])
     ->name('api.webhook.afribapay');

Route::middleware(['tenant.resolve'])->group(function () {

    // Route pour le dashboard apporteur d'affaire

    Route::get('/{org_slug}', [OrganizationController::class, 'showEvents'])
         ->name('org.events')
         ->where(['org_slug' => '[a-z0-9\-]+']);

    Route::get('/{org_slug}/rss', [OrganizationController::class, 'rssFeed'])
         ->name('org.rss')
         ->where(['org_slug' => '[a-z0-9\-]+']);

    Route::prefix('{org_slug}/{event_slug}')
         ->where([
             'org_slug' => '[a-z0-9\-]+',
             'event_slug' => '[a-z0-9\-]+'
         ])
         ->middleware(['event.resolve'])
         ->name('event.')
         ->group(function () {

        Route::get('/test-hash', [PaymentController::class, 'testHashGeneration'])
            ->name('test.hash.generation');

        Route::get('/', [EventController::class, 'showRegistrationForm'])->name('registration');
        Route::post('/', [EventController::class, 'storeRegistration'])->name('registration.store');
        Route::get('/check-status/{email}', [EventController::class, 'checkRegistrationStatus'])->name('registration.check');
        Route::get('/finaliser-paiement/{registrationId}', [EventController::class, 'completePartialPayment'])
            ->name('registration.complete-payment')
            ->where('registrationId', '[0-9]+');
        Route::get('/verify-zone/{ticket_hash}', [VerifierController::class, 'verifyZoneTicket'])
             ->name('verify-zone')
             ->where('ticket_hash', '[a-f0-9]{64}|[a-f0-9]{32}');
        Route::get('/verify/{ticket_hash}', [VerifierController::class, 'verifyTicketWithZone'])
             ->name('verify')
             ->where('ticket_hash', '[a-f0-9]{32}|[A-Z0-9\-]+');
        Route::prefix('verify')->group(function () {
            Route::get('/auth', [VerifierController::class, 'showAuthForm'])->name('verifier.auth');
            Route::post('/auth', [VerifierController::class, 'authenticate'])->name('verifier.authenticate');
        });
        Route::prefix('verify')->middleware(['verifier.auth'])->group(function () {

            Route::get('/scan', [VerifierController::class, 'showScanInterface'])->name('verifier.scan');
            Route::get('/dashboard', [VerifierController::class, 'dashboard'])->name('verifier.dashboard');
            Route::get('/analytics', [VerifierController::class, 'analytics'])->name('verifier.analytics');
            Route::get('/zone/{ticket_hash}', [VerifierController::class, 'verifyTicketWithZone'])
                 ->name('verifier.verify-zone')
                 ->where('ticket_hash', '[a-f0-9]{32}|[A-Z0-9\-]+');
            Route::post('/set-zone', [VerifierController::class, 'setActiveZone'])->name('verifier.set-zone');
            Route::get('/zone-status/{zone}', [VerifierController::class, 'checkZoneAccess'])
                 ->name('verifier.zone-status')
                 ->where('zone', '[a-z_]+');
            Route::post('/process-scan', [VerifierController::class, 'processScan'])->name('verifier.process-scan');
            Route::post('/manual-verify', [VerifierController::class, 'verifyManual'])->name('verifier.manual-verify');
            Route::post('/bulk-verify', [VerifierController::class, 'processBulkVerification'])->name('verifier.bulk-verify');
            Route::get('/history', [VerifierController::class, 'verificationHistory'])->name('verifier.history');
            Route::get('/history/detailed', [VerifierController::class, 'detailedHistory'])->name('verifier.history.detailed');
            Route::get('/export', [VerifierController::class, 'exportVerifications'])->name('verifier.export');
            Route::get('/export/detailed', [VerifierController::class, 'exportDetailedVerifications'])->name('verifier.export.detailed');
            Route::prefix('api')->group(function () {
                Route::get('/stats', [VerifierController::class, 'getRealtimeStats'])->name('verifier.api.stats');
                Route::get('/search', [VerifierController::class, 'searchRegistration'])->name('verifier.api.search');
                Route::get('/zones', [VerifierController::class, 'getAvailableZones'])->name('verifier.api.zones');
                Route::get('/check-access-time/{zone}', [VerifierController::class, 'checkZoneAccessTime'])
                     ->name('verifier.api.check-access-time')
                     ->where('zone', '[a-z_]+');
                Route::post('/keep-alive', [VerifierController::class, 'keepAlive'])->name('verifier.api.keep-alive');
                Route::get('/zone-stats/{zone}', [VerifierController::class, 'getZoneStats'])
                     ->name('verifier.api.zone-stats')
                     ->where('zone', '[a-z_]+');
                Route::post('/quick-verify', [VerifierController::class, 'quickVerify'])->name('verifier.api.quick-verify');
            });

            // Déconnexion vérificateur
            Route::post('/logout', [VerifierController::class, 'logout'])->name('verifier.logout');
        });

        Route::post('/verify', [VerifierController::class, 'processTicketVerification'])->name('verify.process');
        Route::get('/validation-paiement', [EventController::class, 'showPaymentValidation'])->name('payment.validation');
        Route::post('/validation-paiement', [EventController::class, 'paymentValidation'])->name('payment.validation.post');
        Route::post('/processus-validation-paiement', [EventController::class, 'processPaymentValidation'])->name('payment.validation.process');
        Route::prefix('validation-paiement')->group(function () {
            Route::post('/orange-money/process', [PaymentController::class, 'orangeMoneyProcess'])->name('payment.orange.process');
            Route::post('/mtn-money/process', [PaymentController::class, 'mtnMoneyProcess'])->name('payment.mtn.process');
            Route::post('/moov-money/process', [PaymentController::class, 'moovMoneyProcess'])->name('payment.moov.process');
            Route::post('/wave-process', [PaymentController::class, 'waveProcess'])->name('payment.wave.process');
            Route::post('/payment-status', [PaymentController::class, 'checkPaymentStatus'])->name('payment.status.check');
        });

        Route::get('/success', [EventController::class, 'showSuccess'])->name('success');
        Route::get('/error', [EventController::class, 'showError'])->name('error');
        Route::get('/ticket/{registration}', [EventController::class, 'downloadTicket'])->name('ticket.download');
        Route::get('/qr/{registration}', [EventController::class, 'generateQRCode'])->name('qr.generate');
        Route::get('/tickets/bulk/{registrations}', [EventController::class, 'downloadBulkTickets'])->name('tickets.bulk');
        Route::get('/tickets/group/{group_id}', [EventController::class, 'downloadGroupTickets'])->name('tickets.group');
        Route::middleware(['verifier.auth'])->group(function () {

            Route::get('/verification-paiement', [VerifierController::class, 'showPaymentVerification'])->name('verification.payment');
            Route::post('/verification-paiement', [VerifierController::class, 'verifyPayment'])->name('verification.payment.process');
            Route::get('/verification-repas', [VerifierController::class, 'showMealVerification'])->name('verification.meal');
            Route::post('/verification-repas', [VerifierController::class, 'verifyMeal'])->name('verification.meal.process');

            $zones = [
                'opening', 'gala', 'conference', 'networking', 'photos', 'ag', 'lunch',
                'dinner', 'vip', 'workshop', 'exhibition', 'welcome', 'closing',
                'awards', 'break', 'panel', 'demo'
            ];

            foreach ($zones as $zone) {
                Route::get('/verification/' . $zone, [VerifierController::class, 'showZoneVerification'])
                     ->defaults('zone', $zone)
                     ->name('verification.' . $zone);
                Route::post('/verification/' . $zone, [VerifierController::class, 'verifyZoneAccess'])
                     ->defaults('zone', $zone)
                     ->name('verification.' . $zone . '.process');
            }

            Route::post('/verification/time-based', [VerifierController::class, 'verifyTimeBasedAccess'])->name('verification.time');
            Route::post('/verification/geo-based', [VerifierController::class, 'verifyGeoBasedAccess'])->name('verification.geo');
            Route::post('/verification/capacity-check', [VerifierController::class, 'verifyCapacityAccess'])->name('verification.capacity');
            Route::post('/verification/multi-zone', [VerifierController::class, 'verifyMultiZoneAccess'])->name('verification.multi-zone');
            Route::get('/verifier/manual', [VerifierController::class, 'showManualVerification'])->name('verifier.manual');
            Route::post('/verifier/manual', [VerifierController::class, 'processManualVerification'])->name('verifier.manual.process');
            Route::get('/verifier/search', [VerifierController::class, 'searchRegistrations'])->name('verifier.search');
            Route::post('/verifier/search', [VerifierController::class, 'processSearch'])->name('verifier.search.process');
            Route::get('/verifier/mobile', [VerifierController::class, 'mobileInterface'])->name('verifier.mobile');
            Route::get('/verifier/offline-sync', [VerifierController::class, 'offlineSync'])->name('verifier.offline');
            Route::get('/verifier/kiosk', [VerifierController::class, 'kioskMode'])->name('verifier.kiosk');
            Route::get('/verification-result', [VerifierController::class, 'showVerificationResult'])->name('verification.result');
            Route::get('/verifier/ticket', [VerifierController::class, 'showTicketView'])->name('verifier.ticket.view');
            Route::get('/verifier/ticket/{registration}', [VerifierController::class, 'showTicketDetails'])->name('verifier.ticket.details');
            Route::post('/verifier/scan/bulk', [VerifierController::class, 'processBulkScan'])->name('verifier.scan.bulk');
            Route::get('/verifier/reports/zone/{zone}', [VerifierController::class, 'zoneReport'])
                 ->name('verifier.reports.zone')
                 ->where('zone', '[a-z_]+');
        });

    });
});

require __DIR__ . '/auth.php';
