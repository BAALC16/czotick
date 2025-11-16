<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\OrganizationCreationService;
use App\Models\SubscriptionPack;
use App\Models\OrganizationType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class OrganizationRegistrationController extends Controller
{
    protected $organizationCreationService;
    
    public function __construct(OrganizationCreationService $organizationCreationService)
    {
        $this->organizationCreationService = $organizationCreationService;
    }
    
    /**
     * Afficher le formulaire d'inscription personnalisé
     */
    public function showCustomRegistrationForm()
    {
        $organizationTypes = OrganizationType::active()
            ->ordered()
            ->get();
        
        return view('organization.register-custom', [
            'organizationTypes' => $organizationTypes
        ]);
    }
    
    /**
     * Afficher la page de sélection d'organisation pour la connexion
     */
    public function showOrganizationSelector()
    {
        return view('organization.select-organization');
    }
    
    /**
     * Rediriger vers la page de connexion de l'organisation
     */
    public function redirectToLogin(Request $request)
    {
        $request->validate([
            'org_slug' => 'required|string|max:255'
        ]);
        
        $orgSlug = $request->org_slug;
        
        // Vérifier que l'organisation existe
        $organization = DB::connection('saas_master')
            ->table('organizations')
            ->where('subdomain', $orgSlug)
            ->orWhere('org_key', $orgSlug)
            ->first();
        
        if (!$organization) {
            return back()->withErrors([
                'org_slug' => 'Organisation non trouvée. Vérifiez votre identifiant d\'organisation.'
            ])->withInput();
        }
        
        // Utiliser le subdomain ou org_key
        $slug = $organization->subdomain ?? $organization->org_key;
        
        return redirect()->route('org.login', ['org_slug' => $slug]);
    }
    
    /**
     * Traiter l'inscription d'une organisation avec upload de logo
     */
    public function registerCustom(Request $request)
    {
        // Récupérer les codes de types valides depuis la BD
        $validOrgTypes = OrganizationType::active()->pluck('code')->toArray();
        
        $validator = Validator::make($request->all(), [
            'org_name' => 'required|string|max:255',
            'org_type' => ['required', 'in:' . implode(',', $validOrgTypes)],
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'logo.image' => 'Le fichier doit être une image.',
            'logo.mimes' => 'L\'image doit être au format JPEG, PNG, JPG ou GIF.',
            'logo.max' => 'L\'image ne doit pas dépasser 2MB.'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Générer automatiquement la clé d'organisation basée sur le nom
        $baseKey = Str::slug($request->org_name);
        $orgKey = $baseKey;
        $counter = 1;
        
        // Vérifier l'unicité et ajouter un suffixe si nécessaire
        while (DB::table('organizations')->where('org_key', $orgKey)->exists()) {
            $orgKey = $baseKey . '-' . $counter;
            $counter++;
        }
        
        // Générer automatiquement le subdomain
        $subdomain = $orgKey;
        
        // Gérer l'upload du logo
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $this->handleLogoUpload($request->file('logo'), $orgKey);
        } else {
            $logoPath = 'default-logo.png';
        }
        
        // Préparer les données
        $data = [
            'org_name' => $request->org_name,
            'org_key' => $orgKey,
            'org_type' => $request->org_type,
            'contact_name' => $request->contact_name,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
            'subdomain' => $subdomain,
            // Pas de souscription à un pack au moment de la création d'organisation
            'subscription_pack' => null,
            'organization_logo' => $logoPath,
            'enabled_countries' => null,
            'enabled_event_types' => null, // Tous les types par défaut
            'pack_settings' => null
        ];
        
        // Créer l'organisation
        $result = $this->organizationCreationService->createOrganization($data);
        
        if ($result['success']) {
            // Créer l'utilisateur propriétaire
            $this->createOwnerUser($result['organization'], $request);
            
            // Connecter automatiquement l'utilisateur après l'inscription
            $this->autoLoginUser($result['organization'], $request);
            
            return response()->json([
                'success' => true,
                'message' => 'Organisation créée avec succès !',
                'organization' => $result['organization'],
                'registration_token' => $result['registration_token'] ?? null,
                'redirect_url' => route('org.dashboard', ['org_slug' => $result['organization']->subdomain ?? $result['organization']->org_key])
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'organisation : ' . $result['error']
            ], 500);
        }
    }
    
    /**
     * Gérer l'upload du logo
     */
    private function handleLogoUpload($file, $orgKey)
    {
        // Créer le répertoire s'il n'existe pas
        $directory = public_path('organizations/' . $orgKey . '/logo');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        
        // Nom du fichier : logo.png
        $filename = 'logo.png';
        $fullPath = $directory . '/' . $filename;
        
        // Déplacer le fichier
        $file->move($directory, $filename);
        
        // Retourner le chemin relatif pour la base de données
        return 'organizations/' . $orgKey . '/logo/' . $filename;
    }
    
    /**
     * Créer l'utilisateur propriétaire de l'organisation
     */
    private function createOwnerUser($organization, $request)
    {
        // Configurer la connexion tenant
        $mysqlConfig = config('database.connections.mysql');
        config(['database.connections.tenant' => array_merge($mysqlConfig, [
            'database' => $organization->database_name
        ])]);
        
        // Purger la connexion pour forcer la reconnexion
        DB::purge('tenant');
        
        // Diviser le nom en prénom et nom
        $nameParts = explode(' ', $request->contact_name, 2);
        $firstName = $nameParts[0] ?? $request->contact_name;
        $lastName = $nameParts[1] ?? '';
        
        // Créer l'utilisateur dans la base tenant
        DB::connection('tenant')->table('users')->insert([
            'email' => $request->contact_email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'nom' => $lastName, // Support ancienne structure
            'prenoms' => $firstName, // Support ancienne structure
            'phone' => $request->contact_phone,
            'mobile' => $request->contact_phone, // Support ancienne structure
            'role' => 'owner',
            'is_active' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
    
    /**
     * Connecter automatiquement l'utilisateur après l'inscription
     */
    private function autoLoginUser($organization, $request)
    {
        try {
            // Configurer la connexion tenant
            $mysqlConfig = config('database.connections.mysql');
            config(['database.connections.tenant' => array_merge($mysqlConfig, [
                'database' => $organization->database_name
            ])]);
            DB::purge('tenant');
            
            // Récupérer l'utilisateur créé
            $user = DB::connection('tenant')
                ->table('users')
                ->where('email', $request->contact_email)
                ->first();
            
            if (!$user) {
                Log::warning('User not found for auto-login after registration', [
                    'email' => $request->contact_email,
                    'database' => $organization->database_name
                ]);
                return;
            }
            
            // Créer la session utilisateur (même logique que AuthController)
            $nameParts = explode(' ', $request->contact_name, 2);
            $firstName = $nameParts[0] ?? $request->contact_name;
            $lastName = $nameParts[1] ?? '';
            
            $sessionData = [
                'id' => $user->id,
                'email' => $user->email,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'full_name' => trim($firstName . ' ' . $lastName),
                'role' => $user->role ?? 'owner',
                'permissions' => json_decode($user->permissions ?? '{}', true),
                'phone' => $request->contact_phone,
                'org_id' => $organization->id,
                'org_name' => $organization->org_name,
                'org_subdomain' => $organization->subdomain ?? $organization->org_key,
                'database_name' => $organization->database_name,
                'logged_in_at' => now()->toDateTimeString(),
                'session_token' => Str::random(60),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ];
            
            // Stocker dans la session
            session(['organization_user' => $sessionData]);
            session()->save();
            
            Log::info('User auto-logged in after organization registration', [
                'user_id' => $user->id,
                'email' => $user->email,
                'org_id' => $organization->id,
                'org_subdomain' => $organization->subdomain ?? $organization->org_key
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to auto-login user after registration', [
                'email' => $request->contact_email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Ne pas bloquer l'inscription si l'auto-login échoue
        }
    }
    
}
