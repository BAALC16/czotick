<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Helpers\TenantHelper;
use Illuminate\Http\JsonResponse;
use App\Models\Event;
use App\Models\TicketType;
use App\Models\Registration;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use App\Models\EventAccessControl;
use App\Models\AccessTimeLog;
use Endroid\QrCode\ErrorCorrectionLevel;


class PaymentController extends Controller
{
    /**
     * Traiter la validation du paiement
     */
    public function processPaymentValidation(Request $request)
    {
        $organization = app('current.organization');
        $event = app('current.event');

        $registrationData = $request->session()->get('registration_data');

        if (!$registrationData) {
            return redirect()->route('event.registration', [
                'org_slug' => $request->route('org_slug'),
                'event_slug' => $request->route('event_slug')
            ])->with('error', 'Données d\'inscription non trouvées.');
        }

        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|in:wave,manual,cash,bank_transfer',
            'phone_payment' => 'required_if:payment_method,wave|string|max:20',
            'payment_reference' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $paymentMethod = $request->payment_method;

        switch ($paymentMethod) {
            case 'wave':
                return $this->processWavePayment($request, $registrationData);

            case 'manual':
                return $this->processManualPayment($request, $registrationData);

            case 'cash':
                return $this->processCashPayment($request, $registrationData);

            case 'bank_transfer':
                return $this->processBankTransferPayment($request, $registrationData);

            default:
                return back()->withErrors(['payment_method' => 'Méthode de paiement non supportée.']);
        }
    }

    /* public function waveCallback(Request $request)
    {
        $currentOrganization = TenantHelper::getCurrentOrganization();
        $currentEvent = TenantHelper::getCurrentEvent();

        if (!$currentOrganization || !$currentEvent) {
            return response('Context missing', 400);
        }

        $waveTransactionId = $request->input('id');
        $status = $request->input('status');
        $clientReference = $request->input('client_reference');

        if (!$waveTransactionId || !$clientReference) {
            return response('Missing data', 400);
        }

        try {
            return TenantHelper::withTenantConnection(function() use ($waveTransactionId, $status, $clientReference, $currentEvent) {
                $transaction = DB::connection('tenant')
                                ->table('payment_transactions')
                                ->where('transaction_reference', $clientReference)
                                ->where('external_reference', $waveTransactionId)
                                ->first();

                if (!$transaction) {
                    return response('Transaction not found', 404);
                }

                if ($status === 'success' && $transaction->status !== 'completed') {
                    DB::connection('tenant')->beginTransaction();

                    try {
                        $participantData = json_decode($transaction->metadata, true)['participant_data'];

                        $registration = Registration::on('tenant')->create([
                            'event_id' => $currentEvent->id,
                            'ticket_type_id' => $participantData['ticket_type_id'],
                            'registration_number' => $this->generateRegistrationNumber(),
                            'fullname' => $participantData['fullname'],
                            'phone' => $participantData['phone'],
                            'email' => $participantData['email'],
                            'organization' => $participantData['organization'],
                            'position' => $participantData['position'],
                            'question_1' => $participantData['question_1'] ?? null,
                            'question_2' => $participantData['question_2'] ?? null,
                            'question_3' => $participantData['question_3'] ?? null,
                            'ticket_price' => $transaction->amount,
                            'amount_paid' => $transaction->amount,
                            'status' => 'confirmed',
                            'payment_status' => 'paid',
                            'confirmation_date' => now()
                        ]);

                        // Mettre à jour la transaction
                        DB::connection('tenant')
                        ->table('payment_transactions')
                        ->where('id', $transaction->id)
                        ->update([
                            'registration_id' => $registration->id,
                            'status' => 'completed',
                            'payment_date' => now(),
                            'processed_date' => now()
                        ]);

                        // Incrémenter le compteur de tickets vendus
                        TicketType::on('tenant')
                                ->where('id', $participantData['ticket_type_id'])
                                ->increment('quantity_sold');

                        DB::connection('tenant')->commit();

                        // Envoyer les emails de confirmation
                        $this->sendConfirmationEmails($registration, $currentEvent, TenantHelper::getCurrentOrganization());

                        Log::info('Inscription créée avec succès après paiement Wave', [
                            'registration_id' => $registration->id,
                            'registration_number' => $registration->registration_number,
                            'wave_transaction_id' => $waveTransactionId
                        ]);

                        return response('OK', 200);

                    } catch (\Exception $e) {
                        DB::connection('tenant')->rollback();
                        Log::error('Erreur lors de la création de l\'inscription après paiement Wave: ' . $e->getMessage());
                        return response('Registration creation failed', 500);
                    }

                } elseif ($status === 'failed' || $status === 'cancelled') {
                    // Paiement échoué
                    DB::connection('tenant')
                    ->table('payment_transactions')
                    ->where('id', $transaction->id)
                    ->update([
                        'status' => 'failed',
                        'processed_date' => now()
                    ]);

                    Log::info('Paiement Wave échoué', [
                        'wave_transaction_id' => $waveTransactionId,
                        'status' => $status
                    ]);

                    return response('Payment failed', 200);
                }

                return response('OK', 200);
            });

        } catch (\Exception $e) {
            Log::error('Erreur dans callback Wave: ' . $e->getMessage());
            return response('Internal error', 500);
        }
    } */

    public function waveCallback(Request $request)
    {
        Log::info('=== WAVE CALLBACK REÇU ===', [
            'timestamp' => now()->toISOString(),
            'wave_data' => $request->all(),
            'headers' => $request->headers->all(),
            'ip_address' => $request->ip()
        ]);

        $currentOrganization = TenantHelper::getCurrentOrganization();
        $currentEvent = TenantHelper::getCurrentEvent();

        if (!$currentOrganization || !$currentEvent) {
            Log::error('Contexte manquant dans Wave callback', [
                'organization' => $currentOrganization ? $currentOrganization->org_key : 'null',
                'event' => $currentEvent ? $currentEvent->event_slug : 'null'
            ]);
            return response('Context missing', 400);
        }

        $waveTransactionId = $request->input('id');
        $status = $request->input('status');
        $clientReference = $request->input('client_reference');

        if (!$waveTransactionId || !$clientReference) {
            Log::warning('Données manquantes Wave callback', [
                'wave_transaction_id' => $waveTransactionId,
                'client_reference' => $clientReference
            ]);
            return response('Missing data', 400);
        }

        try {
            return TenantHelper::withTenantConnection(function() use ($waveTransactionId, $status, $clientReference, $currentEvent, $currentOrganization) {
                $transaction = DB::connection('tenant')
                                ->table('payment_transactions')
                                ->where('transaction_reference', $clientReference)
                                ->where('external_reference', $waveTransactionId)
                                ->first();

                if (!$transaction) {
                    Log::warning('Transaction Wave non trouvée', [
                        'wave_transaction_id' => $waveTransactionId,
                        'client_reference' => $clientReference
                    ]);
                    return response('Transaction not found', 404);
                }

                Log::info('Transaction Wave trouvée', [
                    'transaction_id' => $transaction->id,
                    'current_status' => $transaction->status,
                    'new_status' => $status,
                    'amount' => $transaction->amount
                ]);

                if ($status === 'success' && $transaction->status !== 'completed') {
                    DB::connection('tenant')->beginTransaction();

                    try {
                        $metadata = json_decode($transaction->metadata, true);
                        $participantData = $metadata['participant_data'] ?? [];

                        Log::info('Données participant Wave extraites', [
                            'participant_data_keys' => array_keys($participantData),
                            'has_full_name' => isset($participantData['fullname']),
                            'has_email' => isset($participantData['email']),
                            'has_phone' => isset($participantData['phone'])
                        ]);

                        // Créer la registration avec TOUS les champs
                        $registration = $this->createCompleteRegistration([
                            'transaction' => $transaction,
                            'metadata' => $metadata,
                            'participant_data' => $participantData,
                            'event_data' => $metadata['event_data'] ?? [],
                            'current_event' => $currentEvent,
                            'current_organization' => $currentOrganization,
                            'payment_provider' => 'Wave'
                        ]);

                        if (!$registration) {
                            throw new \Exception('Échec de création de registration Wave');
                        }

                        // Mettre à jour la transaction
                        DB::connection('tenant')
                            ->table('payment_transactions')
                            ->where('id', $transaction->id)
                            ->update([
                                'registration_id' => $registration->id,
                                'status' => 'completed',
                                'payment_date' => now(),
                                'processed_date' => now(),
                                'metadata' => json_encode(array_merge($metadata, [
                                    'wave_callback_processed' => [
                                        'processed_at' => now()->toISOString(),
                                        'registration_id' => $registration->id,
                                        'registration_number' => $registration->registration_number
                                    ]
                                ]))
                            ]);

                        // Incrémenter le compteur de tickets vendus
                        TicketType::on('tenant')
                                ->where('id', $participantData['ticket_type_id'])
                                ->increment('quantity_sold');

                        DB::connection('tenant')->commit();

                        // Envoyer les emails de confirmation
                        $this->sendConfirmationEmails($registration, $currentEvent, $currentOrganization);

                        Log::info('Registration Wave créée avec succès', [
                            'registration_id' => $registration->id,
                            'registration_number' => $registration->registration_number,
                            'wave_transaction_id' => $waveTransactionId,
                            'participant_name' => $registration->fullname
                        ]);

                        return response('OK', 200);

                    } catch (\Exception $e) {
                        DB::connection('tenant')->rollback();
                        Log::error('Erreur création registration Wave', [
                            'transaction_id' => $transaction->id,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        return response('Registration creation failed', 500);
                    }

                } elseif ($status === 'failed' || $status === 'cancelled') {
                    // Marquer la transaction comme échouée
                    DB::connection('tenant')
                        ->table('payment_transactions')
                        ->where('id', $transaction->id)
                        ->update([
                            'status' => 'failed',
                            'processed_date' => now(),
                            'metadata' => json_encode(array_merge(
                                json_decode($transaction->metadata, true) ?? [],
                                [
                                    'wave_callback_failed' => [
                                        'failed_at' => now()->toISOString(),
                                        'failure_status' => $status,
                                        'wave_transaction_id' => $waveTransactionId
                                    ]
                                ]
                            ))
                        ]);

                    Log::info('Paiement Wave échoué', [
                        'wave_transaction_id' => $waveTransactionId,
                        'status' => $status,
                        'transaction_id' => $transaction->id
                    ]);

                    return response('Payment failed', 200);
                }

                return response('OK', 200);
            });

        } catch (\Exception $e) {
            Log::error('Erreur dans Wave callback', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'wave_transaction_id' => $waveTransactionId,
                'client_reference' => $clientReference
            ]);
            return response('Internal error', 500);
        }
    }

    /**
     * Traitement du paiement Orange Money via Afribapay avec gestion multi-pays
     */
    /* public function orangeMoneyProcess(Request $request): JsonResponse
    {
        Log::info('Début orangeMoneyProcess avec données complètes', [
            'all_data' => $request->all(),
            'headers' => $request->headers->all()
        ]);

        $currentOrganization = TenantHelper::getCurrentOrganization();
        $currentEvent = TenantHelper::getCurrentEvent();

        if (!$currentOrganization || !$currentEvent) {
            Log::error('Contexte manquant dans orangeMoneyProcess', [
                'organization' => $currentOrganization ? $currentOrganization->org_key : 'null',
                'event' => $currentEvent ? $currentEvent->event_slug : 'null'
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Contexte d\'organisation ou d\'événement manquant'
            ], 500);
        }

        // CORRECTION: Validation plus flexible avec gestion du code pays
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'fullname' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'phone_country' => 'nullable|string|max:5', // Code pays
            'phone_local' => 'nullable|string|max:15',  // Numéro local
            'full_phone_number' => 'nullable|string|max:20', // Numéro complet
            'organization' => 'nullable|string|max:255',
            'other_organization' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'quality' => 'nullable|string|max:255',
            'ticket_type_id' => 'required|integer',
            'ticket_name' => 'required|string',
            'ticket_price' => 'required|numeric|min:1',
            'currency' => 'required|string|max:10',
            'phone_number' => 'required|string|min:8|max:15', // Plus flexible pour multi-pays
            'otp_code' => 'required',
            'payment_method' => 'required|string|in:orange'
        ]);

        if ($validator->fails()) {
            Log::warning('Validation échouée dans orangeMoneyProcess', [
                'errors' => $validator->errors()->toArray(),
                'input_data' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Données de formulaire invalides',
                'errors' => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated();

        try {
            return TenantHelper::withTenantConnection(function() use ($validatedData, $currentEvent, $currentOrganization) {

                // Vérification du type de ticket
                $ticketType = TicketType::on('tenant')->find($validatedData['ticket_type_id']);
                if (!$ticketType || !$ticketType->is_active || $ticketType->event_id != $currentEvent->id) {
                    Log::warning('Type de ticket invalide', [
                        'ticket_type_id' => $validatedData['ticket_type_id'],
                        'event_id' => $currentEvent->id
                    ]);

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Type de ticket invalide ou non disponible.'
                    ], 400);
                }

                // NOUVEAU: Extraire et valider le code pays
                $phoneCountry = $this->extractPhoneCountry($validatedData);
                $fullPhoneNumber = $this->buildFullPhoneNumber($validatedData, $phoneCountry);

                Log::info('Numéros de téléphone traités', [
                    'phone_country' => $phoneCountry,
                    'phone_number' => $validatedData['phone_number'],
                    'full_phone_number' => $fullPhoneNumber
                ]);

                // Générer une référence unique
                $reference = "OM-" . strtoupper($currentOrganization->org_key) . "-" . strtoupper($currentEvent->event_slug) . "-" . uniqid();
                $position = $validatedData['position'] ?? $validatedData['quality'] ?? null;

                // Préparer les métadonnées complètes avec code pays
                $metadata = [
                    'participant_data' => [
                        'email' => $validatedData['email'],
                        'fullname' => $validatedData['fullname'],
                        'phone' => $validatedData['phone'],
                        'phone_country' => $phoneCountry,
                        'phone_local' => $validatedData['phone_number'],
                        'full_phone_number' => $fullPhoneNumber,
                        'organization' => $validatedData['organization'] ?: $validatedData['other_organization'],
                        'position' => $position,
                        'ticket_type_id' => $validatedData['ticket_type_id'],
                        'ticket_name' => $validatedData['ticket_name'],
                        'phone_number' => $validatedData['phone_number'],
                        'otp_code' => $validatedData['otp_code'],
                        'registration_id' => $validatedData['registration_id'] ?? null,
                        'is_partial_completion' => isset($validatedData['is_partial_completion']) && $validatedData['is_partial_completion']
                    ],
                    'event_data' => [
                        'event_id' => $currentEvent->id,
                        'event_title' => $currentEvent->event_title,
                        'organization_id' => $currentOrganization->id,
                        'organization_name' => $currentOrganization->org_name
                    ],
                    'payment_data' => [
                        'method' => 'orange_money',
                        'provider' => 'afribapay',
                        'country' => $phoneCountry,
                        'initiated_at' => now()->toISOString()
                    ]
                ];

                // Créer la transaction
                $transactionId = DB::connection('tenant')
                                ->table('payment_transactions')
                                ->insertGetId([
                                    'registration_id' => NULL,
                                    'transaction_reference' => $reference,
                                    'amount' => $validatedData['ticket_price'],
                                    'currency' => $validatedData['currency'],
                                    'payment_method' => 'mobile_money',
                                    'payment_provider' => 'Orange Money',
                                    'fees' => 500,
                                    'status' => 'pending',
                                    'metadata' => json_encode($metadata),
                                    'created_at' => now()
                                ]);

                Log::info('Transaction créée pour Orange Money', [
                    'transaction_id' => $transactionId,
                    'reference' => $reference,
                    'amount' => $validatedData['ticket_price'],
                    'country' => $phoneCountry
                ]);

                // Préparer les données pour Afribapay avec le bon numéro
                $afribapayRequest = (object) [
                    'senderOperator' => 'Orange Money',
                    'transactionId' => $reference,
                    'transactionAmount1' => $validatedData['ticket_price'],
                    'senderPhone' => $fullPhoneNumber,
                    'codeOtp' => $validatedData['otp_code']
                ];

                Log::info('Appel Afribapay pour Orange Money', [
                    'request' => [
                        'operator' => $afribapayRequest->senderOperator,
                        'transaction_id' => $afribapayRequest->transactionId,
                        'amount' => $afribapayRequest->transactionAmount1,
                        'phone' => $afribapayRequest->senderPhone,
                        'country' => $phoneCountry
                    ]
                ]);

                $afribapayResponse = $this->payinAfribapay($afribapayRequest);

                if ($afribapayResponse->status_code === 200) {
                    // Succès immédiat
                    DB::connection('tenant')
                        ->table('payment_transactions')
                        ->where('id', $transactionId)
                        ->update([
                            'status' => 'completed',
                            'external_reference' => $afribapayResponse->data['reference'] ?? null,
                            'updated_at' => now()
                        ]);

                    $this->createRegistrationFromTransaction($transactionId, 'completed');

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Paiement Orange Money confirmé avec succès.',
                        'redirect_url' => route('event.registration', [
                            'org_slug' => $currentOrganization->org_key,
                            'event_slug' => $currentEvent->event_slug
                        ]) . "?payment=success&ref=" . $reference
                    ]);

                } elseif ($afribapayResponse->status_code === 202) {
                    // Transaction en attente
                    DB::connection('tenant')
                        ->table('payment_transactions')
                        ->where('id', $transactionId)
                        ->update([
                            'status' => 'processing',
                            'external_reference' => $afribapayResponse->data['reference'] ?? null,
                            'updated_at' => now()
                        ]);

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Validation de paiement en cours.',
                        'transaction_reference' => $reference,
                        'instruction' => ''
                    ]);

                } else {
                    // Échec
                    DB::connection('tenant')
                        ->table('payment_transactions')
                        ->where('id', $transactionId)
                        ->update([
                            'status' => 'failed',
                            'metadata' => json_encode([
                                'error' => $afribapayResponse->message,
                                'afribapay_response' => $afribapayResponse->data
                            ])
                        ]);

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Échec du paiement Orange Money. ' . $afribapayResponse->message,
                    ], 400);
                }
            });

        } catch (\Exception $e) {
            Log::error('Erreur Orange Money Process', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input_data' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Une erreur est survenue lors du traitement de votre demande.',
            ], 500);
        }
    } */

    public function orangeMoneyProcess(Request $request): JsonResponse
    {
        Log::info('Début orangeMoneyProcess avec données complètes', [
            'all_data' => $request->except(['otp_code', '_token']),
            'headers' => $request->headers->all(),
            'total_fields' => count($request->all())
        ]);

        $currentOrganization = TenantHelper::getCurrentOrganization();
        $currentEvent = TenantHelper::getCurrentEvent();

        if (!$currentOrganization || !$currentEvent) {
            Log::error('Contexte manquant dans orangeMoneyProcess', [
                'organization' => $currentOrganization ? $currentOrganization->org_key : 'null',
                'event' => $currentEvent ? $currentEvent->event_slug : 'null'
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Contexte d\'organisation ou d\'événement manquant'
            ], 500);
        }

        // VALIDATION ÉTENDUE avec TOUS les champs du formulaire dynamique
        $validator = Validator::make($request->all(), [
            // Champs de base obligatoires
            'email' => 'required|email|max:255',
            'fullname' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'organization' => 'required|string|max:255',
            'other_organization' => 'nullable|string|max:255',

            // Champs téléphone
            'phone_country' => 'nullable|string|max:5',
            'phone_local' => 'nullable|string|max:15',
            'full_phone_number' => 'nullable|string|max:20',
            'phone_number' => 'required|string|min:8|max:15',

            // Champs ticket
            'ticket_type_id' => 'required|integer',
            'ticket_name' => 'required|string',
            'ticket_price' => 'required|numeric|min:1',
            'currency' => 'required|string|max:10',

            // Champs professionnels
            'position' => 'nullable|string|max:255',
            'quality' => 'nullable|string|max:255',
            'jci_quality' => 'nullable|string|max:100',
            'business_sector' => 'nullable|string|max:100',
            'years_experience' => 'nullable|string|max:50',

            // Informations personnelles étendues
            'gender' => 'nullable|string|max:20',
            'nationality' => 'nullable|string|max:100',

            // Préférences alimentaires
            'breakfast_preference' => 'nullable',
            'breakfast_preference_other' => 'nullable|string|max:100',
            'lunch_preference' => 'nullable',
            'lunch_preference_other' => 'nullable|string|max:100',
            'dinner_preference' => 'nullable',
            'dinner_preference_other' => 'nullable|string|max:100',

            // Autres préférences
            'dormitory_sharing' => 'nullable|string|max:100',
            'networking_interests' => 'nullable',
            'dietary_requirements' => 'nullable|string|max:500',
            'special_needs' => 'nullable|string|max:500',

            // Paiement Orange Money
            'otp_code' => 'required|string|min:4|max:10',
            'payment_method' => 'required|string|in:orange'
        ]);

        if ($validator->fails()) {
            Log::warning('Validation échouée dans orangeMoneyProcess', [
                'errors' => $validator->errors()->toArray(),
                'input_data' => $request->except(['otp_code', '_token'])
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Données de formulaire invalides',
                'errors' => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated();

        try {
            return TenantHelper::withTenantConnection(function() use ($validatedData, $currentEvent, $currentOrganization) {

                $ticketType = TicketType::on('tenant')->find($validatedData['ticket_type_id']);
                if (!$ticketType || !$ticketType->is_active || $ticketType->event_id != $currentEvent->id) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Type de ticket invalide ou non disponible.'
                    ], 400);
                }

                // Extraire et valider le code pays
                $phoneCountry = $this->extractPhoneCountry($validatedData);
                $fullPhoneNumber = $this->buildFullPhoneNumber($validatedData, $phoneCountry);

                $reference = "MOOV-" . strtoupper($currentOrganization->org_key) . "-" . strtoupper($currentEvent->event_slug) . "-" . uniqid();
                $position = $validatedData['position'] ?? $validatedData['quality'] ?? null;
                $organization = $validatedData['organization'] ?: $validatedData['other_organization'];

                // Métadonnées COMPLÈTES pour Moov
                $metadata = [
                    'participant_data' => [
                        // Informations de base
                        'email' => $validatedData['email'],
                        'fullname' => $validatedData['fullname'],
                        'phone' => $validatedData['phone'],
                        'phone_country' => $phoneCountry,
                        'phone_local' => $validatedData['phone_number'],
                        'full_phone_number' => $fullPhoneNumber,
                        'organization' => $organization,
                        'position' => $position,

                        // Informations étendues
                        'gender' => $validatedData['gender'] ?? null,
                        'nationality' => $validatedData['nationality'] ?? null,
                        'jci_quality' => $validatedData['jci_quality'] ?? null,
                        'business_sector' => $validatedData['business_sector'] ?? null,
                        'years_experience' => $validatedData['years_experience'] ?? null,

                        // Préférences alimentaires
                        'breakfast_preference' => is_array($validatedData['breakfast_preference'] ?? null) ?
                            $validatedData['breakfast_preference'] :
                            (isset($validatedData['breakfast_preference']) ? [$validatedData['breakfast_preference']] : null),
                        'breakfast_preference_other' => $validatedData['breakfast_preference_other'] ?? null,
                        'lunch_preference' => is_array($validatedData['lunch_preference'] ?? null) ?
                            $validatedData['lunch_preference'] :
                            (isset($validatedData['lunch_preference']) ? [$validatedData['lunch_preference']] : null),
                        'lunch_preference_other' => $validatedData['lunch_preference_other'] ?? null,
                        'dinner_preference' => is_array($validatedData['dinner_preference'] ?? null) ?
                            $validatedData['dinner_preference'] :
                            (isset($validatedData['dinner_preference']) ? [$validatedData['dinner_preference']] : null),
                        'dinner_preference_other' => $validatedData['dinner_preference_other'] ?? null,

                        // Autres préférences
                        'dormitory_sharing' => $validatedData['dormitory_sharing'] ?? null,
                        'networking_interests' => is_array($validatedData['networking_interests'] ?? null) ?
                            $validatedData['networking_interests'] :
                            (isset($validatedData['networking_interests']) ? [$validatedData['networking_interests']] : null),
                        'dietary_requirements' => $validatedData['dietary_requirements'] ?? null,
                        'special_needs' => $validatedData['special_needs'] ?? null,

                        // Données ticket
                        'ticket_type_id' => $validatedData['ticket_type_id'],
                        'ticket_name' => $validatedData['ticket_name'],
                        'phone_number' => $validatedData['phone_number']
                    ],
                    'event_data' => [
                        'event_id' => $currentEvent->id,
                        'event_title' => $currentEvent->event_title,
                        'organization_id' => $currentOrganization->id,
                        'organization_name' => $currentOrganization->org_name
                    ],
                    'payment_data' => [
                        'method' => 'moov_money',
                        'provider' => 'afribapay',
                        'country' => $phoneCountry,
                        'initiated_at' => now()->toISOString()
                    ],
                    'form_submission_data' => [
                        'user_agent' => request()->userAgent(),
                        'ip_address' => request()->ip(),
                        'submitted_at' => now()->toISOString(),
                        'total_fields' => count($validatedData)
                    ]
                ];

                $transactionId = DB::connection('tenant')
                                ->table('payment_transactions')
                                ->insertGetId([
                                    'registration_id' => NULL,
                                    'transaction_reference' => $reference,
                                    'amount' => $validatedData['ticket_price'],
                                    'currency' => $validatedData['currency'],
                                    'payment_method' => 'mobile_money',
                                    'payment_provider' => 'Moov Money',
                                    'fees' => 500,
                                    'status' => 'processing',
                                    'metadata' => json_encode($metadata),
                                    'created_at' => now()
                                ]);

                // Préparer les données pour Afribapay
                $afribapayRequest = (object) [
                    'senderOperator' => 'Moov Money',
                    'transactionId' => $reference,
                    'transactionAmount1' => $validatedData['ticket_price'],
                    'senderPhone' => $fullPhoneNumber
                ];

                Log::info('Appel payinAfribapay pour Moov Money', [
                    'request' => $afribapayRequest,
                    'country' => $phoneCountry,
                    'metadata_fields' => count($metadata['participant_data'])
                ]);

                $afribapayResponse = $this->payinAfribapay($afribapayRequest);

                if ($afribapayResponse->status_code === 200) {
                    // Succès immédiat
                    DB::connection('tenant')
                        ->table('payment_transactions')
                        ->where('id', $transactionId)
                        ->update([
                            'status' => 'completed',
                            'external_reference' => $afribapayResponse->data['reference'] ?? null,
                            'updated_at' => now()
                        ]);

                    $this->createRegistrationFromTransaction($transactionId, 'completed');

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Paiement Moov Money confirmé avec succès.',
                        'redirect_url' => route('event.registration', [
                            'org_slug' => $currentOrganization->org_key,
                            'event_slug' => $currentEvent->event_slug
                        ]) . "?payment=success&ref=" . $reference
                    ]);

                } elseif ($afribapayResponse->status_code === 202) {
                    // Transaction en attente
                    $instruction = $this->getPaymentInstruction('moov', $phoneCountry);

                    DB::connection('tenant')
                        ->table('payment_transactions')
                        ->where('id', $transactionId)
                        ->update([
                            'status' => 'processing',
                            'external_reference' => $afribapayResponse->data['reference'] ?? null,
                            'updated_at' => now()
                        ]);

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Demande de paiement Moov Money initiée.',
                        'transaction_reference' => $reference,
                        'instruction' => $instruction
                    ]);

                } else {
                    // Échec
                    DB::connection('tenant')
                        ->table('payment_transactions')
                        ->where('id', $transactionId)
                        ->update([
                            'status' => 'failed',
                            'metadata' => json_encode(array_merge($metadata, [
                                'error' => $afribapayResponse->message,
                                'afribapay_response' => $afribapayResponse->data
                            ]))
                        ]);

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Échec du paiement Moov Money. ' . $afribapayResponse->message,
                    ], 400);
                }
            });

        } catch (\Exception $e) {
            Log::error('Erreur Moov Money Process', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Une erreur est survenue lors du traitement de votre demande.',
            ], 500);
        }
    }

    /**
     * Traitement du paiement MTN Money via Afribapay avec gestion multi-pays
     */
    /* public function mtnMoneyProcess(Request $request): JsonResponse
    {
        Log::info('Début de mtnMoneyProcess via Afribapay', ['data_reçue' => $request->all()]);

        $currentOrganization = TenantHelper::getCurrentOrganization();
        $currentEvent = TenantHelper::getCurrentEvent();

        if (!$currentOrganization || !$currentEvent) {
            return response()->json([
                'status' => 'error',
                'message' => 'Contexte d\'organisation ou d\'événement manquant'
            ], 500);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'fullname' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'phone_country' => 'nullable|string|max:5',
            'phone_local' => 'nullable|string|max:15',
            'full_phone_number' => 'nullable|string|max:20',
            'organization' => 'nullable|string|max:255',
            'other_organization' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'quality' => 'nullable|string|max:255',
            'ticket_type_id' => 'required|integer',
            'ticket_name' => 'required|string',
            'ticket_price' => 'required|numeric|min:1',
            'currency' => 'required|string|max:10',
            'phone_number' => 'required|string|min:8|max:15',
            'payment_method' => 'required|string|in:mtn'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Données invalides',
                'errors' => $validator->errors()
            ], 400);
        }

        $validatedData = $validator->validated();

        try {
            return TenantHelper::withTenantConnection(function() use ($validatedData, $currentEvent, $currentOrganization) {

                $ticketType = TicketType::on('tenant')->find($validatedData['ticket_type_id']);
                if (!$ticketType || !$ticketType->is_active || $ticketType->event_id != $currentEvent->id) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Type de ticket invalide ou non disponible.'
                    ], 400);
                }

                // NOUVEAU: Extraire et valider le code pays
                $phoneCountry = $this->extractPhoneCountry($validatedData);
                $fullPhoneNumber = $this->buildFullPhoneNumber($validatedData, $phoneCountry);

                $reference = "MTN-" . strtoupper($currentOrganization->org_key) . "-" . strtoupper($currentEvent->event_slug) . "-" . uniqid();
                $position = $validatedData['position'] ?? $validatedData['quality'] ?? null;

                $transactionId = DB::connection('tenant')
                                ->table('payment_transactions')
                                ->insertGetId([
                                    'registration_id' => NULL,
                                    'transaction_reference' => $reference,
                                    'amount' => $validatedData['ticket_price'],
                                    'currency' => $validatedData['currency'],
                                    'payment_method' => 'mobile_money',
                                    'payment_provider' => 'MTN Money',
                                    'fees' => 500,
                                    'status' => 'processing',
                                    'metadata' => json_encode([
                                        'participant_data' => [
                                            'email' => $validatedData['email'],
                                            'fullname' => $validatedData['fullname'],
                                            'phone' => $validatedData['phone'],
                                            'phone_country' => $phoneCountry,
                                            'phone_local' => $validatedData['phone_number'],
                                            'full_phone_number' => $fullPhoneNumber,
                                            'organization' => $validatedData['organization'] ?: $validatedData['other_organization'],
                                            'position' => $position,
                                            'ticket_type_id' => $validatedData['ticket_type_id'],
                                            'ticket_name' => $validatedData['ticket_name'],
                                            'phone_number' => $validatedData['phone_number']
                                        ],
                                        'event_data' => [
                                            'event_id' => $currentEvent->id,
                                            'event_title' => $currentEvent->event_title,
                                            'organization_id' => $currentOrganization->id,
                                            'organization_name' => $currentOrganization->org_name
                                        ],
                                        'payment_data' => [
                                            'country' => $phoneCountry
                                        ],
                                        'initiated_at' => now()->toISOString()
                                    ]),
                                    'created_at' => now()
                                ]);

                // Préparer les données pour Afribapay avec le bon numéro
                $afribapayRequest = (object) [
                    'senderOperator' => 'MTN Money',
                    'transactionId' => $reference,
                    'transactionAmount1' => $validatedData['ticket_price'],
                    'senderPhone' => $fullPhoneNumber
                ];

                Log::info('Appel payinAfribapay pour MTN Money', [
                    'request' => $afribapayRequest,
                    'country' => $phoneCountry
                ]);

                $afribapayResponse = $this->payinAfribapay($afribapayRequest);

                if ($afribapayResponse->status_code === 200) {
                    // Succès immédiat
                    DB::connection('tenant')
                        ->table('payment_transactions')
                        ->where('id', $transactionId)
                        ->update([
                            'status' => 'completed',
                            'external_reference' => $afribapayResponse->data['reference'] ?? null,
                            'updated_at' => now()
                        ]);

                    $this->createRegistrationFromTransaction($transactionId, 'completed');

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Paiement MTN Money confirmé avec succès.',
                        'redirect_url' => route('event.registration', [
                            'org_slug' => $currentOrganization->org_key,
                            'event_slug' => $currentEvent->event_slug
                        ]) . "?payment=success&ref=" . $reference
                    ]);

                } elseif ($afribapayResponse->status_code === 202) {
                    // Transaction en attente avec message adapté au pays
                    $instruction = $this->getPaymentInstruction('mtn', $phoneCountry);

                    DB::connection('tenant')
                        ->table('payment_transactions')
                        ->where('id', $transactionId)
                        ->update([
                            'status' => 'processing',
                            'external_reference' => $afribapayResponse->data['reference'] ?? null,
                            'updated_at' => now()
                        ]);

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Demande de paiement MTN Money initiée.',
                        'transaction_reference' => $reference,
                        'instruction' => $instruction
                    ]);

                } else {
                    // Échec
                    DB::connection('tenant')
                        ->table('payment_transactions')
                        ->where('id', $transactionId)
                        ->update([
                            'status' => 'failed',
                            'metadata' => json_encode([
                                'error' => $afribapayResponse->message,
                                'afribapay_response' => $afribapayResponse->data
                            ])
                        ]);

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Échec du paiement MTN Money. ' . $afribapayResponse->message,
                    ], 400);
                }
            });

        } catch (\Exception $e) {
            Log::error('Erreur MTN Money Process', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Une erreur est survenue lors du traitement de votre demande.',
            ], 500);
        }
    } */

    /**
 * MTN MONEY PROCESS - RÉVISÉ AVEC COLLECTE COMPLÈTE
 */
