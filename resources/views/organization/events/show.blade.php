@extends('organization.layouts.app')

@section('title', 'Détails de l\'événement')

@push('styles')
<style>
    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 1rem;
        padding: 3rem 2rem;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.1"><circle cx="30" cy="30" r="2"/></g></svg>');
        opacity: 0.3;
    }

    .info-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }

    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .info-card.primary {
        border-left-color: #667eea;
    }

    .info-card.success {
        border-left-color: #10b981;
    }

    .info-card.warning {
        border-left-color: #f59e0b;
    }

    .info-card.info {
        border-left-color: #3b82f6;
    }

    .stat-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        text-align: center;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    }

    .stat-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 1.5rem;
    }

    .stat-icon.blue {
        background: #dbeafe;
        color: #1e40af;
    }

    .stat-icon.green {
        background: #dcfce7;
        color: #166534;
    }

    .stat-icon.orange {
        background: #fed7aa;
        color: #9a3412;
    }

    .stat-icon.purple {
        background: #e9d5ff;
        color: #6b21a8;
    }

    .progress-bar {
        width: 100%;
        height: 0.5rem;
        background: #e5e7eb;
        border-radius: 9999px;
        overflow: hidden;
        margin-top: 0.5rem;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        transition: width 0.5s ease;
    }

    .detail-item {
        display: flex;
        align-items: start;
        padding: 1rem;
        border-bottom: 1px solid #f3f4f6;
        transition: background 0.2s;
    }

    .detail-item:last-child {
        border-bottom: none;
    }

    .detail-item:hover {
        background: #f9fafb;
    }

    .detail-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        flex-shrink: 0;
    }

    .detail-icon.blue {
        background: #dbeafe;
        color: #1e40af;
    }

    .detail-icon.purple {
        background: #e9d5ff;
        color: #6b21a8;
    }

    .detail-icon.green {
        background: #dcfce7;
        color: #166534;
    }

    .detail-icon.orange {
        background: #fed7aa;
        color: #9a3412;
    }

    .detail-icon.red {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-modern {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .badge-modern.published {
        background: #dcfce7;
        color: #166534;
    }

    .badge-modern.draft {
        background: #f3f4f6;
        color: #374151;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-in-up {
        animation: fadeInUp 0.6s ease-out;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Hero Section -->
    <div class="hero-section fade-in-up">
        <div class="relative z-10">
            <div class="flex justify-between items-start mb-4">
                <div class="flex-1">
                    <h1 class="text-4xl font-bold mb-2">{{ $event->event_title }}</h1>
                    <p class="text-white/90 text-lg">
                        @if($event->event_description)
                            {{ \Illuminate\Support\Str::limit($event->event_description, 100) }}
                        @else
                            Détails de l'événement
                        @endif
                    </p>
                </div>
                <div class="flex gap-2 ml-4">
                    @if(isset($canEdit) && $canEdit)
                        <a href="{{ route('org.collaborateurs.assign-commission', ['org_slug' => $orgSlug, 'eventId' => $event->id]) }}"
                           class="bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-lg hover:bg-white/30 transition border border-white/30">
                            <i class="fas fa-hand-holding-usd mr-2"></i>Attribuer commissions
                        </a>
                        <a href="{{ route('org.events.edit', ['org_slug' => $orgSlug, 'event' => $event->id]) }}"
                           class="bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-lg hover:bg-white/30 transition border border-white/30">
                            <i class="fas fa-edit mr-2"></i>Modifier
                        </a>
                    @endif
                    <a href="{{ route('org.events.index', ['org_slug' => $orgSlug]) }}"
                       class="bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-lg hover:bg-white/30 transition border border-white/30">
                        <i class="fas fa-arrow-left mr-2"></i>Retour
                    </a>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 mt-4">
                @if($event->is_published)
                    <span class="badge-modern published">
                        <i class="fas fa-check-circle mr-2"></i>Publié
                    </span>
                @else
                    <span class="badge-modern draft">
                        <i class="fas fa-file-alt mr-2"></i>Brouillon
                    </span>
                @endif

                @php
                    $eventDate = $event->event_date ? \Carbon\Carbon::parse($event->event_date) : null;
                    $isPast = $eventDate && $eventDate->isPast();
                    $isUpcoming = $eventDate && $eventDate->isFuture();
                @endphp

                @if($isUpcoming)
                    <span class="badge-modern" style="background: #dbeafe; color: #1e40af;">
                        <i class="fas fa-calendar-alt mr-2"></i>À venir
                    </span>
                @elseif($isPast)
                    <span class="badge-modern" style="background: #fee2e2; color: #991b1b;">
                        <i class="fas fa-history mr-2"></i>Passé
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card fade-in-up" style="animation-delay: 0.1s;">
            <div class="stat-icon blue">
                <i class="fas fa-users"></i>
            </div>
            <div class="text-3xl font-bold text-gray-900 mb-1">{{ $registrationsCount ?? 0 }}</div>
            <div class="text-sm text-gray-500">Inscriptions</div>
            @if($event->max_participants)
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ min(100, ($registrationsCount ?? 0) / $event->max_participants * 100) }}%"></div>
                </div>
                <div class="text-xs text-gray-400 mt-1">{{ $registrationsCount ?? 0 }} / {{ $event->max_participants }} participants</div>
            @endif
        </div>

        <div class="stat-card fade-in-up" style="animation-delay: 0.2s;">
            <div class="stat-icon green">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="text-3xl font-bold text-gray-900 mb-1">{{ $event->current_participants ?? 0 }}</div>
            <div class="text-sm text-gray-500">Participants actuels</div>
        </div>

        <div class="stat-card fade-in-up" style="animation-delay: 0.3s;">
            <div class="stat-icon orange">
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="text-3xl font-bold text-gray-900 mb-1">{{ $event->max_participants ?? '∞' }}</div>
            <div class="text-sm text-gray-500">Capacité maximale</div>
        </div>

        @if($event->requires_payment && $event->event_price)
        <div class="stat-card fade-in-up" style="animation-delay: 0.4s;">
            <div class="stat-icon purple">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="text-3xl font-bold text-gray-900 mb-1">
                {{ number_format($event->event_price, 0, ',', ' ') }}
            </div>
            <div class="text-sm text-gray-500">{{ $event->currency ?? 'XOF' }}</div>
        </div>
        @else
        <div class="stat-card fade-in-up" style="animation-delay: 0.4s;">
            <div class="stat-icon green">
                <i class="fas fa-gift"></i>
            </div>
            <div class="text-3xl font-bold text-gray-900 mb-1">Gratuit</div>
            <div class="text-sm text-gray-500">Événement gratuit</div>
        </div>
        @endif
    </div>

    <!-- Lien d'inscription -->
    <div class="info-card success fade-in-up">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900 mb-2 flex items-center">
                    <i class="fas fa-link mr-2 text-green-600"></i>
                    Lien d'inscription à l'événement
                </h3>
                <p class="text-sm text-gray-600 mb-3">
                    Partagez ce lien avec vos participants pour qu'ils puissent s'inscrire à votre événement.
                </p>
                <div class="flex items-center gap-2 flex-wrap">
                    <input type="text"
                           id="registrationLink"
                           readonly
                           value="{{ url('/' . $orgSlug . '/' . ($event->event_slug ?? 'event-' . $event->id)) }}"
                           class="flex-1 min-w-[300px] px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <button onclick="copyRegistrationLink(event)"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                        <i class="fas fa-copy"></i>
                        <span>Copier</span>
                    </button>
                    <a href="{{ url('/' . $orgSlug . '/' . ($event->event_slug ?? 'event-' . $event->id)) }}"
                       target="_blank"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                        <i class="fas fa-external-link-alt"></i>
                        <span>Ouvrir</span>
                    </a>
                </div>
                <div id="copySuccessMessage" class="hidden mt-2 text-sm text-green-600 flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    Lien copié avec succès !
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations générales -->
        <div class="lg:col-span-2">
            <div class="info-card primary fade-in-up">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-info-circle mr-3 text-primary-custom"></i>
                    Informations générales
                </h2>

                <div class="space-y-0">
                    @if($event->event_description)
                    <div class="detail-item">
                        <div class="detail-icon blue">
                            <i class="fas fa-align-left"></i>
                        </div>
                        <div class="flex-1">
                            <dt class="text-sm font-semibold text-gray-500 mb-1">Description</dt>
                            <dd class="text-gray-900 leading-relaxed">{{ $event->event_description }}</dd>
                        </div>
                    </div>
                    @endif

                    @if($event->event_date)
                    <div class="detail-item">
                        <div class="detail-icon purple">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="flex-1">
                            <dt class="text-sm font-semibold text-gray-500 mb-1">Date et heure</dt>
                            <dd class="text-gray-900">
                                <div class="font-medium">{{ \Carbon\Carbon::parse($event->event_date)->format('d F Y') }}</div>
                                @if($event->event_start_time || $event->event_end_time)
                                <div class="text-sm text-gray-600 mt-1">
                                    @if($event->event_start_time)
                                        <i class="far fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($event->event_start_time)->format('H:i') }}
                                    @endif
                                    @if($event->event_end_time)
                                        - {{ \Carbon\Carbon::parse($event->event_end_time)->format('H:i') }}
                                    @endif
                                </div>
                                @endif
                            </dd>
                        </div>
                    </div>
                    @endif

                    @if($event->event_location)
                    <div class="detail-item">
                        <div class="detail-icon green">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="flex-1">
                            <dt class="text-sm font-semibold text-gray-500 mb-1">Lieu</dt>
                            <dd class="text-gray-900 font-medium">{{ $event->event_location }}</dd>
                            @if($event->event_address)
                            <dd class="text-sm text-gray-600 mt-1">{{ $event->event_address }}</dd>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($event->event_type_id)
                    @php
                        $eventType = \Illuminate\Support\Facades\DB::connection('tenant')
                            ->table('event_types')
                            ->where('id', $event->event_type_id)
                            ->first();
                    @endphp
                    @if($eventType)
                    <div class="detail-item">
                        <div class="detail-icon orange">
                            <i class="fas fa-tag"></i>
                        </div>
                        <div class="flex-1">
                            <dt class="text-sm font-semibold text-gray-500 mb-1">Type d'événement</dt>
                            <dd class="text-gray-900 font-medium">{{ $eventType->type_name }}</dd>
                        </div>
                    </div>
                    @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Dates d'inscription -->
            @if($event->registration_start_date || $event->registration_end_date)
            <div class="info-card success fade-in-up">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-calendar-check mr-2 text-green-600"></i>
                    Inscriptions
                </h3>
                <div class="space-y-3">
                    @if($event->registration_start_date)
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Début</div>
                        <div class="text-sm font-medium text-gray-900">
                            {{ \Carbon\Carbon::parse($event->registration_start_date)->format('d/m/Y H:i') }}
                        </div>
                    </div>
                    @endif
                    @if($event->registration_end_date)
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Fin</div>
                        <div class="text-sm font-medium text-gray-900">
                            {{ \Carbon\Carbon::parse($event->registration_end_date)->format('d/m/Y H:i') }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Actions rapides -->
            <div class="info-card info fade-in-up">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-bolt mr-2 text-blue-600"></i>
                    Actions rapides
                </h3>
                <div class="space-y-2">
                    <a href="{{ route('org.events.edit', ['org_slug' => $orgSlug, 'event' => $event->id]) }}"
                       class="block w-full bg-primary-custom text-white px-4 py-2 rounded-lg hover:opacity-90 transition text-center">
                        <i class="fas fa-edit mr-2"></i>Modifier l'événement
                    </a>
                    <a href="{{ route('org.events.index', ['org_slug' => $orgSlug]) }}"
                       class="block w-full bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition text-center">
                        <i class="fas fa-arrow-left mr-2"></i>Retour à la liste
                    </a>
                </div>
            </div>

            <!-- Informations additionnelles -->
            <div class="info-card warning fade-in-up">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-cog mr-2 text-orange-600"></i>
                    Configuration
                </h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Paiement requis</span>
                        <span class="font-medium text-gray-900">
                            {{ $event->requires_payment ? 'Oui' : 'Non' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Inscriptions ouvertes</span>
                        <span class="font-medium text-gray-900">
                            {{ $event->registration_open ? 'Oui' : 'Non' }}
                        </span>
                    </div>
                    @if($event->pack_type)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Pack</span>
                        <span class="font-medium text-gray-900 capitalize">{{ $event->pack_type }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Spécimen du ticket -->
            @if($ticketCustomization && isset($ticketCustomization['template_path']))
            <div class="info-card primary fade-in-up">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-ticket-alt mr-2 text-purple-600"></i>
                    Spécimen du ticket
                </h3>
                <div class="space-y-3">
                    @php
                        $templatePath = $ticketCustomization['template_path'];
                        $fullPath = url('public/' . $templatePath);
                        $packType = $ticketCustomization['pack_type'] ?? 'standard';
                    @endphp
                    <div class="border-2 border-gray-200 rounded-lg p-3 bg-gray-50" style="position: relative;">
                        <div id="ticketPreviewContainer" style="position: relative; display: inline-block; margin: 0 auto;">
                            <img id="ticketPreviewImage"
                                 src="{{ $fullPath }}"
                                 alt="Spécimen du ticket"
                                 class="max-w-full h-auto rounded-lg shadow-sm"
                                 style="max-height: 400px; width: auto; display: block;">
                            <div id="ticketPreviewOverlay" style="position: absolute; top: 0; left: 0; pointer-events: none; z-index: 10;"></div>
                        </div>
                    </div>
                    <div class="text-xs text-gray-500 text-center">
                        Pack: <span class="font-medium capitalize">{{ $packType }}</span>
                    </div>

                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
@if($ticketCustomization && isset($ticketCustomization['template_path']))
<script>
document.addEventListener('DOMContentLoaded', function() {
    const imgElement = document.getElementById('ticketPreviewImage');
    const overlay = document.getElementById('ticketPreviewOverlay');
    const packType = '{{ $packType }}';

    if (!imgElement || !overlay) return;

    function createElementRectangle(element, x, y, width, height, color, label) {
        const rect = document.createElement('div');
        rect.className = 'element-rectangle-preview';
        rect.style.position = 'absolute';
        rect.style.left = x + 'px';
        rect.style.top = y + 'px';
        rect.style.width = width + 'px';
        rect.style.height = height + 'px';
        // Bordure plus épaisse et plus visible
        rect.style.border = '3px solid ' + color;
        rect.style.backgroundColor = 'rgba(255, 255, 255, 0.2)';
        rect.style.pointerEvents = 'none';
        rect.style.boxSizing = 'border-box';
        rect.style.borderRadius = '4px';
        rect.style.boxShadow = '0 0 0 1px rgba(0, 0, 0, 0.1), 0 2px 8px rgba(0, 0, 0, 0.3)';
        rect.style.zIndex = '100';

        const labelDiv = document.createElement('div');
        labelDiv.className = 'element-label-preview';
        labelDiv.style.position = 'absolute';
        labelDiv.style.top = '-25px';
        labelDiv.style.left = '0';
        labelDiv.style.padding = '4px 8px';
        labelDiv.style.backgroundColor = color;
        labelDiv.style.color = '#fff';
        labelDiv.style.fontSize = '11px';
        labelDiv.style.fontWeight = 'bold';
        labelDiv.style.borderRadius = '4px';
        labelDiv.style.whiteSpace = 'nowrap';
        labelDiv.style.boxShadow = '0 2px 4px rgba(0, 0, 0, 0.3)';
        labelDiv.style.zIndex = '101';
        labelDiv.textContent = label;
        rect.appendChild(labelDiv);

        overlay.appendChild(rect);

        console.log('Rectangle créé:', {
            element, x, y, width, height, color, label,
            overlayWidth: overlay.style.width,
            overlayHeight: overlay.style.height
        });
    }

    function showTicketElements() {
        if (!imgElement.complete || imgElement.naturalWidth === 0) {
            imgElement.onload = showTicketElements;
            return;
        }

        // Attendre un peu pour que l'image soit complètement rendue
        setTimeout(function() {
            // Obtenir les dimensions réelles de l'image AFFICHÉE
            const imgRect = imgElement.getBoundingClientRect();
            const containerRect = document.getElementById('ticketPreviewContainer').getBoundingClientRect();

            // Utiliser les dimensions réelles de l'image affichée
            const displayedWidth = imgRect.width || imgElement.offsetWidth || imgElement.clientWidth || imgElement.width;
            const displayedHeight = imgRect.height || imgElement.offsetHeight || imgElement.clientHeight || imgElement.height;

            // Obtenir les dimensions NATURELLES de l'image (taille originale)
            const naturalWidth = imgElement.naturalWidth || imgElement.width || displayedWidth;
            const naturalHeight = imgElement.naturalHeight || imgElement.height || displayedHeight;

            // Calculer le ratio de redimensionnement
            const scaleX = displayedWidth / naturalWidth;
            const scaleY = displayedHeight / naturalHeight;

            console.log('Dimensions image pour overlay:', {
                displayedWidth,
                displayedHeight,
                naturalWidth,
                naturalHeight,
                scaleX,
                scaleY,
                imgRect: { width: imgRect.width, height: imgRect.height },
                offsetWidth: imgElement.offsetWidth,
                offsetHeight: imgElement.offsetHeight
            });

            // Ajuster la taille de l'overlay pour correspondre exactement à l'image affichée
            if (displayedWidth > 0 && displayedHeight > 0) {
                overlay.style.width = displayedWidth + 'px';
                overlay.style.height = displayedHeight + 'px';
                overlay.style.position = 'absolute';
                overlay.style.top = '0';
                overlay.style.left = '0';
                overlay.style.pointerEvents = 'none';
                overlay.style.zIndex = '10';

                console.log('Overlay dimensionné:', {
                    overlayWidth: overlay.style.width,
                    overlayHeight: overlay.style.height
                });
            }

            // Seul le pack Premium a une personnalisation manuelle des éléments
            if (packType === 'premium') {
                // QR Code (toujours présent)
                @if(isset($ticketCustomization['qr_code']) && isset($ticketCustomization['qr_code']['x']))
                    // Détecter si les positions sont pour la taille originale ou affichée
                    // Si les positions sont très petites par rapport à l'image originale, elles sont probablement pour la taille affichée
                    const originalQrX = {{ $ticketCustomization['qr_code']['x'] ?? 0 }};
                    const originalQrY = {{ $ticketCustomization['qr_code']['y'] ?? 0 }};
                    const originalQrWidth = {{ $ticketCustomization['qr_code']['width'] ?? 100 }};
                    const originalQrHeight = {{ $ticketCustomization['qr_code']['height'] ?? 100 }};

                    // Les positions dans la base de données sont pour la taille ORIGINALE de l'image
                    // On les convertit pour la taille AFFICHÉE actuelle
                    const qrX = Math.round(originalQrX * scaleX);
                    const qrY = Math.round(originalQrY * scaleY);
                    const qrWidth = Math.round(originalQrWidth * scaleX);
                    const qrHeight = Math.round(originalQrHeight * scaleY);

                    const qrColor = '{{ $ticketCustomization['qr_code']['color'] ?? '#000000' }}';

                    console.log('QR Code position:', {
                        original: { x: originalQrX, y: originalQrY, width: originalQrWidth, height: originalQrHeight },
                        displayed: { x: qrX, y: qrY, width: qrWidth, height: qrHeight },
                        scale: { x: scaleX, y: scaleY }
                    });

                    createElementRectangle('qr_code', qrX, qrY, qrWidth, qrHeight, qrColor, 'QR Code');
                @endif

                // Name
                @if(isset($ticketCustomization['show_name']) && $ticketCustomization['show_name'] && isset($ticketCustomization['name']['x']))
                    const originalNameX = {{ $ticketCustomization['name']['x'] ?? 0 }};
                    const originalNameY = {{ $ticketCustomization['name']['y'] ?? 0 }};
                    const originalNameWidth = {{ $ticketCustomization['name']['width'] ?? 150 }};
                    const originalNameHeight = {{ $ticketCustomization['name']['height'] ?? 30 }};

                    const nameX = Math.round(originalNameX * scaleX);
                    const nameY = Math.round(originalNameY * scaleY);
                    const nameWidth = Math.round(originalNameWidth * scaleX);
                    const nameHeight = Math.round(originalNameHeight * scaleY);
                    const nameColor = '{{ $ticketCustomization['name']['color'] ?? '#000000' }}';

                    console.log('Name position:', {
                        original: { x: originalNameX, y: originalNameY, width: originalNameWidth, height: originalNameHeight },
                        displayed: { x: nameX, y: nameY, width: nameWidth, height: nameHeight },
                        scale: { x: scaleX, y: scaleY }
                    });

                    createElementRectangle('name', nameX, nameY, nameWidth, nameHeight, nameColor, 'Nom');
                @endif

                // Function
                @if(isset($ticketCustomization['show_function']) && $ticketCustomization['show_function'] && isset($ticketCustomization['function']['x']))
                    const originalFuncX = {{ $ticketCustomization['function']['x'] ?? 0 }};
                    const originalFuncY = {{ $ticketCustomization['function']['y'] ?? 0 }};
                    const originalFuncWidth = {{ $ticketCustomization['function']['width'] ?? 150 }};
                    const originalFuncHeight = {{ $ticketCustomization['function']['height'] ?? 30 }};

                    const funcX = Math.round(originalFuncX * scaleX);
                    const funcY = Math.round(originalFuncY * scaleY);
                    const funcWidth = Math.round(originalFuncWidth * scaleX);
                    const funcHeight = Math.round(originalFuncHeight * scaleY);
                    const funcColor = '{{ $ticketCustomization['function']['color'] ?? '#000000' }}';

                    console.log('Function position:', {
                        original: { x: originalFuncX, y: originalFuncY, width: originalFuncWidth, height: originalFuncHeight },
                        displayed: { x: funcX, y: funcY, width: funcWidth, height: funcHeight },
                        scale: { x: scaleX, y: scaleY }
                    });

                    createElementRectangle('function', funcX, funcY, funcWidth, funcHeight, funcColor, 'Fonction');
                @endif
            }
        }, 300);
    }

    showTicketElements();
});
</script>
@endif

<script>
    // Fonction pour copier le lien d'inscription
    function copyRegistrationLink(e) {
        const linkInput = document.getElementById('registrationLink');
        const successMessage = document.getElementById('copySuccessMessage');

        if (!linkInput) return;

        // Sélectionner le texte
        linkInput.select();
        linkInput.setSelectionRange(0, 99999); // Pour mobile

        try {
            // Copier dans le presse-papiers
            document.execCommand('copy');

            // Afficher le message de succès
            if (successMessage) {
                successMessage.classList.remove('hidden');

                // Masquer le message après 3 secondes
                setTimeout(function() {
                    successMessage.classList.add('hidden');
                }, 3000);
            }

            // Feedback visuel sur le bouton
            const copyButton = e ? e.target.closest('button') : document.querySelector('button[onclick*="copyRegistrationLink"]');
            if (copyButton) {
                const originalText = copyButton.innerHTML;
                copyButton.innerHTML = '<i class="fas fa-check"></i> <span>Copié !</span>';
                copyButton.classList.add('bg-green-700');

                setTimeout(function() {
                    copyButton.innerHTML = originalText;
                    copyButton.classList.remove('bg-green-700');
                }, 2000);
            }
        } catch (err) {
            console.error('Erreur lors de la copie:', err);
            alert('Impossible de copier le lien. Veuillez le sélectionner manuellement.');
        }
    }

    // Utiliser l'API Clipboard moderne si disponible
    document.addEventListener('DOMContentLoaded', function() {
        const copyButton = document.querySelector('button[onclick*="copyRegistrationLink"]');
        if (copyButton && navigator.clipboard) {
            // Ajouter un gestionnaire d'événement pour utiliser l'API moderne
            copyButton.addEventListener('click', async function(e) {
                e.preventDefault();
                const linkInput = document.getElementById('registrationLink');
                const successMessage = document.getElementById('copySuccessMessage');

                if (!linkInput) return;

                try {
                    await navigator.clipboard.writeText(linkInput.value);

                    if (successMessage) {
                        successMessage.classList.remove('hidden');
                        setTimeout(function() {
                            successMessage.classList.add('hidden');
                        }, 3000);
                    }

                    const originalText = copyButton.innerHTML;
                    copyButton.innerHTML = '<i class="fas fa-check"></i> <span>Copié !</span>';
                    copyButton.classList.add('bg-green-700');

                    setTimeout(function() {
                        copyButton.innerHTML = originalText;
                        copyButton.classList.remove('bg-green-700');
                    }, 2000);
                } catch (err) {
                    console.error('Erreur lors de la copie:', err);
                    // Fallback vers la méthode ancienne
                    copyRegistrationLink(e);
                }
            });
        }
    });
</script>
@endpush
@endsection
