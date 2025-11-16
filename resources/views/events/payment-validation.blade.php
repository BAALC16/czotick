<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}">
    <link rel="icon" href="{{ asset('assets/images/favicon-32x32.png') }}" sizes="32x32">
    <link rel="apple-touch-icon" href="{{ asset('assets/images/apple-touch-icon.png') }}">
    
    <title>Validation de paiement | {{ $currentEvent->event_title ?? 'Événement' }}</title>
    <style>
        :root {
            /* Couleurs dynamiques basées sur l'événement */
            --primary-color: {{ $currentEvent->primary_color ?? '#1d86d9' }};
            --secondary-color: {{ $currentEvent->secondary_color ?? '#28a745' }};
            --primary-dark: {{ ($currentEvent->primary_color ?? null) ? 'color-mix(in srgb, ' . $currentEvent->primary_color . ' 80%, black 20%)' : '#0056b3' }};
            --light-primary: {{ ($currentEvent->primary_color ?? null) ? 'color-mix(in srgb, ' . $currentEvent->primary_color . ' 10%, white 90%)' : '#f5f9ff' }};
            --text-dark: #333;
            --text-light: #666;
            --shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            --radius: 12px;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: var(--light-primary);
            color: var(--text-dark);
            line-height: 1.6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Background elements */
        .background-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.05;
            z-index: -1;
            background-size: cover;
        }
        
        .bg-shape-1 {
            position: absolute;
            top: -100px;
            right: -100px;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            opacity: 0.15;
            z-index: -1;
        }
        
        .bg-shape-2 {
            position: absolute;
            bottom: -150px;
            left: -150px;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            opacity: 0.1;
            z-index: -1;
        }
        
        /* Payment container */
        .payment-container {
            background-color: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 700px;
            overflow: hidden;
            position: relative;
        }
        
        .payment-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .payment-header h2 {
            font-weight: 600;
            font-size: 1.5rem;
            margin: 0;
        }
        
        .logos-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            margin: 25px auto;
        }
        
        .payment-logo {
            height: 80px;
            width: auto;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: white;
            padding: 10px;
        }
        
        .payment-body {
            padding: 25px;
        }
        
        .participant-info {
            background-color: var(--light-primary);
            border-radius: var(--radius);
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid var(--primary-color);
        }
        
        .participant-info h3 {
            color: var(--primary-color);
            margin-bottom: 10px;
            font-size: 1.1rem;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }
        
        .info-label {
            color: var(--text-light);
            font-weight: 500;
        }
        
        .info-value {
            color: var(--text-dark);
            font-weight: 600;
            text-align: right;
            flex: 1;
            margin-left: 10px;
        }
        
        .amount-display {
            background-color: rgba(255, 255, 255, 0.8);
            border: 2px solid var(--primary-color);
            border-radius: var(--radius);
            padding: 20px;
            margin-bottom: 25px;
            text-align: center;
        }
        
        .amount-label {
            font-size: 0.9rem;
            color: var(--text-light);
            margin-bottom: 8px;
        }
        
        .ticket-info {
            font-size: 0.9rem;
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .amount-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--secondary-color);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-dark);
        }
        
        .required-label:after {
            content: ' *';
            color: #e53935;
        }
        
        select,
        input[type="text"] {
            width: 100%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: var(--radius);
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        select:focus,
        input[type="text"]:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(29, 134, 217, 0.2);
            outline: none;
        }
        
        .submit-btn {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            border-radius: var(--radius);
            padding: 15px;
            font-size: 1rem;
            font-weight: 600;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        .submit-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }
        
        .submit-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .security-note {
            margin-top: 20px;
            font-size: 0.85rem;
            color: var(--text-light);
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .security-note svg {
            width: 16px;
            height: 16px;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 20px;
            transition: color 0.3s ease;
        }
        
        .back-link:hover {
            color: var(--primary-dark);
        }
        
        .error-message {
            background-color: #fff5f5;
            border: 1px solid #fed7d7;
            border-radius: var(--radius);
            padding: 15px;
            margin-bottom: 20px;
            color: #c53030;
            font-size: 0.9rem;
        }
        
        .payment-methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }
        
        .payment-method-card {
            border: 2px solid #e2e8f0;
            border-radius: var(--radius);
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }
        
        .payment-method-card:hover {
            border-color: var(--primary-color);
            background-color: var(--light-primary);
        }
        
        .payment-method-card.selected {
            border-color: var(--primary-color);
            background-color: var(--light-primary);
            box-shadow: 0 0 0 2px rgba(29, 134, 217, 0.2);
        }
        
        .payment-method-card input[type="radio"] {
            display: none;
        }
        
        .payment-method-icon {
            font-size: 1.5rem;
            margin-bottom: 8px;
            display: block;
        }
        
        .payment-method-name {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-dark);
        }
        
        /* Responsive */
        @media (max-width: 576px) {
            .payment-container {
                border-radius: 0;
                margin: 0;
            }
            
            .payment-header h2 {
                font-size: 1.3rem;
            }
            
            .amount-value {
                font-size: 1.5rem;
            }
            
            .payment-body {
                padding: 20px;
            }
            
            .logos-container {
                flex-direction: column;
                gap: 10px;
            }
            
            .payment-logo {
                height: 60px;
            }
            
            .info-row {
                flex-direction: column;
                gap: 2px;
            }
            
            .info-value {
                text-align: left;
                margin-left: 0;
            }
            
            .payment-methods {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <!-- Background elements -->
    <div class="background-pattern"></div>
    <div class="bg-shape-1"></div>
    <div class="bg-shape-2"></div>
    
    <div class="payment-container">
        <div class="payment-header">
            <h2>Validation du paiement</h2>
            <h3 style="font-weight: 400; opacity: 0.9; margin-top: 5px;">{{ $currentEvent->event_title }}</h3>
        </div>
        
        @if($currentOrganization->organization_logo || $currentEvent->event_image)
        <div class="logos-container">
            @if($currentOrganization->organization_logo)
            <img src="{{ url('public/' . $currentOrganization->organization_logo) }}" alt="Logo {{ $currentOrganization->org_name }}" class="payment-logo">
            @endif
            @if($currentEvent->event_image && $currentEvent->event_image !== $currentOrganization->organization_logo)
            <img src="{{ $currentEvent->event_image }}" alt="{{ $currentEvent->event_title }}" class="payment-logo">
            @endif
        </div>
        @endif
        
        <div class="payment-body">
            <a href="{{ route('event.registration', ['org_slug' => $currentOrganization->org_key, 'event_slug' => $currentEvent->event_slug]) }}" class="back-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m12 19-7-7 7-7"/>
                    <path d="M19 12H5"/>
                </svg>
                Retour au formulaire
            </a>
            
            <!-- Affichage des erreurs -->
            @if($errors->any())
            <div class="error-message">
                <strong>⚠️ Erreur :</strong>
                <ul style="margin: 5px 0 0 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <div class="participant-info">
                <h3>📋 Informations du participant</h3>
                <div class="info-row">
                    <span class="info-label">Nom & Prénoms :</span>
                    <span class="info-value">{{ $paymentData['fullname'] ?? $paymentData['full_name'] ?? 'Non spécifié' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email :</span>
                    <span class="info-value">{{ $paymentData['email'] ?? 'Non spécifié' }}</span>
                </div>
                @if(!empty($paymentData['phone']))
                <div class="info-row">
                    <span class="info-label">Téléphone :</span>
                    <span class="info-value">{{ $paymentData['phone'] }}</span>
                </div>
                @endif
                @if(!empty($paymentData['organization']))
                <div class="info-row">
                    <span class="info-label">Organisation :</span>
                    <span class="info-value">{{ $paymentData['organization'] }}</span>
                </div>
                @endif
                @if(!empty($paymentData['position']))
                <div class="info-row">
                    <span class="info-label">Fonction :</span>
                    <span class="info-value">{{ $paymentData['position'] }}</span>
                </div>
                @endif
            </div>
            
            <div class="amount-display">
                <div class="ticket-info">{{ $paymentData['ticket_name'] ?? 'Ticket' }}</div>
                <div class="amount-label">Montant à payer :</div>
                <div class="amount-value">{{ number_format($paymentData['ticket_price'] ?? 0, 0, ',', ' ') }} {{ $paymentData['currency'] ?? 'FCFA' }}</div>
            </div>

            <form action="{{ route('event.payment.validation.process', ['org_slug' => $currentOrganization->org_key, 'event_slug' => $currentEvent->event_slug]) }}" method="POST" id="paymentForm">
                @csrf
                
                <!-- Tous les champs cachés avec les données du formulaire -->
                @foreach($paymentData as $key => $value)
                    @if(is_string($value) || is_numeric($value))
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @elseif(is_array($value))
                        @foreach($value as $subKey => $subValue)
                            <input type="hidden" name="{{ $key }}[{{ $subKey }}]" value="{{ $subValue }}">
                        @endforeach
                    @endif
                @endforeach
                
             
                
                <div class="form-group">
                    <label for="payment_reference">Référence de paiement (optionnel)</label>
                    <input type="text" name="payment_reference" id="payment_reference" placeholder="Numéro de transaction, référence, etc.">
                </div>
                
                <button type="submit" class="submit-btn" id="submitBtn" disabled>Confirmer le paiement</button>
                
                <div class="security-note">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                    </svg>
                    Transaction sécurisée - Vos données sont protégées
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('paymentForm');
            const submitBtn = document.getElementById('submitBtn');
            const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
            
            // Fonction pour sélectionner une méthode de paiement
            window.selectPaymentMethod = function(method) {
                // Désélectionner toutes les cartes
                document.querySelectorAll('.payment-method-card').forEach(card => {
                    card.classList.remove('selected');
                });
                
                // Sélectionner la nouvelle carte
                const selectedCard = document.getElementById(method).closest('.payment-method-card');
                selectedCard.classList.add('selected');
                
                // Cocher le radio
                document.getElementById(method).checked = true;
                
                // Activer le bouton
                submitBtn.disabled = false;
            };
            
            // Gérer la soumission du formulaire
            form.addEventListener('submit', function(e) {
                const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
                
                if (!selectedMethod) {
                    e.preventDefault();
                    alert('Veuillez sélectionner une méthode de paiement');
                    return;
                }
                
                // Désactiver le bouton et changer le texte
                submitBtn.disabled = true;
                submitBtn.textContent = 'Traitement du paiement...';
                
                // Ajouter un spinner
                submitBtn.style.position = 'relative';
                submitBtn.innerHTML = `
                    <span style="opacity: 0.7;">Traitement du paiement...</span>
                    <span style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%);">⏳</span>
                `;
            });
            
            // Validation en temps réel des champs
            const inputs = form.querySelectorAll('input[type="text"]');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.style.borderColor = 'var(--primary-color)';
                });
                
                input.addEventListener('blur', function() {
                    if (!this.value && this.hasAttribute('required')) {
                        this.style.borderColor = '#dc3545';
                    } else {
                        this.style.borderColor = '#ddd';
                    }
                });
            });
            
            // Améliorer l'accessibilité
            document.querySelectorAll('.payment-method-card').forEach(card => {
                card.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        const radioId = this.querySelector('input').id;
                        selectPaymentMethod(radioId);
                    }
                });
                
                // Rendre les cartes focusables
                card.setAttribute('tabindex', '0');
                card.setAttribute('role', 'button');
                card.setAttribute('aria-label', 'Sélectionner ' + card.querySelector('.payment-method-name').textContent);
            });
        });
    </script>
</body>
</html>