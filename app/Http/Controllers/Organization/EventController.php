<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EventController extends Controller
{
    /**
     * Afficher la liste des événements
     */
    public function index(Request $request)
    {
        Log::info('=== EVENT CONTROLLER INDEX START ===', [
            'url' => $request->fullUrl(),
            'route' => $request->route()?->getName(),
            'org_slug' => $request->route('org_slug')
        ]);

        $user = session('organization_user');
        $orgSlug = $request->route('org_slug');

        if (!$user) {
            Log::info('User not authenticated in EventController::index');
            return redirect()->route('org.login', ['org_slug' => $orgSlug]);
        }

        try {
            // Récupérer l'organisation
            $organization = DB::connection('saas_master')
                ->table('organizations')
                ->where('id', $user['org_id'])
                ->select('id', 'org_name', 'subdomain', 'organization_logo', 'database_name')
                ->first();

            if (!$organization) {
                session()->forget('organization_user');
                return redirect()->route('saas.home')
                    ->withErrors(['organization' => 'Organisation non trouvée.']);
            }

            // Récupérer les événements avec filtres (sans pagination pour la vue initiale)
            $query = DB::connection('tenant')->table('events');

            // Filtre par statut de publication
            $status = $request->get('status');
            if ($status === 'published') {
                $query->where('is_published', true);
            } elseif ($status === 'draft') {
                $query->where('is_published', false);
            }

            // Filtre par recherche
            $search = $request->get('search');
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('event_title', 'like', "%{$search}%")
                      ->orWhere('event_description', 'like', "%{$search}%")
                      ->orWhere('event_location', 'like', "%{$search}%");
                });
            }

            // Filtre par date ou période
            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');
            if ($dateFrom) {
                $query->where('event_date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->where('event_date', '<=', $dateTo);
            }

            // Tri
            $sort = $request->get('sort', 'event_date');
            $order = $request->get('order', 'desc');
            $query->orderBy($sort, $order);

            // Pagination
            $events = $query->paginate(15);

            // Générer les slugs manquants pour les événements existants
            // Vérifier d'abord si la colonne event_slug existe
            try {
                $hasEventSlugColumn = DB::connection('tenant')
                    ->getSchemaBuilder()
                    ->hasColumn('events', 'event_slug');
            } catch (\Exception $e) {
                $hasEventSlugColumn = false;
            }

            if ($hasEventSlugColumn) {
                foreach ($events->items() as $event) {
                    if (empty($event->event_slug)) {
                        $baseSlug = Str::slug($event->event_title);
                        $eventSlug = $baseSlug;
                        $counter = 1;

                        // Vérifier l'unicité du slug dans cette organisation (tenant)
                        while (DB::connection('tenant')->table('events')->where('event_slug', $eventSlug)->where('id', '!=', $event->id)->exists()) {
                            $eventSlug = $baseSlug . '-' . $counter;
                            $counter++;
                        }

                        // Mettre à jour le slug dans la base de données
                        DB::connection('tenant')
                            ->table('events')
                            ->where('id', $event->id)
                            ->update(['event_slug' => $eventSlug]);

                        // Mettre à jour l'objet en mémoire
                        $event->event_slug = $eventSlug;
                    }
                }
            }

            // Statistiques
            $stats = [
                'total' => DB::connection('tenant')->table('events')->count(),
                'published' => DB::connection('tenant')->table('events')->where('is_published', true)->count(),
                'draft' => DB::connection('tenant')->table('events')->where('is_published', false)->count(),
                'upcoming' => DB::connection('tenant')->table('events')
                    ->where('is_published', true)
                    ->where('event_date', '>=', now()->toDateString())
                    ->count(),
            ];

            return view('organization.events.index', compact(
                'user',
                'orgSlug',
                'organization',
                'events',
                'stats',
                'status',
                'search',
                'sort',
                'order',
                'dateFrom',
                'dateTo'
            ));

        } catch (\Exception $e) {
            Log::error('Error loading events list', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('org.dashboard', ['org_slug' => $orgSlug])
                ->with('error', 'Erreur lors du chargement des événements.');
        }
    }

    /**
     * Afficher le formulaire de création d'événement
     */
    public function create(Request $request)
    {
        $user = session('organization_user');
        $orgSlug = $request->route('org_slug');

        if (!$user) {
            return redirect()->route('org.login', ['org_slug' => $orgSlug]);
        }

        try {
            // Récupérer l'organisation
            $organization = DB::connection('saas_master')
                ->table('organizations')
                ->where('id', $user['org_id'])
                ->select('id', 'org_name', 'subdomain', 'organization_logo', 'database_name')
                ->first();

            if (!$organization) {
                session()->forget('organization_user');
                return redirect()->route('saas.home')
                    ->withErrors(['organization' => 'Organisation non trouvée.']);
            }

            // Récupérer les types d'événements
            $eventTypes = DB::connection('tenant')
                ->table('event_types')
                ->where('is_active', true)
                ->orderBy('display_order')
                ->orderBy('type_name')
                ->get();

            return view('organization.events.create', compact(
                'user',
                'orgSlug',
                'organization',
                'eventTypes'
            ));

        } catch (\Exception $e) {
            Log::error('Error loading create event form', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('org.events.index', ['org_slug' => $orgSlug])
                ->with('error', 'Erreur lors du chargement du formulaire.');
        }
    }

    /**
     * Recherche AJAX des événements
     */
    public function search(Request $request)
    {
        $user = session('organization_user');
        $orgSlug = $request->route('org_slug');

        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        try {
            // Récupérer les événements avec filtres
            $query = DB::connection('tenant')->table('events')
                ->select('id', 'event_title', 'event_description', 'event_date', 'event_location',
                         'max_participants', 'current_participants', 'is_published', 'created_at', 'event_slug');

            // Filtre par statut de publication
            $status = $request->get('status');
            if ($status === 'published') {
                $query->where('is_published', true);
            } elseif ($status === 'draft') {
                $query->where('is_published', false);
            }

            // Filtre par recherche
            $search = $request->get('search');
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('event_title', 'like', "%{$search}%")
                      ->orWhere('event_description', 'like', "%{$search}%")
                      ->orWhere('event_location', 'like', "%{$search}%");
                });
            }

            // Filtre par date ou période
            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');
            if ($dateFrom) {
                $query->where('event_date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->where('event_date', '<=', $dateTo);
            }

            // Tri
            $sort = $request->get('sort', 'event_date');
            $order = $request->get('order', 'desc');
            $query->orderBy($sort, $order);

            // Pagination
            $events = $query->paginate(15);

            // Formater les événements pour la réponse
            $formattedEvents = $events->map(function ($event) {
                $eventDate = $event->event_date ? Carbon::parse($event->event_date) : null;
                $isPast = $eventDate && $eventDate->isPast();
                $isUpcoming = $eventDate && $eventDate->isFuture();

                // Vérifier s'il y a des registrations
                $hasRegistrations = DB::connection('tenant')
                    ->table('registrations')
                    ->where('event_id', $event->id)
                    ->exists();

                return [
                    'id' => $event->id,
                    'event_title' => $event->event_title,
                    'event_description' => $event->event_description,
                    'event_date' => $eventDate ? $eventDate->format('d/m/Y') : null,
                    'event_date_raw' => $event->event_date,
                    'event_location' => $event->event_location,
                    'max_participants' => $event->max_participants,
                    'current_participants' => $event->current_participants ?? 0,
                    'is_published' => $event->is_published,
                    'is_past' => $isPast,
                    'is_upcoming' => $isUpcoming,
                    'has_registrations' => $hasRegistrations,
                    'event_slug' => $event->event_slug ?? null,
                ];
            });

            return response()->json([
                'success' => true,
                'events' => $formattedEvents->values()->toArray(),
                'pagination' => [
                    'current_page' => $events->currentPage(),
                    'last_page' => $events->lastPage(),
                    'per_page' => $events->perPage(),
                    'total' => $events->total(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error searching events', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la recherche des événements.'
            ], 500);
        }
    }

    /**
     * Enregistrer un nouvel événement
     */
    /**
 * Enregistrer un nouvel événement
 */
    public function store(Request $request)
    {
        $user = session('organization_user');
        $orgSlug = $request->route('org_slug');

        // Log de la requête complète
        Log::info('Request data', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'all' => $request->all(),
            'headers' => $request->headers->all(),
            'files' => array_map(function($file) {
                return [
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType()
                ];
            }, $request->allFiles()),
        ]);

        Log::info('=== DÉBUT CRÉATION ÉVÉNEMENT ===', [
            'org_slug' => $orgSlug,
            'user_id' => $user['id'] ?? null,
            'org_id' => $user['org_id'] ?? null,
        ]);

        if (!$user) {
            Log::warning('Tentative de création sans authentification', ['org_slug' => $orgSlug]);
            return redirect()->route('org.login', ['org_slug' => $orgSlug]);
        }

        try {
            // Log des données reçues
            Log::info('Données de la requête reçues', [
                'pack_type' => $request->pack_type,
                'event_title' => $request->event_title,
                'event_date' => $request->event_date,
                'has_ticket_standard' => $request->hasFile('ticket_template_standard'),
                'has_ticket_premium' => $request->hasFile('ticket_template_premium'),
                'has_ticket_custom' => $request->hasFile('ticket_template_custom'),
                'all_input_keys' => array_keys($request->all()),
            ]);

            // Préparer les données pour la validation
            $input = $request->all();
            if (isset($input['event_type_id']) && $input['event_type_id'] === '') {
                $input['event_type_id'] = null;
                Log::info('event_type_id converti de chaîne vide à null');
            }

            Log::info('Démarrage de la validation');

            // Validation avec les données modifiées
            $validator = Validator::make($input, [
                'event_title' => 'required|string|max:255',
                'event_description' => 'nullable|string',
                'event_type_id' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        if ($value) {
                            $exists = DB::connection('tenant')
                                ->table('event_types')
                                ->where('id', $value)
                                ->where('is_active', true)
                                ->exists();

                            if (!$exists) {
                                // Log pour debugging
                                $availableTypes = DB::connection('tenant')
                                    ->table('event_types')
                                    ->where('is_active', true)
                                    ->pluck('id', 'type_name')
                                    ->toArray();

                                Log::warning('Event type validation failed', [
                                    'provided_id' => $value,
                                    'available_types' => $availableTypes,
                                ]);

                                $fail('Le champ event type id sélectionné est invalide.');
                            }
                        }
                    },
                ],
                'event_date' => 'required|date|after_or_equal:today',
                'event_start_time' => 'required|date_format:H:i',
                'event_end_time' => [
                    'nullable',
                    'date_format:H:i',
                    function ($attribute, $value, $fail) use ($request) {
                        if ($value && $request->event_start_time) {
                            if (strtotime($value) <= strtotime($request->event_start_time)) {
                                $fail('L\'heure de fin doit être supérieure à l\'heure de début.');
                            }
                        }
                    },
                ],
                'event_location' => 'required|string|max:255',
                'event_address' => 'nullable|string|max:500',
                'event_price' => 'nullable|numeric|min:0',
                'currency' => 'nullable|string|size:3',
                'max_participants' => 'required|integer|min:1',
                'requires_payment' => 'nullable|boolean',
                'registration_open' => 'nullable|boolean',
                'registration_start_date' => 'nullable|date',
                'registration_end_date' => [
                    'nullable',
                    'date',
                    function ($attribute, $value, $fail) use ($request) {
                        if ($value && $request->registration_start_date) {
                            if (strtotime($value) < strtotime($request->registration_start_date)) {
                                $fail('La date de fin des inscriptions doit être supérieure ou égale à la date de début.');
                            }
                        }
                    },
                ],
                'is_published' => 'nullable|boolean',
                'pack_type' => 'required|in:standard,premium,custom',
                'ticket_template_standard' => [
                    'nullable',
                    'file',
                    'mimes:jpg,jpeg,png,pdf',
                    'max:5120',
                    function ($attribute, $value, $fail) use ($request) {
                        if ($request->pack_type === 'standard' && !$request->hasFile('ticket_template_standard')) {
                            $fail('Le fichier du template de ticket est requis pour le pack Standard.');
                        }
                    },
                ],
                'ticket_template_premium' => [
                    'nullable',
                    'file',
                    'mimes:jpg,jpeg,png,pdf',
                    'max:10240',
                    function ($attribute, $value, $fail) use ($request) {
                        if ($request->pack_type === 'premium' && !$request->hasFile('ticket_template_premium')) {
                            $fail('Le fichier du template de ticket est requis pour le pack Premium.');
                        }
                    },
                ],
                'ticket_template_custom' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
                'ticket_elements' => 'required_if:pack_type,standard|nullable|array',
                'ticket_elements.*' => 'in:qr_code,ticket_id,seat,date_time,name,function',
                'show_name_premium' => 'nullable|boolean',
                'show_function_premium' => 'nullable|boolean',
                // Coordonnées Premium
                'premium_qr_code_x' => 'nullable|integer',
                'premium_qr_code_y' => 'nullable|integer',
                'premium_qr_code_width' => 'nullable|integer',
                'premium_qr_code_height' => 'nullable|integer',
                'premium_qr_code_color' => 'nullable|string',
                'premium_name_x' => 'nullable|integer',
                'premium_name_y' => 'nullable|integer',
                'premium_name_width' => 'nullable|integer',
                'premium_name_height' => 'nullable|integer',
                'premium_name_color' => 'nullable|string',
                'premium_function_x' => 'nullable|integer',
                'premium_function_y' => 'nullable|integer',
                'premium_function_width' => 'nullable|integer',
                'premium_function_height' => 'nullable|integer',
                'premium_function_color' => 'nullable|string',
            ]);

            // Vérifier les erreurs de validation
            if ($validator->fails()) {
                Log::warning('Validation échouée', [
                    'errors' => $validator->errors()->toArray()
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Veuillez corriger les erreurs du formulaire.',
                        'errors' => $validator->errors()
                    ], 422);
                }
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            Log::info('Validation réussie');

            // Récupérer les données validées
            $validated = $validator->validated();

            // Récupérer l'organisation pour le chemin de stockage
            Log::info('Récupération de l\'organisation');

            $organization = DB::connection('saas_master')
                ->table('organizations')
                ->where('id', $user['org_id'])
                ->select('id', 'database_name', 'org_key')
                ->first();

            if (!$organization) {
                Log::error('Organisation non trouvée', ['org_id' => $user['org_id']]);
                return redirect()->back()
                    ->with('error', 'Organisation non trouvée.')
                    ->withInput();
            }

            Log::info('Organisation trouvée', [
                'org_id' => $organization->id,
                'database_name' => $organization->database_name,
                'org_key' => $organization->org_key
            ]);

            // Générer le slug de l'événement AVANT le stockage du template
            $baseSlug = Str::slug($validated['event_title']);
            $eventSlug = $baseSlug;
            $counter = 1;

            // Vérifier si la colonne event_slug existe
            try {
                $hasEventSlugColumn = DB::connection('tenant')
                    ->getSchemaBuilder()
                    ->hasColumn('events', 'event_slug');
            } catch (\Exception $e) {
                $hasEventSlugColumn = false;
            }

            // Vérifier l'unicité du slug dans cette organisation (tenant) seulement si la colonne existe
            if ($hasEventSlugColumn) {
                while (DB::connection('tenant')->table('events')->where('event_slug', $eventSlug)->exists()) {
                    Log::info('Slug déjà existant pour cette organisation, génération d\'un nouveau slug', [
                        'base_slug' => $baseSlug,
                        'tentative_slug' => $eventSlug,
                        'nouveau_slug' => $baseSlug . '-' . $counter
                    ]);
                    $eventSlug = $baseSlug . '-' . $counter;
                    $counter++;
                }
            }

            Log::info('Slug généré pour nouvel événement', [
                'event_title' => $validated['event_title'],
                'base_slug' => $baseSlug,
                'final_slug' => $eventSlug,
                'has_column' => $hasEventSlugColumn ?? false
            ]);

            // Gérer l'upload des fichiers de tickets
            $ticketTemplatePath = null;

            Log::info('Traitement du fichier de ticket', ['pack_type' => $request->pack_type]);

            // Vérifier que le fichier est requis et présent selon le pack type
            if ($request->pack_type === 'standard') {
                if (!$request->hasFile('ticket_template_standard')) {
                    Log::error('Fichier standard manquant');
                    return redirect()->back()
                        ->withErrors(['ticket_template_standard' => 'Le fichier du template de ticket est requis pour le pack Standard.'])
                        ->withInput();
                }
                $file = $request->file('ticket_template_standard');
                Log::info('Fichier standard trouvé', [
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType()
                ]);

                // Créer le répertoire s'il n'existe pas
                $directory = public_path('organizations/' . $organization->org_key . '/events/' . $eventSlug . '/template');
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                // Nom du fichier : template.png (ou l'extension originale)
                $fileName = 'template.' . $file->getClientOriginalExtension();
                $fullPath = $directory . '/' . $fileName;

                // Déplacer le fichier
                $file->move($directory, $fileName);

                // Chemin relatif pour la base de données
                $ticketTemplatePath = 'organizations/' . $organization->org_key . '/events/' . $eventSlug . '/template/' . $fileName;

                Log::info('Fichier standard sauvegardé', [
                    'path' => $ticketTemplatePath,
                    'filename' => $fileName,
                    'full_path' => $fullPath
                ]);
            } elseif ($request->pack_type === 'premium') {
                if (!$request->hasFile('ticket_template_premium')) {
                    Log::error('Fichier premium manquant');
                    return redirect()->back()
                        ->withErrors(['ticket_template_premium' => 'Le fichier du template de ticket est requis pour le pack Premium.'])
                        ->withInput();
                }
                $file = $request->file('ticket_template_premium');
                Log::info('Fichier premium trouvé', [
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType()
                ]);

                // Créer le répertoire s'il n'existe pas
                $directory = public_path('organizations/' . $organization->org_key . '/events/' . $eventSlug . '/template');
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                // Nom du fichier : template.png (ou l'extension originale)
                $fileName = 'template.' . $file->getClientOriginalExtension();
                $fullPath = $directory . '/' . $fileName;

                // Déplacer le fichier
                $file->move($directory, $fileName);

                // Chemin relatif pour la base de données
                $ticketTemplatePath = 'organizations/' . $organization->org_key . '/events/' . $eventSlug . '/template/' . $fileName;

                Log::info('Fichier premium sauvegardé', [
                    'path' => $ticketTemplatePath,
                    'filename' => $fileName,
                    'full_path' => $fullPath
                ]);
            } elseif ($request->pack_type === 'custom' && $request->hasFile('ticket_template_custom')) {
                $file = $request->file('ticket_template_custom');
                Log::info('Fichier custom trouvé', [
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType()
                ]);

                // Créer le répertoire s'il n'existe pas
                $directory = public_path('organizations/' . $organization->org_key . '/events/' . $eventSlug . '/template');
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                // Nom du fichier : template.png (ou l'extension originale)
                $fileName = 'template.' . $file->getClientOriginalExtension();
                $fullPath = $directory . '/' . $fileName;

                // Déplacer le fichier
                $file->move($directory, $fileName);

                // Chemin relatif pour la base de données
                $ticketTemplatePath = 'organizations/' . $organization->org_key . '/events/' . $eventSlug . '/template/' . $fileName;

                Log::info('Fichier custom sauvegardé', [
                    'path' => $ticketTemplatePath,
                    'filename' => $fileName,
                    'full_path' => $fullPath
                ]);
            }

            // Préparer la personnalisation du ticket
            Log::info('Préparation de la personnalisation du ticket');

            $ticketCustomization = [];
            if ($request->pack_type === 'standard') {
                $ticketCustomization = [
                    'pack_type' => 'standard',
                    'template_path' => $ticketTemplatePath,
                    'elements' => $request->ticket_elements ?? [],
                ];

                Log::info('Personnalisation standard créée', $ticketCustomization);
            } elseif ($request->pack_type === 'premium') {
                // Récupérer les éléments sélectionnés
                $premiumElements = $request->input('premium_elements', []);
                
                // S'assurer que qr_code et ticket_id sont toujours présents (obligatoires)
                if (!in_array('qr_code', $premiumElements)) {
                    $premiumElements[] = 'qr_code';
                }
                if (!in_array('ticket_id', $premiumElements)) {
                    $premiumElements[] = 'ticket_id';
                }
                
                $ticketCustomization = [
                    'pack_type' => 'premium',
                    'template_path' => $ticketTemplatePath,
                    'elements' => $premiumElements, // QR Code, Ticket ID, Seat, Ticket Type, Amount
                ];

                Log::info('Personnalisation premium créée', $ticketCustomization);
            } elseif ($request->pack_type === 'custom') {
                $ticketCustomization = [
                    'pack_type' => 'custom',
                    'template_path' => $ticketTemplatePath,
                    'notes' => 'Configuration sur mesure - Contactez notre équipe',
                ];
                Log::info('Personnalisation custom créée', $ticketCustomization);
            }

            // Préparer les données
            Log::info('Préparation des données pour l\'insertion');

            $data = [
                'event_title' => $validated['event_title'],
                'event_description' => $validated['event_description'] ?? null,
                'event_type_id' => $validated['event_type_id'] ?? null,
                'pack_type' => $validated['pack_type'],
                'event_date' => $validated['event_date'],
                'event_start_time' => $validated['event_start_time'],
                'event_end_time' => $validated['event_end_time'] ?? null,
                'event_location' => $validated['event_location'],
                'event_address' => $validated['event_address'] ?? null,
                'max_participants' => $validated['max_participants'],
                'current_participants' => 0,
                'requires_payment' => $request->has('requires_payment') ? (bool)$request->requires_payment : true,
                'event_price' => $request->has('requires_payment') && $request->requires_payment && !$request->has('use_multiple_tickets')
                    ? ($validated['event_price'] ?? 0)
                    : 0,
                'currency' => $request->has('requires_payment') && $request->requires_payment
                    ? ($validated['currency'] ?? 'XOF')
                    : 'XOF',
                'use_multiple_tickets' => $request->has('use_multiple_tickets') ? (bool)$request->use_multiple_tickets : false,
                'allow_partial_payment' => $request->has('allow_partial_payment') ? (bool)$request->allow_partial_payment : false,
                'partial_payment_amount' => $request->has('allow_partial_payment') && $request->allow_partial_payment
                    ? ($request->input('partial_payment_amount') ?? null)
                    : null,
                'allow_reservation' => $request->has('allow_reservation') ? (bool)$request->allow_reservation : false,
                'reservation_amount' => $request->has('allow_reservation') && $request->allow_reservation
                    ? ($request->input('reservation_amount') ?? null)
                    : null,
                'reservation_terms' => $request->has('allow_reservation') && $request->allow_reservation
                    ? ($request->input('reservation_terms') ?? null)
                    : null,
                'registration_open' => $request->has('registration_open') ? (bool)$request->registration_open : true,
                'registration_start_date' => $validated['registration_start_date']
                    ? Carbon::parse($validated['registration_start_date'])->format('Y-m-d H:i:s')
                    : null,
                'registration_end_date' => $validated['registration_end_date']
                    ? Carbon::parse($validated['registration_end_date'])->format('Y-m-d H:i:s')
                    : null,
                'is_published' => $request->has('is_published') ? (bool)$request->is_published : false,
                'ticket_customization' => json_encode($ticketCustomization),
                'payment_status' => 'en attente',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Ajouter event_slug seulement si la colonne existe
            if ($hasEventSlugColumn) {
                $data['event_slug'] = $eventSlug;
            }

            Log::info('Données préparées pour insertion', [
                'event_title' => $data['event_title'],
                'pack_type' => $data['pack_type'],
                'event_date' => $data['event_date'],
                'max_participants' => $data['max_participants'],
                'is_published' => $data['is_published'],
                'ticket_customization_length' => strlen($data['ticket_customization'])
            ]);

            // Enregistrer dans la base de données tenant
            Log::info('Insertion dans la base de données tenant');

            $eventId = DB::connection('tenant')->table('events')->insertGetId($data);

            // Créer les tarifs multiples si activé
            if ($request->has('use_multiple_tickets') && $request->use_multiple_tickets && $request->has('tickets')) {
                $tickets = $request->input('tickets', []);
                $displayOrder = 0;
                foreach ($tickets as $ticketData) {
                    if (!empty($ticketData['ticket_name']) && isset($ticketData['ticket_price'])) {
                        DB::connection('tenant')->table('event_tickets')->insert([
                            'event_id' => $eventId,
                            'ticket_name' => $ticketData['ticket_name'],
                            'ticket_description' => $ticketData['ticket_description'] ?? null,
                            'ticket_price' => $ticketData['ticket_price'],
                            'currency' => $validated['currency'] ?? 'XOF',
                            'quantity_available' => !empty($ticketData['quantity_available']) ? (int)$ticketData['quantity_available'] : null,
                            'quantity_sold' => 0,
                            'is_active' => true,
                            'display_order' => $displayOrder++,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            // Créer les champs personnalisés du formulaire
            if ($request->has('form_fields')) {
                $formFields = $request->input('form_fields', []);
                $displayOrder = 0;
                foreach ($formFields as $fieldKey => $fieldData) {
                    if (!empty($fieldData['enabled'])) {
                        DB::connection('tenant')->table('event_form_fields')->insert([
                            'event_id' => $eventId,
                            'field_key' => $fieldKey,
                            'field_label' => $fieldData['label'] ?? ucfirst(str_replace('_', ' ', $fieldKey)),
                            'field_type' => $fieldData['type'] ?? 'text',
                            'is_required' => !empty($fieldData['required']),
                            'is_visible' => true,
                            'is_readonly' => false,
                            'section_name' => 'main',
                            'display_order' => $displayOrder++,
                            'field_width' => 'full',
                            'field_config' => json_encode([]),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            Log::info('✅ Événement créé avec succès !', [
                'event_id' => $eventId,
                'org_id' => $user['org_id'],
                'org_slug' => $orgSlug,
                'event_title' => $data['event_title'],
                'pack_type' => $data['pack_type']
            ]);

            // Si c'est une requête AJAX, retourner une réponse JSON
            if ($request->wantsJson() || $request->ajax()) {
                Log::info('Retour réponse JSON');
                return response()->json([
                    'success' => true,
                    'message' => 'Événement créé avec succès !',
                    'redirect' => route('org.events.index', ['org_slug' => $orgSlug])
                ]);
            }

            Log::info('Redirection vers la liste des événements');
            return redirect()->route('org.events.index', ['org_slug' => $orgSlug])
                ->with('success', 'Événement créé avec succès !');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('❌ Exception de validation', [
                'errors' => $e->errors(),
                'message' => $e->getMessage()
            ]);

            // Si c'est une requête AJAX, retourner les erreurs en JSON
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors(),
                    'message' => 'Veuillez corriger les erreurs du formulaire.'
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('❌ ERREUR CRITIQUE lors de la création de l\'événement', [
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'org_slug' => $orgSlug,
                'user_id' => $user['id'] ?? null,
                'org_id' => $user['org_id'] ?? null,
            ]);

            // Si c'est une requête AJAX, retourner l'erreur en JSON
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la création de l\'événement.',
                    'debug' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Erreur lors de la création de l\'événement.')
                ->withInput();
        } finally {
            Log::info('=== FIN CRÉATION ÉVÉNEMENT ===');
        }
    }

    /**
     * Afficher les détails d'un événement
     */
    public function show(Request $request, $orgSlug, $event)
    {
        $user = session('organization_user');

        Log::info('=== SHOW EVENT ===', [
            'event_id' => $event,
            'org_slug' => $orgSlug,
            'route_params' => $request->route()->parameters(),
            'url' => $request->fullUrl(),
        ]);

        if (!$user) {
            return redirect()->route('org.login', ['org_slug' => $orgSlug]);
        }

        try {
            // Récupérer l'organisation
            $organization = DB::connection('saas_master')
                ->table('organizations')
                ->where('id', $user['org_id'])
                ->select('id', 'org_name', 'subdomain', 'organization_logo', 'database_name')
                ->first();

            if (!$organization) {
                session()->forget('organization_user');
                return redirect()->route('saas.home')
                    ->withErrors(['organization' => 'Organisation non trouvée.']);
            }

            // Récupérer l'événement
            $eventData = DB::connection('tenant')
                ->table('events')
                ->where('id', $event)
                ->first();

            Log::info('Event lookup result', [
                'event_id' => $event,
                'event_found' => $eventData ? true : false,
            ]);

            if (!$eventData) {
                return redirect()->route('org.events.index', ['org_slug' => $orgSlug])
                    ->with('error', 'Événement non trouvé.');
            }

            // Compter les registrations
            $registrationsCount = DB::connection('tenant')
                ->table('registrations')
                ->where('event_id', $eventData->id)
                ->count();

            // Décoder les données de personnalisation du ticket
            $ticketCustomization = null;
            if ($eventData->ticket_customization) {
                $ticketCustomization = json_decode($eventData->ticket_customization, true);
            }

            // Générer le slug s'il n'existe pas
            // Le slug est unique au niveau de l'organisation (tenant)
            // Vérifier d'abord si la colonne event_slug existe
            try {
                $hasEventSlugColumn = DB::connection('tenant')
                    ->getSchemaBuilder()
                    ->hasColumn('events', 'event_slug');
            } catch (\Exception $e) {
                $hasEventSlugColumn = false;
            }

            if ($hasEventSlugColumn && empty($eventData->event_slug)) {
                $baseSlug = Str::slug($eventData->event_title);
                $eventSlug = $baseSlug;
                $counter = 1;

                // Vérifier l'unicité du slug dans cette organisation (tenant), en excluant l'événement actuel
                while (DB::connection('tenant')->table('events')->where('event_slug', $eventSlug)->where('id', '!=', $eventData->id)->exists()) {
                    Log::info('Slug déjà existant pour cette organisation, génération d\'un nouveau slug', [
                        'event_id' => $eventData->id,
                        'event_title' => $eventData->event_title,
                        'base_slug' => $baseSlug,
                        'tentative_slug' => $eventSlug,
                        'nouveau_slug' => $baseSlug . '-' . $counter
                    ]);
                    $eventSlug = $baseSlug . '-' . $counter;
                    $counter++;
                }

                // Mettre à jour le slug dans la base de données
                DB::connection('tenant')
                    ->table('events')
                    ->where('id', $eventData->id)
                    ->update(['event_slug' => $eventSlug]);

                // Mettre à jour l'objet
                $eventData->event_slug = $eventSlug;

                Log::info('Slug généré pour événement existant', [
                    'event_id' => $eventData->id,
                    'event_title' => $eventData->event_title,
                    'base_slug' => $baseSlug,
                    'final_slug' => $eventSlug
                ]);
            }

            $event = $eventData;

            return view('organization.events.show', compact(
                'user',
                'orgSlug',
                'organization',
                'event',
                'registrationsCount',
                'ticketCustomization'
            ));

        } catch (\Exception $e) {
            Log::error('Error loading event details', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('org.events.index', ['org_slug' => $orgSlug])
                ->with('error', 'Erreur lors du chargement de l\'événement.');
        }
    }

    /**
     * Afficher le formulaire de modification d'événement
     */
    public function edit(Request $request, $orgSlug, $event)
    {
        $user = session('organization_user');

        Log::info('=== EDIT EVENT ===', [
            'event_id' => $event,
            'org_slug' => $orgSlug,
            'route_params' => $request->route()->parameters(),
            'url' => $request->fullUrl(),
        ]);

        if (!$user) {
            return redirect()->route('org.login', ['org_slug' => $orgSlug]);
        }

        try {
            // Récupérer l'organisation
            $organization = DB::connection('saas_master')
                ->table('organizations')
                ->where('id', $user['org_id'])
                ->select('id', 'org_name', 'subdomain', 'organization_logo', 'database_name')
                ->first();

            if (!$organization) {
                session()->forget('organization_user');
                return redirect()->route('saas.home')
                    ->withErrors(['organization' => 'Organisation non trouvée.']);
            }

            // Récupérer l'événement
            $eventData = DB::connection('tenant')
                ->table('events')
                ->where('id', $event)
                ->first();

            Log::info('Event lookup result', [
                'event_id' => $event,
                'event_found' => $eventData ? true : false,
            ]);

            if (!$eventData) {
                return redirect()->route('org.events.index', ['org_slug' => $orgSlug])
                    ->with('error', 'Événement non trouvé.');
            }

            // Récupérer les types d'événements
            $eventTypes = DB::connection('tenant')
                ->table('event_types')
                ->where('is_active', true)
                ->orderBy('display_order')
                ->orderBy('type_name')
                ->get();

            // Décoder les données de personnalisation du ticket
            $ticketCustomization = null;
            if ($eventData->ticket_customization) {
                $ticketCustomization = json_decode($eventData->ticket_customization, true);
            }

            // Récupérer les tarifs multiples (event_tickets)
            $eventTickets = DB::connection('tenant')
                ->table('event_tickets')
                ->where('event_id', $event)
                ->where('is_active', true)
                ->orderBy('display_order')
                ->get();

            // Récupérer les champs personnalisés du formulaire
            $eventFormFields = DB::connection('tenant')
                ->table('event_form_fields')
                ->where('event_id', $event)
                ->where('is_visible', true)
                ->orderBy('display_order')
                ->get();

            $event = $eventData;

            return view('organization.events.edit', compact(
                'user',
                'orgSlug',
                'organization',
                'event',
                'eventTypes',
                'ticketCustomization',
                'eventTickets',
                'eventFormFields'
            ));

        } catch (\Exception $e) {
            Log::error('Error loading edit event form', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('org.events.index', ['org_slug' => $orgSlug])
                ->with('error', 'Erreur lors du chargement du formulaire.');
        }
    }

    /**
     * Mettre à jour un événement
     */
    public function update(Request $request, $orgSlug, $event)
    {
        $user = session('organization_user');

        if (!$user) {
            return redirect()->route('org.login', ['org_slug' => $orgSlug]);
        }

        try {
            // Vérifier que l'événement existe
            $existingEvent = DB::connection('tenant')
                ->table('events')
                ->where('id', $event)
                ->first();

            if (!$existingEvent) {
                return redirect()->route('org.events.index', ['org_slug' => $orgSlug])
                    ->with('error', 'Événement non trouvé.');
            }

            // Utiliser la même logique de validation que store()
            $input = $request->all();
            if (isset($input['event_type_id']) && $input['event_type_id'] === '') {
                $input['event_type_id'] = null;
            }

            // Validation similaire à store() mais adaptée pour l'update
            $validator = Validator::make($input, [
                'event_title' => 'required|string|max:255',
                'event_description' => 'nullable|string',
                'event_type_id' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        if ($value) {
                            $exists = DB::connection('tenant')
                                ->table('event_types')
                                ->where('id', $value)
                                ->where('is_active', true)
                                ->exists();

                            if (!$exists) {
                                $fail('Le champ event type id sélectionné est invalide.');
                            }
                        }
                    },
                ],
                'event_date' => 'required|date',
                'event_start_time' => 'required|date_format:H:i',
                'event_end_time' => [
                    'nullable',
                    'date_format:H:i',
                    function ($attribute, $value, $fail) use ($request) {
                        if ($value && $request->event_start_time) {
                            if (strtotime($value) <= strtotime($request->event_start_time)) {
                                $fail('L\'heure de fin doit être supérieure à l\'heure de début.');
                            }
                        }
                    },
                ],
                'event_location' => 'required|string|max:255',
                'event_address' => 'nullable|string|max:500',
                'event_price' => 'nullable|numeric|min:0',
                'currency' => 'nullable|string|size:3',
                'max_participants' => 'required|integer|min:1',
                'requires_payment' => 'nullable|boolean',
                'registration_open' => 'nullable|boolean',
                'registration_start_date' => 'nullable|date',
                'registration_end_date' => [
                    'nullable',
                    'date',
                    function ($attribute, $value, $fail) use ($request) {
                        if ($value && $request->registration_start_date) {
                            if (strtotime($value) < strtotime($request->registration_start_date)) {
                                $fail('La date de fin des inscriptions doit être supérieure ou égale à la date de début.');
                            }
                        }
                    },
                ],
                'is_published' => 'nullable|boolean',
                // Champs de personnalisation du ticket
                'pack_type' => 'nullable|string|in:standard,premium,custom',
                'ticket_elements' => 'nullable|array',
                'ticket_elements.*' => 'in:qr_code,ticket_id,seat,date_time,name,function',
                'show_name_premium' => 'nullable|boolean',
                'show_function_premium' => 'nullable|boolean',
                // Coordonnées Premium (seul le pack Premium a une personnalisation manuelle)
                'premium_qr_code_x' => 'nullable|integer',
                'premium_qr_code_y' => 'nullable|integer',
                'premium_qr_code_width' => 'nullable|integer',
                'premium_qr_code_height' => 'nullable|integer',
                'premium_qr_code_color' => 'nullable|string',
                'premium_name_x' => 'nullable|integer',
                'premium_name_y' => 'nullable|integer',
                'premium_name_width' => 'nullable|integer',
                'premium_name_height' => 'nullable|integer',
                'premium_name_color' => 'nullable|string',
                'premium_function_x' => 'nullable|integer',
                'premium_function_y' => 'nullable|integer',
                'premium_function_width' => 'nullable|integer',
                'premium_function_height' => 'nullable|integer',
                'premium_function_color' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Veuillez corriger les erreurs du formulaire.',
                        'errors' => $validator->errors()
                    ], 422);
                }
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $validated = $validator->validated();

            // Récupérer l'organisation
            $organization = DB::connection('saas_master')
                ->table('organizations')
                ->where('id', $user['org_id'])
                ->select('id', 'database_name', 'org_key')
                ->first();

            if (!$organization) {
                return redirect()->back()
                    ->with('error', 'Organisation non trouvée.')
                    ->withInput();
            }

            // Récupérer ou générer l'eventSlug
            $eventSlug = $existingEvent->event_slug ?? null;
            if (!$eventSlug) {
                // Générer le slug si nécessaire
                $baseSlug = Str::slug($validated['event_title']);
                $eventSlug = $baseSlug;
                $counter = 1;

                try {
                    $hasEventSlugColumn = DB::connection('tenant')
                        ->getSchemaBuilder()
                        ->hasColumn('events', 'event_slug');

                    if ($hasEventSlugColumn) {
                        while (DB::connection('tenant')->table('events')->where('event_slug', $eventSlug)->where('id', '!=', $event)->exists()) {
                            $eventSlug = $baseSlug . '-' . $counter;
                            $counter++;
                        }
                    }
                } catch (\Exception $e) {
                    // Si la colonne n'existe pas, on continue sans slug
                }
            }

            // Gérer l'upload des fichiers de tickets si un nouveau fichier est fourni
            $ticketTemplatePath = null;
            $existingTicketCustomization = null;

            if ($existingEvent->ticket_customization) {
                $existingTicketCustomization = json_decode($existingEvent->ticket_customization, true);
                $ticketTemplatePath = $existingTicketCustomization['template_path'] ?? null;
            }

            // Vérifier si un nouveau fichier de ticket est uploadé
            if ($request->pack_type === 'standard' && $request->hasFile('ticket_template_standard')) {
                $file = $request->file('ticket_template_standard');

                // Créer le répertoire s'il n'existe pas
                $directory = public_path('organizations/' . $organization->org_key . '/events/' . $eventSlug . '/template');
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                // Nom du fichier : template.png (ou l'extension originale)
                $fileName = 'template.' . $file->getClientOriginalExtension();
                $fullPath = $directory . '/' . $fileName;

                // Déplacer le fichier
                $file->move($directory, $fileName);

                // Chemin relatif pour la base de données
                $ticketTemplatePath = 'organizations/' . $organization->org_key . '/events/' . $eventSlug . '/template/' . $fileName;
            } elseif ($request->pack_type === 'premium' && $request->hasFile('ticket_template_premium')) {
                $file = $request->file('ticket_template_premium');

                // Supprimer l'ancien fichier s'il existe
                if ($existingTicketCustomization && isset($existingTicketCustomization['template_path'])) {
                    $oldFilePath = public_path($existingTicketCustomization['template_path']);
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                        \Log::info('Ancien fichier premium supprimé', ['path' => $oldFilePath]);
                    }
                }

                // Créer le répertoire s'il n'existe pas
                $directory = public_path('organizations/' . $organization->org_key . '/events/' . $eventSlug . '/template');
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                // Nom du fichier : template.png (ou l'extension originale)
                $fileName = 'template.' . $file->getClientOriginalExtension();
                $fullPath = $directory . '/' . $fileName;

                // Déplacer le fichier
                $file->move($directory, $fileName);

                // Chemin relatif pour la base de données
                $ticketTemplatePath = 'organizations/' . $organization->org_key . '/events/' . $eventSlug . '/template/' . $fileName;
            } elseif ($request->pack_type === 'custom' && $request->hasFile('ticket_template_custom')) {
                $file = $request->file('ticket_template_custom');

                // Créer le répertoire s'il n'existe pas
                $directory = public_path('organizations/' . $organization->org_key . '/events/' . $eventSlug . '/template');
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                // Nom du fichier : template.png (ou l'extension originale)
                $fileName = 'template.' . $file->getClientOriginalExtension();
                $fullPath = $directory . '/' . $fileName;

                // Déplacer le fichier
                $file->move($directory, $fileName);

                // Chemin relatif pour la base de données
                $ticketTemplatePath = 'organizations/' . $organization->org_key . '/events/' . $eventSlug . '/template/' . $fileName;
            }

            // Mettre à jour la personnalisation du ticket si pack_type est fourni
            $ticketCustomization = null;
            $packType = $request->input('pack_type') ?? $existingTicketCustomization['pack_type'] ?? null;

            if ($packType) {
                if ($packType === 'standard') {
                    $ticketCustomization = [
                        'pack_type' => 'standard',
                        'template_path' => $ticketTemplatePath ?? $existingTicketCustomization['template_path'] ?? null,
                        'elements' => $request->ticket_elements ?? $existingTicketCustomization['elements'] ?? [],
                    ];
                } elseif ($packType === 'premium') {
                    // Récupérer les éléments sélectionnés
                    $premiumElements = $request->input('premium_elements', []);
                    
                    // Si aucun élément n'est fourni, utiliser les éléments existants ou par défaut
                    if (empty($premiumElements) && isset($existingTicketCustomization['elements'])) {
                        $premiumElements = $existingTicketCustomization['elements'];
                    }
                    
                    // S'assurer que ticket_id est toujours présent (obligatoire)
                    if (!in_array('ticket_id', $premiumElements)) {
                        $premiumElements[] = 'ticket_id';
                    }
                    
                    $ticketCustomization = [
                        'pack_type' => 'premium',
                        'template_path' => $ticketTemplatePath ?? $existingTicketCustomization['template_path'] ?? null,
                        'elements' => $premiumElements, // QR Code, Ticket ID, Seat, Ticket Type, Amount
                    ];
                    
                    \Log::info('💾 Valeurs premium sauvegardées dans update:', [
                        'elements' => $ticketCustomization['elements'],
                    ]);
                } elseif ($packType === 'custom') {
                    $ticketCustomization = [
                        'pack_type' => 'custom',
                        'template_path' => $ticketTemplatePath ?? $existingTicketCustomization['template_path'] ?? null,
                        'notes' => 'Configuration sur mesure - Contactez notre équipe',
                    ];
                }
            }

            // Générer ou mettre à jour le slug
            // Le slug est unique au niveau de l'organisation (tenant)
            // Vérifier d'abord si la colonne event_slug existe
            try {
                $hasEventSlugColumn = DB::connection('tenant')
                    ->getSchemaBuilder()
                    ->hasColumn('events', 'event_slug');
            } catch (\Exception $e) {
                $hasEventSlugColumn = false;
            }

            $eventSlug = null;
            if ($hasEventSlugColumn) {
                // Si le slug n'existe pas ou est vide, ou si le titre a changé, générer un nouveau slug
                if (empty($existingEvent->event_slug) || $existingEvent->event_title !== $validated['event_title']) {
                    $baseSlug = Str::slug($validated['event_title']);
                    $eventSlug = $baseSlug;
                    $counter = 1;

                    // Vérifier l'unicité du slug dans cette organisation (tenant), en excluant l'événement actuel
                    while (DB::connection('tenant')->table('events')->where('event_slug', $eventSlug)->where('id', '!=', $event)->exists()) {
                        Log::info('Slug déjà existant pour cette organisation, génération d\'un nouveau slug', [
                            'event_id' => $event,
                            'event_title' => $validated['event_title'],
                            'base_slug' => $baseSlug,
                            'tentative_slug' => $eventSlug,
                            'nouveau_slug' => $baseSlug . '-' . $counter
                        ]);
                        $eventSlug = $baseSlug . '-' . $counter;
                        $counter++;
                    }

                    Log::info('Slug généré/mis à jour pour événement', [
                        'event_id' => $event,
                        'event_title' => $validated['event_title'],
                        'base_slug' => $baseSlug,
                        'final_slug' => $eventSlug,
                        'ancien_slug' => $existingEvent->event_slug ?? 'null'
                    ]);
                } else {
                    // Conserver le slug existant
                    $eventSlug = $existingEvent->event_slug;
                }
            }

            // Préparer les données pour la mise à jour
            $data = [
                'event_title' => $validated['event_title'],
                'event_description' => $validated['event_description'] ?? null,
                'event_type_id' => $validated['event_type_id'] ?? null,
                'event_date' => $validated['event_date'],
                'event_start_time' => $validated['event_start_time'],
                'event_end_time' => $validated['event_end_time'] ?? null,
                'event_location' => $validated['event_location'],
                'event_address' => $validated['event_address'] ?? null,
                'max_participants' => $validated['max_participants'],
                'requires_payment' => $request->has('requires_payment') ? (bool)$request->requires_payment : true,
                'event_price' => $request->has('requires_payment') && $request->requires_payment && !$request->has('use_multiple_tickets')
                    ? ($validated['event_price'] ?? 0)
                    : 0,
                'currency' => $request->has('requires_payment') && $request->requires_payment
                    ? ($validated['currency'] ?? 'XOF')
                    : 'XOF',
                'use_multiple_tickets' => $request->has('use_multiple_tickets') ? (bool)$request->use_multiple_tickets : false,
                'allow_partial_payment' => $request->has('allow_partial_payment') ? (bool)$request->allow_partial_payment : false,
                'partial_payment_amount' => $request->has('allow_partial_payment') && $request->allow_partial_payment
                    ? ($request->input('partial_payment_amount') ?? null)
                    : null,
                'allow_reservation' => $request->has('allow_reservation') ? (bool)$request->allow_reservation : false,
                'reservation_amount' => $request->has('allow_reservation') && $request->allow_reservation
                    ? ($request->input('reservation_amount') ?? null)
                    : null,
                'reservation_terms' => $request->has('allow_reservation') && $request->allow_reservation
                    ? ($request->input('reservation_terms') ?? null)
                    : null,
                'registration_open' => $request->has('registration_open') ? (bool)$request->registration_open : true,
                'registration_start_date' => $validated['registration_start_date']
                    ? Carbon::parse($validated['registration_start_date'])->format('Y-m-d H:i:s')
                    : null,
                'registration_end_date' => $validated['registration_end_date']
                    ? Carbon::parse($validated['registration_end_date'])->format('Y-m-d H:i:s')
                    : null,
                'is_published' => $request->has('is_published') ? (bool)$request->is_published : false,
                'updated_at' => now(),
            ];

            // Ajouter event_slug seulement si la colonne existe
            if ($hasEventSlugColumn && $eventSlug !== null) {
                $data['event_slug'] = $eventSlug;
            }

            // Ajouter ticket_customization si disponible
            if ($ticketCustomization !== null) {
                $data['ticket_customization'] = json_encode($ticketCustomization);
                $data['pack_type'] = $packType;
            }

            // Mettre à jour l'événement
            DB::connection('tenant')
                ->table('events')
                ->where('id', $event)
                ->update($data);

            // Mettre à jour ou créer les tarifs multiples si activé
            if ($request->has('use_multiple_tickets') && $request->use_multiple_tickets && $request->has('tickets')) {
                // Supprimer les anciens tarifs
                DB::connection('tenant')->table('event_tickets')->where('event_id', $event)->delete();

                // Créer les nouveaux tarifs
                $tickets = $request->input('tickets', []);
                $displayOrder = 0;
                foreach ($tickets as $ticketData) {
                    if (!empty($ticketData['ticket_name']) && isset($ticketData['ticket_price'])) {
                        DB::connection('tenant')->table('event_tickets')->insert([
                            'event_id' => $event,
                            'ticket_name' => $ticketData['ticket_name'],
                            'ticket_description' => $ticketData['ticket_description'] ?? null,
                            'ticket_price' => $ticketData['ticket_price'],
                            'currency' => $validated['currency'] ?? 'XOF',
                            'quantity_available' => !empty($ticketData['quantity_available']) ? (int)$ticketData['quantity_available'] : null,
                            'is_active' => true,
                            'display_order' => $displayOrder++,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            } elseif (!$request->has('use_multiple_tickets') || !$request->use_multiple_tickets) {
                // Si on désactive les tarifs multiples, supprimer les tarifs existants
                DB::connection('tenant')->table('event_tickets')->where('event_id', $event)->delete();
            }

            // Mettre à jour ou créer les champs personnalisés du formulaire
            if ($request->has('form_fields')) {
                // Supprimer les anciens champs
                DB::connection('tenant')->table('event_form_fields')->where('event_id', $event)->delete();

                // Créer les nouveaux champs
                $formFields = $request->input('form_fields', []);
                $displayOrder = 0;
                foreach ($formFields as $fieldKey => $fieldData) {
                    if (!empty($fieldData['enabled'])) {
                        DB::connection('tenant')->table('event_form_fields')->insert([
                            'event_id' => $event,
                            'field_key' => $fieldKey,
                            'field_label' => $fieldData['label'] ?? ucfirst(str_replace('_', ' ', $fieldKey)),
                            'field_type' => $fieldData['type'] ?? 'text',
                            'is_required' => !empty($fieldData['required']),
                            'is_visible' => true,
                            'is_readonly' => false,
                            'section_name' => 'main',
                            'display_order' => $displayOrder++,
                            'field_width' => 'full',
                            'field_config' => json_encode([]),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            Log::info('Événement mis à jour avec succès', [
                'event_id' => $event,
                'org_slug' => $orgSlug,
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Événement mis à jour avec succès !',
                    'redirect' => route('org.events.index', ['org_slug' => $orgSlug])
                ]);
            }

            return redirect()->route('org.events.index', ['org_slug' => $orgSlug])
                ->with('success', 'Événement mis à jour avec succès !');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de l\'événement', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la mise à jour de l\'événement.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour de l\'événement.')
                ->withInput();
        }
    }

    /**
     * Supprimer un événement (seulement s'il n'a pas de registrations)
     */
    public function destroy(Request $request, $orgSlug, $event)
    {
        $user = session('organization_user');

        if (!$user) {
            return redirect()->route('org.login', ['org_slug' => $orgSlug]);
        }

        try {
            // Vérifier que l'événement existe
            $eventData = DB::connection('tenant')
                ->table('events')
                ->where('id', $event)
                ->first();

            if (!$eventData) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Événement non trouvé.'
                    ], 404);
                }
                return redirect()->route('org.events.index', ['org_slug' => $orgSlug])
                    ->with('error', 'Événement non trouvé.');
            }

            // Vérifier s'il y a des registrations
            $registrationsCount = DB::connection('tenant')
                ->table('registrations')
                ->where('event_id', $event)
                ->count();

            if ($registrationsCount > 0) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cet événement ne peut pas être supprimé car il contient ' . $registrationsCount . ' inscription(s).'
                    ], 422);
                }
                return redirect()->route('org.events.index', ['org_slug' => $orgSlug])
                    ->with('error', 'Cet événement ne peut pas être supprimé car il contient ' . $registrationsCount . ' inscription(s).');
            }

            // Supprimer l'événement
            DB::connection('tenant')
                ->table('events')
                ->where('id', $event)
                ->delete();

            Log::info('Événement supprimé avec succès', [
                'event_id' => $event,
                'org_slug' => $orgSlug,
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Événement supprimé avec succès !'
                ]);
            }

            return redirect()->route('org.events.index', ['org_slug' => $orgSlug])
                ->with('success', 'Événement supprimé avec succès !');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de l\'événement', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la suppression de l\'événement.'
                ], 500);
            }

            return redirect()->route('org.events.index', ['org_slug' => $orgSlug])
                ->with('error', 'Erreur lors de la suppression de l\'événement.');
        }
    }
}

