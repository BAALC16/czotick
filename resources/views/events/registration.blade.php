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

        /* Event Info Panel - Utilise la couleur primaire de l'événement */
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
        }

        .event-logo {
            max-width: 200px;
            margin-bottom: 2rem;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            padding: 1rem;
        }

        .event-title {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
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
        }

        .event-detail {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            text-align: left;
        }

        .detail-icon {
            margin-right: 1rem;
            font-size: 1.2rem;
            min-width: 30px;
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
            display: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .register-btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }

        /* Form Panel */
        .form-panel {
            flex: 1;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background-color: white;
            overflow-y: auto;
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
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

        form {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 1.5rem;
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

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="tel"]:focus,
        select:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(29, 134, 217, 0.1);
        }

        .radio-group {
            margin-top: 0.5rem;
        }

        .radio-option {
            display: flex;
            align-items: flex-start;
            margin-bottom: 0.8rem;
            padding: 1rem;
            border: 1px solid #eee;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .radio-option:hover {
            border-color: var(--primary-color);
            background-color: var(--light-primary);
        }

        .radio-option.selected {
            border-color: var(--primary-color);
            background-color: var(--light-primary);
        }

        input[type="radio"] {
            margin-right: 10px;
            margin-top: 2px;
            cursor: pointer;
        }

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
            margin-top: 1rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            margin: auto;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: var(--shadow);
            width: 90%;
            max-width: 500px;
            position: relative;
            transform: translateY(0);
            animation: modalAppear 0.3s ease;
        }

        @keyframes modalAppear {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-content h2 {
            margin-bottom: 1rem;
            color: var(--primary-color);
        }

        .close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 1.5rem;
            color: #aaa;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .close:hover {
            color: #333;
        }

        .modal-buttons-container {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .modal-button {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            flex: 1;
        }

        .primary-button {
            background-color: var(--primary-color);
            color: white;
        }

        .primary-button:hover {
            background-color: var(--dark-primary);
        }

        .secondary-button {
            background-color: var(--secondary-color);
            color: white;
        }

        .secondary-button:hover {
            opacity: 0.9;
        }

        .modal-button-error {
            background-color: #fa4c04;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 1rem;
        }

        .modal-button-error:hover {
            background-color: #e63946;
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
            }

            .register-btn {
                display: inline-block;
                margin-top: 1rem;
            }

            .form-panel {
                padding: 2rem 1rem;
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

            .modal-buttons-container {
                flex-direction: column;
            }

            .logos-container {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Event Information Panel -->
        <div class="event-panel">
            <div class="event-content">
                @if($currentOrganization->organization_logo)
                <img src="{{ url('public/' . $currentOrganization->organization_logo) }}" alt="Logo {{ $currentOrganization->org_name }}" class="event-logo">
                @endif

                <h1 class="event-title">{{ $currentEvent->event_title }}</h1>
                <h2 class="event-subtitle">{{ $currentOrganization->org_name }}</h2>

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
                    <div class="event-detail">
                        <span class="detail-icon">📅</span>
                        <span>{{ $currentEvent->event_date->format('d/m/Y') }}</span>
                    </div>

                    @if($currentEvent->event_start_time)
                    <div class="event-detail">
                        <span class="detail-icon">🕒</span>
                        <span>{{ $currentEvent->event_start_time->format('H:i') }}
                        @if($currentEvent->event_end_time)
                            - {{ $currentEvent->event_end_time->format('H:i') }}
                        @endif
                        </span>
                    </div>
                    @endif

                    @if($currentEvent->event_location)
                    <div class="event-detail">
                        <span class="detail-icon">📍</span>
                        <span>{{ $currentEvent->event_location }}</span>
                    </div>
                    @endif

                    @if($currentEvent->event_description)
                    <div class="event-detail">
                        <span class="detail-icon">🎯</span>
                        <span>{{ \Str::limit($currentEvent->event_description, 150) }}</span>
                    </div>
                    @endif

                    @if($currentEvent->dress_code_general)
                    <div class="event-detail">
                        <span class="detail-icon">👗</span>
                        <span>{{ $currentEvent->dress_code_general }}</span>
                    </div>
                    @endif

                    @if($currentEvent->max_participants)
                    <div class="event-detail">
                        <span class="detail-icon">👥</span>
                        <span>Limité à {{ $currentEvent->max_participants }} participants
                        @if($currentEvent->available_spots !== null)
                            ({{ $currentEvent->available_spots }} places restantes)
                        @endif
                        </span>
                    </div>
                    @endif

                    <!-- Tarification dynamique -->
                    @if($currentEvent->ticketTypes->count() > 0)
                    <div class="event-detail">
                        <span class="detail-icon">💰</span>
                        <div>
                            @foreach($currentEvent->ticketTypes->sortBy('display_order') as $ticketType)
                                <div style="margin-bottom: 0.5rem;">
                                    <strong>{{ $ticketType->ticket_name }}:</strong>
                                    {{ number_format($ticketType->price, 0, ',', ' ') }} {{ $ticketType->currency ?? 'FCFA' }}
                                    @if($ticketType->ticket_description)
                                        <br><small style="opacity: 0.8;">{{ $ticketType->ticket_description }}</small>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <button class="register-btn" id="mobile-register-btn">S'inscrire maintenant</button>
            </div>
        </div>

        <!-- Registration Form Panel -->
        <div class="form-panel" id="form-section">
            <div class="form-header">
                @if($currentOrganization->organization_logo || $currentEvent->event_image)
                <div class="logos-container">
                    @if($currentOrganization->organization_logo)
                    <img src="{{ url('public/' . $currentOrganization->organization_logo) }}" alt="Logo {{ $currentOrganization->org_name }}" class="organization-logo">
                    @endif
                    @if($currentEvent->event_image && $currentEvent->event_image !== $currentOrganization->organization_logo)
                    <img src="{{ $currentEvent->event_image }}" alt="{{ $currentEvent->event_title }}" class="organization-logo">
                    @endif
                </div>
                @endif

                <h2 class="form-title">Inscription {{ $currentEvent->event_title }}</h2>
                <p class="form-subtitle">Remplissez le formulaire ci-dessous pour participer à l'événement</p>
            </div>

            @if($currentEvent->can_register)
            <form action="{{ route('event.registration.store', ['org_slug' => $currentOrganization->org_key, 'event_slug' => $currentEvent->event_slug]) }}" method="post">
                @csrf

                <div class="form-group">
                    <label for="fullname" class="required-label">Nom & Prénoms</label>
                    <input type="text" id="fullname" name="fullname" required value="{{ old('fullname') }}">
                </div>

                <div class="form-group">
                    <label for="phone" class="required-label">Numéro de téléphone</label>
                    <input type="tel" id="phone" name="phone" required value="{{ old('phone') }}">
                </div>

                <div class="form-group">
                    <label for="email" class="required-label">Email</label>
                    <input type="email" id="email" name="email" required value="{{ old('email') }}">
                </div>

                <div class="form-group">
                    <label for="organization">Organisation</label>
                    <input type="text" id="organization" name="organization" value="{{ old('organization') }}" placeholder="Votre organisation (optionnel)">
                </div>

                <div class="form-group">
                    <label for="position">Fonction/Poste</label>
                    <input type="text" id="position" name="position" value="{{ old('position') }}" placeholder="Votre fonction (optionnel)">
                </div>

                @if($currentEvent->ticketTypes->count() > 1)
                <div class="form-group">
                    <label class="required-label">Type de ticket</label>
                    <div class="radio-group">
                        @foreach($currentEvent->ticketTypes->sortBy('display_order') as $ticketType)
                            @if($ticketType->is_available)
                            <div class="radio-option" onclick="selectTicket({{ $ticketType->id }})">
                                <input type="radio" id="ticket-{{ $ticketType->id }}" name="ticket_type_id" value="{{ $ticketType->id }}" required
                                       @if(old('ticket_type_id') == $ticketType->id) checked @endif>
                                <div>
                                    <label for="ticket-{{ $ticketType->id }}">
                                        <div class="ticket-price">{{ number_format($ticketType->price, 0, ',', ' ') }} {{ $ticketType->currency ?? 'FCFA' }}</div>
                                        <div><strong>{{ $ticketType->ticket_name }}</strong></div>
                                        @if($ticketType->ticket_description)
                                        <div class="ticket-description">{{ $ticketType->ticket_description }}</div>
                                        @endif
                                        @if($ticketType->available_quantity !== null)
                                        <div class="ticket-description">{{ $ticketType->available_quantity }} places disponibles</div>
                                        @endif
                                    </label>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @else
                    @php $singleTicket = $currentEvent->ticketTypes->first(); @endphp
                    @if($singleTicket)
                    <input type="hidden" name="ticket_type_id" value="{{ $singleTicket->id }}">
                    <div class="form-group">
                        <label>Tarif</label>
                        <div style="padding: 1rem; background: var(--light-primary); border-radius: 8px; border-left: 4px solid var(--primary-color);">
                            <div class="ticket-price">{{ number_format($singleTicket->price, 0, ',', ' ') }} {{ $singleTicket->currency ?? 'FCFA' }}</div>
                            <div><strong>{{ $singleTicket->ticket_name }}</strong></div>
                            @if($singleTicket->ticket_description)
                            <div class="ticket-description">{{ $singleTicket->ticket_description }}</div>
                            @endif
                        </div>
                    </div>
                    @endif
                @endif

                <!-- Champs spéciaux pour certains événements -->
                <div class="form-group">
                    <label for="dietary_requirements">Exigences alimentaires</label>
                    <input type="text" id="dietary_requirements" name="dietary_requirements" value="{{ old('dietary_requirements') }}" placeholder="Allergies, régime spécial...">
                </div>

                <div class="form-group">
                    <label for="special_needs">Besoins spéciaux</label>
                    <input type="text" id="special_needs" name="special_needs" value="{{ old('special_needs') }}" placeholder="Accessibilité, assistance...">
                </div>

                <button type="submit">S'inscrire maintenant</button>
            </form>
            @else
            <div style="text-align: center; padding: 2rem; background: #f8f9fa; border-radius: 10px;">
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
            </div>
            @endif
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('successModal')">&times;</span>
            <h2>Inscription réussie !</h2>
            <p id="successModalMessage">
                Votre inscription a été enregistrée avec succès. Vous recevrez bientôt une confirmation par email.
            </p>
        </div>
    </div>

    <!-- Error Modal -->
    <div id="errorModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('errorModal')">&times;</span>
            <h2>Erreur lors de l'inscription</h2>
            <ul id="errorModalMessage" style="padding-left: 1.2rem;"></ul>
            <button class="modal-button-error" onclick="closeModal('errorModal')">Fermer</button>
        </div>
    </div>

    <script>
        // Variables dynamiques depuis le serveur
        const eventData = {
            title: @json($currentEvent->event_title),
            colors: {
                primary: @json($currentEvent->primary_color),
                secondary: @json($currentEvent->secondary_color)
            },
            organization: @json($currentOrganization->org_name)
        };

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize modals
            document.getElementById('successModal').style.display = 'none';
            document.getElementById('errorModal').style.display = 'none';

            // Mobile register button functionality
            const mobileBtn = document.getElementById('mobile-register-btn');
            if (mobileBtn) {
                mobileBtn.addEventListener('click', function() {
                    document.getElementById('form-section').scrollIntoView({ behavior: 'smooth' });
                });
            }

            // Form validation
            setupFormValidation();

            // Gestion des erreurs de validation Laravel
            @if($errors->any())
                const errors = @json($errors->all());
                showModal('errorModal', errors);
            @endif

            @if(session('success'))
                showModal('successModal', @json(session('success')));
            @endif
        });

        function selectTicket(ticketId) {
            // Remove selected class from all options
            document.querySelectorAll('.radio-option').forEach(option => {
                option.classList.remove('selected');
            });

            // Add selected class to clicked option
            event.currentTarget.classList.add('selected');

            // Select the radio button
            document.getElementById('ticket-' + ticketId).checked = true;
        }

        function showModal(modalId, messages) {
            const modal = document.getElementById(modalId);
            const messageElement = document.getElementById(modalId + 'Message');

            if (!modal || !messageElement) {
                console.error('Modal or message element not found');
                return;
            }

            messageElement.innerHTML = '';

            if (modalId === 'successModal') {
                messageElement.textContent = messages;
            } else {
                if (Array.isArray(messages)) {
                    messages.forEach(msg => {
                        const li = document.createElement('li');
                        li.textContent = msg;
                        messageElement.appendChild(li);
                    });
                } else {
                    const li = document.createElement('li');
                    li.textContent = messages;
                    messageElement.appendChild(li);
                }
            }

            modal.style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function setupFormValidation() {
            // Phone validation
            const phoneInput = document.getElementById('phone');
            if (phoneInput) {
                phoneInput.addEventListener('input', function() {
                    const phoneRegex = /^[0-9+\s-]{8,15}$/;
                    if (phoneRegex.test(this.value)) {
                        this.style.borderColor = 'var(--secondary-color)';
                    } else {
                        this.style.borderColor = '#dc3545';
                    }
                });
            }

            // Email validation
            const emailInput = document.getElementById('email');
            if (emailInput) {
                emailInput.addEventListener('input', function() {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (emailRegex.test(this.value)) {
                        this.style.borderColor = 'var(--secondary-color)';
                    } else {
                        this.style.borderColor = '#dc3545';
                    }
                });
            }

            // Form field animations
            const formInputs = document.querySelectorAll('input, select');
            formInputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('active-field');
                });

                input.addEventListener('blur', function() {
                    if (!this.value) {
                        this.parentElement.classList.remove('active-field');
                    }
                });
            });
        }

        // Add CSS animation class
        const style = document.createElement('style');
        style.textContent = `
            .active-field label {
                color: var(--primary-color);
                transition: color 0.3s ease;
            }

            .form-group.active-field input,
            .form-group.active-field select {
                border-color: var(--primary-color);
                box-shadow: 0 0 0 3px rgba(29, 134, 217, 0.1);
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
