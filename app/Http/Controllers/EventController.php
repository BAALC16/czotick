<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\TicketType;
use App\Models\Registration;
use App\Models\PaymentTransaction;
use App\Helpers\TenantHelper;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class EventController extends Controller
{
    public function showRegistrationForm(Request $request)
    {
        $currentOrganization = TenantHelper::getCurrentOrganization();
        $currentEvent = TenantHelper::getCurrentEvent();

        if (!$currentOrganization || !$currentEvent) {
            abort(500, 'Contexte d\'organisation ou d\'événement manquant');
        }

        // Sauvegarder l'ID de l'événement pour le recharger dans le contexte tenant
        $eventId = $currentEvent->id;

        return TenantHelper::withTenantConnection(function() use ($eventId, $currentOrganization, $currentEvent) {
            // Utiliser l'événement déjà chargé depuis le middleware, mais recharger les relations
            // via DB direct pour éviter les problèmes de connexion
            try {
                // Vérifier si l'événement utilise plusieurs tarifs (event_tickets)
                $useMultipleTickets = $currentEvent->use_multiple_tickets ?? false;

                $ticketTypes = collect();

                if ($useMultipleTickets) {
                    // Charger les tarifs multiples depuis event_tickets
                    $ticketTypes = DB::connection('tenant')
                        ->table('event_tickets')
                        ->where('event_id', $eventId)
                        ->where('is_active', true)
                        ->orderBy('display_order')
                        ->get()
                        ->map(function($ticket) {
                            // Convertir event_tickets en format compatible avec ticketTypes
                            return (object)[
                                'id' => $ticket->id,
                                'ticket_name' => $ticket->ticket_name,
                                'ticket_description' => $ticket->ticket_description,
                                'price' => $ticket->ticket_price,
                                'currency' => $ticket->currency ?? 'XOF',
                                'is_active' => $ticket->is_active,
                                'display_order' => $ticket->display_order,
                                'quantity_available' => $ticket->quantity_available,
                                'quantity_sold' => $ticket->quantity_sold ?? 0,
                            ];
                        });
                } else {
                    // Charger les types de tickets depuis ticket_types (ancien système)
                    $ticketTypes = DB::connection('tenant')
                        ->table('ticket_types')
                        ->where('event_id', $eventId)
                        ->where('is_active', true)
                        ->orderBy('display_order')
                        ->get();

                    // Si aucun ticket n'existe, créer un ticket par défaut
                    if ($ticketTypes->isEmpty()) {
                        Log::info('Aucun ticket trouvé pour l\'événement, création d\'un ticket par défaut', [
                            'event_id' => $eventId
                        ]);

                        $defaultTicketId = DB::connection('tenant')->table('ticket_types')->insertGetId([
                            'event_id' => $eventId,
                            'ticket_name' => 'Entrée standard',
                            'ticket_description' => 'Ticket d\'entrée pour l\'événement',
                            'price' => $currentEvent->event_price ?? 0,
                            'currency' => $currentEvent->currency ?? 'XOF',
                            'is_active' => true,
                            'display_order' => 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        // Recharger les tickets avec le nouveau ticket par défaut
                        $ticketTypes = DB::connection('tenant')
                            ->table('ticket_types')
                            ->where('event_id', $eventId)
                            ->where('is_active', true)
                            ->orderBy('display_order')
                            ->get();

                        Log::info('Ticket par défaut créé', [
                            'ticket_id' => $defaultTicketId,
                            'event_id' => $eventId
                        ]);
                    }
                }

                // Attacher les types de tickets au modèle Event
                $currentEvent->setRelation('ticketTypes', $ticketTypes->map(function($ticket) {
                    $ticketType = new \App\Models\TicketType();
                    $ticketType->setConnection('tenant');
                    foreach ((array)$ticket as $key => $value) {
                        $ticketType->$key = $value;
                    }
                    $ticketType->exists = true;
                    return $ticketType;
                }));

                // Récupérer la structure du formulaire dynamique
                $formStructure = $this->getEventFormStructure($currentEvent->id);

                // Enrichir les données de l'événement
                $currentEvent = $this->enrichEventData($currentEvent);

                // Vérifier s'il y a une inscription partielle (paiement partiel ou réservation)
                $partialRegistration = null;
                $email = $request->query('email');
                $phone = $request->query('phone');
                
                if ($email || $phone) {
                    $query = Registration::on('tenant')
                        ->where('event_id', $eventId)
                        ->whereIn('payment_status', ['partial', 'reservation'])
                        ->where('status', '!=', 'cancelled');
                    
                    if ($email) {
                        $query->where(function($q) use ($email) {
                            $q->where('email', $email)
                              ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(form_data, '$.email')) = ?", [$email]);
                        });
                    }
                    
                    if ($phone) {
                        $query->orWhere(function($q) use ($phone) {
                            $q->where('phone', $phone)
                              ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(form_data, '$.phone')) = ?", [$phone]);
                        });
                    }
                    
                    $partialRegistration = $query->first();
                    
                    if ($partialRegistration) {
                        // Calculer le solde restant
                        $balanceDue = max(0, $partialRegistration->ticket_price - $partialRegistration->amount_paid);
                        $partialRegistration->balance_due = $balanceDue;
                    }
                }

                return view('events.registration-form', compact('currentOrganization', 'currentEvent', 'formStructure', 'partialRegistration'));
            } catch (\Exception $e) {
                Log::error('Erreur lors du chargement des données de l\'événement', [
                    'event_id' => $eventId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        });
    }

    private function getEventFormStructure($eventId)
    {
        return TenantHelper::withTenantConnection(function() use ($eventId) {
            $formFields = DB::connection('tenant')
                ->table('event_form_fields as f')
                ->leftJoin('event_form_sections as s', function($join) {
                    $join->on('f.event_id', '=', 's.event_id')
                         ->on('f.section_name', '=', 's.section_key');
                })
                ->where('f.event_id', $eventId)
                ->where('f.is_visible', true)
                ->select([
                    'f.id',
                    'f.field_key',
                    'f.field_label',
                    'f.field_type',
                    'f.field_config',
                    'f.is_required',
                    'f.is_readonly',
                    'f.field_width',
                    'f.display_order',
                    'f.section_name',
                    'f.field_help_text',
                    'f.field_description',
                    's.section_title',
                    's.section_description',
                    's.display_order as section_order',
                    's.is_collapsible',
                    's.is_expanded',
                    's.section_key'
                ])
                ->orderBy('s.display_order', 'asc')
                ->orderBy('f.display_order', 'asc')
                ->get();

            return $formFields;
        });
    }

    public function storeRegistration(Request $request)
    {

        Log::info('=== RÉCEPTION DONNÉES FORMULAIRE ===', [
            'all_data' => $request->all(),
            'breakfast_other' => $request->get('breakfast_preference_other'),
            'lunch_other' => $request->get('lunch_preference_other'),
            'dinner_other' => $request->get('dinner_preference_other'),
            'has_breakfast_other' => $request->has('breakfast_preference_other'),
            'has_lunch_other' => $request->has('lunch_preference_other'),
            'has_dinner_other' => $request->has('dinner_preference_other'),
        ]);

        $currentOrganization = TenantHelper::getCurrentOrganization();
        $currentEvent = TenantHelper::getCurrentEvent();

        if (!$currentOrganization || !$currentEvent) {
            return response()->json(['error' => 'Contexte manquant'], 500);
        }

        try {
            return TenantHelper::withTenantConnection(function() use ($request, $currentEvent, $currentOrganization) {
                $formStructure = $this->getEventFormStructure($currentEvent->id);
                $validator = $this->validateFormData($request, $formStructure, $currentEvent);

                Log::info('=== APRÈS VALIDATION ===', [
                    'has_errors' => $validator->fails(),
                    'errors' => $validator->errors()->toArray(),
                    'validated_data' => $validator->validated(),
                    'validated_breakfast_other' => $validator->validated()['breakfast_preference_other'] ?? 'ABSENT',
                    'validated_lunch_other' => $validator->validated()['lunch_preference_other'] ?? 'ABSENT',
                    'validated_dinner_other' => $validator->validated()['dinner_preference_other'] ?? 'ABSENT',
                    'validated_question_1' => $validator->validated()['question_1'] ?? 'ABSENT',
                    'validated_question_2' => $validator->validated()['question_2'] ?? 'ABSENT',
                    'validated_question_3' => $validator->validated()['question_3'] ?? 'ABSENT'
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $validator->errors()
                    ], 422);
                }

                $validated = $validator->validated();
                $processedData = $this->processFormData($validated, $formStructure);
                $ticketValidation = $this->validateTicketAvailability($validated['ticket_type_id'], $currentEvent);
                if (!$ticketValidation['valid']) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['ticket_type_id' => [$ticketValidation['message']]]
                    ], 422);
                }

                $ticketType = $ticketValidation['ticket_type'];

                $duplicateCheck = $this->checkDuplicateRegistration($processedData, $currentEvent);
                if ($duplicateCheck['exists']) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['email' => [$duplicateCheck['message']]]
                    ], 422);
                }

                if ($ticketType->price == 0) {
                    Log::info('=== DÉTECTION ÉVÉNEMENT GRATUIT ===', [
                        'ticket_type_id' => $ticketType->id,
                        'ticket_name' => $ticketType->ticket_name,
                        'price' => $ticketType->price,
                        'currency' => $ticketType->currency ?? 'FCFA'
                    ]);
                    return $this->createFreeRegistration($processedData, $ticketType, $currentEvent, $currentOrganization);
                }

                Log::info('=== DÉTECTION ÉVÉNEMENT PAYANT ===', [
                    'ticket_type_id' => $ticketType->id,
                    'ticket_name' => $ticketType->ticket_name,
                    'price' => $ticketType->price,
                    'currency' => $ticketType->currency ?? 'FCFA'
                ]);
                return $this->preparePaidRegistration($processedData, $ticketType, $currentEvent);
            });

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => ['general' => ['Une erreur est survenue lors de l\'inscription. Veuillez réessayer.']]
            ], 500);
        }
    }

    private function validateFormData(Request $request, $formStructure, $event)
    {
        if ($formStructure && $formStructure->isNotEmpty()) {
            return $this->validateDynamicForm($request, $formStructure, $event);
        }

        return $this->validateClassicForm($request, $event);
    }

    private function validateDynamicForm(Request $request, $formStructure, $event)
    {
        $rules = [];
        $messages = [];
        $rules['ticket_type_id'] = 'required|integer|exists:tenant.ticket_types,id';
        $messages['ticket_type_id.required'] = 'Veuillez sélectionner un type de ticket';
        $messages['ticket_type_id.exists'] = 'Le type de ticket sélectionné n\'est pas valide';

        foreach ($formStructure as $field) {
            $fieldName = $field->field_key;
            $fieldConfig = json_decode($field->field_config, true) ?? [];
            $fieldRules = [];

            if ($field->is_required) {
                $fieldRules[] = 'required';
                $messages[$fieldName . '.required'] = $fieldConfig['error_messages']['required'] ??
                    "Le champ {$field->field_label} est obligatoire";
            } else {
                $fieldRules[] = 'nullable';
            }

            $fieldRules = array_merge($fieldRules, $this->getFieldValidationRules($field, $fieldConfig, $messages));
            $rules[$fieldName] = implode('|', array_filter($fieldRules));
            $this->addOtherFieldRules($field, $fieldConfig, $rules, $messages, $fieldName);
        }

        // SOLUTION DE SECOURS : Ajouter manuellement les règles des champs "autres"
        $rules['breakfast_preference_other'] = 'nullable|string|max:255';
        $rules['lunch_preference_other'] = 'nullable|string|max:255';
        $rules['dinner_preference_other'] = 'nullable|string|max:255';
        $rules['phone_country'] = 'nullable|string|max:10'; // Pour le code pays aussi

        // Ajouter les règles pour les questions
        $rules['question_1'] = 'nullable|string|max:1000';
        $rules['question_2'] = 'nullable|string|max:1000';
        $rules['question_3'] = 'nullable|string|max:1000';

        Log::info('=== RÈGLES DE VALIDATION FINALES ===', [
            'total_rules' => count($rules),
            'other_rules' => array_filter($rules, fn($key) => str_contains($key, '_other'), ARRAY_FILTER_USE_KEY),
            'all_rules' => $rules
        ]);

        return Validator::make($request->all(), $rules, $messages);
    }

    private function getFieldValidationRules($field, $fieldConfig, &$messages)
    {
        $fieldName = $field->field_key;
        $rules = [];

        switch ($field->field_type) {
            case 'email':
                $rules[] = 'email';
                $messages[$fieldName . '.email'] = $fieldConfig['error_messages']['email'] ?? 'Format d\'email invalide';
                break;

            case 'phone':
            case 'country_phone':
                $rules[] = 'string';
                if (isset($fieldConfig['min_length'])) {
                    $rules[] = 'min:' . $fieldConfig['min_length'];
                }
                if (isset($fieldConfig['max_length'])) {
                    $rules[] = 'max:' . $fieldConfig['max_length'];
                }
                if (isset($fieldConfig['pattern'])) {
                    $rules[] = 'regex:/' . str_replace('/', '\/', $fieldConfig['pattern']) . '/';
                }
                break;

            case 'text':
            case 'textarea':
                $rules[] = 'string';
                if (isset($fieldConfig['min_length'])) {
                    $rules[] = 'min:' . $fieldConfig['min_length'];
                }
                if (isset($fieldConfig['max_length'])) {
                    $rules[] = 'max:' . $fieldConfig['max_length'];
                }
                break;

            case 'number':
                $rules[] = 'numeric';
                if (isset($fieldConfig['min'])) {
                    $rules[] = 'min:' . $fieldConfig['min'];
                }
                if (isset($fieldConfig['max'])) {
                    $rules[] = 'max:' . $fieldConfig['max'];
                }
                break;

            case 'date':
                $rules[] = 'date';
                if (isset($fieldConfig['min_date'])) {
                    $rules[] = 'after_or_equal:' . $fieldConfig['min_date'];
                }
                if (isset($fieldConfig['max_date'])) {
                    $rules[] = 'before_or_equal:' . $fieldConfig['max_date'];
                }
                break;

            case 'url':
                $rules[] = 'url';
                break;

            case 'file':
                $rules[] = 'file';
                if (isset($fieldConfig['max_size_mb'])) {
                    $rules[] = 'max:' . ($fieldConfig['max_size_mb'] * 1024);
                }
                if (isset($fieldConfig['accepted_types'])) {
                    $mimes = array_map(function($type) {
                        return str_replace(['image/', 'application/', '.'], '', $type);
                    }, $fieldConfig['accepted_types']);
                    $rules[] = 'mimes:' . implode(',', $mimes);
                }
                break;

            case 'select':
            case 'radio':
                $options = $fieldConfig['options'] ?? [];
                $allowedValues = array_column($options, 'value');
                if ($fieldConfig['allow_other'] ?? false) {
                    $allowedValues[] = 'other';
                }
                if (!empty($allowedValues)) {
                    $rules[] = 'in:' . implode(',', $allowedValues);
                }
                break;

            case 'checkbox_group':
                $rules[] = 'array';
                if (isset($fieldConfig['min_selections'])) {
                    $rules[] = 'min:' . $fieldConfig['min_selections'];
                }
                if (isset($fieldConfig['max_selections'])) {
                    $rules[] = 'max:' . $fieldConfig['max_selections'];
                }
                $options = $fieldConfig['options'] ?? [];
                $allowedValues = array_column($options, 'value');
                if ($fieldConfig['allow_other'] ?? false) {
                    $allowedValues[] = 'other';
                }
                if (!empty($allowedValues)) {
                    $rules[] = 'in:' . implode(',', $allowedValues);
                }
                break;
        }

        return $rules;
    }

    private function addOtherFieldRules($field, $fieldConfig, &$rules, &$messages, $fieldName)
    {
        // DEBUG: Log pour voir la configuration des champs
        if (in_array($fieldName, ['breakfast_preference', 'lunch_preference', 'dinner_preference'])) {
            Log::info("DEBUG CHAMP: {$fieldName}", [
                'field_type' => $field->field_type,
                'field_config' => $fieldConfig,
                'allow_other' => $fieldConfig['allow_other'] ?? 'NON_DÉFINI'
            ]);
        }

        if (in_array($field->field_type, ['select', 'radio']) && ($fieldConfig['allow_other'] ?? false)) {
            $rules[$fieldName . '_other'] = 'required_if:' . $fieldName . ',other|string|max:255';
            $messages[$fieldName . '_other.required_if'] = 'Veuillez préciser votre choix';
            Log::info("RÈGLE RADIO/SELECT AJOUTÉE: {$fieldName}_other");
        }

        if ($field->field_type === 'checkbox_group' && ($fieldConfig['allow_other'] ?? false)) {
            $rules[$fieldName . '_other'] = 'nullable|string|max:255';
            $messages[$fieldName . '_other.max'] = 'Le texte de précision ne peut pas dépasser 255 caractères';
            Log::info("RÈGLE CHECKBOX_GROUP AJOUTÉE: {$fieldName}_other");
        }

        // FORÇAGE POUR LES 3 CHAMPS SPÉCIFIQUES (au cas où la config ne fonctionne pas)
        if (in_array($fieldName, ['breakfast_preference', 'lunch_preference', 'dinner_preference'])) {
            $rules[$fieldName . '_other'] = 'nullable|string|max:255';
            $messages[$fieldName . '_other.max'] = 'Le texte de précision ne peut pas dépasser 255 caractères';
            Log::info("RÈGLE FORCÉE AJOUTÉE: {$fieldName}_other");
        }
    }

    private function validateClassicForm(Request $request, Event $event)
    {
        $rules = [
            'fullname' => 'required|string|max:191',
            'phone' => 'required|string|max:191',
            'email' => 'required|email|max:191',
            'organization' => 'nullable|string|max:191',
            'jci_quality' => 'nullable|string|max:191',
            'question_1' => 'nullable|string|max:1000',
            'question_2' => 'nullable|string|max:1000',
            'question_3' => 'nullable|string|max:1000',
            'ticket_type_id' => 'required|integer|exists:tenant.ticket_types,id',
        ];

        $messages = [
            'fullname.required' => 'Le nom complet est obligatoire',
            'phone.required' => 'Le numéro de téléphone est obligatoire',
            'email.required' => 'L\'adresse email est obligatoire',
            'email.email' => 'L\'adresse email doit être valide',
            'question_1.max' => 'La réponse à la question 1 ne doit pas dépasser 1000 caractères',
            'question_2.max' => 'La réponse à la question 2 ne doit pas dépasser 1000 caractères',
            'question_3.max' => 'La réponse à la question 3 ne doit pas dépasser 1000 caractères',
            'ticket_type_id.required' => 'Veuillez sélectionner un type de ticket',
            'ticket_type_id.exists' => 'Le type de ticket sélectionné n\'est pas valide'
        ];

        return Validator::make($request->all(), $rules, $messages);
    }

    private function processFormData($validated, $formStructure)
    {
        if ($formStructure && $formStructure->isNotEmpty()) {
            return $this->processDynamicFormData($validated, $formStructure);
        }

        return $validated;
    }

    private function processDynamicFormData($validated, $formStructure)
    {
        $formData = [];

        // Log pour debug
        Log::info('=== TRAITEMENT DONNÉES DYNAMIQUES ===', [
            'validated_keys' => array_keys($validated),
            'validated_data' => $validated
        ]);

        foreach ($formStructure as $field) {
            $fieldName = $field->field_key;
            $fieldConfig = json_decode($field->field_config, true) ?? [];

            if (!isset($validated[$fieldName])) {
                continue;
            }

            $value = $validated[$fieldName];

            switch ($field->field_type) {
                case 'select':
                case 'radio':
                    if ($value === 'other' && isset($validated[$fieldName . '_other'])) {
                        $formData[$fieldName] = $validated[$fieldName . '_other'];
                        $formData[$fieldName . '_is_other'] = true;
                    } else {
                        $formData[$fieldName] = $value;
                    }
                    break;

                case 'checkbox_group':
                    $formData[$fieldName] = is_array($value) ? $value : [$value];

                    // CORRECTION: Chercher les champs "autres" avec les bons noms
                    if (in_array('Autre', $formData[$fieldName])) {
                        // Essayer d'abord avec '_other'
                        if (isset($validated[$fieldName . '_other'])) {
                            $formData[$fieldName . '_other'] = $validated[$fieldName . '_other'];
                            Log::info("CHAMP AUTRE TRAITÉ: {$fieldName}_other = " . $validated[$fieldName . '_other']);
                        }
                        // Puis avec '_other_text' pour compatibilité
                        elseif (isset($validated[$fieldName . '_other_text'])) {
                            $formData[$fieldName . '_other'] = $validated[$fieldName . '_other_text'];
                            Log::info("CHAMP AUTRE TRAITÉ (compat): {$fieldName}_other_text = " . $validated[$fieldName . '_other_text']);
                        }
                    }
                    break;

                case 'country_phone':
                    $countryCode = $validated[$fieldName . '_country'] ?? $fieldConfig['country_code'] ?? '';
                    $formData[$fieldName] = $countryCode . $value;
                    if ($countryCode) {
                        $formData[$fieldName . '_country'] = $countryCode;
                    }
                    break;

                case 'file':
                    if ($value) {
                        $formData[$fieldName] = $this->handleFileUpload($value, $fieldName);
                    }
                    break;

                case 'checkbox':
                    $formData[$fieldName] = $value ? true : false;
                    break;

                default:
                    $formData[$fieldName] = $value;
            }
        }

        // AJOUT: Forcer l'inclusion des champs "autres" spécifiques
        $specificOtherFields = ['breakfast_preference_other', 'lunch_preference_other', 'dinner_preference_other'];
        foreach ($specificOtherFields as $otherField) {
            if (isset($validated[$otherField]) && !isset($formData[$otherField])) {
                $formData[$otherField] = $validated[$otherField];
                Log::info("CHAMP AUTRE FORCÉ: {$otherField} = " . $validated[$otherField]);
            }
        }

        if (isset($validated['ticket_type_id'])) {
            $formData['ticket_type_id'] = $validated['ticket_type_id'];
        }

        Log::info('=== DONNÉES TRAITÉES FINALES ===', $formData);

        return $formData;
    }

    private function validateTicketAvailability($ticketTypeId, $event)
    {
        $ticketType = TicketType::on('tenant')
            ->where('id', $ticketTypeId)
            ->where('event_id', $event->id)
            ->first();

        if (!$ticketType) {
            return [
                'valid' => false,
                'message' => 'Le type de ticket sélectionné n\'existe pas'
            ];
        }

        if (!$ticketType->is_active) {
            return [
                'valid' => false,
                'message' => 'Ce type de ticket n\'est plus disponible'
            ];
        }

        if ($ticketType->max_quantity && $ticketType->quantity_sold >= $ticketType->max_quantity) {
            return [
                'valid' => false,
                'message' => 'Ce type de ticket est épuisé'
            ];
        }

        $now = now();
        if ($ticketType->sale_start_date && $now->lt($ticketType->sale_start_date)) {
            return [
                'valid' => false,
                'message' => 'La vente pour ce type de ticket n\'a pas encore commencé'
            ];
        }

        if ($ticketType->sale_end_date && $now->gt($ticketType->sale_end_date)) {
            return [
                'valid' => false,
                'message' => 'La vente pour ce type de ticket est terminée'
            ];
        }

        return [
            'valid' => true,
            'ticket_type' => $ticketType
        ];
    }

    private function checkDuplicateRegistration($formData, $event)
    {
        $phone = $this->extractPhoneFromFormData($formData);

        if (!$phone) {
            return ['exists' => false];
        }

        $existingRegistration = Registration::on('tenant')
            ->where('event_id', $event->id)
            ->where('status', '!=', 'cancelled')
            ->where(function($q) use ($phone) {
                $q->where('phone', $phone)
                ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(form_data, '$.phone')) = ?", [$phone]);
            })
            ->first();

        if ($existingRegistration) {
            return [
                'exists' => true,
                'message' => "Ce numéro de téléphone est déjà utilisé pour cet événement"
            ];
        }

        return ['exists' => false];
    }


    private function createFreeRegistration($formData, $ticketType, $event, $organization)
    {
        Log::info('=== DÉBUT CRÉATION INSCRIPTION GRATUITE ===', [
            'event_id' => $event->id,
            'event_title' => $event->event_title,
            'ticket_type_id' => $ticketType->id,
            'ticket_name' => $ticketType->ticket_name,
            'ticket_price' => $ticketType->price,
            'organization' => $organization->org_key
        ]);

        DB::connection('tenant')->beginTransaction();
        Log::info('Transaction de base de données démarrée');

        try {
            // Extraction des données
            Log::info('=== EXTRACTION DES DONNÉES ===');
            $fullPhone = $this->extractPhoneFromFormData($formData);
            $countryCode = $this->extractPhoneCountryFromFormData($formData);
            $fullname = $this->extractNameFromFormData($formData);
            $email = $this->extractEmailFromFormData($formData);
            $organization_name = $this->extractOrganizationFromFormData($formData);
            $position = $this->extractPositionFromFormData($formData);
            $dietary = $this->extractDietaryFromFormData($formData);
            $specialNeeds = $this->extractSpecialNeedsFromFormData($formData);
            $question1 = $this->extractQuestion1FromFormData($formData);
            $question2 = $this->extractQuestion2FromFormData($formData);
            $question3 = $this->extractQuestion3FromFormData($formData);

            Log::info('Données extraites', [
                'fullPhone' => $fullPhone,
                'countryCode' => $countryCode,
                'fullname' => $fullname,
                'email' => $email,
                'organization_name' => $organization_name,
                'position' => $position,
                'dietary' => $dietary,
                'specialNeeds' => $specialNeeds,
                'question1' => $question1,
                'question2' => $question2,
                'question3' => $question3
            ]);

            // Génération du numéro d'inscription
            Log::info('=== GÉNÉRATION NUMÉRO INSCRIPTION ===');
            $registrationNumber = $this->generateRegistrationNumber();
            Log::info('Numéro d\'inscription généré', ['registration_number' => $registrationNumber]);

            // Préparation des données d'inscription
            $registrationData = [
                'event_id' => $event->id,
                'ticket_type_id' => $ticketType->id,
                'registration_number' => $registrationNumber,
                'registration_date' => now(),
                'fullname' => $fullname,
                'phone' => $fullPhone,
                'email' => $email,
                'organization' => $organization_name,
                'position' => $position,
                'dietary_requirements' => $dietary,
                'special_needs' => $specialNeeds,
                'question_1' => $question1,
                'question_2' => $question2,
                'question_3' => $question3,
                'form_data' => json_encode($formData),
                'ticket_price' => 0,
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'amount_paid' => 0,
                'confirmation_date' => now()
            ];

            Log::info('=== CRÉATION INSCRIPTION EN BASE ===', [
                'registration_data' => $registrationData
            ]);

            // Créer l'inscription avec toutes les données, y compris le code pays
            $registration = Registration::on('tenant')->create($registrationData);

            Log::info('Inscription créée avec succès', [
                'registration_id' => $registration->id,
                'registration_number' => $registration->registration_number,
                'status' => $registration->status,
                'payment_status' => $registration->payment_status
            ]);

            // Incrémenter le compteur de tickets vendus
            Log::info('=== INCréMENTATION COMPTEUR TICKETS ===', [
                'ticket_type_id' => $ticketType->id,
                'quantity_sold_avant' => $ticketType->quantity_sold
            ]);

            $ticketType->increment('quantity_sold');

            Log::info('Compteur de tickets incrémenté', [
                'ticket_type_id' => $ticketType->id,
                'quantity_sold_apres' => $ticketType->fresh()->quantity_sold
            ]);

            DB::connection('tenant')->commit();
            Log::info('Transaction de base de données commitée avec succès');

            // Générer et envoyer le ticket via PaymentController
            Log::info('=== GÉNÉRATION ET ENVOI TICKET ===', [
                'registration_id' => $registration->id,
                'event_id' => $event->id,
                'organization' => $organization->org_key
            ]);

            try {
                $paymentController = new PaymentController();
                $ticketResult = $paymentController->generateAndSendTicket($registration, $event, $organization);

                Log::info('Ticket généré et envoyé', [
                    'registration_id' => $registration->id,
                    'result' => $ticketResult
                ]);
            } catch (\Exception $ticketError) {
                Log::error('Erreur lors de la génération/envoi du ticket', [
                    'registration_id' => $registration->id,
                    'error' => $ticketError->getMessage(),
                    'trace' => $ticketError->getTraceAsString()
                ]);
                // Ne pas faire échouer l'inscription si le ticket échoue
            }

            $responseData = [
                'success' => true,
                'free_ticket' => true,
                'message' => 'Inscription confirmée ! Votre ticket électronique vous a été envoyé par WhatsApp. (Email temporairement indisponible)',
                'registration_number' => $registration->registration_number,
                'redirect_url' => route('event.success', [
                    'org_slug' => $organization->org_key,
                    'event_slug' => $event->event_slug
                ])
            ];

            Log::info('=== RÉPONSE JSON PRÉPARÉE ===', $responseData);

            return response()->json($responseData);

        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            Log::error('=== ERREUR CRÉATION INSCRIPTION GRATUITE ===', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'form_data' => $formData,
                'event_id' => $event->id ?? 'N/A',
                'ticket_type_id' => $ticketType->id ?? 'N/A',
                'organization' => $organization->org_key ?? 'N/A'
            ]);
            throw $e;
        }
    }

    private function cleanPhoneNumber($phone)
    {
        if (empty($phone)) {
            return '';
        }

        // CORRECTION: Supprimer exactement +XXX (3 chiffres après le +)
        $phone = preg_replace('/^\+\d{3}/', '', $phone);

        // Supprimer tous les caractères non numériques
        $phone = preg_replace('/[^\d]/', '', $phone);

        return $phone;
    }

    private function extractPhoneCountryFromFormData($formData)
    {
        // Chercher le code pays dans les champs dédiés
        $phoneFields = ['phone', 'telephone', 'whatsapp_number', 'numero_whatsapp'];

        foreach ($phoneFields as $field) {
            // Priorité au champ pays dédié
            if (isset($formData[$field . '_country'])) {
                return $formData[$field . '_country'];
            }
        }

        // Si pas trouvé, essayer d'extraire du numéro complet
        foreach ($phoneFields as $field) {
            if (isset($formData[$field])) {
                $phone = $formData[$field];
                if (str_starts_with($phone, '+')) {
                    // CORRECTION: Extraire exactement 3 chiffres après le +
                    preg_match('/^\+(\d{3})/', $phone, $matches);
                    if (isset($matches[0])) {
                        return $matches[0]; // Retourne +XXX
                    }
                }
            }
        }

        return '+225'; // Code par défaut pour la Côte d'Ivoire
    }

    private function preparePaidRegistration($formData, $ticketType, $event)
    {
        $fullPhone = $this->extractPhoneFromFormData($formData);
        $countryCode = $this->extractPhoneCountryFromFormData($formData);

        Log::info('=== PRÉPARATION PAIEMENT ===', [
            'breakfast_other' => $formData['breakfast_preference_other'] ?? 'ABSENT',
            'lunch_other' => $formData['lunch_preference_other'] ?? 'ABSENT',
            'dinner_other' => $formData['dinner_preference_other'] ?? 'ABSENT',
            'all_form_data' => $formData
        ]);

        // Séparer le numéro local du code pays pour le paiement
        $localPhone = $fullPhone;
        if ($countryCode && str_starts_with($fullPhone, $countryCode)) {
            $localPhone = substr($fullPhone, strlen($countryCode));
        }

        $paymentData = array_merge($formData, [
            'ticket_type_id' => $ticketType->id,
            'ticket_price' => $ticketType->price,
            'ticket_name' => $ticketType->ticket_name,
            'currency' => $ticketType->currency ?? 'FCFA',
            'fullname' => $this->extractNameFromFormData($formData),
            'full_name' => $this->extractNameFromFormData($formData),
            'email' => $this->extractEmailFromFormData($formData),
            'phone' => $fullPhone, // Numéro complet avec code pays
            'phone_country' => $countryCode, // Code pays séparé
            'phone_local' => $localPhone, // Numéro local sans code pays
            'organization' => $this->extractOrganizationFromFormData($formData),
            'position' => $this->extractPositionFromFormData($formData)
        ]);

        \Log::info('Données de paiement préparées:', $paymentData);

        return response()->json([
            'success' => true,
            'requires_payment' => true,
            'message' => 'Validation réussie, redirection vers le paiement',
            'payment_data' => $paymentData
        ]);
    }

    private function handleFileUpload($file, $fieldName)
    {
        try {
            $storagePath = 'uploads/events/' . app('current.event')->id . '/' . $fieldName;
            $path = $file->store($storagePath, 'public');

            return [
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'uploaded_at' => now()->toISOString()
            ];
        } catch (\Exception $e) {
            Log::error('Erreur upload fichier', [
                'field' => $fieldName,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    private function extractEmailFromFormData($formData)
    {
        if (!is_array($formData)) {
            return null;
        }

        return $formData['email'] ??
               $formData['email_address'] ??
               $formData['contact_email'] ??
               $formData['user_email'] ?? null;
    }

    private function extractPhoneFromFormData($formData)
    {
        // Chercher les champs de téléphone avec ou sans code pays
        $phoneFields = ['phone', 'telephone', 'whatsapp_number', 'numero_whatsapp'];

        foreach ($phoneFields as $field) {
            if (isset($formData[$field])) {
                $phone = trim($formData[$field]);
                $countryCode = $formData[$field . '_country'] ?? null;

                // CORRECTION: Formater correctement le numéro avec le code pays sélectionné
                if ($countryCode) {
                    // Nettoyer le numéro de téléphone
                    $cleanPhone = $this->cleanPhoneNumber($phone);

                    // Si le numéro nettoyé n'est pas vide, le formater avec le bon code pays
                    if ($cleanPhone) {
                        return $countryCode . $cleanPhone;
                    }
                }

                // Si pas de code pays séparé, retourner tel quel
                return $phone;
            }
        }

        return null;
    }

    private function extractNameFromFormData($formData)
    {
        if (!is_array($formData)) {
            return 'Participant';
        }

        if (!empty($formData['full_name'])) {
            return trim($formData['full_name']);
        }

        if (!empty($formData['fullname'])) {
            return trim($formData['fullname']);
        }

        if (!empty($formData['name'])) {
            return trim($formData['name']);
        }

        if (!empty($formData['participant_name'])) {
            return trim($formData['participant_name']);
        }

        // Logique pour les formulaires avec nom/prenom séparés
        $nom = trim($formData['nom'] ?? '');
        $prenoms = trim($formData['prenoms'] ?? '');

        \Log::info('Données nom/prénoms', [
            'nom' => $nom,
            'prenoms' => $prenoms
        ]);



        if ($nom || $prenoms) {
            return trim($nom . ' ' . $prenoms);
        }

        $firstName = trim($formData['first_name'] ?? '');
        $lastName = trim($formData['last_name'] ?? '');

        if ($firstName || $lastName) {
            return trim($firstName . ' ' . $lastName);
        }

        return 'Participant';
    }

    private function extractOrganizationFromFormData($formData)
    {
        if (!is_array($formData)) {
            return null;
        }

        return $formData['organization'] ??
               $formData['company'] ??
               $formData['employer'] ??
               $formData['enterprise'] ?? null;
    }

    private function extractPositionFromFormData($formData)
    {
        if (!is_array($formData)) {
            return null;
        }

        return $formData['qualite_fonction'] ??
               $formData['jci_quality'] ??
               $formData['job_title'] ??
               $formData['function'] ??
               $formData['job_function'] ??
               $formData['role'] ?? null;
    }

    private function extractDietaryFromFormData($formData)
    {
        if (!is_array($formData)) {
            return null;
        }

        $dietary = $formData['dietary_restrictions'] ??
                  $formData['dietary_requirements'] ??
                  $formData['diet'] ??
                  $formData['allergies'] ?? null;

        if (is_array($dietary)) {
            return implode(', ', array_filter($dietary));
        }

        return $dietary;
    }

    private function extractSpecialNeedsFromFormData($formData)
    {
        if (!is_array($formData)) {
            return null;
        }

        return $formData['special_needs'] ??
               $formData['special_requirements'] ??
               $formData['accessibility_needs'] ??
               $formData['medical_needs'] ?? null;
    }

    private function extractQuestion1FromFormData($formData)
    {
        if (!is_array($formData)) {
            return null;
        }

        return $formData['question_1'] ??
               $formData['question1'] ??
               $formData['q1'] ?? null;
    }

    private function extractQuestion2FromFormData($formData)
    {
        if (!is_array($formData)) {
            return null;
        }

        return $formData['question_2'] ??
               $formData['question2'] ??
               $formData['q2'] ?? null;
    }

    private function extractQuestion3FromFormData($formData)
    {
        if (!is_array($formData)) {
            return null;
        }

        return $formData['question_3'] ??
               $formData['question3'] ??
               $formData['q3'] ?? null;
    }

    private function generateRegistrationNumber()
    {
        return TenantHelper::withTenantConnection(function() {
            do {
                $number = 'Czotick-' . strtoupper(uniqid());
            } while (Registration::on('tenant')->where('registration_number', $number)->exists());

            return $number;
        });
    }

    private function enrichEventData(Event $event)
    {
        return TenantHelper::withTenantConnection(function() use ($event) {
            $totalRegistrations = Registration::on('tenant')
                ->where('event_id', $event->id)
                ->where('status', 'confirmed')
                ->count();

            $event->total_registrations = $totalRegistrations;
            $event->available_spots = $event->max_participants ?
                max(0, $event->max_participants - $totalRegistrations) : null;

            $now = now();
            if ($event->registration_end_date && $now->gt($event->registration_end_date)) {
                $event->registration_status = 'closed';
                $event->can_register = false;
            } elseif ($event->registration_start_date && $now->lt($event->registration_start_date)) {
                $event->registration_status = 'not_started';
                $event->can_register = false;
            } elseif ($event->max_participants && $totalRegistrations >= $event->max_participants) {
                $event->registration_status = 'full';
                $event->can_register = false;
            } else {
                $event->registration_status = 'open';
                $event->can_register = true;
            }

            return $event;
        });
    }

    public function showPaymentValidation(Request $request)
    {
        try {
            $currentOrganization = TenantHelper::getCurrentOrganization();
            $currentEvent = TenantHelper::getCurrentEvent();

            if (!$currentOrganization) {
                Log::error('Organisation non trouvée dans showPaymentValidation', [
                    'url' => $request->fullUrl(),
                    'route' => $request->route() ? $request->route()->getName() : null
                ]);
                abort(500, 'Organisation non trouvée');
            }

            // Si l'événement n'est pas dans le contexte (route sans event_slug), le récupérer depuis les paramètres
            if (!$currentEvent) {
                $eventSlug = $request->input('event_slug') ?? $request->route('event_slug');

                if (!$eventSlug) {
                    Log::error('Event slug manquant dans showPaymentValidation', [
                        'url' => $request->fullUrl(),
                        'organization' => $currentOrganization->org_key ?? null
                    ]);
                    abort(404, 'Événement non trouvé : slug manquant');
                }

                return TenantHelper::withTenantConnection(function() use ($request, $currentOrganization, $eventSlug) {
                    try {
                        $event = DB::connection('tenant')
                            ->table('events')
                            ->where('event_slug', $eventSlug)
                            ->where('is_published', true)
                            ->first();

                        if (!$event) {
                            Log::warning('Événement non trouvé dans la base tenant', [
                                'event_slug' => $eventSlug,
                                'organization' => $currentOrganization->org_key
                            ]);
                            abort(404, 'Événement non trouvé');
                        }

                        $currentEvent = Event::on('tenant')->find($event->id);
                        if (!$currentEvent) {
                            Log::error('Impossible de charger l\'événement depuis le modèle', [
                                'event_id' => $event->id,
                                'event_slug' => $eventSlug
                            ]);
                            abort(404, 'Événement non trouvé');
                        }

                        // Récréer la méthode avec l'événement trouvé
                        $data = $request->all();
                        $email = $this->extractEmailFromFormData($data);
                        if (!$email || !isset($data['ticket_type_id'])) {
                            return redirect()->route('event.registration', [
                                'org_slug' => $currentOrganization->org_key,
                                'event_slug' => $currentEvent->event_slug
                            ])->withErrors(['error' => 'Données manquantes pour le paiement']);
                        }

                        $ticketType = TicketType::on('tenant')->findOrFail($data['ticket_type_id']);

                        $paymentData = array_merge($data, [
                            'ticket_price' => $ticketType->price,
                            'ticket_name' => $ticketType->ticket_name,
                            'currency' => $ticketType->currency ?? 'FCFA',
                            'fullname' => $this->extractNameFromFormData($data),
                            'full_name' => $this->extractNameFromFormData($data),
                            'email' => $this->extractEmailFromFormData($data),
                            'phone' => $this->extractPhoneFromFormData($data),
                            'organization' => $this->extractOrganizationFromFormData($data),
                            'position' => $this->extractPositionFromFormData($data)
                        ]);

                        // Vérification finale avant de charger la vue
                        if (!$currentEvent) {
                            Log::error('currentEvent est null avant de charger la vue', [
                                'event_slug' => $eventSlug,
                                'organization' => $currentOrganization->org_key ?? null
                            ]);
                            abort(500, 'Événement non trouvé');
                        }

                        return view('events.payment-validation', compact('currentOrganization', 'currentEvent', 'paymentData'));
                    } catch (\Exception $e) {
                        Log::error('Erreur dans showPaymentValidation (sans event)', [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                            'event_slug' => $eventSlug
                        ]);
                        throw $e;
                    }
                });
            }

            $data = $request->all();
            $email = $this->extractEmailFromFormData($data);
            if (!$email || !isset($data['ticket_type_id'])) {
                return redirect()->route('event.registration', [
                    'org_slug' => $currentOrganization->org_key,
                    'event_slug' => $currentEvent->event_slug
                ])->withErrors(['error' => 'Données manquantes pour le paiement']);
            }

            return TenantHelper::withTenantConnection(function() use ($data, $currentOrganization, $currentEvent) {
                try {
                    $ticketType = TicketType::on('tenant')->findOrFail($data['ticket_type_id']);

                    $paymentData = array_merge($data, [
                        'ticket_price' => $ticketType->price,
                        'ticket_name' => $ticketType->ticket_name,
                        'currency' => $ticketType->currency ?? 'FCFA',
                        'fullname' => $this->extractNameFromFormData($data),
                        'full_name' => $this->extractNameFromFormData($data),
                        'email' => $this->extractEmailFromFormData($data),
                        'phone' => $this->extractPhoneFromFormData($data),
                        'organization' => $this->extractOrganizationFromFormData($data),
                        'position' => $this->extractPositionFromFormData($data)
                    ]);

                    // Vérification finale avant de charger la vue
                    if (!$currentEvent) {
                        Log::error('currentEvent est null avant de charger la vue (avec event)', [
                            'organization' => $currentOrganization->org_key ?? null
                        ]);
                        abort(500, 'Événement non trouvé');
                    }

                    return view('events.payment-validation', compact('currentOrganization', 'currentEvent', 'paymentData'));
                } catch (\Exception $e) {
                    Log::error('Erreur dans showPaymentValidation (avec event)', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'event_id' => $currentEvent->id ?? null
                    ]);
                    throw $e;
                }
            });
        } catch (\Exception $e) {
            Log::error('Erreur fatale dans showPaymentValidation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'url' => $request->fullUrl()
            ]);
            throw $e;
        }
    }

    public function paymentValidation(Request $request)
    {
        Log::info('=== paymentValidation appelé ===', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'route' => $request->route() ? $request->route()->getName() : null
        ]);

        $currentOrganization = TenantHelper::getCurrentOrganization();
        $currentEvent = TenantHelper::getCurrentEvent();

        Log::info('=== Contexte récupéré dans paymentValidation ===', [
            'has_organization' => !is_null($currentOrganization),
            'has_event' => !is_null($currentEvent),
            'org_key' => $currentOrganization->org_key ?? null,
            'event_slug' => $currentEvent->event_slug ?? null
        ]);

        // Si l'événement n'est pas dans le contexte (route sans event_slug), le récupérer depuis les paramètres
        if (!$currentEvent && $currentOrganization) {
            $eventSlug = $request->input('event_slug') ?? $request->route('event_slug');
            if ($eventSlug) {
                return TenantHelper::withTenantConnection(function() use ($request, $currentOrganization, $eventSlug) {
                    $event = DB::connection('tenant')
                        ->table('events')
                        ->where('event_slug', $eventSlug)
                        ->where('is_published', true)
                        ->first();

                    if (!$event) {
                        abort(404, 'Événement non trouvé');
                    }

                    $currentEvent = Event::on('tenant')->find($event->id);
                    if (!$currentEvent) {
                        abort(404, 'Événement non trouvé');
                    }

                    // Récréer la méthode avec l'événement trouvé
                    $formData = $request->all();
                    $ticketTypeId = $formData['ticket_type_id'] ?? null;
                    $email = $this->extractEmailFromFormData($formData);
                    $phone = $this->extractPhoneFromFormData($formData);

                    if (!$ticketTypeId || !$email) {
                        return back()->withErrors(['error' => 'Données manquantes pour traiter l\'inscription']);
                    }

                    $ticketType = TicketType::on('tenant')
                        ->where('id', $ticketTypeId)
                        ->where('event_id', $currentEvent->id)
                        ->where('is_active', true)
                        ->first();

                    if (!$ticketType) {
                        return back()->withErrors(['ticket_type_id' => 'Type de ticket invalide ou non disponible']);
                    }

                    $duplicateCheck = $this->checkDuplicateRegistration($formData, $currentEvent);
                    if ($duplicateCheck['exists']) {
                        return back()->withErrors(['error' => $duplicateCheck['message']]);
                    }

                    $paymentData = array_merge($formData, [
                        'ticket_name' => $ticketType->ticket_name,
                        'ticket_price' => $ticketType->price,
                        'currency' => $ticketType->currency ?? 'FCFA',
                        'fullname' => $this->extractNameFromFormData($formData),
                        'full_name' => $this->extractNameFromFormData($formData),
                        'email' => $email,
                        'phone' => $phone,
                        'organization' => $this->extractOrganizationFromFormData($formData),
                        'position' => $this->extractPositionFromFormData($formData)
                    ]);

                    return view('public.validation_form', compact(
                        'paymentData',
                        'currentOrganization',
                        'currentEvent'
                    ));
                });
            }
        }

        if (!$currentOrganization || !$currentEvent) {
            Log::error('Contexte manquant dans paymentValidation', [
                'has_organization' => !is_null($currentOrganization),
                'has_event' => !is_null($currentEvent)
            ]);
            abort(500, 'Contexte d\'organisation ou d\'événement manquant');
        }

        $formData = $request->all();
        $ticketTypeId = $formData['ticket_type_id'] ?? null;
        $email = $this->extractEmailFromFormData($formData);
        $phone = $this->extractPhoneFromFormData($formData);

        Log::info('=== Données extraites dans paymentValidation ===', [
            'ticket_type_id' => $ticketTypeId,
            'email' => $email,
            'phone' => $phone
        ]);

        if (!$ticketTypeId || !$email) {
            Log::warning('Données manquantes dans paymentValidation', [
                'ticket_type_id' => $ticketTypeId,
                'email' => $email
            ]);
            return back()->withErrors(['error' => 'Données manquantes pour traiter l\'inscription']);
        }

        return TenantHelper::withTenantConnection(function() use (
            $formData, $ticketTypeId, $email, $phone, $currentOrganization, $currentEvent
        ) {
            Log::info('=== Dans withTenantConnection paymentValidation ===');
            $ticketType = TicketType::on('tenant')
                ->where('id', $ticketTypeId)
                ->where('event_id', $currentEvent->id)
                ->where('is_active', true)
                ->first();

            if (!$ticketType) {
                return back()->withErrors(['ticket_type_id' => 'Type de ticket invalide ou non disponible']);
            }

            $duplicateCheck = $this->checkDuplicateRegistration($formData, $currentEvent);
            if ($duplicateCheck['exists']) {
                return back()->withErrors(['error' => $duplicateCheck['message']]);
            }

            $paymentData = array_merge($formData, [
                'ticket_name' => $ticketType->ticket_name,
                'ticket_price' => $ticketType->price,
                'currency' => $ticketType->currency ?? 'FCFA',
                'fullname' => $this->extractNameFromFormData($formData),
                'full_name' => $this->extractNameFromFormData($formData),
                'email' => $email,
                'phone' => $phone,
                'organization' => $this->extractOrganizationFromFormData($formData),
                'position' => $this->extractPositionFromFormData($formData)
            ]);

            Log::info('=== Chargement de la vue validation_form ===', [
                'has_paymentData' => !empty($paymentData),
                'has_organization' => !is_null($currentOrganization),
                'has_event' => !is_null($currentEvent)
            ]);

            try {
                return view('public.validation_form', compact(
                    'paymentData',
                    'currentOrganization',
                    'currentEvent'
                ));
            } catch (\Exception $e) {
                Log::error('Erreur lors du chargement de la vue validation_form', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        });
    }

    public function processPaymentValidation(Request $request)
    {
        Log::error('processPaymentValidation', [
                    'request' => $request
                ]);

        $currentOrganization = TenantHelper::getCurrentOrganization();
        $currentEvent = TenantHelper::getCurrentEvent();

        if (!$currentOrganization || !$currentEvent) {
            abort(500, 'Contexte d\'organisation ou d\'événement manquant');
        }

        $formData = $request->all();

        return TenantHelper::withTenantConnection(function() use ($formData, $currentOrganization, $currentEvent) {
            $ticketTypeId = $formData['ticket_type_id'] ?? null;
            $ticketType = TicketType::on('tenant')->findOrFail($ticketTypeId);

            DB::connection('tenant')->beginTransaction();

            try {
                $registration = Registration::on('tenant')->create([
                    'event_id' => $currentEvent->id,
                    'ticket_type_id' => $ticketType->id,
                    'registration_number' => $this->generateRegistrationNumber(),
                    'registration_date' => now(),
                    'fullname' => $this->extractNameFromFormData($formData),
                    'phone' => $this->extractPhoneFromFormData($formData),
                    'email' => $this->extractEmailFromFormData($formData),
                    'organization' => $this->extractOrganizationFromFormData($formData),
                    'position' => $this->extractPositionFromFormData($formData),
                    'dietary_requirements' => $this->extractDietaryFromFormData($formData),
                    'special_needs' => $this->extractSpecialNeedsFromFormData($formData),
                    'question_1' => $this->extractQuestion1FromFormData($formData),
                    'question_2' => $this->extractQuestion2FromFormData($formData),
                    'question_3' => $this->extractQuestion3FromFormData($formData),
                    'form_data' => json_encode($formData),
                    'ticket_price' => $ticketType->price,
                    'status' => 'pending',
                    'payment_status' => 'pending'
                ]);

                $ticketType->increment('quantity_sold');
                DB::connection('tenant')->commit();

                return $this->initiatePayment($registration, $currentOrganization, $currentEvent);

            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();

                Log::error('Erreur lors de la création de l\'inscription', [
                    'error' => $e->getMessage(),
                    'event_id' => $currentEvent->id,
                    'form_data' => $formData
                ]);

                return back()->withErrors(['error' => 'Une erreur est survenue lors de l\'inscription']);
            }
        });
    }

    private function initiatePayment($registration, $organization, $event)
    {

        Log::info('initiatePayment', [
                    'registration_id' => $registration->id,
                ]);

        $paymentData = [
            'registration_id' => $registration->id,
            'amount' => $registration->ticket_price,
            'currency' => 'FCFA',
            'reference' => $registration->registration_number,
            'description' => "Inscription {$event->event_title}",
            'success_url' => route('event.registration', [
                'org_slug' => $organization->org_key,
                'event_slug' => $event->event_slug
            ]) . '?payment=success&ref=' . $registration->registration_number,
            'error_url' => route('event.registration', [
                'org_slug' => $organization->org_key,
                'event_slug' => $event->event_slug
            ]) . '?payment=error&ref=' . $registration->registration_number
        ];

        return redirect()->to('https://payment-gateway.com/pay?' . http_build_query($paymentData));
    }

    public function paymentCallback(Request $request)
    {
        $paymentReference = $request->input('reference');
        $status = $request->input('status');
        $transactionId = $request->input('transaction_id');

        return TenantHelper::withTenantConnection(function() use ($paymentReference, $status, $transactionId) {
            $registration = Registration::on('tenant')
                ->where('registration_number', $paymentReference)
                ->first();

            if (!$registration) {
                Log::error('Registration not found for payment callback', [
                    'reference' => $paymentReference
                ]);
                return response()->json(['error' => 'Registration not found'], 404);
            }

            if ($status === 'success') {
                $registration->update([
                    'status' => 'confirmed',
                    'payment_status' => 'paid',
                    'amount_paid' => $registration->ticket_price,
                    'confirmation_date' => now(),
                    'payment_transaction_id' => $transactionId
                ]);

                $this->sendTicketEmail($registration);

                Log::info('Payment successful', [
                    'registration_id' => $registration->id,
                    'transaction_id' => $transactionId
                ]);
            } else {
                $registration->update([
                    'payment_status' => 'failed'
                ]);

                $registration->ticketType->decrement('quantity_sold');

                Log::warning('Payment failed', [
                    'registration_id' => $registration->id,
                    'transaction_id' => $transactionId
                ]);
            }

            return response()->json(['success' => true]);
        });
    }

    public function showSuccess(Request $request)
    {
        $currentOrganization = TenantHelper::getCurrentOrganization();
        $currentEvent = TenantHelper::getCurrentEvent();

        $registrationNumber = $request->query('ref') ?? $request->session()->get('registration_number');

        return view('events.success', compact('currentOrganization', 'currentEvent', 'registrationNumber'));
    }

    public function showError(Request $request)
    {
        $currentOrganization = TenantHelper::getCurrentOrganization();
        $currentEvent = TenantHelper::getCurrentEvent();

        $error = $request->session()->get('error', 'Une erreur est survenue');

        return view('events.error', compact('currentOrganization', 'currentEvent', 'error'));
    }

    public function downloadTicket($registrationId)
    {
        $currentOrganization = TenantHelper::getCurrentOrganization();
        $currentEvent = TenantHelper::getCurrentEvent();

        return TenantHelper::withTenantConnection(function() use ($registrationId, $currentOrganization, $currentEvent) {
            $registration = Registration::on('tenant')
                ->where('id', $registrationId)
                ->where('event_id', $currentEvent->id)
                ->first();

            if (!$registration) {
                abort(404, 'Inscription non trouvée');
            }

            if ($registration->status !== 'confirmed' || $registration->payment_status !== 'paid') {
                abort(403, 'Ticket non disponible');
            }

            return $this->generateTicketPDF($registration, $currentOrganization, $currentEvent);
        });
    }

    private function generateTicketPDF(Registration $registration, $organization, $event)
    {
        return response()->json([
            'message' => 'Génération du ticket PDF à implémenter',
            'registration' => $registration->registration_number
        ]);
    }

    public function getEventInfo()
    {
        $currentEvent = TenantHelper::getCurrentEvent();
        $currentOrganization = TenantHelper::getCurrentOrganization();

        return TenantHelper::withTenantConnection(function() use ($currentEvent, $currentOrganization) {
            $formStructure = $this->getEventFormStructure($currentEvent->id);
            $enrichedEvent = $this->enrichEventData($currentEvent);

            return response()->json([
                'event' => [
                    'id' => $enrichedEvent->id,
                    'title' => $enrichedEvent->event_title,
                    'description' => $enrichedEvent->event_description,
                    'date' => $enrichedEvent->event_date->format('Y-m-d'),
                    'start_time' => $enrichedEvent->event_start_time?->format('H:i'),
                    'end_time' => $enrichedEvent->event_end_time?->format('H:i'),
                    'location' => $enrichedEvent->event_location,
                    'max_participants' => $enrichedEvent->max_participants,
                    'total_registrations' => $enrichedEvent->total_registrations,
                    'available_spots' => $enrichedEvent->available_spots,
                    'registration_status' => $enrichedEvent->registration_status,
                    'can_register' => $enrichedEvent->can_register,
                    'colors' => [
                        'primary' => $enrichedEvent->primary_color,
                        'secondary' => $enrichedEvent->secondary_color
                    ]
                ],
                'organization' => [
                    'name' => $currentOrganization->org_name,
                    'logo' => $currentOrganization->organization_logo ? url('public/' . $currentOrganization->organization_logo) : null
                ],
                'form_structure' => $formStructure->map(function($field) {
                    return [
                        'field_key' => $field->field_key,
                        'field_label' => $field->field_label,
                        'field_type' => $field->field_type,
                        'field_config' => json_decode($field->field_config, true),
                        'is_required' => $field->is_required,
                        'section_name' => $field->section_name,
                        'section_title' => $field->section_title,
                        'field_width' => $field->field_width,
                        'display_order' => $field->display_order
                    ];
                })
            ]);
        });
    }

    public function getTicketTypes()
    {
        $currentEvent = TenantHelper::getCurrentEvent();

        return TenantHelper::withTenantConnection(function() use ($currentEvent) {
            $ticketTypes = TicketType::on('tenant')
                ->where('event_id', $currentEvent->id)
                ->where('is_active', true)
                ->orderBy('display_order')
                ->get();

            return response()->json([
                'ticket_types' => $ticketTypes->map(function($ticket) {
                    $availableQuantity = null;
                    if ($ticket->max_quantity) {
                        $availableQuantity = max(0, $ticket->max_quantity - $ticket->quantity_sold);
                    }

                    return [
                        'id' => $ticket->id,
                        'name' => $ticket->ticket_name,
                        'description' => $ticket->ticket_description,
                        'price' => $ticket->price,
                        'currency' => $ticket->currency,
                        'available_quantity' => $availableQuantity,
                        'is_available' => $ticket->is_active && ($availableQuantity === null || $availableQuantity > 0)
                    ];
                })
            ]);
        });
    }

    public function checkRegistration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required_without:phone|email',
            'phone' => 'required_without:email|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $currentEvent = TenantHelper::getCurrentEvent();

        return TenantHelper::withTenantConnection(function() use ($request, $currentEvent) {
            $query = Registration::on('tenant')->where('event_id', $currentEvent->id);

            if ($request->email) {
                $query->where(function($q) use ($request) {
                    $q->where('email', $request->email)
                      ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(form_data, '$.email')) = ?", [$request->email]);
                });
            }

            if ($request->phone) {
                $query->orWhere(function($q) use ($request) {
                    $q->where('phone', $request->phone)
                      ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(form_data, '$.phone')) = ?", [$request->phone]);
                });
            }

            $registration = $query->with('ticketType')->first();

            if (!$registration) {
                return response()->json(['found' => false]);
            }

            $formData = json_decode($registration->form_data, true) ?? [];

            return response()->json([
                'found' => true,
                'registration' => [
                    'number' => $registration->registration_number,
                    'fullname' => $registration->fullname ?? $this->extractNameFromFormData($formData),
                    'email' => $registration->email ?? $this->extractEmailFromFormData($formData),
                    'phone' => $registration->phone ?? $this->extractPhoneFromFormData($formData),
                    'organization' => $registration->organization ?? $this->extractOrganizationFromFormData($formData),
                    'ticket_type' => $registration->ticketType->ticket_name,
                    'status' => $registration->status,
                    'payment_status' => $registration->payment_status,
                    'amount_paid' => $registration->amount_paid,
                    'ticket_price' => $registration->ticket_price,
                    'registration_date' => $registration->registration_date?->format('d/m/Y H:i'),
                    'form_data' => $formData
                ]
            ]);
        });
    }

    public function exportRegistrations(Request $request)
    {
        $currentEvent = TenantHelper::getCurrentEvent();
        $currentOrganization = TenantHelper::getCurrentOrganization();

        return TenantHelper::withTenantConnection(function() use ($currentEvent, $currentOrganization) {
            $registrations = Registration::on('tenant')
                ->where('event_id', $currentEvent->id)
                ->with('ticketType')
                ->orderBy('created_at')
                ->get();

            $formStructure = $this->getEventFormStructure($currentEvent->id);

            $headers = [
                'Numéro d\'inscription',
                'Date d\'inscription',
                'Statut',
                'Statut de paiement',
                'Type de ticket',
                'Prix',
                'Montant payé'
            ];

            $dynamicHeaders = [];
            if ($formStructure && $formStructure->isNotEmpty()) {
                foreach ($formStructure->groupBy('section_key') as $sectionKey => $fields) {
                    foreach ($fields->sortBy('display_order') as $field) {
                        $dynamicHeaders[] = $field->field_label;
                    }
                }
            }

            $headers = array_merge($headers, $dynamicHeaders);
            $headers = array_merge($headers, [
                'Nom complet (legacy)',
                'Email (legacy)',
                'Téléphone (legacy)',
                'Organisation (legacy)',
                'Position (legacy)'
            ]);

            $csvData = [$headers];

            foreach ($registrations as $registration) {
                $formData = json_decode($registration->form_data, true) ?? [];

                $row = [
                    $registration->registration_number,
                    $registration->registration_date?->format('d/m/Y H:i') ?? '',
                    $registration->status,
                    $registration->payment_status,
                    $registration->ticketType?->ticket_name ?? '',
                    $registration->ticket_price,
                    $registration->amount_paid
                ];

                if ($formStructure && $formStructure->isNotEmpty()) {
                    foreach ($formStructure->groupBy('section_key') as $sectionKey => $fields) {
                        foreach ($fields->sortBy('display_order') as $field) {
                            $value = $formData[$field->field_key] ?? '';

                            if (is_array($value)) {
                                $value = implode(', ', $value);
                            }

                            $row[] = $value;
                        }
                    }
                }

                $row = array_merge($row, [
                    $registration->fullname ?? '',
                    $registration->email ?? '',
                    $registration->phone ?? '',
                    $registration->organization ?? '',
                    $registration->position ?? ''
                ]);

                $csvData[] = $row;
            }

            $filename = "inscriptions_{$currentEvent->event_slug}_" . date('Y-m-d_H-i-s') . ".csv";
            $callback = function() use ($csvData) {
                $file = fopen('php://output', 'w');

                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

                foreach ($csvData as $row) {
                    fputcsv($file, $row, ';');
                }

                fclose($file);
            };

            return response()->stream($callback, 200, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ]);
        });
    }

    private function normalizePhoneNumber($phone, $countryCode = '+225')
    {
        if (empty($phone)) {
            return null;
        }

        // Nettoyer le numéro (supprimer espaces, tirets, etc.)
        $phone = preg_replace('/[^\d+]/', '', $phone);

        // Si le numéro commence déjà par +, le retourner tel quel
        if (str_starts_with($phone, '+')) {
            return $phone;
        }

        // Si le numéro commence par 00, remplacer par +
        if (str_starts_with($phone, '00')) {
            return '+' . substr($phone, 2);
        }

        // Ajouter le code pays par défaut
        return $countryCode . $phone;
    }

    /**
     * Finaliser le paiement partiel ou la réservation
     */
    public function completePartialPayment(Request $request, $registrationId)
    {
        $currentOrganization = TenantHelper::getCurrentOrganization();
        $currentEvent = TenantHelper::getCurrentEvent();

        if (!$currentOrganization || !$currentEvent) {
            abort(500, 'Contexte d\'organisation ou d\'événement manquant');
        }

        return TenantHelper::withTenantConnection(function() use ($request, $registrationId, $currentOrganization, $currentEvent) {
            $registration = Registration::on('tenant')
                ->where('id', $registrationId)
                ->where('event_id', $currentEvent->id)
                ->whereIn('payment_status', ['partial', 'reservation'])
                ->where('status', '!=', 'cancelled')
                ->first();

            if (!$registration) {
                return redirect()->route('event.registration', [
                    'org_slug' => $currentOrganization->org_key,
                    'event_slug' => $currentEvent->event_slug
                ])->withErrors(['error' => 'Inscription non trouvée ou déjà complétée']);
            }

            // Calculer le solde restant
            $balanceDue = max(0, $registration->ticket_price - $registration->amount_paid);

            if ($balanceDue <= 0) {
                return redirect()->route('event.registration', [
                    'org_slug' => $currentOrganization->org_key,
                    'event_slug' => $currentEvent->event_slug
                ])->with('success', 'Votre inscription est déjà complètement payée.');
            }

            // Préparer les données pour le paiement
            $paymentData = [
                'registration_id' => $registration->id,
                'registration_number' => $registration->registration_number,
                'fullname' => $registration->fullname,
                'email' => $registration->email,
                'phone' => $registration->phone,
                'organization' => $registration->organization,
                'position' => $registration->position,
                'ticket_type_id' => $registration->ticket_type_id,
                'ticket_name' => $registration->ticketType->ticket_name ?? 'Ticket',
                'ticket_price' => $registration->ticket_price,
                'amount_paid' => $registration->amount_paid,
                'balance_due' => $balanceDue,
                'currency' => $currentEvent->currency ?? 'XOF',
                'payment_type' => 'complete', // Indique que c'est pour compléter le paiement
                'is_partial_completion' => true
            ];

            Log::info('Finalisation du paiement partiel', [
                'registration_id' => $registration->id,
                'balance_due' => $balanceDue,
                'amount_paid' => $registration->amount_paid,
                'ticket_price' => $registration->ticket_price
            ]);

            // Rediriger vers la page de validation du paiement avec les données
            return view('public.validation_form', compact(
                'paymentData',
                'currentOrganization',
                'currentEvent'
            ));
        });
    }
}
