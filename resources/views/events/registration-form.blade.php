<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}">
    <link rel="icon" href="{{ asset('assets/images/favicon-32x32.png') }}" sizes="32x32">
    <link rel="apple-touch-icon" href="{{ asset('assets/images/apple-touch-icon.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <title>{{ $currentEvent->event_title }} - {{ $currentOrganization->org_name }}</title>
    <style>
        :root {
            /* Couleurs dynamiques basées sur l'événement */
            --primary-color: {{ $currentEvent->primary_color ?? '#1d86d9' }};
            --secondary-color: {{ $currentEvent->secondary_color ?? '#28a745' }};
            --dark-primary: {{ $currentEvent->primary_color ? 'color-mix(in srgb, ' . $currentEvent->primary_color . ' 80%, black 20%)' : '#0056b3' }};
            --light-primary: {{ $currentEvent->primary_color ? 'color-mix(in srgb, ' . $currentEvent->primary_color . ' 10%, white 90%)' : '#f5f9ff' }};
            --warning-color: #ffa800;
            --background-light: var(--light-primary);
            --shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--background-light);
            color: #333;
            line-height: 1.6;
        }

        .container {
            width: 100%;
            min-height: 100vh;
            display: flex;
            flex-direction: row;
        }

        /* Event Info Panel - Panel gauche avec les informations de l'événement */
        .event-panel {
            flex: 1;
            background: linear-gradient(135deg, var(--primary-color), var(--dark-primary));
            color: white;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            min-height: 100vh;
        }

        @if($currentEvent->event_banner)
        .event-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('{{ $currentEvent->event_banner }}');
            background-size: cover;
            background-position: center;
            opacity: 0.3;
            z-index: 1;
        }

        .event-panel::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, var(--primary-color), var(--dark-primary));
            opacity: 0.8;
            z-index: 2;
        }
        @endif

        .event-content {
            position: relative;
            z-index: 3;
            text-align: center;
            max-width: 700px;
            width: 100%;
        }

        .event-logo {
            max-width: 200px;
            height: auto;
            margin-bottom: 2rem;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            backdrop-filter: blur(10px);
        }

        .event-title {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            line-height: 1.2;
            margin-top: 30px;
        }

        .event-subtitle {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .event-details {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .event-detail {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1rem;
            text-align: left;
        }

        .event-detail:last-child {
            margin-bottom: 0;
        }

        .detail-icon {
            margin-right: 1rem;
            font-size: 1.2rem;
            min-width: 30px;
            margin-top: 2px;
        }

        .register-btn {
            background-color: var(--secondary-color);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: none; /* Affiché seulement sur mobile */
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .register-btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }

        /* Form Panel - Panel droit avec le formulaire */
        .form-panel {
            flex: 1;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            background-color: white;
            overflow-y: auto;
            max-height: 100vh;
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-top: 1rem;
        }

        .logos-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .organization-logo {
            height: 80px;
            max-width: 150px;
            object-fit: contain;
        }

        .form-title {
            color: var(--primary-color);
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .form-subtitle {
            color: #666;
            font-size: 1rem;
        }

        /* Sections du formulaire */
        .form-section {
            margin-bottom: 2rem;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            overflow: hidden;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .section-header {
            background: linear-gradient(135deg, var(--light-primary), #f8f9fa);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e9ecef;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary-color);
            margin: 0;
        }

        .section-description {
            font-size: 0.9rem;
            color: #666;
            margin: 0.2rem 0 0 0;
        }

        .section-toggle {
            font-size: 1.2rem;
            color: var(--primary-color);
            transition: transform 0.3s ease;
        }

        .section-content {
            padding: 1.5rem;
            display: grid;
            gap: 1.5rem;
        }

        .section-content.collapsed {
            display: none;
        }

        /* Grille responsive pour les champs */
        .field-row {
            display: grid;
            gap: 1rem;
            grid-template-columns: 1fr;
        }

        .field-row.two-columns {
            grid-template-columns: 1fr 1fr;
        }

        .field-row.three-columns {
            grid-template-columns: 1fr 1fr 1fr;
        }

        .field-row.four-columns {
            grid-template-columns: 1fr 1fr 1fr 1fr;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group.field-full {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #444;
        }

        .required-label:after {
            content: ' *';
            color: #e53935;
        }

        .field-help-text {
            font-size: 0.85rem;
            color: #666;
            margin-top: 0.3rem;
            font-style: italic;
        }

        /* Styles des champs de saisie */
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="url"],
        input[type="number"],
        input[type="date"],
        input[type="time"],
        input[type="datetime-local"],
        input[type="password"],
        textarea,
        select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border 0.3s ease;
            font-family: inherit;
        }

        input:focus,
        textarea:focus,
        select:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(29, 134, 217, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        /* Styles pour les champs radio */
        .radio-group {
            margin-top: 0.5rem;
        }

        .radio-group.horizontal {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .radio-group.vertical .radio-option {
            margin-bottom: 0.8rem;
        }

        .radio-option {
            display: flex;
            align-items: flex-start;
            padding: 1rem;
            border: 1px solid #eee;
            border-radius: 8px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .radio-option:hover {
            border-color: var(--primary-color);
            background-color: var(--light-primary);
        }

        .radio-option.selected {
            border-color: var(--primary-color);
            background-color: var(--light-primary);
        }

        .radio-group.horizontal .radio-option {
            margin-bottom: 0;
            flex: 1;
            min-width: 150px;
        }

        input[type="radio"] {
            margin-right: 10px;
            margin-top: 2px;
            cursor: pointer;
        }

        /* Styles pour les checkbox groups */
        .checkbox-group {
            margin-top: 0.5rem;
            display: grid;
            gap: 0.8rem;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }

        .checkbox-option {
            display: flex;
            align-items: center;
            padding: 0.8rem;
            border: 1px solid #eee;
            border-radius: 8px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .checkbox-option:hover {
            border-color: var(--primary-color);
            background-color: var(--light-primary);
        }

        .checkbox-option.selected {
            border-color: var(--primary-color);
            background-color: var(--light-primary);
        }

        input[type="checkbox"] {
            margin-right: 10px;
            cursor: pointer;
        }

        /* Champ avec option "Autre" */
        .other-field {
            margin-top: 1rem;
            display: none;
        }

        .other-field.show {
            display: block;
        }

        /* Types de tickets */
        .ticket-price {
            font-weight: 700;
            color: var(--primary-color);
            font-size: 1.1rem;
        }

        .ticket-description {
            font-size: 0.9rem;
            color: #666;
            margin-top: 0.3rem;
        }

        /* Bouton de soumission */
        button[type="submit"] {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            border-radius: 8px;
            padding: 1rem;
            font-size: 1.1rem;
            font-weight: 600;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }

        button[type="submit"]:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* Erreurs de validation */
        .field-error {
            color: #e53935;
            font-size: 0.85rem;
            margin-top: 0.3rem;
        }

        .form-group.has-error input,
        .form-group.has-error textarea,
        .form-group.has-error select {
            border-color: #e53935;
        }

        /* Styles des modals */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            z-index: 10000;
            display: none;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal.show {
            display: flex !important;
            opacity: 1;
        }

        .modal-content {
            background-color: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            width: 90%;
            max-width: 500px;
            max-height: 80vh;
            overflow-y: auto;
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transform: translateY(-50px) scale(0.9);
            transition: transform 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .modal-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            gap: 0.5rem;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
        }

        .modal-header::before {
            content: '';
            width: 4px;
            height: 2rem;
            border-radius: 2px;
        }

        #successModal .modal-header {
            color: var(--secondary-color);
        }

        #successModal .modal-header::before {
            background: var(--secondary-color);
        }

        #errorModal .modal-header {
            color: #e74c3c;
        }

        #errorModal .modal-header::before {
            background: #e74c3c;
        }

        .close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.1);
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            font-weight: bold;
            color: #666;
            transition: all 0.2s ease;
        }

        .close:hover {
            background: rgba(0, 0, 0, 0.2);
            transform: rotate(90deg);
            color: #333;
        }

        .modal.show .modal-content {
            transform: translateY(0) scale(1);
        }

        #successModalMessage {
            background: linear-gradient(135deg, var(--light-primary), #e8f7e8);
            padding: 1.5rem;
            border-radius: 12px;
            border-left: 4px solid var(--secondary-color);
            margin: 1rem 0;
            font-size: 0.95rem;
            line-height: 1.6;
            white-space: pre-line;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.1);
        }

        #errorModalMessage {
            background: linear-gradient(135deg, #fff5f5, #ffebee);
            padding: 1.5rem;
            border-radius: 12px;
            border-left: 4px solid #e74c3c;
            margin: 1rem 0;
            box-shadow: 0 2px 8px rgba(231, 76, 60, 0.1);
        }

        #errorModalMessage li {
            margin-bottom: 0.5rem;
            color: #721c24;
            font-weight: 500;
        }

        /* Registration status indicators */
        .registration-status {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .status-open {
            background-color: #d4edda;
            color: #155724;
        }

        .status-closing {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-full {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Responsive design */
        @media (max-width: 992px) {
            .container {
                flex-direction: column;
            }

            .event-panel {
                min-height: 60vh;
                flex: none;
            }

            .register-btn {
                display: inline-block;
                margin-top: 1rem;
            }

            .form-panel {
                padding: 2rem 1rem;
                max-height: none;
            }

            .field-row.two-columns,
            .field-row.three-columns,
            .field-row.four-columns {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .modal-content {
                margin: 1rem;
                padding: 1.5rem;
                max-height: 90vh;
                max-width: 95vw;
            }

            .checkbox-group {
                grid-template-columns: 1fr;
            }

            .radio-group.horizontal {
                flex-direction: column;
            }
        }

        @media (max-width: 576px) {
            .event-panel {
                padding: 1.5rem;
            }

            .event-title {
                font-size: 2rem;
            }

            .event-subtitle {
                font-size: 1.2rem;
            }

            .event-details {
                padding: 1.5rem;
            }

            .logos-container {
                flex-direction: column;
                gap: 1rem;
            }

            .section-content {
                padding: 1rem;
            }
        }

        /* AJOUTER ces styles à votre section <style> existante */

/* === AMÉLIORATION DES CHAMPS "AUTRE" === */

/* Champ avec option "Autre" - Styles de base améliorés */
.other-field {
    margin-top: 1rem;
    padding: 1rem;
    background: linear-gradient(135deg, var(--light-primary), #f8f9fa);
    border: 1px solid #e9ecef;
    border-radius: 8px;
    border-left: 4px solid var(--primary-color);
    display: none;
    opacity: 0;
    transform: translateY(-10px);
    transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.other-field.show {
    display: block;
    opacity: 1;
    transform: translateY(0);
}

.other-field label {
    font-weight: 600;
    color: var(--primary-color);
    margin-bottom: 0.5rem;
    display: block;
    font-size: 0.9rem;
}

.other-field input {
    width: 100%;
    padding: 0.7rem;
    border: 1px solid #ced4da;
    border-radius: 6px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background: white;
    box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
}

.other-field input:focus {
    border-color: var(--primary-color);
    outline: none;
    box-shadow:
        inset 0 1px 3px rgba(0,0,0,0.05),
        0 0 0 3px rgba(29, 134, 217, 0.1);
    background: white;
    transform: translateY(-1px);
}

.other-field input::placeholder {
    color: #6c757d;
    font-style: italic;
}

/* === AMÉLIORATION DES CHECKBOX OPTIONS === */

/* Amélioration des checkbox options pour indiquer l'état sélectionné */
.checkbox-option {
    position: relative;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.checkbox-option:hover {
    border-color: var(--primary-color);
    background-color: var(--light-primary);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.checkbox-option.selected {
    border-color: var(--primary-color);
    background-color: var(--light-primary);
    box-shadow: 0 2px 8px rgba(29, 134, 217, 0.2);
}

.checkbox-option.selected::after {
    content: '✓';
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    color: var(--primary-color);
    font-weight: bold;
    font-size: 1.1rem;
    animation: checkmark 0.3s ease-in-out;
}

@keyframes checkmark {
    0% {
        opacity: 0;
        transform: scale(0.5);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

/* === ANIMATIONS POUR LES CHAMPS AUTRES === */

/* Animation d'apparition pour les champs conditionnels */
@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideOutUp {
    from {
        opacity: 1;
        transform: translateY(0);
    }
    to {
        opacity: 0;
        transform: translateY(-15px);
    }
}

.other-field.showing {
    animation: slideInDown 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
}

.other-field.hiding {
    animation: slideOutUp 0.3s ease-in forwards;
}

/* === STYLES POUR LES ERREURS === */

/* Styles pour les erreurs de validation sur les checkbox groups */
.form-group.has-error .checkbox-group {
    border: 1px solid #e53935;
    border-radius: 8px;
    padding: 0.5rem;
    background-color: rgba(229, 57, 53, 0.05);
}

.form-group.has-error .checkbox-option {
    border-color: rgba(229, 57, 53, 0.3);
}

/* Amélioration des messages d'erreur */
.field-error {
    color: #e53935;
    font-size: 0.85rem;
    margin-top: 0.5rem;
    padding: 0.5rem;
    background: rgba(229, 57, 53, 0.1);
    border-radius: 4px;
    border: 1px solid rgba(229, 57, 53, 0.3);
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* === STYLES SPÉCIFIQUES PAR SECTION === */

/* Styles pour les champs conditionnels dans les différentes sections */
.form-section[data-section="preferences"] .other-field {
    border-left-color: var(--secondary-color);
}

.form-section[data-section="preferences"] .other-field label {
    color: var(--secondary-color);
}

.form-section[data-section="professional_info"] .other-field {
    border-left-color: #ffa800;
}

.form-section[data-section="professional_info"] .other-field label {
    color: #ffa800;
}

/* === MISE EN ÉVIDENCE DES CHAMPS AVEC "AUTRE" === */

/* Mise en évidence subtile des checkbox groups qui ont une option "Autre" */
.checkbox-group[data-field-key*="preference"]:hover {
    background: rgba(var(--primary-color-rgb, 29, 134, 217), 0.02);
    border-radius: 8px;
    transition: background 0.3s ease;
}

/* === RESPONSIVE === */

/* Responsive adjustments */
@media (max-width: 768px) {
    .other-field {
        margin-top: 0.8rem;
        padding: 0.8rem;
    }

    .other-field input {
        padding: 0.6rem;
        font-size: 1rem;
    }

    .checkbox-option {
        margin-bottom: 0.5rem;
    }

    .checkbox-option.selected::after {
        top: 0.3rem;
        right: 0.3rem;
        font-size: 1rem;
    }
}

/* === INDICATEURS VISUELS === */

/* Animation subtile pour l'ajout/suppression de sélections */
.checkbox-option input[type="checkbox"] {
    transform: scale(1);
    transition: transform 0.2s ease;
}

.checkbox-option.selected input[type="checkbox"] {
    transform: scale(1.1);
}

/* Indicateur visuel pour les champs obligatoires conditionnels */
.other-field.required label::after {
    content: ' *';
    color: #e53935;
}

/* === AMÉLIORATION DE L'ACCESSIBILITÉ === */

/* Focus amélioré pour l'accessibilité */
.checkbox-option:focus-within {
    outline: 2px solid var(--primary-color);
    outline-offset: 2px;
}

.other-field input:focus {
    box-shadow:
        inset 0 1px 3px rgba(0,0,0,0.05),
        0 0 0 3px rgba(29, 134, 217, 0.15);
}

/* === DEBUG ET DÉVELOPPEMENT === */

/* Classes de debug (à retirer en production) */
.debug .other-field {
    border: 3px solid red !important;
}

.debug .checkbox-option[onclick*="Autre"] {
    border: 2px solid orange !important;
    background: rgba(255, 165, 0, 0.1) !important;
}

.debug .checkbox-group[data-field-key*="preference"] {
    border: 2px dashed blue !important;
    padding: 1rem !important;
}


    </style>
</head>
<body>
    <div class="container">
        <!-- Event Information Panel - PANEL GAUCHE AVEC LOGO ET INFORMATIONS -->
        <div class="event-panel">
            <div class="event-content">
                @if($currentOrganization->organization_logo)
                <img src="{{ url('public/' . $currentOrganization->organization_logo) }}" alt="Logo {{ $currentOrganization->org_name }}" class="event-logo">
                @endif

                <h1 class="event-title">{{ $currentEvent->event_title }}</h1>

                @if($currentEvent->event_description)
                <p class="event-subtitle">{{ $currentEvent->event_description }}</p>
                @endif

                <!-- Statut d'inscription -->
                @if($currentEvent->registration_status === 'open')
                    <div class="registration-status status-open">
                        ✅ Inscriptions ouvertes
                    </div>
                @elseif($currentEvent->registration_status === 'closing')
                    <div class="registration-status status-closing">
                        ⏰ Inscriptions bientôt fermées
                    </div>
                @elseif($currentEvent->registration_status === 'full')
                    <div class="registration-status status-full">
                        🚫 Événement complet
                    </div>
                @endif

                <div class="event-details">
                    @if($currentEvent->event_slug == "ria-2025")
                        <div class="event-detail">
                            <span class="detail-icon">📅</span>
                            <span>22 - 24 Août 2025</span>
                        </div>
                    @else
                        <div class="event-detail">
                            <span class="detail-icon">📅</span>
                            <span style="margin-top: 6px">{{ $currentEvent->event_date->locale('fr')->isoFormat('D MMMM YYYY') }}</span>
                        </div>

                        @if($currentEvent->event_start_time)
                        <div class="event-detail">
                            <span class="detail-icon">🕒</span>
                            <span style="margin-top: 6px">{{ $currentEvent->event_start_time->format('H:i') }}
                            @if($currentEvent->event_end_time)
                                - {{ $currentEvent->event_end_time->format('H:i') }}
                            @endif
                            </span>
                        </div>
                        @endif
                    @endif

                    @if($currentEvent->event_location)
                    <div class="event-detail">
                        <span class="detail-icon">📍</span>
                        <span style="margin-top: 6px">{{ $currentEvent->event_location }}</span>
                    </div>
                    @endif

                    @if($currentEvent->event_address)
                    <div class="event-detail">
                        <span class="detail-icon">🗺️</span>
                        <span style="margin-top: 6px">{{ $currentEvent->event_address }}</span>
                    </div>
                    @endif

                    @if($currentEvent->dress_code_general)
                    <div class="event-detail">
                        <span class="detail-icon">👗</span>
                        <span style="margin-top: 6px">{{ $currentEvent->dress_code_general }}</span>
                    </div>
                    @endif

                    <!-- Tarification dynamique -->
                    @if($currentEvent->ticketTypes && $currentEvent->ticketTypes->count() > 0)
                    <div class="event-detail">
                        <span class="detail-icon">💰</span>
                        <div>
                            @if($currentEvent->ticketTypes->count() == 1)
                                <div style="margin-bottom: 0.5rem;">
                                    <strong>Pass d'entrée:</strong>
                                    @php $ticketType = $currentEvent->ticketTypes->first(); @endphp
                                    @if($ticketType->price > 0)
                                        {{ number_format($ticketType->price, 0, ',', ' ') }} {{ $ticketType->currency ?? 'FCFA' }}
                                    @else
                                        Gratuit
                                    @endif
                                </div>
                            @else
                                @foreach($currentEvent->ticketTypes->sortBy('display_order') as $ticketType)
                                    <div style="margin-bottom: 0.5rem;">
                                        <strong>{{ $ticketType->ticket_name ?? 'Pass d\'entrée' }}:</strong>
                                        @if($ticketType->price > 0)
                                            {{ number_format($ticketType->price, 0, ',', ' ') }} {{ $ticketType->currency ?? 'FCFA' }}
                                        @else
                                            Gratuit
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    @endif


                </div>

                <button class="register-btn" id="mobile-register-btn">S'incrire</button>
            </div>
        </div>

        <!-- Registration Form Panel - PANEL DROIT AVEC LE FORMULAIRE -->
        <div class="form-panel" id="form-section">
            <div class="form-header">
                @if($currentOrganization->organization_logo)
                <div class="logos-container">
                    <img src="{{ url('public/' . $currentOrganization->organization_logo) }}" alt="Logo {{ $currentOrganization->org_name }}" class="organization-logo">
                </div>
                @endif

                <h2 class="form-title">{{ $currentEvent->event_title }}</h2>
                <p class="form-subtitle">Remplissez le formulaire ci-dessous pour participer à l'événement</p>
            </div>

            <!-- Message pour paiement partiel ou réservation -->
            @if(isset($partialRegistration) && $partialRegistration)
            <div class="partial-payment-alert" style="background: linear-gradient(135deg, #fff3cd, #ffe69c); border: 2px solid #ffc107; border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem; box-shadow: 0 4px 12px rgba(255, 193, 7, 0.2);">
                <div style="display: flex; align-items: flex-start; gap: 1rem;">
                    <div style="font-size: 2rem; line-height: 1;">⚠️</div>
                    <div style="flex: 1;">
                        <h3 style="color: #856404; font-size: 1.2rem; font-weight: 700; margin-bottom: 0.5rem;">
                            Inscription en cours
                        </h3>
                        <p style="color: #856404; margin-bottom: 1rem; line-height: 1.6;">
                            Vous avez déjà une inscription pour cet événement avec un
                            <strong>{{ $partialRegistration->payment_status === 'partial' ? 'paiement partiel' : 'paiement de réservation' }}</strong>.
                        </p>
                        <div style="background: white; border-radius: 8px; padding: 1rem; margin-bottom: 1rem; border-left: 4px solid #ffc107;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span style="color: #856404; font-weight: 600;">Montant total :</span>
                                <span style="color: #856404; font-weight: 700;">{{ number_format($partialRegistration->ticket_price, 0, ',', ' ') }} {{ $currentEvent->currency ?? 'FCFA' }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span style="color: #856404; font-weight: 600;">Montant payé :</span>
                                <span style="color: #28a745; font-weight: 700;">{{ number_format($partialRegistration->amount_paid, 0, ',', ' ') }} {{ $currentEvent->currency ?? 'FCFA' }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding-top: 0.5rem; border-top: 2px solid #ffc107;">
                                <span style="color: #856404; font-weight: 700; font-size: 1.1rem;">Solde restant :</span>
                                <span style="color: #dc3545; font-weight: 700; font-size: 1.2rem;">{{ number_format($partialRegistration->balance_due, 0, ',', ' ') }} {{ $currentEvent->currency ?? 'FCFA' }}</span>
                            </div>
                        </div>
                        <a href="{{ route('event.registration.complete-payment', ['org_slug' => $currentOrganization->org_key, 'event_slug' => $currentEvent->event_slug, 'registrationId' => $partialRegistration->id]) }}"
                           style="display: inline-block; background: linear-gradient(135deg, var(--primary-color), var(--dark-primary)); color: white; padding: 1rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 600; text-align: center; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                            <i class="fas fa-credit-card" style="margin-right: 0.5rem;"></i>
                            Finaliser le paiement
                        </a>
                        <p style="color: #856404; font-size: 0.9rem; margin-top: 1rem; opacity: 0.8;">
                            <i class="fas fa-info-circle" style="margin-right: 0.5rem;"></i>
                            Vous pouvez également continuer avec une nouvelle inscription ci-dessous.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            @if($currentEvent->can_register ?? true)
            <form action="{{ route('event.registration.store', ['org_slug' => $currentOrganization->org_key, 'event_slug' => $currentEvent->event_slug]) }}" method="post" id="dynamicForm">
                @csrf

                @if(isset($formStructure) && $formStructure && $formStructure->count() > 0)
                    <!-- Formulaire dynamique généré -->
                    @foreach($formStructure->groupBy('section_key') as $sectionKey => $fields)
                        @php $firstField = $fields->first(); @endphp
                        <div class="form-section" data-section="{{ $sectionKey }}">
                            <div class="section-header" onclick="toggleSection('{{ $sectionKey }}')">
                                <div>
                                    <h3 class="section-title">{{ $firstField->section_title }}</h3>
                                    @if($firstField->section_description)
                                    <p class="section-description">{{ $firstField->section_description }}</p>
                                    @endif
                                </div>
                                <span class="section-toggle">▼</span>
                            </div>

                            <div class="section-content" id="section-{{ $sectionKey }}">
                                @php
                                    $fieldsArray = $fields->sortBy('display_order')->values()->all();
                                    $currentRow = [];
                                    $i = 0;
                                @endphp

                                @while($i < count($fieldsArray))
                                    @php
                                        $field = $fieldsArray[$i];
                                        $currentRow = [];

                                        // Si c'est un champ full, il prend toute la ligne
                                        if ($field->field_width === 'full') {
                                            $currentRow[] = $field;
                                            $i++;
                                        }
                                        // Sinon, essayer de remplir la ligne avec des champs compatibles
                                        else {
                                            $remainingWidth = 1.0; // 100% de largeur disponible

                                            while ($i < count($fieldsArray) && $remainingWidth > 0) {
                                                $currentField = $fieldsArray[$i];

                                                // Déterminer la largeur du champ
                                                $fieldWidthValue = match($currentField->field_width) {
                                                    'half' => 0.5,
                                                    'third' => 0.33,
                                                    'quarter' => 0.25,
                                                    'full' => 1.0,
                                                    default => 1.0
                                                };

                                                // Si le champ peut tenir dans la ligne
                                                if ($fieldWidthValue <= $remainingWidth + 0.01) { // +0.01 pour gérer les erreurs d'arrondi
                                                    $currentRow[] = $currentField;
                                                    $remainingWidth -= $fieldWidthValue;
                                                    $i++;

                                                    // Si on a rempli la ligne ou si le prochain champ est 'full'
                                                    if ($remainingWidth <= 0.01 ||
                                                        ($i < count($fieldsArray) && $fieldsArray[$i]->field_width === 'full')) {
                                                        break;
                                                    }
                                                } else {
                                                    break; // Le champ ne peut pas tenir, passer à la ligne suivante
                                                }
                                            }
                                        }

                                        // Déterminer la classe CSS pour la ligne
                                        $columnClass = '';
                                        if (count($currentRow) === 1 && $currentRow[0]->field_width !== 'full') {
                                            $columnClass = 'two-columns'; // Pour centrer un champ half seul
                                        } elseif (count($currentRow) === 2) {
                                            $columnClass = 'two-columns';
                                        } elseif (count($currentRow) === 3) {
                                            $columnClass = 'three-columns';
                                        } elseif (count($currentRow) === 4) {
                                            $columnClass = 'four-columns';
                                        }
                                    @endphp

                                    <div class="field-row {{ $columnClass }}">
                                        @foreach($currentRow as $rowField)
                                            @include('components.dynamic-field', ['field' => $rowField])
                                        @endforeach
                                    </div>
                                @endwhile
                            </div>
                        </div>
                    @endforeach

                    <!-- Section pour la sélection du type de ticket -->
                    @if($currentEvent->ticketTypes && $currentEvent->ticketTypes->count() > 0)
                    <div class="form-section">
                        <div class="section-header">
                            <div>
                                <h3 class="section-title">Type de participation</h3>
                                <p class="section-description">
                                    @if($currentEvent->ticketTypes->count() > 1)
                                        Choisissez votre catégorie de participation
                                    @else
                                        Tarification de l'événement
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="section-content">
                            <div class="form-group">
                                <label class="required-label">Type de ticket</label>
                                <div class="radio-group vertical">
                                    @foreach($currentEvent->ticketTypes->sortBy('display_order') as $ticketType)
                                        <div class="radio-option" onclick="selectTicket({{ $ticketType->id }})">
                                            <input type="radio" id="ticket-{{ $ticketType->id }}" name="ticket_type_id" value="{{ $ticketType->id }}" required
                                                @if(old('ticket_type_id') == $ticketType->id || ($currentEvent->ticketTypes->count() == 1 && $loop->first)) checked @endif>
                                            <div>
                                                <label for="ticket-{{ $ticketType->id }}">
                                                    <div class="ticket-price">
                                                        @if($ticketType->price > 0)
                                                            {{ number_format($ticketType->price, 0, ',', ' ') }} {{ $ticketType->currency ?? 'FCFA' }}
                                                        @else
                                                            Gratuit
                                                        @endif
                                                    </div>
                                                    <div><strong>{{ $ticketType->ticket_name }}</strong></div>
                                                    @if($ticketType->ticket_description)
                                                    <div class="ticket-description">{{ $ticketType->ticket_description }}</div>
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                @else
                    <!-- Formulaire de fallback si pas de structure dynamique -->
                    <div class="form-section">
                        <div class="section-header">
                            <div>
                                <h3 class="section-title">Informations de base</h3>
                                <p class="section-description">Vos informations personnelles</p>
                            </div>
                        </div>

                        <div class="section-content">
                            <div class="form-group">
                                <label for="fullname" class="required-label">Nom & Prénoms</label>
                                <input type="text" id="fullname" name="fullname" required value="{{ old('fullname') }}" placeholder="Votre nom complet">
                            </div>

                            <div class="field-row two-columns">
                                <div class="form-group">
                                    <label for="phone" class="required-label">Numéro WhatsApp</label>
                                    <input type="tel" id="phone" name="phone" required value="{{ old('phone') }}" placeholder="Votre numéro WhatsApp">
                                </div>

                                <div class="form-group">
                                    <label for="email" class="required-label">Email</label>
                                    <input type="email" id="email" name="email" required value="{{ old('email') }}" placeholder="votre@email.com">
                                </div>
                            </div>

                            <div class="field-row two-columns">
                                <div class="form-group">
                                    <label for="organization">Organisation</label>
                                    <input type="text" id="organization" name="organization" value="{{ old('organization') }}" placeholder="Votre organisation (optionnel)">
                                </div>

                                <div class="form-group">
                                    <label for="position">Fonction/Poste</label>
                                    <input type="text" id="position" name="position" value="{{ old('position') }}" placeholder="Votre fonction (optionnel)">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Types de tickets pour le fallback -->
                    @if($currentEvent->ticketTypes && $currentEvent->ticketTypes->count() > 0)
                    <div class="form-section">
                        <div class="section-header">
                            <div>
                                <h3 class="section-title">Type de ticket</h3>
                                <p class="section-description">
                                    @if($currentEvent->ticketTypes->count() > 1)
                                        Choisissez votre catégorie
                                    @else
                                        Tarification de l'événement
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="section-content">
                            <div class="form-group">
                                <label class="required-label">Choisissez votre catégorie</label>
                                <div class="radio-group vertical">
                                    @foreach($currentEvent->ticketTypes->sortBy('display_order') as $ticketType)
                                        <div class="radio-option" onclick="selectTicket({{ $ticketType->id }})">
                                            <input type="radio" id="ticket-{{ $ticketType->id }}" name="ticket_type_id" value="{{ $ticketType->id }}" required
                                                @if(old('ticket_type_id') == $ticketType->id || ($currentEvent->ticketTypes->count() == 1 && $loop->first)) checked @endif>
                                            <div>
                                                <label for="ticket-{{ $ticketType->id }}">
                                                    <div class="ticket-price">
                                                        @if($ticketType->price > 0)
                                                            {{ number_format($ticketType->price, 0, ',', ' ') }} {{ $ticketType->currency ?? 'FCFA' }}
                                                        @else
                                                            Gratuit
                                                        @endif
                                                    </div>
                                                    <div><strong>{{ $ticketType->ticket_name }}</strong></div>
                                                    @if($ticketType->ticket_description)
                                                    <div class="ticket-description">{{ $ticketType->ticket_description }}</div>
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                @endif

                <!-- Options de paiement partiel et réservation -->
                @if($currentEvent->requires_payment && ($currentEvent->allow_partial_payment || $currentEvent->allow_reservation))
                <div class="form-section" id="paymentOptionsSection" style="display: none;">
                    <div class="section-header">
                        <div>
                            <h3 class="section-title">Options de paiement</h3>
                            <p class="section-description">Choisissez votre mode de paiement</p>
                        </div>
                    </div>
                    <div class="section-content">
                        @if($currentEvent->allow_partial_payment)
                        <div class="form-group">
                            <label class="flex items-center">
                                <input type="radio"
                                       id="payment_type_full"
                                       name="payment_type"
                                       value="full"
                                       checked
                                       class="payment-type-radio">
                                <span class="ml-2">Paiement complet</span>
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="flex items-center">
                                <input type="radio"
                                       id="payment_type_partial"
                                       name="payment_type"
                                       value="partial"
                                       class="payment-type-radio">
                                <span class="ml-2">Paiement partiel</span>
                            </label>
                            @if($currentEvent->partial_payment_amount)
                            <p class="text-sm text-gray-600 mt-1 ml-6">
                                Montant du paiement partiel : <strong>{{ number_format($currentEvent->partial_payment_amount, 0, ',', ' ') }} {{ $currentEvent->currency ?? 'FCFA' }}</strong>
                            </p>
                            @endif
                        </div>
                        @endif

                        @if($currentEvent->allow_reservation)
                        <div class="form-group">
                            <label class="flex items-center">
                                <input type="radio"
                                       id="payment_type_reservation"
                                       name="payment_type"
                                       value="reservation"
                                       class="payment-type-radio">
                                <span class="ml-2">Réservation</span>
                            </label>
                            @if($currentEvent->reservation_amount)
                            <p class="text-sm text-gray-600 mt-1 ml-6">
                                Montant de la réservation : <strong>{{ number_format($currentEvent->reservation_amount, 0, ',', ' ') }} {{ $currentEvent->currency ?? 'FCFA' }}</strong>
                            </p>
                            @endif
                            @if($currentEvent->reservation_terms)
                            <div class="mt-2 ml-6 p-3 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-700"><strong>Conditions de réservation :</strong></p>
                                <p class="text-sm text-gray-600 mt-1">{{ $currentEvent->reservation_terms }}</p>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <button type="submit">S'inscrire</button>
            </form>
            @else
            <div style="text-align: center; padding: 2rem; background: #f8f9fa; border-radius: 10px;">
                @if(isset($currentEvent->registration_status))
                    @if($currentEvent->registration_status === 'not_started')
                        <h3>Inscriptions pas encore ouvertes</h3>
                        <p>Les inscriptions pour cet événement ne sont pas encore disponibles.</p>
                        @if($currentEvent->registration_start_date)
                            <p>Les inscriptions ouvriront le <strong>{{ $currentEvent->registration_start_date->format('d/m/Y à H:i') }}</strong></p>
                        @endif
                    @elseif($currentEvent->registration_status === 'closed')
                        <h3>Inscriptions fermées</h3>
                        <p>Les inscriptions pour cet événement ne sont plus disponibles.</p>
                        @if($currentEvent->registration_end_date)
                            <p>Les inscriptions ont fermé le <strong>{{ $currentEvent->registration_end_date->format('d/m/Y à H:i') }}</strong></p>
                        @endif
                    @else
                        <h3>Inscriptions fermées</h3>
                        <p>Les inscriptions pour cet événement ne sont plus disponibles.</p>
                    @endif
                @else
                    <h3>Inscriptions fermées</h3>
                    <p>Les inscriptions pour cet événement ne sont plus disponibles.</p>
                @endif
            </div>
            @endif
        </div>
    </div>

    <!-- Modals -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <button class="close" onclick="closeModal('successModal')">&times;</button>
            <div class="modal-header">
                <h2>🎉 Inscription réussie !</h2>
            </div>
            <div id="successModalMessage">
                Votre inscription a été enregistrée avec succès. Vous recevrez bientôt une confirmation par email.
            </div>
        </div>
    </div>

    <div id="errorModal" class="modal">
        <div class="modal-content">
            <button class="close" onclick="closeModal('errorModal')">&times;</button>
            <div class="modal-header">
                <h2>❌ Erreur lors de l'inscription</h2>
            </div>
            <div id="errorModalMessage"></div>
        </div>
    </div>

    <script>
        // Variables dynamiques depuis le serveur
        const eventData = {
            title: @json($currentEvent->event_title ?? 'Événement'),
            colors: {
                primary: @json($currentEvent->primary_color ?? '#1d86d9'),
                secondary: @json($currentEvent->secondary_color ?? '#28a745')
            },
            organization: @json($currentOrganization->org_name ?? 'Organisation')
        };

        // Variables pour la redirection (évite de parser l'URL)
        @php
            $paymentUrl = route('event.payment.validation.post', ['org_slug' => $currentOrganization->org_key, 'event_slug' => $currentEvent->event_slug]);
            $paymentPath = parse_url($paymentUrl, PHP_URL_PATH);
        @endphp
        const routeData = {
            orgSlug: @json($currentOrganization->org_key ?? ''),
            eventSlug: @json($currentEvent->event_slug ?? ''),
            // Utiliser l'helper Laravel route() pour construire l'URL correctement (prend en compte les sous-dossiers)
            // Extraire uniquement le chemin (sans le domaine) pour éviter les problèmes avec les URLs absolues dans les formulaires
            paymentUrl: @json($paymentPath ?? '/')
        };

        // Vérifier que les données sont bien définies
        console.log('RouteData initialisé:', routeData);
        if (!routeData.orgSlug || !routeData.eventSlug) {
            console.error('⚠️ RouteData incomplet:', routeData);
        }

        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Initialisation du formulaire dynamique...');

            // Vérifier les paramètres URL pour les retours de paiement
            checkPaymentStatus();

            // Initialiser les composants du formulaire
            initializeFormComponents();

            // Configurer le bouton mobile
            setupMobileButton();

            // Configurer la soumission du formulaire
            setupFormSubmission();

            // Gérer les erreurs de validation Laravel
            handleLaravelValidationErrors();

            // Initialiser l'affichage des options de paiement
            initializePaymentOptions();

            //setTimeout(() => addDebugButton(), 1000);
        });

        function initializeFormComponents() {
            // Initialiser les sections collapsibles
            initializeSections();

            // Initialiser les champs avec options "Autre"
            initializeOtherFields();

            // Initialiser la validation
            initializeValidation();

            // Initialiser les modals
            initializeModals();
        }

        function initializeSections() {
            // Par défaut, garder toutes les sections ouvertes
            document.querySelectorAll('.section-content').forEach(section => {
                section.classList.remove('collapsed');
            });
        }

        function toggleSection(sectionKey) {
            const content = document.getElementById(`section-${sectionKey}`);
            if (!content) return;

            const toggle = content.closest('.form-section').querySelector('.section-toggle');

            if (content.classList.contains('collapsed')) {
                content.classList.remove('collapsed');
                if (toggle) toggle.style.transform = 'rotate(0deg)';
            } else {
                content.classList.add('collapsed');
                if (toggle) toggle.style.transform = 'rotate(-90deg)';
            }
        }

        function initializeOtherFields() {
        console.log('🔧 Initialisation de la gestion des champs "Autre" (base de données)...');

        // 1. CHERCHER LES CHAMPS "AUTRES" DÉJÀ DANS LE DOM (définis en base)
        const existingOtherFields = document.querySelectorAll('[data-field-key$="_other"]');
        console.log(`🗄️ Champs "autres" trouvés en base de données: ${existingOtherFields.length}`);

        existingOtherFields.forEach(otherFieldGroup => {
            const otherInput = otherFieldGroup.querySelector('input');
            if (otherInput && otherInput.name) {
                console.log(`📝 Champ autre en DB trouvé: ${otherInput.name}`);

                // Marquer comme champ "autre" pour la collecte
                otherInput.setAttribute('data-is-other-field', 'true');
                otherInput.classList.add('other-field-input');

                // Marquer le container
                otherFieldGroup.classList.add('other-field');

                // Masquer initialement si pas déjà visible
                if (!otherFieldGroup.style.display || otherFieldGroup.style.display === 'none') {
                    otherFieldGroup.style.display = 'none';
                }
            }
        });

        // 2. GÉRER LES CHECKBOX GROUPS AVEC OPTION "AUTRE"
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                handleOtherCheckboxOption(this);
            });
        });

        // 3. GÉRER LES RADIO BUTTONS ET SELECT (pour compatibilité)
        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                handleOtherRadioOption(this);
            });
        });

        document.querySelectorAll('select').forEach(select => {
            select.addEventListener('change', function() {
                handleOtherSelectOption(this);
            });
        });

        console.log('✅ Gestion des champs "Autre" initialisée (DB + dynamiques)');
    }

    function handleOtherCheckboxOption(checkbox) {
    if (checkbox.value.toLowerCase() === 'autre') {
        console.log('🎯 Option "Autre" détectée:', checkbox.name, checkbox.checked);

        const fieldGroup = checkbox.closest('.form-group');
        if (!fieldGroup) return;

        // CHERCHER D'ABORD LES CHAMPS "AUTRES" DÉFINIS EN BASE
        const baseFieldName = getFieldKeyFromCheckbox(checkbox);
        const otherFieldName = baseFieldName + '_other';

        console.log(`🔍 Recherche du champ autre: ${otherFieldName}`);

        // Méthode 1: Chercher par data-field-key (champs de base de données)
        let otherField = document.querySelector(`[data-field-key="${otherFieldName}"]`);

        // Méthode 2: Chercher dans le même groupe de formulaire
        if (!otherField) {
            otherField = fieldGroup.querySelector('.other-field');
        }

        // Méthode 3: Chercher par data-other-field (champs dynamiques)
        if (!otherField) {
            otherField = fieldGroup.querySelector(`[data-other-field="${otherFieldName}"]`);
        }

        // Méthode 4: Création dynamique en dernier recours
        if (!otherField) {
            console.log(`🆕 Création dynamique du champ: ${otherFieldName}`);
            otherField = createOtherField(baseFieldName, otherFieldName);

            const checkboxGroup = fieldGroup.querySelector('.checkbox-group');
            if (checkboxGroup) {
                checkboxGroup.parentNode.insertBefore(otherField, checkboxGroup.nextSibling);
            }
        } else {
            console.log(`✅ Champ autre trouvé en base: ${otherFieldName}`);

            // S'assurer que le champ est marqué correctement
            const input = otherField.querySelector('input');
            if (input) {
                input.setAttribute('data-is-other-field', 'true');
                input.classList.add('other-field-input');
            }
            otherField.classList.add('other-field');
        }

        // Afficher ou masquer selon l'état de la checkbox
        if (checkbox.checked) {
            showOtherFieldDB(otherField);
        } else {
            hideOtherFieldDB(otherField);
        }
    }
}

function addDebugButton() {
    if (document.getElementById('debug-btn')) return; // Éviter les doublons

    const debugBtn = document.createElement('button');
    debugBtn.id = 'debug-btn';
    debugBtn.type = 'button';
    debugBtn.textContent = '🔍 Debug Champs';
    debugBtn.style.cssText = `
        position: fixed;
        top: 10px;
        right: 10px;
        z-index: 9999;
        background: #ff4444;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 12px;
    `;

    debugBtn.addEventListener('click', function() {
        console.log('🔍 === DEBUG CHAMPS AUTRES ===');
        const form = document.querySelector('form');
        const otherFields = form.querySelectorAll('input[name$="_other"]');

        otherFields.forEach(field => {
            const isVisible = field.offsetParent !== null;
            const hasValue = field.value.trim() !== '';
            console.log(`📝 ${field.name}: valeur="${field.value}", visible=${isVisible}`);
        });

        const checkboxesAutre = form.querySelectorAll('input[type="checkbox"][value="Autre"]:checked');
        console.log(`☑️ Checkboxes "Autre" cochées: ${checkboxesAutre.length}`);
        checkboxesAutre.forEach(cb => {
            console.log(`  ☑️ ${cb.name}: ${cb.value}`);
        });
    });

    document.body.appendChild(debugBtn);
    console.log('🔧 Bouton de debug ajouté');
}


function showOtherFieldDB(otherField) {
    if (!otherField) return;

    console.log('✅ Affichage du champ "autre" (base de données)');

    // Rendre visible
    otherField.style.display = 'block';
    otherField.classList.add('visible');
    otherField.classList.remove('d-none', 'hidden');

    // AJOUT IMPORTANT : Marquer l'input correctement
    const input = otherField.querySelector('input');
    if (input) {
        input.setAttribute('data-is-other-field', 'true');
        input.disabled = false;
        input.style.display = 'block';
    }

    // Animation d'apparition
    otherField.style.opacity = '0';
    otherField.style.transform = 'translateY(-10px)';

    setTimeout(() => {
        otherField.style.opacity = '1';
        otherField.style.transform = 'translateY(0)';
        if (input) input.focus();
    }, 10);
}

function hideOtherFieldDB(otherField) {
    if (!otherField) return;

    console.log('❌ Masquage du champ "autre" (base de données)');

    otherField.classList.remove('visible');

    // Animation de disparition
    otherField.style.opacity = '0';
    otherField.style.transform = 'translateY(-10px)';

    setTimeout(() => {
        otherField.style.display = 'none';

        // Vider et désactiver l'input
        const input = otherField.querySelector('input');
        if (input) {
            input.value = '';
            input.required = false;
            // Ne pas désactiver complètement pour éviter les problèmes de soumission
        }
    }, 300);
}

function collectFormDataEnhanced(form) {
    const formData = new FormData();

    console.log('🔍 === COLLECTE AVANCÉE DES DONNÉES ===');

    // Token CSRF
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        formData.append('_token', csrfToken.getAttribute('content'));
    }

    // 1. Collecter les champs normaux
    const normalFields = form.querySelectorAll('input:not([data-is-other-field]), select, textarea');
    console.log(`📝 Champs normaux: ${normalFields.length}`);

    normalFields.forEach(field => {
        if (!field.name) return;

        if (field.type === 'radio' || field.type === 'checkbox') {
            if (field.checked && !field.name.endsWith('[]')) {
                formData.append(field.name, field.value);
                console.log(`📝 ${field.type}: ${field.name} = "${field.value}"`);
            }
        } else if (field.value.trim() !== '') {
            formData.append(field.name, field.value);
            console.log(`📝 Champ: ${field.name} = "${field.value}"`);
        }
    });

    // Vérification supplémentaire pour les radios (notamment ticket_type_id)
    // Parcourir tous les radios pour s'assurer qu'ils sont collectés
    const allRadios = form.querySelectorAll('input[type="radio"]');
    allRadios.forEach(radio => {
        if (radio.checked && radio.name && !radio.name.endsWith('[]')) {
            // Vérifier si déjà ajouté
            if (!formData.has(radio.name)) {
                formData.append(radio.name, radio.value);
                console.log(`📝 Radio ajouté (vérification): ${radio.name} = "${radio.value}"`);
            }
        }
    });

    // 2. COLLECTE SPÉCIALE POUR CHAMPS "AUTRES" (base de données + dynamiques)
    console.log('🔍 === COLLECTE CHAMPS AUTRES ===');

    // Méthode A: Champs marqués avec data-is-other-field
    const markedOtherFields = form.querySelectorAll('input[data-is-other-field="true"]');
    console.log(`🎯 Champs marqués "autre": ${markedOtherFields.length}`);

    // Méthode B: Champs dans des containers .other-field visibles
    const containerOtherFields = form.querySelectorAll('.other-field.visible input, .other-field[style*="block"] input');
    console.log(`🎯 Champs dans containers visibles: ${containerOtherFields.length}`);

    // Méthode C: Champs avec nom se terminant par "_other"
    const namedOtherFields = form.querySelectorAll('input[name$="_other"]');
    console.log(`🎯 Champs nommés "*_other": ${namedOtherFields.length}`);

    // Combiner toutes les méthodes et dédupliquer
    const allOtherInputs = new Set();

    markedOtherFields.forEach(input => {
        const container = input.closest('.form-group, .other-field');
        const isVisible = container && (
            container.style.display === 'block' ||
            container.classList.contains('visible') ||
            (container.style.display !== 'none' && !container.classList.contains('d-none', 'hidden'))
        );

        if (isVisible) {
            allOtherInputs.add(input);
        }
    });

    containerOtherFields.forEach(input => allOtherInputs.add(input));

    namedOtherFields.forEach(input => {
        const container = input.closest('.form-group, .other-field');
        const isVisible = container && (
            container.style.display === 'block' ||
            container.classList.contains('visible') ||
            (container.style.display !== 'none' && !container.classList.contains('d-none', 'hidden'))
        );

        if (isVisible) {
            allOtherInputs.add(input);
        }
    });

    console.log(`🎯 TOTAL champs "autres" uniques à collecter: ${allOtherInputs.size}`);

    // Collecter tous les champs "autres"
    allOtherInputs.forEach((input, index) => {
        if (input.name) {
            formData.append(input.name, input.value || '');
            console.log(`🎯 AUTRE ${index + 1}: ${input.name} = "${input.value}"`);
        }
    });

    // 3. Collecter les checkbox arrays
    const checkboxGroups = {};
    form.querySelectorAll('input[type="checkbox"]:checked').forEach(cb => {
        if (cb.name.endsWith('[]')) {
            const baseName = cb.name.slice(0, -2);
            if (!checkboxGroups[baseName]) checkboxGroups[baseName] = [];
            checkboxGroups[baseName].push(cb.value);
        }
    });

    Object.keys(checkboxGroups).forEach(fieldName => {
        checkboxGroups[fieldName].forEach(value => {
            formData.append(fieldName + '[]', value);
        });
        console.log(`☑️ Array: ${fieldName}[] = [${checkboxGroups[fieldName].join(', ')}]`);
    });

    // 4. COLLECTE FORCÉE DES CHAMPS "AUTRES" SPÉCIFIQUES
    console.log('🔍 === COLLECTE SUPER SIMPLE ===');

    // Forcer la collecte des 3 champs spécifiques
    const breakfast_other = form.querySelector('input[name="breakfast_preference_other"]');
    const lunch_other = form.querySelector('input[name="lunch_preference_other"]');
    const dinner_other = form.querySelector('input[name="dinner_preference_other"]');

    if (breakfast_other && breakfast_other.value) {
        formData.append('breakfast_preference_other', breakfast_other.value);
        console.log(`🎯 FORCÉ: breakfast_preference_other = "${breakfast_other.value}"`);
    }

    if (lunch_other && lunch_other.value) {
        formData.append('lunch_preference_other', lunch_other.value);
        console.log(`🎯 FORCÉ: lunch_preference_other = "${lunch_other.value}"`);
    }

    if (dinner_other && dinner_other.value) {
        formData.append('dinner_preference_other', dinner_other.value);
        console.log(`🎯 FORCÉ: dinner_preference_other = "${dinner_other.value}"`);
    }

    // 5. RÉSUMÉ FINAL
    console.log('📋 === DONNÉES FINALES ===');
    for (let [key, value] of formData.entries()) {
        console.log(`  ✓ ${key}: ${value}`);
    }

    return formData;
}

// Gestion spécifique pour les radio buttons
function handleOtherRadioOption(radio) {
    if (radio.value.toLowerCase() === 'autre') {
        const fieldGroup = radio.closest('.form-group');
        if (!fieldGroup) return;

        const fieldKey = radio.name;
        const otherFieldKey = fieldKey + '_other';

        let otherField = fieldGroup.querySelector('.other-field');
        if (!otherField) {
            otherField = fieldGroup.querySelector(`[data-other-field="${otherFieldKey}"]`);
        }

        if (!otherField) {
            otherField = createOtherField(fieldKey, otherFieldKey);
            const radioGroup = fieldGroup.querySelector('.radio-group');
            if (radioGroup) {
                radioGroup.parentNode.insertBefore(otherField, radioGroup.nextSibling);
            }
        }

        if (radio.checked) {
            showOtherField(otherField);
        }
    } else {
        // Si on sélectionne une autre option, masquer le champ "autre"
        const fieldGroup = radio.closest('.form-group');
        const otherField = fieldGroup?.querySelector('.other-field');
        if (otherField) {
            hideOtherField(otherField);
        }
    }
}

// Gestion spécifique pour les select
function handleOtherSelectOption(select) {
    if (select.value.toLowerCase() === 'autre') {
        const fieldGroup = select.closest('.form-group');
        if (!fieldGroup) return;

        const fieldKey = select.name;
        const otherFieldKey = fieldKey + '_other';

        let otherField = fieldGroup.querySelector('.other-field');
        if (!otherField) {
            otherField = fieldGroup.querySelector(`[data-other-field="${otherFieldKey}"]`);
        }

        if (!otherField) {
            otherField = createOtherField(fieldKey, otherFieldKey);
            fieldGroup.appendChild(otherField);
        }

        showOtherField(otherField);
    } else {
        const fieldGroup = select.closest('.form-group');
        const otherField = fieldGroup?.querySelector('.other-field');
        if (otherField) {
            hideOtherField(otherField);
        }
    }
}

// Extraire la clé du champ à partir du nom de la checkbox
function getFieldKeyFromCheckbox(checkbox) {
    // Si le nom se termine par [], on retire cette partie
    let fieldKey = checkbox.name;
    if (fieldKey.endsWith('[]')) {
        fieldKey = fieldKey.slice(0, -2);
    }
    return fieldKey;
}

// Créer dynamiquement un champ "autre"
/* function createOtherField(baseFieldKey, otherFieldKey) {
    const otherFieldDiv = document.createElement('div');
    otherFieldDiv.className = 'other-field';
    otherFieldDiv.setAttribute('data-other-field', otherFieldKey);
    otherFieldDiv.style.display = 'none';
    otherFieldDiv.style.marginTop = '1rem';
    otherFieldDiv.style.padding = '1rem';
    otherFieldDiv.style.background = 'linear-gradient(135deg, var(--light-primary), #f8f9fa)';
    otherFieldDiv.style.border = '1px solid #e9ecef';
    otherFieldDiv.style.borderRadius = '8px';
    otherFieldDiv.style.borderLeft = '4px solid var(--primary-color)';
    otherFieldDiv.style.transition = 'all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
    otherFieldDiv.style.boxShadow = '0 2px 8px rgba(0,0,0,0.05)';

    // Créer le label
    const label = document.createElement('label');
    label.setAttribute('for', otherFieldKey);
    label.textContent = 'Précisez votre choix :';
    label.style.fontWeight = '600';
    label.style.color = 'var(--primary-color)';
    label.style.marginBottom = '0.5rem';
    label.style.display = 'block';
    label.style.fontSize = '0.9rem';

    // Créer l'input
    const input = document.createElement('input');
    input.type = 'text';
    input.id = otherFieldKey;
    input.name = otherFieldKey;
    input.placeholder = getPlaceholderForField(baseFieldKey);
    input.style.width = '100%';
    input.style.padding = '0.7rem';
    input.style.border = '1px solid #ced4da';
    input.style.borderRadius = '6px';
    input.style.fontSize = '0.95rem';
    input.style.background = 'white';
    input.style.boxShadow = 'inset 0 1px 3px rgba(0,0,0,0.05)';
    input.style.transition = 'all 0.3s ease';
    input.maxLength = 100;

    // Ajouter les événements de validation
    input.addEventListener('focus', function() {
        this.style.borderColor = 'var(--primary-color)';
        this.style.boxShadow = 'inset 0 1px 3px rgba(0,0,0,0.05), 0 0 0 3px rgba(29, 134, 217, 0.1)';
        this.style.transform = 'translateY(-1px)';
    });

    input.addEventListener('blur', function() {
        this.style.borderColor = '#ced4da';
        this.style.boxShadow = 'inset 0 1px 3px rgba(0,0,0,0.05)';
        this.style.transform = 'translateY(0)';
    });

    otherFieldDiv.appendChild(label);
    otherFieldDiv.appendChild(input);

    return otherFieldDiv;
} */

function createOtherField(baseFieldKey, otherFieldKey) {
    const otherFieldDiv = document.createElement('div');
    otherFieldDiv.className = 'other-field';
    otherFieldDiv.setAttribute('data-other-field', otherFieldKey);
    otherFieldDiv.setAttribute('data-base-field', baseFieldKey); // AJOUT
    otherFieldDiv.style.display = 'none';

    // Styles...
    otherFieldDiv.style.marginTop = '1rem';
    otherFieldDiv.style.padding = '1rem';
    otherFieldDiv.style.background = 'linear-gradient(135deg, var(--light-primary), #f8f9fa)';
    otherFieldDiv.style.border = '1px solid #e9ecef';
    otherFieldDiv.style.borderRadius = '8px';
    otherFieldDiv.style.borderLeft = '4px solid var(--primary-color)';

    const label = document.createElement('label');
    label.setAttribute('for', otherFieldKey);
    label.textContent = 'Précisez votre choix :';
    label.style.fontWeight = '600';
    label.style.color = 'var(--primary-color)';
    label.style.marginBottom = '0.5rem';
    label.style.display = 'block';

    const input = document.createElement('input');
    input.type = 'text';
    input.id = otherFieldKey;
    input.name = otherFieldKey;
    input.placeholder = getPlaceholderForField(baseFieldKey);
    input.className = 'other-field-input'; // AJOUT: classe spéciale
    input.setAttribute('data-is-other-field', 'true'); // AJOUT: attribut pour identification

    // Styles de l'input...
    input.style.width = '100%';
    input.style.padding = '0.7rem';
    input.style.border = '1px solid #ced4da';
    input.style.borderRadius = '6px';

    otherFieldDiv.appendChild(label);
    otherFieldDiv.appendChild(input);

    return otherFieldDiv;
}

// Obtenir le placeholder approprié selon le type de champ
function getPlaceholderForField(fieldKey) {
    const placeholders = {
        'breakfast_preference': 'Ex: Croissant et chocolat chaud',
        'lunch_preference': 'Ex: Salade de fruits et poisson grillé',
        'dinner_preference': 'Ex: Pizza aux légumes',
        'business_sector': 'Ex: Votre secteur d\'activité',
        'networking_interests': 'Ex: Votre type de partenariat souhaité'
    };

    return placeholders[fieldKey] || 'Précisez votre choix';
}

// Afficher le champ "autre"
/* function showOtherField(otherField) {
    if (!otherField) return;

    console.log('✅ Affichage du champ "autre"');

    otherField.style.display = 'block';

    // Animation d'apparition
    otherField.style.opacity = '0';
    otherField.style.transform = 'translateY(-10px)';

    setTimeout(() => {
        otherField.style.opacity = '1';
        otherField.style.transform = 'translateY(0)';
    }, 10);

    // Optionnel: focus sur l'input après l'animation
    const input = otherField.querySelector('input');
    if (input) {
        setTimeout(() => {
            input.focus();
        }, 350);
    }
} */

function showOtherField(otherField) {
    if (!otherField) return;

    console.log('✅ Affichage du champ "autre"');

    otherField.style.display = 'block';
    otherField.classList.add('visible'); // AJOUT: classe pour identification

    // Animation
    otherField.style.opacity = '0';
    otherField.style.transform = 'translateY(-10px)';

    setTimeout(() => {
        otherField.style.opacity = '1';
        otherField.style.transform = 'translateY(0)';
    }, 10);

    // Focus sur l'input
    const input = otherField.querySelector('input');
    if (input) {
        setTimeout(() => {
            input.focus();
        }, 350);
    }
}

// Masquer le champ "autre"
/* function hideOtherField(otherField) {
    if (!otherField) return;

    console.log('❌ Masquage du champ "autre"');

    // Animation de disparition
    otherField.style.opacity = '0';
    otherField.style.transform = 'translateY(-10px)';

    setTimeout(() => {
        otherField.style.display = 'none';

        // Vider la valeur de l'input
        const input = otherField.querySelector('input');
        if (input) {
            input.value = '';
            input.required = false;
        }
    }, 300);
} */

function hideOtherField(otherField) {
    if (!otherField) return;

    console.log('❌ Masquage du champ "autre"');

    otherField.classList.remove('visible'); // AJOUT

    // Animation
    otherField.style.opacity = '0';
    otherField.style.transform = 'translateY(-10px)';

    setTimeout(() => {
        otherField.style.display = 'none';

        // Vider la valeur
        const input = otherField.querySelector('input');
        if (input) {
            input.value = '';
            input.required = false;
        }
    }, 300);
}

function collectFormDataSimple(form) {
    const formData = new FormData();

    // Token CSRF
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        formData.append('_token', csrfToken.getAttribute('content'));
    }

    // 1. Collecter les champs normaux (sauf les champs "autres")
    const normalFields = form.querySelectorAll('input:not([data-is-other-field]), select, textarea');
    normalFields.forEach(field => {
        if (!field.name) return;

        // Radio et checkbox : seulement si cochés
        if (field.type === 'radio' || field.type === 'checkbox') {
            if (field.checked) {
                // Pour les checkbox arrays, on les traite séparément
                if (!field.name.endsWith('[]')) {
                    formData.append(field.name, field.value);
                    console.log(`📝 ${field.type} ajouté: ${field.name} = "${field.value}"`);
                }
            }
        }
        // Autres champs : si ils ont une valeur
        else if (field.value.trim() !== '') {
            formData.append(field.name, field.value);
            console.log(`📝 Champ normal: ${field.name} = "${field.value}"`);
        }
    });

    // 2. COLLECTE SPÉCIALE pour les champs "autres" VISIBLES
    const otherFields = document.querySelectorAll('.other-field.visible input, .other-field[style*="block"] input');
    console.log(`🔍 Champs "autres" trouvés: ${otherFields.length}`);

    otherFields.forEach(otherInput => {
        if (otherInput.name) {
            formData.append(otherInput.name, otherInput.value || '');
            console.log(`🎯 CHAMP AUTRE ajouté: ${otherInput.name} = "${otherInput.value}"`);
        }
    });

    // 3. Collecter les checkbox arrays (traitement séparé)
    const checkboxGroups = {};
    form.querySelectorAll('input[type="checkbox"]:checked').forEach(cb => {
        if (cb.name.endsWith('[]')) {
            const baseName = cb.name.slice(0, -2);
            if (!checkboxGroups[baseName]) checkboxGroups[baseName] = [];
            checkboxGroups[baseName].push(cb.value);
        }
    });

    // Ajouter les checkbox arrays au FormData
    Object.keys(checkboxGroups).forEach(fieldName => {
        checkboxGroups[fieldName].forEach(value => {
            formData.append(fieldName + '[]', value);
        });
        console.log(`☑️ Checkbox array: ${fieldName}[] = [${checkboxGroups[fieldName].join(', ')}]`);
    });

    return formData;
}


        function handleOtherOption(element) {
            const fieldGroup = element.closest('.form-group');
            if (!fieldGroup) return;

            const otherField = fieldGroup.querySelector('.other-field');

            if (otherField) {
                if (element.value === 'other' || element.value === 'autres') {
                    otherField.classList.add('show');
                    const otherInput = otherField.querySelector('input');
                    if (otherInput) otherInput.required = true;
                } else {
                    otherField.classList.remove('show');
                    const otherInput = otherField.querySelector('input');
                    if (otherInput) {
                        otherInput.required = false;
                        otherInput.value = '';
                    }
                }
            }
        }

        function initializeValidation() {
            // Validation en temps réel des champs
            document.querySelectorAll('input, textarea, select').forEach(input => {
                input.addEventListener('blur', function() {
                    validateField(this);
                });

                input.addEventListener('input', function() {
                    clearFieldError(this);
                });
            });
        }

        function validateField(field) {
            const value = field.value.trim();

            // Supprimer les erreurs précédentes
            clearFieldError(field);

            // Validation selon le type de champ
            if (field.required && !value) {
                showFieldError(field, 'Ce champ est obligatoire');
                return false;
            }

            if (field.type === 'email' && value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    showFieldError(field, 'Format d\'email invalide');
                    return false;
                }
            }

            if (field.type === 'tel' && value) {
                const phoneRegex = /^[0-9+\s-]{8,15}$/;
                if (!phoneRegex.test(value)) {
                    showFieldError(field, 'Format de téléphone invalide');
                    return false;
                }
            }

            if (field.hasAttribute('pattern') && value) {
                const pattern = new RegExp(field.getAttribute('pattern'));
                if (!pattern.test(value)) {
                    showFieldError(field, 'Format invalide');
                    return false;
                }
            }

            return true;
        }

        function showFieldError(field, message) {
    if (!field) return;

    const fieldGroup = field.closest('.form-group');
    if (!fieldGroup) return;

    fieldGroup.classList.add('has-error');

    let errorElement = fieldGroup.querySelector('.field-error');
    if (!errorElement) {
        errorElement = document.createElement('div');
        errorElement.className = 'field-error';
        errorElement.style.color = '#e53935';
        errorElement.style.fontSize = '0.85rem';
        errorElement.style.marginTop = '0.5rem';
        errorElement.style.padding = '0.5rem';
        errorElement.style.background = 'rgba(229, 57, 53, 0.1)';
                errorElement.style.borderRadius = '4px';
                errorElement.style.border = '1px solid rgba(229, 57, 53, 0.3)';
                errorElement.style.animation = 'slideIn 0.3s ease-out';

                // Trouver le bon endroit pour insérer l'erreur
                const insertAfter = fieldGroup.querySelector('.checkbox-group') ||
                                fieldGroup.querySelector('.radio-group') ||
                                field;
                insertAfter.parentNode.insertBefore(errorElement, insertAfter.nextSibling);
            }
            errorElement.textContent = message;
        }

        function clearFieldError(field) {
            if (!field) return;

            const fieldGroup = field.closest('.form-group');
            if (!fieldGroup) return;

            fieldGroup.classList.remove('has-error');

            const errorElement = fieldGroup.querySelector('.field-error');
            if (errorElement) {
                errorElement.remove();
            }
        }

        function selectTicket(ticketId) {
            // Supprimer la classe selected de toutes les options
            document.querySelectorAll('.radio-option').forEach(option => {
                option.classList.remove('selected');
            });

            // Trouver l'option correspondante et lui ajouter la classe selected
            const radioButton = document.getElementById('ticket-' + ticketId);
            if (radioButton) {
                // Sélectionner le radio button
                radioButton.checked = true;

                // Ajouter la classe selected à l'option parente
                const radioOption = radioButton.closest('.radio-option');
                if (radioOption) {
                    radioOption.classList.add('selected');
                }

                // Déclencher l'événement change pour que les autres listeners soient notifiés
                radioButton.dispatchEvent(new Event('change'));

                // Afficher la section des options de paiement si l'événement permet le paiement partiel ou la réservation
                const paymentOptionsSection = document.getElementById('paymentOptionsSection');
                if (paymentOptionsSection) {
                    const requiresPayment = {{ $currentEvent->requires_payment ? 'true' : 'false' }};
                    const allowPartialPayment = {{ $currentEvent->allow_partial_payment ?? false ? 'true' : 'false' }};
                    const allowReservation = {{ $currentEvent->allow_reservation ?? false ? 'true' : 'false' }};

                    if (requiresPayment && (allowPartialPayment || allowReservation)) {
                        paymentOptionsSection.style.display = 'block';
                    } else {
                        paymentOptionsSection.style.display = 'none';
                    }
                }

                console.log('✅ Ticket sélectionné:', ticketId, 'Radio checked:', radioButton.checked);
            }
        }

        function selectCheckbox(element) {
            const checkbox = element.querySelector('input[type="checkbox"]');
            if (!checkbox) return;

            // Vérifier la limite de sélections avant de cocher
            const fieldGroup = element.closest('.form-group');
            const checkboxGroup = element.closest('.checkbox-group');
            const maxSelections = checkboxGroup?.getAttribute('data-max-selections');

            if (maxSelections && !checkbox.checked) {
                const checkedBoxes = checkboxGroup.querySelectorAll('input[type="checkbox"]:checked');

                if (checkedBoxes.length >= parseInt(maxSelections)) {
                    showFieldError(fieldGroup?.querySelector('input') || checkbox,
                        `Vous ne pouvez sélectionner que ${maxSelections} option(s) maximum.`);

                    setTimeout(() => {
                        clearFieldError(fieldGroup?.querySelector('input') || checkbox);
                    }, 3000);

                    return; // Empêcher la sélection
                }
            }

            // Inverser l'état de la checkbox
            checkbox.checked = !checkbox.checked;

            // Mettre à jour l'apparence visuelle
            if (checkbox.checked) {
                element.classList.add('selected');
            } else {
                element.classList.remove('selected');
            }

            // Déclencher l'événement change pour gérer les champs "autre"
            const changeEvent = new Event('change', { bubbles: true });
            checkbox.dispatchEvent(changeEvent);
        }


        function checkPaymentStatus() {
            const urlParams = new URLSearchParams(window.location.search);
            const paymentStatus = urlParams.get('payment');
            const paymentRef = urlParams.get('ref');

            if (paymentStatus === 'success') {
                console.log('✅ Paiement réussi - Affichage du modal de succès');

                const eventTitle = eventData.title || 'cet événement';

                const successMessage = `🎉 Félicitations ! Votre paiement a été effectué avec succès.

                Votre ticket électronique vous a été envoyé par email et WhatsApp.`;

                showModal('successModal', successMessage);
                cleanUrlAndResetForm();

            } else if (paymentStatus === 'error') {
                console.log('❌ Erreur de paiement - Affichage du modal d\'erreur');

                showModal('errorModal', [
                    '❌ Le paiement n\'a pas pu être traité.',
                    '',
                    'Causes possibles :',
                    '• Solde insuffisant sur votre compte',
                    '• Problème de connexion réseau',
                    '• Transaction annulée ou expirée',
                    '• Informations de paiement incorrectes',
                    '',
                    '🔄 Solutions :',
                    '• Vérifiez votre solde',
                    '• Réessayez dans quelques minutes',
                    '• Contactez notre support si le problème persiste',
                    '',
                    paymentRef ? `Référence d'erreur: ${paymentRef}` : ''
                ]);

                window.history.replaceState({}, document.title, window.location.pathname);
            }
        }

        function showModal(modalId, messages) {
            console.log(`🎯 Affichage du modal: ${modalId}`);

            const modal = document.getElementById(modalId);
            const messageElement = document.getElementById(modalId + 'Message');

            if (!modal) {
                console.error(`❌ Modal ${modalId} non trouvé`);
                const text = Array.isArray(messages) ? messages.join('\n') : messages;
                alert(text);
                return;
            }

            if (messageElement) {
                messageElement.innerHTML = '';

                if (modalId === 'successModal') {
                    messageElement.style.whiteSpace = 'pre-line';
                    messageElement.textContent = messages;
                } else if (modalId === 'errorModal') {
                    if (Array.isArray(messages)) {
                        const ul = document.createElement('ul');
                        ul.style.margin = '0';
                        ul.style.paddingLeft = '1.2rem';

                        messages.forEach(msg => {
                            if (msg.trim() === '') {
                                const br = document.createElement('br');
                                ul.appendChild(br);
                            } else {
                                const li = document.createElement('li');
                                li.textContent = msg;
                                ul.appendChild(li);
                            }
                        });

                        messageElement.appendChild(ul);
                    } else {
                        messageElement.textContent = messages;
                    }
                }
            }

            modal.classList.add('show');
            console.log(`✅ Modal ${modalId} affiché`);
        }

        function closeModal(modalId) {
            console.log(`🔒 Fermeture du modal: ${modalId}`);

            const modal = document.getElementById(modalId);

            if (modal) {
                modal.classList.remove('show');
                console.log(`✅ Modal ${modalId} fermé`);

                // Vider le formulaire si c'est le modal de succès
                if (modalId === 'successModal') {
                    clearForm();
                }
            }
        }

        function clearForm() {
            console.log('🧹 Vidage du formulaire...');

            const form = document.getElementById('dynamicForm');
            if (!form) {
                console.warn('⚠️ Formulaire non trouvé');
                return;
            }

            // Vider tous les champs de saisie
            const inputs = form.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                if (input.type === 'checkbox' || input.type === 'radio') {
                    input.checked = false;
                } else if (input.type === 'file') {
                    input.value = '';
                } else {
                    input.value = '';
                }
            });

            // Réinitialiser les sélecteurs multiples (Choices.js)
            const choiceElements = form.querySelectorAll('.choices');
            choiceElements.forEach(choiceElement => {
                if (choiceElement._choices) {
                    choiceElement._choices.removeActiveItems();
                }
            });

            // Réinitialiser les messages d'erreur
            const errorElements = form.querySelectorAll('.error-message, .field-error');
            errorElements.forEach(errorElement => {
                errorElement.remove();
            });

            // Réinitialiser les classes d'erreur
            const errorFields = form.querySelectorAll('.field-error, .error');
            errorFields.forEach(field => {
                field.classList.remove('field-error', 'error');
            });

            console.log('✅ Formulaire vidé avec succès');
        }

        function initializeModals() {
            console.log('🔧 Configuration des événements de modal...');

            window.addEventListener('click', function(event) {
                if (event.target.classList.contains('modal')) {
                    closeModal(event.target.id);
                }
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    const visibleModal = document.querySelector('.modal.show');
                    if (visibleModal) {
                        closeModal(visibleModal.id);
                    }
                }
            });

            console.log('✅ Événements de modal configurés');
        }

        function initializePaymentOptions() {
            // Afficher la section des options de paiement si un ticket est déjà sélectionné
            const selectedTicket = document.querySelector('input[name="ticket_type_id"]:checked');
            if (selectedTicket) {
                const ticketId = selectedTicket.value;
                selectTicket(ticketId);
            }

            // Ajouter des écouteurs d'événements sur tous les boutons radio de tickets
            const ticketRadios = document.querySelectorAll('input[name="ticket_type_id"]');
            ticketRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.checked) {
                        selectTicket(this.value);
                    }
                });
            });
        }

        function cleanUrlAndResetForm() {
            window.history.replaceState({}, document.title, window.location.pathname);

            setTimeout(() => {
                const form = document.querySelector('form');
                if (form) {
                    form.reset();

                    const inputs = form.querySelectorAll('input, select, textarea');
                    inputs.forEach(input => {
                        clearFieldError(input);
                    });

                    const radioOptions = form.querySelectorAll('.radio-option');
                    radioOptions.forEach(option => {
                        option.classList.remove('selected');
                    });

                    const checkboxOptions = form.querySelectorAll('.checkbox-option');
                    checkboxOptions.forEach(option => {
                        option.classList.remove('selected');
                    });
                }
            }, 4000);
        }

        function setupMobileButton() {
            const mobileBtn = document.getElementById('mobile-register-btn');
            if (mobileBtn) {
                mobileBtn.addEventListener('click', function() {
                    const formSection = document.getElementById('form-section');
                    if (formSection) {
                        formSection.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });

                        setTimeout(() => {
                            const firstInput = formSection.querySelector('input[type="text"], input[type="email"]');
                            if (firstInput) {
                                firstInput.focus();
                            }
                        }, 500);
                    }
                });
            }
        }

        // Remplacez la fonction setupFormSubmission() dans votre fichier HTML par cette version corrigée

        /* function setupFormSubmission() {
            const form = document.querySelector('form');

            if (!form) {
                console.error('Formulaire non trouvé');
                return;
            }

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Valider tous les champs avant soumission
                let isValid = true;
                const requiredFields = form.querySelectorAll('[required]');

                requiredFields.forEach(field => {
                    if (!validateField(field)) {
                        isValid = false;
                    }
                });

                if (!isValid) {
                    showModal('errorModal', ['Veuillez corriger les erreurs dans le formulaire avant de continuer.']);
                    return;
                }

                // Collecter les données du formulaire dynamique
                const formData = new FormData();

                // Ajouter le token CSRF
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (csrfToken) {
                    formData.append('_token', csrfToken.getAttribute('content'));
                }

                // Collecter toutes les données du formulaire
                const inputs = form.querySelectorAll('input, select, textarea');
                const checkboxArrays = {}; // Pour gérer les checkbox groups

                inputs.forEach(input => {
                    if (!input.name) return; // Ignorer les champs sans nom

                    if (input.type === 'radio') {
                        if (input.checked) {
                            formData.append(input.name, input.value);
                        }
                    }
                    else if (input.type === 'checkbox') {
                        if (input.checked) {
                            // Gérer les checkbox arrays (comme breakfast_preference[])
                            if (input.name.endsWith('[]')) {
                                const baseName = input.name.slice(0, -2); // Enlever []
                                if (!checkboxArrays[baseName]) {
                                    checkboxArrays[baseName] = [];
                                }
                                checkboxArrays[baseName].push(input.value);
                            } else {
                                formData.append(input.name, input.value);
                            }
                        }
                    }
                    else if (input.value !== '') {
                        // Traitement spécial pour les champs téléphone
                        if (input.type === 'tel' && input.hasAttribute('data-country-field')) {
                            const countryFieldId = input.getAttribute('data-country-field');
                            const countrySelect = document.getElementById(countryFieldId);

                            if (countrySelect && countrySelect.value) {
                                const countryCode = countrySelect.value;
                                let phoneValue = input.value;

                                // Nettoyer et formater le numéro
                                let cleanPhone = phoneValue.replace(/^\+\d{1,4}/, ''); // Supprimer ancien code
                                cleanPhone = cleanPhone.replace(/[^\d]/g, ''); // Garder seulement les chiffres

                                if (cleanPhone) {
                                    const formattedPhone = countryCode + cleanPhone;
                                    console.log(`📞 Formatage téléphone: ${phoneValue} -> ${formattedPhone}`);
                                    formData.append(input.name, formattedPhone);

                                    // IMPORTANT: Ajouter le code pays séparément pour le PHP
                                    formData.append(input.name + '_country', countryCode);
                                }
                            } else {
                                formData.append(input.name, input.value);
                            }
                        } else {
                            formData.append(input.name, input.value);
                        }
                    }
                });

                // Ajouter les checkbox arrays au FormData
                Object.keys(checkboxArrays).forEach(fieldName => {
                    const values = checkboxArrays[fieldName];
                    values.forEach(value => {
                        formData.append(fieldName + '[]', value);
                    });
                });

                // Log pour debug
                console.log('🔍 Données du formulaire collectées:');
                for (let [key, value] of formData.entries()) {
                    console.log(`${key}: ${value}`);
                }

                const url = form.action;

                // Désactiver le bouton de soumission
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                submitBtn.disabled = true;
                submitBtn.textContent = 'Traitement en cours...';

                fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    console.log('📡 Statut de la réponse:', response.status);

                    if (response.ok) {
                        return response.json();
                    } else if (response.status === 422) {
                        return response.json().then(errors => {
                            throw errors;
                        });
                    } else if (response.status >= 500) {
                        // Erreur serveur - essayer de lire le texte de l'erreur
                        return response.text().then(text => {
                            console.error('❌ Erreur serveur (500):', text);
                            throw new Error('Erreur serveur - Veuillez contacter le support');
                        });
                    } else {
                        throw new Error(`Erreur HTTP ${response.status}`);
                    }
                })
                .then(data => {
                    console.log('✅ Réponse reçue:', data);

                    if (data.success) {
                        if (data.free_ticket) {
                            showModal('successModal', data.message);
                            // Vider le formulaire après un délai pour permettre à l'utilisateur de voir le message
                            setTimeout(() => {
                                clearForm();
                            }, 2000);
                        } else if (data.requires_payment) {
                            const paymentData = data.payment_data;

                            if (!paymentData) {
                                console.error('❌ Aucune donnée de paiement trouvée');
                                showModal('errorModal', ['Erreur: données de paiement manquantes']);
                                return;
                            }

                            console.log('💳 Redirection vers paiement avec:', paymentData);
                            submitBtn.textContent = 'Redirection vers le paiement...';
                            redirectToPayment(paymentData);
                            return;
                        }
                    }
                })
                .catch(error => {
                    console.error('❌ Erreur complète:', error);

                    if (error.errors) {
                        // Erreurs de validation (422)
                        const validationErrors = error.errors;
                        let errorMessages = [];
                        for (const [field, messages] of Object.entries(validationErrors)) {
                            errorMessages.push(`${field}: ${messages.join(', ')}`);
                        }
                        showModal('errorModal', errorMessages);
                    } else {
                        // Autres erreurs
                        const errorMessage = error.message || 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer.';
                        showModal('errorModal', [errorMessage]);
                    }
                })
                .finally(() => {
                    if (submitBtn.textContent !== 'Redirection vers le paiement...') {
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalText;
                    }
                });
            });
        } */

        function setupFormSubmission() {
    const form = document.querySelector('form');

    if (!form) {
        console.error('Formulaire non trouvé');
        return;
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        console.log('🚀 SOUMISSION AVEC CHAMPS DB');

        // Vérifier et sélectionner un ticket si nécessaire
        const ticketRadios = form.querySelectorAll('input[name="ticket_type_id"]');
        let ticketSelected = false;

        ticketRadios.forEach(radio => {
            if (radio.checked) {
                ticketSelected = true;
            }
        });

        // Si aucun ticket n'est sélectionné et qu'il y a des tickets disponibles
        if (!ticketSelected && ticketRadios.length > 0) {
            // Sélectionner le premier ticket disponible
            const firstTicket = ticketRadios[0];
            firstTicket.checked = true;

            // Appeler selectTicket pour mettre à jour l'interface
            const ticketId = firstTicket.value;
            selectTicket(ticketId);

            console.log('⚠️ Aucun ticket sélectionné, sélection automatique du premier:', ticketId);
        }

        // Validation
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (field.type === 'radio') {
                // Pour les radios, vérifier si au moins un est coché
                const radioGroup = form.querySelectorAll(`input[name="${field.name}"]`);
                const isChecked = Array.from(radioGroup).some(radio => radio.checked);
                if (!isChecked) {
                    showFieldError(field, 'Ce champ est obligatoire');
                    isValid = false;
                }
            } else if (!field.value.trim()) {
                showFieldError(field, 'Ce champ est obligatoire');
                isValid = false;
            }
        });

        if (!isValid) {
            showModal('errorModal', ['Veuillez remplir tous les champs obligatoires.']);
            return;
        }

        // UTILISER LA NOUVELLE FONCTION DE COLLECTE
        const formData = collectFormDataEnhanced(form);

        // Vérifier que ticket_type_id est bien présent
        const ticketTypeId = formData.get('ticket_type_id');
        if (!ticketTypeId) {
            console.error('❌ ticket_type_id manquant dans les données collectées');
            showModal('errorModal', ['Veuillez sélectionner un type de ticket.']);
            return;
        }

        console.log('✅ ticket_type_id présent:', ticketTypeId);

        // Soumettre
        submitForm(form, formData);
    });
}


