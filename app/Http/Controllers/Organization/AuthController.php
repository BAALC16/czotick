<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLoginForm(Request $request)
    {
        /* $hashedPassword = password_hash('M@rcAdmin2@25!', PASSWORD_DEFAULT);
        $hashedPassword = password_hash('BellUser@25!', PASSWORD_DEFAULT);
        var_dump($hashedPassword);
        die; */
        $orgSlug = $request->route('org_slug');
        
        Log::info('Showing login form', [
            'org_slug' => $orgSlug,
            'has_session' => session('organization_user') ? true : false
        ]);
        
        // Récupérer l'organisation depuis saas_master
        $organization = DB::connection('saas_master')
            ->table('organizations')
            ->where('subdomain', $orgSlug)
            ->first();

        if (!$organization) {
            abort(404, 'Organisation non trouvée ou inactive.');
        }
        
        return view('organization.auth.login', compact('organization', 'orgSlug'));
    }

    public function login(Request $request)
    {
        $orgSlug = $request->route('org_slug');
        $userEmail = $request->email;
        $userIp = $request->ip();
        $userAgent = $request->userAgent();
        
        Log::info('Login attempt started', [
            'email' => $userEmail,
            'org_slug' => $orgSlug,
            'ip' => $userIp,
            'timestamp' => now()->toISOString()
        ]);

        // Limitation du taux de tentatives de connexion
        $key = 'login.' . $userIp . '.' . $orgSlug;
        
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            
            Log::warning('Rate limit exceeded', [
                'email' => $userEmail,
                'org_slug' => $orgSlug,
                'ip' => $userIp,
                'retry_after_seconds' => $seconds
            ]);
            
            return back()->withErrors([
                'email' => "Trop de tentatives de connexion. Réessayez dans {$seconds} secondes."
            ])->withInput();
        }

        // Validation des données
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6|max:255',
        ], [
            'email.required' => 'L\'adresse email est requise.',
            'email.email' => 'L\'adresse email doit être valide.',
            'password.required' => 'Le mot de passe est requis.',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères.',
        ]);

        if ($validator->fails()) {
            Log::warning('Input validation failed', [
                'email' => $userEmail,
                'org_slug' => $orgSlug,
                'validation_errors' => $validator->errors()->toArray()
            ]);
            
            RateLimiter::hit($key);
            return back()->withErrors($validator)->withInput($request->only('email'));
        }
        
        try {
            // 1. Récupérer l'organisation depuis saas_master
            Log::debug('Fetching organization', ['org_slug' => $orgSlug]);
            
            $organization = $this->getOrganizationBySlug($orgSlug);
            
            if (!$organization) {
                Log::warning('Organization not found', [
                    'org_slug' => $orgSlug,
                    'email' => $userEmail
                ]);
                
                RateLimiter::hit($key);
                return back()->withErrors([
                    'email' => 'Organisation non trouvée ou inactive.'
                ])->withInput($request->only('email'));
            }

            Log::info('Organization found', [
                'org_id' => $organization->id,
                'database_name' => $organization->database_name,
                'org_name' => $organization->org_name
            ]);

            // 2. Configurer et tester la connexion à la base de données de l'organisation
            if (!$this->configureTenantDatabase($organization->database_name)) {
                Log::error('Failed to configure tenant database', [
                    'org_slug' => $orgSlug,
                    'database_name' => $organization->database_name
                ]);
                
                return back()->withErrors([
                    'email' => 'Erreur de configuration. Contactez l\'administrateur.'
                ])->withInput($request->only('email'));
            }

            // 3. Chercher l'utilisateur dans la base de données tenant
            Log::info('=== LOGIN DEBUG START ===');
            Log::info('Searching user in tenant database', [
                'email' => $userEmail,
                'database' => $organization->database_name,
                'organization_id' => $organization->id,
                'org_slug' => $orgSlug
            ]);
            
            $user = $this->getUserByEmail($userEmail);

            if (!$user) {
                Log::error('❌ User not found in tenant database', [
                    'email' => $userEmail,
                    'org_slug' => $orgSlug,
                    'database' => $organization->database_name,
                    'organization_id' => $organization->id
                ]);
                
                RateLimiter::hit($key);
                Log::info('=== LOGIN DEBUG END: USER NOT FOUND ===');
                return back()->withErrors([
                    'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.'
                ])->withInput($request->only('email'));
            }

            Log::info('✅ User found in tenant database', [
                'user_id' => $user->id ?? null,
                'email' => $user->email ?? null,
                'role' => $user->role ?? null,
                'is_active' => $user->is_active ?? true,
                'has_password' => isset($user->password),
                'has_password_hash' => isset($user->password_hash),
                'password_length' => isset($user->password) ? strlen($user->password) : (isset($user->password_hash) ? strlen($user->password_hash) : 0),
                'user_object_keys' => array_keys((array)$user)
            ]);

            // 4. Vérifier le mot de passe
            // Utiliser password_hash si disponible, sinon password
            $passwordField = isset($user->password_hash) ? $user->password_hash : ($user->password ?? null);
            
            Log::info('🔐 Password verification attempt', [
                'user_id' => $user->id ?? null,
                'email' => $userEmail,
                'has_password_field' => !empty($passwordField),
                'password_field_length' => $passwordField ? strlen($passwordField) : 0,
                'password_field_preview' => $passwordField ? substr($passwordField, 0, 20) . '...' : null,
                'has_password_hash' => isset($user->password_hash),
                'has_password' => isset($user->password),
                'provided_password_length' => strlen($request->password)
            ]);
            
            if (!$passwordField) {
                Log::error('❌ No password field found for user', [
                    'user_id' => $user->id ?? null,
                    'email' => $userEmail,
                    'user_columns' => array_keys((array)$user),
                    'user_data' => (array)$user
                ]);
                
                RateLimiter::hit($key);
                Log::info('=== LOGIN DEBUG END: NO PASSWORD FIELD ===');
                return back()->withErrors([
                    'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.'
                ])->withInput($request->only('email'));
            }
            
            Log::info('🔍 Checking password hash', [
                'password_field_type' => isset($user->password_hash) ? 'password_hash' : 'password',
                'password_field_starts_with' => substr($passwordField, 0, 7)
            ]);
            
            $passwordMatches = Hash::check($request->password, $passwordField);
            
            Log::info('🔑 Hash check result', [
                'password_matches' => $passwordMatches,
                'used_field' => isset($user->password_hash) ? 'password_hash' : 'password'
            ]);
            
            if (!$passwordMatches) {
                Log::error('❌ Password verification FAILED', [
                    'user_id' => $user->id ?? null,
                    'email' => $userEmail,
                    'ip' => $userIp,
                    'has_password_field' => !empty($passwordField),
                    'password_field_length' => strlen($passwordField),
                    'password_field_preview' => substr($passwordField, 0, 30),
                    'hash_check_result' => false,
                    'provided_password_length' => strlen($request->password)
                ]);
                
                RateLimiter::hit($key);
                Log::info('=== LOGIN DEBUG END: PASSWORD MISMATCH ===');
                return back()->withErrors([
                    'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.'
                ])->withInput($request->only('email'));
            }
            
            Log::info('✅ Password verified successfully');

            // 5. Vérifier si le compte est actif (par défaut true si le champ n'existe pas)
            $isActive = isset($user->is_active) ? $user->is_active : true;
            if (!$isActive) {
                Log::warning('Inactive user login attempt', [
                    'user_id' => $user->id,
                    'email' => $userEmail
                ]);
                
                RateLimiter::hit($key);
                return back()->withErrors([
                    'email' => 'Votre compte est désactivé. Contactez l\'administrateur.'
                ])->withInput($request->only('email'));
            }

            // 6. Connexion réussie - effacer les tentatives
            RateLimiter::clear($key);
            
            Log::info('✅ All checks passed, creating session...');

            // 7. Créer la session utilisateur
            $this->createUserSession($request, $user, $organization);

            // 8. Mettre à jour les informations de dernière connexion
            $this->updateLastLogin($user->id, $request);

            Log::info('🎉 User logged in successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'org_slug' => $orgSlug,
                'session_id' => session()->getId(),
                'role' => $user->role ?? null
            ]);
            Log::info('=== LOGIN DEBUG END: SUCCESS ===');

            // Gérer les deux structures de noms possibles pour le message de bienvenue
            $userName = $user->first_name ?? $user->prenoms ?? $user->email;
            
            return redirect()->route('org.dashboard', ['org_slug' => $orgSlug])
                ->with('success', "Bienvenue {$userName} ! Connexion réussie.");

        } catch (\Exception $e) {
            Log::error('Login error', [
                'email' => $userEmail,
                'org_slug' => $orgSlug,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors([
                'email' => 'Une erreur est survenue. Veuillez réessayer.'
            ])->withInput($request->only('email'));
        }
    }
    
    public function logout(Request $request)
    {
        $user = session('organization_user');
        $orgSlug = $user['org_subdomain'] ?? $request->route('org_slug');
        
        if ($user) {
            try {
                if (isset($user['database_name'])) {
                    $this->configureTenantDatabase($user['database_name']);
                    
                    DB::connection('tenant')
                        ->table('user_sessions')
                        ->where('session_token', $user['session_token'] ?? '')
                        ->update(['is_active' => false, 'updated_at' => now()]);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to invalidate session in database', [
                    'error' => $e->getMessage()
                ]);
            }
            
            Log::info('User logged out', [
                'user_id' => $user['id'],
                'email' => $user['email'],
                'org_slug' => $orgSlug,
            ]);
        }
        
        session()->forget('organization_user');
        session()->invalidate();
        session()->regenerateToken();
        
        return redirect()->route('org.login', ['org_slug' => $orgSlug])
            ->with('success', 'Vous avez été déconnecté avec succès.');
    }

    /**
     * Récupérer l'organisation par slug depuis saas_master
     */
    private function getOrganizationBySlug($orgSlug)
    {
        return cache()->remember("org.{$orgSlug}", 300, function () use ($orgSlug) {
            return DB::connection('saas_master')
                ->table('organizations')
                ->where('subdomain', $orgSlug)
                ->select('id', 'org_name', 'subdomain', 'database_name', 'org_key')
                ->first();
        });
    }

    /**
     * Récupérer l'utilisateur par email depuis la base de données tenant
     */
    private function getUserByEmail($email)
    {
        try {
            // Normaliser l'email (minuscules, suppression des espaces)
            $normalizedEmail = strtolower(trim($email));
            
            Log::info('🔍 Searching user by email', [
                'original_email' => $email,
                'normalized_email' => $normalizedEmail,
                'connection' => 'tenant'
            ]);
            
            // Les utilisateurs sont dans la table users de la base tenant
            // Recherche insensible à la casse
            $user = DB::connection('tenant')
                ->table('users')
                ->whereRaw('LOWER(TRIM(email)) = ?', [$normalizedEmail])
                ->first();
            
            // Si pas trouvé, essayer la recherche exacte aussi
            if (!$user) {
                Log::info('⚠️ User not found with normalized email, trying exact match', [
                    'email' => $email
                ]);
                $user = DB::connection('tenant')
                    ->table('users')
                    ->where('email', $email)
                    ->first();
            }
            
            // Si toujours pas trouvé, lister tous les emails pour debug
            if (!$user) {
                $allEmails = DB::connection('tenant')
                    ->table('users')
                    ->pluck('email')
                    ->toArray();
                
                Log::error('❌ User not found in tenant database', [
                    'searched_email' => $email,
                    'normalized_email' => $normalizedEmail,
                    'available_emails_in_database' => $allEmails,
                    'total_users_in_database' => count($allEmails)
                ]);
                return null;
            }
            
            // Normaliser les champs selon la structure de la table
            // Support pour deux structures possibles:
            // 1. Ancienne: nom, prenoms, password
            // 2. Nouvelle: first_name, last_name, password_hash ou password, role, is_active
            
            // Si la table a first_name/last_name, les utiliser, sinon nom/prenoms
            if (!isset($user->first_name) && isset($user->prenoms)) {
                $user->first_name = $user->prenoms;
            }
            if (!isset($user->last_name) && isset($user->nom)) {
                $user->last_name = $user->nom;
            }
            
            // Si password_hash existe, l'utiliser, sinon password
            if (!isset($user->password_hash) && isset($user->password)) {
                $user->password_hash = $user->password;
            }
            
            // Définir is_active par défaut si absent
            if (!isset($user->is_active)) {
                $user->is_active = true;
            }
            
            // Définir role par défaut si absent
            if (!isset($user->role)) {
                $user->role = 'user';
            }
            
            Log::info('✅ User found in tenant database', [
                'email' => $email,
                'user_id' => $user->id ?? null,
                'has_password' => isset($user->password),
                'has_password_hash' => isset($user->password_hash),
                'is_active' => $user->is_active ?? null,
                'role' => $user->role ?? null,
                'password_length' => isset($user->password) ? strlen($user->password) : 0,
                'password_hash_length' => isset($user->password_hash) ? strlen($user->password_hash) : 0,
                'columns' => array_keys((array)$user),
                'first_name' => $user->first_name ?? $user->prenoms ?? null,
                'last_name' => $user->last_name ?? $user->nom ?? null
            ]);
            
            return $user;
        } catch (\Exception $e) {
            Log::error('Error fetching user from tenant database', [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Configurer la connexion à la base de données tenant
     * En local, utilise directement les identifiants de la connexion principale
     */
    private function configureTenantDatabase($databaseName)
    {
        try {
            // En local, utiliser directement les identifiants de la connexion principale
            // car les utilisateurs MySQL dédiés n'existent généralement pas
            $mysqlConfig = config('database.connections.mysql');
            
            $tenantConfig = [
                'driver' => 'mysql',
                'host' => $mysqlConfig['host'],
                'port' => $mysqlConfig['port'],
                'database' => $databaseName,
                'username' => $mysqlConfig['username'],
                'password' => $mysqlConfig['password'],
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'prefix_indexes' => true,
                'strict' => true,
                'engine' => null,
                'options' => extension_loaded('pdo_mysql') ? array_filter([
                    \PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
                ]) : [],
            ];

            // Configurer la connexion
            config(['database.connections.tenant' => $tenantConfig]);
            
            // Purger les connexions existantes pour forcer la reconnexion
            DB::purge('tenant');
            
            // Tester la connexion
            DB::connection('tenant')->getPdo();
            
            Log::debug('Tenant database configured successfully', [
                'database_name' => $databaseName,
                'username' => $mysqlConfig['username']
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to configure tenant database', [
                'database_name' => $databaseName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Créer la session utilisateur
     */
    private function createUserSession(Request $request, $user, $organization)
    {
        // Régénérer l'ID de session pour sécurité
        $request->session()->regenerate();
        
        // Gérer les deux structures de noms possibles
        $firstName = $user->first_name ?? $user->prenoms ?? '';
        $lastName = $user->last_name ?? $user->nom ?? '';
        $fullName = trim($firstName . ' ' . $lastName);
        
        $sessionData = [
            'id' => $user->id,
            'email' => $user->email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'full_name' => $fullName,
            'role' => $user->role ?? 'user',
            'permissions' => json_decode($user->permissions ?? '{}', true),
            'phone' => $user->phone ?? $user->mobile ?? null,
            'org_id' => $organization->id,
            'org_name' => $organization->org_name,
            'org_subdomain' => $organization->subdomain,
            'database_name' => $organization->database_name,
            'logged_in_at' => now()->toDateTimeString(),
            'session_token' => Str::random(60),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ];
        
        // Stocker dans la session
        session(['organization_user' => $sessionData]);
        session()->save();
        
        // Stocker la session en base de données pour un suivi avancé
        $this->storeSessionInDatabase($user->id, $sessionData);
        
        Log::debug('User session created', [
            'user_id' => $user->id,
            'session_token' => $sessionData['session_token'],
            'org_id' => $organization->id
        ]);
    }

    /**
     * Stocker la session en base de données
     */
    private function storeSessionInDatabase($userId, $sessionData)
    {
        try {
            // Vérifier si la table user_sessions existe
            if (!$this->tableExists('user_sessions')) {
                Log::warning('Table user_sessions does not exist, skipping session storage');
                return;
            }

            DB::connection('tenant')
                ->table('user_sessions')
                ->insert([
                    'user_id' => $userId,
                    'session_token' => $sessionData['session_token'],
                    'ip_address' => $sessionData['ip_address'],
                    'user_agent' => $sessionData['user_agent'],
                    'expires_at' => now()->addHours(8),
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
            Log::debug('Session stored in database', ['user_id' => $userId]);
            
        } catch (\Exception $e) {
            Log::warning('Failed to store session in database', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Vérifier si une table existe
     */
    private function tableExists($tableName)
    {
        try {
            return DB::connection('tenant')
                ->getSchemaBuilder()
                ->hasTable($tableName);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Mettre à jour les informations de dernière connexion
     */
    private function updateLastLogin($userId, Request $request)
    {
        try {
            // Vérifier si la colonne last_login_ip existe avant de l'utiliser
            $updateData = [
                'last_login_at' => now(),
                'updated_at' => now(),
            ];
            
            // Vérifier si la colonne last_login_ip existe
            try {
                $columns = DB::connection('tenant')
                    ->select("SHOW COLUMNS FROM users LIKE 'last_login_ip'");
                
                if (!empty($columns)) {
                    $updateData['last_login_ip'] = $request->ip();
                }
            } catch (\Exception $e) {
                // Colonne n'existe pas, on continue sans
                Log::debug('Column last_login_ip does not exist, skipping');
            }
            
            DB::connection('tenant')
                ->table('users')
                ->where('id', $userId)
                ->update($updateData);
                
            Log::debug('Last login updated', ['user_id' => $userId]);
            
        } catch (\Exception $e) {
            Log::warning('Failed to update last login info', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Afficher le profil utilisateur
     */
    public function showProfile(Request $request)
    {
        $user = session('organization_user');
        $orgSlug = $request->route('org_slug');
        
        if (!$user) {
            return redirect()->route('org.login', ['org_slug' => $orgSlug]);
        }
        
        return view('organization.auth.profile', compact('user', 'orgSlug'));
    }

    /**
     * Mettre à jour le profil utilisateur
     */
    public function updateProfile(Request $request)
    {
        $user = session('organization_user');
        $orgSlug = $request->route('org_slug');
        
        if (!$user) {
            return redirect()->route('org.login', ['org_slug' => $orgSlug]);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $this->configureTenantDatabase($user['database_name']);
            
            DB::connection('tenant')
                ->table('users')
                ->where('id', $user['id'])
                ->update([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'phone' => $request->phone,
                    'updated_at' => now(),
                ]);

            // Mettre à jour la session
            $user['first_name'] = $request->first_name;
            $user['last_name'] = $request->last_name;
            $user['full_name'] = trim($request->first_name . ' ' . $request->last_name);
            $user['phone'] = $request->phone;
            
            session(['organization_user' => $user]);

            return back()->with('success', 'Profil mis à jour avec succès.');
            
        } catch (\Exception $e) {
            Log::error('Profile update failed', [
                'user_id' => $user['id'],
                'error' => $e->getMessage()
            ]);
            
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour du profil.']);
        }
    }

    /**
     * Mettre à jour le mot de passe
     */
    public function updatePassword(Request $request)
    {
        $user = session('organization_user');
        $orgSlug = $request->route('org_slug');
        
        if (!$user) {
            return redirect()->route('org.login', ['org_slug' => $orgSlug]);
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        try {
            $this->configureTenantDatabase($user['database_name']);
            
            // Vérifier le mot de passe actuel
            $currentUser = DB::connection('tenant')
                ->table('users')
                ->where('id', $user['id'])
                ->first();

            if (!$currentUser || !Hash::check($request->current_password, $currentUser->password_hash)) {
                return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
            }

            // Mettre à jour le mot de passe
            DB::connection('tenant')
                ->table('users')
                ->where('id', $user['id'])
                ->update([
                    'password_hash' => Hash::make($request->password),
                    'updated_at' => now(),
                ]);

            return back()->with('success', 'Mot de passe mis à jour avec succès.');
            
        } catch (\Exception $e) {
            Log::error('Password update failed', [
                'user_id' => $user['id'],
                'error' => $e->getMessage()
            ]);
            
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour du mot de passe.']);
        }
    }
}