public function mtnMoneyProcess(Request $request): JsonResponse
{
    Log::info('Début de mtnMoneyProcess via Afribapay avec données complètes', [
        'data_received' => $request->except(['_token']),
        'total_fields' => count($request->all())
    ]);

    $currentOrganization = TenantHelper::getCurrentOrganization();
    $currentEvent = TenantHelper::getCurrentEvent();

    if (!$currentOrganization || !$currentEvent) {
        return response()->json([
            'status' => 'error',
            'message' => 'Contexte d\'organisation ou d\'événement manquant'
        ], 500);
    }

    // Validation étendue avec TOUS les champs
    $validator = Validator::make($request->all(), [
        // Champs de base obligatoires
        'email' => 'required|email|max:255',
        'fullname' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'organization' => 'required|string|max:255',
        'other_organization' => 'nullable|string|max:255',

        // Champs téléphone
        'phone_country' => 'nullable|string|max:5',
        'phone_local' => 'nullable|string|max:15',
        'full_phone_number' => 'nullable|string|max:20',
        'phone_number' => 'required|string|min:8|max:15',

        // Champs ticket
        'ticket_type_id' => 'required|integer',
        'ticket_name' => 'required|string',
        'ticket_price' => 'required|numeric|min:1',
        'currency' => 'required|string|max:10',

        // Champs professionnels
        'position' => 'nullable|string|max:255',
        'quality' => 'nullable|string|max:255',
        'jci_quality' => 'nullable|string|max:100',
        'business_sector' => 'nullable|string|max:100',
        'years_experience' => 'nullable|string|max:50',

        // Informations personnelles
        'gender' => 'nullable|string|max:20',
        'nationality' => 'nullable|string|max:100',

        // Préférences alimentaires
        'breakfast_preference' => 'nullable',
        'breakfast_preference_other' => 'nullable|string|max:100',
        'lunch_preference' => 'nullable',
        'lunch_preference_other' => 'nullable|string|max:100',
        'dinner_preference' => 'nullable',
        'dinner_preference_other' => 'nullable|string|max:100',

        // Autres préférences
        'dormitory_sharing' => 'nullable|string|max:100',
        'networking_interests' => 'nullable',
        'dietary_requirements' => 'nullable|string|max:500',
        'special_needs' => 'nullable|string|max:500',

        'payment_method' => 'required|string|in:mtn'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Données invalides',
            'errors' => $validator->errors()
        ], 400);
    }

    $validatedData = $validator->validated();

    try {
        return TenantHelper::withTenantConnection(function() use ($validatedData, $currentEvent, $currentOrganization) {

            $ticketType = TicketType::on('tenant')->find($validatedData['ticket_type_id']);
            if (!$ticketType || !$ticketType->is_active || $ticketType->event_id != $currentEvent->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Type de ticket invalide ou non disponible.'
                ], 400);
            }

            // Extraire et valider le code pays
            $phoneCountry = $this->extractPhoneCountry($validatedData);
            $fullPhoneNumber = $this->buildFullPhoneNumber($validatedData, $phoneCountry);

            $reference = "MTN-" . strtoupper($currentOrganization->org_key) . "-" . strtoupper($currentEvent->event_slug) . "-" . uniqid();
            $position = $validatedData['position'] ?? $validatedData['quality'] ?? null;
            $organization = $validatedData['organization'] ?: $validatedData['other_organization'];

            // Métadonnées COMPLÈTES pour MTN
            $metadata = [
                'participant_data' => [
                    // Informations de base
                    'email' => $validatedData['email'],
                    'fullname' => $validatedData['fullname'],
                    'phone' => $validatedData['phone'],
                    'phone_country' => $phoneCountry,
                    'phone_local' => $validatedData['phone_number'],
                    'full_phone_number' => $fullPhoneNumber,
                    'organization' => $organization,
                    'position' => $position,

                    // Informations étendues
                    'gender' => $validatedData['gender'] ?? null,
                    'nationality' => $validatedData['nationality'] ?? null,
                    'jci_quality' => $validatedData['jci_quality'] ?? null,
                    'business_sector' => $validatedData['business_sector'] ?? null,
                    'years_experience' => $validatedData['years_experience'] ?? null,

                    // Préférences alimentaires
                    'breakfast_preference' => is_array($validatedData['breakfast_preference'] ?? null) ?
                        $validatedData['breakfast_preference'] :
                        (isset($validatedData['breakfast_preference']) ? [$validatedData['breakfast_preference']] : null),
                    'breakfast_preference_other' => $validatedData['breakfast_preference_other'] ?? null,
                    'lunch_preference' => is_array($validatedData['lunch_preference'] ?? null) ?
                        $validatedData['lunch_preference'] :
                        (isset($validatedData['lunch_preference']) ? [$validatedData['lunch_preference']] : null),
                    'lunch_preference_other' => $validatedData['lunch_preference_other'] ?? null,
                    'dinner_preference' => is_array($validatedData['dinner_preference'] ?? null) ?
                        $validatedData['dinner_preference'] :
                        (isset($validatedData['dinner_preference']) ? [$validatedData['dinner_preference']] : null),
                    'dinner_preference_other' => $validatedData['dinner_preference_other'] ?? null,

                    // Autres préférences
                    'dormitory_sharing' => $validatedData['dormitory_sharing'] ?? null,
                    'networking_interests' => is_array($validatedData['networking_interests'] ?? null) ?
                        $validatedData['networking_interests'] :
                        (isset($validatedData['networking_interests']) ? [$validatedData['networking_interests']] : null),
                    'dietary_requirements' => $validatedData['dietary_requirements'] ?? null,
                    'special_needs' => $validatedData['special_needs'] ?? null,

                    // Données ticket
                    'ticket_type_id' => $validatedData['ticket_type_id'],
                    'ticket_name' => $validatedData['ticket_name'],
                    'phone_number' => $validatedData['phone_number']
                ],
                'event_data' => [
                    'event_id' => $currentEvent->id,
                    'event_title' => $currentEvent->event_title,
                    'organization_id' => $currentOrganization->id,
                    'organization_name' => $currentOrganization->org_name
                ],
                'payment_data' => [
                    'method' => 'mtn_money',
                    'provider' => 'afribapay',
                    'country' => $phoneCountry,
                    'initiated_at' => now()->toISOString()
                ],
                'form_submission_data' => [
                    'user_agent' => request()->userAgent(),
                    'ip_address' => request()->ip(),
                    'submitted_at' => now()->toISOString(),
                    'total_fields' => count($validatedData)
                ]
            ];

            $transactionId = DB::connection('tenant')
                            ->table('payment_transactions')
                            ->insertGetId([
                                'registration_id' => NULL,
                                'transaction_reference' => $reference,
                                'amount' => $validatedData['ticket_price'],
                                'currency' => $validatedData['currency'],
                                'payment_method' => 'mobile_money',
                                'payment_provider' => 'MTN Money',
                                'fees' => 500,
                                'status' => 'processing',
                                'metadata' => json_encode($metadata),
                                'created_at' => now()
                            ]);

            // Préparer les données pour Afribapay
            $afribapayRequest = (object) [
                'senderOperator' => 'MTN Money',
                'transactionId' => $reference,
                'transactionAmount1' => $validatedData['ticket_price'],
                'senderPhone' => $fullPhoneNumber
            ];

            Log::info('Appel payinAfribapay pour MTN Money', [
                'request' => $afribapayRequest,
                'country' => $phoneCountry,
                'metadata_fields' => count($metadata['participant_data'])
            ]);

            $afribapayResponse = $this->payinAfribapay($afribapayRequest);

            if ($afribapayResponse->status_code === 200) {
                // Succès immédiat
                DB::connection('tenant')
                    ->table('payment_transactions')
                    ->where('id', $transactionId)
                    ->update([
                        'status' => 'completed',
                        'external_reference' => $afribapayResponse->data['reference'] ?? null,
                        'updated_at' => now()
                    ]);

                $this->createRegistrationFromTransaction($transactionId, 'completed');

                return response()->json([
                    'status' => 'success',
                    'message' => 'Paiement MTN Money confirmé avec succès.',
                    'redirect_url' => route('event.registration', [
                        'org_slug' => $currentOrganization->org_key,
                        'event_slug' => $currentEvent->event_slug
                    ]) . "?payment=success&ref=" . $reference
                ]);

            } elseif ($afribapayResponse->status_code === 202) {
                // Transaction en attente
                $instruction = $this->getPaymentInstruction('mtn', $phoneCountry);

                DB::connection('tenant')
                    ->table('payment_transactions')
                    ->where('id', $transactionId)
                    ->update([
                        'status' => 'processing',
                        'external_reference' => $afribapayResponse->data['reference'] ?? null,
                        'updated_at' => now()
                    ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Demande de paiement MTN Money initiée.',
                    'transaction_reference' => $reference,
                    'instruction' => $instruction
                ]);

            } else {
                // Échec
                DB::connection('tenant')
                    ->table('payment_transactions')
                    ->where('id', $transactionId)
                    ->update([
                        'status' => 'failed',
                        'metadata' => json_encode(array_merge($metadata, [
                            'error' => $afribapayResponse->message,
                            'afribapay_response' => $afribapayResponse->data
                        ]))
                    ]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Échec du paiement MTN Money. ' . $afribapayResponse->message,
                ], 400);
            }
        });

    } catch (\Exception $e) {
        Log::error('Erreur MTN Money Process', ['error' => $e->getMessage()]);
        return response()->json([
            'status' => 'error',
            'message' => 'Une erreur est survenue lors du traitement de votre demande.',
        ], 500);
    }
}

    /**
     * Traitement du paiement Moov Money via Afribapay avec gestion multi-pays
     */
    /* public function moovMoneyProcess(Request $request): JsonResponse
    {
        Log::info('Début de moovMoneyProcess via Afribapay', ['data_reçue' => $request->all()]);

        $currentOrganization = TenantHelper::getCurrentOrganization();
        $currentEvent = TenantHelper::getCurrentEvent();

        if (!$currentOrganization || !$currentEvent) {
            return response()->json([
                'status' => 'error',
                'message' => 'Contexte d\'organisation ou d\'événement manquant'
            ], 500);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'fullname' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'phone_country' => 'nullable|string|max:5',
            'phone_local' => 'nullable|string|max:15',
            'full_phone_number' => 'nullable|string|max:20',
            'organization' => 'nullable|string|max:255',
            'other_organization' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'quality' => 'nullable|string|max:255',
            'ticket_type_id' => 'required|integer',
            'ticket_name' => 'required|string',
            'ticket_price' => 'required|numeric|min:1',
            'currency' => 'required|string|max:10',
            'phone_number' => 'required|string|min:8|max:15',
            'payment_method' => 'required|string|in:moov'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Données invalides',
                'errors' => $validator->errors()
            ], 400);
        }

        $validatedData = $validator->validated();

        try {
            return TenantHelper::withTenantConnection(function() use ($validatedData, $currentEvent, $currentOrganization) {

                $ticketType = TicketType::on('tenant')->find($validatedData['ticket_type_id']);
                if (!$ticketType || !$ticketType->is_active || $ticketType->event_id != $currentEvent->id) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Type de ticket invalide ou non disponible.'
                    ], 400);
                }

                // NOUVEAU: Extraire et valider le code pays
                $phoneCountry = $this->extractPhoneCountry($validatedData);
                $fullPhoneNumber = $this->buildFullPhoneNumber($validatedData, $phoneCountry);

                $reference = "MOOV-" . strtoupper($currentOrganization->org_key) . "-" . strtoupper($currentEvent->event_slug) . "-" . uniqid();
                $position = $validatedData['position'] ?? $validatedData['quality'] ?? null;

                $transactionId = DB::connection('tenant')
                                ->table('payment_transactions')
                                ->insertGetId([
                                    'registration_id' => NULL,
                                    'transaction_reference' => $reference,
                                    'amount' => $validatedData['ticket_price'],
                                    'currency' => $validatedData['currency'],
                                    'payment_method' => 'mobile_money',
                                    'payment_provider' => 'Moov Money',
                                    'fees' => 500,
                                    'status' => 'processing',
                                    'metadata' => json_encode([
                                        'participant_data' => [
                                            'email' => $validatedData['email'],
                                            'fullname' => $validatedData['fullname'],
                                            'phone' => $validatedData['phone'],
                                            'phone_country' => $phoneCountry,
                                            'phone_local' => $validatedData['phone_number'],
                                            'full_phone_number' => $fullPhoneNumber,
                                            'organization' => $validatedData['organization'] ?: $validatedData['other_organization'],
                                            'position' => $position,
                                            'ticket_type_id' => $validatedData['ticket_type_id'],
                                            'ticket_name' => $validatedData['ticket_name'],
                                            'phone_number' => $validatedData['phone_number']
                                        ],
                                        'event_data' => [
                                            'event_id' => $currentEvent->id,
                                            'event_title' => $currentEvent->event_title,
                                            'organization_id' => $currentOrganization->id,
                                            'organization_name' => $currentOrganization->org_name
                                        ],
                                        'payment_data' => [
                                            'country' => $phoneCountry
                                        ],
                                        'initiated_at' => now()->toISOString()
                                    ]),
                                    'created_at' => now()
                                ]);

                // Préparer les données pour Afribapay avec le bon numéro
                $afribapayRequest = (object) [
                    'senderOperator' => 'Moov Money',
                    'transactionId' => $reference,
                    'transactionAmount1' => $validatedData['ticket_price'],
                    'senderPhone' => $fullPhoneNumber
                ];

                Log::info('Appel payinAfribapay pour Moov Money', [
                    'request' => $afribapayRequest,
                    'country' => $phoneCountry
                ]);

                $afribapayResponse = $this->payinAfribapay($afribapayRequest);

                if ($afribapayResponse->status_code === 200) {
                    // Succès immédiat
                    DB::connection('tenant')
                        ->table('payment_transactions')
                        ->where('id', $transactionId)
                        ->update([
                            'status' => 'completed',
                            'external_reference' => $afribapayResponse->data['reference'] ?? null,
                            'updated_at' => now()
                        ]);

                    $this->createRegistrationFromTransaction($transactionId, 'completed');

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Paiement Moov Money confirmé avec succès.',
                        'redirect_url' => route('event.registration', [
                            'org_slug' => $currentOrganization->org_key,
                            'event_slug' => $currentEvent->event_slug
                        ]) . "?payment=success&ref=" . $reference
                    ]);

                } elseif ($afribapayResponse->status_code === 202) {
                    // Transaction en attente avec message adapté au pays
                    $instruction = $this->getPaymentInstruction('moov', $phoneCountry);

                    DB::connection('tenant')
                        ->table('payment_transactions')
                        ->where('id', $transactionId)
                        ->update([
                            'status' => 'processing',
                            'external_reference' => $afribapayResponse->data['reference'] ?? null,
                            'updated_at' => now()
                        ]);

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Demande de paiement Moov Money initiée.',
                        'transaction_reference' => $reference,
                        'instruction' => $instruction
                    ]);

                } else {
                    // Échec
                    DB::connection('tenant')
                        ->table('payment_transactions')
                        ->where('id', $transactionId)
                        ->update([
                            'status' => 'failed',
                            'metadata' => json_encode([
                                'error' => $afribapayResponse->message,
                                'afribapay_response' => $afribapayResponse->data
                            ])
                        ]);

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Échec du paiement Moov Money. ' . $afribapayResponse->message,
                    ], 400);
                }
            });

        } catch (\Exception $e) {
            Log::error('Erreur Moov Money Process', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Une erreur est survenue lors du traitement de votre demande.',
            ], 500);
        }
    } */

    /**
 * MOOV MONEY PROCESS - RÉVISÉ AVEC COLLECTE COMPLÈTE
 */
