@extends('organization.layouts.app')

@section('title', 'Créer un événement')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/min/dropzone.min.css" crossorigin="anonymous" />
<style>
    .form-section {
        background: white;
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    .form-section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #111827;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e5e7eb;
    }
    .required-field::after {
        content: ' *';
        color: #ef4444;
    }

    /* Wizard Styles */
    .wizard-step {
        display: none;
    }
    .wizard-step.active {
        display: block;
    }
    /* Boutons de navigation */
    .wizard-step .form-section:last-child,
    .wizard-step > div:last-child.mt-6 {
        margin-top: 2rem !important;
        padding: 1.5rem !important;
        border-top: 2px solid #e5e7eb;
        background: #f9fafb;
        border-radius: 0.5rem;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
    }
    .wizard-step button[type="button"] {
        font-weight: 600;
        min-width: 140px;
    }

    /* Primary custom color */
    .bg-primary-custom {
        background-color: rgb(17, 19, 165) !important;
    }
    .text-primary-custom {
        color: rgb(17, 19, 165) !important;
    }
    .border-primary-custom {
        border-color: rgb(17, 19, 165) !important;
    }
    .focus\:ring-primary-custom:focus {
        --tw-ring-color: rgb(17, 19, 165);
    }
    .wizard-progress {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
        position: relative;
    }
    .wizard-progress::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        height: 2px;
        background: #e5e7eb;
        z-index: 0;
    }
    .wizard-step-indicator {
        position: relative;
        z-index: 1;
        background: white;
        padding: 0 1rem;
    }
    .wizard-step-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e5e7eb;
        color: #6b7280;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        margin: 0 auto 0.5rem;
        transition: all 0.3s;
    }
    .wizard-step-indicator.active .wizard-step-circle {
        background: var(--primary-color);
        color: white;
    }
    .wizard-step-indicator.completed .wizard-step-circle {
        background: #10b981;
        color: white;
    }
    .wizard-step-label {
        font-size: 0.875rem;
        text-align: center;
        color: #6b7280;
    }
    .wizard-step-indicator.active .wizard-step-label {
        color: var(--primary-color);
        font-weight: 600;
    }

    /* Pack Cards */
    .pack-card {
        border: 2px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1.5rem;
        cursor: pointer;
        transition: all 0.3s;
        background: white;
    }
    .pack-card:hover {
        border-color: var(--primary-color);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .pack-card.selected {
        border-color: var(--primary-color);
        background: #f0f4ff;
        box-shadow: 0 4px 12px rgba(17, 19, 165, 0.15);
    }
    .pack-card-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 1rem;
    }
    .pack-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .pack-badge.standard {
        background: #dbeafe;
        color: #1e40af;
    }
    .pack-badge.premium {
        background: #fef3c7;
        color: #92400e;
    }
    .pack-badge.custom {
        background: #f3e8ff;
        color: #6b21a8;
    }
    .pack-features {
        list-style: none;
        padding: 0;
        margin: 1rem 0;
    }
    .pack-features li {
        padding: 0.5rem 0;
        display: flex;
        align-items: center;
    }
    .pack-features li i {
        color: #10b981;
        margin-right: 0.5rem;
        width: 20px;
    }

    /* Modal/Popup Styles */
    .custom-pack-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
        animation: fadeIn 0.3s ease;
    }
    .custom-pack-modal.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .custom-pack-modal-content {
        background-color: white;
        margin: auto;
        padding: 2rem;
        border-radius: 1rem;
        max-width: 500px;
        width: 90%;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        animation: slideUp 0.3s ease;
        position: relative;
    }
    .custom-pack-modal-close {
        position: absolute;
        top: 1rem;
        right: 1rem;
        font-size: 1.5rem;
        font-weight: bold;
        color: #6b7280;
        cursor: pointer;
        transition: color 0.3s;
    }
    .custom-pack-modal-close:hover {
        color: #111827;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes slideUp {
        from {
            transform: translateY(30px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* Element Configuration Styles */
    .element-config-panel {
        padding: 0.75rem;
        background: #f9fafb;
        border-radius: 0.5rem;
        border: 1px solid #e5e7eb;
        transition: all 0.2s;
    }
    .element-config-panel:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
    }
    .element-status-badge {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #d1d5db;
        transition: all 0.2s;
    }
    .element-status-badge.active {
        background: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
    }
    .element-position-btn.active {
        background: #1113a5 !important;
        color: white !important;
        border-color: #1113a5 !important;
    }
    .element-color-picker {
        transition: transform 0.2s;
    }
    .element-color-picker:hover {
        transform: scale(1.1);
    }

    /* Styles Premium (réutilisation des mêmes styles) */
    .element-config-panel-premium {
        padding: 0.75rem;
        background: #f9fafb;
        border-radius: 0.5rem;
        border: 1px solid #e5e7eb;
        transition: all 0.2s;
    }
    .element-config-panel-premium:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
    }
    .element-position-btn-premium.active {
        background: #1113a5 !important;
        color: white !important;
        border-color: #1113a5 !important;
    }
    .element-color-picker-premium {
        transition: transform 0.2s;
    }
    .element-color-picker-premium:hover {
        transform: scale(1.1);
    }

    /* Styles pour les rectangles draggables */
    .element-rectangle {
        position: absolute !important;
        border: 3px solid #1113a5 !important;
        cursor: move !important;
        background: rgba(255, 255, 255, 0.4) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 100px !important;
        min-height: 30px !important;
        user-select: none !important;
        transition: all 0.2s;
        box-sizing: border-box !important;
        border-radius: 4px !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2) !important;
    }
    .element-rectangle:hover {
        opacity: 0.8;
    }
    .element-rectangle.selected {
        box-shadow: 0 0 0 3px rgba(17, 19, 165, 0.3);
        z-index: 10;
    }
    .element-rectangle-label {
        font-size: 12px;
        font-weight: bold;
        padding: 4px 8px;
        color: #1113a5 !important;
        text-shadow: 0 1px 2px rgba(255, 255, 255, 0.8);
        background: rgba(255, 255, 255, 0.9);
        border-radius: 4px;
        pointer-events: none;
        text-transform: capitalize;
    }
    .element-resize-handle {
        position: absolute;
        width: 10px;
        height: 10px;
        background: #1113a5;
        border: 2px solid white;
        border-radius: 50%;
        cursor: nwse-resize;
        right: -5px;
        bottom: -5px;
        z-index: 11;
    }
    .element-resize-handle:hover {
        background: #0e0f8a;
        transform: scale(1.2);
    }
    #standardTicketContainer, #premiumTicketContainer {
        display: inline-block;
        position: relative;
    }

    /* Dropzone Styles */
    .dropzone {
        min-height: 200px;
        border: 2px dashed #d1d5db !important;
        border-radius: 0.5rem;
        background: #f9fafb;
        transition: all 0.3s;
    }
    .dropzone.dz-started {
        min-height: auto;
    }
    .dropzone:hover {
        border-color: var(--primary-color) !important;
        background: #f0f4ff;
    }
    .dropzone .dz-message {
        margin: 2em 0;
    }
    .dropzone .dz-preview {
        display: inline-block;
        margin: 0.5rem;
        vertical-align: top;
    }
    .dropzone .dz-preview .dz-image {
        width: 120px;
        height: 120px;
        border-radius: 0.5rem;
        overflow: hidden;
    }
    .dropzone .dz-preview .dz-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .dropzone .dz-preview .dz-details {
        padding: 0.5rem;
        font-size: 0.875rem;
    }
    .dropzone .dz-preview .dz-remove {
        margin-top: 0.5rem;
        padding: 0.25rem 0.75rem;
        background: #ef4444;
        color: white;
        border-radius: 0.25rem;
        text-decoration: none;
        font-size: 0.875rem;
    }
    .dropzone .dz-preview .dz-remove:hover {
        background: #dc2626;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Créer un événement</h1>
            <p class="mt-1 text-sm text-gray-500">Suivez les étapes pour créer votre événement</p>
        </div>
        <a href="{{ route('org.events.index', ['org_slug' => $orgSlug]) }}"
           class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i>Retour à la liste
        </a>
    </div>

    <!-- Progress Bar -->
    <div class="wizard-progress">
        <div class="wizard-step-indicator active" data-step="1">
            <div class="wizard-step-circle">1</div>
            <div class="wizard-step-label">Choix du pack</div>
        </div>
        <div class="wizard-step-indicator" data-step="2">
            <div class="wizard-step-circle">2</div>
            <div class="wizard-step-label">Informations</div>
        </div>
        <div class="wizard-step-indicator" data-step="3">
            <div class="wizard-step-circle">3</div>
            <div class="wizard-step-label">Date & Lieu</div>
        </div>
        <div class="wizard-step-indicator" data-step="4">
            <div class="wizard-step-circle">4</div>
            <div class="wizard-step-label">Tarification</div>
        </div>
        <div class="wizard-step-indicator" data-step="5">
            <div class="wizard-step-circle">5</div>
            <div class="wizard-step-label">Champs formulaire</div>
        </div>
        <div class="wizard-step-indicator" data-step="6">
            <div class="wizard-step-circle">6</div>
            <div class="wizard-step-label">Personnalisation</div>
        </div>
        <div class="wizard-step-indicator" data-step="7">
            <div class="wizard-step-circle">7</div>
            <div class="wizard-step-label">Finalisation</div>
        </div>
    </div>

    <form id="eventForm" method="POST" action="{{ route('org.events.store', ['org_slug' => $orgSlug]) }}" enctype="multipart/form-data" class="dropzone-form">
        @csrf
        <input type="hidden" name="pack_type" id="selected_pack_type" value="{{ old('pack_type') }}">

        <!-- Étape 1: Choix du pack -->
        <div class="wizard-step active" id="step1">
            <div class="form-section">
                <h2 class="form-section-title">Choisissez votre pack</h2>
                <p class="text-gray-600 mb-6">Sélectionnez le pack qui correspond le mieux à vos besoins</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Pack Standard -->
                    <div class="pack-card" data-pack="standard">
                        <div class="pack-card-header">
                            <h3 class="text-xl font-bold text-gray-900">Pack Standard</h3>
                            <span class="pack-badge standard">STANDARD</span>
                        </div>
                        <p class="text-gray-600 mb-4">Pack de base avec tickets par email et design basique</p>
                        <ul class="pack-features">
                            <li><i class="fas fa-check-circle"></i> Tickets par email</li>
                            <li><i class="fas fa-check-circle"></i> Design basique</li>
                            <li><i class="fas fa-check-circle"></i> Upload de création (600x300px)</li>
                            <li><i class="fas fa-check-circle"></i> Choix des éléments à afficher</li>
                            <li><i class="fas fa-check-circle"></i> QR Code, ID Ticket, Date/Heure</li>
                            <li><i class="fas fa-check-circle"></i> Nom, Prénoms, Fonction</li>
                        </ul>
                    </div>

                    <!-- Pack Premium -->
                    <div class="pack-card" data-pack="premium">
                        <div class="pack-card-header">
                            <h3 class="text-xl font-bold text-gray-900">Pack Premium</h3>
                            <span class="pack-badge premium">PREMIUM</span>
                        </div>
                        <p class="text-gray-600 mb-4">Pack avancé avec tickets personnalisés et design sur mesure</p>
                        <ul class="pack-features">
                            <li><i class="fas fa-check-circle"></i> Tickets par email + WhatsApp</li>
                            <li><i class="fas fa-check-circle"></i> Design personnalisé</li>
                            <li><i class="fas fa-check-circle"></i> Upload de spécimen complet</li>
                            <li><i class="fas fa-check-circle"></i> Choix position QR code</li>
                            <li><i class="fas fa-check-circle"></i> Aperçu de positionnement</li>
                            <li><i class="fas fa-check-circle"></i> Support prioritaire</li>
                        </ul>
                    </div>

                    <!-- Pack Custom -->
                    <div class="pack-card" data-pack="custom">
                        <div class="pack-card-header">
                            <h3 class="text-xl font-bold text-gray-900">Pack Custom</h3>
                            <span class="pack-badge custom">CUSTOM</span>
                        </div>
                        <p class="text-gray-600 mb-4">Pack personnalisé avec configuration sur mesure</p>
                        <ul class="pack-features">
                            <li><i class="fas fa-check-circle"></i> Toutes les fonctionnalités</li>
                            <li><i class="fas fa-check-circle"></i> Configuration sur mesure</li>
                            <li><i class="fas fa-check-circle"></i> Support dédié</li>
                            <li><i class="fas fa-check-circle"></i> Limites illimitées</li>
                            <li><i class="fas fa-check-circle"></i> API access</li>
                            <li><i class="fas fa-check-circle"></i> Domain personnalisé</li>
                        </ul>
                    </div>
                </div>

                <!-- Modal Popup pour Pack Custom -->
                <div id="customPackModal" class="custom-pack-modal">
                    <div class="custom-pack-modal-content">
                        <span class="custom-pack-modal-close" id="closeCustomPackModal">&times;</span>
                        <div class="text-center">
                            <div class="mb-4">
                                <i class="fas fa-crown text-purple-500 text-5xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">Pack Custom</h3>
                            <p class="text-gray-700 mb-6">
                                Pour bénéficier d'une configuration personnalisée adaptée à vos besoins spécifiques,
                                contactez notre équipe support via WhatsApp. Nous serons ravis de vous accompagner
                                dans la création de votre événement avec toutes les fonctionnalités dont vous avez besoin.
                            </p>
                            <a href="https://wa.me/2250758942495?text=Bonjour%2C%20je%20souhaite%20cr%C3%A9er%20un%20%C3%A9v%C3%A9nement%20avec%20le%20pack%20Custom.%20Pouvez-vous%20m%27aider%20%3F"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="inline-flex items-center px-8 py-4 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-all shadow-lg hover:shadow-xl transform hover:scale-105 mb-4">
                                <i class="fab fa-whatsapp text-3xl mr-3"></i>
                                <span class="text-lg font-semibold">Contacter le support WhatsApp</span>
                            </a>
                            <p class="text-sm text-gray-600 mt-4">
                                <i class="fas fa-info-circle mr-1"></i>
                                Veuillez contacter le support pour utiliser le pack Custom
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-between items-center" style="margin-top: 2rem; padding: 1.5rem; background: #f9fafb; border-radius: 0.5rem; border-top: 2px solid #e5e7eb; box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);">
                    <p class="text-sm text-gray-600" id="step1Help">
                        <i class="fas fa-info-circle mr-1"></i>
                        Veuillez sélectionner un pack pour continuer
                    </p>
                    <button type="button" class="px-6 py-3 rounded-lg hover:opacity-90 transition font-semibold shadow-md border-2 text-white" id="nextFromStep1" onclick="if(!this.disabled) goToStep(2);" disabled style="min-width: 140px; background-color: rgb(17, 19, 165); border-color: rgb(17, 19, 165);" onmouseover="if(!this.disabled) this.style.backgroundColor='rgb(14, 15, 138)'" onmouseout="if(!this.disabled) this.style.backgroundColor='rgb(17, 19, 165)'">
                        Suivant <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Étape 2: Informations générales -->
        <div class="wizard-step" id="step2">
            <div class="form-section">
                <h2 class="form-section-title">Informations générales</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="event_title" class="block text-sm font-medium text-gray-700 mb-1 required-field">Titre de l'événement</label>
                        <input type="text"
                               id="event_title"
                               name="event_title"
                               value="{{ old('event_title') }}"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-custom focus:border-transparent @error('event_title') border-red-500 @enderror"
                               placeholder="Ex: Conférence annuelle 2025">
                        @error('event_title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="event_description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea id="event_description"
                                  name="event_description"
                                  rows="4"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-custom focus:border-transparent @error('event_description') border-red-500 @enderror"
                                  placeholder="Décrivez votre événement...">{{ old('event_description') }}</textarea>
                        @error('event_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="event_type_id" class="block text-sm font-medium text-gray-700 mb-1 required-field">Type d'événement</label>
                        <select id="event_type_id"
                                name="event_type_id"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-custom focus:border-transparent @error('event_type_id') border-red-500 @enderror">
                            <option value="">Sélectionner un type</option>
                            @foreach($eventTypes as $type)
                                <option value="{{ $type->id }}" {{ old('event_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->type_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('event_type_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="max_participants" class="block text-sm font-medium text-gray-700 mb-1">Nombre maximum de participants</label>
                        <input type="number"
                               id="max_participants"
                               name="max_participants"
                               value="{{ old('max_participants', 100) }}"
                               min="1"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-custom focus:border-transparent @error('max_participants') border-red-500 @enderror">
                        @error('max_participants')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-between" style="margin-top: 2rem; padding: 1.5rem; background: #f9fafb; border-radius: 0.5rem; border-top: 2px solid #e5e7eb; box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);">
                    <button type="button" class="px-6 py-3 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition font-semibold" onclick="goToStep(1)" style="min-width: 140px;">
                        <i class="fas fa-arrow-left mr-2"></i>Précédent
                    </button>
                    <button type="button" class="px-6 py-3 rounded-lg hover:opacity-90 transition font-semibold shadow-md text-white" onclick="goToStep(3)" style="min-width: 140px; background-color: rgb(17, 19, 165);">
                        Suivant <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Étape 3: Date et lieu -->
        <div class="wizard-step" id="step3">
            <div class="form-section">
                <h2 class="form-section-title">Date et lieu</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="event_date" class="block text-sm font-medium text-gray-700 mb-1 required-field">Date de l'événement</label>
                        <input type="text"
                               id="event_date"
                               name="event_date"
                               value="{{ old('event_date') }}"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-custom focus:border-transparent @error('event_date') border-red-500 @enderror"
                               placeholder="Sélectionner une date">
                        @error('event_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="event_start_time" class="block text-sm font-medium text-gray-700 mb-1 required-field">Heure de début</label>
                        <input type="text"
                               id="event_start_time"
                               name="event_start_time"
                               value="{{ old('event_start_time') }}"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-custom focus:border-transparent @error('event_start_time') border-red-500 @enderror"
                               placeholder="HH:MM">
                        @error('event_start_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="event_end_time" class="block text-sm font-medium text-gray-700 mb-1">Heure de fin</label>
                        <input type="text"
                               id="event_end_time"
                               name="event_end_time"
                               value="{{ old('event_end_time') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-custom focus:border-transparent @error('event_end_time') border-red-500 @enderror"
                               placeholder="HH:MM">
                        @error('event_end_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label for="event_location" class="block text-sm font-medium text-gray-700 mb-1 required-field">Lieu</label>
                        <input type="text"
                               id="event_location"
                               name="event_location"
                               value="{{ old('event_location') }}"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-custom focus:border-transparent @error('event_location') border-red-500 @enderror"
                               placeholder="Ex: Centre de conférences">
                        @error('event_location')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="event_address" class="block text-sm font-medium text-gray-700 mb-1">Adresse complète</label>
                        <input type="text"
                               id="event_address"
                               name="event_address"
                               value="{{ old('event_address') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-custom focus:border-transparent @error('event_address') border-red-500 @enderror"
                               placeholder="Rue, ville, pays">
                        @error('event_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-between" style="margin-top: 2rem; padding: 1.5rem; background: #f9fafb; border-radius: 0.5rem; border-top: 2px solid #e5e7eb; box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);">
                    <button type="button" class="px-6 py-3 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition font-semibold" onclick="goToStep(2)" style="min-width: 140px;">
                        <i class="fas fa-arrow-left mr-2"></i>Précédent
                    </button>
                    <button type="button" class="px-6 py-3 rounded-lg hover:opacity-90 transition font-semibold shadow-md text-white" onclick="goToStep(4)" style="min-width: 140px; background-color: rgb(17, 19, 165);">
                        Suivant <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Étape 4: Tarification -->
        <div class="wizard-step" id="step4">
            <div class="form-section">
                <h2 class="form-section-title">Tarification</h2>
                <div class="space-y-6">
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox"
                                   id="requires_payment"
                                   name="requires_payment"
                                   value="1"
                                   {{ old('requires_payment', true) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-primary-custom focus:ring-primary-custom">
                            <span class="ml-2 text-sm text-gray-700">L'événement est payant</span>
                        </label>
                    </div>

                    <!-- Option pour utiliser plusieurs tarifs -->
                    <div id="multipleTicketsOption" style="display: none;">
                        <label class="flex items-center">
                            <input type="checkbox"
                                   id="use_multiple_tickets"
                                   name="use_multiple_tickets"
                                   value="1"
                                   {{ old('use_multiple_tickets') ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-primary-custom focus:ring-primary-custom">
                            <span class="ml-2 text-sm text-gray-700">Utiliser plusieurs tarifs (VIP, Standard, etc.)</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1 ml-6">
                            <i class="fas fa-info-circle mr-1"></i>
                            Cochez cette option pour proposer plusieurs tarifs différents pour votre événement
                        </p>
                    </div>

                    <!-- Tarif unique -->
                    <div id="singlePriceFields" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="event_price" class="block text-sm font-medium text-gray-700 mb-1">Prix</label>
                            <input type="number"
                                   id="event_price"
                                   name="event_price"
                                   value="{{ old('event_price', 0) }}"
                                   min="0"
                                   step="0.01"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-custom focus:border-transparent @error('event_price') border-red-500 @enderror">
                            @error('event_price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="currencyFields">
                            <label for="currency" class="block text-sm font-medium text-gray-700 mb-1">Devise</label>
                            <select id="currency"
                                    name="currency"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-custom focus:border-transparent @error('currency') border-red-500 @enderror">
                                <option value="XOF" {{ old('currency', 'XOF') == 'XOF' ? 'selected' : '' }}>XOF (FCFA)</option>
                                <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                            </select>
                            @error('currency')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Tarifs multiples -->
                    <div id="multipleTicketsFields" style="display: none;">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tarifs disponibles</h3>
                            <p class="text-sm text-gray-600 mb-4">Ajoutez les différents tarifs disponibles pour cet événement (VIP, Standard, Étudiant, etc.)</p>
                            <div id="ticketsContainer" class="space-y-4">
                                <!-- Les tarifs seront ajoutés dynamiquement ici -->
                            </div>
                            <div class="mt-4">
                                <button type="button"
                                        id="addTicketBtn"
                                        class="px-4 py-2 bg-primary-custom text-white rounded-lg hover:opacity-90 transition inline-flex items-center"
                                        style="background-color: rgb(17, 19, 165);">
                                    <i class="fas fa-plus mr-2"></i>Ajouter un tarif
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                Cliquez sur "Ajouter un tarif" pour créer un nouveau tarif. Vous pouvez ajouter plusieurs tarifs.
                            </p>
                        </div>
                    </div>

                    <!-- Options de paiement partiel et réservation -->
                    <div id="paymentOptions" style="display: none;">
                        <div class="border-t pt-4 space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900">Options de paiement</h3>

                            <!-- Paiement partiel -->
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox"
                                           id="allow_partial_payment"
                                           name="allow_partial_payment"
                                           value="1"
                                           {{ old('allow_partial_payment') ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-primary-custom focus:ring-primary-custom">
                                    <span class="ml-2 text-sm text-gray-700">Autoriser le paiement partiel</span>
                                </label>
                                <div id="partialPaymentAmountField" class="mt-2 ml-6" style="display: none;">
                                    <label for="partial_payment_amount" class="block text-sm font-medium text-gray-700 mb-1">Montant du paiement partiel</label>
                                    <input type="number"
                                           id="partial_payment_amount"
                                           name="partial_payment_amount"
                                           value="{{ old('partial_payment_amount') }}"
                                           min="0"
                                           step="0.01"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-custom focus:border-transparent">
                                </div>
                            </div>

                            <!-- Réservation -->
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox"
                                           id="allow_reservation"
                                           name="allow_reservation"
                                           value="1"
                                           {{ old('allow_reservation') ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-primary-custom focus:ring-primary-custom">
                                    <span class="ml-2 text-sm text-gray-700">Autoriser la réservation avec montant défini</span>
                                </label>
                                <div id="reservationFields" class="mt-2 ml-6 space-y-2" style="display: none;">
                                    <div>
                                        <label for="reservation_amount" class="block text-sm font-medium text-gray-700 mb-1">Montant de la réservation</label>
                                        <input type="number"
                                               id="reservation_amount"
                                               name="reservation_amount"
                                               value="{{ old('reservation_amount') }}"
                                               min="0"
                                               step="0.01"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-custom focus:border-transparent">
                                    </div>
                                    <div>
                                        <label for="reservation_terms" class="block text-sm font-medium text-gray-700 mb-1">Conditions de réservation</label>
                                        <textarea id="reservation_terms"
                                                  name="reservation_terms"
                                                  rows="3"
                                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-custom focus:border-transparent"
                                                  placeholder="Ex: Le solde doit être payé 48h avant l'événement">{{ old('reservation_terms') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-between" style="margin-top: 2rem; padding: 1.5rem; background: #f9fafb; border-radius: 0.5rem; border-top: 2px solid #e5e7eb; box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);">
                    <button type="button" class="px-6 py-3 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition font-semibold" onclick="goToStep(3)" style="min-width: 140px;">
                        <i class="fas fa-arrow-left mr-2"></i>Précédent
                    </button>
                    <button type="button" class="px-6 py-3 rounded-lg hover:opacity-90 transition font-semibold shadow-md text-white" onclick="goToStep(5)" style="min-width: 140px; background-color: rgb(17, 19, 165);">
                        Suivant <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Étape 5: Configuration des champs du formulaire -->
        <div class="wizard-step" id="step5">
            <div class="form-section">
                <h2 class="form-section-title">Configuration des champs du formulaire</h2>
                <p class="text-gray-600 mb-6">Choisissez les champs à afficher dans le formulaire d'inscription et définissez lesquels sont obligatoires</p>

                <div class="space-y-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Champs disponibles</h3>
                        <div id="formFieldsContainer" class="space-y-3">
                            @php
                                $defaultFields = [
                                    'fullname' => ['label' => 'Nom complet', 'type' => 'text', 'required' => true],
                                    'email' => ['label' => 'Email', 'type' => 'email', 'required' => true],
                                    'phone' => ['label' => 'Téléphone', 'type' => 'phone', 'required' => false],
                                    'organization' => ['label' => 'Organisation', 'type' => 'text', 'required' => false],
                                    'position' => ['label' => 'Fonction/Poste', 'type' => 'text', 'required' => false],
                                    'address' => ['label' => 'Adresse', 'type' => 'textarea', 'required' => false],
                                    'city' => ['label' => 'Ville', 'type' => 'text', 'required' => false],
                                    'country' => ['label' => 'Pays', 'type' => 'text', 'required' => false],
                                    'special_needs' => ['label' => 'Besoins spéciaux', 'type' => 'textarea', 'required' => false],
                                    'dietary_restrictions' => ['label' => 'Restrictions alimentaires', 'type' => 'textarea', 'required' => false],
                                ];
                            @endphp

                            @foreach($defaultFields as $fieldKey => $fieldConfig)
                            <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200">
                                <div class="flex items-center space-x-3">
                                    <input type="checkbox"
                                           id="field_{{ $fieldKey }}"
                                           name="form_fields[{{ $fieldKey }}][enabled]"
                                           value="1"
                                           {{ old("form_fields.{$fieldKey}.enabled", $fieldConfig['required'] ?? false) ? 'checked' : '' }}
                                           class="field-checkbox rounded border-gray-300 text-primary-custom focus:ring-primary-custom">
                                    <label for="field_{{ $fieldKey }}" class="text-sm font-medium text-gray-700">
                                        {{ $fieldConfig['label'] }}
                                    </label>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <label class="flex items-center">
                                        <input type="checkbox"
                                               id="field_{{ $fieldKey }}_required"
                                               name="form_fields[{{ $fieldKey }}][required]"
                                               value="1"
                                               {{ old("form_fields.{$fieldKey}.required", $fieldConfig['required'] ?? false) ? 'checked' : '' }}
                                               class="field-required rounded border-gray-300 text-primary-custom focus:ring-primary-custom"
                                               disabled>
                                        <span class="ml-2 text-xs text-gray-600">Obligatoire</span>
                                    </label>
                                </div>
                                <input type="hidden" name="form_fields[{{ $fieldKey }}][label]" value="{{ $fieldConfig['label'] }}">
                                <input type="hidden" name="form_fields[{{ $fieldKey }}][type]" value="{{ $fieldConfig['type'] }}">
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-between" style="margin-top: 2rem; padding: 1.5rem; background: #f9fafb; border-radius: 0.5rem; border-top: 2px solid #e5e7eb; box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);">
                    <button type="button" class="px-6 py-3 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition font-semibold" onclick="goToStep(4)" style="min-width: 140px;">
                        <i class="fas fa-arrow-left mr-2"></i>Précédent
                    </button>
                    <button type="button" class="px-6 py-3 rounded-lg hover:opacity-90 transition font-semibold shadow-md text-white" onclick="goToStep(6)" style="min-width: 140px; background-color: rgb(17, 19, 165);">
                        Suivant <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Étape 6: Personnalisation des tickets -->
        <div class="wizard-step" id="step6">
            <!-- Pack Standard -->
            <div id="standardPack" class="form-section" style="display: none;">
                <h2 class="form-section-title">Personnalisation du ticket - Pack Standard</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 required-field">Modèle de ticket (Création)</label>
                        <p class="mb-3 text-xs text-gray-500">
                            <strong>Dimensions requises :</strong> 600 x 300 pixels (format recommandé) ou ratio 2:1<br>
                            Formats acceptés : JPG, PNG, PDF. Taille max : 5 Mo
                        </p>
                        <div id="dropzoneStandard" class="dropzone border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary-custom transition-colors">
                            <div class="dz-message">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                                <p class="text-gray-600 font-medium">Glissez-déposez votre création ici</p>
                                <p class="text-gray-500 text-sm mt-1">ou cliquez pour sélectionner un fichier</p>
                            </div>
                        </div>
                        <input type="hidden" id="ticket_template_standard" name="ticket_template_standard">
                        @error('ticket_template_standard')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <div id="standardPreview" class="mt-4"></div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Éléments à afficher sur le ticket</label>
                        <p class="mb-3 text-xs text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            Cochez les éléments que vous souhaitez voir apparaître sur le ticket. Le système positionnera automatiquement ces éléments lors de la génération du ticket.
                        </p>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="checkbox"
                                       name="ticket_elements[]"
                                       value="qr_code"
                                       {{ in_array('qr_code', old('ticket_elements', ['qr_code'])) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-primary-custom focus:ring-primary-custom">
                                <span class="ml-2 text-sm text-gray-700">QR Code</span>
                            </label>

                            <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="checkbox"
                                       name="ticket_elements[]"
                                       value="ticket_id"
                                       {{ in_array('ticket_id', old('ticket_elements', ['ticket_id'])) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-primary-custom focus:ring-primary-custom">
                                <span class="ml-2 text-sm text-gray-700">ID Ticket</span>
                            </label>

                            <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="checkbox"
                                       name="ticket_elements[]"
                                       value="seat"
                                       {{ in_array('seat', old('ticket_elements', [])) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-primary-custom focus:ring-primary-custom">
                                <span class="ml-2 text-sm text-gray-700">Siège</span>
                            </label>

                            <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="checkbox"
                                       name="ticket_elements[]"
                                       value="date_time"
                                       {{ in_array('date_time', old('ticket_elements', ['date_time'])) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-primary-custom focus:ring-primary-custom">
                                <span class="ml-2 text-sm text-gray-700">Date et Heure</span>
                            </label>

                            <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="checkbox"
                                       name="ticket_elements[]"
                                       value="name"
                                       {{ in_array('name', old('ticket_elements', ['name'])) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-primary-custom focus:ring-primary-custom">
                                <span class="ml-2 text-sm text-gray-700">Nom et Prénoms</span>
                            </label>

                            <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="checkbox"
                                       name="ticket_elements[]"
                                       value="function"
                                       {{ in_array('function', old('ticket_elements', [])) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-primary-custom focus:ring-primary-custom">
                                <span class="ml-2 text-sm text-gray-700">Fonction</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pack Premium -->
            <div id="premiumPack" class="form-section" style="display: none;">
                <h2 class="form-section-title">Personnalisation du ticket - Pack Premium</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 required-field">Spécimen du ticket</label>
                        <p class="mb-3 text-xs text-gray-500">
                            Importez le spécimen de votre ticket au format standard. Formats acceptés : JPG, PNG, PDF. Taille max : 10 Mo
                        </p>
                        <div id="dropzonePremium" class="dropzone border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary-custom transition-colors">
                            <div class="dz-message">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                                <p class="text-gray-600 font-medium">Glissez-déposez votre spécimen ici</p>
                                <p class="text-gray-500 text-sm mt-1">ou cliquez pour sélectionner un fichier</p>
                            </div>
                        </div>
                        <input type="hidden" id="ticket_template_premium" name="ticket_template_premium">
                        @error('ticket_template_premium')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Zone de prévisualisation interactive pour Premium -->
                    <div id="premiumInteractivePreview" class="mt-6" style="display: none;">
                        <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl border border-gray-200 shadow-lg p-6">
                            <div class="mb-6">
                                <h3 class="text-xl font-semibold text-gray-800 mb-1">Personnalisation du ticket</h3>
                                <p class="text-sm text-gray-500">Sélectionnez les éléments à afficher sur votre ticket</p>
                            </div>

                            <div class="space-y-6">
                                <!-- Zone de prévisualisation du ticket -->
                                <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
                                        <i class="fas fa-image mr-2 text-primary-custom"></i>
                                        Aperçu du ticket
                                    </h4>
                                    <div class="relative inline-block w-full">
                                        <div class="border border-gray-300 rounded-lg p-3 bg-gray-50 shadow-inner overflow-auto" style="max-height: 600px; position: relative;">
                                            <div id="premiumTicketContainer" class="relative inline-block mx-auto" style="position: relative; display: inline-block;">
                                                <img id="premiumTicketImage" class="max-w-full h-auto block" style="display: block; max-width: 100%;" alt="Ticket preview">
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mt-3 text-xs text-gray-500">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        <strong>Note :</strong> Une zone blanche sera automatiquement ajoutée à droite du ticket pour afficher les éléments sélectionnés.
                                    </p>
                                </div>

                                <!-- Configuration des éléments -->
                                <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
                                        <i class="fas fa-sliders-h mr-2 text-primary-custom"></i>
                                        Éléments à afficher sur le ticket
                                    </h4>
                                    <div class="space-y-3">
                                        <label class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition cursor-pointer">
                                            <input type="checkbox"
                                                   id="show_qr_code_premium"
                                                   name="premium_elements[]"
                                                   value="qr_code"
                                                   checked
                                                   disabled
                                                   class="rounded border-gray-300 text-primary-custom focus:ring-primary-custom">
                                            <i class="fas fa-qrcode ml-3 mr-3 text-gray-400"></i>
                                            <span class="text-sm text-gray-700 flex-1">QR Code <span class="text-red-500">*</span></span>
                                            <span class="text-xs text-gray-500">(Obligatoire)</span>
                                        </label>

                                        <label class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition cursor-pointer">
                                            <input type="checkbox"
                                                   id="show_ticket_id_premium"
                                                   name="premium_elements[]"
                                                   value="ticket_id"
                                                   checked
                                                   disabled
                                                   class="rounded border-gray-300 text-primary-custom focus:ring-primary-custom">
                                            <i class="fas fa-hashtag ml-3 mr-3 text-gray-400"></i>
                                            <span class="text-sm text-gray-700 flex-1">ID Ticket <span class="text-red-500">*</span></span>
                                            <span class="text-xs text-gray-500">(Obligatoire)</span>
                                        </label>

                                        <label class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition cursor-pointer">
                                            <input type="checkbox"
                                                   id="show_seat_premium"
                                                   name="premium_elements[]"
                                                   value="seat"
                                                   {{ in_array('seat', old('premium_elements', [])) ? 'checked' : '' }}
                                                   class="rounded border-gray-300 text-primary-custom focus:ring-primary-custom">
                                            <i class="fas fa-chair ml-3 mr-3 text-gray-400"></i>
                                            <span class="text-sm text-gray-700 flex-1">Siège</span>
                                        </label>

                                        <label class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition cursor-pointer">
                                            <input type="checkbox"
                                                   id="show_ticket_type_premium"
                                                   name="premium_elements[]"
                                                   value="ticket_type"
                                                   {{ in_array('ticket_type', old('premium_elements', [])) ? 'checked' : '' }}
                                                   class="rounded border-gray-300 text-primary-custom focus:ring-primary-custom">
                                            <i class="fas fa-ticket-alt ml-3 mr-3 text-gray-400"></i>
                                            <span class="text-sm text-gray-700 flex-1">Type de ticket</span>
                                        </label>

                                        <label class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition cursor-pointer">
                                            <input type="checkbox"
                                                   id="show_amount_premium"
                                                   name="premium_elements[]"
                                                   value="amount"
                                                   {{ in_array('amount', old('premium_elements', [])) ? 'checked' : '' }}
                                                   class="rounded border-gray-300 text-primary-custom focus:ring-primary-custom">
                                            <i class="fas fa-money-bill-wave ml-3 mr-3 text-gray-400"></i>
                                            <span class="text-sm text-gray-700 flex-1">Montant</span>
                                        </label>
                                    </div>
                                    <p class="mt-4 text-xs text-gray-500">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Les éléments sélectionnés seront automatiquement ajoutés dans une zone blanche à droite du ticket.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pack Custom -->
            <div id="customPack" class="form-section" style="display: none;">
                <h2 class="form-section-title">Personnalisation du ticket - Pack Custom</h2>
                <div class="bg-gradient-to-r from-purple-50 to-indigo-50 border-2 border-purple-200 rounded-lg p-8 text-center">
                    <div class="mb-4">
                        <i class="fas fa-crown text-purple-500 text-5xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Pack Custom - Configuration sur mesure</h3>
                    <p class="text-gray-700 mb-6 text-lg">
                        Pour bénéficier d'une configuration personnalisée adaptée à vos besoins spécifiques,
                        contactez notre équipe support via WhatsApp. Nous serons ravis de vous accompagner
                        dans la création de votre événement avec toutes les fonctionnalités dont vous avez besoin.
                    </p>
                    <a href="https://wa.me/2250758942495?text=Bonjour%2C%20je%20souhaite%20cr%C3%A9er%20un%20%C3%A9v%C3%A9nement%20avec%20le%20pack%20Custom.%20Pouvez-vous%20m%27aider%20%3F"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="inline-flex items-center px-8 py-4 bg-green-500 rounded-lg hover:bg-green-600 transition-all shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="fab fa-whatsapp text-3xl mr-3"></i>
                        <span class="text-lg font-semibold">Contacter le support WhatsApp</span>
                    </a>
                    <p class="mt-4 text-sm text-gray-600">
                        <i class="fas fa-info-circle mr-2"></i>
                        Cliquez sur le bouton ci-dessus pour ouvrir WhatsApp et nous contacter directement
                    </p>
                </div>
            </div>

            <div class="form-section" style="margin-top: 2rem; padding: 1.5rem; background: #f9fafb; border-radius: 0.5rem; border-top: 2px solid #e5e7eb; box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);">
                <div class="flex justify-between">
                    <button type="button" class="px-6 py-3 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition font-semibold" onclick="window.goToStep && window.goToStep(5);" style="min-width: 140px;">
                        <i class="fas fa-arrow-left mr-2"></i>Précédent
                    </button>
                    <button type="button" id="nextFromStep6" class="px-6 py-3 rounded-lg hover:opacity-90 transition font-semibold shadow-md text-white" style="min-width: 140px; background-color: rgb(17, 19, 165);">
                        Suivant <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Étape 7: Finalisation -->
        <div class="wizard-step" id="step7">
            <div class="form-section">
                <h2 class="form-section-title">Finalisation</h2>
                <div class="space-y-4">
                    <div>
                        <label for="registration_open" class="flex items-center">
                            <input type="checkbox"
                                   id="registration_open"
                                   name="registration_open"
                                   value="1"
                                   {{ old('registration_open', true) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-primary-custom focus:ring-primary-custom">
                            <span class="ml-2 text-sm text-gray-700">Inscriptions ouvertes</span>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="registration_start_date" class="block text-sm font-medium text-gray-700 mb-1">Date de début des inscriptions</label>
                            <input type="text"
                                   id="registration_start_date"
                                   name="registration_start_date"
                                   value="{{ old('registration_start_date') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-custom focus:border-transparent @error('registration_start_date') border-red-500 @enderror"
                                   placeholder="Sélectionner une date">
                            @error('registration_start_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="registration_end_date" class="block text-sm font-medium text-gray-700 mb-1">Date de fin des inscriptions</label>
                            <input type="text"
                                   id="registration_end_date"
                                   name="registration_end_date"
                                   value="{{ old('registration_end_date') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-custom focus:border-transparent @error('registration_end_date') border-red-500 @enderror"
                                   placeholder="Sélectionner une date">
                            @error('registration_end_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox"
                                   id="is_published"
                                   name="is_published"
                                   value="1"
                                   {{ old('is_published') ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-primary-custom focus:ring-primary-custom">
                            <span class="ml-2 text-sm text-gray-700">Publier l'événement immédiatement</span>
                        </label>
                        <p class="mt-1 text-xs text-gray-500">Si non coché, l'événement sera sauvegardé en tant que brouillon</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-between" style="margin-top: 2rem; padding: 1.5rem; background: #f9fafb; border-radius: 0.5rem; border-top: 2px solid #e5e7eb; box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);">
                    <button type="button" class="px-6 py-3 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition font-semibold" onclick="goToStep(6)" style="min-width: 140px;">
                        <i class="fas fa-arrow-left mr-2"></i>Précédent
                    </button>
                    <button type="submit" class="px-6 py-3 rounded-lg hover:opacity-90 transition font-semibold shadow-md text-white" style="min-width: 200px; background-color: rgb(17, 19, 165);">
                        <i class="fas fa-save mr-2"></i>Enregistrer l'événement
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // Définir goToStep IMMÉDIATEMENT dans un script inline pour qu'elle soit accessible dès le chargement
    (function() {
        window.goToStep = function(step) {
            console.log('goToStep appelée avec step:', step);
            const totalSteps = 7;
            if (step < 1 || step > totalSteps) {
                console.error('Étape invalide:', step, 'doit être entre 1 et', totalSteps);
                return;
            }

            // Désactiver l'étape actuelle
            document.querySelectorAll('.wizard-step').forEach(s => s.classList.remove('active'));
            document.querySelectorAll('.wizard-step-indicator').forEach(i => {
                i.classList.remove('active');
                if (parseInt(i.dataset.step) < step) {
                    i.classList.add('completed');
                    i.querySelector('.wizard-step-circle').innerHTML = '<i class="fas fa-check"></i>';
                } else {
                    i.classList.remove('completed');
                    i.querySelector('.wizard-step-circle').textContent = i.dataset.step;
                }
            });

            // Activer la nouvelle étape
            const stepElement = document.getElementById('step' + step);
            console.log('Élément de l\'étape trouvé:', stepElement ? 'Oui' : 'Non', 'id:', 'step' + step);
            if (stepElement) {
                stepElement.classList.add('active');
                console.log('Étape', step, 'activée');
            } else {
                console.error('Élément de l\'étape', step, 'non trouvé!');
            }
            const indicatorElement = document.querySelector(`[data-step="${step}"]`);
            if (indicatorElement) {
                indicatorElement.classList.add('active');
            }

            // Scroll vers le haut pour voir l'étape
            window.scrollTo({ top: 0, behavior: 'smooth' });
        };
    })();
</script>
@endsection
@push('scripts')
<script>
    // Désactiver l'auto-découverte de Dropzone AVANT son chargement
    window.Dropzone = window.Dropzone || {};
    window.Dropzone.autoDiscover = false;
</script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/min/dropzone.min.js" crossorigin="anonymous" onload="if(typeof Dropzone !== 'undefined') { Dropzone.autoDiscover = false; }"></script>
<script>
    // Désactiver l'auto-découverte immédiatement après chargement
    if (typeof Dropzone !== 'undefined') {
        Dropzone.autoDiscover = false;
    }

    // Fonction pour attendre que Dropzone soit chargé
    function waitForDropzone(callback) {
        if (typeof Dropzone !== 'undefined') {
            Dropzone.autoDiscover = false;
            callback();
        } else {
            setTimeout(function() {
                waitForDropzone(callback);
            }, 100);
        }
    }

    let currentStep = 1;
    const totalSteps = 7;

    // Mettre à jour goToStep pour inclure currentStep si elle existe déjà
    if (window.goToStep && typeof window.goToStep === 'function') {
        const originalGoToStep = window.goToStep;
        window.goToStep = function(step) {
            originalGoToStep(step);
            currentStep = step;
        };
    } else {
    // Rendre goToStep accessible globalement
    window.goToStep = function(step) {
            console.log('goToStep appelée avec step:', step, 'totalSteps:', totalSteps);
            if (step < 1 || step > totalSteps) {
                console.error('Étape invalide:', step, 'doit être entre 1 et', totalSteps);
                return;
            }

        // Désactiver l'étape actuelle
        document.querySelectorAll('.wizard-step').forEach(s => s.classList.remove('active'));
        document.querySelectorAll('.wizard-step-indicator').forEach(i => {
            i.classList.remove('active');
            if (parseInt(i.dataset.step) < step) {
                i.classList.add('completed');
                i.querySelector('.wizard-step-circle').innerHTML = '<i class="fas fa-check"></i>';
            } else {
                i.classList.remove('completed');
                i.querySelector('.wizard-step-circle').textContent = i.dataset.step;
            }
        });

        // Activer la nouvelle étape
        const stepElement = document.getElementById('step' + step);
            console.log('Élément de l\'étape trouvé:', stepElement ? 'Oui' : 'Non', 'id:', 'step' + step);
        if (stepElement) {
            stepElement.classList.add('active');
                console.log('Étape', step, 'activée');
            } else {
                console.error('Élément de l\'étape', step, 'non trouvé!');
        }
        const indicatorElement = document.querySelector(`[data-step="${step}"]`);
        if (indicatorElement) {
            indicatorElement.classList.add('active');
        }
        currentStep = step;

        // Scroll vers le haut pour voir l'étape
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Gestionnaire pour le bouton "Suivant" de l'étape 6
        const nextFromStep6 = document.getElementById('nextFromStep6');
        if (nextFromStep6) {
            nextFromStep6.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Bouton Suivant étape 6 cliqué');
                if (window.goToStep && typeof window.goToStep === 'function') {
                    console.log('Appel de goToStep(7)');
                    window.goToStep(7);
                } else {
                    console.error('goToStep n\'est pas définie');
                    alert('Erreur: Impossible de passer à l\'étape suivante. Veuillez recharger la page.');
                }
            });
        }

        // Attendre que Dropzone soit chargé avant d'initialiser
        waitForDropzone(function() {
            initializeDropzones();
        });

        // Gestion du choix du pack
        const packCards = document.querySelectorAll('.pack-card');
        const nextFromStep1 = document.getElementById('nextFromStep1');
        const selectedPackType = document.getElementById('selected_pack_type');

        const step1Help = document.getElementById('step1Help');

        packCards.forEach(card => {
            card.addEventListener('click', function() {
                const packType = this.dataset.pack;

                // Si c'est le pack custom, afficher le popup sans sélectionner
                if (packType === 'custom') {
                    const modal = document.getElementById('customPackModal');
                    if (modal) {
                        modal.classList.add('active');
                    }
                    // Ne pas sélectionner le pack custom
                    return;
                }

                // Pour les autres packs, sélectionner normalement
                packCards.forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                selectedPackType.value = packType;
                nextFromStep1.disabled = false;
                if (step1Help) {
                    step1Help.innerHTML = '<i class="fas fa-check-circle mr-1 text-green-600"></i> Pack sélectionné. Cliquez sur "Suivant" pour continuer';
                    step1Help.className = 'text-sm text-green-600 font-medium';
                }
            });
        });

        // Gestion du popup custom pack
        const customPackModal = document.getElementById('customPackModal');
        const closeCustomPackModal = document.getElementById('closeCustomPackModal');

        // Fonction pour fermer le modal et réinitialiser
        function closeCustomModal() {
            if (customPackModal) {
                customPackModal.classList.remove('active');
            }
            // Désélectionner le pack custom s'il était sélectionné
            const customCard = document.querySelector('[data-pack="custom"]');
            if (customCard) {
                customCard.classList.remove('selected');
            }
            // Réinitialiser le champ hidden
            if (selectedPackType.value === 'custom') {
                selectedPackType.value = '';
            }
            // Réinitialiser le message d'aide
            if (step1Help) {
                step1Help.innerHTML = '<i class="fas fa-info-circle mr-1"></i> Veuillez sélectionner un pack pour continuer';
                step1Help.className = 'text-sm text-gray-600';
            }
            // Désactiver le bouton suivant
            nextFromStep1.disabled = true;
        }

        // Fermer le popup avec le bouton X
        if (closeCustomPackModal) {
            closeCustomPackModal.addEventListener('click', closeCustomModal);
        }

        // Fermer le popup en cliquant en dehors
        if (customPackModal) {
            customPackModal.addEventListener('click', function(e) {
                if (e.target === customPackModal) {
                    closeCustomModal();
                }
            });
        }

        // Si un pack est déjà sélectionné (old value) - mais pas custom
        if (selectedPackType.value && selectedPackType.value !== 'custom') {
            const selectedCard = document.querySelector(`[data-pack="${selectedPackType.value}"]`);
            if (selectedCard) {
                selectedCard.classList.add('selected');
                nextFromStep1.disabled = false;
                if (step1Help) {
                    step1Help.innerHTML = '<i class="fas fa-check-circle mr-1 text-green-600"></i> Pack sélectionné. Cliquez sur "Suivant" pour continuer';
                    step1Help.className = 'text-sm text-green-600 font-medium';
                }
            }
        }

        nextFromStep1.addEventListener('click', function() {
            if (selectedPackType.value && selectedPackType.value !== 'custom') {
                // Afficher la bonne section de personnalisation
                const packType = selectedPackType.value;
                document.getElementById('standardPack').style.display = packType === 'standard' ? 'block' : 'none';
                document.getElementById('premiumPack').style.display = packType === 'premium' ? 'block' : 'none';
                document.getElementById('customPack').style.display = packType === 'custom' ? 'block' : 'none';

                // Réinitialiser les dropzones si nécessaire
                if (packType === 'standard' && dropzoneStandard) {
                    dropzoneStandard.removeAllFiles(true);
                }
                if (packType === 'premium' && dropzonePremium) {
                    dropzonePremium.removeAllFiles(true);
                }

                goToStep(2);
            }
        });

        // Configuration Flatpickr
        const eventDateInput = document.getElementById('event_date');
        if (eventDateInput) {
            flatpickr(eventDateInput, {
                locale: "fr",
                dateFormat: "Y-m-d",
                minDate: "today",
                altInput: true,
                altFormat: "d/m/Y",
                allowInput: false
            });
        }

        const regStartDateInput = document.getElementById('registration_start_date');
        if (regStartDateInput) {
            flatpickr(regStartDateInput, {
                locale: "fr",
                dateFormat: "Y-m-d H:i",
                enableTime: true,
                time_24hr: true,
                altInput: true,
                altFormat: "d/m/Y H:i",
                allowInput: false
            });
        }

        const regEndDateInput = document.getElementById('registration_end_date');
        if (regEndDateInput) {
            flatpickr(regEndDateInput, {
                locale: "fr",
                dateFormat: "Y-m-d H:i",
                enableTime: true,
                time_24hr: true,
                altInput: true,
                altFormat: "d/m/Y H:i",
                allowInput: false
            });
        }

        // Configuration Flatpickr pour les champs d'heure (format 24h français)
        const eventStartTimeInput = document.getElementById('event_start_time');
        if (eventStartTimeInput) {
            flatpickr(eventStartTimeInput, {
                locale: "fr",
                noCalendar: true,
                enableTime: true,
                time_24hr: true,
                dateFormat: "H:i",
                altInput: true,
                altFormat: "H:i",
                allowInput: false
            });
        }

        const eventEndTimeInput = document.getElementById('event_end_time');
        if (eventEndTimeInput) {
            flatpickr(eventEndTimeInput, {
                locale: "fr",
                noCalendar: true,
                enableTime: true,
                time_24hr: true,
                dateFormat: "H:i",
                altInput: true,
                altFormat: "H:i",
                allowInput: false
            });
        }

        // Gérer l'affichage des champs de prix
        const requiresPayment = document.getElementById('requires_payment');
        const priceFields = document.getElementById('priceFields');

        function togglePriceFields() {
            if (requiresPayment && requiresPayment.checked) {
                if (priceFields) priceFields.style.display = 'grid';
            } else {
                if (priceFields) priceFields.style.display = 'none';
                const priceInput = document.getElementById('event_price');
                if (priceInput) priceInput.value = '0';
            }
        }

        if (requiresPayment) {
            togglePriceFields();
            requiresPayment.addEventListener('change', togglePriceFields);
        }

        // Gestion des tarifs multiples et options de paiement
        const requiresPaymentCheckbox = document.getElementById('requires_payment');
        const multipleTicketsOption = document.getElementById('multipleTicketsOption');
        const useMultipleTickets = document.getElementById('use_multiple_tickets');
        const singlePriceFields = document.getElementById('singlePriceFields');
        const multipleTicketsFields = document.getElementById('multipleTicketsFields');
        const paymentOptions = document.getElementById('paymentOptions');
        const addTicketBtn = document.getElementById('addTicketBtn');
        const ticketsContainer = document.getElementById('ticketsContainer');
        let ticketCounter = 0;

        // Afficher/masquer les options selon si l'événement est payant
        function togglePaymentOptions() {
            const isPayant = requiresPaymentCheckbox && requiresPaymentCheckbox.checked;
            if (multipleTicketsOption) {
                multipleTicketsOption.style.display = isPayant ? 'block' : 'none';
            }
            if (paymentOptions) {
                paymentOptions.style.display = isPayant ? 'block' : 'none';
            }
        }

        // Gérer l'affichage des tarifs uniques vs multiples
        function toggleTicketFields() {
            const useMultiple = useMultipleTickets && useMultipleTickets.checked;
            if (singlePriceFields) {
                singlePriceFields.style.display = useMultiple ? 'none' : 'grid';
            }
            if (multipleTicketsFields) {
                if (useMultiple) {
                    multipleTicketsFields.style.display = 'block';
                    multipleTicketsFields.style.visibility = 'visible';
                    // S'assurer que le bouton est visible
                    if (addTicketBtn) {
                        addTicketBtn.style.display = 'block';
                        addTicketBtn.style.visibility = 'visible';
                    }
                } else {
                    multipleTicketsFields.style.display = 'none';
                }
            }
        }

        // Gérer les options de paiement partiel
        const allowPartialPayment = document.getElementById('allow_partial_payment');
        const partialPaymentAmountField = document.getElementById('partialPaymentAmountField');

        function togglePartialPayment() {
            if (partialPaymentAmountField) {
                partialPaymentAmountField.style.display = (allowPartialPayment && allowPartialPayment.checked) ? 'block' : 'none';
            }
        }

        // Gérer les options de réservation
        const allowReservation = document.getElementById('allow_reservation');
        const reservationFields = document.getElementById('reservationFields');

        function toggleReservation() {
            if (reservationFields) {
                reservationFields.style.display = (allowReservation && allowReservation.checked) ? 'block' : 'none';
            }
        }

        // Ajouter un nouveau tarif
        function addTicket() {
            ticketCounter++;
            const ticketDiv = document.createElement('div');
            ticketDiv.className = 'bg-white rounded-lg p-4 border border-gray-200';
            ticketDiv.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom du tarif</label>
                        <input type="text" name="tickets[${ticketCounter}][ticket_name]" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-custom focus:border-transparent"
                               placeholder="Ex: VIP, Standard, Étudiant">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prix</label>
                        <input type="number" name="tickets[${ticketCounter}][ticket_price]" required min="0" step="0.01"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-custom focus:border-transparent">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description (optionnel)</label>
                        <textarea name="tickets[${ticketCounter}][ticket_description]" rows="2"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-custom focus:border-transparent"
                                  placeholder="Description du tarif"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantité disponible (optionnel)</label>
                        <input type="number" name="tickets[${ticketCounter}][quantity_available]" min="1"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-custom focus:border-transparent"
                               placeholder="Laisser vide pour illimité">
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="button" class="px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-sm" onclick="this.closest('div').remove()">
                        <i class="fas fa-trash mr-1"></i>Supprimer
                    </button>
                </div>
            `;
            ticketsContainer.appendChild(ticketDiv);
        }

        // Gestion des champs du formulaire
        const fieldCheckboxes = document.querySelectorAll('.field-checkbox');
        fieldCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const fieldKey = this.id.replace('field_', '');
                const requiredCheckbox = document.getElementById(`field_${fieldKey}_required`);
                if (requiredCheckbox) {
                    requiredCheckbox.disabled = !this.checked;
                    if (!this.checked) {
                        requiredCheckbox.checked = false;
                    }
                }
            });
        });

        // Initialisation
        if (requiresPaymentCheckbox) {
            requiresPaymentCheckbox.addEventListener('change', togglePaymentOptions);
            // Initialiser l'état au chargement
            togglePaymentOptions();
        }

        if (useMultipleTickets) {
            useMultipleTickets.addEventListener('change', function() {
                toggleTicketFields();
                // Forcer l'affichage du bouton si l'option est cochée
                if (useMultipleTickets.checked && addTicketBtn) {
                    addTicketBtn.style.display = 'block';
                    addTicketBtn.style.visibility = 'visible';
                }
            });
            // Initialiser l'état au chargement
            toggleTicketFields();
            // Forcer l'affichage du bouton si l'option est déjà cochée au chargement
            if (useMultipleTickets.checked && addTicketBtn) {
                addTicketBtn.style.display = 'block';
                addTicketBtn.style.visibility = 'visible';
            }
        } else {
            // Si l'élément n'existe pas encore, initialiser quand même
            if (multipleTicketsFields) {
                multipleTicketsFields.style.display = 'none';
            }
            if (singlePriceFields) {
                singlePriceFields.style.display = 'grid';
            }
        }

        if (allowPartialPayment) {
            allowPartialPayment.addEventListener('change', togglePartialPayment);
            togglePartialPayment();
        }

        if (allowReservation) {
            allowReservation.addEventListener('change', toggleReservation);
            toggleReservation();
        }

        if (addTicketBtn) {
            addTicketBtn.addEventListener('click', addTicket);
        }

        // Initialiser les champs du formulaire
        fieldCheckboxes.forEach(checkbox => {
            const fieldKey = checkbox.id.replace('field_', '');
            const requiredCheckbox = document.getElementById(`field_${fieldKey}_required`);
            if (requiredCheckbox) {
                requiredCheckbox.disabled = !checkbox.checked;
            }
        });
    });

    // Variables globales pour les dropzones
    let dropzoneStandard = null;
    let dropzonePremium = null;

    // Variables globales pour les rectangles (déclarées tôt pour être disponibles dans Dropzone)
    let standardElementRectangles = {};
    let standardElementColors = {
        qr_code: '#000000',
        name: '#000000',
        function: '#000000',
        ticket_id: '#000000',
        date_time: '#000000',
        seat: '#000000'
    };
    let premiumElementRectangles = {};
    let premiumElementColors = {
        qr_code: '#000000',
        name: '#000000',
        function: '#000000'
    };

    // Fonction pour initialiser les dropzones
    function initializeDropzones() {
        if (typeof Dropzone === 'undefined') {
            console.error('Dropzone n\'est pas disponible');
            return;
        }

        // Configuration Dropzone pour Pack Standard
        const dropzoneStandardEl = document.getElementById('dropzoneStandard');
        if (dropzoneStandardEl && !dropzoneStandard) {
            // Vérifier si Dropzone n'est pas déjà attaché
            if (dropzoneStandardEl.dropzone) {
                dropzoneStandard = dropzoneStandardEl.dropzone;
            } else {
                dropzoneStandard = new Dropzone("#dropzoneStandard", {
                url: "{{ route('org.events.store', ['org_slug' => $orgSlug]) }}", // URL nécessaire même si autoProcessQueue est false
                autoProcessQueue: false,
                parallelUploads: 1,
                maxFiles: 1,
                acceptedFiles: "image/jpeg,image/jpg,image/png,application/pdf",
                maxFilesize: 5, // 5 Mo
                dictDefaultMessage: "",
                dictMaxFilesExceeded: "Vous ne pouvez télécharger qu'un seul fichier",
                dictFileTooBig: "Le fichier est trop volumineux (@{{filesize}}MB). Taille max: @{{maxFilesize}}MB",
                dictInvalidFileType: "Type de fichier non autorisé. Formats acceptés: JPG, PNG, PDF",
                addRemoveLinks: false,
                dictRemoveFile: "Supprimer",
                previewTemplate: `
                    <div class="dz-preview dz-file-preview">
                        <div class="dz-image">
                            <img data-dz-thumbnail />
                        </div>
                        <div class="dz-details">
                            <div class="dz-size"><span data-dz-size></span></div>
                            <div class="dz-filename"><span data-dz-name></span></div>
                        </div>
                        <div class="dz-error-message"><span data-dz-errormessage></span></div>
                        <div class="dz-success-mark">
                            <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                <title>Check</title>
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <path d="M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.3679711 11.9306128,25.9283877 C10.3701962,27.4888043 10.3701962,30.0226002 11.9306128,31.5830168 L21.1666667,40.8190707 C22.7270833,42.3794873 25.2608792,42.3794873 26.8212958,40.8190707 L43.0693872,24.5709793 C44.6298038,23.0105627 44.6298038,20.4767668 43.0693872,18.9163502 C41.5089706,17.3559336 38.9751747,17.3559336 37.4147581,18.9163502 L23.5,32.8311458 Z" stroke-opacity="0.198794158" stroke="#747474" fill-opacity="0.816519475" fill="#FFFFFF"></path>
                                </g>
                            </svg>
                        </div>
                        <div class="dz-error-mark">
                            <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                <title>Error</title>
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <g stroke="#747474" stroke-opacity="0.198794158" stroke-width="3.83783784">
                                        <path d="M32.6568542,29 L38.3106978,23.3461564 C39.8771021,21.7797521 39.8758057,19.2483887 38.3137085,17.6862915 C36.7516113,16.1241943 34.2202479,16.1228979 32.6538436,17.6893022 L27,23.3431458 L21.3461564,17.6893022 C19.7797521,16.1228979 17.2483887,16.1241943 15.6862915,17.6862915 C14.1241943,19.2483887 14.1228979,21.7797521 15.6893022,23.3461564 L21.3431458,29 L15.6893022,34.6538436 C14.1228979,36.2202479 14.1241943,38.7516113 15.6862915,40.3137085 C17.2483887,41.8758057 19.7797521,41.8771021 21.3461564,40.3106978 L27,34.6568542 L32.6538436,40.3106978 C34.2202479,41.8771021 36.7516113,41.8758057 38.3137085,40.3137085 C39.8758057,38.7516113 39.8771021,36.2202479 38.3106978,34.6538436 L32.6568542,29 Z" id="path-2"></path>
                                    </g>
                                </g>
                            </svg>
                        </div>
                        <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
                        <a class="dz-remove" href="javascript:undefined;" data-dz-remove>Supprimer</a>
                    </div>
                `,
                init: function() {
                    const dropzone = this;
                    const hiddenInput = document.getElementById('ticket_template_standard');

                    dropzone.on("addedfile", function(file) {
                        // Si un fichier existait déjà, le supprimer
                        if (dropzone.files.length > 1) {
                            dropzone.removeFile(dropzone.files[0]);
                        }

                        // Créer un FormData et le stocker dans le fichier
                        const formData = new FormData();
                        formData.append('file', file);
                        file.formData = formData;

                        // Mettre à jour le champ hidden (on stockera juste le nom pour validation)
                        hiddenInput.value = file.name;

                        // Afficher la prévisualisation
                        if (file.type.startsWith('image/')) {
                            const preview = document.getElementById('standardPreview');
                            const interactivePreview = document.getElementById('standardInteractivePreview');
                            const canvas = document.getElementById('standardTicketCanvas');
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                // Aperçu simple
                                preview.innerHTML = `
                                    <div class="mt-2">
                                        <p class="text-sm font-medium text-gray-700 mb-2">Aperçu :</p>
                                        <img src="${e.target.result}" alt="Aperçu" class="max-w-full h-auto border border-gray-300 rounded-lg shadow-sm" style="max-height: 300px;">
                                    </div>
                                `;

                                // Charger dans l'image pour l'aperçu interactif
                                const imgElement = document.getElementById('standardTicketImage');
                                const container = document.getElementById('standardTicketContainer');
                                const interactivePreview = document.getElementById('standardInteractivePreview');

                                if (imgElement && container && interactivePreview) {
                                    imgElement.src = e.target.result;

                                    imgElement.onload = function() {
                                        // Afficher la zone interactive
                                        interactivePreview.style.display = 'block';

                                        // Fonction pour créer le rectangle QR code
                                        function createQRCodeRectangle() {
                                            // S'assurer que l'image est complètement chargée
                                            if (!imgElement.complete || imgElement.naturalWidth === 0) {
                                                setTimeout(createQRCodeRectangle, 100);
                                                return;
                                            }

                                            // Obtenir les dimensions réelles affichées
                                            const rect = imgElement.getBoundingClientRect();
                                            const imgWidth = rect.width || imgElement.offsetWidth || 800;
                                            const imgHeight = rect.height || imgElement.offsetHeight || 400;

                                            // Ajuster l'overlay pour qu'il ait exactement les mêmes dimensions que l'image
                                            const overlay = document.getElementById('standardTicketOverlay');
                                            if (overlay) {
                                                overlay.style.width = imgWidth + 'px';
                                                overlay.style.height = imgHeight + 'px';
                                            }

                                            // Position par défaut: coin supérieur droit
                                            const defaultX = Math.max(10, imgWidth - 110);
                                            const defaultY = 10;

                                            // Réinitialiser tous les rectangles si la fonction existe
                                            if (typeof resetStandardRectangles === 'function') {
                                                resetStandardRectangles();
                                            } else if (standardElementRectangles && typeof standardElementRectangles === 'object') {
                                                Object.keys(standardElementRectangles).forEach(element => {
                                                    const rect = standardElementRectangles[element];
                                                    if (rect && rect.remove) rect.remove();
                                                    delete standardElementRectangles[element];
                                                });
                                            }

                                            // Créer le rectangle QR code - FORCER la création même si les fonctions ne sont pas disponibles
                                            console.log('Tentative de création rectangle QR code Standard', {
                                                createStandardRectangle: typeof createStandardRectangle,
                                                standardElementColors: !!standardElementColors,
                                                imgWidth,
                                                imgHeight,
                                                defaultX,
                                                defaultY
                                            });

                                            // S'assurer que le checkbox QR code est coché
                                            const qrCodeCheckbox = document.querySelector('input[name="ticket_elements[]"][value="qr_code"]');
                                            if (qrCodeCheckbox && !qrCodeCheckbox.checked) {
                                                qrCodeCheckbox.checked = true;
                                                qrCodeCheckbox.dispatchEvent(new Event('change'));
                                            }

                                            function tryCreateRect() {
                                                if (typeof createStandardRectangle === 'function' && standardElementColors) {
                                                    const result = createStandardRectangle('qr_code', defaultX, defaultY, 100, 100, standardElementColors['qr_code'] || '#1113a5');
                                                    console.log('Résultat création:', result);
                                                    if (result && typeof updateStandardElementData === 'function') {
                                                        updateStandardElementData('qr_code');
                                                    }
                                                    return true;
                                                }
                                                return false;
                                            }

                                            if (!tryCreateRect()) {
                                                // Retarder et réessayer si les fonctions ne sont pas encore chargées
                                                console.log('Fonctions non disponibles, retry dans 100ms');
                                                setTimeout(function() {
                                                    if (!tryCreateRect()) {
                                                        console.log('Fonctions toujours non disponibles, retry dans 200ms');
                                                        setTimeout(function() {
                                                            if (!tryCreateRect()) {
                                                                console.log('Fonctions toujours non disponibles, retry dans 500ms');
                                                                setTimeout(tryCreateRect, 500);
                                                            }
                                                        }, 200);
                                                    }
                                                }, 100);
                                            }
                                        }

                                        // Attendre un peu pour que le DOM se mette à jour
                                        setTimeout(createQRCodeRectangle, 50);
                                    };
                                }
                            };
                            reader.readAsDataURL(file);
                        } else {
                            const preview = document.getElementById('standardPreview');
                            preview.innerHTML = `
                                <div class="mt-2">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Fichier sélectionné :</p>
                                    <div class="inline-flex items-center px-4 py-2 bg-gray-100 rounded-lg">
                                        <i class="fas fa-file-pdf text-red-500 mr-2"></i>
                                        <span class="text-sm text-gray-700">${file.name}</span>
                                    </div>
                                </div>
                            `;
                            // Pour les PDF, on ne peut pas les afficher sur canvas
                            const interactivePreview = document.getElementById('standardInteractivePreview');
                            if (interactivePreview) interactivePreview.style.display = 'none';
                        }
                    });

                    dropzone.on("removedfile", function(file) {
                        hiddenInput.value = '';
                        document.getElementById('standardPreview').innerHTML = '';
                    });

                    dropzone.on("error", function(file, errorMessage) {
                        console.error('Erreur Dropzone:', errorMessage);
                    });
                }
            });
            }
        }

        // Configuration Dropzone pour Pack Premium
        const dropzonePremiumEl = document.getElementById('dropzonePremium');
        if (dropzonePremiumEl && !dropzonePremium) {
            // Vérifier si Dropzone n'est pas déjà attaché
            if (dropzonePremiumEl.dropzone) {
                dropzonePremium = dropzonePremiumEl.dropzone;
            } else {
            dropzonePremium = new Dropzone("#dropzonePremium", {
                url: "{{ route('org.events.store', ['org_slug' => $orgSlug]) }}", // URL nécessaire même si autoProcessQueue est false
                autoProcessQueue: false,
                parallelUploads: 1,
                maxFiles: 1,
                acceptedFiles: "image/jpeg,image/jpg,image/png,application/pdf",
                maxFilesize: 10, // 10 Mo
                dictDefaultMessage: "",
                dictMaxFilesExceeded: "Vous ne pouvez télécharger qu'un seul fichier",
                dictFileTooBig: "Le fichier est trop volumineux (@{{filesize}}MB). Taille max: @{{maxFilesize}}MB",
                dictInvalidFileType: "Type de fichier non autorisé. Formats acceptés: JPG, PNG, PDF",
                addRemoveLinks: false,
                dictRemoveFile: "Supprimer",
                previewTemplate: `
                    <div class="dz-preview dz-file-preview">
                        <div class="dz-image">
                            <img data-dz-thumbnail />
                        </div>
                        <div class="dz-details">
                            <div class="dz-size"><span data-dz-size></span></div>
                            <div class="dz-filename"><span data-dz-name></span></div>
                        </div>
                        <div class="dz-error-message"><span data-dz-errormessage></span></div>
                        <div class="dz-success-mark">
                            <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                <title>Check</title>
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <path d="M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.3679711 11.9306128,25.9283877 C10.3701962,27.4888043 10.3701962,30.0226002 11.9306128,31.5830168 L21.1666667,40.8190707 C22.7270833,42.3794873 25.2608792,42.3794873 26.8212958,40.8190707 L43.0693872,24.5709793 C44.6298038,23.0105627 44.6298038,20.4767668 43.0693872,18.9163502 C41.5089706,17.3559336 38.9751747,17.3559336 37.4147581,18.9163502 L23.5,32.8311458 Z" stroke-opacity="0.198794158" stroke="#747474" fill-opacity="0.816519475" fill="#FFFFFF"></path>
                                </g>
                            </svg>
                        </div>
                        <div class="dz-error-mark">
                            <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                <title>Error</title>
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <g stroke="#747474" stroke-opacity="0.198794158" stroke-width="3.83783784">
                                        <path d="M32.6568542,29 L38.3106978,23.3461564 C39.8771021,21.7797521 39.8758057,19.2483887 38.3137085,17.6862915 C36.7516113,16.1241943 34.2202479,16.1228979 32.6538436,17.6893022 L27,23.3431458 L21.3461564,17.6893022 C19.7797521,16.1228979 17.2483887,16.1241943 15.6862915,17.6862915 C14.1241943,19.2483887 14.1228979,21.7797521 15.6893022,23.3461564 L21.3431458,29 L15.6893022,34.6538436 C14.1228979,36.2202479 14.1241943,38.7516113 15.6862915,40.3137085 C17.2483887,41.8758057 19.7797521,41.8771021 21.3461564,40.3106978 L27,34.6568542 L32.6538436,40.3106978 C34.2202479,41.8771021 36.7516113,41.8758057 38.3137085,40.3137085 C39.8758057,38.7516113 39.8771021,36.2202479 38.3106978,34.6538436 L32.6568542,29 Z" id="path-2"></path>
                                    </g>
                                </g>
                            </svg>
                        </div>
                        <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
                        <a class="dz-remove" href="javascript:undefined;" data-dz-remove>Supprimer</a>
                    </div>
                `,
                init: function() {
                    const dropzone = this;
                    const hiddenInput = document.getElementById('ticket_template_premium');

                    dropzone.on("addedfile", function(file) {
                        if (dropzone.files.length > 1) {
                            dropzone.removeFile(dropzone.files[0]);
                        }

                        const formData = new FormData();
                        formData.append('file', file);
                        file.formData = formData;

                        hiddenInput.value = file.name;

                        if (file.type.startsWith('image/')) {
                            const interactivePreview = document.getElementById('premiumInteractivePreview');
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                // Charger dans l'image pour l'aperçu interactif
                                const imgElement = document.getElementById('premiumTicketImage');
                                const container = document.getElementById('premiumTicketContainer');

                                if (imgElement && container && interactivePreview) {
                                    imgElement.src = e.target.result;

                                    imgElement.onload = function() {
                                        // Afficher la zone interactive
                                        interactivePreview.style.display = 'block';

                                        // Fonction pour synchroniser les dimensions de l'overlay
                                        function syncOverlaySize() {
                                            const overlay = document.getElementById('premiumTicketOverlay');
                                            if (overlay && imgElement) {
                                                // Utiliser offsetWidth/Height pour les dimensions réelles rendues
                                                const imgWidth = imgElement.offsetWidth || imgElement.clientWidth || 800;
                                                const imgHeight = imgElement.offsetHeight || imgElement.clientHeight || 400;

                                                if (imgWidth > 0 && imgHeight > 0) {
                                                    overlay.style.width = imgWidth + 'px';
                                                    overlay.style.height = imgHeight + 'px';
                                                    overlay.style.pointerEvents = 'all';
                                                    console.log('Overlay Premium synchronisé après chargement image:', {
                                                        imgWidth,
                                                        imgHeight,
                                                        overlayWidth: overlay.style.width,
                                                        overlayHeight: overlay.style.height
                                                    });
                                                }
                                            }
                                        }

                                        // Synchroniser les dimensions plusieurs fois pour s'assurer
                                        syncOverlaySize();
                                        setTimeout(syncOverlaySize, 50);
                                        setTimeout(syncOverlaySize, 200);
                                        requestAnimationFrame(syncOverlaySize);

                                        // Fonction pour créer le rectangle QR code
                                        function createQRCodeRectangle() {
                                            // S'assurer que l'image est complètement chargée
                                            if (!imgElement.complete || imgElement.naturalWidth === 0) {
                                                setTimeout(createQRCodeRectangle, 100);
                                                return;
                                            }

                                            // Obtenir les dimensions réelles affichées
                                            const imgWidth = imgElement.offsetWidth || imgElement.clientWidth || imgElement.getBoundingClientRect().width || 800;
                                            const imgHeight = imgElement.offsetHeight || imgElement.clientHeight || imgElement.getBoundingClientRect().height || 400;

                                            // Ajuster l'overlay pour qu'il ait exactement les mêmes dimensions que l'image
                                            const overlay = document.getElementById('premiumTicketOverlay');
                                            if (overlay) {
                                                overlay.style.width = imgWidth + 'px';
                                                overlay.style.height = imgHeight + 'px';
                                                overlay.style.pointerEvents = 'all';
                                            }

                                            // Position par défaut: coin supérieur droit
                                            const defaultX = Math.max(10, imgWidth - 110);
                                            const defaultY = 10;

                                            // Réinitialiser tous les rectangles si la fonction existe
                                            if (typeof resetPremiumRectangles === 'function') {
                                                resetPremiumRectangles();
                                            } else if (premiumElementRectangles && typeof premiumElementRectangles === 'object') {
                                                Object.keys(premiumElementRectangles).forEach(element => {
                                                    const rect = premiumElementRectangles[element];
                                                    if (rect && rect.remove) rect.remove();
                                                    delete premiumElementRectangles[element];
                                                });
                                            }

                                            // Créer le rectangle QR code - FORCER la création même si les fonctions ne sont pas disponibles
                                            console.log('Tentative de création rectangle QR code Premium', {
                                                createPremiumRectangle: typeof createPremiumRectangle,
                                                premiumElementColors: !!premiumElementColors,
                                                imgWidth,
                                                imgHeight,
                                                defaultX,
                                                defaultY
                                            });

                                            function tryCreateRect() {
                                                if (typeof createPremiumRectangle === 'function' && premiumElementColors) {
                                                    const result = createPremiumRectangle('qr_code', defaultX, defaultY, 100, 100, premiumElementColors['qr_code'] || '#1113a5');
                                                    console.log('Résultat création:', result);
                                                    if (result && typeof updatePremiumElementData === 'function') {
                                                        updatePremiumElementData('qr_code');
                                                    }
                                                    return true;
                                                }
                                                return false;
                                            }

                                            if (!tryCreateRect()) {
                                                // Retarder et réessayer si les fonctions ne sont pas encore chargées
                                                console.log('Fonctions non disponibles, retry dans 100ms');
                                                setTimeout(function() {
                                                    if (!tryCreateRect()) {
                                                        console.log('Fonctions toujours non disponibles, retry dans 200ms');
                                                        setTimeout(function() {
                                                            if (!tryCreateRect()) {
                                                                console.log('Fonctions toujours non disponibles, retry dans 500ms');
                                                                setTimeout(tryCreateRect, 500);
                                                            }
                                                        }, 200);
                                                    }
                                                }, 100);
                                            }
                                        }

                                        // Attendre un peu pour que le DOM se mette à jour
                                        setTimeout(createQRCodeRectangle, 50);
                                    };
                                }
                            };
                            reader.readAsDataURL(file);
                        } else {
                            // Pour les PDF, on ne peut pas les afficher
                            const interactivePreview = document.getElementById('premiumInteractivePreview');
                            if (interactivePreview) interactivePreview.style.display = 'none';
                        }
                    });

                    dropzone.on("removedfile", function(file) {
                        hiddenInput.value = '';
                        // Masquer la zone interactive si le fichier est supprimé
                        const interactivePreview = document.getElementById('premiumInteractivePreview');
                        if (interactivePreview) {
                            interactivePreview.style.display = 'none';
                        }
                        // Réinitialiser les rectangles
                        if (typeof resetPremiumRectangles === 'function') {
                            resetPremiumRectangles();
                        }
                    });

                    dropzone.on("error", function(file, errorMessage) {
                        console.error('Erreur Dropzone:', errorMessage);
                    });
                }
            });
            }
        }
    }

    // Gérer l'affichage des éléments pour Premium (sans aperçu statique)
    document.addEventListener('DOMContentLoaded', function() {
        const showNamePremium = document.getElementById('show_name_premium');
        const showFunctionPremium = document.getElementById('show_function_premium');

        function updateElementVisibility() {
            // Afficher/masquer les panneaux de configuration dans l'aperçu interactif
            const namePanel = document.querySelector('.element-config-panel-premium[data-element="name"]');
            const functionPanel = document.querySelector('.element-config-panel-premium[data-element="function"]');
            const seatPanel = document.querySelector('.element-config-panel-premium[data-element="seat"]');
            const showSeatPremium = document.getElementById('show_seat_premium');

            // ID Ticket est toujours visible (obligatoire)
            const ticketIdPanel = document.querySelector('.element-config-panel-premium[data-element="ticket_id"]');
            if (ticketIdPanel) {
                ticketIdPanel.style.display = 'block';
            }

            if (showNamePremium && namePanel) {
                namePanel.style.display = showNamePremium.checked ? 'block' : 'none';
            }
            if (showFunctionPremium && functionPanel) {
                functionPanel.style.display = showFunctionPremium.checked ? 'block' : 'none';
            }
            if (showSeatPremium && seatPanel) {
                seatPanel.style.display = showSeatPremium.checked ? 'block' : 'none';
            }
        }

        if (showNamePremium) showNamePremium.addEventListener('change', updateElementVisibility);
        if (showFunctionPremium) showFunctionPremium.addEventListener('change', updateElementVisibility);
        const showSeatPremium = document.getElementById('show_seat_premium');
        if (showSeatPremium) showSeatPremium.addEventListener('change', updateElementVisibility);

        updateElementVisibility();

        // Gestion de la soumission du formulaire avec Dropzone
        const eventForm = document.getElementById('eventForm');
        if (eventForm) {
            eventForm.addEventListener('submit', function(e) {
                const selectedPackType = document.getElementById('selected_pack_type');
                if (!selectedPackType) {
                    console.error('selected_pack_type element not found');
                    return; // Laisser la soumission normale se faire
                }

                const packType = selectedPackType.value;

                // Mettre à jour toutes les valeurs des éléments premium avant la soumission
                if (packType === 'premium') {
                    console.log('Mise à jour des valeurs premium avant soumission');

                    // Mettre à jour ticket_id (obligatoire)
                    const ticketIdXInput = document.getElementById('premium_ticket_id_x');
                    const ticketIdYInput = document.getElementById('premium_ticket_id_y');

                    if (premiumElementRectangles['ticket_id']) {
                        console.log('Rectangle ticket_id existe, mise à jour...');
                        updatePremiumElementData('ticket_id');
                    } else {
                        console.log('Rectangle ticket_id n\'existe pas, création...');
                        // Si le rectangle n'existe pas, créer avec des valeurs par défaut
                        const imgElement = document.getElementById('premiumTicketImage');
                        if (imgElement && imgElement.src) {
                            // Obtenir les dimensions affichées (taille à l'écran)
                            const displayedWidth = imgElement.offsetWidth || imgElement.clientWidth || 800;
                            const displayedHeight = imgElement.offsetHeight || imgElement.clientHeight || 400;

                            // Position par défaut en coordonnées affichées (coin supérieur droit)
                            const displayedX = Math.max(10, displayedWidth - 110);
                            const displayedY = 10;
                            const displayedWidth_rect = 100;
                            const displayedHeight_rect = 30;
                            const color = premiumElementColors['ticket_id'] || '#000000';

                            // Obtenir les dimensions naturelles (taille réelle de l'image)
                            const naturalWidth = imgElement.naturalWidth || 800;
                            const naturalHeight = imgElement.naturalHeight || 400;

                            // Calculer les ratios de conversion de displayed vers natural
                            const scaleX = displayedWidth > 0 ? naturalWidth / displayedWidth : 1;
                            const scaleY = displayedHeight > 0 ? naturalHeight / displayedHeight : 1;

                            // Convertir les coordonnées affichées en coordonnées naturelles pour sauvegarde
                            const naturalX = Math.round(displayedX * scaleX);
                            const naturalY = Math.round(displayedY * scaleY);
                            const naturalWidth_rect = Math.round(displayedWidth_rect * scaleX);
                            const naturalHeight_rect = Math.round(displayedHeight_rect * scaleY);

                            // Mettre à jour directement les champs hidden
                            if (ticketIdXInput) ticketIdXInput.value = naturalX;
                            if (ticketIdYInput) ticketIdYInput.value = naturalY;
                            const ticketIdWidthInput = document.getElementById('premium_ticket_id_width');
                            const ticketIdHeightInput = document.getElementById('premium_ticket_id_height');
                            if (ticketIdWidthInput) ticketIdWidthInput.value = naturalWidth_rect;
                            if (ticketIdHeightInput) ticketIdHeightInput.value = naturalHeight_rect;

                            console.log('Valeurs ticket_id définies:', {
                                displayed: { x: displayedX, y: displayedY, width: displayedWidth_rect, height: displayedHeight_rect },
                                natural: { x: naturalX, y: naturalY, width: naturalWidth_rect, height: naturalHeight_rect },
                                scales: { scaleX, scaleY }
                            });
                        } else {
                            // Si pas d'image, utiliser des valeurs par défaut absolues
                            if (ticketIdXInput) ticketIdXInput.value = 500;
                            if (ticketIdYInput) ticketIdYInput.value = 10;
                            console.log('Pas d\'image, valeurs par défaut définies pour ticket_id');
                        }
                    }

                    // Vérifier les valeurs finales
                    console.log('Valeurs ticket_id avant soumission:', {
                        x: ticketIdXInput ? ticketIdXInput.value : 'N/A',
                        y: ticketIdYInput ? ticketIdYInput.value : 'N/A'
                    });

                    // Mettre à jour seat si la checkbox est cochée
                    const showSeatCheck = document.getElementById('show_seat_premium');
                    if (showSeatCheck && showSeatCheck.checked) {
                        console.log('Checkbox seat cochée, mise à jour...');
                        const seatXInput = document.getElementById('premium_seat_x');
                        const seatYInput = document.getElementById('premium_seat_y');

                        if (premiumElementRectangles['seat']) {
                            updatePremiumElementData('seat');
                        } else {
                            console.log('Rectangle seat n\'existe pas, création...');
                            // Si le rectangle n'existe pas, créer avec des valeurs par défaut
                            const imgElement = document.getElementById('premiumTicketImage');
                            if (imgElement && imgElement.src) {
                                // Obtenir les dimensions affichées (taille à l'écran)
                                const displayedWidth = imgElement.offsetWidth || imgElement.clientWidth || 800;
                                const displayedHeight = imgElement.offsetHeight || imgElement.clientHeight || 400;

                                // Position par défaut en coordonnées affichées
                                const displayedX = 10;
                                const displayedY = displayedHeight > 100 ? 130 : 10;
                                const displayedWidth_rect = 100;
                                const displayedHeight_rect = 30;

                                // Obtenir les dimensions naturelles (taille réelle de l'image)
                                const naturalWidth = imgElement.naturalWidth || 800;
                                const naturalHeight = imgElement.naturalHeight || 400;

                                // Calculer les ratios de conversion de displayed vers natural
                                const scaleX = displayedWidth > 0 ? naturalWidth / displayedWidth : 1;
                                const scaleY = displayedHeight > 0 ? naturalHeight / displayedHeight : 1;

                                // Convertir les coordonnées affichées en coordonnées naturelles pour sauvegarde
                                const naturalX = Math.round(displayedX * scaleX);
                                const naturalY = Math.round(displayedY * scaleY);
                                const naturalWidth_rect = Math.round(displayedWidth_rect * scaleX);
                                const naturalHeight_rect = Math.round(displayedHeight_rect * scaleY);

                                // Mettre à jour directement les champs hidden
                                if (seatXInput) seatXInput.value = naturalX;
                                if (seatYInput) seatYInput.value = naturalY;
                                const seatWidthInput = document.getElementById('premium_seat_width');
                                const seatHeightInput = document.getElementById('premium_seat_height');
                                if (seatWidthInput) seatWidthInput.value = naturalWidth_rect;
                                if (seatHeightInput) seatHeightInput.value = naturalHeight_rect;

                                console.log('Valeurs seat définies:', {
                                    x: naturalX,
                                    y: naturalY,
                                    width: naturalWidth,
                                    height: naturalHeight
                                });
                            } else {
                                // Si pas d'image, utiliser des valeurs par défaut absolues
                                if (seatXInput) seatXInput.value = 10;
                                if (seatYInput) seatYInput.value = 130;
                                console.log('Pas d\'image, valeurs par défaut définies pour seat');
                            }
                        }

                        // Vérifier les valeurs finales
                        console.log('Valeurs seat avant soumission:', {
                            x: seatXInput ? seatXInput.value : 'N/A',
                            y: seatYInput ? seatYInput.value : 'N/A'
                        });
                    }

                    // Mettre à jour les autres éléments si nécessaire
                    ['name', 'function', 'qr_code'].forEach(element => {
                        if (premiumElementRectangles[element]) {
                            updatePremiumElementData(element);
                        }
                    });
                }

                let hasDropzoneFile = false;

                // Vérifier et préparer les fichiers Dropzone
                if (packType === 'standard' && dropzoneStandard && dropzoneStandard.files.length > 0) {
                    hasDropzoneFile = true;
                } else if (packType === 'premium' && dropzonePremium && dropzonePremium.files.length > 0) {
                    hasDropzoneFile = true;
                }

                if (hasDropzoneFile) {
                    e.preventDefault();

                    // Créer un nouveau FormData à partir du formulaire
                    // Cela gère automatiquement tous les champs, y compris les selects vides
                    const formData = new FormData(eventForm);

                    // Supprimer les fichiers input normaux si on utilise Dropzone
                    if (packType === 'standard') {
                        formData.delete('ticket_template_standard');
                    } else if (packType === 'premium') {
                        formData.delete('ticket_template_premium');
                    }

                    // Note: event_type_id est maintenant required, donc on ne le supprime pas même s'il est vide
                    // La validation côté serveur gérera l'erreur appropriée

                    // Ajouter le fichier Dropzone
                    if (packType === 'standard' && dropzoneStandard.files.length > 0) {
                        formData.append('ticket_template_standard', dropzoneStandard.files[0]);
                    } else if (packType === 'premium' && dropzonePremium.files.length > 0) {
                        formData.append('ticket_template_premium', dropzonePremium.files[0]);
                    }

                    // Le token CSRF est déjà inclus par FormData(eventForm)

                    // Afficher un indicateur de chargement
                    const submitBtn = eventForm.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Enregistrement...';

                    fetch(eventForm.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    })
                    .then(response => {
                        if (response.redirected) {
                            window.location.href = response.url;
                            return;
                        }

                        // Vérifier si la réponse est OK avant de parser le JSON
                        if (!response.ok) {
                            // Si la réponse n'est pas OK, essayer de parser le JSON pour les erreurs
                            return response.text().then(text => {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalText;
                                try {
                                    const data = JSON.parse(text);
                                    throw new Error(data.message || 'Une erreur est survenue lors de la soumission.');
                                } catch (e) {
                                    if (e.message) throw e;
                                    throw new Error('Erreur ' + response.status + ': ' + response.statusText);
                                }
                            });
                        }

                        return response.json().then(data => {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;

                            if (data.success) {
                                // Succès - rediriger
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                } else {
                                    window.location.reload();
                                }
                            } else {
                                // Erreurs de validation
                                if (data.errors) {
                                    let errorMessages = [];
                                    for (let field in data.errors) {
                                        if (data.errors.hasOwnProperty(field)) {
                                            data.errors[field].forEach(error => {
                                                errorMessages.push(error);
                                            });
                                        }
                                    }

                                    if (errorMessages.length > 0) {
                                        alert('Erreurs de validation:\n\n' + errorMessages.join('\n'));
                                        console.error('Validation errors:', data.errors);
                                    } else {
                                        alert(data.message || 'Veuillez corriger les erreurs du formulaire.');
                                    }
                                } else {
                                    alert(data.message || 'Une erreur est survenue. Veuillez réessayer.');
                                }
                            }
                        });
                    })
                    .catch(error => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                        console.error('Erreur:', error);
                        alert('Une erreur est survenue. Veuillez réessayer.');
                    });

                    return false;
                }
                // Si pas de fichier Dropzone requis ou si pack custom, laisser le submit normal se faire
            });
        }

        // ===== GESTION DES RECTANGLES DRAGGABLES POUR STANDARD =====
        // Variables déjà déclarées en haut du fichier, pas besoin de les redéclarer ici
        let draggingElement = null;
        let resizingElement = null;
        let dragOffset = { x: 0, y: 0 };

        // Fonction pour traduire les noms d'éléments en français
        function getElementLabel(element) {
            const labels = {
                'qr_code': 'QR Code',
                'name': 'Nom & Prénoms',
                'function': 'Fonction',
                'ticket_id': 'ID Ticket',
                'date_time': 'Date & Heure',
                'seat': 'Siège'
            };
            return labels[element] || element.replace('_', ' ');
        }

        // Créer un rectangle pour un élément
        function createStandardRectangle(element, x, y, width, height, color) {
            const container = document.getElementById('standardTicketContainer');
            const overlay = document.getElementById('standardTicketOverlay');
            if (!container || !overlay) {
                console.error('Container or overlay not found for Standard');
                return null;
            }

            // Supprimer l'ancien rectangle s'il existe
            const existing = document.getElementById(`standard_rect_${element}`);
            if (existing && existing.remove) {
                existing.remove();
            }

            const rect = document.createElement('div');
            rect.id = `standard_rect_${element}`;
            rect.className = 'element-rectangle';
            rect.style.position = 'absolute';
            rect.style.left = x + 'px';
            rect.style.top = y + 'px';
            rect.style.width = width + 'px';
            rect.style.height = height + 'px';
            rect.style.borderColor = color || '#1113a5';
            rect.style.borderWidth = '3px';
            rect.style.borderStyle = 'solid';
            rect.style.zIndex = '1000';
            rect.style.backgroundColor = 'rgba(255, 255, 255, 0.4)';
            rect.style.borderRadius = '4px';
            rect.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.2)';
            rect.dataset.element = element;

            // Debug: vérifier que le rectangle sera visible
            console.log('Création rectangle Standard:', {
                element, x, y, width, height, color,
                styles: {
                    left: x + 'px',
                    top: y + 'px',
                    width: width + 'px',
                    height: height + 'px',
                    borderColor: color
                }
            });

            const label = document.createElement('div');
            label.className = 'element-rectangle-label';
            label.textContent = getElementLabel(element);
            label.style.color = color;
            label.style.pointerEvents = 'none'; // S'assurer que le label n'interfère pas
            rect.appendChild(label);

            const resizeHandle = document.createElement('div');
            resizeHandle.className = 'element-resize-handle';
            resizeHandle.style.pointerEvents = 'all'; // S'assurer que le handle capture les événements
            rect.appendChild(resizeHandle);

            overlay.appendChild(rect);

            // S'assurer que l'overlay a les mêmes dimensions que l'image
            const img = document.getElementById('standardTicketImage');
            if (img) {
                // Attendre le prochain frame pour que l'image soit complètement rendue
                requestAnimationFrame(function() {
                    const imgRect = img.getBoundingClientRect();
                    const containerRect = container.getBoundingClientRect();
                    overlay.style.width = imgRect.width + 'px';
                    overlay.style.height = imgRect.height + 'px';
                    console.log('Overlay Standard dimensionné:', {
                        imgWidth: imgRect.width,
                        imgHeight: imgRect.height,
                        overlayWidth: overlay.style.width,
                        overlayHeight: overlay.style.height
                    });
                });
            }

            overlay.style.pointerEvents = 'all';
            overlay.style.position = 'absolute';
            overlay.style.top = '0';
            overlay.style.left = '0';

            standardElementRectangles[element] = rect;

            // Vérifier que le rectangle est bien dans le DOM
            setTimeout(function() {
                const rectInDOM = document.getElementById(`standard_rect_${element}`);
                console.log('Rectangle Standard vérifié dans DOM:', {
                    element,
                    exists: !!rectInDOM,
                    visible: rectInDOM ? (rectInDOM.offsetWidth > 0 && rectInDOM.offsetHeight > 0) : false,
                    computedStyles: rectInDOM ? window.getComputedStyle(rectInDOM) : null
                });
            }, 100);

            // Gestion du drag
            rect.addEventListener('mousedown', function(e) {
                // Ignorer si c'est le resize handle
                if (e.target === resizeHandle || e.target.closest('.element-resize-handle')) {
                    return;
                }

                draggingElement = rect;
                const overlay = document.getElementById('standardTicketOverlay');
                if (!overlay) {
                    console.error('Overlay not found for Standard drag');
                    return;
                }

                const overlayBounds = overlay.getBoundingClientRect();

                // Calculer l'offset : position du clic dans l'overlay - position actuelle du rectangle
                const currentLeft = parseFloat(rect.style.left) || 0;
                const currentTop = parseFloat(rect.style.top) || 0;
                dragOffset.x = (e.clientX - overlayBounds.left) - currentLeft;
                dragOffset.y = (e.clientY - overlayBounds.top) - currentTop;

                console.log('Drag started Standard:', {
                    mouseX: e.clientX,
                    mouseY: e.clientY,
                    overlayLeft: overlayBounds.left,
                    overlayTop: overlayBounds.top,
                    currentLeft,
                    currentTop,
                    dragOffset,
                    rect: rect
                });

                document.querySelectorAll('#standardTicketOverlay .element-rectangle').forEach(r => r.classList.remove('selected'));
                rect.classList.add('selected');
                rect.style.cursor = 'grabbing';

                e.preventDefault();
                e.stopPropagation();
                return false;
            });

            // Gestion du resize
            resizeHandle.addEventListener('mousedown', function(e) {
                resizingElement = rect;
                e.stopPropagation();
                e.preventDefault();
            });

            return rect;
        }

        // Mettre à jour les champs cachés avec la position et taille
        function updateStandardElementData(element) {
            const rect = standardElementRectangles[element];
            if (!rect) return;

            // Utiliser directement les styles left/top qui sont déjà relatifs à l'overlay
            const x = parseFloat(rect.style.left) || 0;
            const y = parseFloat(rect.style.top) || 0;
            const width = parseFloat(rect.style.width) || rect.offsetWidth;
            const height = parseFloat(rect.style.height) || rect.offsetHeight;

            document.getElementById(`standard_${element}_x`).value = Math.round(x);
            document.getElementById(`standard_${element}_y`).value = Math.round(y);
            const widthInput = document.getElementById(`standard_${element}_width`);
            const heightInput = document.getElementById(`standard_${element}_height`);
            if (widthInput) widthInput.value = Math.round(width);
            if (heightInput) heightInput.value = Math.round(height);

            updateElementStatus(element, true);
        }

        function updateElementStatus(element, isPositioned) {
            const statusBadge = document.getElementById(`${element}_status`);
            if (statusBadge) {
                if (isPositioned) {
                    statusBadge.classList.add('active');
                    statusBadge.title = 'Position définie';
                } else {
                    statusBadge.classList.remove('active');
                    statusBadge.title = 'Position non définie';
                }
            }
        }

        function resetStandardRectangles() {
            Object.keys(standardElementRectangles).forEach(element => {
                const rect = standardElementRectangles[element];
                if (rect) rect.remove();
                delete standardElementRectangles[element];
                document.getElementById(`standard_${element}_x`).value = '';
                document.getElementById(`standard_${element}_y`).value = '';
                updateElementStatus(element, false);
            });
        }

        // Gestion globale du drag pour Standard
        document.addEventListener('mousemove', function(e) {
            // Ignorer si on drag Premium
            if (premiumDraggingElement || premiumResizingElement) return;

            if (draggingElement) {
                const overlay = document.getElementById('standardTicketOverlay');
                if (!overlay) return;

                const overlayBounds = overlay.getBoundingClientRect();

                // Calculer la nouvelle position par rapport à l'overlay
                let x = e.clientX - overlayBounds.left - dragOffset.x;
                let y = e.clientY - overlayBounds.top - dragOffset.y;

                // Limiter dans les bounds de l'overlay
                const maxX = overlayBounds.width - draggingElement.offsetWidth;
                const maxY = overlayBounds.height - draggingElement.offsetHeight;
                x = Math.max(0, Math.min(x, maxX));
                y = Math.max(0, Math.min(y, maxY));

                draggingElement.style.left = x + 'px';
                draggingElement.style.top = y + 'px';

                const element = draggingElement.dataset.element;
                updateStandardElementData(element);
            } else if (resizingElement) {
                const overlay = document.getElementById('standardTicketOverlay');
                if (!overlay) return;

                const overlayBounds = overlay.getBoundingClientRect();
                const rectBounds = resizingElement.getBoundingClientRect();

                // Calculer la nouvelle taille
                let width = e.clientX - rectBounds.left;
                let height = e.clientY - rectBounds.top;

                width = Math.max(50, width);
                height = Math.max(30, height);

                // Limiter à la taille de l'overlay
                const currentX = parseFloat(resizingElement.style.left) || 0;
                const currentY = parseFloat(resizingElement.style.top) || 0;
                const maxWidth = overlayBounds.width - currentX;
                const maxHeight = overlayBounds.height - currentY;
                width = Math.min(width, maxWidth);
                height = Math.min(height, maxHeight);

                resizingElement.style.width = width + 'px';
                resizingElement.style.height = height + 'px';

                const element = resizingElement.dataset.element;
                updateStandardElementData(element);
            }
        });

        document.addEventListener('mouseup', function(e) {
            if (draggingElement) {
                draggingElement.style.cursor = 'move';
                const element = draggingElement.dataset.element;
                updateStandardElementData(element);
                console.log('Drag ended Standard:', element);
            }
            if (resizingElement) {
                const element = resizingElement.dataset.element;
                updateStandardElementData(element);
                console.log('Resize ended Standard:', element);
            }
            draggingElement = null;
            resizingElement = null;
        });

        // Gestion des sélecteurs de couleur pour Standard
        document.querySelectorAll('.element-color-picker[data-element]').forEach(picker => {
            picker.addEventListener('change', function() {
                const element = this.dataset.element;
                const color = this.value;
                standardElementColors[element] = color;
                document.getElementById(`standard_${element}_color`).value = color;

                const rect = standardElementRectangles[element];
                if (rect) {
                    rect.style.borderColor = color;
                    const label = rect.querySelector('.element-rectangle-label');
                    if (label) label.style.color = color;
                }
            });
        });

        // Bouton réinitialiser Standard
        const resetStandardBtn = document.getElementById('resetStandardPositions');
        if (resetStandardBtn) {
            resetStandardBtn.addEventListener('click', function() {
                if (confirm('Êtes-vous sûr de vouloir réinitialiser toutes les positions ?')) {
                    resetStandardRectangles();

                    // Recréer le rectangle QR code
                    const imgElement = document.getElementById('standardTicketImage');
                    if (imgElement && imgElement.src) {
                        const imgWidth = imgElement.offsetWidth || imgElement.width;
                        const defaultX = Math.max(10, imgWidth - 110);
                        createStandardRectangle('qr_code', defaultX, 10, 100, 100, standardElementColors['qr_code']);
                        updateStandardElementData('qr_code');
                    }
                }
            });
        }

        // Initialiser les panneaux de configuration Standard au chargement
        document.querySelectorAll('input[name="ticket_elements[]"]').forEach(checkbox => {
            const element = checkbox.value;
            const configPanel = document.querySelector(`.element-config-panel[data-element="${element}"]`);
            if (configPanel) {
                configPanel.style.display = checkbox.checked ? 'block' : 'none';
            }
            // Charger les couleurs depuis les champs cachés si elles existent
            const colorInput = document.getElementById(`standard_${element}_color`);
            if (colorInput && colorInput.value) {
                standardElementColors[element] = colorInput.value;
                const colorPicker = document.querySelector(`.element-color-picker[data-element="${element}"]`);
                if (colorPicker) colorPicker.value = colorInput.value;
            }
        });

        // Mettre à jour les panneaux de configuration selon les éléments sélectionnés pour Standard
        document.querySelectorAll('input[name="ticket_elements[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const element = this.value;
                const configPanel = document.querySelector(`.element-config-panel[data-element="${element}"]`);
                const imgElement = document.getElementById('standardTicketImage');

                if (configPanel) {
                    configPanel.style.display = this.checked ? 'block' : 'none';
                }

                if (this.checked && imgElement && imgElement.src) {
                    // Créer le rectangle pour cet élément
                    const imgWidth = imgElement.offsetWidth || imgElement.width;
                    const imgHeight = imgElement.offsetHeight || imgElement.height;

                    // Position par défaut différente selon le type d'élément
                    let defaultX = 10, defaultY = 10, defaultWidth = 150, defaultHeight = 30;
                    if (element === 'qr_code') {
                        defaultWidth = 100;
                        defaultHeight = 100;
                        defaultX = Math.max(10, imgWidth - 110);
                    } else if (element === 'ticket_id' || element === 'seat') {
                        defaultWidth = 100;
                        defaultX = Math.max(10, imgWidth - 110);
                        defaultY = imgHeight > 100 ? 120 : 10;
                    } else {
                        defaultY = imgHeight > 100 ? 50 : 10;
                    }

                    // Charger les valeurs existantes si disponibles
                    const x = document.getElementById(`standard_${element}_x`).value || defaultX;
                    const y = document.getElementById(`standard_${element}_y`).value || defaultY;
                    const width = document.getElementById(`standard_${element}_width`).value || defaultWidth;
                    const height = document.getElementById(`standard_${element}_height`).value || defaultHeight;
                    const color = standardElementColors[element] || '#000000';

                    createStandardRectangle(element, parseInt(x), parseInt(y), parseInt(width), parseInt(height), color);
                    updateStandardElementData(element);
                } else if (!this.checked) {
                    // Supprimer le rectangle
                    const rect = standardElementRectangles[element];
                    if (rect) {
                        rect.remove();
                        delete standardElementRectangles[element];
                    }
                    document.getElementById(`standard_${element}_x`).value = '';
                    document.getElementById(`standard_${element}_y`).value = '';
                    updateElementStatus(element, false);
                }
            });
        });

        // ===== GESTION DES RECTANGLES DRAGGABLES POUR PREMIUM =====
        // Variables déjà déclarées en haut du fichier, pas besoin de les redéclarer ici
        let premiumDraggingElement = null;
        let premiumResizingElement = null;
        let premiumDragOffset = { x: 0, y: 0 };

        // Créer un rectangle pour un élément Premium
        function createPremiumRectangle(element, x, y, width, height, color) {
            const container = document.getElementById('premiumTicketContainer');
            const overlay = document.getElementById('premiumTicketOverlay');
            if (!container || !overlay) {
                console.error('Container or overlay not found for Premium');
                return null;
            }

            // Supprimer l'ancien rectangle s'il existe
            const existing = document.getElementById(`premium_rect_${element}`);
            if (existing && existing.remove) {
                existing.remove();
            }

            const rect = document.createElement('div');
            rect.id = `premium_rect_${element}`;
            rect.className = 'element-rectangle';
            rect.style.position = 'absolute';
            rect.style.left = x + 'px';
            rect.style.top = y + 'px';
            rect.style.width = width + 'px';
            rect.style.height = height + 'px';
            rect.style.borderColor = color || '#1113a5';
            rect.style.borderWidth = '3px';
            rect.style.borderStyle = 'solid';
            rect.style.zIndex = '1000';
            rect.style.backgroundColor = 'rgba(255, 255, 255, 0.4)';
            rect.style.borderRadius = '4px';
            rect.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.2)';
            rect.dataset.element = element;

            // Debug: vérifier que le rectangle sera visible
            console.log('Création rectangle Premium:', {
                element, x, y, width, height, color,
                styles: {
                    left: x + 'px',
                    top: y + 'px',
                    width: width + 'px',
                    height: height + 'px',
                    borderColor: color
                }
            });

            const label = document.createElement('div');
            label.className = 'element-rectangle-label';
            label.textContent = getElementLabel(element);
            label.style.color = color;
            label.style.pointerEvents = 'none'; // S'assurer que le label n'interfère pas
            rect.appendChild(label);

            const resizeHandle = document.createElement('div');
            resizeHandle.className = 'element-resize-handle';
            resizeHandle.style.pointerEvents = 'all'; // S'assurer que le handle capture les événements
            rect.appendChild(resizeHandle);

            overlay.appendChild(rect);

            // Fonction pour synchroniser les dimensions de l'overlay avec l'image
            function syncOverlayDimensions() {
                const img = document.getElementById('premiumTicketImage');
                if (img && overlay) {
                    // Utiliser les dimensions réelles de l'image (offsetWidth/Height pour les dimensions rendues)
                    const imgWidth = img.offsetWidth || img.clientWidth || img.getBoundingClientRect().width;
                    const imgHeight = img.offsetHeight || img.clientHeight || img.getBoundingClientRect().height;

                    if (imgWidth > 0 && imgHeight > 0) {
                        overlay.style.width = imgWidth + 'px';
                        overlay.style.height = imgHeight + 'px';
                        console.log('Overlay Premium dimensionné:', {
                            imgWidth,
                            imgHeight,
                            overlayWidth: overlay.style.width,
                            overlayHeight: overlay.style.height,
                            imgSrc: img.src ? 'loaded' : 'no src'
                        });
                    }
                }
            }

            // Synchroniser les dimensions immédiatement et après le rendu
            syncOverlayDimensions();
            const img = document.getElementById('premiumTicketImage');
            if (img) {
                if (img.complete) {
                    // Image déjà chargée
                    setTimeout(syncOverlayDimensions, 0);
                } else {
                    // Attendre le chargement de l'image
                    img.addEventListener('load', syncOverlayDimensions, { once: true });
                }
                // Aussi après le prochain frame pour s'assurer
                requestAnimationFrame(syncOverlayDimensions);
            }

            // S'assurer que l'overlay accepte les événements de souris
            overlay.style.pointerEvents = 'all';
            overlay.style.position = 'absolute';
            overlay.style.top = '0';
            overlay.style.left = '0';
            overlay.style.cursor = 'default';
            overlay.style.zIndex = '1000'; // S'assurer que l'overlay est au-dessus de l'image

            // S'assurer que les rectangles capturent bien les événements
            rect.style.pointerEvents = 'all';
            rect.style.userSelect = 'none'; // Empêcher la sélection de texte pendant le drag
            rect.style.cursor = 'move'; // Curseur pour indiquer qu'on peut drag

            premiumElementRectangles[element] = rect;

            // Effet hover amélioré pour mieux voir les zones
            rect.addEventListener('mouseenter', function() {
                if (premiumDraggingElement !== rect) {
                    rect.style.boxShadow = '0 4px 12px rgba(17, 19, 165, 0.3)';
                }
            });

            rect.addEventListener('mouseleave', function() {
                if (premiumDraggingElement !== rect && !rect.classList.contains('selected')) {
                    rect.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.2)';
                }
            });

            // Vérifier que le rectangle est bien dans le DOM
            setTimeout(function() {
                const rectInDOM = document.getElementById(`premium_rect_${element}`);
                console.log('Rectangle Premium vérifié dans DOM:', {
                    element,
                    exists: !!rectInDOM,
                    visible: rectInDOM ? (rectInDOM.offsetWidth > 0 && rectInDOM.offsetHeight > 0) : false,
                    computedStyles: rectInDOM ? window.getComputedStyle(rectInDOM) : null
                });
            }, 100);

            // Gestion du drag avec améliorations
            rect.addEventListener('mousedown', function(e) {
                console.log('MouseDown on Premium rectangle:', {
                    element: element,
                    target: e.target,
                    targetTagName: e.target.tagName,
                    targetClass: e.target.className,
                    isResizeHandle: e.target === resizeHandle || e.target.closest('.element-resize-handle')
                });

                // Ignorer si c'est le resize handle
                if (e.target === resizeHandle || e.target.closest('.element-resize-handle')) {
                    console.log('Ignoring mousedown - resize handle');
                    return;
                }

                // Si c'est le label, on autorise quand même le drag (le label a pointer-events: none de toute façon)

                premiumDraggingElement = rect;
                const overlay = document.getElementById('premiumTicketOverlay');
                if (!overlay) {
                    console.error('Overlay not found for Premium drag');
                    return;
                }

                // Empêcher la sélection de texte et les autres comportements par défaut
                e.preventDefault();
                e.stopPropagation();

                const overlayBounds = overlay.getBoundingClientRect();
                const rectBounds = rect.getBoundingClientRect();

                // Calculer l'offset : position du clic dans le rectangle
                premiumDragOffset.x = e.clientX - rectBounds.left;
                premiumDragOffset.y = e.clientY - rectBounds.top;

                // Afficher la grille de guidage
                showPremiumSnapGrid();

                console.log('Drag started Premium:', {
                    element: element,
                    mouseX: e.clientX,
                    mouseY: e.clientY,
                    overlayLeft: overlayBounds.left,
                    overlayTop: overlayBounds.top,
                    rectLeft: rectBounds.left,
                    rectTop: rectBounds.top,
                    rectWidth: rectBounds.width,
                    rectHeight: rectBounds.height,
                    premiumDragOffset,
                    rect: rect
                });

                // Désélectionner les autres rectangles et sélectionner celui-ci
                document.querySelectorAll('#premiumTicketOverlay .element-rectangle').forEach(r => {
                    r.classList.remove('selected');
                    r.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.2)';
                });
                rect.classList.add('selected');
                rect.style.cursor = 'grabbing';
                rect.style.boxShadow = '0 4px 12px rgba(17, 19, 165, 0.5), 0 0 0 2px rgba(17, 19, 165, 0.3)';
                rect.style.zIndex = '1001'; // Mettre au premier plan pendant le drag

                // Empêcher la sélection de texte et le comportement par défaut
                document.body.style.userSelect = 'none';
                document.body.style.cursor = 'grabbing';

                return false;
            }, true); // Utiliser capture phase pour s'assurer que l'événement est capturé

            // Gestion du resize
            resizeHandle.addEventListener('mousedown', function(e) {
                premiumResizingElement = rect;
                e.stopPropagation();
                e.preventDefault();
            });

            return rect;
        }

        // Mettre à jour les champs cachés avec la position et taille Premium
        function updatePremiumElementData(element) {
            const rect = premiumElementRectangles[element];
            if (!rect) {
                console.warn(`Rectangle not found for element: ${element}`);
                return;
            }

            // Obtenir les dimensions de l'image NATURELLE (taille originale)
            const imgElement = document.getElementById('premiumTicketImage');
            if (!imgElement) {
                console.warn('premiumTicketImage not found');
                return;
            }

            const naturalWidth = imgElement.naturalWidth || imgElement.width || 800;
            const naturalHeight = imgElement.naturalHeight || imgElement.height || 400;

            // Dimensions affichées de l'image
            const imgRect = imgElement.getBoundingClientRect();
            const displayedWidth = imgRect.width || imgElement.offsetWidth || imgElement.clientWidth || naturalWidth;
            const displayedHeight = imgRect.height || imgElement.offsetHeight || imgElement.clientHeight || naturalHeight;

            // Calculer le ratio de redimensionnement
            const scaleX = naturalWidth / displayedWidth;
            const scaleY = naturalHeight / displayedHeight;

            // Utiliser directement les styles left/top qui sont relatifs à l'overlay (taille affichée)
            const displayedX = parseFloat(rect.style.left) || 0;
            const displayedY = parseFloat(rect.style.top) || 0;
            const displayedWidth_rect = parseFloat(rect.style.width) || rect.offsetWidth;
            const displayedHeight_rect = parseFloat(rect.style.height) || rect.offsetHeight;

            // Convertir en coordonnées pour la taille originale de l'image
            const x = Math.round(displayedX * scaleX);
            const y = Math.round(displayedY * scaleY);
            const width = Math.round(displayedWidth_rect * scaleX);
            const height = Math.round(displayedHeight_rect * scaleY);

            // Mettre à jour les champs hidden
            const xInput = document.getElementById(`premium_${element}_x`);
            const yInput = document.getElementById(`premium_${element}_y`);
            const widthInput = document.getElementById(`premium_${element}_width`);
            const heightInput = document.getElementById(`premium_${element}_height`);

            if (!xInput || !yInput) {
                console.error(`Input fields not found for element: ${element}`, {
                    xInput: `premium_${element}_x`,
                    yInput: `premium_${element}_y`
                });
                return;
            }

            xInput.value = x;
            yInput.value = y;
            if (widthInput) widthInput.value = width;
            if (heightInput) heightInput.value = height;

            console.log('Position sauvegardée pour', element, {
                displayedX, displayedY, displayedWidth_rect, displayedHeight_rect,
                naturalWidth, naturalHeight,
                scaleX, scaleY,
                savedX: x, savedY: y, savedWidth: width, savedHeight: height,
                xInputValue: xInput.value,
                yInputValue: yInput.value
            });

            updatePremiumElementStatus(element, true);
        }

        function updatePremiumElementStatus(element, isPositioned) {
            const statusBadge = document.getElementById(`premium_${element}_status`);
            if (statusBadge) {
                if (isPositioned) {
                    statusBadge.classList.add('active');
                    statusBadge.title = 'Position définie';
                } else {
                    statusBadge.classList.remove('active');
                    statusBadge.title = 'Position non définie';
                }
            }
        }

        function resetPremiumRectangles() {
            Object.keys(premiumElementRectangles).forEach(element => {
                const rect = premiumElementRectangles[element];
                if (rect) rect.remove();
                delete premiumElementRectangles[element];
                document.getElementById(`premium_${element}_x`).value = '';
                document.getElementById(`premium_${element}_y`).value = '';
                updatePremiumElementStatus(element, false);
            });
        }

        // Créer une grille de guidage pour le snap
        function showPremiumSnapGrid() {
            const overlay = document.getElementById('premiumTicketOverlay');
            if (!overlay) return;

            let grid = document.getElementById('premiumSnapGrid');
            if (!grid) {
                grid = document.createElement('div');
                grid.id = 'premiumSnapGrid';
                grid.style.position = 'absolute';
                grid.style.top = '0';
                grid.style.left = '0';
                grid.style.width = '100%';
                grid.style.height = '100%';
                grid.style.pointerEvents = 'none';
                grid.style.zIndex = '999';
                grid.style.opacity = '0.3';
                grid.style.backgroundImage = 'linear-gradient(to right, rgba(17, 19, 165, 0.1) 1px, transparent 1px), linear-gradient(to bottom, rgba(17, 19, 165, 0.1) 1px, transparent 1px)';
                grid.style.backgroundSize = '20px 20px';
                overlay.appendChild(grid);
            }
            grid.style.display = 'block';
        }

        function hidePremiumSnapGrid() {
            const grid = document.getElementById('premiumSnapGrid');
            if (grid) grid.style.display = 'none';
        }

        // Fonction de snap aux bordures et autres éléments
        function snapPremiumPosition(x, y, width, height) {
            const snapDistance = 10; // Distance de snap en pixels
            const overlay = document.getElementById('premiumTicketOverlay');
            if (!overlay) return { x, y };

            const overlayBounds = overlay.getBoundingClientRect();
            const snappedX = x;
            const snappedY = y;

            // Snap aux bordures du ticket
            if (Math.abs(x) < snapDistance) {
                return { x: 0, y: snappedY };
            }
            if (Math.abs(overlayBounds.width - (x + width)) < snapDistance) {
                return { x: overlayBounds.width - width, y: snappedY };
            }
            if (Math.abs(y) < snapDistance) {
                return { x: snappedX, y: 0 };
            }
            if (Math.abs(overlayBounds.height - (y + height)) < snapDistance) {
                return { x: snappedX, y: overlayBounds.height - height };
            }

            // Snap au centre horizontal
            const centerX = overlayBounds.width / 2;
            if (Math.abs(x + width / 2 - centerX) < snapDistance) {
                return { x: centerX - width / 2, y: snappedY };
            }

            // Snap au centre vertical
            const centerY = overlayBounds.height / 2;
            if (Math.abs(y + height / 2 - centerY) < snapDistance) {
                return { x: snappedX, y: centerY - height / 2 };
            }

            return { x: snappedX, y: snappedY };
        }

        // Gestion globale du drag pour Premium (séparée de Standard) avec améliorations
        document.addEventListener('mousemove', function(e) {
            // Ignorer si on drag Standard
            if (draggingElement || resizingElement) return;

            if (premiumDraggingElement) {
                const overlay = document.getElementById('premiumTicketOverlay');
                if (!overlay) {
                    console.error('Overlay not found during drag');
                    return;
                }

                // S'assurer que l'overlay a les bonnes dimensions avant de calculer les positions
                const img = document.getElementById('premiumTicketImage');
                if (img && img.offsetWidth > 0 && img.offsetHeight > 0) {
                    overlay.style.width = img.offsetWidth + 'px';
                    overlay.style.height = img.offsetHeight + 'px';
                }

                const overlayBounds = overlay.getBoundingClientRect();

                // Calculer la nouvelle position par rapport à l'overlay
                // On soustrait l'offset pour que le rectangle suive exactement la souris
                let x = e.clientX - overlayBounds.left - premiumDragOffset.x;
                let y = e.clientY - overlayBounds.top - premiumDragOffset.y;

                // S'assurer que les coordonnées sont valides
                if (isNaN(x) || isNaN(y)) {
                    console.error('Invalid coordinates:', { x, y, clientX: e.clientX, clientY: e.clientY, overlayLeft: overlayBounds.left, overlayTop: overlayBounds.top, offset: premiumDragOffset, overlayWidth: overlayBounds.width, overlayHeight: overlayBounds.height });
                    return;
                }

                // Limiter dans les bounds de l'overlay
                const maxX = overlayBounds.width - premiumDraggingElement.offsetWidth;
                const maxY = overlayBounds.height - premiumDraggingElement.offsetHeight;
                x = Math.max(0, Math.min(x, maxX));
                y = Math.max(0, Math.min(y, maxY));

                // Appliquer le snap
                const snapped = snapPremiumPosition(x, y, premiumDraggingElement.offsetWidth, premiumDraggingElement.offsetHeight);
                x = snapped.x;
                y = snapped.y;

                // Afficher la grille de guidage pendant le drag
                showPremiumSnapGrid();

                // Empêcher le comportement par défaut pendant le drag
                e.preventDefault();

                premiumDraggingElement.style.left = x + 'px';
                premiumDraggingElement.style.top = y + 'px';

                // Feedback visuel avec ligne de guidage
                updatePremiumDragGuides(x, y, premiumDraggingElement.offsetWidth, premiumDraggingElement.offsetHeight);

                const element = premiumDraggingElement.dataset.element;
                updatePremiumElementData(element);
            } else if (premiumResizingElement) {
                const overlay = document.getElementById('premiumTicketOverlay');
                if (!overlay) return;

                const overlayBounds = overlay.getBoundingClientRect();
                const rectBounds = premiumResizingElement.getBoundingClientRect();

                // Calculer la nouvelle taille
                let width = e.clientX - rectBounds.left;
                let height = e.clientY - rectBounds.top;

                width = Math.max(50, width);
                height = Math.max(30, height);

                // Limiter à la taille de l'overlay
                const currentX = parseFloat(premiumResizingElement.style.left) || 0;
                const currentY = parseFloat(premiumResizingElement.style.top) || 0;
                const maxWidth = overlayBounds.width - currentX;
                const maxHeight = overlayBounds.height - currentY;
                width = Math.min(width, maxWidth);
                height = Math.min(height, maxHeight);

                premiumResizingElement.style.width = width + 'px';
                premiumResizingElement.style.height = height + 'px';

                const element = premiumResizingElement.dataset.element;
                updatePremiumElementData(element);
            }
        });

        // Fonction pour mettre à jour les guides visuels pendant le drag
        function updatePremiumDragGuides(x, y, width, height) {
            const overlay = document.getElementById('premiumTicketOverlay');
            if (!overlay) return;

            // Supprimer les anciens guides
            let guides = document.getElementById('premiumDragGuides');
            if (!guides) {
                guides = document.createElement('div');
                guides.id = 'premiumDragGuides';
                guides.style.position = 'absolute';
                guides.style.top = '0';
                guides.style.left = '0';
                guides.style.width = '100%';
                guides.style.height = '100%';
                guides.style.pointerEvents = 'none';
                guides.style.zIndex = '998';
                overlay.appendChild(guides);
            }

            const overlayBounds = overlay.getBoundingClientRect();
            guides.innerHTML = '';

            // Ligne de guidage horizontale (centre)
            const centerX = overlayBounds.width / 2;
            const snapDistance = 10;
            if (Math.abs(x + width / 2 - centerX) < snapDistance) {
                const line = document.createElement('div');
                line.style.position = 'absolute';
                line.style.left = centerX + 'px';
                line.style.top = '0';
                line.style.width = '2px';
                line.style.height = '100%';
                line.style.backgroundColor = 'rgba(17, 19, 165, 0.5)';
                line.style.boxShadow = '0 0 4px rgba(17, 19, 165, 0.8)';
                guides.appendChild(line);
            }

            // Ligne de guidage verticale (centre)
            const centerY = overlayBounds.height / 2;
            if (Math.abs(y + height / 2 - centerY) < snapDistance) {
                const line = document.createElement('div');
                line.style.position = 'absolute';
                line.style.left = '0';
                line.style.top = centerY + 'px';
                line.style.width = '100%';
                line.style.height = '2px';
                line.style.backgroundColor = 'rgba(17, 19, 165, 0.5)';
                line.style.boxShadow = '0 0 4px rgba(17, 19, 165, 0.8)';
                guides.appendChild(line);
            }
        }

        function clearPremiumDragGuides() {
            const guides = document.getElementById('premiumDragGuides');
            if (guides) guides.innerHTML = '';
            hidePremiumSnapGrid();
        }

        document.addEventListener('mouseup', function(e) {
            // Restaurer le comportement normal
            document.body.style.userSelect = '';
            document.body.style.cursor = '';

            if (premiumDraggingElement) {
                premiumDraggingElement.style.cursor = 'move';
                premiumDraggingElement.style.zIndex = '1000'; // Remettre le z-index normal
                premiumDraggingElement.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.2)';
                const element = premiumDraggingElement.dataset.element;

                // Forcer la mise à jour des valeurs à la fin du drag
                console.log('🔄 Drag ended Premium, mise à jour finale pour:', element);
                updatePremiumElementData(element);

                // Vérifier que les valeurs ont bien été sauvegardées
                setTimeout(() => {
                    const xInput = document.getElementById(`premium_${element}_x`);
                    const yInput = document.getElementById(`premium_${element}_y`);
                    if (xInput && yInput) {
                        console.log('✅ Valeurs finales sauvegardées après drag:', {
                            element: element,
                            x: xInput.value,
                            y: yInput.value,
                            width: document.getElementById(`premium_${element}_width`)?.value,
                            height: document.getElementById(`premium_${element}_height`)?.value
                        });
                    }
                }, 100);

                clearPremiumDragGuides();
                console.log('Drag ended Premium:', element);
            }
            if (premiumResizingElement) {
                premiumResizingElement.style.zIndex = '1000';
                const element = premiumResizingElement.dataset.element;

                // Forcer la mise à jour des valeurs à la fin du resize
                console.log('🔄 Resize ended Premium, mise à jour finale pour:', element);
                updatePremiumElementData(element);

                // Vérifier que les valeurs ont bien été sauvegardées
                setTimeout(() => {
                    const xInput = document.getElementById(`premium_${element}_x`);
                    const yInput = document.getElementById(`premium_${element}_y`);
                    if (xInput && yInput) {
                        console.log('✅ Valeurs finales sauvegardées après resize:', {
                            element: element,
                            x: xInput.value,
                            y: yInput.value,
                            width: document.getElementById(`premium_${element}_width`)?.value,
                            height: document.getElementById(`premium_${element}_height`)?.value
                        });
                    }
                }, 100);

                clearPremiumDragGuides();
                console.log('Resize ended Premium:', element);
            }
            premiumDraggingElement = null;
            premiumResizingElement = null;
        });

        // Gestion des boutons "Définir position" pour Premium
        document.querySelectorAll('.element-position-btn-premium[data-element]').forEach(btn => {
            btn.addEventListener('click', function() {
                const element = this.dataset.element;
                const imgElement = document.getElementById('premiumTicketImage');

                if (!imgElement || !imgElement.src) {
                    alert('Veuillez d\'abord uploader une image de ticket.');
                    return;
                }

                // Vérifier si un rectangle existe déjà
                let rect = premiumElementRectangles[element];
                if (!rect) {
                    // Créer un nouveau rectangle avec position par défaut
                    const imgWidth = imgElement.offsetWidth || imgElement.width || 600;
                    const imgHeight = imgElement.offsetHeight || imgElement.height || 300;

                    let defaultX = 10;
                    let defaultY = 10;
                    let defaultWidth = 100;
                    let defaultHeight = 100;

                    // Positions par défaut selon l'élément
                    if (element === 'qr_code') {
                        defaultX = Math.max(10, imgWidth - 110);
                        defaultY = 10;
                        defaultWidth = 100;
                        defaultHeight = 100;
                    } else if (element === 'name') {
                        defaultX = 10;
                        defaultY = imgHeight > 100 ? 50 : 10;
                        defaultWidth = 150;
                        defaultHeight = 30;
                    } else if (element === 'function') {
                        defaultX = 10;
                        defaultY = imgHeight > 100 ? 90 : 40;
                        defaultWidth = 150;
                        defaultHeight = 30;
                    }

                    // Récupérer la couleur depuis le champ caché ou utiliser la valeur par défaut
                    const colorInput = document.getElementById(`premium_${element}_color`);
                    const color = colorInput && colorInput.value ? colorInput.value : (premiumElementColors[element] || '#1113a5');

                    rect = createPremiumRectangle(element, defaultX, defaultY, defaultWidth, defaultHeight, color);
                    updatePremiumElementData(element);
                } else {
                    // Si le rectangle existe, le mettre en surbrillance
                    document.querySelectorAll('#premiumTicketOverlay .element-rectangle').forEach(r => {
                        r.classList.remove('selected');
                        r.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.2)';
                    });
                    rect.classList.add('selected');
                    rect.style.boxShadow = '0 4px 12px rgba(17, 19, 165, 0.5), 0 0 0 2px rgba(17, 19, 165, 0.3)';
                }

                // Mettre le bouton en état actif
                document.querySelectorAll('.element-position-btn-premium').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Gestion des sélecteurs de couleur pour Premium
        document.querySelectorAll('.element-color-picker-premium[data-element]').forEach(picker => {
            picker.addEventListener('change', function() {
                const element = this.dataset.element;
                const color = this.value;
                premiumElementColors[element] = color;
                document.getElementById(`premium_${element}_color`).value = color;

                const rect = premiumElementRectangles[element];
                if (rect) {
                    rect.style.borderColor = color;
                    const label = rect.querySelector('.element-rectangle-label');
                    if (label) label.style.color = color;
                }
            });
        });

        // Bouton réinitialiser Premium
        const resetPremiumBtn = document.getElementById('resetPremiumPositions');
        if (resetPremiumBtn) {
            resetPremiumBtn.addEventListener('click', function() {
                if (confirm('Êtes-vous sûr de vouloir réinitialiser toutes les positions ?')) {
                    resetPremiumRectangles();

                    // Recréer le rectangle QR code
                    const imgElement = document.getElementById('premiumTicketImage');
                    if (imgElement && imgElement.src) {
                        const imgWidth = imgElement.offsetWidth || imgElement.width;
                        const defaultX = Math.max(10, imgWidth - 110);
                        createPremiumRectangle('qr_code', defaultX, 10, 100, 100, premiumElementColors['qr_code']);
                        updatePremiumElementData('qr_code');
                    }
                }
            });
        }

        // Initialiser les panneaux de configuration Premium au chargement
        const showNamePremiumCheck = document.getElementById('show_name_premium');
        const showFunctionPremiumCheck = document.getElementById('show_function_premium');
        const showSeatPremiumCheck = document.getElementById('show_seat_premium');

        // ID Ticket est toujours visible (obligatoire)
        let ticketIdPanel = document.querySelector(`.element-config-panel-premium[data-element="ticket_id"]`);
        if (ticketIdPanel) {
            ticketIdPanel.style.display = 'block';
        }
        const ticketIdColorInput = document.getElementById('premium_ticket_id_color');
        if (ticketIdColorInput && ticketIdColorInput.value) {
            premiumElementColors['ticket_id'] = ticketIdColorInput.value;
            const colorPicker = document.querySelector(`.element-color-picker-premium[data-element="ticket_id"]`);
            if (colorPicker) colorPicker.value = ticketIdColorInput.value;
        }

        if (showNamePremiumCheck) {
            const namePanel = document.querySelector(`.element-config-panel-premium[data-element="name"]`);
            if (namePanel) {
                namePanel.style.display = showNamePremiumCheck.checked ? 'block' : 'none';
            }
            // Charger la couleur depuis le champ caché si elle existe
            const colorInput = document.getElementById('premium_name_color');
            if (colorInput && colorInput.value) {
                premiumElementColors['name'] = colorInput.value;
                const colorPicker = document.querySelector(`.element-color-picker-premium[data-element="name"]`);
                if (colorPicker) colorPicker.value = colorInput.value;
            }
        }

        if (showFunctionPremiumCheck) {
            const functionPanel = document.querySelector(`.element-config-panel-premium[data-element="function"]`);
            if (functionPanel) {
                functionPanel.style.display = showFunctionPremiumCheck.checked ? 'block' : 'none';
            }
            // Charger la couleur depuis le champ caché si elle existe
            const colorInput = document.getElementById('premium_function_color');
            if (colorInput && colorInput.value) {
                premiumElementColors['function'] = colorInput.value;
                const colorPicker = document.querySelector(`.element-color-picker-premium[data-element="function"]`);
                if (colorPicker) colorPicker.value = colorInput.value;
            }
        }

        if (showSeatPremiumCheck) {
            const seatPanel = document.querySelector(`.element-config-panel-premium[data-element="seat"]`);
            if (seatPanel) {
                seatPanel.style.display = showSeatPremiumCheck.checked ? 'block' : 'none';
            }
            // Charger la couleur depuis le champ caché si elle existe
            const colorInput = document.getElementById('premium_seat_color');
            if (colorInput && colorInput.value) {
                premiumElementColors['seat'] = colorInput.value;
                const colorPicker = document.querySelector(`.element-color-picker-premium[data-element="seat"]`);
                if (colorPicker) colorPicker.value = colorInput.value;
            }
        }

        // Charger la couleur QR Code
        const qrColorInput = document.getElementById('premium_qr_code_color');
        if (qrColorInput && qrColorInput.value) {
            premiumElementColors['qr_code'] = qrColorInput.value;
            const colorPicker = document.querySelector(`.element-color-picker-premium[data-element="qr_code"]`);
            if (colorPicker) colorPicker.value = qrColorInput.value;
        }

        // Mettre à jour les panneaux de configuration selon les éléments sélectionnés pour Premium
        if (showNamePremiumCheck) {
            showNamePremiumCheck.addEventListener('change', function() {
                const configPanel = document.querySelector(`.element-config-panel-premium[data-element="name"]`);
                const imgElement = document.getElementById('premiumTicketImage');

                if (configPanel) {
                    configPanel.style.display = this.checked ? 'block' : 'none';
                }

                if (this.checked && imgElement && imgElement.src) {
                    const imgWidth = imgElement.offsetWidth || imgElement.width;
                    const imgHeight = imgElement.offsetHeight || imgElement.height;

                    const x = document.getElementById('premium_name_x').value || 10;
                    const y = document.getElementById('premium_name_y').value || (imgHeight > 100 ? 50 : 10);
                    const width = document.getElementById('premium_name_width').value || 150;
                    const height = document.getElementById('premium_name_height').value || 30;
                    const color = premiumElementColors['name'] || '#000000';

                    createPremiumRectangle('name', parseInt(x), parseInt(y), parseInt(width), parseInt(height), color);
                    updatePremiumElementData('name');
                } else if (!this.checked) {
                    const rect = premiumElementRectangles['name'];
                    if (rect) {
                        rect.remove();
                        delete premiumElementRectangles['name'];
                    }
                    document.getElementById('premium_name_x').value = '';
                    document.getElementById('premium_name_y').value = '';
                    updatePremiumElementStatus('name', false);
                }
            });
        }

        if (showFunctionPremiumCheck) {
            showFunctionPremiumCheck.addEventListener('change', function() {
                const configPanel = document.querySelector(`.element-config-panel-premium[data-element="function"]`);
                const imgElement = document.getElementById('premiumTicketImage');

                if (configPanel) {
                    configPanel.style.display = this.checked ? 'block' : 'none';
                }

                if (this.checked && imgElement && imgElement.src) {
                    const imgWidth = imgElement.offsetWidth || imgElement.width;
                    const imgHeight = imgElement.offsetHeight || imgElement.height;

                    const x = document.getElementById('premium_function_x').value || 10;
                    const y = document.getElementById('premium_function_y').value || (imgHeight > 100 ? 90 : 10);
                    const width = document.getElementById('premium_function_width').value || 150;
                    const height = document.getElementById('premium_function_height').value || 30;
                    const color = premiumElementColors['function'] || '#000000';

                    createPremiumRectangle('function', parseInt(x), parseInt(y), parseInt(width), parseInt(height), color);
                    updatePremiumElementData('function');
                } else if (!this.checked) {
                    const rect = premiumElementRectangles['function'];
                    if (rect) {
                        rect.remove();
                        delete premiumElementRectangles['function'];
                    }
                    document.getElementById('premium_function_x').value = '';
                    document.getElementById('premium_function_y').value = '';
                    updatePremiumElementStatus('function', false);
                }
            });
        }

        if (showSeatPremiumCheck) {
            showSeatPremiumCheck.addEventListener('change', function() {
                const configPanel = document.querySelector(`.element-config-panel-premium[data-element="seat"]`);
                const imgElement = document.getElementById('premiumTicketImage');

                if (configPanel) {
                    configPanel.style.display = this.checked ? 'block' : 'none';
                }

                if (this.checked && imgElement && imgElement.src) {
                    const imgWidth = imgElement.offsetWidth || imgElement.width;
                    const imgHeight = imgElement.offsetHeight || imgElement.height;

                    const x = document.getElementById('premium_seat_x').value || 10;
                    const y = document.getElementById('premium_seat_y').value || (imgHeight > 100 ? 130 : 10);
                    const width = document.getElementById('premium_seat_width').value || 100;
                    const height = document.getElementById('premium_seat_height').value || 30;
                    const color = premiumElementColors['seat'] || '#000000';

                    createPremiumRectangle('seat', parseInt(x), parseInt(y), parseInt(width), parseInt(height), color);
                    updatePremiumElementData('seat');
                } else if (!this.checked) {
                    const rect = premiumElementRectangles['seat'];
                    if (rect) {
                        rect.remove();
                        delete premiumElementRectangles['seat'];
                    }
                    document.getElementById('premium_seat_x').value = '';
                    document.getElementById('premium_seat_y').value = '';
                    updatePremiumElementStatus('seat', false);
                }
            });
        }

        // S'assurer que l'ID ticket est toujours positionné (obligatoire)
        if (!ticketIdPanel) {
            ticketIdPanel = document.querySelector(`.element-config-panel-premium[data-element="ticket_id"]`);
        }
        if (ticketIdPanel) {
            const imgElement = document.getElementById('premiumTicketImage');
            const ticketIdXInput = document.getElementById('premium_ticket_id_x');
            const ticketIdYInput = document.getElementById('premium_ticket_id_y');

            // Fonction pour initialiser ticket_id
            function initializeTicketId() {
                if (imgElement && imgElement.src) {
                    const imgWidth = imgElement.offsetWidth || imgElement.width;
                    const imgHeight = imgElement.offsetHeight || imgElement.height;

                    // Vérifier si les valeurs sont vides
                    const currentX = ticketIdXInput ? ticketIdXInput.value : '';
                    const currentY = ticketIdYInput ? ticketIdYInput.value : '';

                    if ((!currentX || currentX === '') && (!currentY || currentY === '') && imgWidth > 0 && imgHeight > 0) {
                        console.log('Initialisation automatique de ticket_id');

                        // Calculer les coordonnées pour l'image affichée
                        const displayedX = Math.max(10, imgWidth - 110);
                        const displayedY = 10;
                        const displayedWidth = 100;
                        const displayedHeight = 30;

                        // Calculer les coordonnées pour l'image naturelle
                        const naturalWidth = imgElement.naturalWidth || 800;
                        const naturalHeight = imgElement.naturalHeight || 400;
                        const scaleX = naturalWidth / imgWidth;
                        const scaleY = naturalHeight / imgHeight;

                        const naturalX = Math.round(displayedX * scaleX);
                        const naturalY = Math.round(displayedY * scaleY);
                        const naturalWidth_rect = Math.round(displayedWidth * scaleX);
                        const naturalHeight_rect = Math.round(displayedHeight * scaleY);

                        // Mettre à jour les champs hidden directement
                        if (ticketIdXInput) ticketIdXInput.value = naturalX;
                        if (ticketIdYInput) ticketIdYInput.value = naturalY;
                        const ticketIdWidthInput = document.getElementById('premium_ticket_id_width');
                        const ticketIdHeightInput = document.getElementById('premium_ticket_id_height');
                        if (ticketIdWidthInput) ticketIdWidthInput.value = naturalWidth_rect;
                        if (ticketIdHeightInput) ticketIdHeightInput.value = naturalHeight_rect;

                        // Créer le rectangle visuel
                        const color = premiumElementColors['ticket_id'] || '#000000';
                        createPremiumRectangle('ticket_id', displayedX, displayedY, displayedWidth, displayedHeight, color);

                        console.log('ticket_id initialisé:', {
                            displayed: { x: displayedX, y: displayedY },
                            natural: { x: naturalX, y: naturalY }
                        });
                    }
                }
            }

            // Essayer d'initialiser immédiatement
            if (imgElement && imgElement.complete && imgElement.naturalWidth > 0) {
                initializeTicketId();
            } else {
                // Attendre que l'image soit chargée
                if (imgElement) {
                    imgElement.addEventListener('load', function() {
                        setTimeout(initializeTicketId, 100);
                    });
                }
                // Aussi essayer après un délai
                setTimeout(initializeTicketId, 1000);
            }
        }

    });
</script>
@endpush
