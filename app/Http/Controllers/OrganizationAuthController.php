<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OrganizationAuthController extends Controller
{
    /**
     * Afficher le formulaire de connexion pour une organisation
     */
    public function showLoginForm(Request $request, $orgSlug = null)
    {
        // Détecter l'organisation via sous-domaine ou slug
        $organization = $this->detectOrganization($request, $orgSlug);
        
        if (!$organization) {
            abort(404, 'Organisation non trouvée');
        }

        // Vérifier si l'organisation est active
        if ($organization->subscription_status !== 'active') {
            abort(403, 'Cette organisation n\'est pas active');
        }

        return view('organization.auth.login', compact('organization'));
    }

    /**
     * Gérer la tentative de connexion pour une organisation
     */
    public function login(Request $request, $orgSlug = null)
    {
        // Validation des données
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ], [
            'email.required' => 'L\'adresse email est requise',
            'email.email' => 'Veuillez saisir une adresse email valide',
            'password.required' => 'Le mot de passe est requis',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères',
        ]);

        // Détecter l'organisation
        $organization = $this->detectOrganization($request, $orgSlug);
        
        if (!$organization) {
            return response()->json([
                'success' => false,
                'message' => 'Organisation non trouvée'
            ], 404);
        }

        // Vérifier si l'organisation est active
        if ($organization->subscription_status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Cette organisation n\'est pas active'
            ], 403);
        }

        // Rate limiting par organisation
        $throttleKey = 'org_login.' . $organization->id . '.' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'success' => false,
                'message' => "Trop de tentatives. Réessayez dans {$seconds} secondes."
            ], 429);
        }

        try {
            // Configurer la connexion à la base de données de l'organisation
            $this->switchToOrganizationDatabase($organization->database_name);

            // Rechercher l'utilisateur dans la base de l'organisation
            $user = $this->findOrganizationUser($request->email);

            // Vérifier les identifiants
            if (!$user || !Hash::check($request->password, $user->password ?? '')) {
                RateLimiter::hit($throttleKey, 300); // 5 minutes
                
                $this->logOrganizationActivity($organization->id, null, 'login_failed', 
                    'Tentative de connexion échouée: ' . $request->email, $request->ip());
                
                return response()->json([
                    'success' => false,
                    'message' => 'Email ou mot de passe incorrect'
                ], 401);
            }

            // Vérifier si l'utilisateur est actif
            if (!($user->is_active ?? true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Votre compte a été désactivé. Contactez l\'administrateur.'
                ], 403);
            }

            // Connexion réussie
            RateLimiter::clear($throttleKey);

            // Mettre à jour la dernière connexion dans la base organisation
            $this->updateUserLastLogin($user->id);

            // Stocker les données de session
            session([
                'organization_user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->fullname ?? ($user->first_name . ' ' . $user->last_name),
                    'role' => $user->role ?? 'user',
                    'permissions' => $user->permissions ?? [],
                ],
                'organization' => [
                    'id' => $organization->id,
                    'name' => $organization->org_name,
                    'type' => $organization->org_type,
                    'database' => $organization->database_name,
                    'logo' => $organization->organization_logo ? url('public/' . $organization->organization_logo) : null,
                ],
                'org_auth_token' => Str::random(60)
            ]);

            // Log de connexion
            $this->logOrganizationActivity($organization->id, $user->id, 'user_login', 
                'Connexion utilisateur réussie', $request->ip());

            return response()->json([
                'success' => true,
                'message' => 'Connexion réussie',
                'redirect' => $this->getRedirectUrl($organization, $user),
                'user' => [
                    'name' => $user->fullname ?? ($user->first_name . ' ' . $user->last_name),
                    'email' => $user->email,
                    'role' => $user->role ?? 'user',
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la connexion organisation: ' . $e->getMessage(), [
                'organization_id' => $organization->id,
                'email' => $request->email,
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur technique est survenue. Veuillez réessayer.'
            ], 500);
        }
    }

    /**
     * Déconnexion utilisateur organisation
     */
    public function logout(Request $request)
    {
        $user = session('organization_user');
        $organization = session('organization');
        
        if ($user && $organization) {
            // Log de déconnexion
            $this->logOrganizationActivity($organization['id'], $user['id'], 'user_logout', 
                'Déconnexion utilisateur', $request->ip());
        }

        // Supprimer les données de session
        session()->forget(['organization_user', 'organization', 'org_auth_token']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Déconnexion réussie',
                'redirect' => $this->getLoginUrl($organization ?? null)
            ]);
        }

        return redirect($this->getLoginUrl($organization ?? null))
            ->with('success', 'Vous avez été déconnecté avec succès');
    }

    /**
     * Vérifier le statut de l'authentification
     */
    public function checkAuth(Request $request)
    {
        $user = session('organization_user');
        $organization = session('organization');
        
        if (!$user || !$organization) {
            return response()->json([
                'authenticated' => false,
                'redirect' => $this->getLoginUrl($organization)
            ], 401);
        }

        return response()->json([
            'authenticated' => true,
            'user' => $user,
            'organization' => $organization
        ]);
    }

    /**
     * Détecter l'organisation via sous-domaine ou slug
     */
    private function detectOrganization(Request $request, $orgSlug = null)
    {
        // Méthode 1: Via slug dans l'URL
        if ($orgSlug) {
            return DB::table('organizations')
                ->where('org_key', $orgSlug)
                ->where('subscription_status', 'active')
                ->first();
        }

        // Méthode 2: Via sous-domaine
        $host = $request->getHost();
        $subdomain = explode('.', $host)[0];
        
        if ($subdomain && $subdomain !== 'www') {
            return DB::table('organizations')
                ->where('subdomain', $subdomain)
                ->where('subscription_status', 'active')
                ->first();
        }

        // Méthode 3: Via domaine personnalisé
        return DB::table('organizations')
            ->where('custom_domain', $host)
            ->where('subscription_status', 'active')
            ->first();
    }

    /**
     * Configurer la connexion à la base de données de l'organisation
     */
    private function switchToOrganizationDatabase(string $databaseName)
    {
        config(['database.connections.organization.database' => $databaseName]);
        DB::purge('organization');
        
        // Tester la connexion
        DB::connection('organization')->getPdo();
    }

    /**
     * Rechercher un utilisateur dans la base de l'organisation
     */
    private function findOrganizationUser(string $email)
    {
        // Essayer différentes tables selon le type d'organisation
        $tables = ['verifiers', 'users', 'members', 'employees', 'participants'];
        
        foreach ($tables as $table) {
            if (DB::connection('organization')->getSchemaBuilder()->hasTable($table)) {
                $user = DB::connection('organization')
                    ->table($table)
                    ->where('email', $email)
                    ->first();
                
                if ($user) {
                    return $user;
                }
            }
        }
        
        return null;
    }

    /**
     * Mettre à jour la dernière connexion
     */
    private function updateUserLastLogin($userId)
    {
        try {
            $tables = ['verifiers', 'users', 'members', 'employees', 'participants'];
            
            foreach ($tables as $table) {
                if (DB::connection('organization')->getSchemaBuilder()->hasTable($table)) {
                    $updated = DB::connection('organization')
                        ->table($table)
                        ->where('id', $userId)
                        ->update(['last_login' => Carbon::now()]);
                    
                    if ($updated) {
                        break;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Impossible de mettre à jour last_login: ' . $e->getMessage());
        }
    }

    /**
     * Obtenir l'URL de redirection après connexion
     */
    private function getRedirectUrl($organization, $user): string
    {
        // URL de base selon l'organisation
        $baseUrl = $organization->custom_domain 
            ? "https://{$organization->custom_domain}"
            : "https://{$organization->subdomain}." . config('app.domain');

        // Redirection selon le rôle
        $role = $user->role ?? 'user';
        
        switch ($role) {
            case 'admin':
            case 'verifier':
                return $baseUrl . '/dashboard/admin';
            case 'manager':
                return $baseUrl . '/dashboard/manager';
            default:
                return $baseUrl . '/dashboard';
        }
    }

    /**
     * Obtenir l'URL de connexion
     */
    /**
 * Obtenir l'URL de connexion pour une organisation
 */
private function getLoginUrl($organization = null): string
{
    if (!$organization) {
        return route('saas.home'); // ou route('home') selon votre route d'accueil
    }

    // Gérer le cas où $organization est un tableau ou un objet
    $customDomain = null;
    $subdomain = null;

    if (is_array($organization)) {
        $customDomain = $organization['custom_domain'] ?? null;
        $subdomain = $organization['subdomain'] ?? null;
    } elseif (is_object($organization)) {
        $customDomain = $organization->custom_domain ?? null;
        $subdomain = $organization->subdomain ?? null;
    }

    // Construire l'URL de base
    if ($customDomain) {
        $baseUrl = "https://{$customDomain}";
    } elseif ($subdomain) {
        $appDomain = config('app.domain', 'localhost');
        $baseUrl = "https://{$subdomain}.{$appDomain}";
    } else {
        // Fallback vers l'URL principale
        return route('saas.home');
    }

    return $baseUrl . '/login';
}

// Version alternative plus concise
private function getLoginUrlAlternative($organization = null): string
{
    if (!$organization) {
        return route('saas.home');
    }

    // Normaliser en tableau pour simplifier
    $org = is_array($organization) ? $organization : (array) $organization;
    
    $customDomain = $org['custom_domain'] ?? null;
    $subdomain = $org['subdomain'] ?? null;
    $appDomain = config('app.domain', 'localhost');

    // Déterminer l'URL de base
    $baseUrl = $customDomain 
        ? "https://{$customDomain}"
        : ($subdomain 
            ? "https://{$subdomain}.{$appDomain}" 
            : config('app.url', 'https://localhost')
        );

    return $baseUrl . '/login';
}

// Version avec gestion d'erreurs
private function getLoginUrlSafe($organization = null): string
{
    try {
        if (!$organization) {
            return route('saas.home');
        }

        // Extraire les données selon le type
        $customDomain = null;
        $subdomain = null;

        if (is_array($organization)) {
            $customDomain = $organization['custom_domain'] ?? null;
            $subdomain = $organization['subdomain'] ?? null;
        } elseif (is_object($organization)) {
            $customDomain = $organization->custom_domain ?? null;
            $subdomain = $organization->subdomain ?? null;
        }

        // Valider les données
        if (empty($customDomain) && empty($subdomain)) {
            \Log::warning('Organisation sans domaine ni sous-domaine', [
                'organization' => $organization
            ]);
            return route('saas.home');
        }

        // Construire l'URL
        if (!empty($customDomain)) {
            $baseUrl = "https://{$customDomain}";
        } else {
            $appDomain = config('app.domain');
            if (empty($appDomain)) {
                throw new \Exception('app.domain non configuré');
            }
            $baseUrl = "https://{$subdomain}.{$appDomain}";
        }

        return $baseUrl . '/login';

    } catch (\Exception $e) {
        \Log::error('Erreur dans getLoginUrl', [
            'organization' => $organization,
            'error' => $e->getMessage()
        ]);
        
        return route('saas.home');
    }
}

    /**
     * Logger une activité dans les logs de l'organisation
     */
    private function logOrganizationActivity($organizationId, $userId, $action, $description, $ip = null)
    {
        try {
            DB::table('organization_logs')->insert([
                'organization_id' => $organizationId,
                'user_id' => $userId,
                'action' => $action,
                'description' => $description,
                'ip_address' => $ip,
                'created_at' => Carbon::now()
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'enregistrement du log organisation: ' . $e->getMessage());
        }
    }

    /**
     * Middleware pour vérifier l'authentification organisation
     */
    public static function checkOrganizationAuth()
    {
        return function ($request, $next) {
            $user = session('organization_user');
            $organization = session('organization');
            
            if (!$user || !$organization) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Accès non autorisé',
                        'redirect' => '/login'
                    ], 401);
                }
                
                return redirect('/login')
                    ->with('error', 'Veuillez vous connecter pour accéder à cette page');
            }
            
            return $next($request);
        };
    }
}