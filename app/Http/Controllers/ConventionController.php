<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Convention;
use App\Models\EventSchedule;
use Carbon\Carbon;

class ConventionController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Validation des données
            $validated = $request->validate([
                'fullname' => 'required|string|max:255',
                'phone' => 'required|string|max:10|unique:conventions,phone',
                'email' => 'required|email|max:255',
                'organization' => 'required|string|max:255',
                'other_organization' => 'nullable|string|max:255',
                'quality' => 'required|string|max:255',
                'ticket_type' => 'required|in:15200,7600', 
            ], [
                'fullname.required' => 'Le nom et les prénoms sont obligatoires.',
                'fullname.max' => 'Le nom et les prénoms ne doivent pas dépasser 255 caractères.',

                'phone.required' => 'Le numéro de téléphone est obligatoire.',
                'phone.max' => 'Le numéro de téléphone ne doit pas dépasser 10 caractères.',
                'phone.unique' => 'Ce numéro de téléphone est déjà enregistré.',

                'email.required' => 'L’adresse email est obligatoire.',
                'email.email' => 'L’adresse email n’est pas valide.',
                'email.max' => 'L’adresse email ne doit pas dépasser 255 caractères.',

                'organization.required' => 'L’OLM est obligatoire.',
                'organization.max' => 'Le nom de l’OLM ne doit pas dépasser 255 caractères.',

                'other_organization.max' => 'Le champ "autre organisation" ne doit pas dépasser 255 caractères.',

                'quality.required' => 'La fonction est obligatoire.',
                'quality.max' => 'La fonction ne doit pas dépasser 255 caractères.',

                'ticket_type.required' => 'Le type de ticket est obligatoire.',
                'ticket_type.in' => 'Le type de ticket sélectionné est invalide.',
            ]);

            return response()->json([
                'message' => 'Inscription en cours',
                'validated' => $validated
                  
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'The given data was invalid.', 'errors' => $e->errors()], 422);
        }
    }


    /* public function verify(Request $request)
    {
        // Récupérer les données depuis les paramètres de la requête GET
        $uniqueId = $request->query('data');
        $eventType = $request->query('event');

        // Vérifier si le scan est effectué pendant l'événement actif
        $currentEvent = $this->getCurrentEvent();
        if (!$currentEvent || $currentEvent !== $eventType) {
            return view('verifier.verification-result', [
                'alert' => [
                    'status' => 'error',
                    'message' => 'Ce QR code ne peut pas être scanné maintenant. ' . $this->getEventName($eventType) . ' n\'est pas en cours.'
                ]
            ]);
        }
        // Vérifier si l'utilisateur est authentifié comme vérificateur
        if (!session()->has('verifier_authenticated')) {
            session(['pending_verification' => [
                'unique_id' => $uniqueId,
                'event' => $eventType
            ]]);
            
            return redirect()->route('verifier.auth');
        }

        // Chercher l'inscription correspondante
        $inscription = Convention::where('unique_id', $uniqueId)->first();

        if (!$inscription) {
            return view('verifier.verification-result', [
                'alert' => [
                    'status' => 'error',
                    'message' => 'Inscription non trouvée.'
                ]
            ]);
        }

        // Vérifier si le ticket a déjà été utilisé
        if ($eventType == 'opening' && $inscription->used_opening == 1) {
            return view('verifier.verification-result', [
                'alert' => [
                    'status' => 'warning',
                    'message' => 'Ce ticket a déjà été utilisé pour la cérémonie d\'ouverture.'
                ],
                'inscription' => $inscription
            ]);
        } elseif ($eventType == 'ag' && $inscription->used_ag == 1) {
            return view('verifier.verification-result', [
                'alert' => [
                    'status' => 'warning',
                    'message' => 'Ce ticket a déjà été utilisé pour l\'assemblée générale.'
                ],
                'inscription' => $inscription
            ]);
        } elseif ($eventType == 'gala' && $inscription->used_gala == 1) {
            return view('verifier.verification-result', [
                'alert' => [
                    'status' => 'warning',
                    'message' => 'Ce ticket a déjà été utilisé pour le gala.'
                ],
                'inscription' => $inscription
            ]);
        }

        // Vérifier le statut de paiement
        if ($inscription->paymentStatus == 1) {
            // Marquer le ticket comme utilisé
            if ($eventType == 'opening') {
                $inscription->used_opening = 1;
            } elseif ($eventType == 'ag') {
                $inscription->used_ag = 1;
            } elseif ($eventType == 'gala') {
                $inscription->used_gala = 1;
            }
            
            $inscription->save();
            
            return view('verifier.verification-result', [
                'alert' => [
                    'status' => 'success',
                    'message' => 'Le ticket est valide pour ' . $this->getEventName($eventType) . '.',
                    'user' => $inscription->fullname
                ],
                'inscription' => $inscription
            ]);
        } else {
            return view('verifier.verification-result', [
                'alert' => [
                    'status' => 'error',
                    'message' => 'Le ticket n\'est pas payé.'
                ],
                'inscription' => $inscription
            ]);
        }
    } */

    public function verify(Request $request)
    {
        // Récupérer les données depuis les paramètres de la requête GET
        $uniqueId = $request->query('data');
        $eventType = $request->query('event');
        $status = 'error'; // Statut par défaut

        // Vérifier si le scan est effectué pendant l'événement actif
        $currentEvent = $this->getCurrentEvent();
        if (!$currentEvent || $currentEvent !== $eventType) {
            return view('verifier.verification-result', [
                'alert' => [
                    'status' => 'error',
                    'message' => 'L\'entrée ne peut être validée maintenant. ' . $this->getEventName($eventType) . ' n\'est pas en cours.'
                ]
            ]);
        }
        
        // Vérifier si l'utilisateur est authentifié comme vérificateur
        if (!session()->has('verifier_authenticated')) {
            session(['pending_verification' => [
                'unique_id' => $uniqueId,
                'event' => $eventType
            ]]);
            
            return redirect()->route('verifier.auth');
        }

        // Chercher l'inscription correspondante
        $inscription = Convention::where('unique_id', $uniqueId)->first();

        if (!$inscription) {
            // Journaliser la tentative échouée
            $this->logVerification(null, $uniqueId, $eventType, 'inscription_not_found', $request->ip());
            
            return view('verifier.verification-result', [
                'alert' => [
                    'status' => 'error',
                    'message' => 'Inscription non trouvée.'
                ]
            ]);
        }

        // Vérifier si le ticket a déjà été utilisé
        if ($eventType == 'opening' && $inscription->used_opening == 1) {
            // Journaliser la tentative de réutilisation
            $this->logVerification($inscription->id, $uniqueId, $eventType, 'already_used', $request->ip());
            
            return view('verifier.verification-result', [
                'alert' => [
                    'status' => 'warning',
                    'message' => 'Ce ticket a déjà été utilisé pour la cérémonie d\'ouverture.'
                ],
                'inscription' => $inscription
            ]);
        } elseif ($eventType == 'ag' && $inscription->used_ag == 1) {
            // Journaliser la tentative de réutilisation
            $this->logVerification($inscription->id, $uniqueId, $eventType, 'already_used', $request->ip());
            
            return view('verifier.verification-result', [
                'alert' => [
                    'status' => 'warning',
                    'message' => 'Ce ticket a déjà été utilisé pour l\'assemblée générale.'
                ],
                'inscription' => $inscription
            ]);
        } elseif ($eventType == 'gala' && $inscription->used_gala == 1) {
            // Journaliser la tentative de réutilisation
            $this->logVerification($inscription->id, $uniqueId, $eventType, 'already_used', $request->ip());
            
            return view('verifier.verification-result', [
                'alert' => [
                    'status' => 'warning',
                    'message' => 'Ce ticket a déjà été utilisé pour le gala.'
                ],
                'inscription' => $inscription
            ]);
        }

        // Vérifier le statut de paiement
        if ($inscription->paymentStatus == 1) {
            // Marquer le ticket comme utilisé
            if ($eventType == 'opening') {
                $inscription->used_opening = 1;
            } elseif ($eventType == 'ag') {
                $inscription->used_ag = 1;
            } elseif ($eventType == 'gala') {
                $inscription->used_gala = 1;
            }
            
            $inscription->save();
            
            // Journaliser la vérification réussie
            $this->logVerification($inscription->id, $uniqueId, $eventType, 'success', $request->ip());
            
            return view('verifier.verification-result', [
                'alert' => [
                    'status' => 'success',
                    'message' => 'Le ticket est valide pour ' . $this->getEventName($eventType) . '.',
                    'user' => $inscription->fullname
                ],
                'inscription' => $inscription
            ]);
        } else {
            // Journaliser le ticket non payé
            $this->logVerification($inscription->id, $uniqueId, $eventType, 'not_paid', $request->ip());
            
            return view('verifier.verification-result', [
                'alert' => [
                    'status' => 'error',
                    'message' => 'Le ticket n\'est pas payé.'
                ],
                'inscription' => $inscription
            ]);
        }
    }

    public function verifyMeal(Request $request)
    {
        // Récupérer les données depuis les paramètres de la requête GET
        $uniqueId = $request->query('data');
        $eventType = $request->query('event');
        $status = 'error'; // Statut par défaut

         // Vérifier si le scan est effectué pendant l'événement actif
       /*  $currentEvent = $this->getCurrentEvent();
        if (!$currentEvent || $currentEvent !== $eventType) {
            return view('verifier.verification-result', [
                'alert' => [
                    'status' => 'error',
                    'message' => 'L\'entrée ne peut être validée maintenant. ' . $this->getEventName($eventType) . ' n\'est pas en cours.'
                ]
            ]);
        } */
        // Vérifier si l'utilisateur est authentifié comme vérificateur
        if (!session()->has('verifier_authenticated')) {
            session(['pending_verification' => [
                'unique_id' => $uniqueId,
                'event' => $eventType
            ]]);
            
            return redirect()->route('verifier.auth');
        }

        // Chercher l'inscription correspondante
        $inscription = Convention::where('unique_id', $uniqueId)->first();

        if (!$inscription) {
            // Journaliser la tentative échouée
            $this->logVerification(null, $uniqueId, $eventType, 'inscription_not_found', $request->ip());
            
            return view('verifier.verification-result', [
                'alert' => [
                    'status' => 'error',
                    'message' => 'Inscription non trouvée.'
                ]
            ]);
        }

        // Vérifier si le ticket a déjà été utilisé
        if ($eventType == 'restau' && $inscription->used_restau == 1) {
            // Journaliser la tentative de réutilisation
            $this->logVerification($inscription->id, $uniqueId, $eventType, 'already_used', $request->ip());
            
            return view('verifier.verification-result', [
                'alert' => [
                    'status' => 'warning',
                    'message' => 'Ce ticket a déjà été utilisé pour la restauration.'
                ],
                'inscription' => $inscription
            ]);
        } 

        // Vérifier le statut de paiement
        if ($inscription->paymentStatus == 1) {
            // Marquer le ticket comme utilisé
            if ($eventType == 'restau') {
                $inscription->used_restau = 1;
            }
            
            $inscription->save();
            
            // Journaliser la vérification réussie
            $this->logVerification($inscription->id, $uniqueId, $eventType, 'success', $request->ip());
            
            return view('verifier.verification-result', [
                'alert' => [
                    'status' => 'success',
                    'message' => 'Le ticket est valide pour ' . $this->getEventName($eventType) . '.',
                    'user' => $inscription->fullname
                ],
                'inscription' => $inscription
            ]);
        } else {
            // Journaliser le ticket non payé
            $this->logVerification($inscription->id, $uniqueId, $eventType, 'not_paid', $request->ip());
            
            return view('verifier.verification-result', [
                'alert' => [
                    'status' => 'error',
                    'message' => 'Le ticket n\'est pas payé.'
                ],
                'inscription' => $inscription
            ]);
        }
    }

    /**
     * Enregistrer une entrée dans le journal de vérification
     *
     * @param int|null $conventionId
     * @param string $uniqueId
     * @param string $eventType
     * @param string $status
     * @param string|null $ipAddress
     * @return void
     */
    private function logVerification($conventionId, $uniqueId, $eventType, $status, $ipAddress = null)
    {
        // Si l'inscription n'a pas été trouvée mais que nous avons un uniqueId
        if (!$conventionId && $uniqueId) {
            // Essayer de trouver l'ID de l'inscription à partir de l'uniqueId
            $inscription = Convention::where('unique_id', $uniqueId)->first();
            $conventionId = $inscription ? $inscription->id : null;
        }
        
        // Créer une nouvelle entrée de journal
        \DB::table('verification_logs')->insert([
            'verifier_id' => session('verifier_id'),
            'convention_id' => $conventionId ?? 0, // 0 si pas trouvé
            'event_type' => $eventType,
            'verification_time' => now(),
            'status' => $status,
            'ip_address' => $ipAddress,
        ]);
    }

    // Fonction helper pour obtenir le nom complet de l'événement
    private function getEventName($eventType)
    {
        switch ($eventType) {
            case 'opening':
                return 'La cérémonie d\'ouverture';
            case 'ag':
                return 'L\'Assemblée Générale';
            case 'gala':
                return 'Le dîner gala';
            case 'restau':
                return 'La restauration';
            default:
                return 'l\'événement';
        }
    }

    private function getCurrentEvent()
    {
        $now = now();
        
        // Récupérer uniquement les événements actifs
        $event = EventSchedule::where('active', true)
                                ->where('start_time', '<=', $now)
                                ->where('end_time', '>=', $now)
                                ->first();
        
        return $event ? $event->event_type : null;
    }

}
