<?php

/* var_dump($paymentData);
die; */
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}">
    <link rel="icon" href="{{ asset('assets/images/favicon-32x32.png') }}" sizes="32x32">
    <link rel="apple-touch-icon" href="{{ asset('assets/images/apple-touch-icon.png') }}">

    <title>Validation de paiement | {{ $currentEvent->event_title }}</title>
    <style>
        :root {
            /* Couleurs dynamiques basées sur l'événement */
            --primary-color: {{ $currentEvent->primary_color ?? '#1d86d9' }};
            --secondary-color: {{ $currentEvent->secondary_color ?? '#28a745' }};
            --primary-dark: {{ $currentEvent->primary_color ? 'color-mix(in srgb, ' . $currentEvent->primary_color . ' 80%, black 20%)' : '#0056b3' }};
            --light-primary: {{ $currentEvent->primary_color ? 'color-mix(in srgb, ' . $currentEvent->primary_color . ' 10%, white 90%)' : '#f5f9ff' }};
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

        /* Styles pour les méthodes de paiement avec images */
        .payment-methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 10px;
        }

        .payment-option {
            position: relative;
            display: block;
            cursor: pointer;
        }

        .payment-option.disabled {
            cursor: not-allowed;
        }

        .payment-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            margin: 0;
            cursor: pointer;
        }

        .payment-option.disabled input[type="radio"] {
            cursor: not-allowed;
        }

        .payment-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            border: 2px solid #e0e0e0;
            border-radius: var(--radius);
            background-color: white;
            transition: all 0.3s ease;
            text-align: center;
            min-height: 120px;
            justify-content: center;
            position: relative;
        }

        .payment-option:hover .payment-card {
            border-color: var(--primary-color);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }

        .payment-option.disabled:hover .payment-card {
            border-color: #e0e0e0;
            box-shadow: none;
            transform: none;
        }

        .payment-option input[type="radio"]:checked + .payment-card {
            border-color: var(--primary-color);
            background-color: var(--light-primary);
            box-shadow: 0 4px 12px rgba(29, 134, 217, 0.2);
        }

        .payment-option.disabled .payment-card {
            background-color: #f8f9fa;
            opacity: 0.5;
        }

        .payment-option.disabled .payment-card::after {
            content: "Bientôt disponible";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.7rem;
            white-space: nowrap;
        }

        .payment-icon {
            width: 50px;
            height: 50px;
            object-fit: contain;
            margin-bottom: 8px;
            border-radius: 4px;
        }

        .payment-name {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-dark);
        }

        /* Style pour le champ téléphone avec indicatif */
        .phone-input-container {
            display: flex;
            border: 1px solid #ddd;
            border-radius: var(--radius);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .phone-input-container:focus-within {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(29, 134, 217, 0.2);
        }

        .country-code {
            background-color: #f8f9fa;
            padding: 15px 12px;
            border-right: 1px solid #ddd;
            font-weight: 600;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            min-width: 70px;
            justify-content: center;
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

        input[type="tel"] {
            flex: 1;
            padding: 15px;
            border: none;
            font-size: 1rem;
            outline: none;
        }

        select:focus,
        input[type="text"]:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(29, 134, 217, 0.2);
            outline: none;
        }

        .input-help {
            font-size: 0.8rem;
            color: var(--text-light);
            margin-top: 5px;
        }

        .phone-input-container.invalid {
            border-color: #e53935;
        }

        .error-message {
            color: #e53935;
            font-size: 0.8rem;
            margin-top: 5px;
            display: none;
        }

        /* Style pour le champ OTP */
        .otp-field {
            display: none;
            margin-top: 15px;
        }

        .otp-field.show {
            display: block;
        }

        .otp-input {
            width: 100%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: var(--radius);
            font-size: 1rem;
            text-align: center;
            letter-spacing: 2px;
            transition: all 0.3s ease;
        }

        .otp-input:focus {
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

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }

        .submit-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* Loading overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .loading-content {
            background: white;
            padding: 30px;
            border-radius: var(--radius);
            text-align: center;
            max-width: 400px;
            margin: 20px;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loading-text {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 10px;
        }

        .loading-instruction {
            font-size: 0.9rem;
            color: var(--text-light);
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

        /* Error message styles */
        .error-alert {
            background-color: #fee;
            border: 1px solid #fcc;
            color: #a00;
            padding: 10px;
            border-radius: var(--radius);
            margin-bottom: 20px;
            display: none;
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

            .payment-methods {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }

            .payment-card {
                padding: 15px;
                min-height: 100px;
            }

            .payment-icon {
                width: 40px;
                height: 40px;
            }

            .payment-name {
                font-size: 0.8rem;
            }

            .country-code {
                padding: 15px 8px;
                min-width: 60px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <!-- Background elements -->
    <div class="background-pattern"></div>
    <div class="bg-shape-1"></div>
    <div class="bg-shape-2"></div>

    <!-- Loading overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <div class="loading-text" id="loadingText">Traitement du paiement...</div>
            <div class="loading-instruction" id="loadingInstruction"></div>
        </div>
    </div>

    <div class="payment-container">
        <div class="payment-header">
            <h2>Validation du paiement</h2>
            <h3 style="font-weight: 400; opacity: 0.9; margin-top: 5px;">{{ $currentEvent->event_title }}</h3>
        </div>

        @if($currentOrganization->organization_logo)
        <div class="logos-container">
            @if($currentOrganization->organization_logo)
            <img src="{{ url('public/' . $currentOrganization->organization_logo) }}" alt="Logo {{ $currentOrganization->org_name }}" class="payment-logo">
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

            <!-- Error message container -->
            <div class="error-alert" id="errorAlert"></div>

            <div class="participant-info">
                <h3>📋 Informations du participant</h3>
                <div class="info-row">
                    <span class="info-label">Nom & Prénoms :</span>
                    <span class="info-value">{{ $paymentData['fullname'] }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email :</span>
                    <span class="info-value">{{ $paymentData['email'] }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Téléphone :</span>
                    <span class="info-value">{{ $paymentData['phone'] }}</span>
                </div>
                @if(isset($paymentData['organization']))
                <div class="info-row">
                    <span class="info-label">Organisation :</span>
                    <span class="info-value">{{ $paymentData['organization'] }}</span>
                </div>
                @endif
            </div>

            <div class="amount-display">
                <div class="ticket-info">{{ $paymentData['ticket_name'] }}</div>
                @if(isset($paymentData['is_partial_completion']) && $paymentData['is_partial_completion'] && isset($paymentData['balance_due']))
                    <!-- Affichage pour finalisation de paiement partiel -->
                    <div style="background: #fff3cd; border: 2px solid #ffc107; border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span style="color: #856404; font-weight: 600;">Montant total :</span>
                            <span style="color: #856404; font-weight: 700;">{{ number_format($paymentData['ticket_price'], 0, ',', ' ') }} {{ $paymentData['currency'] ?? 'FCFA' }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span style="color: #856404; font-weight: 600;">Déjà payé :</span>
                            <span style="color: #28a745; font-weight: 700;">{{ number_format($paymentData['amount_paid'], 0, ',', ' ') }} {{ $paymentData['currency'] ?? 'FCFA' }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding-top: 0.5rem; border-top: 2px solid #ffc107;">
                            <span style="color: #856404; font-weight: 700; font-size: 1.1rem;">Solde restant :</span>
                            <span style="color: #dc3545; font-weight: 700; font-size: 1.2rem;">{{ number_format($paymentData['balance_due'], 0, ',', ' ') }} {{ $paymentData['currency'] ?? 'FCFA' }}</span>
                        </div>
                    </div>
                    <div class="amount-label">Montant à payer :</div>
                    <div class="amount-value">{{ number_format($paymentData['balance_due'], 0, ',', ' ') }} {{ $paymentData['currency'] ?? 'FCFA' }}</div>
                @else
                    <!-- Affichage normal pour nouveau paiement -->
                    <div class="amount-label">Montant à payer :</div>
                    @php
                        // Déterminer le montant selon l'URL et le type de ticket
                        $currentUrl = request()->fullUrl();
                        $isRiaEvent = str_contains($currentUrl, 'jci-abidjan-ivoire/ria-2025/validation-paiement');

                        if ($isRiaEvent) {
                            $amount = ($paymentData['ticket_name'] ?? '') === 'Membre' ? 60000 : 75000;
                        } else {
                            $amount = $paymentData['ticket_price'] ?? 20500;
                        }
                    @endphp
                    <div class="amount-value">{{ number_format($amount, 0, ',', ' ') }} {{ $paymentData['currency'] ?? 'FCFA' }}</div>
                @endif
            </div>

            <form id="paymentForm">
                @csrf

                <!-- Tous les champs cachés avec les données du formulaire -->
                @foreach($paymentData as $key => $value)
                    @if($key === 'ticket_price' && isset($paymentData['is_partial_completion']) && $paymentData['is_partial_completion'] && isset($paymentData['balance_due']))
                        <!-- Pour paiement partiel, utiliser balance_due comme montant à payer -->
                        <input type="hidden" name="ticket_price" value="{{ $paymentData['balance_due'] }}">
                        <input type="hidden" name="original_ticket_price" value="{{ $value }}">
                    @else
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach

                <!-- Code apporteur d'affaire depuis l'URL -->
                @if(isset($referrerCode) && $referrerCode)
                    <input type="hidden" name="referrer_code" value="{{ $referrerCode }}">
                @endif

                @php
                    // Extraire le code pays depuis les données de paiement avec debug
                    $phoneCountry = $paymentData['phone_country'] ?? $paymentData['phone_local'] ?? '+225';

                    // Debug pour vérifier les données reçues
                    \Log::info('Données de paiement phone:', [
                        'phone_country' => $paymentData['phone_country'] ?? 'non défini',
                        'phone' => $paymentData['phone'] ?? 'non défini',
                        'phone_local' => $paymentData['phone_local'] ?? 'non défini'
                    ]);

                    // Si le code pays n'est pas complet, essayer d'extraire du numéro complet
                    if (isset($paymentData['phone']) && str_starts_with($paymentData['phone'], '+')) {
                        preg_match('/^(\+\d{3})/', $paymentData['phone'], $matches);
                        if (isset($matches[1])) {
                            $phoneCountry = $matches[1];
                        }
                    }

                    // Correction pour les codes pays incomplets
                    if ($phoneCountry === '+229' || str_starts_with($phoneCountry, '+229')) {
                        $phoneCountry = '+229';
                    } elseif ($phoneCountry === '+226' || str_starts_with($phoneCountry, '+226')) {
                        $phoneCountry = '+226';
                    } elseif ($phoneCountry === '+223' || str_starts_with($phoneCountry, '+223')) {
                        $phoneCountry = '+223';
                    } elseif ($phoneCountry === '+228' || str_starts_with($phoneCountry, '+228')) {
                        $phoneCountry = '+228';
                    } elseif (!in_array($phoneCountry, ['+225', '+229', '+226', '+223', '+228'])) {
                        $phoneCountry = '+225'; // Valeur par défaut
                    }

                    // Configuration des opérateurs par pays
                    $operators = [
                        '+225' => ['orange', 'mtn', 'moov', 'wave'], // Côte d'Ivoire
                        '+229' => ['mtn', 'moov'], // Bénin
                        '+226' => ['orange', 'moov'], // Burkina Faso
                        '+223' => ['orange', 'moov'], // Mali
                        '+228' => ['moov'] // Togo
                    ];

                    $availableOperators = $operators[$phoneCountry] ?? $operators['+225'];

                    \Log::info('Code pays final détecté:', [
                        'phoneCountry' => $phoneCountry,
                        'availableOperators' => $availableOperators
                    ]);
                @endphp

                <div class="form-group">
                    <label class="required-label">Méthode de paiement</label>
                    <div class="payment-methods">
                        @if(in_array('orange', $availableOperators))
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="orange">
                            <div class="payment-card">
                                <img src="{{ url('public/assets/images/orange-money.png') }}" alt="Orange Money" class="payment-icon">
                                <span class="payment-name">Orange Money</span>
                            </div>
                        </label>
                        @endif

                        @if(in_array('mtn', $availableOperators))
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="mtn">
                            <div class="payment-card">
                                <img src="{{ url('public/assets/images/mtn-money.png') }}" alt="MTN Money" class="payment-icon">
                                <span class="payment-name">MTN Money</span>
                            </div>
                        </label>
                        @endif

                        @if(in_array('moov', $availableOperators))
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="moov">
                            <div class="payment-card">
                                <img src="{{ url('public/assets/images/moov-money.png') }}" alt="Moov Money" class="payment-icon">
                                <span class="payment-name">Moov Money</span>
                            </div>
                        </label>
                        @endif

                        @if(in_array('wave', $availableOperators))
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="wave">
                            <div class="payment-card">
                                <img src="{{ url('public/assets/images/wave.png') }}" alt="Wave" class="payment-icon">
                                <span class="payment-name">Wave</span>
                            </div>
                        </label>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone_number" class="required-label">Numéro de téléphone à débiter</label>
                    <div class="phone-input-container" id="phoneContainer">
                        <div class="country-code">{{ $phoneCountry }}</div>
                        <input type="tel"
                               id="phone_number"
                               name="phone_number"
                               placeholder="{{ $phoneCountry === '+225' ? '0123456789' : 'Numéro de téléphone' }}"
                               required>
                    </div>
                    <div class="input-help">{{ $phoneCountry === '+225' ? 'Saisissez exactement 10 chiffres' : 'Saisissez votre numéro de téléphone' }}</div>

                    <!-- Champ OTP pour Orange Money -->
                    <div class="otp-field" id="otpField">
                        <label for="otp_code" class="required-label">Code OTP</label>
                        <input type="text"
                               id="otp_code"
                               name="otp_code"
                               class="otp-input"
                               pattern="[0-9]*"
                               maxlength="6">
                        <div class="input-help" id="otpHelp">
                            {{ $phoneCountry === '+225' ? 'Composez #144*82# pour obtenir un code otp' : 'Générer un OTP Orange Money' }}
                        </div>
                    </div>
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
            const phoneInput = document.getElementById('phone_number');
            const phoneContainer = document.getElementById('phoneContainer');
            const errorAlert = document.getElementById('errorAlert');
            const otpField = document.getElementById('otpField');
            const otpInput = document.getElementById('otp_code');
            const loadingOverlay = document.getElementById('loadingOverlay');
            const loadingText = document.getElementById('loadingText');
            const loadingInstruction = document.getElementById('loadingInstruction');

            // Récupérer le code pays depuis PHP avec validation
            let phoneCountry = @json($phoneCountry);

            // Correction pour les codes pays mal formatés
            if (phoneCountry === '+229' || phoneCountry.startsWith('+229')) {
                phoneCountry = '+229';
            } else if (phoneCountry === '+226' || phoneCountry.startsWith('+226')) {
                phoneCountry = '+226';
            } else if (phoneCountry === '+223' || phoneCountry.startsWith('+223')) {
                phoneCountry = '+223';
            } else if (phoneCountry === '+228' || phoneCountry.startsWith('+228')) {
                phoneCountry = '+228';
            } else if (!phoneCountry || !phoneCountry.startsWith('+')) {
                phoneCountry = '+225'; // Valeur par défaut
            }

            console.log('🌍 Code pays détecté et corrigé:', phoneCountry);

            const isIvoryCoast = phoneCountry === '+225';

            // Mettre à jour l'affichage du code pays dans l'interface
            const countryCodeDisplay = document.querySelector('.country-code');
            if (countryCodeDisplay) {
                countryCodeDisplay.textContent = phoneCountry;
            }

            let isSubmitting = false;
            let selectedPaymentMethod = null;
            let activeCheckingInterval = null;

            // Configuration des timeouts par méthode de paiement
            const PAYMENT_TIMEOUTS = {
                'orange': { maxAttempts: 20, interval: 2000 },  // Orange : 40s
                'mtn': { maxAttempts: 40, interval: 3000 },     // MTN : 2 minutes
                'moov': { maxAttempts: 40, interval: 3000 },    // Moov : 2 minutes
                'wave': { maxAttempts: 30, interval: 5000 }     // Wave : 2.5 minutes
            };

            function collectFormData() {
                const formData = new FormData();

                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                formData.append('_token', csrfToken);

                const hiddenInputs = form.querySelectorAll('input[type="hidden"]');
                hiddenInputs.forEach(input => {
                    if (input.name && input.value) {
                        formData.append(input.name, input.value);
                    }
                });

                const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
                if (selectedMethod) {
                    formData.append('payment_method', selectedMethod.value);
                }

                const phoneValue = phoneInput.value;
                if (phoneValue) {
                    formData.append('phone_number', phoneValue);
                    formData.append('full_phone_number', phoneCountry + phoneValue);
                }

                const otpValue = otpInput.value;
                if (otpValue && selectedMethod && selectedMethod.value === 'orange') {
                    formData.append('otp_code', otpValue);
                }

                console.log("formdata",formData);
                return formData;
            }

            // FONCTION PRINCIPALE: Vérification de statut adaptée par méthode
            function checkPaymentStatus(transactionReference, paymentMethod) {
                const config = PAYMENT_TIMEOUTS[paymentMethod] || PAYMENT_TIMEOUTS['mtn'];
                let attempts = 0;

                if (activeCheckingInterval) {
                    clearInterval(activeCheckingInterval);
                }

                console.log(`🔍 Début vérification ${paymentMethod.toUpperCase()}:`, transactionReference);
                console.log(`⚙️ Config: ${config.maxAttempts} tentatives, intervalle ${config.interval}ms`);

                activeCheckingInterval = setInterval(async () => {
                    attempts++;
                    console.log(`📊 ${paymentMethod.toUpperCase()} - Tentative ${attempts}/${config.maxAttempts}`);

                    if (attempts >= config.maxAttempts) {
                        console.log(`⏰ Délai maximum atteint pour ${paymentMethod.toUpperCase()}`);
                        clearInterval(activeCheckingInterval);
                        hideLoading();

                        let timeoutMessage = getTimeoutMessage(paymentMethod);
                        showError(timeoutMessage);

                        isSubmitting = false;
                        submitBtn.disabled = false;
                        checkFormValidity();
                        return;
                    }

                    try {
                        // Construire l'URL de vérification
                        const statusUrl = buildStatusCheckUrl();
                        console.log(`📡 URL vérification: ${statusUrl}`);

                        const response = await fetch(statusUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                transaction_reference: transactionReference
                            })
                        });

                        if (!response.ok) {
                            console.warn(`⚠️ ${paymentMethod.toUpperCase()} - HTTP ${response.status}: ${response.statusText}`);

                            // Continuer pour les erreurs temporaires
                            if (response.status === 404 || response.status >= 500) {
                                return;
                            }

                            throw new Error(`Erreur HTTP: ${response.status}`);
                        }

                        const result = await response.json();
                        console.log(`📨 ${paymentMethod.toUpperCase()} - Réponse:`, result);

                        // Traitement des différents statuts
                        if (isSuccessStatus(result.status)) {
                            handlePaymentSuccess(result, paymentMethod, transactionReference);
                        } else if (isFailureStatus(result.status)) {
                            handlePaymentFailure(result, paymentMethod);
                        } else if (isPendingStatus(result.status)) {
                            handlePaymentPending(result, paymentMethod, attempts, config.maxAttempts);
                        } else {
                            console.warn(`⚠️ Statut inconnu pour ${paymentMethod.toUpperCase()}:`, result.status);
                            updateLoadingInstruction(result.message || `Vérification en cours (${attempts}/${config.maxAttempts})...`);
                        }

                    } catch (error) {
                        console.error(`🚨 Erreur vérification ${paymentMethod.toUpperCase()}:`, error);

                        // Ne pas arrêter le polling pour les erreurs réseau
                        if (attempts >= config.maxAttempts - 5) {
                            updateLoadingInstruction(`Connexion instable - Tentative ${attempts}/${config.maxAttempts}`);
                        }
                    }
                }, config.interval);
            }

            // Fonctions utilitaires pour les statuts
            function isSuccessStatus(status) {
                const successStatuses = ['completed', 'success', 'successful', 'SUCCESS', 'paid', 'validated'];
                return successStatuses.includes(status);
            }

            function isFailureStatus(status) {
                const failureStatuses = ['failed', 'failure', 'error', 'cancelled', 'canceled', 'rejected', 'declined'];
                return failureStatuses.includes(status);
            }

            function isPendingStatus(status) {
                const pendingStatuses = ['pending', 'processing', 'in_progress', 'waiting', 'initiated', 'PENDING'];
                return pendingStatuses.includes(status);
            }

            function handlePaymentSuccess(result, paymentMethod, transactionReference) {
                console.log(`✅ ${paymentMethod.toUpperCase()} - SUCCÈS!`);
                clearInterval(activeCheckingInterval);
                hideLoading();

                if (result.redirect_url) {
                    console.log(`🔗 Redirection vers: ${result.redirect_url}`);
                    window.location.href = result.redirect_url;
                } else {
                    showError(`✅ Paiement ${paymentMethod.toUpperCase()} confirmé avec succès ! Rechargement...`);
                    setTimeout(() => window.location.reload(), 2000);
                }
            }

            function handlePaymentFailure(result, paymentMethod) {
                console.log(`❌ ${paymentMethod.toUpperCase()} - ÉCHEC`);
                clearInterval(activeCheckingInterval);
                hideLoading();

                const failureMessage = result.message || `Le paiement ${paymentMethod.toUpperCase()} a échoué. Veuillez réessayer.`;
                showError(failureMessage);

                isSubmitting = false;
                submitBtn.disabled = false;
                checkFormValidity();
            }

            function handlePaymentPending(result, paymentMethod, attempts, maxAttempts) {
                console.log(`⏳ ${paymentMethod.toUpperCase()} - EN COURS (${attempts}/${maxAttempts})`);

                let message = result.message;
                if (!message) {
                    message = getDefaultPendingMessage(paymentMethod, attempts, maxAttempts);
                }

                updateLoadingInstruction(message);
            }

            function getDefaultPendingMessage(paymentMethod, attempts, maxAttempts) {
                const progress = Math.round((attempts / maxAttempts) * 100);

                if (isIvoryCoast) {
                    // Messages pour la Côte d'Ivoire
                    switch (paymentMethod) {
                        case 'mtn':
                            return `Paiement MTN en cours... (${progress}%) - Composer *133# puis l'option 1 pour valider`;
                        case 'moov':
                            return `Paiement Moov en cours... (${progress}%) - Composer *155*15# pour valider`;
                        case 'orange':
                            return `Vérification Orange Money... (${progress}%)`;
                        case 'wave':
                            return `Traitement Wave en cours... (${progress}%)`;
                        default:
                            return `Vérification en cours... (${progress}%)`;
                    }
                } else {
                    // Messages pour les autres pays
                    switch (paymentMethod) {
                        case 'mtn':
                            return `Paiement MTN en cours... (${progress}%) - Veuillez valider votre transaction`;
                        case 'moov':
                            return `Paiement Moov en cours... (${progress}%) - Veuillez valider votre transaction`;
                        case 'orange':
                            return `Vérification Orange Money... (${progress}%)`;
                        default:
                            return `Vérification en cours... (${progress}%)`;
                    }
                }
            }

            function getTimeoutMessage(paymentMethod) {
                switch (paymentMethod) {
                    case 'mtn':
                        return '⏰ Délai MTN dépassé, veuillez réessayer.';
                    case 'moov':
                        return '⏰ Délai Moov dépassé, veuillez réessayer.';
                    case 'orange':
                        return '⏰ Délai Orange Money dépassé.';
                    case 'wave':
                        return '⏰ Délai Wave dépassé, veuillez réessayer.';
                    default:
                        return '';
                }
            }

            function buildStatusCheckUrl() {
                // Essayer d'abord avec le préfixe validation-paiement
                let statusUrl = window.location.pathname.replace('/validation-paiement', '/validation-paiement/payment-status');

                // Fallback si pas de préfixe
                if (!statusUrl.includes('payment-status')) {
                    statusUrl = window.location.pathname.replace('/paiement', '/payment-status');
                }

                return window.location.origin + statusUrl;
            }

            function updateLoadingInstruction(message) {
                if (loadingInstruction) {
                    loadingInstruction.textContent = message;
                }
            }

            // GESTION PRINCIPALE DE SOUMISSION
            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                if (isSubmitting) {
                    console.log('⚠️ Soumission déjà en cours');
                    return;
                }

                const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
                const phoneValue = phoneInput.value.trim();
                const otpValue = otpInput.value.trim();

                // Validation côté client
                if (!selectedMethod) {
                    showError('❌ Veuillez sélectionner une méthode de paiement');
                    return;
                }

                if (!phoneValue) {
                    showError('❌ Veuillez saisir un numéro de téléphone');
                    phoneInput.focus();
                    return;
                }

                // Validation spécifique pour la Côte d'Ivoire (10 chiffres exactement)
                if (isIvoryCoast && (phoneValue.length !== 10 || !/^\d{10}$/.test(phoneValue))) {
                    showError('❌ Veuillez saisir un numéro de téléphone valide (10 chiffres)');
                    phoneInput.focus();
                    return;
                }

                // Validation générale pour les autres pays (au moins 8 chiffres)
                if (!isIvoryCoast && (phoneValue.length < 8 || !/^\d+$/.test(phoneValue))) {
                    showError('❌ Veuillez saisir un numéro de téléphone valide');
                    phoneInput.focus();
                    return;
                }

                if (selectedMethod.value === 'orange' && (!otpValue)) {
                    showError('❌ Veuillez saisir un code OTP valide');
                    otpInput.focus();
                    return;
                }

                isSubmitting = true;
                submitBtn.disabled = true;
                hideError();
                showLoading(selectedMethod.value);

                try {
                    console.log(`🚀 Démarrage paiement ${selectedMethod.value.toUpperCase()}`);
                    const formData = collectFormData();

                    // Construction URL d'action
                    const actionUrl = buildActionUrl(selectedMethod.value);
                    console.log(`🎯 URL action: ${actionUrl}`);

                    const response = await fetch(actionUrl, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    });

                    console.log(`📡 ${selectedMethod.value.toUpperCase()} - Réponse HTTP: ${response.status} ${response.statusText}`);

                    // Accepter le statut 202
                    if (!response.ok && response.status !== 202) {
                        const errorText = await response.text();
                        console.error(`📄 Erreur ${selectedMethod.value.toUpperCase()}:`, errorText);

                        if (response.status === 422) {
                            try {
                                const errorData = JSON.parse(errorText);
                                if (errorData.errors) {
                                    const errorMessages = Object.values(errorData.errors).flat();
                                    throw new Error(`Données invalides: ${errorMessages.join(', ')}`);
                                }
                            } catch (parseError) {
                                throw new Error('Données du formulaire invalides. Veuillez vérifier vos informations.');
                            }
                        }

                        throw new Error(`Erreur HTTP ${response.status}: ${response.statusText}`);
                    }

                    const result = await response.json();
                    console.log(`✅ ${selectedMethod.value.toUpperCase()} - Réponse serveur:`, result);

                    // Traitement selon le statut de réponse
                    if (result.status === 'success') {
                        handleInitialSuccess(result, selectedMethod.value);
                    } else if (result.status === 'error') {
                        throw new Error(result.message || `Erreur ${selectedMethod.value.toUpperCase()}`);
                    } else {
                        console.warn(`⚠️ Statut réponse inattendu pour ${selectedMethod.value.toUpperCase()}:`, result.status);
                        throw new Error(result.message || 'Réponse inattendue du serveur');
                    }

                } catch (error) {
                    console.error(`💥 Erreur ${selectedMethod.value.toUpperCase()}:`, error);
                    handleSubmissionError(error, selectedMethod.value);
                }
            });

            function buildActionUrl(paymentMethod) {
                // Extraire org_slug et event_slug depuis l'URL actuelle
                const pathSegments = window.location.pathname.split('/').filter(segment => segment);
                
                // Vérifier si le chemin contient 'czotick'
                const hasCzotick = pathSegments.includes('czotick');
                
                // Trouver les indices de org_slug et event_slug
                let orgIndex, eventIndex;
                
                if (hasCzotick) {
                    // Format: /czotick/{org_slug}/{event_slug}/...
                    const czotickIndex = pathSegments.indexOf('czotick');
                    orgIndex = czotickIndex + 1;
                    eventIndex = czotickIndex + 2;
                } else {
                    // Format: /{org_slug}/{event_slug}/...
                    orgIndex = 0;
                    eventIndex = 1;
                }
                
                // Si on est sur /finaliser-paiement/{id}, on doit remonter
                if (pathSegments.includes('finaliser-paiement')) {
                    const finaliserIndex = pathSegments.indexOf('finaliser-paiement');
                    if (hasCzotick) {
                        orgIndex = finaliserIndex - 2;
                        eventIndex = finaliserIndex - 1;
                    } else {
                        orgIndex = finaliserIndex - 2;
                        eventIndex = finaliserIndex - 1;
                    }
                }
                
                const orgSlug = pathSegments[orgIndex] || '{{ $currentOrganization->org_key }}';
                const eventSlug = pathSegments[eventIndex] || '{{ $currentEvent->event_slug }}';
                
                const methodPaths = {
                    'orange': '/validation-paiement/orange-money/process',
                    'mtn': '/validation-paiement/mtn-money/process',
                    'moov': '/validation-paiement/moov-money/process',
                    'wave': '/validation-paiement/wave-process'
                };
                
                // Construire l'URL correcte avec le préfixe czotick si nécessaire
                const basePath = hasCzotick 
                    ? '/czotick/' + orgSlug + '/' + eventSlug
                    : '/' + orgSlug + '/' + eventSlug;
                const actionUrl = basePath + (methodPaths[paymentMethod] || methodPaths['wave']);
                
                return window.location.origin + actionUrl;
            }

            function handleInitialSuccess(result, paymentMethod) {
                if (paymentMethod === 'wave' && result.url) {
                    console.log(`🌊 Redirection Wave: ${result.url}`);
                    window.location.href = result.url;
                } else if (['mtn', 'moov'].includes(paymentMethod)) {
                    console.log(`📱 Démarrage vérification ${paymentMethod.toUpperCase()}`);
                    const transactionRef = result.transaction_reference;
                    if (transactionRef) {
                        checkPaymentStatus(transactionRef, paymentMethod);
                    } else {
                        throw new Error('Référence de transaction manquante');
                    }
                } else if (paymentMethod === 'orange') {
                    if (result.transaction_reference && !result.redirect_url) {
                        console.log('🍊 Orange - Démarrage vérification');
                        checkPaymentStatus(result.transaction_reference, paymentMethod);
                    } else {
                        hideLoading();
                        if (result.redirect_url) {
                            console.log(`🍊 Redirection Orange: ${result.redirect_url}`);
                            window.location.href = result.redirect_url;
                        } else {
                            showError(result.message || '✅ Paiement Orange traité avec succès');
                            setTimeout(() => window.location.reload(), 2000);
                        }
                    }
                }
            }

            function handleSubmissionError(error, paymentMethod) {
                hideLoading();

                if (activeCheckingInterval) {
                    clearInterval(activeCheckingInterval);
                }

                let errorMessage = error.message || 'Une erreur est survenue.';

                if (error.message.includes('Données invalides')) {
                    errorMessage = '❌ ' + error.message;
                } else if (error.message.includes('422')) {
                    errorMessage = '❌ Données du formulaire invalides. Veuillez vérifier vos informations.';
                } else if (error.message.includes('500')) {
                    errorMessage = '🔧 Erreur serveur temporaire. Veuillez réessayer.';
                } else if (error.message.includes('Failed to fetch')) {
                    errorMessage = '🌐 Problème de connexion. Vérifiez votre connexion internet.';
                } else {
                    errorMessage = `❌ ${paymentMethod.toUpperCase()}: ${errorMessage}`;
                }

                showError(errorMessage);

                isSubmitting = false;
                submitBtn.disabled = false;
                checkFormValidity();
            }

            // FONCTIONS UTILITAIRES
            function toggleOtpField(paymentMethod) {
                if (paymentMethod === 'orange') {
                    otpField.classList.add('show');
                    otpInput.required = true;
                } else {
                    otpField.classList.remove('show');
                    otpInput.required = false;
                    otpInput.value = '';
                }
            }

            function showError(message) {
                console.error('🚨 Erreur affichée:', message);
                errorAlert.textContent = message;
                errorAlert.style.display = 'block';
                errorAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            function hideError() {
                errorAlert.style.display = 'none';
            }

            function showLoading(method) {
                const loadingMessages = {
                    'mtn': {
                        text: '📱 Validation du paiement MTN',
                        instruction: isIvoryCoast ? 'Composer *133# puis l\'option 1 pour valider votre paiement' : 'Paiement MTN en cours... veuillez valider votre transaction'
                    },
                    'moov': {
                        text: '📱 Validation du paiement Moov',
                        instruction: isIvoryCoast ? 'Composer *155*15# pour valider votre paiement' : 'Paiement Moov en cours... veuillez valider votre transaction'
                    },
                    'orange': {
                        text: '🍊 Traitement Orange Money',
                        instruction: 'Vérification du code OTP en cours...'
                    },
                    'wave': {
                        text: '🌊 Redirection vers Wave',
                        instruction: 'Vous allez être redirigé vers Wave'
                    }
                };

                const config = loadingMessages[method] || {
                    text: 'Traitement du paiement...',
                    instruction: ''
                };

                loadingText.textContent = config.text;
                loadingInstruction.textContent = config.instruction;
                loadingOverlay.style.display = 'flex';
            }

            function hideLoading() {
                loadingOverlay.style.display = 'none';

                if (activeCheckingInterval) {
                    clearInterval(activeCheckingInterval);
                    activeCheckingInterval = null;
                }
            }

            // FONCTION CORRIGÉE: checkFormValidity
            function checkFormValidity() {
                const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
                const isPaymentSelected = !!selectedMethod;
                const phoneValue = phoneInput.value.trim();

                let isPhoneValid;
                if (isIvoryCoast) {
                    isPhoneValid = phoneValue.length === 10 && /^\d{10}$/.test(phoneValue);
                } else {
                    isPhoneValid = phoneValue.length >= 8 && /^\d+$/.test(phoneValue);
                }

                // Validation OTP correcte
                let isOtpValid = true;
                if (selectedMethod && selectedMethod.value === 'orange') {
                    const otpValue = otpInput.value.trim();
                    isOtpValid = otpValue.length >= 4; // Au moins 4 chiffres pour l'OTP
                }

                console.log('🔍 Validation du formulaire:', {
                    isPaymentSelected,
                    isPhoneValid,
                    isOtpValid,
                    selectedMethod: selectedMethod ? selectedMethod.value : 'aucune',
                    phoneValue: phoneValue.length,
                    otpValue: otpInput.value.length,
                    isSubmitting
                });

                submitBtn.disabled = !(isPaymentSelected && isPhoneValid && isOtpValid) || isSubmitting;
            }

            // EVENT LISTENERS
            paymentMethods.forEach(method => {
                method.addEventListener('change', function() {
                    selectedPaymentMethod = this.value;
                    console.log('🎯 Méthode sélectionnée:', selectedPaymentMethod);
                    toggleOtpField(selectedPaymentMethod);
                    checkFormValidity();
                });
            });

            phoneInput.addEventListener('input', function() {
                hideError();
                this.value = this.value.replace(/\D/g, '');

                // Validation différente selon le pays
                if (isIvoryCoast) {
                    if (this.value.length > 10) {
                        this.value = this.value.slice(0, 10);
                    }

                    phoneContainer.classList.remove('invalid');
                    if (this.value.length === 10) {
                        phoneContainer.style.borderColor = 'var(--secondary-color)';
                    } else if (this.value.length > 0) {
                        phoneContainer.style.borderColor = '#e53935';
                        phoneContainer.classList.add('invalid');
                    } else {
                        phoneContainer.style.borderColor = '#ddd';
                    }
                } else {
                    // Pour les autres pays, pas de limite stricte
                    phoneContainer.classList.remove('invalid');
                    if (this.value.length >= 8) {
                        phoneContainer.style.borderColor = 'var(--secondary-color)';
                    } else if (this.value.length > 0) {
                        phoneContainer.style.borderColor = '#e53935';
                        phoneContainer.classList.add('invalid');
                    } else {
                        phoneContainer.style.borderColor = '#ddd';
                    }
                }

                checkFormValidity();
            });

            otpInput.addEventListener('input', function() {
                hideError();
                this.value = this.value.replace(/\D/g, '');

                if (this.value.length >= 4) {
                    this.style.borderColor = 'var(--secondary-color)';
                } else if (this.value.length > 0) {
                    this.style.borderColor = '#e53935';
                } else {
                    this.style.borderColor = '#ddd';
                }

                checkFormValidity();
            });

            window.addEventListener('beforeunload', function() {
                if (activeCheckingInterval) {
                    clearInterval(activeCheckingInterval);
                }
            });

            // Initialisation
            console.log('✅ Script de paiement optimisé initialisé');
            console.log(`🌍 Pays détecté: ${phoneCountry} (Côte d'Ivoire: ${isIvoryCoast})`);

            // IMPORTANT: Appeler checkFormValidity() après un court délai pour l'initialisation
            setTimeout(() => {
                checkFormValidity();
                console.log('🔄 Validation initiale du formulaire effectuée');
            }, 100);
        });
    </script>
</body>
</html>