function submitForm(form, formData) {
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Traitement en cours...';

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (response.ok) {
            return response.json();
        } else if (response.status === 422) {
            return response.json().then(errors => { throw errors; });
        } else {
            throw new Error(`Erreur HTTP ${response.status}`);
        }
    })
    .then(data => {
        console.log('✅ Réponse:', data);
        if (data.success) {
            if (data.free_ticket) {
                showModal('successModal', data.message);
                // Vider le formulaire après un délai pour permettre à l'utilisateur de voir le message
                setTimeout(() => {
                    clearForm();
                }, 2000);
            } else if (data.requires_payment) {
                submitBtn.textContent = 'Redirection vers le paiement...';
                redirectToPayment(data.payment_data);
            }
        }
    })
    .catch(error => {
        console.error('❌ Erreur:', error);
        if (error.errors) {
            const messages = Object.values(error.errors).flat();
            showModal('errorModal', messages);
        } else {
            showModal('errorModal', [error.message || 'Erreur lors de l\'inscription']);
        }
    })
    .finally(() => {
        if (submitBtn.textContent !== 'Redirection vers le paiement...') {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });
}
        function redirectToPayment(paymentData) {
            try {
                console.log('Début de redirection avec:', paymentData);

                // Utiliser les variables routeData directement (plus fiable que parser l'URL)
                const orgSlug = routeData.orgSlug;
                const eventSlug = routeData.eventSlug;

                if (!orgSlug || !eventSlug) {
                    console.error('Slugs manquants:', { orgSlug, eventSlug });
                    throw new Error('Données de route manquantes');
                }

                const form = document.createElement('form');
                form.method = 'POST';

                // Utiliser l'URL générée par Laravel (prend en compte les sous-dossiers automatiquement)
                if (routeData.paymentUrl) {
                    form.action = routeData.paymentUrl;
                } else {
                    // Fallback si l'URL n'est pas disponible
                    form.action = `/${orgSlug}/${eventSlug}/validation-paiement`;
                }
                form.style.display = 'none';

                console.log('URL de redirection:', form.action);
                console.log('Slugs utilisés:', { orgSlug, eventSlug });
                console.log('RouteData:', routeData);
                console.log('Window location:', {
                    pathname: window.location.pathname,
                    origin: window.location.origin,
                    href: window.location.href
                });

                // Transférer TOUTES les données, y compris les codes pays
                Object.keys(paymentData).forEach(key => {
                    if (paymentData[key] !== null && paymentData[key] !== undefined) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = key;
                        input.value = String(paymentData[key]);
                        form.appendChild(input);
                        console.log(`✓ ${key}: ${paymentData[key]}`);
                    }
                });

                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (csrfToken) {
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken.getAttribute('content');
                    form.appendChild(csrfInput);
                }

                console.log('Soumission du formulaire...');

                document.body.appendChild(form);
                form.submit();

            } catch (error) {
                console.error('Erreur redirection:', error);

                const submitBtn = document.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'S\'inscrire maintenant';
                }

                showModal('errorModal', [
                    'Erreur lors de la redirection vers le paiement.',
                    'Veuillez réessayer.'
                ]);
            }
        }

        function updatePhoneCountry(fieldId) {
            const phoneInput = document.getElementById(fieldId);
            const countrySelect = document.getElementById(fieldId + '_country');

            if (!phoneInput || !countrySelect) return;

            const newCountryCode = countrySelect.value;
            const currentPhone = phoneInput.value;

            console.log('Changement de code pays:', newCountryCode);
            console.log('Numéro actuel:', currentPhone);

            // Si le champ téléphone n'est pas vide, nettoyer et reformater
            if (currentPhone) {
                // Supprimer l'ancien code pays s'il existe
                let cleanPhone = currentPhone;

                // Supprimer tout code pays existant au début
                cleanPhone = cleanPhone.replace(/^\+\d{1,4}/, '');

                // Supprimer espaces, tirets, etc.
                cleanPhone = cleanPhone.replace(/[^\d]/g, '');

                // Ne mettre à jour que si on a un numéro valide
                if (cleanPhone) {
                    console.log('Numéro nettoyé:', cleanPhone);
                    console.log('Nouveau numéro formaté:', newCountryCode + cleanPhone);
                }
            }
        }

        function formatPhoneForSubmission() {
            // Parcourir tous les champs téléphone avec sélecteur de pays
            document.querySelectorAll('input[data-country-field]').forEach(phoneInput => {
                const countryFieldId = phoneInput.getAttribute('data-country-field');
                const countrySelect = document.getElementById(countryFieldId);

                if (countrySelect) {
                    const countryCode = countrySelect.value;
                    const phoneValue = phoneInput.value;

                    if (phoneValue) {
                        // Nettoyer le numéro
                        let cleanPhone = phoneValue.replace(/^\+\d{1,4}/, ''); // Supprimer ancien code pays
                        cleanPhone = cleanPhone.replace(/[^\d]/g, ''); // Garder seulement les chiffres

                        // Formatter avec le nouveau code pays
                        if (cleanPhone) {
                            const formattedPhone = countryCode + cleanPhone;
                            console.log(`Formatage: ${phoneValue} -> ${formattedPhone}`);
                            // Note: On ne modifie pas la valeur ici pour éviter de perturber l'utilisateur
                            // Le formatage se fera côté serveur
                        }
                    }
                }
            });
        }

        function handleLaravelValidationErrors() {
            @if($errors->any())
                const errors = @json($errors->all());
                showModal('errorModal', errors);
            @endif

            @if(session('success'))
                showModal('successModal', @json(session('success')));
            @endif
        }
    </script>
</body>
</html>
