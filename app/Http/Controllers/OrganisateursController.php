<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\OrganizationCreationService;
use App\Models\SubscriptionPack;
use App\Models\EventType;
use App\Models\SupportedCountry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OrganisateursController extends Controller
{
    protected $organizationCreationService;
    
    public function __construct(OrganizationCreationService $organizationCreationService)
    {
        $this->organizationCreationService = $organizationCreationService;
    }
    
    /**
     * Afficher la liste des organisateurs (pour l'admin)
     */
    public function index()
    {
        // Cette méthode sera utilisée par l'admin pour voir tous les organisateurs
        $organizations = \App\Models\Organization::with('users')->paginate(20);
        return view('admin.organisateurs.index', compact('organizations'));
    }
    
    /**
     * Afficher le formulaire d'inscription d'un organisateur
     */
    public function create()
    {
        $packs = SubscriptionPack::where('is_active', true)->orderBy('display_order')->get();
        $eventTypes = EventType::where('is_active', true)->orderBy('display_order')->get();
        $countries = SupportedCountry::where('is_active', true)->orderBy('display_order')->get();
        
        return view('organisateurs.inscription', compact('packs', 'eventTypes', 'countries'));
    }
    
    /**
     * Enregistrer un nouvel organisateur
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'org_name' => 'required|string|max:255',
            'org_key' => 'required|string|max:50|unique:organizations,org_key|regex:/^[a-z0-9_-]+$/',
            'org_type' => 'required|in:jci,rotary,lions,association,company,other',
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'subscription_pack' => 'required|exists:subscription_packs,pack_key',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'org_key.regex' => 'La clé d\'organisation ne peut contenir que des lettres minuscules, chiffres, tirets et underscores.',
            'org_key.unique' => 'Cette clé d\'organisation est déjà utilisée.',
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
        
        // Générer automatiquement le subdomain
        $subdomain = Str::slug($request->org_key);
        
        // Gérer l'upload du logo
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $this->handleLogoUpload($request->file('logo'), $request->org_key);
        } else {
            $logoPath = 'default-logo.png';
        }
        
        // Préparer les données
        $data = [
            'org_name' => $request->org_name,
            'org_key' => $request->org_key,
            'org_type' => $request->org_type,
            'contact_name' => $request->contact_name,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
            'subdomain' => $subdomain,
            'subscription_pack' => $request->subscription_pack,
            'organization_logo' => $logoPath,
            'enabled_countries' => ['CI'], // Par défaut Côte d'Ivoire
            'enabled_event_types' => null, // Tous les types par défaut
            'pack_settings' => null
        ];
        
        // Créer l'organisation
        $result = $this->organizationCreationService->createOrganization($data);
        
        if ($result['success']) {
            // Créer l'utilisateur propriétaire
            $this->createOwnerUser($result['organization'], $request);
            
            return response()->json([
                'success' => true,
                'message' => 'Organisation créée avec succès !',
                'organization' => $result['organization'],
                'registration_token' => $result['registration']->registration_token,
                'redirect_url' => route('organisateurs.show', $result['organization']->org_key)
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'organisation : ' . $result['error']
            ], 500);
        }
    }
    
    /**
     * Afficher les détails d'un organisateur
     */
    public function show($orgKey)
    {
        $organization = \App\Models\Organization::where('org_key', $orgKey)->firstOrFail();
        return view('organisateurs.profil', compact('organization'));
    }
    
    /**
     * Afficher le formulaire d'édition d'un organisateur
     */
    public function edit($orgKey)
    {
        $organization = \App\Models\Organization::where('org_key', $orgKey)->firstOrFail();
        $packs = SubscriptionPack::where('is_active', true)->orderBy('display_order')->get();
        
        return view('organisateurs.modifier', compact('organization', 'packs'));
    }
    
    /**
     * Mettre à jour un organisateur
     */
    public function update(Request $request, $orgKey)
    {
        $organization = \App\Models\Organization::where('org_key', $orgKey)->firstOrFail();
        
        $validator = Validator::make($request->all(), [
            'org_name' => 'required|string|max:255',
            'org_type' => 'required|in:jci,rotary,lions,association,company,other',
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        // Gérer l'upload du logo si fourni
        if ($request->hasFile('logo')) {
            $logoPath = $this->handleLogoUpload($request->file('logo'), $organization->org_key);
            $organization->organization_logo = $logoPath;
        }
        
        $organization->update($request->only([
            'org_name', 'org_type', 'contact_name', 'contact_email', 'contact_phone'
        ]));
        
        return redirect()->route('organisateurs.show', $organization->org_key)
            ->with('success', 'Organisation mise à jour avec succès !');
    }
    
    /**
     * Supprimer un organisateur
     */
    public function destroy($orgKey)
    {
        $organization = \App\Models\Organization::where('org_key', $orgKey)->firstOrFail();
        
        // Supprimer l'organisation et sa base de données
        $this->organizationCreationService->deleteOrganization($organization);
        
        return redirect()->route('admin.organisateurs.index')
            ->with('success', 'Organisation supprimée avec succès !');
    }
    
    /**
     * Vérifier la disponibilité d'une clé d'organisation
     */
    public function verifierCle(Request $request)
    {
        $orgKey = $request->input('org_key');
        
        if (!$orgKey) {
            return response()->json(['available' => false, 'message' => 'Clé requise']);
        }
        
        $exists = \App\Models\Organization::where('org_key', $orgKey)->exists();
        
        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Cette clé est déjà utilisée' : 'Cette clé est disponible'
        ]);
    }
    
    /**
     * Vérifier la disponibilité d'un sous-domaine
     */
    public function verifierSousDomaine(Request $request)
    {
        $subdomain = $request->input('subdomain');
        
        if (!$subdomain) {
            return response()->json(['available' => false, 'message' => 'Sous-domaine requis']);
        }
        
        $exists = \App\Models\Organization::where('subdomain', $subdomain)->exists();
        
        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Ce sous-domaine est déjà utilisé' : 'Ce sous-domaine est disponible'
        ]);
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
        \App\Models\SaasUser::create([
            'organization_id' => $organization->id,
            'name' => $request->contact_name,
            'email' => $request->contact_email,
            'phone' => $request->contact_phone,
            'password' => bcrypt($request->password),
            'role' => 'owner',
            'is_active' => true,
            'email_verified_at' => now()
        ]);
    }
}