public function moovMoneyProcess(Request $request): JsonResponse
{
    Log::info('Début de moovMoneyProcess via Afribapay avec données complètes', [
        'data_received' => $request->except(['_token']),
        'total_fields' => count($request->all())
    ]);

    $currentOrganization = TenantHelper::getCurrentOrganization();
    $currentEvent = TenantHelper::getCurrentEvent();

    if (!$currentOrganization || !$currentEvent) {
        return response()->json([
            'status' => 'error',
            'message' => 'Contexte d\'organisation ou d\'événement manquant'
        ], 500);
    }

    // Validation étendue identique aux autres
    $validator = Validator::make($request->all(), [
        // Champs de base obligatoires
        'email' => 'required|email|max:255',
        'fullname' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'organization' => 'required|string|max:255',
        'other_organization' => 'nullable|string|max:255',

        // Champs téléphone
        'phone_country' => 'nullable|string|max:5',
        'phone_local' => 'nullable|string|max:15',
        'full_phone_number' => 'nullable|string|max:20',
        'phone_number' => 'required|string|min:8|max:15',

        // Champs ticket
        'ticket_type_id' => 'required|integer',
        'ticket_name' => 'required|string',
        'ticket_price' => 'required|numeric|min:1',
        'currency' => 'required|string|max:10',

        // Champs professionnels
        'position' => 'nullable|string|max:255',
        'quality' => 'nullable|string|max:255',
        'jci_quality' => 'nullable|string|max:100',
        'business_sector' => 'nullable|string|max:100',
        'years_experience' => 'nullable|string|max:50',

        // Informations personnelles
        'gender' => 'nullable|string|max:20',
        'nationality' => 'nullable|string|max:100',

        // Préférences alimentaires
        'breakfast_preference' => 'nullable',
        'breakfast_preference_other' => 'nullable|string|max:100',
        'lunch_preference' => 'nullable',
        'lunch_preference_other' => 'nullable|string|max:100',
        'dinner_preference' => 'nullable',
        'dinner_preference_other' => 'nullable|string|max:100',

        // Autres préférences
        'dormitory_sharing' => 'nullable|string|max:100',
        'networking_interests' => 'nullable',
        'dietary_requirements' => 'nullable|string|max:500',
        'special_needs' => 'nullable|string|max:500',

        'payment_method' => 'required|string|in:moov'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Données invalides',
            'errors' => $validator->errors()
        ], 400);
    }

    $validatedData = $validator->validated();

    try {
        return TenantHelper::withTenantConnection(function() use ($validatedData, $currentEvent, $currentOrganization) {

            $ticketType = TicketType::on('tenant')->find($validatedData['ticket_type_id']);
            if (!$ticketType || !$ticketType->is_active || $ticketType->event_id != $currentEvent->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Type de ticket invalide ou non disponible.'
                ], 400);
            }

            // Extraire et valider le code pays
            $phoneCountry = $this->extractPhoneCountry($validatedData);
            $fullPhoneNumber = $this->buildFullPhoneNumber($validatedData, $phoneCountry);

            $reference = "MOOV-" . strtoupper($currentOrganization->org_key) . "-" . strtoupper($currentEvent->event_slug) . "-" . uniqid();
            $position = $validatedData['position'] ?? $validatedData['quality'] ?? null;
            $organization = $validatedData['organization'] ?: $validatedData['other_organization'];

            // Métadonnées COMPLÈTES pour Moov
            $metadata = [
                'participant_data' => [
                    // Informations de base
                    'email' => $validatedData['email'],
                    'fullname' => $validatedData['fullname'],
                    'phone' => $validatedData['phone'],
                    'phone_country' => $phoneCountry,
                    'phone_local' => $validatedData['phone_number'],
                    'full_phone_number' => $fullPhoneNumber,
                    'organization' => $organization,
                    'position' => $position,

                    // Informations étendues
                    'gender' => $validatedData['gender'] ?? null,
                    'nationality' => $validatedData['nationality'] ?? null,
                    'jci_quality' => $validatedData['jci_quality'] ?? null,
                    'business_sector' => $validatedData['business_sector'] ?? null,
                    'years_experience' => $validatedData['years_experience'] ?? null,

                    // Préférences alimentaires
                    'breakfast_preference' => is_array($validatedData['breakfast_preference'] ?? null) ?
                        $validatedData['breakfast_preference'] :
                        (isset($validatedData['breakfast_preference']) ? [$validatedData['breakfast_preference']] : null),
                    'breakfast_preference_other' => $validatedData['breakfast_preference_other'] ?? null,
                    'lunch_preference' => is_array($validatedData['lunch_preference'] ?? null) ?
                        $validatedData['lunch_preference'] :
                        (isset($validatedData['lunch_preference']) ? [$validatedData['lunch_preference']] : null),
                    'lunch_preference_other' => $validatedData['lunch_preference_other'] ?? null,
                    'dinner_preference' => is_array($validatedData['dinner_preference'] ?? null) ?
                        $validatedData['dinner_preference'] :
                        (isset($validatedData['dinner_preference']) ? [$validatedData['dinner_preference']] : null),
                    'dinner_preference_other' => $validatedData['dinner_preference_other'] ?? null,

                    // Autres préférences
                    'dormitory_sharing' => $validatedData['dormitory_sharing'] ?? null,
                    'networking_interests' => is_array($validatedData['networking_interests'] ?? null) ?
                        $validatedData['networking_interests'] :
                        (isset($validatedData['networking_interests']) ? [$validatedData['networking_interests']] : null),
                    'dietary_requirements' => $validatedData['dietary_requirements'] ?? null,
                    'special_needs' => $validatedData['special_needs'] ?? null,

                    // Données ticket
                    'ticket_type_id' => $validatedData['ticket_type_id'],
                    'ticket_name' => $validatedData['ticket_name'],
                    'phone_number' => $validatedData['phone_number']
                ],
                'event_data' => [
                    'event_id' => $currentEvent->id,
                    'event_title' => $currentEvent->event_title,
                    'organization_id' => $currentOrganization->id,
                    'organization_name' => $currentOrganization->org_name
                ],
                'payment_data' => [
                    'method' => 'moov_money',
                    'provider' => 'afribapay',
                    'country' => $phoneCountry,
                    'initiated_at' => now()->toISOString()
                ],
                'form_submission_data' => [
                    'user_agent' => request()->userAgent(),
                    'ip_address' => request()->ip(),
                    'submitted_at' => now()->toISOString(),
                    'total_fields' => count($validatedData)
                ]
            ];

            $transactionId = DB::connection('tenant')
                            ->table('payment_transactions')
                            ->insertGetId([
                                'registration_id' => NULL,
                                'transaction_reference' => $reference,
                                'amount' => $validatedData['ticket_price'],
                                'currency' => $validatedData['currency'],
                                'payment_method' => 'mobile_money',
                                'payment_provider' => 'Moov Money',
                                'fees' => 500,
                                'status' => 'processing',
                                'metadata' => json_encode($metadata),
                                'created_at' => now()
                            ]);

            // Préparer les données pour Afribapay
            $afribapayRequest = (object) [
                'senderOperator' => 'Moov Money',
                'transactionId' => $reference,
                'transactionAmount1' => $validatedData['ticket_price'],
                'senderPhone' => $fullPhoneNumber
            ];

            Log::info('Appel payinAfribapay pour Moov Money', [
                'request' => $afribapayRequest,
                'country' => $phoneCountry,
                'metadata_fields' => count($metadata['participant_data'])
            ]);

            $afribapayResponse = $this->payinAfribapay($afribapayRequest);

            if ($afribapayResponse->status_code === 200) {
                // Succès immédiat
                DB::connection('tenant')
                    ->table('payment_transactions')
                    ->where('id', $transactionId)
                    ->update([
                        'status' => 'completed',
                        'external_reference' => $afribapayResponse->data['reference'] ?? null,
                        'updated_at' => now()
                    ]);

                $this->createRegistrationFromTransaction($transactionId, 'completed');

                return response()->json([
                    'status' => 'success',
                    'message' => 'Paiement Moov Money confirmé avec succès.',
                    'redirect_url' => route('event.registration', [
                        'org_slug' => $currentOrganization->org_key,
                        'event_slug' => $currentEvent->event_slug
                    ]) . "?payment=success&ref=" . $reference
                ]);

            } elseif ($afribapayResponse->status_code === 202) {
                // Transaction en attente
                $instruction = $this->getPaymentInstruction('moov', $phoneCountry);

                DB::connection('tenant')
                    ->table('payment_transactions')
                    ->where('id', $transactionId)
                    ->update([
                        'status' => 'processing',
                        'external_reference' => $afribapayResponse->data['reference'] ?? null,
                        'updated_at' => now()
                    ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Demande de paiement Moov Money initiée.',
                    'transaction_reference' => $reference,
                    'instruction' => $instruction
                ]);

            } else {
                // Échec
                DB::connection('tenant')
                    ->table('payment_transactions')
                    ->where('id', $transactionId)
                    ->update([
                        'status' => 'failed',
                        'metadata' => json_encode(array_merge($metadata, [
                            'error' => $afribapayResponse->message,
                            'afribapay_response' => $afribapayResponse->data
                        ]))
                    ]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Échec du paiement Moov Money. ' . $afribapayResponse->message,
                ], 400);
            }
        });

    } catch (\Exception $e) {
        Log::error('Erreur Moov Money Process', ['error' => $e->getMessage()]);
        return response()->json([
            'status' => 'error',
            'message' => 'Une erreur est survenue lors du traitement de votre demande.',
        ], 500);
    }
}

    private function extractPhoneCountry(array $validatedData): string
    {
        // Priorité 1: Champ phone_country dédié
        if (!empty($validatedData['phone_country'])) {
            return $this->normalizeCountryCode($validatedData['phone_country']);
        }

        // Priorité 2: Extraire du numéro complet
        if (!empty($validatedData['full_phone_number']) && str_starts_with($validatedData['full_phone_number'], '+')) {
            preg_match('/^(\+\d{3})/', $validatedData['full_phone_number'], $matches);
            if (isset($matches[1])) {
                return $this->normalizeCountryCode($matches[1]);
            }
        }

        // Priorité 3: Extraire du champ phone principal
        if (!empty($validatedData['phone']) && str_starts_with($validatedData['phone'], '+')) {
            preg_match('/^(\+\d{3})/', $validatedData['phone'], $matches);
            if (isset($matches[1])) {
                return $this->normalizeCountryCode($matches[1]);
            }
        }

        // Valeur par défaut
        return '+225';
    }


    private function normalizeCountryCode(string $code): string
    {
        // Nettoyer le code
        $code = trim($code);

        // Ajouter + si manquant
        if (!str_starts_with($code, '+')) {
            $code = '+' . $code;
        }

        // Corriger les codes mal formatés
        if (str_starts_with($code, '+229')) {
            return '+229';
        } elseif (str_starts_with($code, '+226')) {
            return '+226';
        } elseif (str_starts_with($code, '+223')) {
            return '+223';
        } elseif (str_starts_with($code, '+228')) {
            return '+228';
        }

        // Vérifier si c'est un code valide
        $validCodes = ['+225', '+229', '+226', '+223', '+228'];
        if (in_array($code, $validCodes)) {
            return $code;
        }

        // Valeur par défaut
        return '+225';
    }


    private function buildFullPhoneNumber(array $validatedData, string $phoneCountry): string
    {
        $phoneNumber = $validatedData['phone_number'];

        // Nettoyer le numéro local
        $cleanPhone = preg_replace('/[^\d]/', '', $phoneNumber);

        // Construire le numéro complet
        return $phoneCountry . $cleanPhone;
    }


    public function waveProcess(Request $request): JsonResponse
    {
        Log::info('Début de waveProcess via Afribapay', ['data_reçue' => $request->all()]);

        $currentOrganization = TenantHelper::getCurrentOrganization();
        $currentEvent = TenantHelper::getCurrentEvent();

        if (!$currentOrganization || !$currentEvent) {
            return response()->json([
                'status' => 'error',
                'message' => 'Contexte d\'organisation ou d\'événement manquant'
            ], 500);
        }

        $validator = Validator::make($request->all(), [
            // Champs de base obligatoires
            'email' => 'required|email|max:255',
            'fullname' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'organization' => 'nullable|string|max:255',
            'other_organization' => 'nullable|string|max:255',

            // Champs téléphone
            'phone_country' => 'nullable|string|max:5',
            'phone_local' => 'nullable|string|max:15',
            'full_phone_number' => 'nullable|string|max:20',
            'phone_number' => 'required|string|min:8|max:15',

            // Champs ticket
            'ticket_type_id' => 'required|integer',
            'ticket_name' => 'required|string',
            'ticket_price' => 'required|numeric|min:1',
            'currency' => 'required|string|max:10',

            // Champs professionnels
            'position' => 'nullable|string|max:255',
            'quality' => 'nullable|string|max:255',
            'jci_quality' => 'nullable|string|max:100',
            'business_sector' => 'nullable|string|max:100',
            'years_experience' => 'nullable|string|max:50',

            // Informations personnelles
            'gender' => 'nullable|string|max:20',
            'nationality' => 'nullable|string|max:100',

            // Préférences alimentaires
            'breakfast_preference' => 'nullable',
            'breakfast_preference_other' => 'nullable|string|max:100',
            'lunch_preference' => 'nullable',
            'lunch_preference_other' => 'nullable|string|max:100',
            'dinner_preference' => 'nullable',
            'dinner_preference_other' => 'nullable|string|max:100',

            // Autres préférences
            'dormitory_sharing' => 'nullable|string|max:100',
            'networking_interests' => 'nullable',
            'dietary_requirements' => 'nullable|string|max:500',
            'special_needs' => 'nullable|string|max:500',

            'payment_method' => 'required|string|in:wave'
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Données invalides',
                'errors' => $validator->errors()
            ], 400);
        }

        $validatedData = $validator->validated();

        try {
            return TenantHelper::withTenantConnection(function() use ($validatedData, $currentEvent, $currentOrganization) {

                $ticketType = TicketType::on('tenant')->find($validatedData['ticket_type_id']);
                if (!$ticketType || !$ticketType->is_active || $ticketType->event_id != $currentEvent->id) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Type de ticket invalide ou non disponible.'
                    ], 400);
                }

                $phoneCountry = $this->extractPhoneCountry($validatedData);
                $fullPhoneNumber = $this->buildFullPhoneNumber($validatedData, $phoneCountry);

                $reference = "Wave-" . strtoupper($currentOrganization->org_key) . "-" . strtoupper($currentEvent->event_slug) . "-" . uniqid();
                $position = $validatedData['position'] ?? $validatedData['quality'] ?? null;
                //$organization = $validatedData['organization'] ?? : $validatedData['other_organization'];
                $organization = isset($validatedData['organization']) && !empty($validatedData['organization']) 
                ? $validatedData['organization'] 
                : ($validatedData['other_organization'] ?? null);
                // Métadonnées COMPLÈTES pour MTN
                $metadata = [
                    'participant_data' => [
                        // Informations de base
                        'email' => $validatedData['email'],
                        'fullname' => $validatedData['fullname'],
                        'phone' => $validatedData['phone'],
                        'phone_country' => $phoneCountry,
                        'phone_local' => $validatedData['phone_number'],
                        'full_phone_number' => $fullPhoneNumber,
                        'organization' => $organization,
                        'position' => $position,

                        // Informations étendues
                        'gender' => $validatedData['gender'] ?? null,
                        'nationality' => $validatedData['nationality'] ?? null,
                        'jci_quality' => $validatedData['jci_quality'] ?? null,
                        'business_sector' => $validatedData['business_sector'] ?? null,
                        'years_experience' => $validatedData['years_experience'] ?? null,

                        // Préférences alimentaires
                        'breakfast_preference' => is_array($validatedData['breakfast_preference'] ?? null) ?
                            $validatedData['breakfast_preference'] :
                            (isset($validatedData['breakfast_preference']) ? [$validatedData['breakfast_preference']] : null),
                        'breakfast_preference_other' => $validatedData['breakfast_preference_other'] ?? null,
                        'lunch_preference' => is_array($validatedData['lunch_preference'] ?? null) ?
                            $validatedData['lunch_preference'] :
                            (isset($validatedData['lunch_preference']) ? [$validatedData['lunch_preference']] : null),
                        'lunch_preference_other' => $validatedData['lunch_preference_other'] ?? null,
                        'dinner_preference' => is_array($validatedData['dinner_preference'] ?? null) ?
                            $validatedData['dinner_preference'] :
                            (isset($validatedData['dinner_preference']) ? [$validatedData['dinner_preference']] : null),
                        'dinner_preference_other' => $validatedData['dinner_preference_other'] ?? null,

                        // Autres préférences
                        'dormitory_sharing' => $validatedData['dormitory_sharing'] ?? null,
                        'networking_interests' => is_array($validatedData['networking_interests'] ?? null) ?
                            $validatedData['networking_interests'] :
                            (isset($validatedData['networking_interests']) ? [$validatedData['networking_interests']] : null),
                        'dietary_requirements' => $validatedData['dietary_requirements'] ?? null,
                        'special_needs' => $validatedData['special_needs'] ?? null,

                        // Données ticket
                        'ticket_type_id' => $validatedData['ticket_type_id'],
                        'ticket_name' => $validatedData['ticket_name'],
                        'phone_number' => $validatedData['phone_number']
                    ],
                    'event_data' => [
                        'event_id' => $currentEvent->id,
                        'event_title' => $currentEvent->event_title,
                        'organization_id' => $currentOrganization->id,
                        'organization_name' => $currentOrganization->org_name
                    ],
                    'payment_data' => [
                        'method' => 'wave',
                        'provider' => 'afribapay',
                        'country' => $phoneCountry,
                        'initiated_at' => now()->toISOString()
                    ],
                    'form_submission_data' => [
                        'user_agent' => request()->userAgent(),
                        'ip_address' => request()->ip(),
                        'submitted_at' => now()->toISOString(),
                        'total_fields' => count($validatedData)
                    ]
                ];

                $transactionId = DB::connection('tenant')
                                ->table('payment_transactions')
                                ->insertGetId([
                                    'registration_id' => NULL,
                                    'transaction_reference' => $reference,
                                    'amount' => $validatedData['ticket_price'],
                                    'currency' => $validatedData['currency'],
                                    'payment_method' => 'mobile_money',
                                    'payment_provider' => 'Wave',
                                    'fees' => 500,
                                    'status' => 'pending',
                                    'metadata' => json_encode($metadata),
                                    'created_at' => now()
                                ]);

                // Préparer les données pour Afribapay
                $afribapayRequest = (object) [
                    'senderOperator' => 'Wave',
                    'transactionId' => $reference,
                    'transactionAmount1' => $validatedData['ticket_price'],
                    'senderPhone' => '+225' . $validatedData['phone_number']
                ];

                Log::info('Appel payinAfribapay pour Wave', [
                    'request' => $afribapayRequest
                ]);

                $afribapayResponse = $this->payinAfribapay($afribapayRequest);

                if ($afribapayResponse->status_code === 200 && isset($afribapayResponse->data['waveLaunchUrl'])) {
                    // Redirection Wave
                    DB::connection('tenant')
                        ->table('payment_transactions')
                        ->where('id', $transactionId)
                        ->update([
                            'status' => 'processing',
                            'external_reference' => $afribapayResponse->data['reference'] ?? null,
                            'metadata' => json_encode(array_merge(
                                json_decode(DB::connection('tenant')->table('payment_transactions')->where('id', $transactionId)->value('metadata'), true),
                                ['afribapay_response' => $afribapayResponse->data]
                            )),
                            'updated_at' => now()
                        ]);

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Lien de paiement Wave généré avec succès.',
                        'url' => $afribapayResponse->data['waveLaunchUrl'],
                        'transaction_reference' => $reference
                    ]);

                } else {
                    // Échec
                    DB::connection('tenant')
                        ->table('payment_transactions')
                        ->where('id', $transactionId)
                        ->update([
                            'status' => 'failed',
                            'metadata' => json_encode([
                                'error' => $afribapayResponse->message,
                                'afribapay_response' => $afribapayResponse->data
                            ])
                        ]);

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Impossible de créer la session de paiement Wave. ' . $afribapayResponse->message,
                    ], 500);
                }
            });

        } catch (\Exception $e) {
            Log::error('Erreur Wave Process', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Une erreur est survenue lors du traitement de votre demande.',
            ], 500);
        }
    }

    private function getAfribapayAccessToken()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.afribapay.com/v1/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Basic cGtfYTE5YzdiY2E2OTljNjA1Njc5MWM2YmZjMDhiYjhlZGE6c2tfcndnMnliZkN6am54eFdrZGVS',
            ),
        ));

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            \Log::error('Erreur CURL lors de l\'appel à Afribapay', [
                'error' => $error,
                'url' => $url ?? 'N/A',
                'timestamp' => now()->toDateTimeString()
            ]);
           
        }

        $data = json_decode($response, true);

        \Log::info('Réponse Afribapay reçue', [
            'response_raw' => $response,
            'response_decoded' => $data,
            'has_access_token' => isset($data['data']['access_token']),
            'timestamp' => now()->toDateTimeString()
        ]);

        if (isset($data['data']['access_token'])) {
            \Log::info('Token Afribapay récupéré avec succès', [
                'token' => substr($data['data']['access_token'], 0, 20) . '...', // Log partiel pour sécurité
                'token_length' => strlen($data['data']['access_token']),
                'timestamp' => now()->toDateTimeString()
            ]);
            
            return [
                'token' => $data['data']['access_token']
            ];
        } else {
            \Log::error('Token Afribapay absent de la réponse', [
                'response_data' => $data,
                'data_keys' => isset($data['data']) ? array_keys($data['data']) : 'data key not found',
                'full_response' => $response,
                'timestamp' => now()->toDateTimeString()
            ]);
            
           
        }
    }

    public function checkTransactionStatus(Request $request): JsonResponse
    {
        $request->validate([
            "reference" => "required|string",
            "operator" => "nullable|string",
        ]);

        $currentOrganization = TenantHelper::getCurrentOrganization();
        $currentEvent = TenantHelper::getCurrentEvent();

        if (!$currentOrganization || !$currentEvent) {
            return response()->json([
                'status' => 'error',
                'message' => 'Contexte d\'organisation ou d\'événement manquant'
            ], 500);
        }

        try {
            return TenantHelper::withTenantConnection(function() use ($request) {

                $reference = $request->input('reference');

                // Rechercher la transaction dans notre système
                $transaction = DB::connection('tenant')
                    ->table('payment_transactions')
                    ->where('transaction_reference', $reference)
                    ->first();

                if (!$transaction) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Transaction non trouvée'
                    ], 404);
                }

                // Si la transaction est déjà complétée ou échouée, retourner le statut local
                if (in_array($transaction->status, ['completed', 'failed'])) {
                    return response()->json([
                        'status' => $transaction->status,
                        'message' => $transaction->status === 'completed' ? 'Paiement confirmé' : 'Paiement échoué'
                    ]);
                }

                // Vérifier auprès d'Afribapay
                $result = $this->getAfribapayAccessToken();

                if (!isset($result['token'])) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Impossible d\'obtenir le token d\'authentification'
                    ], 500);
                }

                $authorization_token = $result['token'];

                Log::info('🟡 Traitement pour afribapay - checkTransactionStatus');

                $url = 'https://api.afribapay.com/v1/status?order_id=' . $reference;
                Log::info('🌐 URL appelée pour afribapay', ['url' => $url]);

                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_SSL_VERIFYHOST => 2,
                    CURLOPT_SSL_VERIFYPEER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => [
                        'authorization: Bearer '. $authorization_token,
                        'Content-Type: application/json',
                    ],
                ]);

                $response = curl_exec($curl);

                if (curl_errno($curl)) {
                    $curlError = curl_error($curl);
                    curl_close($curl);
                    Log::error('❌ Erreur CURL afribapay', ['error' => $curlError]);
                    return response()->json([
                        'status' => 'error',
                        'message' => 'La vérification de transaction a échoué. Veuillez réessayer.'
                    ], 500);
                }

                curl_close($curl);
                Log::info('📩 Réponse brute de afribapay', ['response' => $response]);

                $responseArray = json_decode($response, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::error('❌ Erreur lors du décodage JSON afribapay', [
                        'error' => json_last_error_msg(),
                        'response_preview' => substr($response, 0, 200)
                    ]);
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Erreur de traitement de la réponse.'
                    ], 500);
                }

                // Accès direct aux données
                $status = $responseArray['data']['status'] ?? null;
                if (!$status) {
                    Log::error('❌ Statut manquant dans la réponse afribapay', [
                        'response_structure' => array_keys($responseArray),
                        'data_structure' => isset($responseArray['data']) ? array_keys($responseArray['data']) : 'data key missing'
                    ]);
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Statut non disponible dans la réponse.'
                    ], 500);
                }

                Log::info('📌 Statut reçu de afribapay', ['status' => $status]);

                // Mettre à jour notre base de données selon le statut
                switch (strtolower($status)) {
                    case 'success':
                    case 'successful':
                    case 'completed':
                        DB::connection('tenant')
                            ->table('payment_transactions')
                            ->where('id', $transaction->id)
                            ->update([
                                'status' => 'completed',
                                'external_reference' => $responseArray['data']['transaction_id'] ?? null,
                                'updated_at' => now()
                            ]);

                        // Créer l'inscription si pas encore fait
                        if (!$transaction->registration_id) {
                            $this->createRegistrationFromTransaction($transaction->id, 'completed');
                        }

                        return response()->json([
                            'status' => 'success',
                            'message' => 'Transaction confirmée avec succès',
                            'data' => ['status' => $status]
                        ]);

                    case 'failed':
                    case 'error':
                    case 'cancelled':
                        DB::connection('tenant')
                            ->table('payment_transactions')
                            ->where('id', $transaction->id)
                            ->update(['status' => 'failed', 'updated_at' => now()]);

                        return response()->json([
                            'status' => 'failed',
                            'message' => 'La transaction a échoué',
                            'data' => ['status' => $status]
                        ]);

                    case 'pending':
                    case 'processing':
                    default:
                        return response()->json([
                            'status' => 'pending',
                            'message' => 'Transaction en cours de traitement',
                            'data' => ['status' => $status]
                        ]);
                }
            });

        } catch (\Exception $e) {
            Log::error('❌ Erreur checkTransactionStatus', [
                'error' => $e->getMessage(),
                'reference' => $request->input('reference')
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la vérification du statut'
            ], 500);
        }
    }

    public function checkWaveStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_reference' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Référence de transaction requise'
            ], 400);
        }

        $transaction = DB::connection('tenant')
                        ->table('payment_transactions')
                        ->where('transaction_reference', $request->transaction_reference)
                        ->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction non trouvée'
            ], 404);
        }

        // Si la transaction est en cours, vérifier auprès de Wave
        if ($transaction->status === 'processing' && $transaction->external_reference) {
            $waveStatus = $this->checkWaveTransactionStatus($transaction->external_reference);

            if ($waveStatus && $waveStatus['status'] !== $transaction->status) {
                // Mettre à jour le statut local
                $this->updateTransactionStatus($transaction, $waveStatus);
                $transaction->status = $waveStatus['status'];
            }
        }

        return response()->json([
            'success' => true,
            'status' => $transaction->status,
            'amount' => $transaction->amount,
            'created_at' => $transaction->created_at
        ]);
    }

    /**
     * Traiter un paiement manuel
     */
    public function processManualPayment(Request $request, $registrationData)
    {
        $organization = app('current.organization');
        $event = app('current.event');

        // Récupérer l'inscription
        $registration = DB::connection('tenant')
                         ->table('registrations')
                         ->where('id', $registrationData['id'])
                         ->first();

        if (!$registration) {
            return back()->withErrors(['error' => 'Inscription non trouvée.']);
        }

        // Créer une transaction manuelle
        $transactionRef = 'MANUAL-' . strtoupper(Str::random(8)) . '-' . time();

        DB::connection('tenant')->table('payment_transactions')->insert([
            'registration_id' => $registration->id,
            'transaction_reference' => $transactionRef,
            'amount' => $registration->ticket_price,
            'currency' => 'FCFA',
            'payment_method' => 'manual',
            'status' => 'pending',
            'metadata' => json_encode([
                'payment_reference' => $request->payment_reference,
                'notes' => 'Paiement manuel en attente de vérification'
            ]),
            'created_at' => now()
        ]);

        // Marquer l'inscription comme en attente de validation
        DB::connection('tenant')
          ->table('registrations')
          ->where('id', $registration->id)
          ->update([
              'payment_status' => 'pending',
              'status' => 'pending'
          ]);

        // Envoyer une notification à l'organisation
        $this->notifyManualPaymentPending($registration, $transactionRef);

        return redirect()->route('event.success', [
            'org_slug' => $request->route('org_slug'),
            'event_slug' => $request->route('event_slug')
        ])->with('info', 'Votre inscription est enregistrée. Le paiement sera vérifié sous peu.');
    }

    /**
     * Envoyer un ticket manuellement
     */
    public function sendManualTicket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'registration_id' => 'required|integer',
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $organization = app('current.organization');
        $event = app('current.event');

        // Récupérer l'inscription
        $registration = DB::connection('tenant')
                         ->table('registrations')
                         ->join('ticket_types', 'registrations.ticket_type_id', '=', 'ticket_types.id')
                         ->where('registrations.id', $request->registration_id)
                         ->where('registrations.event_id', $event->id)
                         ->select('registrations.*', 'ticket_types.ticket_name')
                         ->first();

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'Inscription non trouvée'
            ], 404);
        }

        try {
            // Générer et envoyer le ticket
            $ticketPath = $this->generateTicketPDF($registration, $event, $organization);
            $this->sendTicketEmail($registration, $event, $organization, $ticketPath);

            // Marquer comme confirmé si ce n'était pas déjà fait
            if ($registration->status !== 'confirmed') {
                DB::connection('tenant')
                  ->table('registrations')
                  ->where('id', $registration->id)
                  ->update([
                      'status' => 'confirmed',
                      'confirmation_date' => now()
                  ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Ticket envoyé avec succès'
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending manual ticket', [
                'registration_id' => $request->registration_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi du ticket'
            ], 500);
        }
    }

    /**
     * Valider un paiement manuellement (pour les administrateurs)
     */
    public function validateManualPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|integer',
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $transaction = DB::connection('tenant')
                        ->table('payment_transactions')
                        ->where('id', $request->transaction_id)
                        ->first();

        if (!$transaction) {
            return back()->withErrors(['error' => 'Transaction non trouvée.']);
        }

        if ($request->action === 'approve') {
            $this->approveManualPayment($transaction, $request->notes);
            $message = 'Paiement approuvé avec succès.';
        } else {
            $this->rejectManualPayment($transaction, $request->notes);
            $message = 'Paiement rejeté.';
        }

        return back()->with('success', $message);
    }

    private function initiateWavePayment($data)
    {
        try {
            $waveApiUrl = env('WAVE_API_URL', 'https://api.wave.com/v1/checkout/sessions');
            $waveApiKey = env('WAVE_API_KEY');

            $payload = [
                'amount' => $data['amount'],
                'currency' => 'XOF', // Franc CFA
                'success_url' => $data['callback_url'],
                'cancel_url' => $data['callback_url'],
                'metadata' => [
                    'merchant_reference' => $data['reference'],
                    'description' => $data['description']
                ]
            ];

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $waveApiUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $waveApiKey,
                    'Content-Type: application/json'
                ]
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                $responseData = json_decode($response, true);
                return [
                    'success' => true,
                    'wave_id' => $responseData['id'],
                    'checkout_url' => $responseData['checkout_url'] ?? null
                ];
            } else {
                Log::error('Wave API Error', [
                    'http_code' => $httpCode,
                    'response' => $response
                ]);

                return [
                    'success' => false,
                    'message' => 'Erreur lors de la communication avec Wave'
                ];
            }

        } catch (\Exception $e) {
            Log::error('Wave payment initiation failed', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);

            return [
                'success' => false,
                'message' => 'Erreur technique lors de l\'initiation du paiement'
            ];
        }
    }

    /**
     * Valider la signature Wave
     */
    private function validateWaveSignature($request)
    {
        // Implémenter la validation de signature selon la documentation Wave
        // Ceci est un exemple simplifié
        $signature = $request->header('X-Wave-Signature');
        $payload = $request->getContent();
        $secret = env('WAVE_WEBHOOK_SECRET');

        if (!$signature || !$secret) {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Traiter un paiement réussi
     */
    private function processSuccessfulPayment($transaction, $callbackData)
    {
        DB::beginTransaction();

        try {
            // Mettre à jour la transaction
            DB::connection('tenant')
              ->table('payment_transactions')
              ->where('id', $transaction->id)
              ->update([
                  'status' => 'completed',
                  'payment_date' => now(),
                  'processed_date' => now(),
                  'metadata' => json_encode(array_merge(
                      json_decode($transaction->metadata, true) ?? [],
                      ['callback_data' => $callbackData]
                  ))
              ]);

            // Mettre à jour l'inscription
            $registration = DB::connection('tenant')
                             ->table('registrations')
                             ->where('id', $transaction->registration_id)
                             ->first();

            DB::connection('tenant')
              ->table('registrations')
              ->where('id', $transaction->registration_id)
              ->update([
                  'payment_status' => 'paid',
                  'status' => 'confirmed',
                  'amount_paid' => $transaction->amount,
                  'confirmation_date' => now()
              ]);

            // Mettre à jour le compteur de tickets vendus
            DB::connection('tenant')
              ->table('ticket_types')
              ->where('id', $registration->ticket_type_id)
              ->increment('quantity_sold');

            DB::commit();

            // Générer et envoyer le ticket
            $this->sendConfirmationTicket($registration);

            Log::info('Payment processed successfully', [
                'transaction_id' => $transaction->id,
                'registration_id' => $transaction->registration_id
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error processing successful payment', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Traiter un paiement échoué
     */
    private function processFailedPayment($transaction, $callbackData)
    {
        DB::connection('tenant')
          ->table('payment_transactions')
          ->where('id', $transaction->id)
          ->update([
              'status' => 'failed',
              'processed_date' => now(),
              'metadata' => json_encode(array_merge(
                  json_decode($transaction->metadata, true) ?? [],
                  ['callback_data' => $callbackData]
              ))
          ]);

        Log::info('Payment failed', [
            'transaction_id' => $transaction->id,
            'callback_data' => $callbackData
        ]);
    }

    /**
     * Générer un ticket PDF
     */
    private function generateTicketPDF($registration, $event, $organization)
    {
        // Générer un QR code pour la vérification
        $qrData = json_encode([
            'reg_id' => $registration->id,
            'reg_number' => $registration->registration_number,
            'event_id' => $event->id,
            'hash' => hash('sha256', $registration->registration_number . $event->id)
        ]);

        $qrCode = QrCode::format('png')->size(200)->generate($qrData);

        // Ici vous pouvez utiliser une bibliothèque comme TCPDF ou DOMPDF
        // pour générer le PDF du ticket

        $ticketData = [
            'registration' => $registration,
            'event' => $event,
            'organization' => $organization,
            'qr_code' => base64_encode($qrCode)
        ];

        // Générer le PDF (exemple simplifié)
        $filename = "ticket_{$registration->registration_number}.pdf";
        $filepath = storage_path("app/tickets/{$filename}");

        // Assurer que le dossier existe
        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        // Ici vous générez le PDF avec vos données
        // file_put_contents($filepath, $pdfContent);

        return $filepath;
    }

    /**
     * Envoyer le ticket de confirmation
     */
    private function sendConfirmationTicket($registration)
    {
        $organization = app('current.organization');
        $event = app('current.event');

        try {
            $ticketPath = $this->generateTicketPDF($registration, $event, $organization);
            $this->sendTicketEmail($registration, $event, $organization, $ticketPath);
        } catch (\Exception $e) {
            Log::error('Error sending confirmation ticket', [
                'registration_id' => $registration->id,
                'error' => $e->getMessage()
            ]);
        }
    }


    /**
     * Approuver un paiement manuel
     */
    private function approveManualPayment($transaction, $notes)
    {
        DB::beginTransaction();

        try {
            // Mettre à jour la transaction
            DB::connection('tenant')
              ->table('payment_transactions')
              ->where('id', $transaction->id)
              ->update([
                  'status' => 'completed',
                  'payment_date' => now(),
                  'processed_date' => now(),
                  'metadata' => json_encode(array_merge(
                      json_decode($transaction->metadata, true) ?? [],
                      ['approval_notes' => $notes, 'approved_at' => now()->toISOString()]
                  ))
              ]);

            // Mettre à jour l'inscription
            $registration = DB::connection('tenant')
                             ->table('registrations')
                             ->where('id', $transaction->registration_id)
                             ->first();

            DB::connection('tenant')
              ->table('registrations')
              ->where('id', $transaction->registration_id)
              ->update([
                  'payment_status' => 'paid',
                  'status' => 'confirmed',
                  'amount_paid' => $transaction->amount,
                  'confirmation_date' => now()
              ]);

            // Mettre à jour le compteur de tickets
            DB::connection('tenant')
              ->table('ticket_types')
              ->where('id', $registration->ticket_type_id)
              ->increment('quantity_sold');

            DB::commit();

            // Envoyer le ticket
            $this->sendConfirmationTicket($registration);

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Rejeter un paiement manuel
     */
    private function rejectManualPayment($transaction, $notes)
    {
        DB::connection('tenant')
          ->table('payment_transactions')
          ->where('id', $transaction->id)
          ->update([
              'status' => 'failed',
              'processed_date' => now(),
              'metadata' => json_encode(array_merge(
                  json_decode($transaction->metadata, true) ?? [],
                  ['rejection_notes' => $notes, 'rejected_at' => now()->toISOString()]
              ))
          ]);

        // Optionnel : notifier l'utilisateur du rejet
        $registration = DB::connection('tenant')
                         ->table('registrations')
                         ->where('id', $transaction->registration_id)
                         ->first();

        // Envoyer une notification de rejet
        // $this->sendPaymentRejectionNotification($registration, $notes);
    }

    /**
     * Vérifier le statut d'une transaction Wave
     */
    private function checkWaveTransactionStatus($waveId)
    {
        try {
            $waveApiUrl = env('WAVE_API_URL') . '/checkout/sessions/' . $waveId;
            $waveApiKey = env('WAVE_API_KEY');

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $waveApiUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $waveApiKey,
                    'Content-Type: application/json'
                ]
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                $data = json_decode($response, true);
                return [
                    'status' => $this->mapWaveStatusToLocal($data['status']),
                    'wave_data' => $data
                ];
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Error checking Wave transaction status', [
                'wave_id' => $waveId,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Mapper le statut Wave vers le statut local
     */
    private function mapWaveStatusToLocal($waveStatus)
    {
        return match($waveStatus) {
            'completed' => 'completed',
            'failed', 'cancelled' => 'failed',
            'pending' => 'processing',
            default => 'pending'
        };
    }

    /**
     * Mettre à jour le statut d'une transaction
     */
    private function updateTransactionStatus($transaction, $statusData)
    {
        DB::connection('tenant')
          ->table('payment_transactions')
          ->where('id', $transaction->id)
          ->update([
              'status' => $statusData['status'],
              'metadata' => json_encode(array_merge(
                  json_decode($transaction->metadata, true) ?? [],
                  ['wave_status_check' => $statusData]
              )),
              'processed_date' => $statusData['status'] === 'completed' ? now() : null
          ]);
    }


    /**
     * Traiter le callback Wave pour confirmer le paiement
     */

     public function handle(Request $request)
     {
         Log::info('Callback Wave reçu', ['data' => $request->all()]);

         // Enregistrer les données brutes pour débogage
         $rawData = $request->getContent();
         Log::info('Données brutes reçues: ' . $rawData);

         // Lire le JSON brut du body
         $data = $request->json()->all();

         // Vérification de la structure conforme au format Wave
         if (!isset($data['type']) || $data['type'] !== 'checkout.session.completed' || !isset($data['data'])) {
             Log::error('Callback Wave invalide', ['data' => $data]);
             return response()->json(['status' => 'error', 'message' => 'Format de données invalide'], 400);
         }

         $sessionData = $data['data'];

         // Vérifier les champs nécessaires dans les données de session
         if (!isset($sessionData['client_reference']) || !isset($sessionData['payment_status'])) {
             Log::error('Données de session Wave incomplètes', ['session_data' => $sessionData]);
             return response()->json(['status' => 'error', 'message' => 'Données de session incomplètes'], 400);
         }

         $clientReference = $sessionData['client_reference'];
         $paymentStatus = $sessionData['payment_status'];
         $transactionId = $sessionData['id'] ?? null;

         Log::info('Traitement du callback', [
             'client_reference' => $clientReference,
             'payment_status' => $paymentStatus,
             'transaction_id' => $transactionId
         ]);

         try {
            // Chercher d'abord l'organisation par la référence client pour déterminer le tenant
            $orgKey = null;

            // Pattern amélioré pour gérer différents formats d'organisations avec préfixes variables
            if (preg_match('/^([A-Z]+-)(.+)-[0-9A-F]{13,}$/i', $clientReference, $matches)) {
                // Extraire le préfixe et la partie principale
                $prefix = $matches[1]; // WAVE-, ORANGE-, MTN-, etc.
                $fullPart = $matches[2];

                // Séparer les segments
                $segments = explode('-', $fullPart);

                Log::info('🔍 Analyse de la référence', [
                    'client_reference' => $clientReference,
                    'prefix' => $prefix,
                    'full_part' => $fullPart,
                    'segments' => $segments
                ]);

                // Logique améliorée pour extraire l'org_key
                if (count($segments) >= 2) {
                    $possibleOrgKeys = [];

                    // Stratégie 1: Essayer les premières combinaisons (2, 3, 4 segments)
                    for ($i = 2; $i <= min(4, count($segments)); $i++) {
                        $orgKeyCandidate = strtolower(implode('-', array_slice($segments, 0, $i)));
                        $possibleOrgKeys[] = $orgKeyCandidate;
                    }

                    Log::info('🧪 Candidats org_key générés', [
                        'possible_keys' => $possibleOrgKeys
                    ]);

                    // Vérifier dans l'ordre de priorité (du plus court au plus long)
                    foreach ($possibleOrgKeys as $candidate) {
                        $orgExists = DB::table('organizations')->where('org_key', $candidate)->exists();
                        Log::info('✅ Vérification org_key', [
                            'candidate' => $candidate,
                            'exists' => $orgExists
                        ]);

                        if ($orgExists) {
                            $orgKey = $candidate;
                            break;
                        }
                    }

                    // Stratégie 2: Si aucun candidat trouvé, essayer une approche intelligente
                    if (!$orgKey) {
                        // Mots-clés qui indiquent généralement le début d'un nom d'événement
                        $eventKeywords = [
                            '1ER', '1ST', '2EME', '2ND', '3EME', '3RD',
                            'CONVENTION', 'GALA', 'SEMINAIRE', 'FORMATION', 'ASSEMBLEE',
                            'CONFERENCE', 'WORKSHOP', 'ATELIER', 'FORUM', 'SUMMIT',
                            'DINER', 'DEJEUNER', 'SOIREE', 'COCKTAIL', 'INAUGURATION'
                        ];

                        // Parcourir les segments pour trouver où commence l'événement
                        for ($i = 1; $i < count($segments); $i++) {
                            $currentSegment = strtoupper($segments[$i]);

                            // Si on trouve un mot-clé d'événement ou un pattern numérique
                            if (in_array($currentSegment, $eventKeywords) ||
                                preg_match('/^\d+(ER|EME|ST|ND|RD|TH)?$/i', $currentSegment)) {

                                $orgKey = strtolower(implode('-', array_slice($segments, 0, $i)));
                                Log::info('🎯 Org_key trouvé via mot-clé événement', [
                                    'keyword_found' => $currentSegment,
                                    'position' => $i,
                                    'extracted_org_key' => $orgKey
                                ]);
                                break;
                            }
                        }
                    }

                    // Stratégie 3: Si toujours rien, essayer des patterns spécifiques connus
                    if (!$orgKey) {
                        $knownPatterns = [
                            '/^(INF-JCI-CI)-/', // Pour INF JCI-CI
                            '/^(ROTARY-CLUB-\w+)-/', // Pour Rotary clubs
                            '/^([A-Z]+-[A-Z]+-[A-Z]+)-/', // Pattern générique 3 segments
                            '/^([A-Z]+-[A-Z]+)-/', // Pattern générique 2 segments
                        ];

                        foreach ($knownPatterns as $pattern) {
                            if (preg_match($pattern, strtoupper($fullPart), $patternMatches)) {
                                $candidateKey = strtolower($patternMatches[1]);

                                if (DB::table('organizations')->where('org_key', $candidateKey)->exists()) {
                                    $orgKey = $candidateKey;
                                    Log::info('🎯 Org_key trouvé via pattern connu', [
                                        'pattern' => $pattern,
                                        'extracted_org_key' => $orgKey
                                    ]);
                                    break;
                                }
                            }
                        }
                    }
                }
            } else {
                // Fallback: Si le pattern principal ne fonctionne pas
                Log::warning('⚠️ Pattern de référence non reconnu, essai fallback', [
                    'client_reference' => $clientReference
                ]);

                // Essayer de détecter d'autres formats possibles
                if (preg_match('/^(.+)-[0-9A-F]{13,}$/i', $clientReference, $matches)) {
                    $withoutHash = $matches[1];
                    $segments = explode('-', $withoutHash);

                    // Ignorer le premier segment (préfixe) et essayer les suivants
                    if (count($segments) > 1) {
                        $withoutPrefix = array_slice($segments, 1);

                        for ($i = 2; $i <= min(3, count($withoutPrefix)); $i++) {
                            $candidate = strtolower(implode('-', array_slice($withoutPrefix, 0, $i)));
                            if (DB::table('organizations')->where('org_key', $candidate)->exists()) {
                                $orgKey = $candidate;
                                Log::info('🎯 Org_key trouvé via fallback', [
                                    'extracted_org_key' => $orgKey
                                ]);
                                break;
                            }
                        }
                    }
                }
            }

            // Log de débogage final
            Log::info('🏢 Extraction org_key depuis référence - RÉSULTAT', [
                'client_reference' => $clientReference,
                'extracted_org_key' => $orgKey,
                'success' => !is_null($orgKey)
            ]);

            if (!$orgKey) {
                Log::error('❌ Impossible d\'extraire l\'organisation de la référence', [
                    'client_reference' => $clientReference,
                    'suggestion' => 'Vérifiez le format de la référence ou ajoutez un nouveau pattern'
                ]);
                return response()->json(['status' => 'error', 'message' => 'Référence invalide'], 400);
            }

            // Récupérer l'organisation depuis la base principale
            $currentOrganization = DB::table('organizations')->where('org_key', $orgKey)->first();

            if (!$currentOrganization) {
                Log::error('❌ Organisation non trouvée', [
                    'org_key' => $orgKey,
                    'client_reference' => $clientReference
                ]);
                return response()->json(['status' => 'error', 'message' => 'Organisation non trouvée'], 400);
            }

            Log::info('✅ Organisation trouvée avec succès', [
                'org_key' => $orgKey,
                'organization' => $currentOrganization->org_name,
                'database_name' => $currentOrganization->database_name
            ]);

            // Définir l'organisation dans le service container
            app()->instance('current.organization', $currentOrganization);

            // Configurer la connexion tenant
            $tenantConfig = config('database.connections.mysql');
            $tenantConfig['database'] = $currentOrganization->database_name;
            config(['database.connections.tenant' => $tenantConfig]);

            // Purger la connexion tenant pour forcer la reconnexion
            DB::purge('tenant');

            // Utiliser TenantHelper
            TenantHelper::setTenantContext($currentOrganization);

            return TenantHelper::withTenantConnection(function() use ($clientReference, $paymentStatus, $transactionId, $currentOrganization) {

                // Rechercher la transaction sur la connexion tenant
                $transaction = DB::connection('tenant')
                                ->table('payment_transactions')
                                ->where('transaction_reference', $clientReference)
                                ->first();

                if (!$transaction) {
                    Log::error('❌ Transaction non trouvée sur tenant', [
                        'client_reference' => $clientReference,
                        'organization' => $currentOrganization->org_name
                    ]);
                    return response()->json(['status' => 'not_found'], 404);
                }

                Log::info('✅ Transaction trouvée sur tenant', [
                    'transaction_id' => $transaction->id,
                    'current_status' => $transaction->status
                ]);

                $metadata = json_decode($transaction->metadata, true);

                if (!isset($metadata['event_data']['event_id'])) {
                    Log::error('❌ Métadonnées de contexte manquantes', ['metadata' => $metadata]);
                    return response()->json(['status' => 'error', 'message' => 'Contexte manquant'], 400);
                }

                $eventId = $metadata['event_data']['event_id'];
                $currentEvent = DB::connection('tenant')->table('events')->where('id', $eventId)->first();

                if (!$currentEvent) {
                    Log::error('❌ Événement non trouvé', ['event_id' => $eventId]);
                    return response()->json(['status' => 'error', 'message' => 'Événement non trouvé'], 400);
                }

                Log::info('✅ Contexte récupéré avec succès', [
                    'organization' => $currentOrganization->org_name,
                    'event' => $currentEvent->event_title ?? $currentEvent->title
                ]);

                if ($paymentStatus === 'succeeded' && $transaction->status !== 'completed') {
                    Log::info('🚀 Début du traitement du paiement réussi');

                    DB::connection('tenant')->beginTransaction();

                    try {
                        $participantData = $metadata['participant_data'];

                        // Créer l'inscription
                        $registration = Registration::on('tenant')->create([
                            'event_id' => $currentEvent->id,
                            'ticket_type_id' => $participantData['ticket_type_id'],
                            'registration_number' => $this->generateRegistrationNumber(),
                            'fullname' => $participantData['fullname'],
                            'phone' => $participantData['phone'],
                            'email' => $participantData['email'],
                            'organization' => $participantData['organization'] ?? null,
                            'position' => $participantData['position'] ?? null,
                            /* 'question_1' => $participantData['question_1'] ?? null,
                            'question_2' => $participantData['question_2'] ?? null,
                            'question_3' => $participantData['question_3'] ?? null, */
                            'ticket_price' => $transaction->amount,
                            'amount_paid' => $transaction->amount,
                            'status' => 'confirmed',
                            'payment_status' => 'paid',
                            'confirmation_date' => now(),
                            'form_data' => json_encode([
                                'full_name' => $participantData['fullname'],
                                'email' => $participantData['email'],
                                'phone' => $participantData['phone'],
                                'organization' => $participantData['organization'] ?? null,
                                'position' => $participantData['position'] ?? null,
                                /* 'question_1' => $participantData['question_1'] ?? null,
                                'question_2' => $participantData['question_2'] ?? null,
                                'question_3' => $participantData['question_3'] ?? null */
                            ])
                        ]);

                        // Mettre à jour la transaction
                        DB::connection('tenant')
                        ->table('payment_transactions')
                        ->where('id', $transaction->id)
                        ->update([
                            'registration_id' => $registration->id,
                            'status' => 'completed',
                            'payment_date' => now(),
                            'processed_date' => now()
                        ]);

                        // Incrémenter le compteur de tickets vendus
                        TicketType::on('tenant')
                                ->where('id', $participantData['ticket_type_id'])
                                ->increment('quantity_sold');

                        DB::connection('tenant')->commit();

                        // Générer et envoyer le ticket
                        try {
                            $this->generateAndSendTicket($registration, $currentEvent, $currentOrganization);
                            Log::info('📧 Ticket généré et envoyé');
                        } catch (\Exception $e) {
                            Log::warning('⚠️ Erreur envoi ticket', ['error' => $e->getMessage()]);
                        }

                        Log::info('🎉 Inscription créée avec succès', [
                            'registration_id' => $registration->id,
                            'registration_number' => $registration->registration_number,
                            'organization' => $currentOrganization->org_name
                        ]);

                        return response('OK', 200);

                    } catch (\Exception $e) {
                        DB::connection('tenant')->rollback();
                        Log::error('💥 Erreur création inscription', [
                            'error' => $e->getMessage(),
                            'organization' => $currentOrganization->org_name
                        ]);
                        return response('Registration creation failed', 500);
                    }

                } elseif (in_array($paymentStatus, ['failed', 'cancelled'])) {
                    DB::connection('tenant')
                    ->table('payment_transactions')
                    ->where('id', $transaction->id)
                    ->update([
                        'status' => 'failed',
                        'processed_date' => now()
                    ]);

                    Log::info('❌ Paiement échoué', ['status' => $paymentStatus]);
                    return response('Payment failed', 200);
                } else {
                    Log::info('ℹ️ Statut non géré ou déjà traité', [
                        'payment_status' => $paymentStatus,
                        'transaction_status' => $transaction->status
                    ]);
                    return response('OK', 200);
                }
            });

        } catch (\Exception $e) {
            Log::error('💥 Erreur générale webhook', [
                'error' => $e->getMessage(),
                'client_reference' => $clientReference ?? 'N/A'
            ]);
            return response('Internal server error', 500);
        }
     }

    public function generateAndSendTicket($registration, $event, $organization)
    {
        try {
            Log::info('Starting ticket generation with new structure', [
                'registration_id' => $registration->id,
                'organization' => $organization->org_key,
                'event' => $event->event_slug
            ]);

            $accessZones = DB::connection('tenant')
                            ->table('event_access_controls')
                            ->where('event_id', $event->id)
                            ->where('is_active', true)
                            ->orderBy('id')
                            ->get();

            $accessZones = $accessZones->take(3);

            if ($accessZones->isEmpty()) {
                $accessZones = collect([
                    (object) [
                        'access_zone' => 'opening',
                        'zone_name' => 'Cérémonie d\'ouverture',
                        'zone_description' => 'Accès à la cérémonie d\'ouverture'
                    ],
                    (object) [
                        'access_zone' => 'conference',
                        'zone_name' => 'Conférences',
                        'zone_description' => 'Sessions de formation et conférences'
                    ],
                    (object) [
                        'access_zone' => 'networking',
                        'zone_name' => 'Cocktail Networking',
                        'zone_description' => 'Moment de networking et d\'échanges'
                    ]
                ]);
            }

            $qrCodes = $this->generateOptimizedQRCodes($registration, $event, $organization, $accessZones);

            Log::info('QR codes generated', [
                'count' => count($qrCodes),
                'zones' => $accessZones->pluck('zone_name')->toArray()
            ]);

            $ticketPath = $this->generateTicketImageWithOrganizedStructure($registration, $event, $organization, $qrCodes);

            Log::info('Ticket image generated', [
                'path' => $ticketPath,
                'exists' => file_exists($ticketPath),
                'size' => file_exists($ticketPath) ? filesize($ticketPath) : 0
            ]);

            if (!file_exists($ticketPath)) {
                throw new \Exception('Ticket file not generated: ' . $ticketPath);
            }

            Log::info('=== TENTATIVE ENVOI EMAIL ===', [
                'registration_id' => $registration->id,
                'email' => $registration->email,
                'ticket_path' => $ticketPath
            ]);

            // Tentative d'envoi email avec gestion d'erreur
            $emailSent = false;
            try {
                $emailSent = $this->sendTicketEmailWithOrganizedStructure($registration, $event, $organization, $ticketPath);
            } catch (\Exception $emailError) {
                Log::error('=== ERREUR ENVOI EMAIL (NON BLOQUANTE) ===', [
                    'registration_id' => $registration->id,
                    'email' => $registration->email,
                    'error' => $emailError->getMessage()
                ]);
                // L'erreur email n'est pas bloquante pour les inscriptions gratuites
            }

            Log::info('=== RÉSULTAT ENVOI EMAIL ===', [
                'registration_id' => $registration->id,
                'success' => $emailSent,
                'email' => $registration->email
            ]);

            Log::info('=== TENTATIVE ENVOI WHATSAPP ===', [
                'registration_id' => $registration->id,
                'phone' => $registration->phone,
                'ticket_path' => $ticketPath
            ]);

            $whatsappSent = $this->sendWhatsAppTicketWithOrganizedStructure($registration, $organization, $ticketPath, $event);
            Log::info('=== RÉSULTAT ENVOI WHATSAPP ===', [
                'registration_id' => $registration->id,
                'success' => $whatsappSent,
                'phone' => $registration->phone
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error generating ticket', [
                'registration_id' => $registration->id,
                'organization' => $organization->org_key,
                'event' => $event->event_slug,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    private function generateTicketImageWithOrganizedStructure($registration, $event, $organization, $qrCodes)
    {
        try {
            $ticketWidth = 800;
            $ticketHeight = 550;

            if (empty($qrCodes)) {
                throw new \Exception('Aucun QR code fourni pour la génération du ticket');
            }

            $backgroundImage = $this->createTicketBackground($ticketWidth, $ticketHeight, $event, $organization);
            $robotoRegularPath = storage_path('app/public/tickets/Roboto-Regular.ttf');
            $robotoBoldPath = storage_path('app/public/tickets/Roboto-Bold.ttf');
            $robotoCondensedPath = storage_path('app/public/tickets/Roboto_Condensed-Italic.ttf');
            $hasRobotoRegular = file_exists($robotoRegularPath);
            $hasRobotoBold = file_exists($robotoBoldPath);
            $hasRobotoCondensed = file_exists($robotoCondensedPath);
            $qrCount = count($qrCodes);
            $qrWidth = 150;
            $qrHeight = 150;
            $qrPanelPadding = 20;
            $qrPanelExactWidth = max($qrWidth, 160) + ($qrPanelPadding * 2);
            $panelMargin = 20;
            $leftPanelPadding = 30;
            $panelHeight = 450;
            $leftPanelWidth = $ticketWidth - $panelMargin * 2 - $qrPanelExactWidth;
            $totalInfoLines = 6;
            $infoSpacing = 45;
            $totalInfoHeight = $infoSpacing * ($totalInfoLines - 1);
            $leftCardPaddingTop = 30;
            $leftCardPaddingBottom = 30;
            $leftCardHeight = $totalInfoHeight + $leftCardPaddingTop + $leftCardPaddingBottom;
            $rightPanelY = (int)(($ticketHeight - $panelHeight) / 2);
            $leftPanelY = (int)(($ticketHeight - $leftCardHeight) / 2);
            $rightPanelStart = $panelMargin + $leftPanelWidth;
            $eventTitle = $event->event_title ?? $event->title ?? 'ÉVÉNEMENT';
            $titleText = "TICKET - " . mb_strtoupper($eventTitle, 'UTF-8');

            $backgroundImage->text($titleText, $ticketWidth / 2, min($leftPanelY, $rightPanelY) - 15, function($font) use ($hasRobotoBold, $robotoBoldPath, $hasRobotoRegular, $robotoRegularPath) {
                if ($hasRobotoBold) {
                    $font->file($robotoBoldPath);
                } elseif ($hasRobotoRegular) {
                    $font->file($robotoRegularPath);
                } else {
                    $font->file(5);
                }
                $font->size(18);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('bottom');
            });

            $cardColor = 'rgba(255, 255, 255, 0.3)';
            $leftCard = Image::canvas($leftPanelWidth, $leftCardHeight, $cardColor);
            $backgroundImage->insert($leftCard, 'top-left', $panelMargin, $leftPanelY);
            $rightCard = Image::canvas($qrPanelExactWidth, $panelHeight, $cardColor);
            $backgroundImage->insert($rightCard, 'top-left', $rightPanelStart, $rightPanelY);

            $infoX = $panelMargin + $leftPanelPadding;
            $valueX = $infoX + 180;
            $infoStartY = $leftPanelY + $leftCardPaddingTop + 20; // Descendre de 20px
            $labelSize = 16;
            $valueSize = 16;

            $participantInfo = [
                'Nom et Prénoms:' => mb_convert_encoding($registration->fullname, 'UTF-8', 'auto'),
                //'Email:' => $registration->email,
                'Téléphone:' => $registration->phone,
            ];

            if (!empty($registration->position)) {
                $participantInfo['Fonction:'] = mb_convert_encoding($registration->position, 'UTF-8', 'auto');
            }

            if (!empty($registration->organization)) {
                $participantInfo['Organisation:'] = mb_convert_encoding($registration->organization, 'UTF-8', 'auto');
            }

            //$participantInfo['Type de ticket:'] = $this->getTicketTypeName($registration->ticket_type_id);
            if ($registration->ticket_price == 0) {
                $participantInfo['Participation:'] = 'Gratuite';
            } else {
                $participantInfo['Montant:'] = number_format($registration->ticket_price, 0, ',', ' ') . ' FCFA';
            }
            $currentInfoY = $infoStartY;
            $infoIndex = 0;
            foreach ($participantInfo as $label => $value) {
                if ($infoIndex >= $totalInfoLines) break;

                $label = mb_convert_encoding($label, 'UTF-8', 'auto');
                $value = mb_convert_encoding($value, 'UTF-8', 'auto');

                if (strlen($value) > 25) {
                    $value = substr($value, 0, 22) . '...';
                }

                $backgroundImage->text($label, $infoX, $currentInfoY, function($font) use ($hasRobotoBold, $robotoBoldPath, $hasRobotoRegular, $robotoRegularPath, $labelSize) {
                    if ($hasRobotoBold) {
                        $font->file($robotoBoldPath);
                    } elseif ($hasRobotoRegular) {
                        $font->file($robotoRegularPath);
                    } else {
                        $font->file(5);
                    }
                    $font->size($labelSize);
                    $font->color('#ffffff');
                    $font->align('left');
                    $font->valign('center');
                });

                $backgroundImage->text($value, $valueX, $currentInfoY, function($font) use ($hasRobotoRegular, $robotoRegularPath, $valueSize) {
                    if ($hasRobotoRegular) {
                        $font->file($robotoRegularPath);
                    } else {
                        $font->file(5);
                    }
                    $font->size($valueSize);
                    $font->color('#ffffff');
                    $font->align('left');
                    $font->valign('center');
                });

                $currentInfoY += $infoSpacing;
                $infoIndex++;
            }

            $ticketNumber = substr("#Czo" . $registration->id . ($registration->registration_number ?? ''), 0, 8);
            $backgroundImage->text($ticketNumber, $panelMargin + $leftPanelWidth - $leftPanelPadding, $leftPanelY + $leftCardHeight - 20, function($font) use ($hasRobotoCondensed, $robotoCondensedPath) {
                if ($hasRobotoCondensed) {
                    $font->file($robotoCondensedPath);
                } else {
                    $font->file(5);
                }
                $font->size(14);
                $font->color('#ffffff');
                $font->align('right');
                $font->valign('bottom');
            });

            $qrXCenter = $rightPanelStart + ($qrPanelExactWidth / 2);
            $qrX = (int)($qrXCenter - ($qrWidth / 2));
            $qrTextHeight = 35;
            $qrBlockHeight = $qrHeight + $qrTextHeight;

            if ($qrCount == 1) {
                $qrStartY = (int)($rightPanelY + (($panelHeight - $qrBlockHeight) / 2));
                $qrSpacing = 0;
            } elseif ($qrCount == 2) {
                $totalHeight = $qrBlockHeight * 2;
                $availableSpace = $panelHeight - $totalHeight;
                $qrSpacing = (int)($availableSpace / 3);
                $qrStartY = (int)($rightPanelY + $qrSpacing);
            } else {
                $qrSpacing = 25;
                $qrStartY = (int)($rightPanelY + $qrPanelPadding + 10);
            }

            Log::info('Configuration QR codes', [
                'qr_count' => $qrCount,
                'qr_start_y' => $qrStartY,
                'qr_spacing' => $qrSpacing,
                'qr_x' => $qrX,
                'qr_size' => "{$qrWidth}x{$qrHeight}"
            ]);

            // ⭐ DEBUG DES DONNÉES D'ÉVÉNEMENT
            Log::info('=== DEBUG EVENT DATA ===', [
                'event_date' => $event->event_date ?? 'NULL',
                'event_start_time' => $event->event_start_time ?? 'NULL',
                'event_end_time' => $event->event_end_time ?? 'NULL',
                'event_id' => $event->id ?? 'NULL',
                'event_title' => $event->event_title ?? 'NULL'
            ]);

            foreach ($qrCodes as $index => $qrData) {
                if ($index >= 3) break;

                $currentQrY = (int)($qrStartY + ($index * ($qrBlockHeight + $qrSpacing)));

                try {
                    // CORRECTION : Accès sécurisé aux propriétés de zone
                    $zoneName = 'Zone ' . ($index + 1); // Valeur par défaut

                    if (isset($qrData['zone_name'])) {
                        $zoneName = $qrData['zone_name'];
                    } elseif (isset($qrData['zone']) && is_object($qrData['zone']) && isset($qrData['zone']->zone_name)) {
                        $zoneName = $qrData['zone']->zone_name;
                    } elseif (isset($qrData['zone']) && is_array($qrData['zone']) && isset($qrData['zone']['zone_name'])) {
                        $zoneName = $qrData['zone']['zone_name'];
                    }

                    Log::info('Traitement QR code', [
                        'index' => $index,
                        'zone' => $zoneName,
                        'y_position' => $currentQrY,
                        'has_image' => !empty($qrData['image'])
                    ]);

                    if (empty($qrData['image'])) {
                        Log::error('QR code image vide', ['index' => $index]);
                        continue;
                    }

                    if (!preg_match('/^data:image\/png;base64,/', $qrData['image'])) {
                        Log::error('Format QR code invalide', [
                            'index' => $index,
                            'format' => substr($qrData['image'], 0, 50)
                        ]);
                        continue;
                    }

                    $base64Data = preg_replace('/^data:image\/png;base64,/', '', $qrData['image']);
                    $qrImageData = base64_decode($base64Data);

                    if ($qrImageData === false) {
                        Log::error('Échec décodage base64 QR code', ['index' => $index]);
                        continue;
                    }

                    $qrObj = Image::make($qrImageData);
                    if (!$qrObj) {
                        Log::error('Échec création image QR code', ['index' => $index]);
                        continue;
                    }

                    $qrObj->resize($qrWidth, $qrHeight);
                    $backgroundImage->insert($qrObj, 'top-left', (int)$qrX, (int)$currentQrY);

                    $displayZoneName = mb_convert_encoding($zoneName, 'UTF-8', 'auto');
                    $backgroundImage->text($displayZoneName, (int)$qrXCenter, (int)($currentQrY + $qrHeight + 7), function($font) use ($hasRobotoRegular, $robotoRegularPath) {
                        if ($hasRobotoRegular) {
                            $font->file($robotoRegularPath);
                        } else {
                            $font->file(5);
                        }
                        $font->size(13);
                        $font->color('#ffffff');
                        $font->align('center');
                        $font->valign('top');
                    });

                    // ⭐⭐⭐ NOUVEAU CALCUL DYNAMIQUE DE LA DATE ⭐⭐⭐
                    $eventDate = null;
                    $dateSource = 'unknown';

                    try {
                        // PRIORITÉ 1 : Date/heure spécifique à la zone d'accès
                        /* if (isset($qrData['zone']) && is_object($qrData['zone'])) {
                            $zone = $qrData['zone'];

                            // Si la zone a sa propre date et heure
                            if (!empty($zone->access_date) && !empty($zone->access_start_time)) {
                                $zoneDateTime = $zone->access_date . ' ' . $zone->access_start_time;
                                $eventDate = date('j M Y - G\hi', strtotime($zoneDateTime));
                                $dateSource = 'zone_specific_datetime';

                                Log::info('Date trouvée - Zone spécifique', [
                                    'zone_name' => $zone->zone_name,
                                    'access_date' => $zone->access_date,
                                    'access_start_time' => $zone->access_start_time,
                                    'combined' => $zoneDateTime,
                                    'formatted' => $eventDate
                                ]);
                            }
                            // Si la zone a seulement l'heure, utiliser la date de l'événement
                            elseif (!empty($zone->access_start_time) && !empty($event->event_date)) {
                                $zoneDateTime = $event->event_date . ' ' . $zone->access_start_time;
                                $eventDate = date('j M Y - G\hi', strtotime($zoneDateTime));
                                $dateSource = 'zone_time_with_event_date';

                                Log::info('Date trouvée - Heure zone + Date événement', [
                                    'zone_name' => $zone->zone_name,
                                    'zone_time' => $zone->access_start_time,
                                    'event_date' => $event->event_date,
                                    'combined' => $zoneDateTime,
                                    'formatted' => $eventDate
                                ]);
                            }
                        } */

                        // PRIORITÉ 2 : Date et heure de l'événement principal
                        if (!empty($event->event_date)) {
                            // Extraire seulement l'heure si event_start_time contient une date complète
                            $startTime = $event->event_start_time;
                            if (strpos($startTime, ' ') !== false) {
                                // Si c'est une date complète, extraire seulement l'heure
                                $startTime = date('H:i:s', strtotime($startTime));
                            }

                            // Extraire seulement la date (sans l'heure) de event_date
                            $eventDateOnly = date('Y-m-d', strtotime($event->event_date));

                            $eventDateTime = $eventDateOnly . ' ' . $startTime;
                            $eventDate = date('j M Y - G\hi', strtotime($eventDateTime));
                            $dateSource = 'main_event_datetime';

                            Log::info('Date calculée', [
                                'event_date' => $event->event_date,
                                'event_date_only' => $eventDateOnly,
                                'event_start_time' => $event->event_start_time,
                                'extracted_time' => $startTime,
                                'combined' => $eventDateTime,
                                'formatted' => $eventDate
                            ]);
                        }

                        // PRIORITÉ 3 : Requête directe en base de données
                        /* if (!$eventDate && !empty($event->id)) {
                            try {
                                $eventFromDB = DB::connection('tenant')
                                    ->table('events')
                                    ->where('id', $event->id)
                                    ->select('event_date', 'event_start_time', 'event_end_time')
                                    ->first();

                                if ($eventFromDB && $eventFromDB->event_date) {
                                    $dbTime = $eventFromDB->event_start_time ?: '19:00:00';
                                    $dbDateTime = $eventFromDB->event_date . ' ' . $dbTime;
                                    $eventDate = date('j M Y - G\hi', strtotime($dbDateTime));
                                    $dateSource = 'direct_db_query';

                                    Log::info('Date trouvée - Requête DB directe', [
                                        'db_event_date' => $eventFromDB->event_date,
                                        'db_event_start_time' => $eventFromDB->event_start_time,
                                        'used_time' => $dbTime,
                                        'combined' => $dbDateTime,
                                        'formatted' => $eventDate
                                    ]);
                                }
                            } catch (\Exception $dbError) {
                                Log::error('Erreur requête DB pour date', [
                                    'error' => $dbError->getMessage(),
                                    'event_id' => $event->id
                                ]);
                            }
                        }

                        // PRIORITÉ 4 : Fallback avec les données connues de votre BDD
                        if (!$eventDate) {
                            $eventDate = "25 Juil 2025 - 19h30";
                            $dateSource = 'hardcoded_known_data';

                            Log::warning('Utilisation fallback hardcodé', [
                                'reason' => 'Aucune date trouvée dans les sources précédentes',
                                'fallback_date' => $eventDate,
                                'event_object_keys' => array_keys((array)$event)
                            ]);
                        } */

                        // FORMATAGE FRANÇAIS
                        if ($eventDate && $dateSource !== 'hardcoded_known_data') {
                            $moisFrancais = [
                                'Jan' => 'Jan', 'Feb' => 'Fév', 'Mar' => 'Mar', 'Apr' => 'Avr',
                                'May' => 'Mai', 'Jun' => 'Juin', 'Jul' => 'Juil', 'Aug' => 'Août',
                                'Sep' => 'Sep', 'Oct' => 'Oct', 'Nov' => 'Nov', 'Dec' => 'Déc'
                            ];

                            foreach ($moisFrancais as $en => $fr) {
                                $eventDate = str_replace($en, $fr, $eventDate);
                            }
                        }

                        Log::info('=== DATE FINALE CALCULÉE ===', [
                            'qr_index' => $index,
                            'final_date' => $eventDate,
                            'source' => $dateSource,
                            'zone_name' => $zoneName
                        ]);

                    } catch (\Exception $dateError) {
                        Log::error('Erreur calcul date dynamique', [
                            'error' => $dateError->getMessage(),
                            'trace' => $dateError->getTraceAsString()
                        ]);

                        // Fallback sécurisé
                        $eventDate = "25 Juil 2025 - 19h30";
                        $dateSource = 'error_fallback';
                    }

                    // ⭐ AFFICHAGE DE LA DATE CALCULÉE DYNAMIQUEMENT
                    $backgroundImage->text($eventDate, (int)$qrXCenter, (int)($currentQrY + $qrHeight + 22), function($font) use ($hasRobotoRegular, $robotoRegularPath) {
                        if ($hasRobotoRegular) {
                            $font->file($robotoRegularPath);
                        } else {
                            $font->file(5);
                        }
                        $font->size(11);
                        $font->color('#ffffff');
                        $font->align('center');
                        $font->valign('top');
                    });

                } catch (\Exception $e) {
                    Log::error('Erreur insertion QR code', [
                        'index' => $index,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    continue;
                }
            }

            $textMarginLeft = 30;

            $backgroundImage->text("Important : Veuillez télécharger votre ticket et le conserver soigneusement.",
                $textMarginLeft,
                $ticketHeight - 40,
                function($font) use ($hasRobotoBold, $robotoBoldPath, $hasRobotoRegular, $robotoRegularPath) {
                    if ($hasRobotoBold) {
                        $font->file($robotoBoldPath);
                    } elseif ($hasRobotoRegular) {
                        $font->file($robotoRegularPath);
                    } else {
                        $font->file(5);
                    }
                    $font->size(13);
                    $font->color('#ffffff');
                    $font->align('left');
                    $font->valign('center');
                }
            );

            $backgroundImage->text("Ce ticket est strictement personnel et nominatif. Il ne doit en aucun cas être partagé ou transféré à autrui.",
                $textMarginLeft,
                $ticketHeight - 20,
                function($font) use ($hasRobotoRegular, $robotoRegularPath) {
                    if ($hasRobotoRegular) {
                        $font->file($robotoRegularPath);
                    } else {
                        $font->file(5);
                    }
                    $font->size(12);
                    $font->color('#ffffff');
                    $font->align('left');
                    $font->valign('center');
                }
            );

            $ticketPath = $this->saveTicketImage($backgroundImage, $registration, $organization, $event);

            return $ticketPath;

        } catch (\Exception $e) {
            Log::error('Erreur dans generateTicketImageWithOrganizedStructure (QR codes)', [
                'registration_id' => $registration->id ?? 'unknown',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    private function createTicketDirectories($organization, $event)
    {
        $directories = [
            storage_path("app/public/images"),
            storage_path("app/public/images/{$organization->org_key}"),
            storage_path("app/public/images/{$organization->org_key}/{$event->event_slug}"),
            storage_path("app/public/images/{$organization->org_key}/{$event->event_slug}/tickets"),
            public_path("images"),
            public_path("images/{$organization->org_key}"),
            public_path("images/{$organization->org_key}/{$event->event_slug}"),
            public_path("images/{$organization->org_key}/{$event->event_slug}/tickets"),
            storage_path("logs"),
            storage_path("logs/tickets"),
            storage_path("logs/tickets/{$organization->org_key}"),
            storage_path("logs/tickets/{$organization->org_key}/{$event->event_slug}")
        ];

        foreach ($directories as $directory) {
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
                Log::info('Répertoire créé', ['path' => $directory]);
            }
        }

        $gitignoreContent = "# Ignore all ticket files\n*.png\n*.jpg\n*.jpeg\n*.pdf\n\n# Keep directory structure\n!.gitignore\n";

        $gitignorePaths = [
            storage_path("app/public/images/{$organization->org_key}/{$event->event_slug}/tickets/.gitignore"),
            public_path("images/{$organization->org_key}/{$event->event_slug}/tickets/.gitignore")
        ];

        foreach ($gitignorePaths as $gitignorePath) {
            if (!file_exists($gitignorePath)) {
                file_put_contents($gitignorePath, $gitignoreContent);
            }
        }
    }

    /* private function sendTicketEmailWithOrganizedStructure($registration, $event, $organization, $ticketPath)
    {
        try {
            if (!file_exists($ticketPath)) {
                Log::error('Fichier ticket non trouvé pour email', ['path' => $ticketPath]);
                return false;
            }

            Log::info('Tentative envoi email avec nouvelle structure', [
                'to' => $registration->email,
                'cc' => $organization->contact_email,
                'ticket_path' => $ticketPath,
                'organization' => $organization->org_key,
                'event' => $event->event_slug
            ]);

            Mail::send('emails.ticket', [
                'registration' => $registration,
                'event' => $event,
                'organization' => $organization,
                'fullname' => $registration->fullname,
                'phone' => $registration->phone,
                'email' => $registration->email,
                'organization_name' => $registration->organization,
                'position' => $registration->position,
            ], function ($message) use ($registration, $event, $organization, $ticketPath) {
                $email = $registration->email;
                $contactEmail = $organization->contact_email;

                // Validation de l'email principal
                if (empty($email) || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                    throw new \Exception("Invalid email address: $email");
                }

                // Validation de l'email de contact (optionnel)
                if (!empty($contactEmail) && filter_var($contactEmail, FILTER_VALIDATE_EMAIL) === false) {
                    Log::warning('Email de contact invalide pour l\'organisation', [
                        'organization' => $organization->org_key,
                        'contact_email' => $contactEmail
                    ]);
                    $contactEmail = null; // On ignore l'email invalide
                }

                $eventTitle = $event->event_title ?? $event->title ?? 'Événement';

                $message->to($email)
                        ->subject("🎫 Votre ticket - {$eventTitle}")
                        ->attach($ticketPath);

                // Ajouter l'email de contact en CC si valide
                if (!empty($contactEmail)) {
                    $message->cc($contactEmail);
                    Log::info('Email de contact ajouté en CC', [
                        'contact_email' => $contactEmail,
                        'organization' => $organization->org_key
                    ]);
                } else {
                    Log::warning('Aucun email de contact valide pour CC', [
                        'organization' => $organization->org_key,
                        'contact_email' => $organization->contact_email ?? 'non défini'
                    ]);
                }
            });

            Log::info('Email de ticket envoyé avec succès (nouvelle structure)', [
                'registration_id' => $registration->id,
                'email' => $registration->email,
                'cc_email' => $organization->contact_email ?? 'aucun',
                'organization' => $organization->org_key,
                'event' => $event->event_slug
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Erreur envoi email ticket (nouvelle structure)', [
                'registration_id' => $registration->id,
                'email' => $registration->email,
                'organization' => $organization->org_key,
                'event' => $event->event_slug,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    } */

    private function sendTicketEmailWithOrganizedStructure($registration, $event, $organization, $ticketPath)
    {
        try {
            if (!file_exists($ticketPath)) {
                Log::error('Fichier ticket non trouvé pour email', ['path' => $ticketPath]);
                return false;
            }

            Log::info('Tentative envoi email avec nouvelle structure', [
                'to' => $registration->email,
                'cc' => $organization->contact_email,
                'ticket_path' => $ticketPath,
                'organization' => $organization->org_key,
                'event' => $event->event_slug
            ]);

            // ✅ Convertir toutes les dates en Carbon avant d'envoyer à la vue
            if (!empty($event->event_date) && !($event->event_date instanceof \Carbon\Carbon)) {
                $event->event_date = \Carbon\Carbon::parse($event->event_date);
            }
            if (!empty($event->event_start_time) && !($event->event_start_time instanceof \Carbon\Carbon)) {
                $event->event_start_time = \Carbon\Carbon::parse($event->event_start_time);
            }
            if (!empty($event->event_end_time) && !($event->event_end_time instanceof \Carbon\Carbon)) {
                $event->event_end_time = \Carbon\Carbon::parse($event->event_end_time);
            }
            if (!empty($event->registration_start_date) && !($event->registration_start_date instanceof \Carbon\Carbon)) {
                $event->registration_start_date = \Carbon\Carbon::parse($event->registration_start_date);
            }
            if (!empty($event->registration_end_date) && !($event->registration_end_date instanceof \Carbon\Carbon)) {
                $event->registration_end_date = \Carbon\Carbon::parse($event->registration_end_date);
            }
            if (!empty($registration->created_at) && !($registration->created_at instanceof \Carbon\Carbon)) {
                $registration->created_at = \Carbon\Carbon::parse($registration->created_at);
            }
            if (!empty($registration->updated_at) && !($registration->updated_at instanceof \Carbon\Carbon)) {
                $registration->updated_at = \Carbon\Carbon::parse($registration->updated_at);
            }

            Mail::send('emails.ticket', [
                'registration' => $registration,
                'event' => $event,
                'organization' => $organization,
                'fullname' => $registration->fullname,
                'phone' => $registration->phone,
                'email' => $registration->email,
                'organization_name' => $registration->organization,
                'position' => $registration->position,
            ], function ($message) use ($registration, $event, $organization, $ticketPath) {
                $email = $registration->email;
                $contactEmail = $organization->contact_email;

                // Validation de l'email principal
                if (empty($email) || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                    throw new \Exception("Invalid email address: $email");
                }

                // Validation de l'email de contact (optionnel)
                if (!empty($contactEmail) && filter_var($contactEmail, FILTER_VALIDATE_EMAIL) === false) {
                    Log::warning('Email de contact invalide pour l\'organisation', [
                        'organization' => $organization->org_key,
                        'contact_email' => $contactEmail
                    ]);
                    $contactEmail = null; // On ignore l'email invalide
                }

                $eventTitle = $event->event_title ?? $event->title ?? 'Événement';

                $message->to($email)
                        ->subject("🎫 Votre ticket - {$eventTitle}")
                        ->attach($ticketPath);

                // Ajouter l'email de contact en CC si valide
                if (!empty($contactEmail)) {
                    $message->cc($contactEmail);
                    Log::info('Email de contact ajouté en CC', [
                        'contact_email' => $contactEmail,
                        'organization' => $organization->org_key
                    ]);
                } else {
                    Log::warning('Aucun email de contact valide pour CC', [
                        'organization' => $organization->org_key,
                        'contact_email' => $organization->contact_email ?? 'non défini'
                    ]);
                }
            });

            Log::info('Email de ticket envoyé avec succès (nouvelle structure)', [
                'registration_id' => $registration->id,
                'email' => $registration->email,
                'cc_email' => $organization->contact_email ?? 'aucun',
                'organization' => $organization->org_key,
                'event' => $event->event_slug
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Erreur envoi email ticket (nouvelle structure)', [
                'registration_id' => $registration->id,
                'email' => $registration->email,
                'organization' => $organization->org_key ?? 'N/A',
                'event' => $event->event_slug ?? 'N/A',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }

    /* private function sendWhatsAppTicketWithOrganizedStructure($registration, $organization, $ticketPath, $event)
    {

        try {
            if (!file_exists($ticketPath)) {
                Log::error('Fichier ticket non trouvé pour WhatsApp', ['path' => $ticketPath]);
                return false;
            }

            $phone = substr($registration->phone, 2);
            $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

            if (strlen($cleanPhone) < 8) {
                return false;
            }

            $lastEightDigits = substr($cleanPhone, -8);
            $chatId = "225" . $lastEightDigits . "@c.us";
            $publicUrl = url("public/images/{$organization->org_key}/{$event->event_slug}/tickets/ticket_{$registration->registration_number}.png");

            $whatsappData = [
                "mediaUrl" => $publicUrl,
                "chatId" => $chatId
            ];

            // Configuration waapi.app (commentée - utiliser ChatWave à la place)
            $url = "https://waapi.app/api/v1/instances/76125/client/action/send-media"; // Pas 76125
            $headers = [
                "accept: application/json",
                "authorization: Bearer E8uVpnIMKLYEIB3KRPrbJ0cgRtl4d5FWQaFje8Pwd77b3b79",
                "content-type: application/json"
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($whatsappData));

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                $errorMessage = 'Erreur cURL WhatsApp : ' . curl_error($ch);
                curl_close($ch);
                return false;
            }

            curl_close($ch);

            if ($httpCode >= 200 && $httpCode < 300) {
                return true;
            }

            return false;

        } catch (\Exception $e) {
            return false;
        }
    } */

    private function sendWhatsAppTicketWithOrganizedStructure($registration, $organization, $ticketPath, $event)
    {
        try {
            if (!file_exists($ticketPath)) {
                Log::error('Fichier ticket non trouvé pour WhatsApp', ['path' => $ticketPath]);
                return false;
            }

            // Récupérer le numéro de téléphone complet
            $fullPhone = $registration->phone;

            // Nettoyer le numéro (enlever espaces, tirets, etc.)
            $cleanPhone = preg_replace('/[^0-9+]/', '', $fullPhone);

            // Supprimer le + si présent
            $cleanPhone = ltrim($cleanPhone, '+');

            Log::info('📱 Numéro original et nettoyé', [
                'original' => $fullPhone,
                'cleaned' => $cleanPhone
            ]);

            // Déterminer le code pays et formater le chatId
            $chatId = null;

            // Codes pays supportés
            $countryCodes = ['225', '229', '226', '223', '228', '227'];

            // Vérifier si le numéro commence par un code pays supporté
            foreach ($countryCodes as $countryCode) {
                if (str_starts_with($cleanPhone, $countryCode)) {
                    $localNumber = substr($cleanPhone, strlen($countryCode));

                    Log::info("🌍 Code pays détecté: $countryCode", [
                        'localNumber' => $localNumber,
                        'localLength' => strlen($localNumber)
                    ]);

                    // Logique spéciale pour CI et Bénin (votre code existant fonctionne)
                    if ($countryCode === '225' || $countryCode === '229') {
                        // Votre logique existante qui marche
                        $phone = substr($fullPhone, 2); // Enlever le +2
                        $cleanPhoneOld = preg_replace('/[^0-9]/', '', $phone);

                        if (strlen($cleanPhoneOld) >= 8) {
                            $lastEightDigits = substr($cleanPhoneOld, -8);
                            $chatId = $countryCode . $lastEightDigits . "@c.us";

                            Log::info("✅ CI/Bénin - ChatId généré avec méthode existante", [
                                'countryCode' => $countryCode,
                                'lastEightDigits' => $lastEightDigits,
                                'chatId' => $chatId
                            ]);
                        }
                    } else {
                        // Pour les autres pays (Burkina, Mali, Togo, Niger)
                        // Prendre directement les 8 derniers chiffres précédés du code pays
                        if (strlen($localNumber) >= 8) {
                            $lastEightDigits = substr($localNumber, -8);
                            $chatId = $countryCode . $lastEightDigits . "@c.us";

                            Log::info("✅ Autres pays - ChatId généré", [
                                'countryCode' => $countryCode,
                                'lastEightDigits' => $lastEightDigits,
                                'chatId' => $chatId
                            ]);
                        }
                    }

                    break; // Sortir de la boucle dès qu'un code pays est trouvé
                }
            }

            // Fallback si aucun code pays détecté
            if (!$chatId) {
                // Si le numéro commence par 0, c'est probablement CI sans indicatif
                if (str_starts_with($cleanPhone, '0') && strlen($cleanPhone) == 10) {
                    $localNumber = substr($cleanPhone, 1); // Enlever le 0
                    $lastEightDigits = substr($localNumber, -8);
                    $chatId = "225" . $lastEightDigits . "@c.us";

                    Log::info("🔄 Fallback CI (numéro local avec 0)", [
                        'chatId' => $chatId
                    ]);
                }
                // Si le numéro fait exactement 10 chiffres, assumer CI
                elseif (strlen($cleanPhone) == 10 && !str_starts_with($cleanPhone, '0')) {
                    $lastEightDigits = substr($cleanPhone, -8);
                    $chatId = "225" . $lastEightDigits . "@c.us";

                    Log::info("🔄 Fallback CI (10 chiffres)", [
                        'chatId' => $chatId
                    ]);
                }
                // Si le numéro fait 8 chiffres ou plus, assumer CI
                elseif (strlen($cleanPhone) >= 8) {
                    $lastEightDigits = substr($cleanPhone, -8);
                    $chatId = "225" . $lastEightDigits . "@c.us";

                    Log::info("🔄 Fallback CI (8+ chiffres)", [
                        'chatId' => $chatId
                    ]);
                }
            }

            if (!$chatId) {
                Log::error('❌ Impossible de formater le chatId WhatsApp', [
                    'phone' => $fullPhone,
                    'cleaned' => $cleanPhone
                ]);
                return false;
            }

            $publicUrl = url("public/images/{$organization->org_key}/{$event->event_slug}/tickets/ticket_{$registration->registration_number}.png");

            $whatsappData = [
                "phoneNumber" => $chatId,
                "mediaUrl" => $publicUrl,
                "caption" => "Ticket pour l'événement {$event->event_title}"
            ];

            Log::info('📤 Envoi WhatsApp', [
                'chatId' => $chatId,
                'mediaUrl' => $publicUrl,
                'registration' => $registration->registration_number,
                'whatsappData' => $whatsappData
            ]);

            // Configuration ChatWave (fonctionne dans Postman)
            $url = "https://chatwave.10nastie-groupe.com/api/clients/Czotick/media";
            $headers = [
                "Content-Type: application/json"
            ];

            Log::info('=== CONFIGURATION CURL WHATSAPP ===', [
                'url' => $url,
                'headers' => $headers,
                'postData' => json_encode($whatsappData)
            ]);

            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($whatsappData),
                CURLOPT_HTTPHEADER => $headers,
            ));

            Log::info('=== EXÉCUTION REQUÊTE CURL ===');
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            $curlInfo = curl_getinfo($ch);

            Log::info('=== RÉSULTAT REQUÊTE CURL ===', [
                'httpCode' => $httpCode,
                'response' => $response,
                'curlError' => $curlError,
                'curlInfo' => $curlInfo
            ]);

            if (curl_errno($ch)) {
                $errorMessage = 'Erreur cURL WhatsApp : ' . $curlError;
                Log::error('❌ Erreur cURL WhatsApp', [
                    'error' => $errorMessage,
                    'curlError' => $curlError,
                    'curlInfo' => $curlInfo
                ]);
                curl_close($ch);
                return false;
            }

            curl_close($ch);

            if ($httpCode >= 200 && $httpCode < 300) {
                Log::info('✅ Ticket WhatsApp envoyé avec succès', [
                    'chatId' => $chatId,
                    'httpCode' => $httpCode,
                    'response' => $response
                ]);
                return true;
            }

            Log::error('❌ Échec envoi WhatsApp', [
                'httpCode' => $httpCode,
                'response' => $response,
                'chatId' => $chatId,
                'url' => $url,
                'postData' => json_encode($whatsappData)
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('💥 Exception lors de l\'envoi WhatsApp', [
                'error' => $e->getMessage(),
                'phone' => $registration->phone ?? 'N/A'
            ]);
            return false;
        }
    }

    private function generateTicketImage($registration, $event, $organization, $qrCodes)
    {
        $ticketWidth = 800;
        $ticketHeight = 550;
        $backgroundImage = $this->createTicketBackground($ticketWidth, $ticketHeight, $event, $organization);
        $fonts = $this->getFontPaths();
        $this->addEventTitle($backgroundImage, $event, $ticketWidth, $fonts);
        $this->createContentAreas($backgroundImage, $ticketWidth, $ticketHeight, $event);
        $this->addParticipantInfo($backgroundImage, $registration, $fonts, $ticketWidth, $ticketHeight);
        $this->addQRCodes($backgroundImage, $qrCodes, $ticketWidth, $ticketHeight, $fonts);
        $this->addTicketFooter($backgroundImage, $registration, $fonts, $ticketWidth, $ticketHeight);
        $ticketPath = $this->saveTicketImage($backgroundImage, $registration, $organization, $event);

        return $ticketPath;
    }

    private function createTicketBackground($width, $height, $event, $organization)
    {
        $eventImagePath = null;
        $possiblePaths = [
            public_path("images/{$organization->org_key}/{$event->event_slug}/event-banner.png"),
            public_path("images/{$organization->org_key}/{$event->event_slug}/event-banner.jpg"),
            public_path($event->event_banner ?? ''),
            storage_path('app/public' . ($event->event_banner ?? '')),
            public_path("images/{$organization->org_key}/logo.png"),
            public_path($organization->organization_logo ?? ''),
            storage_path('app/public' . ($organization->organization_logo ?? ''))
        ];

        foreach ($possiblePaths as $path) {
            if ($path && file_exists($path)) {
                $eventImagePath = $path;
                break;
            }
        }

        if ($eventImagePath && file_exists($eventImagePath)) {
            try {
                $backgroundImage = Image::make($eventImagePath);
                $backgroundImage->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                if ($backgroundImage->width() < $width || $backgroundImage->height() < $height) {
                    $canvas = Image::canvas($width, $height, '#ffffff');
                    $canvas->insert($backgroundImage, 'center');
                    $backgroundImage = $canvas;
                }

                if ($backgroundImage->width() > $width || $backgroundImage->height() > $height) {
                    $backgroundImage->fit($width, $height, function ($constraint) {
                        $constraint->upsize();
                    }, 'center');
                }
            } catch (\Exception $e) {
                Log::error('Erreur chargement image', ['error' => $e->getMessage()]);
                $backgroundImage = Image::canvas($width, $height, '#f5f5f5');
            }
        } else {
            $backgroundImage = Image::canvas($width, $height, '#f5f5f5');
        }

        $primaryColor = '#1e3a8a';
        $overlay = Image::canvas($width, $height, $primaryColor);
        $overlay->opacity(85);
        $backgroundImage->insert($overlay, 'top-left', 0, 0);

        return $backgroundImage;
    }

    private function getFontPaths()
    {
        $robotoRegularPath = storage_path('app/public/tickets/Roboto-Regular.ttf');
        $robotoBoldPath = storage_path('app/public/tickets/Roboto-Bold.ttf');
        $robotoCondensedPath = storage_path('app/public/tickets/Roboto_Condensed-Italic.ttf');

        $fonts = [
            'regular' => file_exists($robotoRegularPath) ? $robotoRegularPath : null,
            'bold' => file_exists($robotoBoldPath) ? $robotoBoldPath : null,
            'italic' => null,
            'condensed' => file_exists($robotoCondensedPath) ? $robotoCondensedPath : null
        ];

        return $fonts;
    }

    private function addEventTitle($image, $event, $width, $fonts)
    {
        $eventTitle = $event->event_title ?? $event->title ?? 'ÉVÉNEMENT';
        $title = "TICKET - " . mb_strtoupper($eventTitle, 'UTF-8');

        $image->text($title, $width / 2, 50, function($font) use ($fonts) {
            if ($fonts['bold'] && file_exists($fonts['bold'])) {
                $font->file($fonts['bold']); // Roboto-Bold.ttf
            } elseif ($fonts['regular'] && file_exists($fonts['regular'])) {
                $font->file($fonts['regular']); // Roboto-Regular.ttf
            } else {
                $font->file(5); // Police système par défaut
            }
            $font->size(18);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('center');
        });
    }

    private function createContentAreas($image, $width, $height, $event)
    {
        $leftWidth = $width * 0.6;
        $rightWidth = $width * 0.35;
        $cardHeight = $height * 0.7;
        $cardColor = 'rgba(255, 255, 255, 0.3)';
        $leftCard = Image::canvas($leftWidth, $cardHeight, $cardColor);
        $image->insert($leftCard, 'top-left', 20, 80);
        $rightCard = Image::canvas($rightWidth, $cardHeight, $cardColor);
        $image->insert($rightCard, 'top-left', $leftWidth + 30, 80);
    }

    private function addParticipantInfo($image, $registration, $fonts, $width, $height)
    {
        $startX = 40;
        $startY = 140; // Descendre de 20px
        $lineHeight = 45;
        $labelSize = 16;
        $valueSize = 16;

        $info = [
            'Nom et Prénoms:' => mb_convert_encoding($registration->fullname, 'UTF-8', 'auto'),
            'Email:' => $registration->email,
            'Téléphone:' => $registration->phone,
        ];

        if (!empty($registration->position)) {
            $info['Fonction:'] = mb_convert_encoding($registration->position, 'UTF-8', 'auto');
        }

        if (!empty($registration->organization)) {
            $info['Organisation:'] = mb_convert_encoding($registration->organization, 'UTF-8', 'auto');
        }

        $info['Type de ticket:'] = $this->getTicketTypeName($registration->ticket_type_id);
        if ($registration->ticket_price == 0) {
            $info['Participation:'] = 'Gratuite';
        } else {
            $info['Montant:'] = number_format($registration->ticket_price, 0, ',', ' ') . ' FCFA';
        }

        $currentY = $startY;
        foreach ($info as $label => $value) {
            $label = mb_convert_encoding($label, 'UTF-8', 'auto');
            $value = mb_convert_encoding($value, 'UTF-8', 'auto');

            $image->text($label, $startX, $currentY, function($font) use ($fonts, $labelSize) {
                if ($fonts['bold'] && file_exists($fonts['bold'])) {
                    $font->file($fonts['bold']); // Roboto-Bold.ttf
                } elseif ($fonts['regular'] && file_exists($fonts['regular'])) {
                    $font->file($fonts['regular']); // Roboto-Regular.ttf
                } else {
                    $font->file(5);
                }
                $font->size($labelSize);
                $font->color('#ffffff');
                $font->align('left');
                $font->valign('center');
            });

            $image->text($value, $startX + 200, $currentY, function($font) use ($fonts, $valueSize) {
                if ($fonts['regular'] && file_exists($fonts['regular'])) {
                    $font->file($fonts['regular']); // Roboto-Regular.ttf
                } elseif ($fonts['bold'] && file_exists($fonts['bold'])) {
                    $font->file($fonts['bold']); // Fallback vers Bold si Regular pas disponible
                } else {
                    $font->file(5);
                }
                $font->size($valueSize);
                $font->color('#ffffff');
                $font->align('left');
                $font->valign('center');
            });

            $currentY += $lineHeight;
        }
    }

    private function addQRCodes($image, $qrCodes, $width, $height, $fonts)
    {
        $qrSize = 80;
        $qrStartX = $width * 0.65;
        $qrStartY = 120;
        $qrSpacing = 120;

        foreach ($qrCodes as $index => $qrData) {
            if ($index >= 3) break; // Maximum 3 QR codes

            $currentY = $qrStartY + ($index * $qrSpacing);
            $qrImageData = base64_decode(preg_replace('/^data:image\/png;base64,/', '', $qrData['image']));
            $qrImage = Image::make($qrImageData)->resize($qrSize, $qrSize);
            $image->insert($qrImage, 'top-left', $qrStartX, $currentY);
            $zoneName = mb_convert_encoding($qrData['zone']->zone_name, 'UTF-8', 'auto');
            $image->text($zoneName, $qrStartX + ($qrSize / 2), $currentY + $qrSize + 10, function($font) use ($fonts) {
                if ($fonts['regular'] && file_exists($fonts['regular'])) {
                    $font->file($fonts['regular']); // Roboto-Regular.ttf
                } elseif ($fonts['bold'] && file_exists($fonts['bold'])) {
                    $font->file($fonts['bold']);
                } else {
                    $font->file(5);
                }
                $font->size(12);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('top');
            });

            if (isset($qrData['zone']->access_start_time)) {
                $timeText = date('d/m/Y H:i', strtotime($qrData['zone']->access_start_time));
                $image->text($timeText, $qrStartX + ($qrSize / 2), $currentY + $qrSize + 25, function($font) use ($fonts) {
                    if ($fonts['regular'] && file_exists($fonts['regular'])) {
                        $font->file($fonts['regular']); // Roboto-Regular.ttf
                    } else {
                        $font->file(5);
                    }
                    $font->size(10);
                    $font->color('#ffffff');
                    $font->align('center');
                    $font->valign('top');
                });
            }
        }
    }

    private function addTicketFooter($image, $registration, $fonts, $width, $height)
    {
        $ticketNumber = "#" . substr($registration->id . $registration->registration_number, 0, 8);
        $image->text($ticketNumber, $width - 30, $height - 100, function($font) use ($fonts) {
            if ($fonts['condensed'] && file_exists($fonts['condensed'])) {
                $font->file($fonts['condensed']); // Roboto_Condensed-Italic.ttf
            } elseif ($fonts['regular'] && file_exists($fonts['regular'])) {
                $font->file($fonts['regular']); // Roboto-Regular.ttf
            } else {
                $font->file(5);
            }
            $font->size(14);
            $font->color('#ffffff');
            $font->align('right');
            $font->valign('center');
        });

        $instructions = "Important : Veuillez conserver ce ticket. Il est strictement personnel et nominatif.";
        $instructions = mb_convert_encoding($instructions, 'UTF-8', 'auto');

        $image->text($instructions, 30, $height - 40, function($font) use ($fonts) {
            if ($fonts['regular'] && file_exists($fonts['regular'])) {
                $font->file($fonts['regular']); // Roboto-Regular.ttf
            } elseif ($fonts['bold'] && file_exists($fonts['bold'])) {
                $font->file($fonts['bold']);
            } else {
                $font->file(5);
            }
            $font->size(11);
            $font->color('#ffffff');
            $font->align('left');
            $font->valign('center');
        });
    }

    private function saveTicketImage($image, $registration, $organization, $event)
    {
        $filename = "ticket_{$registration->registration_number}.png";
        $orgKey = $organization->org_key;
        $eventSlug = $event->event_slug; // Fallback
        $publicTicketsDir = public_path("images/{$orgKey}/{$eventSlug}/tickets");
        $storageTicketsDir = storage_path("app/public/images/{$orgKey}/{$eventSlug}/tickets");

        if (!file_exists($publicTicketsDir)) {
            mkdir($publicTicketsDir, 0755, true);
        }

        if (!file_exists($storageTicketsDir)) {
            mkdir($storageTicketsDir, 0755, true);
        }

        $publicPath = $publicTicketsDir . '/' . $filename;
        $storagePath = $storageTicketsDir . '/' . $filename;
        $image->save($publicPath, 90); // Qualité 90%
        $image->save($storagePath, 90);

        return $storagePath; // Retourner le chemin storage pour compatibilité
    }

    private function sendTicketEmail($registration, $event, $organization, $ticketPath)
    {
        try {
            Mail::send('emails.ticket', [
                'registration' => $registration,
                'event' => $event,
                'organization' => $organization
            ], function ($message) use ($registration, $event, $ticketPath) {
                $message->to($registration->email, $registration->fullname)
                        ->subject("Votre ticket - {$event->event_title}")
                        ->cc('beugrelouloualexstephane@gmail.com')
                        ->attach($ticketPath);
            });

            Log::info('Email de ticket envoyé', [
                'registration_id' => $registration->id,
                'email' => $registration->email
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur envoi email ticket', [
                'registration_id' => $registration->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function sendWhatsAppTicket($registration, $organization, $ticketPath)
    {
        try {
            $phone = $registration->phone;
            $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

            if (strlen($cleanPhone) < 8) {
                Log::warning('Numéro de téléphone trop court pour WhatsApp', [
                    'registration_id' => $registration->id,
                    'phone' => $phone,
                    'clean_phone' => $cleanPhone
                ]);
                return false;
            }

            $lastEightDigits = substr($cleanPhone, -8);
            $chatId = "225" . $lastEightDigits . "@c.us";
            $publicUrl = url("tickets/{$organization->org_key}/ticket_{$registration->registration_number}.png");

            $ticketExists = file_exists(public_path("tickets/{$organization->org_key}/ticket_{$registration->registration_number}.png"));
            if (!$ticketExists) {
                Log::error('Fichier ticket non trouvé pour WhatsApp', [
                    'registration_id' => $registration->id,
                    'ticket_path' => $publicUrl
                ]);
                return false;
            }

            $whatsappData = [
                "mediaUrl" => $publicUrl,
                "chatId" => $chatId
            ];

            $url = "https://waapi.app/api/v1/instances/76125/client/action/send-media";

            $headers = [
                "accept: application/json",
                "authorization: Bearer E8uVpnIMKLYEIB3KRPrbJ0cgRtl4d5FWQaFje8Pwd77b3b79",
                "content-type: application/json"
            ];

            $ch = curl_init($url);

            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POSTFIELDS => json_encode($whatsappData),
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 3
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);

            if (curl_errno($ch)) {
                $errorMessage = 'Erreur cURL WhatsApp : ' . $curlError;
                curl_close($ch);

                file_put_contents(
                    storage_path('logs/whatsapp_curl_errors.log'),
                    '[' . date('Y-m-d H:i:s') . '] ' . $errorMessage . ' - Registration ID: ' . $registration->id . PHP_EOL,
                    FILE_APPEND
                );

                return false;
            }

            curl_close($ch);

            $responseData = json_decode($response, true);

            $logData = [
                'registration_id' => $registration->id,
                'phone' => $phone,
                'chat_id' => $chatId,
                'http_code' => $httpCode,
                'ticket_url' => $publicUrl,
                'response' => $response,
                'response_data' => $responseData
            ];

            if ($httpCode >= 200 && $httpCode < 300) {

                file_put_contents(
                    storage_path('logs/whatsapp_success.log'),
                    '[' . date('Y-m-d H:i:s') . '] SUCCESS - Registration ID: ' . $registration->id .
                    ' - Phone: ' . $phone . ' - ChatID: ' . $chatId .
                    ' - HTTP Code: ' . $httpCode . PHP_EOL,
                    FILE_APPEND
                );

                return true;

            } else {
                file_put_contents(
                    storage_path('logs/whatsapp_http_errors.log'),
                    '[' . date('Y-m-d H:i:s') . '] HTTP ERROR ' . $httpCode .
                    ' - Registration ID: ' . $registration->id .
                    ' - Phone: ' . $phone .
                    ' - Response: ' . $response . PHP_EOL,
                    FILE_APPEND
                );

                return false;
            }

        } catch (\Exception $e) {
            file_put_contents(
                storage_path('logs/whatsapp_exceptions.log'),
                '[' . date('Y-m-d H:i:s') . '] EXCEPTION - Registration ID: ' . $registration->id .
                ' - Error: ' . $e->getMessage() .
                ' - File: ' . $e->getFile() . ':' . $e->getLine() . PHP_EOL,
                FILE_APPEND
            );

            return false;
        }
    }

    private function generateRegistrationNumber()
    {
        $sufix = 'Czotick';
        $timestamp = now()->format('Ymd');
        $random = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

        return $timestamp . '-' . $random . '-' . $sufix;
    }

    private function getTicketTypeName($ticketTypeId)
    {
        $ticketType = TicketType::on('tenant')->find($ticketTypeId);
        return $ticketType ? $ticketType->ticket_name : 'N/A';
    }

    private function downloadDefaultFonts()
    {
        $fontsDir = storage_path('app/public/fonts');

        if (!file_exists($fontsDir)) {
            mkdir($fontsDir, 0755, true);
        }

        $fonts = [
            'Roboto-Regular.ttf' => 'https://fonts.gstatic.com/s/roboto/v30/KFOmCnqEu92Fr1Mu72xKOzY.woff2',
            'Roboto-Bold.ttf' => 'https://fonts.gstatic.com/s/roboto/v30/KFOlCnqEu92Fr1MmWUlfBBc4.woff2'
        ];

        foreach ($fonts as $filename => $url) {
            $filepath = $fontsDir . '/' . $filename;
            if (!file_exists($filepath)) {
                try {
                    Log::info("Police manquante: {$filename}");
                } catch (\Exception $e) {
                    Log::warning("Impossible de télécharger la police: {$filename}");
                }
            }
        }

    }

    private function getLayoutPreview($zoneCount)
    {
        return match($zoneCount) {
            1 => [
                'description' => 'QR code unique centré (120x120px)',
                'layout' => 'vertical_center',
                'qr_size' => 120,
                'font_size' => 14,
                'includes_time' => true
            ],
            2 => [
                'description' => 'Deux QR codes répartis (100x100px)',
                'layout' => 'vertical_spread',
                'qr_size' => 100,
                'font_size' => 13,
                'includes_time' => true
            ],
            3 => [
                'description' => 'Trois QR codes compacts (80x80px)',
                'layout' => 'vertical_compact',
                'qr_size' => 80,
                'font_size' => 12,
                'includes_time' => false
            ]
        };
    }

    private function generateOptimizedQRCodes($registration, $event, $organization, $accessZones)
    {
        $qrCodes = [];

        // Récupérer les zones d'accès pour cet événement et ce type de ticket
        $accessControls = $this->getEventAccessControls($event, $registration->ticket_type_id);
        $zoneCount = count($accessControls);

        if ($zoneCount === 0) {
            // Fallback : créer un QR code général si pas de zones définies
            return $this->generateFallbackQRCode($registration, $event, $organization);
        }

        foreach ($accessControls as $index => $accessControl) {
            try {
                // Générer un hash spécifique à la zone
                $ticketHash = $this->generateZoneTicketHash($registration, $accessControl, $event);

                // **AJOUT CRITIQUE : Stocker le hash immédiatement**
                $this->storeTicketHashMapping($ticketHash, $registration, $accessControl, $event);

                // URL optimisée pour la vérification de zone
                $verifyUrl = $this->buildZoneVerificationUrl($organization, $event, $ticketHash, $accessControl);

                $qrSize = match($zoneCount) {
                    1 => 200,
                    2 => 150,
                    default => 120
                };

                $qrCode = new QrCode($verifyUrl);
                $qrCode->setSize($qrSize);
                $qrCode->setMargin(10);

                $writer = new PngWriter();
                $result = $writer->write($qrCode);
                $qrBinaryData = $result->getString();

                if (empty($qrBinaryData)) {
                    throw new \Exception("QR code binaire vide pour la zone {$accessControl->zone_name}");
                }

                $qrBase64 = base64_encode($qrBinaryData);
                $qrDataUri = 'data:image/png;base64,' . $qrBase64;

                // CORRECTION: Calculer l'usage depuis la base de données directement
                $currentUsage = 0;
                try {
                    $currentUsage = DB::connection('tenant')
                        ->table('access_time_logs')
                        ->where('event_id', $event->id)
                        ->where('access_zone', $accessControl->access_zone)
                        ->where('registration_id', $registration->id)
                        ->where('access_status', 'on_time') // ou le statut équivalent dans votre système
                        ->count();
                } catch (\Exception $e) {
                    Log::debug('Erreur comptage usage accès', ['error' => $e->getMessage()]);
                }

                $qrCodes[] = [
                    'access_control_id' => $accessControl->id,
                    'access_zone' => $accessControl->access_zone,
                    'zone_slug' => $accessControl->zone_slug ?? $accessControl->access_zone,
                    'zone_name' => $accessControl->zone_name,
                    'zone_description' => $accessControl->zone_description,
                    'requires_separate_check' => $accessControl->requires_separate_check ?? 1,
                    'image' => $qrDataUri,
                    'url' => $verifyUrl,
                    'ticket_hash' => $ticketHash,
                    'original_size' => $qrSize,
                    'index' => $index,
                    'usage_count' => $currentUsage,
                    'max_capacity' => $accessControl->max_capacity,
                    'schedule' => [
                        'access_start_time' => $accessControl->access_start_time,
                        'access_end_time' => $accessControl->access_end_time,
                        'access_date' => isset($accessControl->access_date) ? date('d/m/Y', strtotime($accessControl->access_date)) : null,
                        'early_access_minutes' => $accessControl->early_access_minutes ?? 0,
                        'late_access_minutes' => $accessControl->late_access_minutes ?? 30
                    ],
                    // Ajouter l'objet zone pour compatibilité avec le code existant
                    'zone' => $accessControl
                ];

            } catch (\Exception $e) {
                Log::error('Erreur génération QR code pour zone', [
                    'registration_id' => $registration->id,
                    'event_id' => $event->id,
                    'access_control_id' => $accessControl->id ?? null,
                    'zone_name' => $accessControl->zone_name ?? 'Unknown',
                    'error' => $e->getMessage()
                ]);

                // Ajouter un QR de fallback en cas d'erreur
                $fallbackQr = $this->generateFallbackQRForZone($registration, $event, $organization, $accessControl, $index);
                if ($fallbackQr) {
                    $qrCodes[] = $fallbackQr;
                }
            }
        }

        if (empty($qrCodes)) {
            throw new \Exception('Aucun QR code n\'a pu être généré pour les zones d\'accès');
        }

        return $qrCodes;
    }

    private function getEventAccessControls($event, $ticketTypeId)
    {
        return TenantHelper::withTenantConnection(function() use ($event, $ticketTypeId) {
            // Utiliser la base de données directement pour plus de compatibilité
            $controls = DB::connection('tenant')
                ->table('event_access_controls')
                ->where('event_id', $event->id)
                ->where('is_active', true)
                ->orderBy('access_start_time')
                ->get();

            // Convertir en objets pour compatibilité
            return $controls->map(function($control) {
                return (object) $control;
            });
        });
    }

    private function generateZoneTicketHash($registration, $accessControl, $event)
    {
        $data = [
            'registration_id' => $registration->id,
            'registration_number' => $registration->registration_number,
            'access_zone' => $accessControl->access_zone,
            'access_control_id' => $accessControl->id,
            'event_id' => $event->id,
            'event_slug' => $event->event_slug,
            'participant_phone' => $registration->phone,
            'generated_at' => now()->timestamp,
            'salt' => config('app.key', 'default_salt')
        ];

        return hash('sha256', json_encode($data));
    }

    private function buildZoneVerificationUrl($organization, $event, $ticketHash, $accessControl)
    {
        $baseUrl = url("/{$organization->org_key}/{$event->event_slug}");

        // URL SIMPLIFIÉE : seulement le paramètre event (nom de la zone)
        $params = [
            'event' => $accessControl->access_zone, // Seul paramètre nécessaire
        ];

        $queryString = http_build_query($params);

        // URL finale simplifiée - utilise verify-zone qui est gérée par VerifierController
        return "{$baseUrl}/verify-zone/{$ticketHash}?{$queryString}";
    }

    private function verifyZoneTicket(Request $request, $orgSlug, $eventSlug, $ticketHash)
    {
        $currentOrganization = TenantHelper::getCurrentOrganization();
        $currentEvent = TenantHelper::getCurrentEvent();

        if (!$currentOrganization || !$currentEvent) {
            return response()->json(['error' => 'Événement non trouvé'], 404);
        }

        return TenantHelper::withTenantConnection(function() use ($request, $ticketHash, $currentEvent) {
            $eventParam = $request->query('event'); // Récupère le paramètre 'event' (nom de la zone)
            $controlId = $request->query('id'); // ID du contrôle d'accès
            $isGeneral = $request->query('general', false);
            $isFallback = $request->query('fallback', false);

            if ($isGeneral || !$eventParam) {
                return $this->verifyGeneralTicket($request, $ticketHash, $currentEvent);
            }

            // CORRECTION: Récupérer comme objet stdClass depuis la base de données
            $accessControl = DB::connection('tenant')
                ->table('event_access_controls')
                ->where('event_id', $currentEvent->id)
                ->where('is_active', true)
                ->where(function($query) use ($controlId, $eventParam) {
                    if ($controlId) {
                        $query->where('id', $controlId);
                    } else {
                        $query->where('access_zone', $eventParam);
                    }
                })
                ->first();

            if (!$accessControl) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Zone d\'accès non trouvée ou inactive',
                    'zone' => $eventParam
                ], 404);
            }

            $registration = $this->findRegistrationByZoneHash($ticketHash, $currentEvent, $accessControl, $isFallback);

            if (!$registration) {
                $this->logAccessAttempt($currentEvent, $accessControl, null,
                    'zone_closed', $request, 'Ticket invalide'); // Utiliser string au lieu de constante

                return response()->json([
                    'valid' => false,
                    'message' => 'Ticket invalide ou expiré',
                    'zone_name' => $accessControl->zone_name,
                    'zone' => $accessControl->access_zone
                ], 400);
            }

            if ($registration->status !== 'confirmed' || $registration->payment_status !== 'paid') {
                $this->logAccessAttempt($currentEvent, $accessControl, $registration,
                    'zone_closed', $request, 'Inscription non confirmée');

                return response()->json([
                    'valid' => false,
                    'message' => 'Inscription non confirmée ou paiement en attente',
                    'zone_name' => $accessControl->zone_name,
                    'zone' => $accessControl->access_zone,
                    'registration_number' => $registration->registration_number
                ], 403);
            }

            // CORRECTION: Vérifier l'accès par type de ticket manuellement
            $hasAccess = $this->checkTicketTypeAccess($accessControl, $registration->ticket_type_id);
            if (!$hasAccess) {
                $this->logAccessAttempt($currentEvent, $accessControl, $registration,
                    'zone_closed', $request, 'Type de ticket non autorisé');

                return response()->json([
                    'valid' => false,
                    'message' => "Votre type de ticket ne donne pas accès à {$accessControl->zone_name}",
                    'zone_name' => $accessControl->zone_name,
                    'zone' => $accessControl->access_zone
                ], 403);
            }

            // CORRECTION: Déterminer le statut d'accès manuellement
            $accessStatus = $this->determineManualAccessStatus($accessControl);
            $this->logAccessAttempt($currentEvent, $accessControl, $registration,
                $accessStatus['status'], $request, $accessStatus['message'], $accessStatus);

            if ($accessStatus['status'] !== 'on_time') {
                $httpStatus = match($accessStatus['status']) {
                    'too_early' => 425, // Too Early
                    'too_late' => 410,  // Gone
                    'zone_closed' => 403, // Forbidden
                    default => 403
                };

                return response()->json([
                    'valid' => false,
                    'message' => $accessStatus['message'],
                    'zone_name' => $accessControl->zone_name,
                    'zone' => $accessControl->access_zone,
                    'access_status' => $accessStatus['status'],
                    'schedule' => [
                        'start_time' => $accessStatus['scheduled_start'] ?? null,
                        'end_time' => $accessStatus['scheduled_end'] ?? null
                    ]
                ], $httpStatus);
            }

            // CORRECTION: Vérifier la capacité manuellement
            if ($accessControl->max_capacity) {
                $currentOccupancy = DB::connection('tenant')
                    ->table('access_time_logs')
                    ->where('event_id', $currentEvent->id)
                    ->where('access_zone', $accessControl->access_zone)
                    ->where('access_status', 'on_time')
                    ->where('attempt_time', '>=', now()->startOfDay())
                    ->distinct('registration_id')
                    ->count();

                if ($currentOccupancy >= $accessControl->max_capacity) {
                    return response()->json([
                        'valid' => false,
                        'message' => "Capacité maximale atteinte pour {$accessControl->zone_name}",
                        'zone_name' => $accessControl->zone_name,
                        'zone' => $accessControl->access_zone,
                        'current_occupancy' => $currentOccupancy,
                        'max_capacity' => $accessControl->max_capacity
                    ], 503); // Service Unavailable
                }
            }

            // CORRECTION: Calculer l'accès successful manuellement
            $successfulAccess = DB::connection('tenant')
                ->table('access_time_logs')
                ->where('event_id', $currentEvent->id)
                ->where('access_zone', $accessControl->access_zone)
                ->where('registration_id', $registration->id)
                ->where('access_status', 'on_time')
                ->count();

            $isFirstAccess = DB::connection('tenant')
                ->table('access_time_logs')
                ->where('event_id', $currentEvent->id)
                ->where('access_zone', $accessControl->access_zone)
                ->where('registration_id', $registration->id)
                ->count() === 0;

            return response()->json([
                'valid' => true,
                'message' => "Accès autorisé à {$accessControl->zone_name}",
                'zone' => [
                    'id' => $accessControl->id,
                    'name' => $accessControl->zone_name,
                    'access_zone' => $accessControl->access_zone,
                    'description' => $accessControl->zone_description
                ],
                'participant' => [
                    'name' => $registration->fullname,
                    'registration_number' => $registration->registration_number,
                    'ticket_type' => $registration->ticketType->ticket_name ?? 'Standard',
                    'organization' => $registration->organization
                ],
                'access_info' => [
                    'time' => now()->format('H:i:s'),
                    'date' => now()->format('d/m/Y'),
                    'access_number' => $successfulAccess + 1,
                    'is_first_access' => $isFirstAccess,
                    'access_status' => $accessStatus['status'],
                    'scheduled_window' => [
                        'start' => $accessStatus['scheduled_start'] ?? null,
                        'end' => $accessStatus['scheduled_end'] ?? null
                    ]
                ]
            ]);
        });
    }

    private function verifyGeneralTicket($request, $ticketHash, $event)
    {
        $registrations = Registration::on('tenant')
            ->where('event_id', $event->id)
            ->where('status', 'confirmed')
            ->where('payment_status', 'paid')
            ->with('ticketType')
            ->get();

        $registration = null;
        foreach ($registrations as $reg) {
            $generalHash = md5($reg->registration_number . '_general_' . substr($ticketHash, -10));
            if (str_contains($ticketHash, substr($generalHash, 0, 20))) {
                $registration = $reg;
                break;
            }
        }

        if (!$registration) {
            return response()->json([
                'valid' => false,
                'message' => 'Ticket général invalide'
            ], 400);
        }

        return response()->json([
            'valid' => true,
            'message' => "Accès général autorisé",
            'participant' => [
                'name' => $registration->fullname,
                'registration_number' => $registration->registration_number,
                'ticket_type' => $registration->ticketType->ticket_name ?? 'Standard'
            ],
            'access_info' => [
                'time' => now()->format('H:i:s'),
                'date' => now()->format('d/m/Y'),
                'access_type' => 'general'
            ]
        ]);
    }

    private function checkTicketTypeAccess($accessControl, $ticketTypeId)
    {
        // Si pas de restriction spécifique, autoriser tous les types
        if (empty($accessControl->allowed_ticket_types)) {
            return true;
        }

        $allowedTypes = json_decode($accessControl->allowed_ticket_types, true);
        if (!is_array($allowedTypes)) {
            return true; // Fallback si pas un array valide
        }

        return in_array($ticketTypeId, $allowedTypes);
    }

    private function determineManualAccessStatus($accessControl)
    {
        $now = now();
        $accessStart = $accessControl->access_start_time ?
            \Carbon\Carbon::parse($accessControl->access_start_time) : null;
        $accessEnd = $accessControl->access_end_time ?
            \Carbon\Carbon::parse($accessControl->access_end_time) : null;

        // Si pas d'horaires définis, accès autorisé
        if (!$accessStart || !$accessEnd) {
            return [
                'status' => 'on_time',
                'message' => 'Accès autorisé',
                'scheduled_start' => $accessStart?->format('Y-m-d H:i:s'),
                'scheduled_end' => $accessEnd?->format('Y-m-d H:i:s')
            ];
        }

        $earlyAccess = $accessControl->early_access_minutes ?? 0;
        $lateAccess = $accessControl->late_access_minutes ?? 30;

        $effectiveStart = $accessStart->subMinutes($earlyAccess);
        $effectiveEnd = $accessEnd->addMinutes($lateAccess);

        if ($now->lt($effectiveStart)) {
            return [
                'status' => 'too_early',
                'message' => "Accès trop tôt. L'accès commence à " . $accessStart->format('H:i'),
                'scheduled_start' => $accessStart->format('Y-m-d H:i:s'),
                'scheduled_end' => $accessEnd->format('Y-m-d H:i:s')
            ];
        }

        if ($now->gt($effectiveEnd)) {
            return [
                'status' => 'too_late',
                'message' => "Accès fermé. L'accès s'est terminé à " . $accessEnd->format('H:i'),
                'scheduled_start' => $accessStart->format('Y-m-d H:i:s'),
                'scheduled_end' => $accessEnd->format('Y-m-d H:i:s')
            ];
        }

        return [
            'status' => 'on_time',
            'message' => 'Accès autorisé',
            'scheduled_start' => $accessStart->format('Y-m-d H:i:s'),
            'scheduled_end' => $accessEnd->format('Y-m-d H:i:s')
        ];
    }

    private function findRegistrationByZoneHash($ticketHash, $event, $accessControl, $isFallback = false)
    {
        try {
            // D'abord, chercher dans la table des mappings
            $hashMapping = DB::connection('tenant')
                ->table('ticket_hash_mappings')
                ->where('ticket_hash', $ticketHash)
                ->where('event_id', $event->id)
                ->where('access_zone', $accessControl->access_zone)
                ->where('is_active', true)
                ->first();

            if ($hashMapping) {
                Log::info('Hash trouvé dans la table de mapping', [
                    'ticket_hash' => $ticketHash,
                    'registration_id' => $hashMapping->registration_id,
                    'zone' => $hashMapping->access_zone
                ]);

                // Récupérer l'inscription complète
                $registration = Registration::on('tenant')
                    ->where('id', $hashMapping->registration_id)
                    ->where('event_id', $event->id)
                    ->where('status', 'confirmed')
                    ->where('payment_status', 'paid')
                    ->with('ticketType')
                    ->first();

                if ($registration) {
                    return $registration;
                }
            }

            // Fallback : recherche par recalcul du hash (méthode existante)
            Log::info('Hash non trouvé en BDD, tentative de recalcul', [
                'ticket_hash' => $ticketHash,
                'event_id' => $event->id,
                'zone' => $accessControl->access_zone
            ]);

            $registrations = Registration::on('tenant')
                ->where('event_id', $event->id)
                ->where('status', 'confirmed')
                ->where('payment_status', 'paid')
                ->with('ticketType')
                ->get();

            foreach ($registrations as $registration) {
                if ($isFallback) {
                    $fallbackHash = md5($registration->registration_number . '_' . $accessControl->access_zone . '_');
                    if (str_contains($ticketHash, substr($fallbackHash, 0, 20))) {
                        // Stocker le hash trouvé pour la prochaine fois
                        $this->storeTicketHashMapping($ticketHash, $registration, $accessControl, $event);
                        return $registration;
                    }
                } else {
                    $calculatedHash = $this->generateZoneTicketHash($registration, $accessControl, $event);
                    if (hash_equals($calculatedHash, $ticketHash)) {
                        // Stocker le hash trouvé pour la prochaine fois
                        $this->storeTicketHashMapping($ticketHash, $registration, $accessControl, $event);
                        return $registration;
                    }
                }
            }

            Log::warning('Aucune inscription trouvée pour le hash', [
                'ticket_hash' => $ticketHash,
                'event_id' => $event->id,
                'zone' => $accessControl->access_zone,
                'total_registrations' => $registrations->count()
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Erreur recherche inscription par hash', [
                'ticket_hash' => $ticketHash,
                'event_id' => $event->id,
                'zone' => $accessControl->access_zone,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    private function logAccessAttempt($event, $accessControl, $registration, $status, $request, $message, $statusData = [])
    {
        try {
            AccessTimeLog::on('tenant')->create([
                'event_id' => $event->id,
                'access_zone' => $accessControl->access_zone,
                'registration_id' => $registration->id ?? null,
                'attempt_time' => now(),
                'access_status' => $status,
                'scheduled_start_time' => $statusData['scheduled_start'] ?? null,
                'scheduled_end_time' => $statusData['scheduled_end'] ?? null,
                'verifier_id' => auth()->id(), // Si vous avez un système d'auth
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            Log::info('Tentative d\'accès zone', [
                'event_id' => $event->id,
                'zone' => $accessControl->access_zone,
                'registration_number' => $registration->registration_number ?? 'N/A',
                'participant' => $registration->fullname ?? 'Inconnu',
                'status' => $status,
                'message' => $message,
                'ip' => $request->ip(),
                'time' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur logging accès zone', [
                'error' => $e->getMessage(),
                'event_id' => $event->id,
                'zone' => $accessControl->access_zone,
                'registration_id' => $registration->id ?? null
            ]);
        }
    }

    public function getZoneAccessStats(Request $request, $orgSlug, $eventSlug, $zoneSlug)
    {
        $currentEvent = TenantHelper::getCurrentEvent();

        if (!$currentEvent) {
            return response()->json(['error' => 'Événement non trouvé'], 404);
        }

        return TenantHelper::withTenantConnection(function() use ($currentEvent, $zoneSlug) {
            $stats = AccessTimeLog::on('tenant')
                ->where('event_id', $currentEvent->id)
                ->where('access_zone', $zoneSlug)
                ->selectRaw('
                    access_status,
                    COUNT(*) as count,
                    COUNT(DISTINCT registration_id) as unique_participants
                ')
                ->groupBy('access_status')
                ->get();

            $timeline = AccessTimeLog::on('tenant')
                ->where('event_id', $currentEvent->id)
                ->where('access_zone', $zoneSlug)
                ->where('access_status', AccessTimeLog::STATUS_ON_TIME)
                ->selectRaw('
                    DATE_FORMAT(attempt_time, "%H:%i") as time_slot,
                    COUNT(*) as access_count
                ')
                ->groupBy('time_slot')
                ->orderBy('time_slot')
                ->get();

            return response()->json([
                'zone' => $zoneSlug,
                'stats_by_status' => $stats,
                'timeline' => $timeline,
                'generated_at' => now()->toISOString()
            ]);
        });
    }

    private function generateFallbackQRCode($registration, $event, $organization)
    {
        try {
            $fallbackHash = md5($registration->registration_number . '_general_' . time());
            $fallbackUrl = url("/{$organization->org_key}/{$event->event_slug}/verify/{$fallbackHash}?general=1");
            $qrCode = new QrCode($fallbackUrl);
            $qrCode->setSize(200);
            $qrCode->setMargin(10);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            $qrBase64 = base64_encode($result->getString());

            return [[
                'access_control_id' => null,
                'access_zone' => 'general',
                'zone_slug' => 'general',
                'zone_name' => 'Accès général',
                'zone_description' => 'QR code d\'accès général à l\'événement',
                'image' => 'data:image/png;base64,' . $qrBase64,
                'url' => $fallbackUrl,
                'ticket_hash' => $fallbackHash,
                'original_size' => 200,
                'is_fallback' => true
            ]];

        } catch (\Exception $e) {
            Log::error('Erreur génération QR fallback', [
                'registration_id' => $registration->id,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    private function generateFallbackQRForZone($registration, $event, $organization, $accessControl, $index)
    {
        try {
            $fallbackHash = md5($registration->registration_number . '_' . $accessControl->access_zone . '_' . time());
            $fallbackUrl = url("/{$organization->org_key}/{$event->event_slug}/verify/{$fallbackHash}?zone={$accessControl->zone_slug}&fallback=1");

            $qrCode = new QrCode($fallbackUrl);
            $qrCode->setSize(120);
            $qrCode->setMargin(10);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            $fallbackData = base64_encode($result->getString());

            return [
                'access_control_id' => $accessControl->id,
                'access_zone' => $accessControl->access_zone,
                'zone_slug' => $accessControl->zone_slug,
                'zone_name' => $accessControl->zone_name,
                'zone_description' => $accessControl->zone_description,
                'image' => 'data:image/png;base64,' . $fallbackData,
                'url' => $fallbackUrl,
                'ticket_hash' => $fallbackHash,
                'original_size' => 120,
                'index' => $index,
                'is_fallback' => true
            ];

        } catch (\Exception $e) {
            Log::error('Erreur génération QR fallback pour zone', [
                'registration_id' => $registration->id,
                'zone' => $accessControl->access_zone,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    private function generateQRCodeWithFallback($url, $size = 200)
    {
        try {
            $qrCode = new QrCode($url);
            $qrCode->setSize($size);
            $qrCode->setMargin(10);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            return $result->getString();

        } catch (\Exception $e) {
            Log::error('Génération QR Code complètement échouée', [
                'url' => $url,
                'size' => $size,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function storeTicketHashMapping($ticketHash, $registration, $accessControl, $event)
    {
        try {
            // Vérifier si le hash existe déjà
            $existingHash = DB::connection('tenant')
                ->table('ticket_hash_mappings')
                ->where('ticket_hash', $ticketHash)
                ->first();

            if ($existingHash) {
                Log::info('Hash déjà existant', [
                    'ticket_hash' => $ticketHash,
                    'registration_id' => $registration->id,
                    'zone' => $accessControl->access_zone
                ]);
                return;
            }

            // Insérer le nouveau mapping selon la structure de votre BDD
            DB::connection('tenant')->table('ticket_hash_mappings')->insert([
                'ticket_hash' => $ticketHash,
                'registration_id' => $registration->id,
                'event_id' => $event->id,
                'access_zone' => $accessControl->access_zone,
                'access_control_id' => $accessControl->id,
                'zone_name' => $accessControl->zone_name,
                'ticket_data' => json_encode([
                    'registration_id' => $registration->id,
                    'registration_number' => $registration->registration_number,
                    'zone' => $accessControl->access_zone,
                    'zone_name' => $accessControl->zone_name,
                    'email' => $registration->email,
                    'fullname' => $registration->fullname,
                    'phone' => $registration->phone,
                    'organization' => $registration->organization,
                    'ticket_type_id' => $registration->ticket_type_id,
                    'generated_at' => now()->toISOString(),
                    'event_id' => $event->id,
                    'access_control_id' => $accessControl->id
                ]),
                'is_active' => true, // Nouvelle colonne
                'created_at' => now(),
                'updated_at' => now() // Nouvelle colonne
            ]);

            Log::info('Hash stocké avec succès', [
                'ticket_hash' => $ticketHash,
                'registration_id' => $registration->id,
                'registration_number' => $registration->registration_number,
                'zone' => $accessControl->access_zone,
                'zone_name' => $accessControl->zone_name,
                'event_id' => $event->id
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur stockage hash ticket', [
                'ticket_hash' => $ticketHash,
                'registration_id' => $registration->id,
                'zone' => $accessControl->access_zone,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Ne pas faire échouer tout le processus si le stockage échoue
        }
    }

    /**
     * CORRECTION : Vérifier le statut général des hash en base
     */
    public function checkHashStatus(Request $request)
    {
        $currentOrganization = TenantHelper::getCurrentOrganization();
        $currentEvent = TenantHelper::getCurrentEvent();

        if (!$currentOrganization || !$currentEvent) {
            return response()->json(['error' => 'Contexte manquant'], 400);
        }

        return TenantHelper::withTenantConnection(function() use ($currentEvent, $currentOrganization) {
            $totalRegistrations = Registration::on('tenant')
                ->where('event_id', $currentEvent->id)
                ->where('status', 'confirmed')
                ->where('payment_status', 'paid')
                ->count();

            $totalHashes = DB::connection('tenant')
                ->table('ticket_hash_mappings')
                ->where('event_id', $currentEvent->id)
                ->where('is_active', true)
                ->count();

            $totalAccessControls = DB::connection('tenant')
                ->table('event_access_controls')
                ->where('event_id', $currentEvent->id)
                ->where('is_active', 1)
                ->count();

            $hashByZone = DB::connection('tenant')
                ->table('ticket_hash_mappings')
                ->where('event_id', $currentEvent->id)
                ->where('is_active', true)
                ->selectRaw('access_zone, zone_name, COUNT(*) as hash_count')
                ->groupBy('access_zone', 'zone_name')
                ->get();

            $expectedTotalHashes = $totalRegistrations * $totalAccessControls;

            $sampleHashes = DB::connection('tenant')
                ->table('ticket_hash_mappings')
                ->where('event_id', $currentEvent->id)
                ->where('is_active', true)
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'context' => [
                    'organization' => $currentOrganization->org_name,
                    'event' => $currentEvent->event_title,
                    'event_id' => $currentEvent->id
                ],
                'statistics' => [
                    'total_registrations' => $totalRegistrations,
                    'total_access_controls' => $totalAccessControls,
                    'expected_total_hashes' => $expectedTotalHashes,
                    'actual_total_hashes' => $totalHashes,
                    'completion_percentage' => $expectedTotalHashes > 0 ? round(($totalHashes / $expectedTotalHashes) * 100, 2) : 0
                ],
                'hash_by_zone' => $hashByZone,
                'sample_hashes' => $sampleHashes->map(function($hash) {
                    return [
                        'ticket_hash' => substr($hash->ticket_hash, 0, 20) . '...',
                        'access_zone' => $hash->access_zone,
                        'zone_name' => $hash->zone_name,
                        'registration_id' => $hash->registration_id,
                        'created_at' => $hash->created_at
                    ];
                }),
                'is_complete' => $totalHashes >= $expectedTotalHashes,
                'missing_hashes' => max(0, $expectedTotalHashes - $totalHashes),
                'recommendations' => [
                    'next_step' => $totalHashes == 0 ? 'Utilisez /regenerate-hashes pour générer tous les hash' :
                                ($totalHashes < $expectedTotalHashes ? 'Il manque des hash, relancez /regenerate-hashes' : 'Système complet, testez avec /test-verify/{hash}'),
                    'test_url' => $totalHashes > 0 ? url("/{$currentOrganization->org_key}/{$currentEvent->event_slug}/test-verify/" . ($sampleHashes->first()->ticket_hash ?? 'NO_HASH')) : null
                ]
            ]);
        });
    }

    public function handleAfribapayTransaction(Request $request): JsonResponse
    {
        Log::info('🔥 Webhook Afribapay reçu', [
            'headers' => $request->headers->all(),
            'payload' => $request->all(),
            'raw_content' => $request->getContent()
        ]);

        try {

            $data = $request->all();
            $reference = $data['reference'] ?? null;
            $transactionId = $data['transaction_id'] ?? null;
            $status = $data['status'] ?? null;
            $operator = $data['operator'] ?? null;
            $amount = $data['amount'] ?? null;
            $phoneNumber = $data['phone_number'] ?? null;

            if (!$reference || !$status) {
                Log::error('❌ Données webhook incomplètes', [
                    'reference' => $reference,
                    'status' => $status,
                    'transaction_id' => $transactionId
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Missing required fields: reference or status'
                ], 400);
            }

            Log::info('📋 Données webhook extraites', [
                'reference' => $reference,
                'transaction_id' => $transactionId,
                'status' => $status,
                'operator' => $operator,
                'amount' => $amount,
                'phone' => $phoneNumber
            ]);

            // 3. Déterminer le contexte organisationnel depuis la référence
            $context = $this->extractContextFromReference($reference);

            if (!$context) {
                Log::error('❌ Impossible d\'extraire le contexte depuis la référence', [
                    'reference' => $reference
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot extract organization/event context from reference'
                ], 400);
            }

            Log::info('🏢 Contexte extrait', [
                'organization' => $context['org_key'],
                'event' => $context['event_slug'],
                'reference' => $reference
            ]);

            // 4. Configurer la connexion tenant
            $organization = DB::table('organizations')->where('org_key', $context['org_key'])->first();

            if (!$organization) {
                Log::error('❌ Organisation non trouvée', [
                    'org_key' => $context['org_key']
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Organization not found'
                ], 404);
            }

            // 5. Traitement avec connexion tenant
            return $this->processAfribapayWebhookWithTenant($organization, $context, $data);

        } catch (\Exception $e) {
            Log::error('💥 Erreur critique webhook Afribapay', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Traitement du webhook dans le contexte tenant
     */
    private function processAfribapayWebhookWithTenant($organization, $context, $data): JsonResponse
    {
        // Configurer la connexion tenant
        app()->instance('current.organization', $organization);

        $tenantConfig = config('database.connections.mysql');
        $tenantConfig['database'] = $organization->database_name;
        config(['database.connections.tenant' => $tenantConfig]);
        DB::purge('tenant');

        return TenantHelper::withTenantConnection(function() use ($context, $data, $organization) {

            $reference = $data['reference'];
            $status = $data['status'];
            $transactionId = $data['transaction_id'] ?? null;
            $amount = $data['amount'] ?? null;

            // 1. Rechercher la transaction dans la base tenant
            $transaction = DB::connection('tenant')
                ->table('payment_transactions')
                ->where('transaction_reference', $reference)
                ->first();

            if (!$transaction) {
                Log::warning('⚠️ Transaction non trouvée en base tenant', [
                    'reference' => $reference,
                    'organization' => $organization->org_name
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaction not found in tenant database'
                ], 404);
            }

            Log::info('✅ Transaction trouvée', [
                'transaction_id' => $transaction->id,
                'current_status' => $transaction->status,
                'webhook_status' => $status
            ]);

            // 2. Vérifier si c'est un paiement réussi
            if ($this->isSuccessfulPayment($status)) {
                return $this->handleSuccessfulAfribapayPayment($transaction, $data, $organization);
            } else {
                return $this->handleFailedAfribapayPayment($transaction, $data, $status);
            }
        });
    }

    /**
     * Traitement d'un paiement Afribapay réussi
     */
    private function handleSuccessfulAfribapayPayment($transaction, $data, $organization): JsonResponse
    {
        // Éviter le double traitement
        if ($transaction->status === 'completed') {
            Log::info('ℹ️ Transaction déjà traitée comme complète', [
                'transaction_id' => $transaction->id,
                'reference' => $transaction->transaction_reference
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Transaction already processed'
            ]);
        }

        DB::connection('tenant')->beginTransaction();

        try {
            // 1. Récupérer les métadonnées de la transaction
            $metadata = json_decode($transaction->metadata, true);
            $participantData = $metadata['participant_data'] ?? null;
            $eventData = $metadata['event_data'] ?? null;

            if (!$participantData || !$eventData) {
                throw new \Exception('Métadonnées participant ou événement manquantes');
            }

            Log::info('📝 Données participant extraites', [
                'email' => $participantData['email'],
                'fullname' => $participantData['fullname'],
                'event_id' => $eventData['event_id']
            ]);

            // 2. Récupérer l'événement
            $event = DB::connection('tenant')->table('events')->where('id', $eventData['event_id'])->first();

            if (!$event) {
                throw new \Exception('Événement non trouvé');
            }

            // 3. Créer l'inscription avec le modèle Registration
            $registration = Registration::on('tenant')->create([
                'event_id' => $eventData['event_id'],
                'ticket_type_id' => $participantData['ticket_type_id'],
                'registration_number' => $this->generateRegistrationNumber(),
                'fullname' => $participantData['fullname'],
                'phone' => $participantData['phone'],
                'email' => $participantData['email'],
                'organization' => $participantData['organization'] ?? null,
                'position' => $participantData['position'] ?? null,
                /* 'question_1' => $participantData['question_1'] ?? null,
                'question_2' => $participantData['question_2'] ?? null,
                'question_3' => $participantData['question_3'] ?? null, */
                'ticket_price' => $transaction->amount,
                'amount_paid' => $transaction->amount,
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'confirmation_date' => now(),
                'form_data' => json_encode([
                    'full_name' => $participantData['fullname'],
                    'email' => $participantData['email'],
                    'phone' => $participantData['phone'],
                    'organization' => $participantData['organization'] ?? null,
                    'position' => $participantData['position'] ?? null,
                    /* 'question_1' => $participantData['question_1'] ?? null,
                    'question_2' => $participantData['question_2'] ?? null,
                    'question_3' => $participantData['question_3'] ?? null, */
                    'payment_method' => 'afribapay_' . ($data['operator'] ?? 'unknown'),
                    'transaction_id' => $data['transaction_id'] ?? null
                ])
            ]);

            Log::info('🎫 Inscription créée avec succès', [
                'registration_id' => $registration->id,
                'registration_number' => $registration->registration_number,
                'participant' => $registration->fullname
            ]);

            // 4. Mettre à jour la transaction
            DB::connection('tenant')
                ->table('payment_transactions')
                ->where('id', $transaction->id)
                ->update([
                    'registration_id' => $registration->id,
                    'status' => 'completed',
                    'external_reference' => $data['transaction_id'] ?? null,
                    'payment_date' => now(),
                    'processed_date' => now(),
                    'metadata' => json_encode(array_merge($metadata, [
                        'afribapay_webhook' => $data,
                        'completed_at' => now()->toISOString()
                    ]))
                ]);

            // 5. Incrémenter le compteur de tickets vendus
            TicketType::on('tenant')
                ->where('id', $participantData['ticket_type_id'])
                ->increment('quantity_sold');

            Log::info('📊 Compteur tickets mis à jour', [
                'ticket_type_id' => $participantData['ticket_type_id']
            ]);

            DB::connection('tenant')->commit();

            // 6. Générer et envoyer le ticket (asynchrone pour éviter de bloquer le webhook)
            try {
                $this->generateAndSendTicket($registration, $event, $organization);
                Log::info('📧 Ticket généré et envoyé', [
                    'registration_id' => $registration->id
                ]);
            } catch (\Exception $e) {
                Log::warning('⚠️ Erreur génération ticket (ne bloque pas le processus)', [
                    'error' => $e->getMessage(),
                    'registration_id' => $registration->id
                ]);
            }

            Log::info('🎉 Paiement Afribapay traité avec succès', [
                'reference' => $transaction->transaction_reference,
                'registration_number' => $registration->registration_number,
                'participant' => $registration->fullname,
                'amount' => $transaction->amount,
                'operator' => $data['operator'] ?? 'unknown'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Payment processed and registration created successfully',
                'data' => [
                    'registration_id' => $registration->id,
                    'registration_number' => $registration->registration_number,
                    'transaction_reference' => $transaction->transaction_reference
                ]
            ]);

        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();

            Log::error('💥 Erreur traitement paiement réussi Afribapay', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'transaction_id' => $transaction->id,
                'reference' => $transaction->transaction_reference
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process successful payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Traitement d'un paiement Afribapay échoué
     */
    private function handleFailedAfribapayPayment($transaction, $data, $status): JsonResponse
    {
        try {
            // Mettre à jour la transaction comme échouée
            DB::connection('tenant')
                ->table('payment_transactions')
                ->where('id', $transaction->id)
                ->update([
                    'status' => 'failed',
                    'external_reference' => $data['transaction_id'] ?? null,
                    'processed_date' => now(),
                    'metadata' => json_encode(array_merge(
                        json_decode($transaction->metadata, true) ?? [],
                        [
                            'afribapay_webhook' => $data,
                            'failed_at' => now()->toISOString(),
                            'failure_reason' => $status
                        ]
                    ))
                ]);

            Log::info('❌ Paiement Afribapay marqué comme échoué', [
                'reference' => $transaction->transaction_reference,
                'status' => $status,
                'operator' => $data['operator'] ?? 'unknown'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Payment failure processed successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('💥 Erreur traitement paiement échoué Afribapay', [
                'error' => $e->getMessage(),
                'transaction_id' => $transaction->id,
                'webhook_status' => $status
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process payment failure'
            ], 500);
        }
    }

    /**
     * Vérifier la signature Afribapay
     */
    private function verifyAfribapaySignature($payload, $receivedSignature): bool
    {
        if (!$receivedSignature) {
            Log::warning('⚠️ Aucune signature Afribapay reçue');
            return false;
        }

        $apiSecret = config('services.afribapay.api_secret', 'sk_rwg2ybfCzjnxxWkdeR');
        $computedSignature = hash_hmac('sha256', $payload, $apiSecret);

        $isValid = hash_equals($computedSignature, $receivedSignature);

        Log::info('🔐 Vérification signature Afribapay', [
            'signature_valid' => $isValid,
            'received_signature' => substr($receivedSignature, 0, 20) . '...',
            'computed_signature' => substr($computedSignature, 0, 20) . '...'
        ]);

        return $isValid;
    }

    private function extractContextFromReference($reference): ?array
    {
        Log::info('🔍 Analyse de la référence', [
            'reference' => $reference,
            'length' => strlen($reference)
        ]);

        // NOUVEAU: Pattern principal pour les références Czotick
        // Format: PAYMENT_METHOD-ORG_KEY-EVENT_SLUG-UNIQID
        if (preg_match('/^(OM|MTN|MOOV|WAVE|Czotick)-(.+)-([a-f0-9]+)$/i', $reference, $matches)) {
            $paymentMethod = strtolower($matches[1]);
            $middlePart = $matches[2]; // Partie entre le payment method et l'uniqid
            $uniqid = $matches[3];

            Log::info('🎯 Pattern principal détecté', [
                'payment_method' => $paymentMethod,
                'middle_part' => $middlePart,
                'uniqid' => $uniqid
            ]);

            // Analyser la partie centrale selon les différents cas
            $context = $this->parseMiddlePart($middlePart, $paymentMethod);

            if ($context) {
                $context['payment_method'] = $paymentMethod;
                $context['uniqid'] = $uniqid;
                return $context;
            }
        }

        // FALLBACK: Pattern legacy ou alternatif
        return $this->extractLegacyContext($reference);
    }

    /**
     * Analyser la partie centrale de la référence selon différents patterns
     */
    private function parseMiddlePart($middlePart, $paymentMethod): ?array
    {
        $parts = explode('-', $middlePart);
        $totalParts = count($parts);

        Log::info('📊 Analyse de la partie centrale', [
            'middle_part' => $middlePart,
            'parts' => $parts,
            'total_parts' => $totalParts
        ]);

        // CAS 1: RIA-2025 (JCI-ABIDJAN-IVOIRE-RIA-2025)
        if ($this->isRiaPattern($parts)) {
            return $this->parseRiaPattern($parts);
        }

        // CAS 2: Pattern avec org_key répétée (ORG-EVENT-SLUG-ORG)
        if ($totalParts >= 4) {
            $duplicateContext = $this->parseDuplicateOrgPattern($parts);
            if ($duplicateContext) {
                return $duplicateContext;
            }
        }

        // CAS 3: Pattern simple (ORG-KEY-EVENT-SLUG)
        return $this->parseSimplePattern($parts);
    }

    /**
     * Détecter si c'est le pattern RIA-2025
     */
    private function isRiaPattern($parts): bool
    {
        $middlePart = implode('-', $parts);

        // Patterns RIA spécifiques
        $riaPatterns = [
            'JCI-ABIDJAN-IVOIRE-RIA-2025',
            'JCI-EMERAUDE-RIA-2025',
            'INF-JCI-CI-RIA-2025'
        ];

        foreach ($riaPatterns as $pattern) {
            if (strtolower($middlePart) === strtolower($pattern)) {
                return true;
            }
        }

        // Pattern générique RIA
        return (in_array('RIA', $parts) && in_array('2025', $parts));
    }

/**
 * Parser le pattern RIA-2025
 */
private function parseRiaPattern($parts): ?array
{
    $middlePart = implode('-', $parts);

    // Mapping des patterns RIA connus
    $riaMapping = [
        'JCI-ABIDJAN-IVOIRE-RIA-2025' => [
            'org_key' => 'jci-abidjan-ivoire',
            'event_slug' => 'ria-2025'
        ],
        'JCI-EMERAUDE-RIA-2025' => [
            'org_key' => 'jci-emeraude',
            'event_slug' => 'ria-2025'
        ],
        'INF-JCI-CI-RIA-2025' => [
            'org_key' => 'inf-jci-ci',
            'event_slug' => 'ria-2025'
        ]
    ];

    $upperMiddlePart = strtoupper($middlePart);

    foreach ($riaMapping as $pattern => $context) {
        if ($upperMiddlePart === $pattern) {
            Log::info('✅ Pattern RIA reconnu', [
                'pattern' => $pattern,
                'org_key' => $context['org_key'],
                'event_slug' => $context['event_slug']
            ]);

            return $context;
        }
    }

    return null;
}

/**
 * Parser le pattern avec org_key dupliquée
 * Format: ORG_KEY-EVENT-SLUG-WITH-ORG_KEY
 */
private function parseDuplicateOrgPattern($parts): ?array
{
    $totalParts = count($parts);

    // Essayer différentes longueurs pour l'org_key (1 à 4 segments)
    for ($orgKeyLength = 1; $orgKeyLength <= min(4, floor($totalParts / 2)); $orgKeyLength++) {
        $possibleOrgKey = implode('-', array_slice($parts, 0, $orgKeyLength));
        $remainingParts = array_slice($parts, $orgKeyLength);

        // Vérifier si l'event_slug se termine par l'org_key
        if (count($remainingParts) >= $orgKeyLength) {
            $eventSlugEnd = implode('-', array_slice($remainingParts, -$orgKeyLength));

            if (strtolower($possibleOrgKey) === strtolower($eventSlugEnd)) {
                $eventSlug = implode('-', $remainingParts);

                Log::info('✅ Pattern avec duplication détecté', [
                    'org_key' => strtolower($possibleOrgKey),
                    'event_slug' => strtolower($eventSlug),
                    'org_key_length' => $orgKeyLength
                ]);

                return [
                    'org_key' => strtolower($possibleOrgKey),
                    'event_slug' => strtolower($eventSlug)
                ];
            }
        }
    }

    return null;
}

/**
 * Parser le pattern simple
 * Format: ORG-KEY-EVENT-SLUG
 */
private function parseSimplePattern($parts): ?array
{
    $totalParts = count($parts);

    if ($totalParts < 2) {
        return null;
    }

    // Essayer différentes répartitions org_key/event_slug
    for ($orgKeyLength = 1; $orgKeyLength <= min(3, $totalParts - 1); $orgKeyLength++) {
        $orgKey = implode('-', array_slice($parts, 0, $orgKeyLength));
        $eventSlug = implode('-', array_slice($parts, $orgKeyLength));

        // Valider que c'est plausible
        if (strlen($orgKey) >= 3 && strlen($eventSlug) >= 3) {
            Log::info('🔧 Pattern simple détecté', [
                'org_key' => strtolower($orgKey),
                'event_slug' => strtolower($eventSlug),
                'org_key_parts' => $orgKeyLength,
                'event_parts' => $totalParts - $orgKeyLength
            ]);

            return [
                'org_key' => strtolower($orgKey),
                'event_slug' => strtolower($eventSlug)
            ];
        }
    }

    return null;
}

/**
 * Extraire le contexte pour les formats legacy
 */
private function extractLegacyContext($reference): ?array
{
    // Pattern legacy simple: PREFIX-SOMETHING-UNIQID
    if (preg_match('/^(.+)-([a-f0-9]+)$/i', $reference, $matches)) {
        $mainPart = $matches[1];
        $uniqid = $matches[2];

        // Essayer de diviser en org_key et event_slug
        $parts = explode('-', $mainPart);

        if (count($parts) >= 2) {
            $midPoint = floor(count($parts) / 2);
            $orgKey = implode('-', array_slice($parts, 0, $midPoint));
            $eventSlug = implode('-', array_slice($parts, $midPoint));

            Log::info('🔄 Format legacy détecté', [
                'org_key' => strtolower($orgKey),
                'event_slug' => strtolower($eventSlug),
                'uniqid' => $uniqid
            ]);

            return [
                'org_key' => strtolower($orgKey),
                'event_slug' => strtolower($eventSlug),
                'uniqid' => $uniqid
            ];
        }
    }

    Log::warning('⚠️ Format de référence non reconnu', [
        'reference' => $reference,
        'patterns_tried' => [
            'principal' => 'PAYMENT_METHOD-ORG_KEY-EVENT_SLUG-UNIQID',
            'ria_specific' => 'Patterns RIA-2025 spécifiques',
            'duplicate_org' => 'ORG_KEY-EVENT-SLUG-ORG_KEY',
            'simple' => 'ORG-KEY-EVENT-SLUG',
            'legacy' => 'Formats anciens'
        ]
    ]);

    return null;
}


    /**
     * Vérifier si le statut indique un paiement réussi
     */
    private function isSuccessfulPayment($status): bool
    {
        $successStatuses = [
            'SUCCESS',
            'SUCCESSFUL',
            'Successful',
            'succeeded',
            'COMPLETED',
            'completed'
        ];

        return in_array($status, $successStatuses, true);
    }

// CORRECTION 1: Méthode pour vérifier le statut de paiement
public function checkPaymentStatus(Request $request): JsonResponse
{
    // Validation plus stricte
    $validator = Validator::make($request->all(), [
        'transaction_reference' => 'required|string|min:10|max:100'
    ]);

    if ($validator->fails()) {
        Log::warning('Validation échouée pour checkPaymentStatus', [
            'errors' => $validator->errors()->toArray(),
            'input' => $request->all()
        ]);

        return response()->json([
            'status' => 'error',
            'message' => 'Référence de transaction invalide',
            'errors' => $validator->errors()
        ], 400);
    }

    $currentOrganization = TenantHelper::getCurrentOrganization();
    $currentEvent = TenantHelper::getCurrentEvent();

    if (!$currentOrganization || !$currentEvent) {
        Log::error('Contexte manquant dans checkPaymentStatus', [
            'organization' => $currentOrganization ? $currentOrganization->org_key : 'null',
            'event' => $currentEvent ? $currentEvent->event_slug : 'null'
        ]);

        return response()->json([
            'status' => 'error',
            'message' => 'Contexte d\'organisation ou d\'événement manquant'
        ], 500);
    }

    try {
        return TenantHelper::withTenantConnection(function() use ($request, $currentOrganization, $currentEvent) {

            $transactionReference = $request->input('transaction_reference');

            Log::info('Recherche transaction pour vérification statut', [
                'transaction_reference' => $transactionReference,
                'organization' => $currentOrganization->org_key,
                'event' => $currentEvent->event_slug
            ]);

            $transaction = DB::connection('tenant')
                ->table('payment_transactions')
                ->where('transaction_reference', $transactionReference)
                ->first();

            if (!$transaction) {
                Log::warning('Transaction non trouvée', [
                    'transaction_reference' => $transactionReference,
                    'organization' => $currentOrganization->org_key,
                    'event' => $currentEvent->event_slug
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaction non trouvée'
                ], 404);
            }

            Log::info('Transaction trouvée', [
                'transaction_id' => $transaction->id,
                'status' => $transaction->status,
                'reference' => $transaction->transaction_reference
            ]);

            // Si la transaction est déjà complétée ou échouée, retourner le statut immédiatement
            if (in_array($transaction->status, ['completed', 'failed'])) {
                $redirectUrl = null;
                if ($transaction->status === 'completed') {
                    $redirectUrl = route('event.registration', [
                        'org_slug' => $currentOrganization->org_key,
                        'event_slug' => $currentEvent->event_slug
                    ]) . "?payment=success&ref=" . $transactionReference;
                }

                return response()->json([
                    'status' => $transaction->status,
                    'message' => $transaction->status === 'completed' ? 'Paiement confirmé' : 'Paiement échoué',
                    'redirect_url' => $redirectUrl,
                    'transaction_reference' => $transactionReference
                ]);
            }

            // Pour les transactions en cours, vérifier auprès d'Afribapay
            return $this->checkAfribapayTransactionStatus($transaction);
        });

    } catch (\Exception $e) {
        Log::error('Erreur dans checkPaymentStatus', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'transaction_reference' => $request->input('transaction_reference'),
            'organization' => $currentOrganization->org_key ?? 'unknown',
            'event' => $currentEvent->event_slug ?? 'unknown'
        ]);

        return response()->json([
            'status' => 'error',
            'message' => 'Erreur lors de la vérification du statut'
        ], 500);
    }
}

// CORRECTION 3: Améliorer la méthode de création d'inscription
private function createRegistrationFromTransaction($transactionId, $status = 'completed')
{
    try {
        $transaction = DB::connection('tenant')
            ->table('payment_transactions')
            ->where('id', $transactionId)
            ->first();

        if (!$transaction) {
            throw new \Exception('Transaction non trouvée');
        }

        $metadata = json_decode($transaction->metadata, true);
        $participantData = $metadata['participant_data'];
        $eventData = $metadata['event_data'];

        Log::info('Création inscription depuis transaction', [
            'transaction_id' => $transactionId,
            'participant' => $participantData['fullname'] ?? 'Unknown',
            'event_id' => $eventData['event_id'],
            'is_partial_completion' => isset($participantData['is_partial_completion']) && $participantData['is_partial_completion'],
            'registration_id' => $participantData['registration_id'] ?? null
        ]);

        // Vérifier si c'est une finalisation de paiement partiel
        $isPartialCompletion = isset($participantData['is_partial_completion']) && $participantData['is_partial_completion'];
        $existingRegistrationId = $participantData['registration_id'] ?? null;

        if ($isPartialCompletion && $existingRegistrationId) {
            // Mettre à jour l'inscription existante
            $existingRegistration = DB::connection('tenant')
                ->table('registrations')
                ->where('id', $existingRegistrationId)
                ->first();

            if ($existingRegistration) {
                // Ajouter le montant payé au montant déjà payé
                $newAmountPaid = $existingRegistration->amount_paid + $transaction->amount;
                $isFullyPaid = $newAmountPaid >= $existingRegistration->ticket_price;

                DB::connection('tenant')
                    ->table('registrations')
                    ->where('id', $existingRegistrationId)
                    ->update([
                        'amount_paid' => $newAmountPaid,
                        'payment_status' => $isFullyPaid ? 'paid' : 'partial',
                        'status' => $isFullyPaid ? 'confirmed' : $existingRegistration->status,
                        'confirmation_date' => $isFullyPaid ? now() : $existingRegistration->confirmation_date,
                        'updated_at' => now()
                    ]);

                // Mettre à jour la transaction avec l'ID d'inscription
                DB::connection('tenant')
                    ->table('payment_transactions')
                    ->where('id', $transactionId)
                    ->update([
                        'registration_id' => $existingRegistrationId,
                        'status' => $status,
                        'updated_at' => now()
                    ]);

                Log::info('Inscription partielle mise à jour avec succès', [
                    'transaction_id' => $transactionId,
                    'registration_id' => $existingRegistrationId,
                    'amount_paid_before' => $existingRegistration->amount_paid,
                    'amount_paid_after' => $newAmountPaid,
                    'is_fully_paid' => $isFullyPaid
                ]);

                // Si entièrement payé, générer et envoyer le ticket
                if ($isFullyPaid) {
                    try {
                        $this->generateAndSendTicketForRegistration($existingRegistrationId);
                    } catch (\Exception $e) {
                        Log::warning('Erreur génération ticket (ne bloque pas la mise à jour)', [
                            'registration_id' => $existingRegistrationId,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                return $existingRegistrationId;
            }
        }

        // Générer un numéro d'inscription unique
        $registrationNumber = $this->generateRegistrationNumber();

        // Créer l'inscription
        $registrationId = DB::connection('tenant')
            ->table('registrations')
            ->insertGetId([
                'event_id' => $eventData['event_id'],
                'ticket_type_id' => $participantData['ticket_type_id'],
                'registration_number' => $registrationNumber,
                'fullname' => $participantData['fullname'],
                'email' => $participantData['email'],
                'phone' => $participantData['phone'],
                'organization' => $participantData['organization'],
                'position' => $participantData['position'],
                'registration_status' => 'confirmed',
                'payment_status' => 'paid',
                'amount_paid' => $transaction->amount,
                'currency' => $transaction->currency,
                'ticket_price' => $transaction->amount,
                'status' => 'confirmed',
                'confirmation_date' => now(),
                'form_data' => json_encode([
                    'payment_method' => $transaction->payment_provider,
                    'transaction_reference' => $transaction->transaction_reference
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ]);

        // Mettre à jour la transaction avec l'ID d'inscription
        DB::connection('tenant')
            ->table('payment_transactions')
            ->where('id', $transactionId)
            ->update([
                'registration_id' => $registrationId,
                'status' => $status,
                'updated_at' => now()
            ]);

        // Mettre à jour le compteur de tickets vendus
        DB::connection('tenant')
            ->table('ticket_types')
            ->where('id', $participantData['ticket_type_id'])
            ->increment('quantity_sold');

        Log::info('Inscription créée avec succès', [
            'transaction_id' => $transactionId,
            'registration_id' => $registrationId,
            'registration_number' => $registrationNumber,
            'participant' => $participantData['fullname']
        ]);

        // Tenter de générer et envoyer le ticket
        try {
            $this->generateAndSendTicketForRegistration($registrationId);
        } catch (\Exception $e) {
            Log::warning('Erreur génération ticket (ne bloque pas la création)', [
                'registration_id' => $registrationId,
                'error' => $e->getMessage()
            ]);
        }

        return $registrationId;

    } catch (\Exception $e) {
        Log::error('Erreur createRegistrationFromTransaction', [
            'transaction_id' => $transactionId,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        throw $e;
    }
}

// CORRECTION 4: Méthode pour générer et envoyer le ticket depuis une inscription
private function generateAndSendTicketForRegistration($registrationId)
{
    try {
        $currentOrganization = TenantHelper::getCurrentOrganization();
        $currentEvent = TenantHelper::getCurrentEvent();

        if (!$currentOrganization || !$currentEvent) {
            throw new \Exception('Contexte organisation/événement manquant');
        }

        // Récupérer l'inscription complète
        $registration = Registration::on('tenant')->find($registrationId);

        if (!$registration) {
            throw new \Exception('Inscription non trouvée');
        }

        Log::info('Génération ticket pour inscription', [
            'registration_id' => $registrationId,
            'registration_number' => $registration->registration_number,
            'participant' => $registration->fullname
        ]);

        // Générer et envoyer le ticket
        $this->generateAndSendTicket($registration, $currentEvent, $currentOrganization);

        Log::info('Ticket généré et envoyé avec succès', [
            'registration_id' => $registrationId,
            'participant' => $registration->fullname
        ]);

    } catch (\Exception $e) {
        Log::error('Erreur génération ticket pour inscription', [
            'registration_id' => $registrationId,
            'error' => $e->getMessage()
        ]);
        throw $e;
    }
}

private function checkAfribapayTransactionStatus($transaction): JsonResponse
{
    try {
        $result = $this->getAfribapayAccessToken();

        if (!isset($result['token'])) {
            Log::warning('Impossible d\'obtenir le token Afribapay pour vérification statut', [
                'transaction_id' => $transaction->id
            ]);

            return response()->json([
                'status' => 'pending',
                'message' => 'Vérification en cours...'
            ]);
        }

        $authorization_token = $result['token'];

        Log::info('Vérification statut Afribapay', [
            'transaction_id' => $transaction->id,
            'reference' => $transaction->transaction_reference
        ]);

        $url = 'https://api.afribapay.com/v1/status?order_id=' . $transaction->transaction_reference;

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'authorization: Bearer '. $authorization_token,
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if (curl_errno($curl)) {
            $curlError = curl_error($curl);
            curl_close($curl);

            Log::error('Erreur CURL vérification Afribapay', [
                'error' => $curlError,
                'transaction_id' => $transaction->id
            ]);

            return response()->json([
                'status' => 'pending',
                'message' => 'Vérification en cours...'
            ]);
        }

        curl_close($curl);

        if ($httpCode !== 200) {
            Log::warning('Code HTTP non-200 de Afribapay', [
                'http_code' => $httpCode,
                'response' => substr($response, 0, 500),
                'transaction_id' => $transaction->id
            ]);

            return response()->json([
                'status' => 'pending',
                'message' => 'Vérification en cours...'
            ]);
        }

        $responseArray = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Erreur décodage JSON Afribapay', [
                'error' => json_last_error_msg(),
                'response_preview' => substr($response, 0, 200),
                'transaction_id' => $transaction->id
            ]);

            return response()->json([
                'status' => 'pending',
                'message' => 'Vérification en cours...'
            ]);
        }

        // Vérifier la structure de la réponse
        $status = $responseArray['data']['status'] ?? null;
        if (!$status) {
            Log::error('Statut manquant dans réponse Afribapay', [
                'response_structure' => array_keys($responseArray),
                'data_structure' => isset($responseArray['data']) ? array_keys($responseArray['data']) : 'data key missing',
                'transaction_id' => $transaction->id
            ]);

            return response()->json([
                'status' => 'pending',
                'message' => 'Vérification en cours...'
            ]);
        }

        Log::info('Statut reçu de Afribapay', [
            'status' => $status,
            'transaction_id' => $transaction->id,
            'reference' => $transaction->transaction_reference
        ]);

        // Traitement selon le statut reçu
        switch (strtolower($status)) {
            case 'success':
            case 'successful':
            case 'completed':
                // Mettre à jour la transaction locale
                DB::connection('tenant')
                    ->table('payment_transactions')
                    ->where('id', $transaction->id)
                    ->update([
                        'status' => 'completed',
                        'external_reference' => $responseArray['data']['transaction_id'] ?? null,
                        'payment_date' => now(),
                        'processed_date' => now(),
                        'updated_at' => now()
                    ]);

                // Créer l'inscription si pas encore fait
                if (!$transaction->registration_id) {
                    try {
                        $registrationId = $this->createRegistrationFromTransaction($transaction->id, 'completed');
                        Log::info('Inscription créée après confirmation Afribapay', [
                            'transaction_id' => $transaction->id,
                            'registration_id' => $registrationId
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Erreur création inscription après confirmation', [
                            'transaction_id' => $transaction->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                return response()->json([
                    'status' => 'completed',
                    'message' => 'Paiement confirmé avec succès',
                    'redirect_url' => route('event.registration', [
                        'org_slug' => TenantHelper::getCurrentOrganization()->org_key,
                        'event_slug' => TenantHelper::getCurrentEvent()->event_slug
                    ]) . "?payment=success&ref=" . $transaction->transaction_reference
                ]);

            case 'failed':
            case 'error':
            case 'cancelled':
                DB::connection('tenant')
                    ->table('payment_transactions')
                    ->where('id', $transaction->id)
                    ->update([
                        'status' => 'failed',
                        'processed_date' => now(),
                        'updated_at' => now()
                    ]);

                Log::info('Paiement marqué comme échoué', [
                    'transaction_id' => $transaction->id,
                    'afribapay_status' => $status
                ]);

                return response()->json([
                    'status' => 'failed',
                    'message' => 'Le paiement a échoué'
                ]);

            case 'pending':
            case 'processing':
            default:
                // NOUVEAU: Extraire le code pays depuis les métadonnées de la transaction
                $phoneCountry = $this->extractPhoneCountryFromTransaction($transaction);
                $provider = strtolower($transaction->payment_provider);

                // NOUVEAU: Générer l'instruction selon le pays et l'opérateur
                $instruction = $this->getStatusCheckInstruction($provider, $phoneCountry);

                Log::info('Instruction de paiement générée', [
                    'transaction_id' => $transaction->id,
                    'provider' => $provider,
                    'phone_country' => $phoneCountry,
                    'instruction' => $instruction
                ]);

                return response()->json([
                    'status' => 'pending',
                    'message' => 'Paiement en cours. ' . $instruction
                ]);
        }

    } catch (\Exception $e) {
        Log::error('Erreur vérification statut Afribapay', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'transaction_id' => $transaction->id
        ]);

        return response()->json([
            'status' => 'pending',
            'message' => 'Vérification en cours...'
        ]);
    }
}

private function getPaymentInstruction(string $operator, string $phoneCountry): string
{
    // Instructions par pays et opérateur
    $instructions = [
        '+225' => [ // Côte d'Ivoire - Instructions spécifiques avec codes USSD
            'mtn' => 'Veuillez composer *133# puis l\'option 1 pour valider votre paiement.',
            'moov' => 'Veuillez composer *155*15# pour valider votre paiement.',
            'orange' => 'Vérification en cours avec Orange Money...',
            'wave' => 'Paiement Wave en cours de traitement...'
        ],
        '+229' => [ // Bénin - Instructions génériques
            'mtn' => 'Paiement MTN en cours... veuillez valider votre transaction.',
            'moov' => 'Paiement Moov en cours... veuillez valider votre transaction.',
            'orange' => 'Paiement Orange Money en cours... veuillez valider votre transaction.',
            'wave' => 'Paiement Wave en cours... veuillez valider votre transaction.'
        ],
        '+226' => [ // Burkina Faso - Instructions génériques
            'orange' => 'Paiement Orange Money en cours... veuillez valider votre transaction.',
            'moov' => 'Paiement Moov en cours... veuillez valider votre transaction.',
            'mtn' => 'Paiement MTN en cours... veuillez valider votre transaction.',
            'wave' => 'Paiement Wave en cours... veuillez valider votre transaction.'
        ],
        '+223' => [ // Mali - Instructions génériques
            'orange' => 'Paiement Orange Money en cours... veuillez valider votre transaction.',
            'moov' => 'Paiement Moov en cours... veuillez valider votre transaction.',
            'mtn' => 'Paiement MTN en cours... veuillez valider votre transaction.',
            'wave' => 'Paiement Wave en cours... veuillez valider votre transaction.'
        ],
        '+228' => [ // Togo - Instructions génériques
            'moov' => 'Paiement Moov en cours... veuillez valider votre transaction.',
            'mtn' => 'Paiement MTN en cours... veuillez valider votre transaction.',
            'orange' => 'Paiement Orange Money en cours... veuillez valider votre transaction.',
            'wave' => 'Paiement Wave en cours... veuillez valider votre transaction.'
        ]
    ];

    // Normaliser l'opérateur
    $normalizedOperator = $this->normalizeOperatorName($operator);

    // Retourner l'instruction appropriée
    if (isset($instructions[$phoneCountry][$normalizedOperator])) {
        return $instructions[$phoneCountry][$normalizedOperator];
    }

    // Instructions de fallback selon le pays
    $fallbackInstructions = [
        '+225' => 'Veuillez valider votre transaction sur votre téléphone.',
        '+229' => 'Veuillez valider votre transaction.',
        '+226' => 'Veuillez valider votre transaction.',
        '+223' => 'Veuillez valider votre transaction.',
        '+228' => 'Veuillez valider votre transaction.'
    ];

    return $fallbackInstructions[$phoneCountry] ?? 'Veuillez valider votre transaction.';
}

/**
 * Normaliser le nom de l'opérateur
 */
private function normalizeOperatorName(string $operator): string
{
    $operator = strtolower(trim($operator));

    // Mappings de normalisation
    $mappings = [
        'mtn money' => 'mtn',
        'mtn_money' => 'mtn',
        'mtnmoney' => 'mtn',
        'moov money' => 'moov',
        'moov_money' => 'moov',
        'moovmoney' => 'moov',
        'orange money' => 'orange',
        'orange_money' => 'orange',
        'orangemoney' => 'orange'
    ];

    return $mappings[$operator] ?? $operator;
}

/**
 * TOUTES LES AUTRES MÉTHODES UTILITAIRES MANQUANTES
 * À ajouter également au PaymentController si elles n'existent pas
 */

/**
 * Extraire le code pays depuis les métadonnées de transaction
 */
private function extractPhoneCountryFromTransaction($transaction): string
{
    try {
        if ($transaction->metadata) {
            $metadata = json_decode($transaction->metadata, true);

            // Priorité 1: Vérifier dans participant_data
            if (isset($metadata['participant_data']['phone_country'])) {
                return $metadata['participant_data']['phone_country'];
            }

            // Priorité 2: Vérifier dans payment_data
            if (isset($metadata['payment_data']['country'])) {
                return $this->convertIsoToPhoneCountry($metadata['payment_data']['country']);
            }

            // Priorité 3: Extraire du numéro de téléphone complet
            if (isset($metadata['participant_data']['full_phone_number'])) {
                $fullPhone = $metadata['participant_data']['full_phone_number'];
                if (str_starts_with($fullPhone, '+')) {
                    preg_match('/^(\+\d{3})/', $fullPhone, $matches);
                    if (isset($matches[1])) {
                        return $matches[1];
                    }
                }
            }

            // Priorité 4: Extraire du champ phone principal
            if (isset($metadata['participant_data']['phone'])) {
                $phone = $metadata['participant_data']['phone'];
                if (str_starts_with($phone, '+')) {
                    preg_match('/^(\+\d{3})/', $phone, $matches);
                    if (isset($matches[1])) {
                        return $matches[1];
                    }
                }
            }
        }
    } catch (\Exception $e) {
        Log::warning('Erreur extraction code pays depuis transaction', [
            'transaction_id' => $transaction->id ?? 'unknown',
            'error' => $e->getMessage()
        ]);
    }

    // Valeur par défaut
    return '+225';
}

/**
 * Convertir un code ISO pays en code téléphonique
 */
private function convertIsoToPhoneCountry(string $isoCode): string
{
    $mapping = [
        'CI' => '+225', // Côte d'Ivoire
        'BJ' => '+229', // Bénin
        'BF' => '+226', // Burkina Faso
        'ML' => '+223', // Mali
        'TG' => '+228'  // Togo
    ];

    return $mapping[strtoupper($isoCode)] ?? '+225';
}

/**
 * Extraire le code pays d'un numéro de téléphone complet
 */
private function extractCountryFromPhone(string $fullPhone): string
{
    // Nettoyer le numéro
    $cleanPhone = preg_replace('/[^\d+]/', '', $fullPhone);

    if (str_starts_with($cleanPhone, '+')) {
        // Extraire les 4 premiers caractères (+XXX)
        $countryCode = substr($cleanPhone, 0, 4);

        // Vérifier si c'est un code valide
        $validCodes = ['+225', '+229', '+226', '+223', '+228'];
        if (in_array($countryCode, $validCodes)) {
            return $countryCode;
        }
    }

    // Valeur par défaut
    return '+225';
}

/**
 * Extraire l'opérateur depuis la requête
 */
private function extractOperatorFromRequest($request): string
{
    if (isset($request->senderOperator)) {
        $operator = strtolower(trim($request->senderOperator));

        if (str_contains($operator, 'orange')) return 'orange';
        if (str_contains($operator, 'mtn')) return 'mtn';
        if (str_contains($operator, 'moov')) return 'moov';
        if (str_contains($operator, 'wave')) return 'wave';
    }

    return 'unknown';
}

/**
 * Obtenir le taux de frais selon le pays et l'opérateur
 */
private function getFeeRate(string $phoneCountry, string $operator): float
{
    // Configuration des frais par pays et opérateur
    $feeRates = [
        '+225' => [ // Côte d'Ivoire - Frais par opérateur
            'wave' => 1.6,   // Wave - 1.5%
            'orange' => 2.5, // Orange Money - 2.5%
            'mtn' => 2.0,    // MTN Money - 2%
            'moov' => 2.5    // Moov Money - 2.5%
        ],
        '+229' => [ // Bénin - Frais uniformes par pays
            'mtn' => 0,
            'moov' => 0,
            'orange' => 0,
            'wave' => 0
        ],
        '+226' => [ // Burkina Faso - Frais uniformes par pays
            'orange' => 0,
            'moov' => 0,
            'mtn' => 0,
            'wave' => 0
        ],
        '+223' => [ // Mali - Frais uniformes par pays
            'orange' => 0,
            'moov' => 0,
            'mtn' => 0,
            'wave' => 0
        ],
        '+228' => [ // Togo - Frais uniformes par pays
            'moov' => 0,
            'mtn' => 0,
            'orange' => 0,
            'wave' => 0
        ]
        /* '+229' => [ // Bénin - Frais uniformes par pays
            'mtn' => 2.5,
            'moov' => 2.5,
            'orange' => 2.5,
            'wave' => 2.5
        ],
        '+226' => [ // Burkina Faso - Frais uniformes par pays
            'orange' => 3.0,
            'moov' => 3.0,
            'mtn' => 3.0,
            'wave' => 3.0
        ],
        '+223' => [ // Mali - Frais uniformes par pays
            'orange' => 3.0,
            'moov' => 3.0,
            'mtn' => 3.0,
            'wave' => 3.0
        ],
        '+228' => [ // Togo - Frais uniformes par pays
            'moov' => 3.5,
            'mtn' => 3.5,
            'orange' => 3.5,
            'wave' => 3.5
        ] */
    ];

    // Retourner le taux spécifique ou 0 par défaut
    return $feeRates[$phoneCountry][$operator] ?? 0;
}

/**
 * Arrondir au multiple le plus proche
 */
private function roundToNearestMultiple(float $number, int $multiple): int
{
    return (int) (round($number / $multiple) * $multiple);
}

/**
 * Nettoyer le numéro de téléphone pour Afribapay
 */
private function cleanPhoneNumber(string $fullPhone): string
{
    // Enlever le + et garder seulement les chiffres
    $cleaned = preg_replace('/[^\d]/', '', $fullPhone);

    // Pour Afribapay, on veut le numéro sans le +
    // Exemple: +22561234567 devient 22561234567
    return $cleaned;
}

/**
 * Obtenir la configuration du pays selon le code téléphonique
 */
private function getCountryConfig(string $phoneCountry): array
{
    $countries = [
        '+225' => ['code' => 'CI', 'name' => 'Côte d\'Ivoire'],
        '+229' => ['code' => 'BJ', 'name' => 'Bénin'],
        '+226' => ['code' => 'BF', 'name' => 'Burkina Faso'],
        '+223' => ['code' => 'ML', 'name' => 'Mali'],
        '+228' => ['code' => 'TG', 'name' => 'Togo']
    ];

    return $countries[$phoneCountry] ?? ['code' => 'CI', 'name' => 'Côte d\'Ivoire'];
}

/**
 * Générer l'instruction selon le pays et l'opérateur
 */
private function getStatusCheckInstruction(string $provider, string $phoneCountry): string
{
    // Instructions par pays et opérateur
    $instructions = [
        '+225' => [ // Côte d'Ivoire - Instructions spécifiques avec codes USSD
            'mtn' => 'Veuillez composer *133# puis l\'option 1 pour valider votre paiement.',
            'moov' => 'Veuillez composer *155*15# pour valider votre paiement.',
            'orange' => 'Vérification en cours avec Orange Money...'
        ],
        '+229' => [ // Bénin - Instructions génériques
            'mtn' => 'Paiement MTN en cours... veuillez valider votre transaction.',
            'moov' => 'Paiement Moov en cours... veuillez valider votre transaction.',
            'orange' => 'Vérification en cours avec Orange Money...'
        ],
        '+226' => [ // Burkina Faso - Instructions génériques
            'orange' => 'Vérification en cours avec Orange Money...',
            'moov' => 'Paiement Moov en cours... veuillez valider votre transaction.'
        ],
        '+223' => [ // Mali - Instructions génériques
            'orange' => 'Vérification en cours avec Orange Money...',
            'moov' => 'Paiement Moov en cours... veuillez valider votre transaction.'
        ],
        '+228' => [ // Togo - Instructions génériques
            'moov' => 'Paiement Moov en cours... veuillez valider votre transaction.'
        ]
    ];

    // Déterminer l'opérateur depuis le provider
    $operator = null;
    if (str_contains($provider, 'mtn')) {
        $operator = 'mtn';
    } elseif (str_contains($provider, 'moov')) {
        $operator = 'moov';
    } elseif (str_contains($provider, 'orange')) {
        $operator = 'orange';
    }

    // Retourner l'instruction appropriée
    if ($operator && isset($instructions[$phoneCountry][$operator])) {
        return $instructions[$phoneCountry][$operator];
    }

    // Instructions de fallback selon le pays
    $fallbackInstructions = [
        '+225' => 'Veuillez valider votre transaction.',
        '+229' => 'Veuillez valider votre transaction.',
        '+226' => 'Veuillez valider votre transaction.',
        '+223' => 'Veuillez valider votre transaction.',
        '+228' => 'Veuillez valider votre transaction.'
    ];

    return $fallbackInstructions[$phoneCountry] ?? 'Veuillez valider votre transaction.';
}

/**
 * MÉTHODE DE DEBUG: Log des données d'instruction
 */
private function logInstructionData($transaction, string $provider, string $phoneCountry, string $instruction): void
{
    Log::info('Données d\'instruction de paiement', [
        'transaction_id' => $transaction->id,
        'transaction_reference' => $transaction->transaction_reference,
        'payment_provider' => $transaction->payment_provider,
        'provider_normalized' => $provider,
        'phone_country_extracted' => $phoneCountry,
        'instruction_generated' => $instruction,
        'metadata_available' => !empty($transaction->metadata)
    ]);
}

    private function payinAfribapay($request)
    {
        $operator = match (trim($request->senderOperator)) {
            'Moov Money' => 'moov',
            'Orange Money' => 'orange',
            'MTN Money' => 'mtn',
            'Wave' => 'wave',
            default => null
        };

        if (!$operator) {
            Log::error('Opérateur non reconnu pour Afribapay', [
                'operator' => $request->senderOperator
            ]);

            return (object) [
                'data' => [],
                'status_code' => 400,
                'message' => "Opérateur non reconnu: {$request->senderOperator}"
            ];
        }

        $result = $this->getAfribapayAccessToken();

        if (!isset($result['token'])) {
            Log::error('Token Afribapay non obtenu');
            return (object) [
                'data' => [],
                'status_code' => 500,
                'message' => "Impossible d'obtenir le token Afribapay"
            ];
        }

        $authorization_token = $result['token'];
        $currency = "XOF";
        $order_id = $request->transactionId;

        // NOUVEAU: Gestion dynamique du montant
        $currentOrganization = TenantHelper::getCurrentOrganization();
        $currentEvent = TenantHelper::getCurrentEvent();
        // Extraire le code pays et l'opérateur depuis la requête
        $fullPhoneNumber = $request->senderPhone;
        $phoneCountry = $this->extractCountryFromPhone($fullPhoneNumber);
        $operator = $this->extractOperatorFromRequest($request);

        // Déterminer le montant selon l'événement
        if ($currentEvent && $currentEvent->event_slug === 'ria-2025') {
            // Pour RIA-2025, utiliser le prix du ticket depuis la requête avec frais par pays/opérateur
            $baseAmount = (int) ($request->transactionAmount1 ?? 20500);
            $amount = $this->calculateAmountWithFees($baseAmount, $phoneCountry, $operator);
            //$amount = 100;
            Log::info('Événement RIA-2025 détecté - Prix dynamique avec frais pays/opérateur', [
                'event_slug' => $currentEvent->event_slug,
                'base_amount' => $baseAmount,
                'phone_country' => $phoneCountry,
                'operator' => $operator,
                'fee_rate' => $this->getFeeRate($phoneCountry, $operator),
                'amount_final' => $amount,
                'fee_applied' => $amount - $baseAmount
            ]);
        } else {
            // Pour les autres événements, utiliser le montant par défaut
            $amount = 100;
            Log::info('Événement standard - Prix fixe', [
                'event_slug' => $currentEvent ? $currentEvent->event_slug : 'inconnu',
                'amount' => $amount,
                'phone_country' => $phoneCountry,
                'operator' => $operator
            ]);
        }

        $notify_url = "https://webhook.toutransfert.com/czotick/index.php"; // URL centralisée pour les webhooks
        $merchant_key = "mk_54056405Ur250603123754";

        // NOUVEAU: Gestion multi-pays du numéro de téléphone
        $fullPhoneNumber = $request->senderPhone;
        $phoneCountry = $this->extractCountryFromPhone($fullPhoneNumber);
        $phoneNumber = $this->cleanPhoneNumber($fullPhoneNumber);

        Log::info('Données téléphone pour Afribapay', [
            'full_phone' => $fullPhoneNumber,
            'country_detected' => $phoneCountry,
            'phone_cleaned' => $phoneNumber,
            'operator' => $operator
        ]);

        // NOUVEAU: Configuration du pays selon le code téléphonique
        $countryConfig = $this->getCountryConfig($phoneCountry);

        $data = [
            "country" => $countryConfig['code'], // Dynamique selon le pays
            "operator" => $operator,
            "phone_number" => $phoneNumber,
            "amount" => $amount,
            "currency" => $currency,
            "order_id" => $order_id,
            "merchant_key" => $merchant_key,
            "notify_url" => $notify_url,
            "reference" => "Inscription événement " . ($currentEvent ? $currentEvent->event_title : 'Événement'),
            "lang" => "fr"
        ];

        // Ajouter des paramètres spécifiques selon l'opérateur
        if ($operator === 'orange') {
            $data["otp_code"] = $request->codeOtp;
            Log::info('Code OTP ajouté pour Orange', [
                'otp_code' => $request->codeOtp,
                'order_id' => $order_id,
                'country' => $countryConfig['code']
            ]);
        }

        if ($operator === 'wave') {
            if ($currentOrganization && $currentEvent) {
                $data["return_url"] = route('event.registration', [
                    'org_slug' => $currentOrganization->org_key,
                    'event_slug' => $currentEvent->event_slug
                ]) . "?payment=success&ref=" . $order_id;
                $data["cancel_url"] = route('event.registration', [
                    'org_slug' => $currentOrganization->org_key,
                    'event_slug' => $currentEvent->event_slug
                ]) . "?payment=cancelled&ref=" . $order_id;
            }
        }

        $json_data = json_encode($data);

        Log::info('Données envoyées à Afribapay', [
            'operator' => $operator,
            'order_id' => $order_id,
            'amount' => $amount,
            'country' => $countryConfig['code'],
            'country_name' => $countryConfig['name'],
            'phone_number' => $phoneNumber,
            'event_slug' => $currentEvent ? $currentEvent->event_slug : 'inconnu',
            'data_size' => strlen($json_data)
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.afribapay.com/v1/pay/payin');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $authorization_token,
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);

            Log::error('Erreur CURL Afribapay', [
                'error' => $error,
                'order_id' => $order_id,
                'country' => $countryConfig['code']
            ]);

            return (object) [
                'data' => ["error" => ["message" => "Erreur CURL : $error"]],
                'status_code' => 500,
                'message' => "Erreur lors de l'appel à Afribapay"
            ];
        }

        curl_close($ch);

        Log::info('Réponse Afribapay reçue', [
            'http_code' => $httpCode,
            'response_size' => strlen($response),
            'order_id' => $order_id,
            'country' => $countryConfig['code']
        ]);

        $decoded = json_decode($response, true);
        $responseData = $decoded['data'] ?? [];

        if ($httpCode === 200 && isset($responseData['status'])) {
            if ($responseData['status'] === 'SUCCESS') {
                Log::info('Transaction Afribapay réussie', [
                    'order_id' => $responseData['order_id'],
                    'status' => $responseData['status'],
                    'amount' => $amount,
                    'country' => $countryConfig['code']
                ]);

                return (object) [
                    'data' => ["reference" => $responseData['order_id']],
                    'status_code' => 200,
                    'message' => "Transaction réussie"
                ];
            } elseif ($responseData['status'] === 'PENDING') {
                Log::info('Transaction Afribapay en attente', [
                    'order_id' => $responseData['order_id'],
                    'status' => $responseData['status'],
                    'operator' => $operator,
                    'country' => $countryConfig['code']
                ]);

                if ($operator == "wave") {
                    return (object) [
                        'data' => [
                            "reference" => $responseData['order_id'],
                            "waveLaunchUrl" => $responseData['provider_link'],
                        ],
                        'status_code' => 200,
                        'message' => "Transaction Wave initiée"
                    ];
                } else {
                    return (object) [
                        'data' => ["reference" => $responseData['order_id']],
                        'status_code' => 202,
                        'message' => "Transaction en attente de confirmation"
                    ];
                }
            }
        }

        Log::warning('Réponse Afribapay inattendue', [
            'http_code' => $httpCode,
            'response_data' => $responseData,
            'order_id' => $order_id,
            'country' => $countryConfig['code']
        ]);

        return (object) [
            'data' => [
                "response" => $responseData,
                "status_code" => $httpCode
            ],
            'status_code' => 500,
            'message' => $decoded['message'] ?? "Réponse inattendue de Afribapay"
        ];
    }


/**
 * Calculer le montant avec les frais selon le pays et l'opérateur
 */
private function calculateAmountWithFees(int $baseAmount, string $phoneCountry, string $operator): int
{
    $feeRate = $this->getFeeRate($phoneCountry, $operator);

    if ($feeRate === 0) {
        return $baseAmount;
    }

    // Calculer le montant avec frais
    $feeAmount = $baseAmount * ($feeRate / 100);
    $totalAmount = $baseAmount + $feeAmount;

    // Arrondir au multiple de 5 le plus proche
    $roundedAmount = $this->roundToNearestMultiple($totalAmount, 5);

    Log::info('Calcul des frais par pays/opérateur', [
        'phone_country' => $phoneCountry,
        'operator' => $operator,
        'base_amount' => $baseAmount,
        'fee_rate' => $feeRate . '%',
        'fee_amount' => round($feeAmount, 2),
        'total_before_rounding' => round($totalAmount, 2),
        'total_after_rounding' => $roundedAmount
    ]);

    return $roundedAmount;
}

/**
 * EXEMPLES DE CALCULS pour RIA-2025 avec différents opérateurs :
 *
 * Base: 60 000 FCFA (Membre RIA-2025)
 *
 * 🇨🇮 Côte d'Ivoire (+225):
 *   - Wave: 60 000 + 1.5% = 60 900 FCFA
 *   - MTN: 60 000 + 2% = 61 200 FCFA
 *   - Orange: 60 000 + 2.5% = 61 500 FCFA
 *   - Moov: 60 000 + 2.5% = 61 500 FCFA
 *
 * 🇧🇯 Bénin (+229): 60 000 + 2.5% = 61 500 FCFA (tous opérateurs)
 * 🇧🇫 Burkina Faso (+226): 60 000 + 3% = 61 800 FCFA (tous opérateurs)
 * 🇲🇱 Mali (+223): 60 000 + 3% = 61 800 FCFA (tous opérateurs)
 * 🇹🇬 Togo (+228): 60 000 + 3.5% = 62 100 FCFA (tous opérateurs)
 *
 * Base: 75 000 FCFA (Non-membre RIA-2025)
 *
 * 🇨🇮 Côte d'Ivoire (+225):
 *   - Wave: 75 000 + 1.5% = 76 125 FCFA
 *   - MTN: 75 000 + 2% = 76 500 FCFA
 *   - Orange: 75 000 + 2.5% = 76 875 FCFA
 *   - Moov: 75 000 + 2.5% = 76 875 FCFA
 *
 * 🇧🇯 Bénin (+229): 75 000 + 2.5% = 76 875 FCFA (tous opérateurs)
 * 🇧🇫 Burkina Faso (+226): 75 000 + 3% = 77 250 FCFA (tous opérateurs)
 * 🇲🇱 Mali (+223): 75 000 + 3% = 77 250 FCFA (tous opérateurs)
 * 🇹🇬 Togo (+228): 75 000 + 3.5% = 77 625 FCFA (tous opérateurs)
 */

/**
 * MÉTHODE DE VALIDATION pour s'assurer que les calculs sont corrects
 */
private function validateFeeCalculation(int $baseAmount, string $phoneCountry, string $operator): array
{
    $feeRate = $this->getFeeRate($phoneCountry, $operator);
    $finalAmount = $this->calculateAmountWithFees($baseAmount, $phoneCountry, $operator);

    $feeAmount = $finalAmount - $baseAmount;
    $actualFeeRate = $baseAmount > 0 ? ($feeAmount / $baseAmount) * 100 : 0;

    return [
        'base_amount' => $baseAmount,
        'phone_country' => $phoneCountry,
        'operator' => $operator,
        'expected_fee_rate' => $feeRate,
        'actual_fee_rate' => round($actualFeeRate, 2),
        'fee_amount' => $feeAmount,
        'final_amount' => $finalAmount,
        'is_multiple_of_5' => ($finalAmount % 5) === 0
    ];
}

/**
 * MÉTHODE POUR TESTER tous les cas de calcul
 */
private function testAllFeeCalculations(): void
{
    $testAmounts = [60000, 75000]; // Montants RIA-2025
    $countries = ['+225', '+229', '+226', '+223', '+228'];
    $operators = ['wave', 'orange', 'mtn', 'moov'];

    Log::info('=== TEST DES CALCULS DE FRAIS PAR PAYS ET OPÉRATEUR ===');

    foreach ($testAmounts as $baseAmount) {
        foreach ($countries as $country) {
            foreach ($operators as $operator) {
                // Vérifier si l'opérateur est disponible dans ce pays
                $feeRate = $this->getFeeRate($country, $operator);
                if ($feeRate > 0) {
                    $validation = $this->validateFeeCalculation($baseAmount, $country, $operator);
                    Log::info('Test calcul frais', $validation);
                }
            }
        }
    }

    Log::info('=== FIN DES TESTS ===');
}

/**
 * MÉTHODE POUR OBTENIR UN RÉSUMÉ DES FRAIS
 */
private function getFeesSummary(): array
{
    return [
        'Côte d\'Ivoire (+225)' => [
            'Wave' => '1.5%',
            'MTN Money' => '2%',
            'Orange Money' => '2.5%',
            'Moov Money' => '2.5%'
        ],
        'Bénin (+229)' => [
            'Tous opérateurs' => '2.5%'
        ],
        'Burkina Faso (+226)' => [
            'Tous opérateurs' => '3%'
        ],
        'Mali (+223)' => [
            'Tous opérateurs' => '3%'
        ],
        'Togo (+228)' => [
            'Tous opérateurs' => '3.5%'
        ]
    ];
}


/**
 * EXEMPLES DE CALCULS pour différents pays :
 *
 * Base: 60 000 FCFA (Membre RIA-2025)
 *
 * 🇨🇮 Côte d'Ivoire (+225): 60 000 FCFA (pas de frais)
 * 🇧🇯 Bénin (+229): 60 000 + 2.5% = 61 500 FCFA
 * 🇧🇫 Burkina Faso (+226): 60 000 + 3% = 61 800 FCFA
 * 🇲🇱 Mali (+223): 60 000 + 3% = 61 800 FCFA
 * 🇹🇬 Togo (+228): 60 000 + 3.5% = 62 100 FCFA
 *
 * Base: 75 000 FCFA (Non-membre RIA-2025)
 *
 * 🇨🇮 Côte d'Ivoire (+225): 75 000 FCFA (pas de frais)
 * 🇧🇯 Bénin (+229): 75 000 + 2.5% = 76 875 → 76 875 FCFA
 * 🇧🇫 Burkina Faso (+226): 75 000 + 3% = 77 250 FCFA
 * 🇲🇱 Mali (+223): 75 000 + 3% = 77 250 FCFA
 * 🇹🇬 Togo (+228): 75 000 + 3.5% = 77 625 → 77 625 FCFA
 */

/**
 * MÉTHODE DE VALIDATION pour s'assurer que les calculs sont corrects
 */
private function validateCountryFeeCalculation(int $baseAmount, string $phoneCountry): array
{
    $feeRate = $this->getCountryFeeRate($phoneCountry);
    $finalAmount = $this->calculateAmountWithCountryFees($baseAmount, $phoneCountry);

    $feeAmount = $finalAmount - $baseAmount;
    $actualFeeRate = $baseAmount > 0 ? ($feeAmount / $baseAmount) * 100 : 0;

    return [
        'base_amount' => $baseAmount,
        'phone_country' => $phoneCountry,
        'expected_fee_rate' => $feeRate,
        'actual_fee_rate' => round($actualFeeRate, 2),
        'fee_amount' => $feeAmount,
        'final_amount' => $finalAmount,
        'is_multiple_of_5' => ($finalAmount % 5) === 0
    ];
}

/**
 * MÉTHODE POUR TESTER tous les cas de calcul
 */
private function testCountryFeeCalculations(): void
{
    $testAmounts = [60000, 75000, 20500]; // Différents montants de base
    $countries = ['+225', '+229', '+226', '+223', '+228'];

    Log::info('=== TEST DES CALCULS DE FRAIS PAR PAYS ===');

    foreach ($testAmounts as $baseAmount) {
        foreach ($countries as $country) {
            $validation = $this->validateCountryFeeCalculation($baseAmount, $country);
            Log::info('Test calcul frais', $validation);
        }
    }

    Log::info('=== FIN DES TESTS ===');
}


    /**
     * Configuration des opérateurs par pays
     */
    private function getPaymentOperatorsByCountry()
    {
        return [
            '+225' => [ // Côte d'Ivoire
                'country_name' => 'Côte d\'Ivoire',
                'flag' => '🇨🇮',
                'operators' => [
                    'wave' => ['name' => 'Wave', 'icon' => '📱', 'type' => 'mobile_money'],
                    'mtn' => ['name' => 'MTN Money', 'icon' => '📱', 'type' => 'mobile_money'],
                    'orange' => ['name' => 'Orange Money', 'icon' => '📱', 'type' => 'mobile_money'],
                    'moov' => ['name' => 'Moov Money', 'icon' => '📱', 'type' => 'mobile_money'],
                ]
            ],
            '+226' => [ // Burkina Faso
                'country_name' => 'Burkina Faso',
                'flag' => '🇧🇫',
                'operators' => [
                    'orange' => ['name' => 'Orange Money', 'icon' => '📱', 'type' => 'mobile_money'],
                    'moov' => ['name' => 'Moov Money', 'icon' => '📱', 'type' => 'mobile_money'],
                    'ligdicash' => ['name' => 'LigdiCash', 'icon' => '📱', 'type' => 'mobile_money'],
                ]
            ],
            '+223' => [ // Mali
                'country_name' => 'Mali',
                'flag' => '🇲🇱',
                'operators' => [
                    'orange' => ['name' => 'Orange Money', 'icon' => '📱', 'type' => 'mobile_money'],
                    'moov' => ['name' => 'Moov Money', 'icon' => '📱', 'type' => 'mobile_money'],
                ]
            ],
            '+227' => [ // Niger
                'country_name' => 'Niger',
                'flag' => '🇳🇪',
                'operators' => [
                    'airtel' => ['name' => 'Airtel Money', 'icon' => '📱', 'type' => 'mobile_money'],
                ]
            ],
            '+229' => [ // Bénin
                'country_name' => 'Bénin',
                'flag' => '🇧🇯',
                'operators' => [
                    'mtn' => ['name' => 'MTN Money', 'icon' => '📱', 'type' => 'mobile_money'],
                    'moov' => ['name' => 'Moov Money', 'icon' => '📱', 'type' => 'mobile_money'],
                    'celtiis' => ['name' => 'Celtiis', 'icon' => '📱', 'type' => 'mobile_money'],
                ]
            ],
            '+228' => [ // Togo
                'country_name' => 'Togo',
                'flag' => '🇹🇬',
                'operators' => [
                    'moov' => ['name' => 'Moov Money', 'icon' => '📱', 'type' => 'mobile_money'],
                    'tmoney' => ['name' => 'T-Money', 'icon' => '📱', 'type' => 'mobile_money'],
                ]
            ]
        ];
    }

    /**
     * Extraire le code pays du formulaire
     */
    private function extractCountryCodeFromFormData($formData)
    {
        // Chercher dans les différents formats possibles
        if (isset($formData['phone_country'])) {
            return $formData['phone_country'];
        }

        if (isset($formData['country_code'])) {
            return $formData['country_code'];
        }

        // Fallback : Côte d'Ivoire par défaut
        return '+225';
    }

    /**
     * Extraire les questions du formulaire
     */
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

    // Modifiez vos méthodes existantes pour inclure les opérateurs

    public function showPaymentValidation(Request $request)
    {
        $currentOrganization = TenantHelper::getCurrentOrganization();
        $currentEvent = TenantHelper::getCurrentEvent();
        $data = $request->all();
        $email = $this->extractEmailFromFormData($data);

        if (!$email || !isset($data['ticket_type_id'])) {
            return redirect()->route('event.registration', [
                'org_slug' => $currentOrganization->org_key,
                'event_slug' => $currentEvent->event_slug
            ])->withErrors(['error' => 'Données manquantes pour le paiement']);
        }

        return TenantHelper::withTenantConnection(function() use ($data, $currentOrganization, $currentEvent) {
            $ticketType = TicketType::on('tenant')->findOrFail($data['ticket_type_id']);
            $countryCode = $this->extractCountryCodeFromFormData($data);
            $allOperators = $this->getPaymentOperatorsByCountry();
            $countryOperators = $allOperators[$countryCode] ?? $allOperators['+225']; // Fallback CI

            $paymentData = array_merge($data, [
                'ticket_price' => $ticketType->price,
                'ticket_name' => $ticketType->ticket_name,
                'currency' => $ticketType->currency ?? 'FCFA',
                'fullname' => $this->extractNameFromFormData($data),
                'full_name' => $this->extractNameFromFormData($data),
                'email' => $this->extractEmailFromFormData($data),
                'phone' => $this->extractPhoneFromFormData($data),
                'organization' => $this->extractOrganizationFromFormData($data),
                //'organization' => $currentOrganization,
                'position' => $this->extractPositionFromFormData($data),
                'country_code' => $countryCode
            ]);

            return view('events.payment-validation', compact(
                'currentOrganization',
                'currentEvent',
                'paymentData',
                'countryOperators'
            ));
        });
    }

    public function paymentValidation(Request $request)
    {
        $currentOrganization = TenantHelper::getCurrentOrganization();
        $currentEvent = TenantHelper::getCurrentEvent();

        if (!$currentOrganization || !$currentEvent) {
            abort(500, 'Contexte d\'organisation ou d\'événement manquant');
        }

        $formData = $request->all();
        $ticketTypeId = $formData['ticket_type_id'] ?? null;
        $email = $this->extractEmailFromFormData($formData);
        $phone = $this->extractPhoneFromFormData($formData);

        if (!$ticketTypeId || !$email) {
            return back()->withErrors(['error' => 'Données manquantes pour traiter l\'inscription']);
        }

        return TenantHelper::withTenantConnection(function() use (
            $formData, $ticketTypeId, $email, $phone, $currentOrganization, $currentEvent
        ) {
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

            // Extraire le code pays
            $countryCode = $this->extractCountryCodeFromFormData($formData);

            // Obtenir les opérateurs pour ce pays
            $allOperators = $this->getPaymentOperatorsByCountry();
            $countryOperators = $allOperators[$countryCode] ?? $allOperators['+225'];

            $paymentData = array_merge($formData, [
                'ticket_name' => $ticketType->ticket_name,
                'ticket_price' => $ticketType->price,
                'currency' => $ticketType->currency ?? 'FCFA',
                'fullname' => $this->extractNameFromFormData($formData),
                'full_name' => $this->extractNameFromFormData($formData),
                'email' => $email,
                'phone' => $phone,
                'organization' => $this->extractOrganizationFromFormData($formData),
                'position' => $this->extractPositionFromFormData($formData),
                'country_code' => $countryCode
            ]);

            return view('public.validation_form', compact(
                'paymentData',
                'currentOrganization',
                'currentEvent',
                'countryOperators'
            ));
        });
    }

    public function sendMessagesToParticipant(){
  
        $message = "Chers Bénévoles,\n\nSi vous souhaitez *ENCORE* faire partie de l'équipe d'appui pour les Représentants et Superviseurs de bureaux de vote pour l'équipe du Candidat AHOUA DON MELLO, veuillez renseignez ce formulaire soigneusement SVP. Il ne vous prendra que 2 minutes.\n\nEnsemble pour la victoire.\n\nLe Coordonnateur National & Diaspora des Bénévoles pour ADM 2025,
                    *Franck Stéphane DEDI*\n\nLien : https://forms.gle/LfJDPutxtcsQzknx5";

        $csvFile = public_path('registrations.csv');
        
        Log::info('Chemin du fichier CSV: ' . $csvFile);
        
        // Vérifier si le fichier existe
        if (!file_exists($csvFile)) {
            Log::error('❌ Fichier CSV non trouvé');
            return ['error' => 'Fichier CSV non trouvé'];
        }
        
        Log::info('✅ Fichier CSV trouvé');
        
        // Lire le fichier CSV
        $registrations = [];
        if (($handle = fopen($csvFile, "r")) !== FALSE) {
            $headers = fgetcsv($handle, 1000, ","); // Lire les en-têtes
            Log::info('En-têtes CSV: ', $headers);
            
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // Selon votre structure CSV :
                // 0=id, 1=event_id, 2=ticket_type_id, 3=registration_number, 4=fullname, 5=phone, 6=phone_country, 7=formatted_phone, 8=email, etc.
                $registrations[] = [
                    'fullname' => $data[4] ?? '', // fullname
                    'phone' => $data[5] ?? ''     // phone
                ];
            }
            fclose($handle);
        }
        
        // Filtrer les enregistrements avec un numéro de téléphone
        $registrations = array_filter($registrations, function($reg) {
            return !empty($reg['phone']);
        });
        
        Log::info('Nombre de participants trouvés: ' . count($registrations));
    
        $url = "https://chatwave.10nastie-groupe.com/api/clients/Czotick/messages";
        $headers = [
            "accept: application/json",
            "content-type: application/json"
        ];
    
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        
        // Envoyer le message à chaque participant
        foreach ($registrations as $registration) {
            // Extraire les 8 derniers chiffres du numéro
            $lastEightDigits = substr($registration['phone'], -8);
            // Formater avec le préfixe 225
            $phoneNumber = "225" . $lastEightDigits . "@c.us";
            
            Log::info('Envoi à: ' . $registration['fullname'] . ' (' . $phoneNumber . ')');
            
            $whatsappData = [
                "phoneNumber" => $phoneNumber,
                "message" => $message,
            ];
        
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($whatsappData));
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
            if (curl_errno($ch)) {
                $errorMessage = 'Erreur cURL WhatsApp pour ' . $registration['fullname'] . ' (' . $phoneNumber . ') : ' . curl_error($ch);
                Log::error('❌ Erreur: ' . $errorMessage);
                $errors[] = $errorMessage;
                $errorCount++;
            } else {
                Log::info('✅ Message envoyé avec succès à ' . $registration['fullname'] . ' (HTTP: ' . $httpCode . ')');
                $successCount++;
            }
            curl_close($ch);
            
            // Petite pause entre les envois pour éviter de surcharger l'API
            usleep(500000); // 0.5 seconde
        }
    
        Log::info('📊 Résumé: Total=' . count($registrations) . ', Succès=' . $successCount . ', Erreurs=' . $errorCount);
    
        return [
            'total' => count($registrations),
            'success' => $successCount,
            'errors' => $errorCount,
            'error_details' => $errors
        ];
    }     
    
    // Formater avec le préfixe 225

    /* public function sendMessagesToParticipant(){
        // Ajouter cette ligne pour vérifier le chemin
        $sqlFile = public_path('registrations.sql');
        Log::info('Chemin du fichier SQL: ' . $sqlFile);
        
        // Vérifier si le fichier existe
        if (file_exists($sqlFile)) {
            Log::info('✅ Fichier SQL trouvé');
        } else {
            Log::info('❌ Fichier SQL non trouvé');
        }
        
        // Appeler votre fonction
        $result = $this->sendMessagesToParticipant();
        
        Log::info('Résultat: ', $result);
        return $result;
    } */

    /* public function sendMessagesToParticipant(){

        // Message avec retours à la ligne
        $message = "Chers Bénévoles,\n\nSi vous souhaitez *ENCORE* faire partie de l'équipe d'appui pour les Représentants et Superviseurs de bureaux de vote pour l'équipe du Candidat AHOUA DON MELLO, veuillez renseignez ce formulaire soigneusement SVP. Il ne vous prendra que 2 minutes.\n\nEnsemble pour la victoire.\n\nLe Coordonnateur National & Diaspora des Bénévoles pour ADM 2025,
                    *Franck Stéphane DEDI*\n\nLien : https://forms.gle/LfJDPutxtcsQzknx5";
    
        $whatsappData = [
            "phoneNumber" => "22557088382@c.us",
            "message" => $message,
        ];
    
        $url = "https://chatwave.10nastie-groupe.com/api/clients/Czotick/messages";
        $headers = [
            "accept: application/json",
            "content-type: application/json"
        ];
    
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($whatsappData));
    
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
        if (curl_errno($ch)) {
            $errorMessage = 'Erreur cURL WhatsApp : ' . curl_error($ch);
            Log::error('❌ Erreur cURL WhatsApp', ['error' => $errorMessage]);
            curl_close($ch);
            return false;
        }
    
        curl_close($ch);
    } */

}
