<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class RegistrationController extends Controller
{
    /**
     * Afficher une inscription spécifique (pour le modal ticket)
     */
    public function show(Request $request, $org_slug, $registrationId)
    {
        $user = session('organization_user');
        
        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Non autorisé'], 401);
            }
            return redirect()->route('org.login', ['org_slug' => request()->route('org_slug')]);
        }

        try {
            $registration = DB::connection('tenant')
                ->table('registrations as r')
                ->leftJoin('events as e', 'r.event_id', '=', 'e.id')
                ->where('r.id', $registrationId)
                ->select(
                    'r.*',
                    'e.event_slug',
                )
                ->first();

            if (!$registration) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Inscription non trouvée'], 404);
                }
                return abort(404, 'Inscription non trouvée');
            }

            $ticketImageUrl = "ticket_".$registration->registration_number.".png";
            
            return response()->json([
                'success' => true,
                'ticket' => [
                    'id' => $registration->id,
                    'fullname' => $registration->fullname,
                    'org_slug' => $org_slug,
                    'event_slug' => $registration->event_slug,
                    'image_url' => "/public/images/".$org_slug."/".$registration->event_slug."/tickets/".$ticketImageUrl,
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error showing registration', [
                'registration_id' => $registrationId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Erreur lors du chargement de l\'inscription'
                ], 500);
            }

            return abort(500, 'Erreur lors du chargement de l\'inscription');
        }
    }

    /**
     * Renvoyer un ticket par email
     */
    public function resendTicket(Request $request, $org_slug, $registrationId)
    {
        $user = session('organization_user');
        
        if (!$user) {
            return response()->json(['error' => 'Non autorisé'], 401);
        }

        try {
            $registration = DB::connection('tenant')
                ->table('registrations as r')
                ->leftJoin('events as e', 'r.event_id', '=', 'e.id')
                ->where('r.id', $registrationId)
                ->select(
                    'r.*',
                    'e.event_slug',
                    'e.event_title' // ⚠️ AJOUTER CETTE LIGNE
                )
                ->first();

            if (!$registration) {
                return response()->json(['error' => 'Inscription non trouvée'], 404);
            }

            $ticketImageUrl = "public/images/".$org_slug."/".$registration->event_slug."/tickets/ticket_".$registration->registration_number.".png";

            // ⚠️ PASSER $org_slug EN PARAMÈTRE
            //$emailSent = $this->sendMail($registration, $ticketImageUrl, $org_slug);
            $emailWhatsapp = $this->sendWhatsAppTicketWithOrganizedStructure($registration, $ticketImageUrl, $org_slug);
            
            return response()->json([
                'success' => $emailWhatsapp,
                'message' => 'Ticket renvoyé avec succès !'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error resending ticket', [
                'registration_id' => $registrationId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erreur lors du renvoi du ticket'
            ], 500);
        }
    }

    /**
     * ⚠️ AJOUTER LE PARAMÈTRE $org_slug
     */
    private function sendMail($registration, $ticketImageUrl, $org_slug = null)
    {
        try {
            // Construire le chemin complet du fichier ticket
            $ticketPath = storage_path("app/{$ticketImageUrl}");
            
            // Vérifier que le fichier existe
            if (!file_exists($ticketPath)) {
                Log::error('Fichier ticket non trouvé pour email', [
                    'path' => $ticketPath,
                    'registration_id' => $registration->id
                ]);
                return false;
            }

            // ⚠️ PASSER $org_slug À getCurrentOrganization
            $organization = $this->getCurrentOrganization($org_slug);
            $event = $this->getCurrentEvent($registration->event_id);

            if (!$organization || !$event) {
                Log::error('Contexte organisation/événement manquant', [
                    'registration_id' => $registration->id,
                    'event_id' => $registration->event_id,
                    'org_slug' => $org_slug
                ]);
                return false;
            }

            Log::info('Début envoi email ticket', [
                'to' => $registration->email,
                'registration_id' => $registration->id,
                'ticket_path' => $ticketPath,
                'organization' => $organization->org_key ?? 'unknown',
                'event' => $event->event_slug ?? 'unknown'
            ]);

            // Envoyer l'email avec le ticket en pièce jointe
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
                $contactEmail = $organization->contact_email ?? null;

                // Validation de l'email principal
                if (empty($email) || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                    throw new \Exception("Adresse email invalide: $email");
                }

                // Validation de l'email de contact (optionnel)
                if (!empty($contactEmail) && filter_var($contactEmail, FILTER_VALIDATE_EMAIL) === false) {
                    Log::warning('Email de contact invalide pour l\'organisation', [
                        'organization' => $organization->org_key ?? 'unknown',
                        'contact_email' => $contactEmail
                    ]);
                    $contactEmail = null; // Ignorer l'email invalide
                }

                // Déterminer le titre de l'événement
                $eventTitle = $event->event_title ?? $event->title ?? 'Événement';
                
                // Configuration du message
                $message->to($email, $registration->fullname)
                        ->subject("🎫 Votre ticket - {$eventTitle}")
                        ->attach($ticketPath, [
                            'as' => "ticket_{$registration->registration_number}.png",
                            'mime' => 'image/png'
                        ]);

                // Ajouter l'email de contact en CC si valide
                if (!empty($contactEmail)) {
                    $message->cc($contactEmail);
                    Log::info('Email de contact ajouté en CC', [
                        'contact_email' => $contactEmail,
                        'organization' => $organization->org_key ?? 'unknown'
                    ]);
                }

                // Ajouter un email de support si configuré
                $supportEmail = config('mail.support_email');
                if (!empty($supportEmail) && filter_var($supportEmail, FILTER_VALIDATE_EMAIL)) {
                    $message->bcc($supportEmail);
                }
            });

            Log::info('Email de ticket envoyé avec succès', [
                'registration_id' => $registration->id,
                'email' => $registration->email,
                'cc_email' => $organization->contact_email ?? 'aucun',
                'organization' => $organization->org_key ?? 'unknown',
                'event' => $event->event_slug ?? 'unknown'
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Erreur envoi email ticket', [
                'registration_id' => $registration->id ?? 'unknown',
                'email' => $registration->email ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return false;
        }
    }

    /**
     * Récupérer l'organisation actuelle
     */
    private function getCurrentOrganization($org_slug = null)
    {
        try {
            // Si org_slug est fourni, l'utiliser directement
            if ($org_slug) {
                $organization = DB::table('organizations')
                    ->where('org_key', $org_slug)
                    ->first();
                if ($organization) {
                    return $organization;
                }
            }

            // Méthode 1: Depuis la session
            $orgUser = session('organization_user');
            if ($orgUser && isset($orgUser->organization_id)) {
                $organization = DB::table('organizations')
                    ->where('id', $orgUser->organization_id)
                    ->first();
                if ($organization) {
                    return $organization;
                }
            }

            // Méthode 2: Depuis la route actuelle
            $routeOrgSlug = request()->route('org_slug');
            if ($routeOrgSlug) {
                $organization = DB::table('organizations')
                    ->where('org_key', $routeOrgSlug)
                    ->first();
                if ($organization) {
                    return $organization;
                }
            }

            // Méthode 3: Depuis le contexte global si disponible
            if (app()->bound('current.organization')) {
                return app('current.organization');
            }

            Log::error('Impossible de récupérer l\'organisation actuelle', [
                'org_slug_param' => $org_slug,
                'route_org_slug' => $routeOrgSlug ?? 'null',
                'session_user' => $orgUser ? 'exists' : 'null'
            ]);
            return null;

        } catch (\Exception $e) {
            Log::error('Erreur récupération organisation', [
                'org_slug' => $org_slug,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Récupérer l'événement actuel
     */
    private function getCurrentEvent($eventId)
    {
        try {
            if (!$eventId) {
                Log::error('ID événement manquant');
                return null;
            }

            $event = DB::connection('tenant')
                ->table('events')
                ->where('id', $eventId)
                ->first();

            if (!$event) {
                Log::error('Événement non trouvé', ['event_id' => $eventId]);
                return null;
            }

            Log::info('Événement récupéré avec succès', [
                'event_id' => $eventId,
                'event_title' => $event->event_title ?? $event->title ?? 'Sans titre'
            ]);

            return $event;

        } catch (\Exception $e) {
            Log::error('Erreur récupération événement', [
                'event_id' => $eventId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    private function sendWhatsAppTicketWithOrganizedStructure($registration, $ticketPath, $org_slug = null)
    {
        $organization = $this->getCurrentOrganization($org_slug);
        $event = $this->getCurrentEvent($registration->event_id);

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
                "mediaUrl" => $publicUrl,
                "phoneNumber" => $chatId,
                "caption" => "Ticket pour l'événement {$event->event_title} ({$event->event_slug})"
            ];

            Log::info('📤 Envoi WhatsApp', [
                'chatId' => $chatId,
                'mediaUrl' => $publicUrl,
                'registration' => $registration->registration_number
            ]);

            $url = "https://chatwave.10nastie-groupe.com/api/clients/Czotick/media";
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
                'chatId' => $chatId
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

   
}