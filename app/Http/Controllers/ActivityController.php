<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Program;
use App\Models\Transaction;
use App\Models\Convention;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Barryvdh\DomPDF\Facade\Pdf;
use Swift_RfcComplianceException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $activities = Activity::orderBy('created_at', 'desc')->get();

        return view('backend.activities.index', [
            'activities' => $activities,
        ]);
    }

    public function convention()
    {
        $conventions = Convention::orderBy('created_at', 'desc')->get();

        return view('backend.conventions.index', [
            'conventions' => $conventions,
        ]);
    }

    /**
     * Handle the payment process and generate a ticket with QR code.
     *
     * @param Convention $convention
     * @return \Illuminate\Http\JsonResponse
     */

    public function waveProcess(Request $request): JsonResponse
    {
        Log::info('Début de waveProcess', ['data_reçue' => $request->all()]);

        $data = $request->validate([
            'email' => 'required|email',
            'fullname' => 'required|string|max:255',
            'organization' => 'nullable|string|max:255',
            'other_organization' => 'nullable|string|max:255',
            'quality' => 'nullable|string|max:255',
            'phone' => 'required|string|max:10',
            'ticket_type' => 'required|integer',
        ]);

        $reference = "czotick-jci-" . uniqid();

        $transaction = Transaction::create([
            'email' => $data['email'],
            'fullname' => $data['fullname'],
            'phone' => $data['phone'],
            'organization' => $data['organization'],
            'other_organization' => $data['other_organization'],
            'quality' => $data['quality'],
            'ticket_type' => intval($data['ticket_type']),
            'reference_czotic' => $reference,
            'status' => 0,
        ]);

        $curl = curl_init();

        $postData = json_encode([
            //"amount" => "100", // forcer en string
            "amount" => (string) $data['ticket_type'], 
            "client_reference" => (string) $reference, // forcer en string
            "currency" => "XOF",
            "aggregated_merchant_id" => "am-1x1ck90dr20d0",
            "error_url" => "https://czotick.com/jci-ci-conseil-national-2025/?payment=error",
            "success_url" => "https://czotick.com/jci-ci-conseil-national-2025/?payment=success"
        ]);

        Log::info('postData enregistrée', ['postData' => $postData]);

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.wave.com/v1/checkout/sessions',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer wave_ci_prod_Kn4kzLAmk8txmfPQpu5jIH5mFlDIV2IydQrtlx1bh4HGP_XMmCyeCn2n_IIH549FLLb3b8LmZ3_WyeJYMbMTXymEy2v5csGHFw',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl); 
        curl_close($curl);

        $result = json_decode($response, true);

        if (isset($result['id']) && isset($result['wave_launch_url'])) {
            $transaction->update([
                'reference_wave' => $result['id'],
            ]);



            Log::info('Session Wave créée avec succès', ['wave_id' => $result['id']]);

            return response()->json([
                'status' => 'success',
                'message' => 'Lien de paiement généré.',
                'url' => $result['wave_launch_url'],
                'reference_wave' => $result['id'],
            ]);
        }

        Log::error('Erreur lors de la création de session Wave', ['result' => $result]);

        return response()->json([
            'status' => 'error',
            'message' => 'Impossible de créer la session de paiement.',
            'response' => $result,
        ], 500);
    }

    public function checkStatus(Request $request)
    {

        $request->validate([
            'reference_wave' => 'required|string',
        ]);

        $transaction = Transaction::where('reference_wave', $request->reference_wave)->first();

        if (!$transaction) {
            Log::warning('Transaction non trouvée', ['reference_wave' => $request->reference_wave]);
            return response()->json(['status' => 'not_found']);
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.wave.com/v1/checkout/sessions/" . $transaction->reference_wave,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer wave_ci_prod_Kn4kzLAmk8txmfPQpu5jIH5mFlDIV2IydQrtlx1bh4HGP_XMmCyeCn2n_IIH549FLLb3b8LmZ3_WyeJYMbMTXymEy2v5csGHFw',
                'Content-Type: application/json'
            ),
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        Log::info('Réponse statut Wave', ['response' => $response]);

        $result = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('JSON invalide lors du check status', ['response_raw' => $response]);
            return response()->json([
                'status' => 'error',
                'message' => 'Réponse JSON invalide.',
            ]);
        }

        if (!empty($result['payment_status'])) {
            Log::info('Statut de paiement récupéré', ['status' => $result['payment_status']]);
            return response()->json([
                'status' => $result['payment_status'],
                'message' => $result['payment_status'] === 'succeeded'
                    ? 'Paiement confirmé avec succès.'
                    : 'Paiement non confirmé.',
            ]);
        }

        Log::error('Statut de paiement introuvable', ['response_decoded' => $result]);

        return response()->json([
            'status' => 'error',
            'message' => 'Statut de paiement introuvable.',
        ]);
    }

    public function payment(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'fullname' => 'required|string|max:255',
            'organization' => 'nullable|string|max:255',
            'other_organization' => 'nullable|string|max:255',
            'quality' => 'nullable|string|max:255',
            'phone' => 'required|string|max:10',
            'ticket_type' => 'required|integer',
        ]);

        if (intval($data['ticket_type']) == 15200) {
            $data['amount'] = 15000;
        } elseif (intval($data['ticket_type']) == 7600) {
            $data['amount'] = 7500;
        }

        $convention = Convention::create([
            'email' => $data['email'],
            'fullname' => $data['fullname'],
            'organization' => $data['organization'],
            'other_organization' => $data['other_organization'],
            'quality' => $data['quality'],
            'phone' => $data['phone'],
            'ticket_type' => intval($data['ticket_type']),
            'amount' => $data['amount'],
            'unique_id' => (string) Str::uuid(),
            'paymentStatus' => 1,
        ]);


        // Générer les URLs pour les 3 événements
        $openingUrl = route('cn.verify', ['data' => $convention->unique_id, 'event' => 'opening']);
        $agUrl = route('cn.verify', ['data' => $convention->unique_id, 'event' => 'ag']);
        $galaUrl = route('cn.verify', ['data' => $convention->unique_id, 'event' => 'gala']);

        // Créer les QR codes pour chaque événement
        $qrOpening = new QrCode($openingUrl);
        $qrAg = new QrCode($agUrl);
        $qrGala = new QrCode($galaUrl);
        
        $writer = new PngWriter();
        $qrOpeningImage = $writer->write($qrOpening)->getDataUri();
        $qrAgImage = $writer->write($qrAg)->getDataUri();
        $qrGalaImage = $writer->write($qrGala)->getDataUri();

        // Dimensions du ticket
        $ticketWidth = 800;
        $ticketHeight = 550;

        // Créer le ticket avec header-cn.png comme background
        $eventImagePath = storage_path('app/public/tickets/header-cn.png');
        if (file_exists($eventImagePath)) {
            // Utiliser directement l'image comme base
            $backgroundImage = Image::make($eventImagePath);
            
            // Redimensionner l'image pour atteindre les dimensions du ticket
            $backgroundImage->resize($ticketWidth, $ticketHeight, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize(); // Permet d'agrandir l'image si nécessaire
            });
            
            // Si l'image est plus petite que les dimensions du ticket, créer un canvas blanc et centrer l'image
            if ($backgroundImage->width() < $ticketWidth || $backgroundImage->height() < $ticketHeight) {
                $canvas = Image::canvas($ticketWidth, $ticketHeight, '#ffffff');
                $canvas->insert($backgroundImage, 'center');
                $backgroundImage = $canvas;
            }
            
            // Si l'image est plus grande que les dimensions du ticket, recadrer au centre
            if ($backgroundImage->width() > $ticketWidth || $backgroundImage->height() > $ticketHeight) {
                $backgroundImage->fit($ticketWidth, $ticketHeight, function ($constraint) {
                    $constraint->upsize();
                }, 'center');
            }
        } else {
            // Si l'image n'existe pas, créer un fond simple
            $backgroundImage = Image::canvas($ticketWidth, $ticketHeight, '#f5f5f5');
        }
        
        // Ajouter un overlay de couleur verte semi-transparent
        $overlay = Image::canvas($ticketWidth, $ticketHeight, '#025225'); // vert foncé
        $overlay->opacity(85); // 85% d'opacité (15% de transparence) - rgba(2, 82, 37, 0.85)
        $backgroundImage->insert($overlay, 'top-left', 0, 0);
        
        // Vérifier si les polices personnalisées existent
        $robotoRegularPath = storage_path('app/public/tickets/Roboto-Regular.ttf');
        $robotoBoldPath = storage_path('app/public/tickets/Roboto-Bold.ttf');
        $robotoCondensedPath = storage_path('app/public/tickets/Roboto_Condensed-Italic.ttf');
        
        // Utiliser des polices par défaut si les polices personnalisées ne sont pas disponibles
        $hasRobotoRegular = file_exists($robotoRegularPath);
        $hasRobotoBold = file_exists($robotoBoldPath);
        $hasRobotoCondensed = file_exists($robotoCondensedPath);
        
        // Configuration des QR codes
        $qrWidth = 80; // Taille des QR codes
        $qrHeight = 80;
        $qrPanelPadding = 20; // Padding pour le panneau QR
        
        // Calculer largeur exacte nécessaire pour la zone QR
        // Largeur max de texte sous QR (Cérémonie d'ouverture) = environ 160px
        $qrPanelExactWidth = max($qrWidth, 160) + ($qrPanelPadding * 2);
        
        // Configuration des zones
        $panelMargin = 20; // Marge autour des zones
        $leftPanelPadding = 30; // Padding interne de la zone gauche
        $panelHeight = 450; // Hauteur de la zone droite
        
        // Largeur de la zone gauche
        $leftPanelWidth = $ticketWidth - $panelMargin * 2 - $qrPanelExactWidth;
        
        // Espacement vertical pour les informations client
        $totalInfoLines = 5; // Nombre de lignes d'information (augmenté pour inclure le montant)
        $infoSpacing = 55; // Espacement entre les lignes d'information
        $totalInfoHeight = $infoSpacing * ($totalInfoLines - 1); // Hauteur totale des informations
        
        // Ajouter du padding autour du contenu pour la carte gauche
        $leftCardPaddingTop = 40;
        $leftCardPaddingBottom = 40;
        
        // Calculer la hauteur exacte nécessaire pour la carte gauche
        $leftCardHeight = $totalInfoHeight + $leftCardPaddingTop + $leftCardPaddingBottom;
        
        // Centrer verticalement les zones
        $rightPanelY = (int)(($ticketHeight - $panelHeight) / 2);
        $leftPanelY = (int)(($ticketHeight - $leftCardHeight) / 2);
        
        // Position de départ de la zone QR
        $rightPanelStart = $panelMargin + $leftPanelWidth;
        
        // Titre du ticket - centré en haut
        $backgroundImage->text("TICKET - CONSEIL NATIONAL 2025", $ticketWidth / 2, min($leftPanelY, $rightPanelY) - 15, function($font) use ($hasRobotoBold, $robotoBoldPath, $hasRobotoRegular, $robotoRegularPath) {
            if ($hasRobotoBold) {
                $font->file($robotoBoldPath);
            } elseif ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size(18);
            $font->color('#ffffff'); // Texte blanc
            $font->align('center');
            $font->valign('bottom');
        });
        
        // Couleur de fond des cartes - blanc semi-transparent (30% au lieu de 10%)
        $cardColor = 'rgba(255, 255, 255, 0.3)';
        $cardBorderColor = 'rgba(255, 255, 255, 0.4)'; // Bordure légèrement plus visible
        
        // Créer l'image de la première carte avec coins arrondis
        $leftCard = Image::canvas($leftPanelWidth, $leftCardHeight, $cardColor);
        
        // Créer l'image de la seconde carte avec coins arrondis
        $rightCard = Image::canvas($qrPanelExactWidth, $panelHeight, $cardColor);
        
        // Insérer les cartes sur l'image principale
        $backgroundImage->insert($leftCard, 'top-left', $panelMargin, $leftPanelY);
        $backgroundImage->insert($rightCard, 'top-left', $rightPanelStart, $rightPanelY);
        
        // Positions des informations client
        $infoX = $panelMargin + $leftPanelPadding;
        $valueX = $infoX + 180;
        
        // Calculer la position de départ des infos centrées dans la carte de gauche
        $infoStartY = $leftPanelY + $leftCardPaddingTop;
        
        // Augmentation de la taille des caractères pour les informations personnelles
        $labelSize = 18; // Était 16
        $valueSize = 18; // Était 16
        
        // Nom et Prénoms
        $backgroundImage->text("Nom et Prénoms:", $infoX, $infoStartY, function($font) use ($hasRobotoBold, $robotoBoldPath, $hasRobotoRegular, $robotoRegularPath, $labelSize) {
            if ($hasRobotoBold) {
                $font->file($robotoBoldPath);
            } elseif ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size($labelSize);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // Valeur pour Nom et Prénoms
        $backgroundImage->text($convention->fullname, $valueX, $infoStartY, function($font) use ($hasRobotoRegular, $robotoRegularPath, $valueSize) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size($valueSize);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // Qualité
        $backgroundImage->text("Qualité:", $infoX, $infoStartY + $infoSpacing, function($font) use ($hasRobotoBold, $robotoBoldPath, $hasRobotoRegular, $robotoRegularPath, $labelSize) {
            if ($hasRobotoBold) {
                $font->file($robotoBoldPath);
            } elseif ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size($labelSize);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // Valeur pour Qualité
        $backgroundImage->text($convention->quality, $valueX, $infoStartY + $infoSpacing, function($font) use ($hasRobotoRegular, $robotoRegularPath, $valueSize) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size($valueSize);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // OLM
        $backgroundImage->text("OLM/Autres:", $infoX, $infoStartY + ($infoSpacing * 2), function($font) use ($hasRobotoBold, $robotoBoldPath, $hasRobotoRegular, $robotoRegularPath, $labelSize) {
            if ($hasRobotoBold) {
                $font->file($robotoBoldPath);
            } elseif ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size($labelSize);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // Valeur pour OLM
        $olm = !empty($convention->other_organization) ? $convention->other_organization : $convention->organization;
        $backgroundImage->text($olm, $valueX, $infoStartY + ($infoSpacing * 2), function($font) use ($hasRobotoRegular, $robotoRegularPath, $valueSize) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size($valueSize);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // Numéro WhatsApp
        $backgroundImage->text("Numéro WhatsApp:", $infoX, $infoStartY + ($infoSpacing * 3), function($font) use ($hasRobotoBold, $robotoBoldPath, $hasRobotoRegular, $robotoRegularPath, $labelSize) {
            if ($hasRobotoBold) {
                $font->file($robotoBoldPath);
            } elseif ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size($labelSize);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // Valeur pour Numéro WhatsApp
        $backgroundImage->text($convention->phone, $valueX, $infoStartY + ($infoSpacing * 3), function($font) use ($hasRobotoRegular, $robotoRegularPath, $valueSize) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size($valueSize);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // Montant du ticket
        $backgroundImage->text("Montant:", $infoX, $infoStartY + ($infoSpacing * 4), function($font) use ($hasRobotoBold, $robotoBoldPath, $hasRobotoRegular, $robotoRegularPath, $labelSize) {
            if ($hasRobotoBold) {
                $font->file($robotoBoldPath);
            } elseif ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size($labelSize);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // Valeur pour le montant (ticket_type)
        $backgroundImage->text($convention->amount . " FCFA", $valueX, $infoStartY + ($infoSpacing * 4), function($font) use ($hasRobotoRegular, $robotoRegularPath, $valueSize) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size($valueSize);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // Numéro du ticket (en bas à droite de la carte gauche)
        $backgroundImage->text("#" . substr($convention->id . $convention->unique_id, 0, 8), $panelMargin + $leftPanelWidth - $leftPanelPadding, $leftPanelY + $leftCardHeight - 20, function($font) use ($hasRobotoCondensed, $robotoCondensedPath) {
            if ($hasRobotoCondensed) {
                $font->file($robotoCondensedPath);
            } else {
                $font->file(5);
            }
            $font->size(14);
            $font->color('#ffffff'); // Texte blanc
            $font->align('right');
            $font->valign('bottom');
        });
        
        // Centrer horizontalement les QR codes dans la zone droite
        $qrXCenter = $rightPanelStart + ($qrPanelExactWidth / 2);
        $qrX = (int)($qrXCenter - ($qrWidth / 2));
        
        // Hauteur de chaque "bloc" QR code (QR + texte)
        $qrTextHeight = 35; // Espace pour le texte sous chaque QR (réduit pour rapprocher le texte du QR)
        $qrBlockHeight = $qrHeight + $qrTextHeight;
        
        // Espace total nécessaire pour les 3 blocs QR
        $totalQrBlocksHeight = $qrBlockHeight * 3;
        
        // Augmenter l'espace entre les QR codes
        $qrSpacing = 25; // Était calculé dynamiquement
        
        // Ajuster la position Y du premier QR code pour compenser l'augmentation de l'espacement
        $qrStartY = $rightPanelY + $qrPanelPadding + 10; // Ajout d'un décalage fixe de 10px
        
        // Premier QR code - Cérémonie d'ouverture
        $qrOpeningImage = base64_decode(preg_replace('/^data:image\/png;base64,/', '', $qrOpeningImage));
        $qrOpeningObj = Image::make($qrOpeningImage)->resize($qrWidth, $qrHeight);
        $backgroundImage->insert($qrOpeningObj, 'top-left', $qrX, $qrStartY);
        
        // Rapprocher la description du QR code
        $backgroundImage->text("Cérémonie d'ouverture", $qrXCenter, $qrStartY + $qrHeight + 7, function($font) use ($hasRobotoRegular, $robotoRegularPath) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size(13);
            $font->color('#ffffff'); // Texte blanc
            $font->align('center');
            $font->valign('top');
        });
        
        $backgroundImage->text("9 mai 2025 - 18h", $qrXCenter, $qrStartY + $qrHeight + 25, function($font) use ($hasRobotoRegular, $robotoRegularPath) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size(11);
            $font->color('#ffffff'); // Texte blanc
            $font->align('center');
            $font->valign('top');
        });
        
        // Deuxième QR code - AG avec espacement augmenté
        $qrY2 = $qrStartY + $qrBlockHeight + $qrSpacing;
        $qrAgImage = base64_decode(preg_replace('/^data:image\/png;base64,/', '', $qrAgImage));
        $qrAgObj = Image::make($qrAgImage)->resize($qrWidth, $qrHeight);
        $backgroundImage->insert($qrAgObj, 'top-left', $qrX, $qrY2);
        
        // Rapprocher la description du QR code
        $backgroundImage->text("AG", $qrXCenter, $qrY2 + $qrHeight + 7, function($font) use ($hasRobotoRegular, $robotoRegularPath) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size(13);
            $font->color('#ffffff'); // Texte blanc
            $font->align('center');
            $font->valign('top');
        });
        
        $backgroundImage->text("10 mai 2025 - 9h", $qrXCenter, $qrY2 + $qrHeight + 25, function($font) use ($hasRobotoRegular, $robotoRegularPath) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size(11);
            $font->color('#ffffff'); // Texte blanc
            $font->align('center');
            $font->valign('top');
        });
        
        // Troisième QR code - Dîner Gala avec espacement augmenté
        $qrY3 = $qrY2 + $qrBlockHeight + $qrSpacing;
        $qrGalaImage = base64_decode(preg_replace('/^data:image\/png;base64,/', '', $qrGalaImage));
        $qrGalaObj = Image::make($qrGalaImage)->resize($qrWidth, $qrHeight);
        $backgroundImage->insert($qrGalaObj, 'top-left', $qrX, $qrY3);
        
        // Rapprocher la description du QR code
        $backgroundImage->text("Dîner Gala", $qrXCenter, $qrY3 + $qrHeight + 7, function($font) use ($hasRobotoRegular, $robotoRegularPath) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size(13);
            $font->color('#ffffff'); // Texte blanc
            $font->align('center');
            $font->valign('top');
        });
        
        $backgroundImage->text("10 mai 2025 - 19h", $qrXCenter, $qrY3 + $qrHeight + 25, function($font) use ($hasRobotoRegular, $robotoRegularPath) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size(11);
            $font->color('#ffffff'); // Texte blanc
            $font->align('center');
            $font->valign('top');
        });
        
        // Ajouter le texte important en bas à gauche du ticket avec la première partie en gras
        // Définir les coordonnées pour le bas à gauche avec une marge
        $textMarginLeft = 30;
        
        // Première partie en gras
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
                $font->color('#ffffff'); // Texte blanc
                $font->align('left');
                $font->valign('center');
            }
        );
        
        // Deuxième partie en normal - sur la même ligne pour un affichage compact
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
                $font->color('#ffffff'); // Texte blanc
                $font->align('left');
                $font->valign('center');
            }
        );
        
        // Sauvegarder l'image dans storage (pour l'email)
        $storagePath = storage_path("app/public/tickets/ticket_{$convention->unique_id}.png");
        $backgroundImage->save($storagePath);
        
        // Sauvegarder également l'image dans le dossier public (pour l'API)
        $publicDirectory = public_path('tickets');
        // Créer le répertoire s'il n'existe pas
        if (!file_exists($publicDirectory)) {
            mkdir($publicDirectory, 0755, true);
        }
    
        $publicPath = $publicDirectory . "/ticket_{$convention->unique_id}.png";
        $backgroundImage->save($publicPath);
        
        // URL publique pour l'API
        $publicUrl = "https://czotick.com/public/tickets/ticket_{$convention->unique_id}.png";
        
        // Préparer les données pour l'API WhatsApp
        // Extraire les 8 derniers chiffres du numéro de téléphone
        $phone = $convention->phone;
        $lastEightDigits = substr($phone, -8);
        
        $whatsappData = [
            "mediaUrl" => $publicUrl, // URL de l'image accessible publiquement
            "chatId" => "225" . $lastEightDigits . "@c.us"
        ];
        
       
        Mail::send('emails.ticket', [
            'fullname' => $convention->fullname,
            'phone' => $convention->phone,
            'email' => $convention->email,
            'organization' => $convention->organization,
            'other_organization' => $convention->other_organization,
            'quality' => $convention->quality,
        ], function ($message) use ($convention, $storagePath) {
            $email = $convention->email;

            if (empty($email) || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                throw new Swift_RfcComplianceException("Invalid email address: $email");
            }

            $message->to($email)
                    ->cc('dematdigit.jcici@gmail.com')
                    ->subject('Ticket - Conseil National 2025')
                    ->attach($storagePath);
        });

        $url = "https://waapi.app/api/v1/instances/62016/client/action/send-media";

        $headers = [
            "accept: application/json",
            "authorization: Bearer YG4kImDmDxZhcQbDupXMGzuL9QzZY2UXWLAHmnId58c2ecee",
            "content-type: application/json"
        ];

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($whatsappData));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $errorMessage = 'Erreur cURL : ' . curl_error($ch);
            file_put_contents(storage_path('logs/curl_errors.log'), '[' . date('Y-m-d H:i:s') . '] ' . $errorMessage . PHP_EOL, FILE_APPEND);
        } else {
            $successMessage = 'Réponse : ' . $response;
            file_put_contents(storage_path('logs/curl_responses.log'), '[' . date('Y-m-d H:i:s') . '] ' . $successMessage . PHP_EOL, FILE_APPEND);
        }

        curl_close($ch); 

        return redirect()->route('success')->with('message', 'Votre inscription a été validée avec succès! Votre ticket a été envoyé par email.');
    }
    /* public function payment(Request $request)
    {
        $convention = Convention::where('email', $request->email)->first();
        $convention->unique_id = (string) Str::uuid();
        $convention->paymentStatus = 1;
        $convention->update();

        // Générer les URLs pour les 3 événements
        $openingUrl = route('cn.verify', ['data' => $convention->unique_id, 'event' => 'opening']);
        $agUrl = route('cn.verify', ['data' => $convention->unique_id, 'event' => 'ag']);
        $galaUrl = route('cn.verify', ['data' => $convention->unique_id, 'event' => 'gala']);

        // Créer les QR codes pour chaque événement
        $qrOpening = new QrCode($openingUrl);
        $qrAg = new QrCode($agUrl);
        $qrGala = new QrCode($galaUrl);
        
        $writer = new PngWriter();
        $qrOpeningImage = $writer->write($qrOpening)->getDataUri();
        $qrAgImage = $writer->write($qrAg)->getDataUri();
        $qrGalaImage = $writer->write($qrGala)->getDataUri();

        // Dimensions du ticket
        $ticketWidth = 800;
        $ticketHeight = 550;

        // Créer le ticket avec header-cn.png comme background
        $eventImagePath = storage_path('app/public/tickets/header-cn.png');
        if (file_exists($eventImagePath)) {
            // Utiliser directement l'image comme base
            $backgroundImage = Image::make($eventImagePath);
            
            // Redimensionner l'image pour atteindre les dimensions du ticket
            $backgroundImage->resize($ticketWidth, $ticketHeight, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize(); // Permet d'agrandir l'image si nécessaire
            });
            
            // Si l'image est plus petite que les dimensions du ticket, créer un canvas blanc et centrer l'image
            if ($backgroundImage->width() < $ticketWidth || $backgroundImage->height() < $ticketHeight) {
                $canvas = Image::canvas($ticketWidth, $ticketHeight, '#ffffff');
                $canvas->insert($backgroundImage, 'center');
                $backgroundImage = $canvas;
            }
            
            // Si l'image est plus grande que les dimensions du ticket, recadrer au centre
            if ($backgroundImage->width() > $ticketWidth || $backgroundImage->height() > $ticketHeight) {
                $backgroundImage->fit($ticketWidth, $ticketHeight, function ($constraint) {
                    $constraint->upsize();
                }, 'center');
            }
        } else {
            // Si l'image n'existe pas, créer un fond simple
            $backgroundImage = Image::canvas($ticketWidth, $ticketHeight, '#f5f5f5');
        }
        
        // Ajouter un overlay de couleur verte semi-transparent
        $overlay = Image::canvas($ticketWidth, $ticketHeight, '#025225'); // vert foncé
        $overlay->opacity(85); // 85% d'opacité (15% de transparence) - rgba(2, 82, 37, 0.85)
        $backgroundImage->insert($overlay, 'top-left', 0, 0);
        
        // Vérifier si les polices personnalisées existent
        $robotoRegularPath = storage_path('app/public/tickets/Roboto-Regular.ttf');
        $robotoBoldPath = storage_path('app/public/tickets/Roboto-Bold.ttf');
        $robotoCondensedPath = storage_path('app/public/tickets/Roboto_Condensed-Italic.ttf');
        
        // Utiliser des polices par défaut si les polices personnalisées ne sont pas disponibles
        $hasRobotoRegular = file_exists($robotoRegularPath);
        $hasRobotoBold = file_exists($robotoBoldPath);
        $hasRobotoCondensed = file_exists($robotoCondensedPath);
        
        // Configuration des QR codes
        $qrWidth = 80; // Taille des QR codes
        $qrHeight = 80;
        $qrPanelPadding = 20; // Padding pour le panneau QR
        
        // Calculer largeur exacte nécessaire pour la zone QR
        // Largeur max de texte sous QR (Cérémonie d'ouverture) = environ 160px
        $qrPanelExactWidth = max($qrWidth, 160) + ($qrPanelPadding * 2);
        
        // Configuration des zones
        $panelMargin = 20; // Marge autour des zones
        $leftPanelPadding = 30; // Padding interne de la zone gauche
        $panelHeight = 450; // Hauteur de la zone droite
        
        // Largeur de la zone gauche
        $leftPanelWidth = $ticketWidth - $panelMargin * 2 - $qrPanelExactWidth;
        
        // Espacement vertical pour les informations client
        $totalInfoLines = 4; // Nombre de lignes d'information
        $infoSpacing = 55; // Espacement entre les lignes d'information
        $totalInfoHeight = $infoSpacing * ($totalInfoLines - 1); // Hauteur totale des informations
        
        // Ajouter du padding autour du contenu pour la carte gauche
        $leftCardPaddingTop = 40;
        $leftCardPaddingBottom = 40;
        
        // Calculer la hauteur exacte nécessaire pour la carte gauche
        $leftCardHeight = $totalInfoHeight + $leftCardPaddingTop + $leftCardPaddingBottom;
        
        // Centrer verticalement les zones
        $rightPanelY = (int)(($ticketHeight - $panelHeight) / 2);
        $leftPanelY = (int)(($ticketHeight - $leftCardHeight) / 2);
        
        // Position de départ de la zone QR
        $rightPanelStart = $panelMargin + $leftPanelWidth;
        
        // Titre du ticket - centré en haut
        $backgroundImage->text("TICKET - CONSEIL NATIONAL 2025", $ticketWidth / 2, min($leftPanelY, $rightPanelY) - 15, function($font) use ($hasRobotoBold, $robotoBoldPath, $hasRobotoRegular, $robotoRegularPath) {
            if ($hasRobotoBold) {
                $font->file($robotoBoldPath);
            } elseif ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size(18);
            $font->color('#ffffff'); // Texte blanc
            $font->align('center');
            $font->valign('bottom');
        });
        
        // Couleur de fond des cartes - blanc semi-transparent (30% au lieu de 10%)
        $cardColor = 'rgba(255, 255, 255, 0.3)';
        $cardBorderColor = 'rgba(255, 255, 255, 0.4)'; // Bordure légèrement plus visible
        
        // Créer l'image de la première carte avec coins arrondis
        $leftCard = Image::canvas($leftPanelWidth, $leftCardHeight, $cardColor);
        
        // Créer l'image de la seconde carte avec coins arrondis
        $rightCard = Image::canvas($qrPanelExactWidth, $panelHeight, $cardColor);
        
        // Insérer les cartes sur l'image principale
        $backgroundImage->insert($leftCard, 'top-left', $panelMargin, $leftPanelY);
        $backgroundImage->insert($rightCard, 'top-left', $rightPanelStart, $rightPanelY);
        
        // Positions des informations client
        $infoX = $panelMargin + $leftPanelPadding;
        $valueX = $infoX + 180;
        
        // Calculer la position de départ des infos centrées dans la carte de gauche
        $infoStartY = $leftPanelY + $leftCardPaddingTop;
        
        // Nom et Prénoms
        $backgroundImage->text("Nom et Prénoms:", $infoX, $infoStartY, function($font) use ($hasRobotoBold, $robotoBoldPath, $hasRobotoRegular, $robotoRegularPath) {
            if ($hasRobotoBold) {
                $font->file($robotoBoldPath);
            } elseif ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size(16);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // Valeur pour Nom et Prénoms
        $backgroundImage->text($convention->fullname, $valueX, $infoStartY, function($font) use ($hasRobotoRegular, $robotoRegularPath) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size(16);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // Qualité
        $backgroundImage->text("Qualité:", $infoX, $infoStartY + $infoSpacing, function($font) use ($hasRobotoBold, $robotoBoldPath, $hasRobotoRegular, $robotoRegularPath) {
            if ($hasRobotoBold) {
                $font->file($robotoBoldPath);
            } elseif ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size(16);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // Valeur pour Qualité
        $backgroundImage->text($convention->quality, $valueX, $infoStartY + $infoSpacing, function($font) use ($hasRobotoRegular, $robotoRegularPath) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size(16);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // OLM
        $backgroundImage->text("OLM:", $infoX, $infoStartY + ($infoSpacing * 2), function($font) use ($hasRobotoBold, $robotoBoldPath, $hasRobotoRegular, $robotoRegularPath) {
            if ($hasRobotoBold) {
                $font->file($robotoBoldPath);
            } elseif ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size(16);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // Valeur pour OLM
        $olm = !empty($convention->other_organization) ? $convention->other_organization : $convention->organization;
        $backgroundImage->text($olm, $valueX, $infoStartY + ($infoSpacing * 2), function($font) use ($hasRobotoRegular, $robotoRegularPath) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size(16);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // Numéro WhatsApp
        $backgroundImage->text("Numéro WhatsApp:", $infoX, $infoStartY + ($infoSpacing * 3), function($font) use ($hasRobotoBold, $robotoBoldPath, $hasRobotoRegular, $robotoRegularPath) {
            if ($hasRobotoBold) {
                $font->file($robotoBoldPath);
            } elseif ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size(16);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // Valeur pour Numéro WhatsApp
        $backgroundImage->text($convention->phone, $valueX, $infoStartY + ($infoSpacing * 3), function($font) use ($hasRobotoRegular, $robotoRegularPath) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size(16);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // Numéro du ticket (en bas à droite de la carte gauche)
        $backgroundImage->text("#" . substr($convention->unique_id, 0, 8), $panelMargin + $leftPanelWidth - $leftPanelPadding, $leftPanelY + $leftCardHeight - 20, function($font) use ($hasRobotoCondensed, $robotoCondensedPath) {
            if ($hasRobotoCondensed) {
                $font->file($robotoCondensedPath);
            } else {
                $font->file(5);
            }
            $font->size(14);
            $font->color('#ffffff'); // Texte blanc
            $font->align('right');
            $font->valign('bottom');
        });
        
        // Centrer horizontalement les QR codes dans la zone droite
        $qrXCenter = $rightPanelStart + ($qrPanelExactWidth / 2);
        $qrX = (int)($qrXCenter - ($qrWidth / 2));
        
        // Hauteur de chaque "bloc" QR code (QR + texte)
        $qrTextHeight = 40; // Espace pour le texte sous chaque QR
        $qrBlockHeight = $qrHeight + $qrTextHeight;
        
        // Espace total nécessaire pour les 3 blocs QR
        $totalQrBlocksHeight = $qrBlockHeight * 3;
        
        // Calculer l'espace entre les blocs pour les centrer verticalement
        $availableHeight = $panelHeight - (2 * $qrPanelPadding);
        $qrSpacing = (int)(($availableHeight - $totalQrBlocksHeight) / 4); // 4 espaces (au-dessus, entre et en-dessous)
        
        // Position Y du premier QR code
        $qrStartY = $rightPanelY + $qrPanelPadding + $qrSpacing;
        
        // Premier QR code - Cérémonie d'ouverture
        $qrOpeningImage = base64_decode(preg_replace('/^data:image\/png;base64,/', '', $qrOpeningImage));
        $qrOpeningObj = Image::make($qrOpeningImage)->resize($qrWidth, $qrHeight);
        $backgroundImage->insert($qrOpeningObj, 'top-left', $qrX, $qrStartY);
        
        $backgroundImage->text("Cérémonie d'ouverture", $qrXCenter, $qrStartY + $qrHeight + 10, function($font) use ($hasRobotoRegular, $robotoRegularPath) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size(13);
            $font->color('#ffffff'); // Texte blanc
            $font->align('center');
            $font->valign('top');
        });
        
        $backgroundImage->text("9 mai 2025 - 18h", $qrXCenter, $qrStartY + $qrHeight + 28, function($font) use ($hasRobotoRegular, $robotoRegularPath) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size(11);
            $font->color('#ffffff'); // Texte blanc
            $font->align('center');
            $font->valign('top');
        });
        
        // Deuxième QR code - AG
        $qrY2 = $qrStartY + $qrBlockHeight + $qrSpacing;
        $qrAgImage = base64_decode(preg_replace('/^data:image\/png;base64,/', '', $qrAgImage));
        $qrAgObj = Image::make($qrAgImage)->resize($qrWidth, $qrHeight);
        $backgroundImage->insert($qrAgObj, 'top-left', $qrX, $qrY2);
        
        $backgroundImage->text("AG", $qrXCenter, $qrY2 + $qrHeight + 10, function($font) use ($hasRobotoRegular, $robotoRegularPath) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size(13);
            $font->color('#ffffff'); // Texte blanc
            $font->align('center');
            $font->valign('top');
        });
        
        $backgroundImage->text("10 mai 2025 - 9h", $qrXCenter, $qrY2 + $qrHeight + 28, function($font) use ($hasRobotoRegular, $robotoRegularPath) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size(11);
            $font->color('#ffffff'); // Texte blanc
            $font->align('center');
            $font->valign('top');
        });
        
        // Troisième QR code - Dîner Gala
        $qrY3 = $qrY2 + $qrBlockHeight + $qrSpacing;
        $qrGalaImage = base64_decode(preg_replace('/^data:image\/png;base64,/', '', $qrGalaImage));
        $qrGalaObj = Image::make($qrGalaImage)->resize($qrWidth, $qrHeight);
        $backgroundImage->insert($qrGalaObj, 'top-left', $qrX, $qrY3);
        
        $backgroundImage->text("Dîner Gala", $qrXCenter, $qrY3 + $qrHeight + 10, function($font) use ($hasRobotoRegular, $robotoRegularPath) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size(13);
            $font->color('#ffffff'); // Texte blanc
            $font->align('center');
            $font->valign('top');
        });
        
        $backgroundImage->text("10 mai 2025 - 19h", $qrXCenter, $qrY3 + $qrHeight + 28, function($font) use ($hasRobotoRegular, $robotoRegularPath) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size(11);
            $font->color('#ffffff'); // Texte blanc
            $font->align('center');
            $font->valign('top');
        });
        
         // Sauvegarder l'image dans storage (pour l'email)
        $storagePath = storage_path("app/public/tickets/ticket_{$convention->unique_id}.png");
        $backgroundImage->save($storagePath);
        
        // Sauvegarder également l'image dans le dossier public (pour l'API)
        $publicDirectory = public_path('tickets');
        
        // Créer le répertoire s'il n'existe pas
        if (!file_exists($publicDirectory)) {
            mkdir($publicDirectory, 0755, true);
        }
    
        $publicPath = $publicDirectory . "/ticket_{$convention->unique_id}.png";
        $backgroundImage->save($publicPath);
        
        // URL publique pour l'API
        $publicUrl = "https://czotick.com/public/tickets/ticket_{$convention->unique_id}.png";
        
        // Préparer les données pour l'API WhatsApp
        // Extraire les 8 derniers chiffres du numéro de téléphone
        $phone = $convention->phone;
        $lastEightDigits = substr($phone, -8);
        
        $whatsappData = [
            "mediaUrl" => $publicUrl, // URL de l'image accessible publiquement
            "chatId" => "225" . $lastEightDigits . "@c.us"
        ];
        
        // Vous pouvez stocker ces données pour les utiliser plus tard avec l'API
        // Par exemple, vous pourriez les enregistrer dans une table de la base de données
        // ou les passer à une autre méthode qui se connecte à l'API WhatsApp
        
        // Si besoin de stocker ces données en session
        // session(['whatsapp_data' => $whatsappData]);
        
        // Envoyer l'email avec l'image en pièce jointe
        Mail::send('emails.ticket', [
            'fullname' => $convention->fullname,
            'phone' => $convention->phone,
            'email' => $convention->email,
            'organization' => $convention->organization,
            'other_organization' => $convention->other_organization,
            'quality' => $convention->quality,
        ], function ($message) use ($convention, $storagePath) {
            $email = $convention->email;

            if (empty($email) || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                throw new Swift_RfcComplianceException("Invalid email address: $email");
            }

            $message->to($email)
                    ->cc('larissabrou6@gmail.com')
                    ->subject('Ticket - Conseil National 2025')
                    ->attach($storagePath);
        });

        $url = "https://waapi.app/api/v1/instances/61670/client/action/send-media";

        $headers = [
            "accept: application/json",
            "authorization: Bearer YG4kImDmDxZhcQbDupXMGzuL9QzZY2UXWLAHmnId58c2ecee",
            "content-type: application/json"
        ];

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($whatsappData));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $errorMessage = 'Erreur cURL : ' . curl_error($ch);
            file_put_contents(storage_path('logs/curl_errors.log'), '[' . date('Y-m-d H:i:s') . '] ' . $errorMessage . PHP_EOL, FILE_APPEND);
        } else {
            $successMessage = 'Réponse : ' . $response;
            file_put_contents(storage_path('logs/curl_responses.log'), '[' . date('Y-m-d H:i:s') . '] ' . $successMessage . PHP_EOL, FILE_APPEND);
        }

        curl_close($ch);

        return redirect()->route('success')->with('message', 'Votre inscription a été validée avec succès! Votre ticket a été envoyé par email.');
    } */

    public function showSuccess()
    {
        return view('public.success'); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $programs = Program::all();
        return view('backend.activities.create', ['edit' => false, 'programs' => $programs]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'content' => 'nullable|string',
            'program_id' => 'required',
            'dateStart' => 'required',
            'dateEnd' => 'required',
            'location' => 'required'
        ]);

        $activity = new Activity($request->only(['title', 'content', 'senateur', 'membre', 'etudiant', 'dateStart', 'dateEnd', 'location']));

        if (auth()->check()) {
            $activity->owner()->associate(auth()->id());
        }

        $imageFile = $request->input('image_file');

        $activityImage = "";
        if (!empty($imageFile)) {
            $fImage = json_decode($imageFile);

            $currentDate = Carbon::now()->toDateString();
            $activityImage = 'activity-' . $currentDate . '-' . uniqid().'.'.substr($fImage->type, -4);

            if (!Storage::disk('public')->exists('activity')) {
                Storage::disk('public')->makeDirectory('activity');
            }
            $fStream = Image::make($fImage->data)->encode(substr($fImage->type, -4), 65)->stream();

            Storage::disk('public')->put('activity/' . $activityImage, $fStream);
        }

        $activity->image = 'activity/' . $activityImage;
        $activity->program_id = $request->input('program_id');
        $activity->save();

        return response()->json([
            "success" => true,
            "message" => 'L\'activité "'.$activity->title.'" a bien été créée.',
            "redirect" => route('activities.index'),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function show(Activity $activity)
    {
        $programs = Program::all();

        return view('backend.activities.show', [
            'edit' => false,
            'activity' => $activity,
            'programs' => $programs,
            'dateStart' => 'required',
            'dateEnd' => 'required'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function edit(Activity $activity)
    {
        $programs = Program::all();
  
        $activityImage['source'] = url('/public/storage/' . $activity->image);
        $activityImage['size'] = Storage::disk('public')->size($activity->image);
        $activityImage['type'] = 'local';

        return view('backend.activities.create', [
            'edit' => true,
            'activityImage' => $activityImage,
            'activity' => $activity,
            'programs' => $programs,
            'dateStart' => 'required',
            'dateEnd' => 'required'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Activity $activity)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'content' => 'required|string',
            'program_id' => 'required',
            'dateStart' => 'required',
            'dateEnd' => 'required'
        ]);

        $activity = $activity->fill($request->only(['title', 'content', 'location', 'dateStart', 'dateEnd']));

        $imageFile = $request->input('image_file');

        if (!empty($imageFile)) {
            $fImage = json_decode($imageFile);

            $currentDate = Carbon::now()->toDateString();
            $activityImage = 'activity-' . $currentDate . '-' . uniqid().'.'.substr($fImage->type, -4);

            if (!Storage::disk('public')->exists('activity')) {
                Storage::disk('public')->makeDirectory('activity');
            }
            $fStream = Image::make($fImage->data)->encode(substr($fImage->type, -4), 65)->stream();

            Storage::disk('public')->put('activity/' . $activityImage, $fStream);
            $activity->image = 'activity/' . $activityImage;
        }

        $activity->program_id = $request->input('program_id');
        $activity->save();

        return response()->json([
            "success" => true,
            "message" => 'L\'activité "'.$activity->title.'" a bien été mise à jour.',
            "redirect" => route('activities.index'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function destroy(Activity $activity)
    {
        if (File::exists(public_path('storage/' . $activity->image))) {
            File::delete(public_path('storage/' . $activity->image));
        }

        $activity->delete();

        return response()->json([
            "success" => true,
            "message" => 'L\'activité a été supprimée.',
            "redirect" => route('activities.index'),
        ]);
    }

    public function handle(Request $request)
    {

        // Enregistrer les données brutes pour débogage
        $rawData = $request->getContent();
        Log::info('Données brutes reçues: ' . $rawData);
        
        // On lit directement le JSON brut du body
        $data = $request->json()->all();
        
        Log::info('Réception callback Wave', ['data' => $data]);
        
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
        
        // Rechercher la transaction via la référence client
        $transaction = Transaction::where('reference_czotic', $clientReference)->first();
        
        if (!$transaction) {
            Log::error('Transaction non trouvée pour callback', ['client_reference' => $clientReference]);
            return response()->json(['status' => 'not_found'], 404);
        }
        
        // Mise à jour de la transaction
        $transaction->status = $paymentStatus === 'succeeded' ? 1 : 0;
        $transaction->update();


        if (intval($transaction->ticket_type) == 15200) {
            $transaction->amount = 15000;
        } elseif (intval($transaction->ticket_type) == 7600) {
            $transaction->amount = 7500;
        }

        $convention = Convention::create([
            'email' => $transaction->email,
            'fullname' => $transaction->fullname,
            'organization' => $transaction->organization,
            'other_organization' => $transaction->other_organization,
            'quality' => $transaction->quality,
            'phone' => $transaction->phone,
            'ticket_type' => intval($transaction->ticket_type),
            'amount' => $transaction->amount,
            'unique_id' => (string) Str::uuid(),
            'paymentStatus' => 1,
        ]);


        // Générer les URLs pour les 3 événements
        $openingUrl = route('cn.verify', ['data' => $convention->unique_id, 'event' => 'opening']);
        $agUrl = route('cn.verify', ['data' => $convention->unique_id, 'event' => 'ag']);
        $galaUrl = route('cn.verify', ['data' => $convention->unique_id, 'event' => 'gala']);

        // Créer les QR codes pour chaque événement
        $qrOpening = new QrCode($openingUrl);
        $qrAg = new QrCode($agUrl);
        $qrGala = new QrCode($galaUrl);
        
        $writer = new PngWriter();
        $qrOpeningImage = $writer->write($qrOpening)->getDataUri();
        $qrAgImage = $writer->write($qrAg)->getDataUri();
        $qrGalaImage = $writer->write($qrGala)->getDataUri();

        // Dimensions du ticket
        $ticketWidth = 800;
        $ticketHeight = 550;

        // Créer le ticket avec header-cn.png comme background
        $eventImagePath = storage_path('app/public/tickets/header-cn.png');
        if (file_exists($eventImagePath)) {
            // Utiliser directement l'image comme base
            $backgroundImage = Image::make($eventImagePath);
            
            // Redimensionner l'image pour atteindre les dimensions du ticket
            $backgroundImage->resize($ticketWidth, $ticketHeight, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize(); // Permet d'agrandir l'image si nécessaire
            });
            
            // Si l'image est plus petite que les dimensions du ticket, créer un canvas blanc et centrer l'image
            if ($backgroundImage->width() < $ticketWidth || $backgroundImage->height() < $ticketHeight) {
                $canvas = Image::canvas($ticketWidth, $ticketHeight, '#ffffff');
                $canvas->insert($backgroundImage, 'center');
                $backgroundImage = $canvas;
            }
            
            // Si l'image est plus grande que les dimensions du ticket, recadrer au centre
            if ($backgroundImage->width() > $ticketWidth || $backgroundImage->height() > $ticketHeight) {
                $backgroundImage->fit($ticketWidth, $ticketHeight, function ($constraint) {
                    $constraint->upsize();
                }, 'center');
            }
        } else {
            // Si l'image n'existe pas, créer un fond simple
            $backgroundImage = Image::canvas($ticketWidth, $ticketHeight, '#f5f5f5');
        }
        
        // Ajouter un overlay de couleur verte semi-transparent
        $overlay = Image::canvas($ticketWidth, $ticketHeight, '#025225'); // vert foncé
        $overlay->opacity(85); // 85% d'opacité (15% de transparence) - rgba(2, 82, 37, 0.85)
        $backgroundImage->insert($overlay, 'top-left', 0, 0);
        
        // Vérifier si les polices personnalisées existent
        $robotoRegularPath = storage_path('app/public/tickets/Roboto-Regular.ttf');
        $robotoBoldPath = storage_path('app/public/tickets/Roboto-Bold.ttf');
        $robotoCondensedPath = storage_path('app/public/tickets/Roboto_Condensed-Italic.ttf');
        
        // Utiliser des polices par défaut si les polices personnalisées ne sont pas disponibles
        $hasRobotoRegular = file_exists($robotoRegularPath);
        $hasRobotoBold = file_exists($robotoBoldPath);
        $hasRobotoCondensed = file_exists($robotoCondensedPath);
        
        // Configuration des QR codes
        $qrWidth = 80; // Taille des QR codes
        $qrHeight = 80;
        $qrPanelPadding = 20; // Padding pour le panneau QR
        
        // Calculer largeur exacte nécessaire pour la zone QR
        // Largeur max de texte sous QR (Cérémonie d'ouverture) = environ 160px
        $qrPanelExactWidth = max($qrWidth, 160) + ($qrPanelPadding * 2);
        
        // Configuration des zones
        $panelMargin = 20; // Marge autour des zones
        $leftPanelPadding = 30; // Padding interne de la zone gauche
        $panelHeight = 450; // Hauteur de la zone droite
        
        // Largeur de la zone gauche
        $leftPanelWidth = $ticketWidth - $panelMargin * 2 - $qrPanelExactWidth;
        
        // Espacement vertical pour les informations client
        $totalInfoLines = 5; // Nombre de lignes d'information (augmenté pour inclure le montant)
        $infoSpacing = 55; // Espacement entre les lignes d'information
        $totalInfoHeight = $infoSpacing * ($totalInfoLines - 1); // Hauteur totale des informations
        
        // Ajouter du padding autour du contenu pour la carte gauche
        $leftCardPaddingTop = 40;
        $leftCardPaddingBottom = 40;
        
        // Calculer la hauteur exacte nécessaire pour la carte gauche
        $leftCardHeight = $totalInfoHeight + $leftCardPaddingTop + $leftCardPaddingBottom;
        
        // Centrer verticalement les zones
        $rightPanelY = (int)(($ticketHeight - $panelHeight) / 2);
        $leftPanelY = (int)(($ticketHeight - $leftCardHeight) / 2);
        
        // Position de départ de la zone QR
        $rightPanelStart = $panelMargin + $leftPanelWidth;
        
        // Titre du ticket - centré en haut
        $backgroundImage->text("TICKET - CONSEIL NATIONAL 2025", $ticketWidth / 2, min($leftPanelY, $rightPanelY) - 15, function($font) use ($hasRobotoBold, $robotoBoldPath, $hasRobotoRegular, $robotoRegularPath) {
            if ($hasRobotoBold) {
                $font->file($robotoBoldPath);
            } elseif ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size(18);
            $font->color('#ffffff'); // Texte blanc
            $font->align('center');
            $font->valign('bottom');
        });
        
        // Couleur de fond des cartes - blanc semi-transparent (30% au lieu de 10%)
        $cardColor = 'rgba(255, 255, 255, 0.3)';
        $cardBorderColor = 'rgba(255, 255, 255, 0.4)'; // Bordure légèrement plus visible
        
        // Créer l'image de la première carte avec coins arrondis
        $leftCard = Image::canvas($leftPanelWidth, $leftCardHeight, $cardColor);
        
        // Créer l'image de la seconde carte avec coins arrondis
        $rightCard = Image::canvas($qrPanelExactWidth, $panelHeight, $cardColor);
        
        // Insérer les cartes sur l'image principale
        $backgroundImage->insert($leftCard, 'top-left', $panelMargin, $leftPanelY);
        $backgroundImage->insert($rightCard, 'top-left', $rightPanelStart, $rightPanelY);
        
        // Positions des informations client
        $infoX = $panelMargin + $leftPanelPadding;
        $valueX = $infoX + 180;
        
        // Calculer la position de départ des infos centrées dans la carte de gauche
        $infoStartY = $leftPanelY + $leftCardPaddingTop;
        
        // Augmentation de la taille des caractères pour les informations personnelles
        $labelSize = 18; // Était 16
        $valueSize = 18; // Était 16
        
        // Nom et Prénoms
        $backgroundImage->text("Nom et Prénoms:", $infoX, $infoStartY, function($font) use ($hasRobotoBold, $robotoBoldPath, $hasRobotoRegular, $robotoRegularPath, $labelSize) {
            if ($hasRobotoBold) {
                $font->file($robotoBoldPath);
            } elseif ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size($labelSize);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // Valeur pour Nom et Prénoms
        $backgroundImage->text($convention->fullname, $valueX, $infoStartY, function($font) use ($hasRobotoRegular, $robotoRegularPath, $valueSize) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size($valueSize);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // Qualité
        $backgroundImage->text("Qualité:", $infoX, $infoStartY + $infoSpacing, function($font) use ($hasRobotoBold, $robotoBoldPath, $hasRobotoRegular, $robotoRegularPath, $labelSize) {
            if ($hasRobotoBold) {
                $font->file($robotoBoldPath);
            } elseif ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size($labelSize);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // Valeur pour Qualité
        $backgroundImage->text($convention->quality, $valueX, $infoStartY + $infoSpacing, function($font) use ($hasRobotoRegular, $robotoRegularPath, $valueSize) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size($valueSize);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // OLM
        $backgroundImage->text("OLM/Autres:", $infoX, $infoStartY + ($infoSpacing * 2), function($font) use ($hasRobotoBold, $robotoBoldPath, $hasRobotoRegular, $robotoRegularPath, $labelSize) {
            if ($hasRobotoBold) {
                $font->file($robotoBoldPath);
            } elseif ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size($labelSize);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // Valeur pour OLM
        $olm = !empty($convention->other_organization) ? $convention->other_organization : $convention->organization;
        $backgroundImage->text($olm, $valueX, $infoStartY + ($infoSpacing * 2), function($font) use ($hasRobotoRegular, $robotoRegularPath, $valueSize) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size($valueSize);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // Numéro WhatsApp
        $backgroundImage->text("Numéro WhatsApp:", $infoX, $infoStartY + ($infoSpacing * 3), function($font) use ($hasRobotoBold, $robotoBoldPath, $hasRobotoRegular, $robotoRegularPath, $labelSize) {
            if ($hasRobotoBold) {
                $font->file($robotoBoldPath);
            } elseif ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size($labelSize);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // Valeur pour Numéro WhatsApp
        $backgroundImage->text($convention->phone, $valueX, $infoStartY + ($infoSpacing * 3), function($font) use ($hasRobotoRegular, $robotoRegularPath, $valueSize) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size($valueSize);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // Montant du ticket
        $backgroundImage->text("Montant:", $infoX, $infoStartY + ($infoSpacing * 4), function($font) use ($hasRobotoBold, $robotoBoldPath, $hasRobotoRegular, $robotoRegularPath, $labelSize) {
            if ($hasRobotoBold) {
                $font->file($robotoBoldPath);
            } elseif ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size($labelSize);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // Valeur pour le montant (ticket_type)
        $backgroundImage->text($convention->amount . " FCFA", $valueX, $infoStartY + ($infoSpacing * 4), function($font) use ($hasRobotoRegular, $robotoRegularPath, $valueSize) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size($valueSize);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // Numéro du ticket (en bas à droite de la carte gauche)
        $backgroundImage->text("#" . substr($convention->id . $convention->unique_id, 0, 8), $panelMargin + $leftPanelWidth - $leftPanelPadding, $leftPanelY + $leftCardHeight - 20, function($font) use ($hasRobotoCondensed, $robotoCondensedPath) {
            if ($hasRobotoCondensed) {
                $font->file($robotoCondensedPath);
            } else {
                $font->file(5);
            }
            $font->size(14);
            $font->color('#ffffff'); // Texte blanc
            $font->align('right');
            $font->valign('bottom');
        });
        
        // Centrer horizontalement les QR codes dans la zone droite
        $qrXCenter = $rightPanelStart + ($qrPanelExactWidth / 2);
        $qrX = (int)($qrXCenter - ($qrWidth / 2));
        
        // Hauteur de chaque "bloc" QR code (QR + texte)
        $qrTextHeight = 35; // Espace pour le texte sous chaque QR (réduit pour rapprocher le texte du QR)
        $qrBlockHeight = $qrHeight + $qrTextHeight;
        
        // Espace total nécessaire pour les 3 blocs QR
        $totalQrBlocksHeight = $qrBlockHeight * 3;
        
        // Augmenter l'espace entre les QR codes
        $qrSpacing = 25; // Était calculé dynamiquement
        
        // Ajuster la position Y du premier QR code pour compenser l'augmentation de l'espacement
        $qrStartY = $rightPanelY + $qrPanelPadding + 10; // Ajout d'un décalage fixe de 10px
        
        // Premier QR code - Cérémonie d'ouverture
        $qrOpeningImage = base64_decode(preg_replace('/^data:image\/png;base64,/', '', $qrOpeningImage));
        $qrOpeningObj = Image::make($qrOpeningImage)->resize($qrWidth, $qrHeight);
        $backgroundImage->insert($qrOpeningObj, 'top-left', $qrX, $qrStartY);
        
        // Rapprocher la description du QR code
        $backgroundImage->text("Cérémonie d'ouverture", $qrXCenter, $qrStartY + $qrHeight + 7, function($font) use ($hasRobotoRegular, $robotoRegularPath) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size(13);
            $font->color('#ffffff'); // Texte blanc
            $font->align('center');
            $font->valign('top');
        });
        
        $backgroundImage->text("9 mai 2025 - 18h", $qrXCenter, $qrStartY + $qrHeight + 25, function($font) use ($hasRobotoRegular, $robotoRegularPath) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size(11);
            $font->color('#ffffff'); // Texte blanc
            $font->align('center');
            $font->valign('top');
        });
        
        // Deuxième QR code - AG avec espacement augmenté
        $qrY2 = $qrStartY + $qrBlockHeight + $qrSpacing;
        $qrAgImage = base64_decode(preg_replace('/^data:image\/png;base64,/', '', $qrAgImage));
        $qrAgObj = Image::make($qrAgImage)->resize($qrWidth, $qrHeight);
        $backgroundImage->insert($qrAgObj, 'top-left', $qrX, $qrY2);
        
        // Rapprocher la description du QR code
        $backgroundImage->text("AG", $qrXCenter, $qrY2 + $qrHeight + 7, function($font) use ($hasRobotoRegular, $robotoRegularPath) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size(13);
            $font->color('#ffffff'); // Texte blanc
            $font->align('center');
            $font->valign('top');
        });
        
        $backgroundImage->text("10 mai 2025 - 9h", $qrXCenter, $qrY2 + $qrHeight + 25, function($font) use ($hasRobotoRegular, $robotoRegularPath) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size(11);
            $font->color('#ffffff'); // Texte blanc
            $font->align('center');
            $font->valign('top');
        });
        
        // Troisième QR code - Dîner Gala avec espacement augmenté
        $qrY3 = $qrY2 + $qrBlockHeight + $qrSpacing;
        $qrGalaImage = base64_decode(preg_replace('/^data:image\/png;base64,/', '', $qrGalaImage));
        $qrGalaObj = Image::make($qrGalaImage)->resize($qrWidth, $qrHeight);
        $backgroundImage->insert($qrGalaObj, 'top-left', $qrX, $qrY3);
        
        // Rapprocher la description du QR code
        $backgroundImage->text("Dîner Gala", $qrXCenter, $qrY3 + $qrHeight + 7, function($font) use ($hasRobotoRegular, $robotoRegularPath) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size(13);
            $font->color('#ffffff'); // Texte blanc
            $font->align('center');
            $font->valign('top');
        });
        
        $backgroundImage->text("10 mai 2025 - 19h", $qrXCenter, $qrY3 + $qrHeight + 25, function($font) use ($hasRobotoRegular, $robotoRegularPath) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size(11);
            $font->color('#ffffff'); // Texte blanc
            $font->align('center');
            $font->valign('top');
        });
        
        // Ajouter le texte important en bas à gauche du ticket avec la première partie en gras
        // Définir les coordonnées pour le bas à gauche avec une marge
        $textMarginLeft = 30;
        
        // Première partie en gras
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
                $font->color('#ffffff'); // Texte blanc
                $font->align('left');
                $font->valign('center');
            }
        );
        
        // Deuxième partie en normal - sur la même ligne pour un affichage compact
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
                $font->color('#ffffff'); // Texte blanc
                $font->align('left');
                $font->valign('center');
            }
        );
        
        // Sauvegarder l'image dans storage (pour l'email)
        $storagePath = storage_path("app/public/tickets/ticket_{$convention->unique_id}.png");
        $backgroundImage->save($storagePath);
        
        // Sauvegarder également l'image dans le dossier public (pour l'API)
        $publicDirectory = public_path('tickets');
        // Créer le répertoire s'il n'existe pas
        if (!file_exists($publicDirectory)) {
            mkdir($publicDirectory, 0755, true);
        }
    
        $publicPath = $publicDirectory . "/ticket_{$convention->unique_id}.png";
        $backgroundImage->save($publicPath);
        
        // URL publique pour l'API
        $publicUrl = "https://czotick.com/public/tickets/ticket_{$convention->unique_id}.png";
        
        // Préparer les données pour l'API WhatsApp
        // Extraire les 8 derniers chiffres du numéro de téléphone
        $phone = $convention->phone;
        $lastEightDigits = substr($phone, -8);
        
        $whatsappData = [
            "mediaUrl" => $publicUrl, // URL de l'image accessible publiquement
            "chatId" => "225" . $lastEightDigits . "@c.us"
        ];
        
       
        Mail::send('emails.ticket', [
            'fullname' => $convention->fullname,
            'phone' => $convention->phone,
            'email' => $convention->email,
            'organization' => $convention->organization,
            'other_organization' => $convention->other_organization,
            'quality' => $convention->quality,
        ], function ($message) use ($convention, $storagePath) {
            $email = $convention->email;

            if (empty($email) || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                throw new Swift_RfcComplianceException("Invalid email address: $email");
            }

            $message->to($email)
                    ->cc('dematdigit.jcici@gmail.com')
                    //->cc('larissabrou6@gmail.com')
                    ->subject('Ticket - Conseil National 2025')
                    ->attach($storagePath);
        });

        $url = "https://waapi.app/api/v1/instances/62661/client/action/send-media";

        $headers = [
            "accept: application/json",
            "authorization: Bearer YG4kImDmDxZhcQbDupXMGzuL9QzZY2UXWLAHmnId58c2ecee",
            "content-type: application/json"
        ];

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($whatsappData));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $errorMessage = 'Erreur cURL : ' . curl_error($ch);
            file_put_contents(storage_path('logs/curl_errors.log'), '[' . date('Y-m-d H:i:s') . '] ' . $errorMessage . PHP_EOL, FILE_APPEND);
        } else {
            $successMessage = 'Réponse : ' . $response;
            file_put_contents(storage_path('logs/curl_responses.log'), '[' . date('Y-m-d H:i:s') . '] ' . $successMessage . PHP_EOL, FILE_APPEND);
        }

        curl_close($ch); 

    }

    public function manual(Request $request)
    {

        $convention = Convention::find(166);
            
        // Générer les URLs pour les 3 événements
        $openingUrl = route('cn.verify', ['data' => $convention->unique_id, 'event' => 'opening']);
        $agUrl = route('cn.verify', ['data' => $convention->unique_id, 'event' => 'ag']);
        $galaUrl = route('cn.verify', ['data' => $convention->unique_id, 'event' => 'gala']);

        // Créer les QR codes pour chaque événement
        $qrOpening = new QrCode($openingUrl);
        $qrAg = new QrCode($agUrl);
        $qrGala = new QrCode($galaUrl);
        
        $writer = new PngWriter();
        $qrOpeningImage = $writer->write($qrOpening)->getDataUri();
        $qrAgImage = $writer->write($qrAg)->getDataUri();
        $qrGalaImage = $writer->write($qrGala)->getDataUri();

        // Dimensions du ticket
        $ticketWidth = 800;
        $ticketHeight = 550;

        // Créer le ticket avec header-cn.png comme background
        $eventImagePath = storage_path('app/public/tickets/header-cn.png');
        if (file_exists($eventImagePath)) {
            // Utiliser directement l'image comme base
            $backgroundImage = Image::make($eventImagePath);
            
            // Redimensionner l'image pour atteindre les dimensions du ticket
            $backgroundImage->resize($ticketWidth, $ticketHeight, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize(); // Permet d'agrandir l'image si nécessaire
            });
            
            // Si l'image est plus petite que les dimensions du ticket, créer un canvas blanc et centrer l'image
            if ($backgroundImage->width() < $ticketWidth || $backgroundImage->height() < $ticketHeight) {
                $canvas = Image::canvas($ticketWidth, $ticketHeight, '#ffffff');
                $canvas->insert($backgroundImage, 'center');
                $backgroundImage = $canvas;
            }
            
            // Si l'image est plus grande que les dimensions du ticket, recadrer au centre
            if ($backgroundImage->width() > $ticketWidth || $backgroundImage->height() > $ticketHeight) {
                $backgroundImage->fit($ticketWidth, $ticketHeight, function ($constraint) {
                    $constraint->upsize();
                }, 'center');
            }
        } else {
            // Si l'image n'existe pas, créer un fond simple
            $backgroundImage = Image::canvas($ticketWidth, $ticketHeight, '#f5f5f5');
        }
        
        // Ajouter un overlay de couleur verte semi-transparent
        $overlay = Image::canvas($ticketWidth, $ticketHeight, '#025225'); // vert foncé
        $overlay->opacity(85); // 85% d'opacité (15% de transparence) - rgba(2, 82, 37, 0.85)
        $backgroundImage->insert($overlay, 'top-left', 0, 0);
        
        // Vérifier si les polices personnalisées existent
        $robotoRegularPath = storage_path('app/public/tickets/Roboto-Regular.ttf');
        $robotoBoldPath = storage_path('app/public/tickets/Roboto-Bold.ttf');
        $robotoCondensedPath = storage_path('app/public/tickets/Roboto_Condensed-Italic.ttf');
        
        // Utiliser des polices par défaut si les polices personnalisées ne sont pas disponibles
        $hasRobotoRegular = file_exists($robotoRegularPath);
        $hasRobotoBold = file_exists($robotoBoldPath);
        $hasRobotoCondensed = file_exists($robotoCondensedPath);
        
        // Configuration des QR codes
        $qrWidth = 80; // Taille des QR codes
        $qrHeight = 80;
        $qrPanelPadding = 20; // Padding pour le panneau QR
        
        // Calculer largeur exacte nécessaire pour la zone QR
        // Largeur max de texte sous QR (Cérémonie d'ouverture) = environ 160px
        $qrPanelExactWidth = max($qrWidth, 160) + ($qrPanelPadding * 2);
        
        // Configuration des zones
        $panelMargin = 20; // Marge autour des zones
        $leftPanelPadding = 30; // Padding interne de la zone gauche
        $panelHeight = 450; // Hauteur de la zone droite
        
        // Largeur de la zone gauche
        $leftPanelWidth = $ticketWidth - $panelMargin * 2 - $qrPanelExactWidth;
        
        // Espacement vertical pour les informations client
        $totalInfoLines = 5; // Nombre de lignes d'information (augmenté pour inclure le montant)
        $infoSpacing = 55; // Espacement entre les lignes d'information
        $totalInfoHeight = $infoSpacing * ($totalInfoLines - 1); // Hauteur totale des informations
        
        // Ajouter du padding autour du contenu pour la carte gauche
        $leftCardPaddingTop = 40;
        $leftCardPaddingBottom = 40;
        
        // Calculer la hauteur exacte nécessaire pour la carte gauche
        $leftCardHeight = $totalInfoHeight + $leftCardPaddingTop + $leftCardPaddingBottom;
        
        // Centrer verticalement les zones
        $rightPanelY = (int)(($ticketHeight - $panelHeight) / 2);
        $leftPanelY = (int)(($ticketHeight - $leftCardHeight) / 2);
        
        // Position de départ de la zone QR
        $rightPanelStart = $panelMargin + $leftPanelWidth;
        
        // Titre du ticket - centré en haut
        $backgroundImage->text("TICKET - CONSEIL NATIONAL 2025", $ticketWidth / 2, min($leftPanelY, $rightPanelY) - 15, function($font) use ($hasRobotoBold, $robotoBoldPath, $hasRobotoRegular, $robotoRegularPath) {
            if ($hasRobotoBold) {
                $font->file($robotoBoldPath);
            } elseif ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size(18);
            $font->color('#ffffff'); // Texte blanc
            $font->align('center');
            $font->valign('bottom');
        });
        
        // Couleur de fond des cartes - blanc semi-transparent (30% au lieu de 10%)
        $cardColor = 'rgba(255, 255, 255, 0.3)';
        $cardBorderColor = 'rgba(255, 255, 255, 0.4)'; // Bordure légèrement plus visible
        
        // Créer l'image de la première carte avec coins arrondis
        $leftCard = Image::canvas($leftPanelWidth, $leftCardHeight, $cardColor);
        
        // Créer l'image de la seconde carte avec coins arrondis
        $rightCard = Image::canvas($qrPanelExactWidth, $panelHeight, $cardColor);
        
        // Insérer les cartes sur l'image principale
        $backgroundImage->insert($leftCard, 'top-left', $panelMargin, $leftPanelY);
        $backgroundImage->insert($rightCard, 'top-left', $rightPanelStart, $rightPanelY);
        
        // Positions des informations client
        $infoX = $panelMargin + $leftPanelPadding;
        $valueX = $infoX + 180;
        
        // Calculer la position de départ des infos centrées dans la carte de gauche
        $infoStartY = $leftPanelY + $leftCardPaddingTop;
        
        // Augmentation de la taille des caractères pour les informations personnelles
        $labelSize = 18; // Était 16
        $valueSize = 18; // Était 16
        
        // Nom et Prénoms
        $backgroundImage->text("Nom et Prénoms:", $infoX, $infoStartY, function($font) use ($hasRobotoBold, $robotoBoldPath, $hasRobotoRegular, $robotoRegularPath, $labelSize) {
            if ($hasRobotoBold) {
                $font->file($robotoBoldPath);
            } elseif ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size($labelSize);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // Valeur pour Nom et Prénoms
        $backgroundImage->text($convention->fullname, $valueX, $infoStartY, function($font) use ($hasRobotoRegular, $robotoRegularPath, $valueSize) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size($valueSize);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // Qualité
        $backgroundImage->text("Qualité:", $infoX, $infoStartY + $infoSpacing, function($font) use ($hasRobotoBold, $robotoBoldPath, $hasRobotoRegular, $robotoRegularPath, $labelSize) {
            if ($hasRobotoBold) {
                $font->file($robotoBoldPath);
            } elseif ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size($labelSize);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // Valeur pour Qualité
        $backgroundImage->text($convention->quality, $valueX, $infoStartY + $infoSpacing, function($font) use ($hasRobotoRegular, $robotoRegularPath, $valueSize) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size($valueSize);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // OLM
        $backgroundImage->text("OLM/Autres:", $infoX, $infoStartY + ($infoSpacing * 2), function($font) use ($hasRobotoBold, $robotoBoldPath, $hasRobotoRegular, $robotoRegularPath, $labelSize) {
            if ($hasRobotoBold) {
                $font->file($robotoBoldPath);
            } elseif ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size($labelSize);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // Valeur pour OLM
        $olm = !empty($convention->other_organization) ? $convention->other_organization : $convention->organization;
        $backgroundImage->text($olm, $valueX, $infoStartY + ($infoSpacing * 2), function($font) use ($hasRobotoRegular, $robotoRegularPath, $valueSize) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size($valueSize);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // Numéro WhatsApp
        $backgroundImage->text("Numéro WhatsApp:", $infoX, $infoStartY + ($infoSpacing * 3), function($font) use ($hasRobotoBold, $robotoBoldPath, $hasRobotoRegular, $robotoRegularPath, $labelSize) {
            if ($hasRobotoBold) {
                $font->file($robotoBoldPath);
            } elseif ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size($labelSize);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // Valeur pour Numéro WhatsApp
        $backgroundImage->text($convention->phone, $valueX, $infoStartY + ($infoSpacing * 3), function($font) use ($hasRobotoRegular, $robotoRegularPath, $valueSize) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size($valueSize);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // Montant du ticket
        $backgroundImage->text("Montant:", $infoX, $infoStartY + ($infoSpacing * 4), function($font) use ($hasRobotoBold, $robotoBoldPath, $hasRobotoRegular, $robotoRegularPath, $labelSize) {
            if ($hasRobotoBold) {
                $font->file($robotoBoldPath);
            } elseif ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size($labelSize);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // Valeur pour le montant (ticket_type)
        $backgroundImage->text($convention->amount . " FCFA", $valueX, $infoStartY + ($infoSpacing * 4), function($font) use ($hasRobotoRegular, $robotoRegularPath, $valueSize) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size($valueSize);
            $font->color('#ffffff'); // Texte blanc
            $font->align('left');
            $font->valign('center');
        });
        
        // Numéro du ticket (en bas à droite de la carte gauche)
        $backgroundImage->text("#" . substr($convention->id . $convention->unique_id, 0, 8), $panelMargin + $leftPanelWidth - $leftPanelPadding, $leftPanelY + $leftCardHeight - 20, function($font) use ($hasRobotoCondensed, $robotoCondensedPath) {
            if ($hasRobotoCondensed) {
                $font->file($robotoCondensedPath);
            } else {
                $font->file(5);
            }
            $font->size(14);
            $font->color('#ffffff'); // Texte blanc
            $font->align('right');
            $font->valign('bottom');
        });
        
        // Centrer horizontalement les QR codes dans la zone droite
        $qrXCenter = $rightPanelStart + ($qrPanelExactWidth / 2);
        $qrX = (int)($qrXCenter - ($qrWidth / 2));
        
        // Hauteur de chaque "bloc" QR code (QR + texte)
        $qrTextHeight = 35; // Espace pour le texte sous chaque QR (réduit pour rapprocher le texte du QR)
        $qrBlockHeight = $qrHeight + $qrTextHeight;
        
        // Espace total nécessaire pour les 3 blocs QR
        $totalQrBlocksHeight = $qrBlockHeight * 3;
        
        // Augmenter l'espace entre les QR codes
        $qrSpacing = 25; // Était calculé dynamiquement
        
        // Ajuster la position Y du premier QR code pour compenser l'augmentation de l'espacement
        $qrStartY = $rightPanelY + $qrPanelPadding + 10; // Ajout d'un décalage fixe de 10px
        
        // Premier QR code - Cérémonie d'ouverture
        $qrOpeningImage = base64_decode(preg_replace('/^data:image\/png;base64,/', '', $qrOpeningImage));
        $qrOpeningObj = Image::make($qrOpeningImage)->resize($qrWidth, $qrHeight);
        $backgroundImage->insert($qrOpeningObj, 'top-left', $qrX, $qrStartY);
        
        // Rapprocher la description du QR code
        $backgroundImage->text("Cérémonie d'ouverture", $qrXCenter, $qrStartY + $qrHeight + 7, function($font) use ($hasRobotoRegular, $robotoRegularPath) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size(13);
            $font->color('#ffffff'); // Texte blanc
            $font->align('center');
            $font->valign('top');
        });
        
        $backgroundImage->text("9 mai 2025 - 18h", $qrXCenter, $qrStartY + $qrHeight + 25, function($font) use ($hasRobotoRegular, $robotoRegularPath) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size(11);
            $font->color('#ffffff'); // Texte blanc
            $font->align('center');
            $font->valign('top');
        });
        
        // Deuxième QR code - AG avec espacement augmenté
        $qrY2 = $qrStartY + $qrBlockHeight + $qrSpacing;
        $qrAgImage = base64_decode(preg_replace('/^data:image\/png;base64,/', '', $qrAgImage));
        $qrAgObj = Image::make($qrAgImage)->resize($qrWidth, $qrHeight);
        $backgroundImage->insert($qrAgObj, 'top-left', $qrX, $qrY2);
        
        // Rapprocher la description du QR code
        $backgroundImage->text("AG", $qrXCenter, $qrY2 + $qrHeight + 7, function($font) use ($hasRobotoRegular, $robotoRegularPath) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size(13);
            $font->color('#ffffff'); // Texte blanc
            $font->align('center');
            $font->valign('top');
        });
        
        $backgroundImage->text("10 mai 2025 - 9h", $qrXCenter, $qrY2 + $qrHeight + 25, function($font) use ($hasRobotoRegular, $robotoRegularPath) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size(11);
            $font->color('#ffffff'); // Texte blanc
            $font->align('center');
            $font->valign('top');
        });
        
        // Troisième QR code - Dîner Gala avec espacement augmenté
        $qrY3 = $qrY2 + $qrBlockHeight + $qrSpacing;
        $qrGalaImage = base64_decode(preg_replace('/^data:image\/png;base64,/', '', $qrGalaImage));
        $qrGalaObj = Image::make($qrGalaImage)->resize($qrWidth, $qrHeight);
        $backgroundImage->insert($qrGalaObj, 'top-left', $qrX, $qrY3);
        
        // Rapprocher la description du QR code
        $backgroundImage->text("Dîner Gala", $qrXCenter, $qrY3 + $qrHeight + 7, function($font) use ($hasRobotoRegular, $robotoRegularPath) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size(13);
            $font->color('#ffffff'); // Texte blanc
            $font->align('center');
            $font->valign('top');
        });
        
        $backgroundImage->text("10 mai 2025 - 19h", $qrXCenter, $qrY3 + $qrHeight + 25, function($font) use ($hasRobotoRegular, $robotoRegularPath) {
            if ($hasRobotoRegular) {
                $font->file($robotoRegularPath);
            } else {
                $font->file(5);
            }
            $font->size(11);
            $font->color('#ffffff'); // Texte blanc
            $font->align('center');
            $font->valign('top');
        });
        
        // Ajouter le texte important en bas à gauche du ticket avec la première partie en gras
        // Définir les coordonnées pour le bas à gauche avec une marge
        $textMarginLeft = 30;
        
        // Première partie en gras
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
                $font->color('#ffffff'); // Texte blanc
                $font->align('left');
                $font->valign('center');
            }
        );
        
        // Deuxième partie en normal - sur la même ligne pour un affichage compact
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
                $font->color('#ffffff'); // Texte blanc
                $font->align('left');
                $font->valign('center');
            }
        );
        
        // Sauvegarder l'image dans storage (pour l'email)
        $storagePath = storage_path("app/public/tickets/ticket_{$convention->unique_id}.png");
        $backgroundImage->save($storagePath);
        
        // Sauvegarder également l'image dans le dossier public (pour l'API)
        $publicDirectory = public_path('tickets');
        // Créer le répertoire s'il n'existe pas
        if (!file_exists($publicDirectory)) {
            mkdir($publicDirectory, 0755, true);
        }
    
        $publicPath = $publicDirectory . "/ticket_{$convention->unique_id}.png";
        $backgroundImage->save($publicPath);
        
        // URL publique pour l'API
        $publicUrl = "https://czotick.com/public/tickets/ticket_{$convention->unique_id}.png";
        
        // Préparer les données pour l'API WhatsApp
        // Extraire les 8 derniers chiffres du numéro de téléphone
        $phone = $convention->phone;
        $lastEightDigits = substr($phone, -8);
        
        $whatsappData = [
            "mediaUrl" => $publicUrl, // URL de l'image accessible publiquement
            "chatId" => "225" . $lastEightDigits . "@c.us"
        ];
        
       
        Mail::send('emails.ticket', [
            'fullname' => $convention->fullname,
            'phone' => $convention->phone,
            'email' => $convention->email,
            'organization' => $convention->organization,
            'other_organization' => $convention->other_organization,
            'quality' => $convention->quality,
        ], function ($message) use ($convention, $storagePath) {
            $email = $convention->email;

            if (empty($email) || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                throw new Swift_RfcComplianceException("Invalid email address: $email");
            }

            $message->to($email)
                    ->cc('dematdigit.jcici@gmail.com')
                    ->subject('Ticket - Conseil National 2025')
                    ->attach($storagePath);
        });

        $url = "https://waapi.app/api/v1/instances/62661/client/action/send-media";

        $headers = [
            "accept: application/json",
            "authorization: Bearer YG4kImDmDxZhcQbDupXMGzuL9QzZY2UXWLAHmnId58c2ecee",
            "content-type: application/json"
        ];

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($whatsappData));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $errorMessage = 'Erreur cURL : ' . curl_error($ch);
            file_put_contents(storage_path('logs/curl_errors.log'), '[' . date('Y-m-d H:i:s') . '] ' . $errorMessage . PHP_EOL, FILE_APPEND);
        } else {
            $successMessage = 'Réponse : ' . $response;
            file_put_contents(storage_path('logs/curl_responses.log'), '[' . date('Y-m-d H:i:s') . '] ' . $successMessage . PHP_EOL, FILE_APPEND);
        }

        curl_close($ch); 

    }
}
