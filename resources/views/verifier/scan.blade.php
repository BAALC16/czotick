<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultat de vérification - {{ $event->event_title ?? 'Événement' }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
    :root {
        --primary-color: {{ $event->primary_color ?? '#025225' }};
        --primary-light: {{ $event->primary_color ?? '#025225' }}cc;
        --primary-dark: #013918;
        --accent-color: #2fac61;
        --secondary-color: {{ $event->secondary_color ?? '#0F8A5F' }};
        --tertiary-color: #1FB379;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --error-color: #ef4444;
        --already-used-color: #8b5cf6;
        --info-color: #3b82f6;
    }
    
    body {
        background: var(--primary-color);
        min-height: 100vh;
        font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .glass-effect {
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 32px 0 rgba(0, 20, 10, 0.37);
    }
    
    .btn-primary {
        background: var(--primary-color);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.25);
    }
    
    .btn-secondary {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .btn-secondary:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-1px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    }
    
    .btn-danger {
        background: #ef4444;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .btn-danger:hover {
        background: #dc2626;
        transform: translateY(-1px);
        box-shadow: 0 8px 16px rgba(239, 68, 68, 0.4);
    }
    
    .result-icon {
        font-size: 5rem;
        animation: resultPulse 1.2s ease-out;
        filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.3));
    }
    
    @keyframes resultPulse {
        0% {
            transform: scale(0.7) rotate(-10deg);
            opacity: 0;
        }
        50% {
            transform: scale(1.1) rotate(2deg);
            opacity: 0.9;
        }
        100% {
            transform: scale(1) rotate(0deg);
            opacity: 1;
        }
    }
    
    .success-bg {
        background: rgba(16, 185, 129, 0.25);
        border: 2px solid var(--success-color);
        box-shadow: 0 0 30px rgba(16, 185, 129, 0.4);
    }
    
    .warning-bg {
        background: rgba(245, 158, 11, 0.25);
        border: 2px solid var(--warning-color);
        box-shadow: 0 0 30px rgba(245, 158, 11, 0.4);
    }
    
    .error-bg {
        background: rgba(239, 68, 68, 0.25);
        border: 2px solid var(--error-color);
        box-shadow: 0 0 30px rgba(239, 68, 68, 0.4);
    }
    
    .already-used-bg {
        background: rgba(139, 92, 246, 0.25);
        border: 2px solid var(--already-used-color);
        box-shadow: 0 0 30px rgba(139, 92, 246, 0.4);
    }
    
    .zone-badge {
        background: var(--primary-color);
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.875rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        animation: zoneBadgeGlow 2s ease-in-out infinite alternate;
    }
    
    @keyframes zoneBadgeGlow {
        0% { box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2); }
        100% { box-shadow: 0 4px 20px var(--primary-light); }
    }
    
    .info-item {
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
        border-radius: 8px;
        margin-bottom: 8px;
    }
    
    .info-item:hover {
        background-color: rgba(255, 255, 255, 0.1);
        transform: translateX(5px);
    }
    
    .slide-in {
        animation: slideInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(40px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    
    .previous-usage-box {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        padding: 1.25rem;
        margin-top: 1rem;
        backdrop-filter: blur(8px);
    }
    
    .verification-timestamp {
        font-family: 'JetBrains Mono', 'Courier New', monospace;
        font-size: 0.8rem;
        background: rgba(0, 0, 0, 0.4);
        padding: 0.375rem 0.75rem;
        border-radius: 8px;
        display: inline-block;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .participant-avatar {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        font-weight: bold;
        margin: 0 auto 1rem auto;
        border: 3px solid rgba(255, 255, 255, 0.3);
        animation: avatarFloat 3s ease-in-out infinite;
        position: relative;
        overflow: hidden;
    }
    
    .participant-avatar::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: rgba(255, 255, 255, 0.1);
        transform: rotate(45deg);
        animation: avatarShimmer 2s infinite;
    }
    
    @keyframes avatarFloat {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-5px); }
    }
    
    @keyframes avatarShimmer {
        0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
        100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
    }
    
    .result-title {
        font-size: 2rem;
        font-weight: 900;
        margin-top: 1.5rem;
        text-shadow: 0 3px 6px rgba(0, 0, 0, 0.4);
        letter-spacing: -0.025em;
    }
    
    .participant-name {
        font-size: 1.5rem;
        font-weight: 800;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        margin-bottom: 0.5rem;
    }
    
    .status-indicator {
        position: absolute;
        top: 1rem;
        right: 1rem;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        animation: statusPulse 2s infinite;
    }
    
    .status-success { background-color: var(--success-color); }
    .status-error { background-color: var(--error-color); }
    .status-warning { background-color: var(--warning-color); }
    .status-already-used { background-color: var(--already-used-color); }
    
    @keyframes statusPulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.7; transform: scale(1.2); }
    }
    
    .action-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-top: 2rem;
    }
    
    .action-grid-centered {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        margin-top: 2rem;
    }
    
    .action-grid-centered > * {
        width: 200px;
        max-width: 100%;
    }
    
    .countdown-bar {
        height: 4px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 2px;
        overflow: hidden;
        margin-top: 1rem;
    }
    
    .countdown-progress {
        height: 100%;
        background: var(--success-color);
        width: 100%;
        animation: countdownProgress 3s linear;
    }
    
    @keyframes countdownProgress {
        from { width: 100%; }
        to { width: 0%; }
    }
    
    @media (max-width: 640px) {
        .result-icon { font-size: 4rem; }
        .result-title { font-size: 1.5rem; }
        .participant-name { font-size: 1.25rem; }
        .action-grid { grid-template-columns: 1fr; gap: 0.75rem; }
        .action-grid-centered > * { width: 100%; }
        .glass-effect { margin: 0.5rem; padding: 1.5rem; }
    }
    
    .zone-info {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        backdrop-filter: blur(8px);
    }
    
    .access-time-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 0.5rem;
        font-size: 0.875rem;
        color: rgba(255, 255, 255, 0.8);
    }
    
    .logout-form {
        display: inline;
        width: 100%;
    }
    
    .top-bar {
        position: absolute;
        top: 1rem;
        left: 1rem;
        right: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        z-index: 10;
    }
    
    .top-bar .logout-btn {
        background: rgba(239, 68, 68, 0.9);
        color: white;
        border: none;
        padding: 0.5rem 0.75rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
        transition: all 0.3s ease;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .top-bar .logout-btn:hover {
        background: rgba(220, 38, 38, 0.9);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(239, 68, 68, 0.3);
    }

    .security-notice {
        background: rgba(245, 158, 11, 0.1);
        border: 1px solid rgba(245, 158, 11, 0.3);
        border-radius: 12px;
        padding: 1rem;
        margin-top: 1rem;
        backdrop-filter: blur(8px);
    }
</style>
</head>
<body class="flex items-center justify-center">

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-lg mx-auto glass-effect rounded-3xl p-8 slide-in relative">
            
            <!-- Barre supérieure avec déconnexion -->
            <div class="top-bar">
                <div class="status-indicator 
                    @if(isset($alert) && $alert['status'] == 'success') status-success
                    @elseif(isset($alert) && $alert['status'] == 'already_used') status-already-used
                    @elseif(isset($alert) && $alert['status'] == 'warning') status-warning
                    @else status-error
                    @endif"></div>
            </div>
            
            <!-- Header -->
            <div class="text-center mb-8 mt-8">
                <h1 class="text-2xl font-bold text-white mb-2">{{ $event->event_title ?? 'Vérification Ticket' }}</h1>
                <p class="text-white text-opacity-80 text-sm">{{ $organization->org_name ?? 'Organisation' }}</p>
                
                <!-- Badge de zone -->
                @if(request()->query('zone') || session('current_zone'))
                    <div class="mt-4">
                        <div class="zone-badge text-white">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>{{ ucfirst(str_replace('_', ' ', request()->query('zone') ?: session('current_zone'))) }}</span>
                        </div>
                    </div>
                @endif
                
                <p class="text-white text-opacity-60 text-xs mt-3">
                    <i class="fas fa-clock me-1"></i> {{ now()->format('d/m/Y H:i:s') }}
                </p>
            </div>
            
            <!-- Résultat de la vérification -->
            @if(isset($alert))
                <!-- Vérifier si l'utilisateur est connecté en tant que vérificateur -->
                @if(!session('verifier_authenticated'))
                    <!-- Message pour utilisateur non authentifié -->
                    <div class="text-center mb-8">
                        <div class="result-icon text-orange-400">
                            <i class="fas fa-user-lock"></i>
                        </div>
                        <h2 class="result-title text-white">ACCÈS RESTREINT</h2>
                        <p class="text-orange-200 text-sm mt-2 opacity-90">Connexion requise pour valider</p>
                        
                        <div class="mt-6 rounded-xl p-6 error-bg">
                            <p class="text-red-100 font-semibold text-center text-lg leading-relaxed">
                                Vous n'êtes pas autorisé à valider ce ticket
                            </p>
                        </div>
                    </div>
                @else
                    <!-- Affichage normal pour utilisateur authentifié -->
                    <div class="text-center mb-8">
                        @if($alert['status'] == 'success')
                            <div class="result-icon text-green-400">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h2 class="result-title text-white">ACCÈS AUTORISÉ</h2>
                            <p class="text-green-200 text-sm mt-2 opacity-90">Bienvenue ! Profitez de l'événement</p>
                        @elseif($alert['status'] == 'already_used')
                            <div class="result-icon text-purple-400">
                                <i class="fas fa-redo-alt"></i>
                            </div>
                            <h2 class="result-title text-white">DÉJÀ UTILISÉ</h2>
                            <p class="text-purple-200 text-sm mt-2 opacity-90">Ce ticket a déjà été scanné</p>
                        @elseif($alert['status'] == 'warning')
                            <div class="result-icon text-yellow-400">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <h2 class="result-title text-white">ATTENTION</h2>
                            <p class="text-yellow-200 text-sm mt-2 opacity-90">Hors horaire d'accès</p>
                        @else
                            <div class="result-icon text-red-500">
                                <i class="fas fa-ban"></i>
                            </div>
                            <h2 class="result-title text-white">ACCÈS REFUSÉ</h2>
                            <p class="text-red-200 text-sm mt-2 opacity-90">Ticket invalide ou non autorisé</p>
                        @endif
                        
                        <div class="mt-6 rounded-xl p-6 
                            @if($alert['status'] == 'success') success-bg
                            @elseif($alert['status'] == 'already_used') already-used-bg
                            @elseif($alert['status'] == 'warning') warning-bg
                            @else error-bg
                            @endif">
                            
                            <p class="
                                @if($alert['status'] == 'success') text-green-100
                                @elseif($alert['status'] == 'already_used') text-purple-100
                                @elseif($alert['status'] == 'warning') text-yellow-100
                                @else text-red-100
                                @endif font-semibold text-center text-lg leading-relaxed">
                                {{ $alert['message'] }}
                            </p>
                            
                            @php
                                // Déterminer le nom du participant depuis plusieurs sources
                                $participantName = null;
                                
                                if (isset($alert['user']) && $alert['user']) {
                                    $participantName = $alert['user'];
                                } elseif (isset($inscription->fullname) && $inscription->fullname) {
                                    $participantName = $inscription->fullname;
                                } elseif (isset($inscription->form_data)) {
                                    $formData = json_decode($inscription->form_data, true);
                                    $participantName = $formData['full_name'] ?? $formData['fullname'] ?? null;
                                }
                            @endphp

                            <!-- Notice de sécurité pour les horaires restreints -->
                            @if($alert['status'] == 'warning')
                                <div class="security-notice mt-4">
                                    <p class="text-yellow-200 text-sm text-center flex items-center justify-center gap-2">
                                        <i class="fas fa-shield-alt"></i>
                                        Les informations du participant ne sont pas affichées pour des raisons de sécurité en dehors des horaires d'accès autorisés.
                                    </p>
                                </div>
                            @endif

                            <!-- Détails de l'utilisation précédente -->
                            @if($alert['status'] == 'already_used' && isset($alert['previous_usage']))
                                <div class="previous-usage-box mt-6">
                                    <h4 class="text-purple-200 font-bold mb-3 text-center text-sm flex items-center justify-center gap-2">
                                        <i class="fas fa-history"></i>Première utilisation
                                    </h4>
                                    <div class="space-y-3 text-sm text-purple-100">
                                        <div class="flex justify-between items-center">
                                            <span class="verification-timestamp">{{ $alert['previous_usage']['verified_at'] }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="opacity-80 flex items-center gap-2">
                                               Vérificateur:
                                            </span>
                                            <span class="font-semibold">{{ $alert['previous_usage']['verifier_name'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                        </div>
                    </div>
                    
                    <!-- Afficher les informations participant SEULEMENT si ce n'est PAS un warning (hors horaire) -->
                    @if(isset($inscription) && $alert['status'] !== 'warning')
                        <div class="mt-6 p-5 rounded-xl glass-effect">
                            <h3 class="text-white font-bold mb-4 text-center text-sm flex items-center justify-center gap-2">
                                <i class="fas fa-id-card"></i>Informations participant
                            </h3>
                            
                            <div class="space-y-3">
                                @php
                                    // Enrichir les données si nécessaire
                                    if (isset($inscription->form_data) && $inscription->form_data) {
                                        $formData = json_decode($inscription->form_data, true);
                                        if (!$inscription->fullname && isset($formData['full_name'])) {
                                            $inscription->fullname = $formData['full_name'];
                                        }
                                        if (!$inscription->email && isset($formData['email'])) {
                                            $inscription->email = $formData['email'];
                                        }
                                        if (!$inscription->organization && isset($formData['organization'])) {
                                            $inscription->organization = $formData['organization'];
                                        }
                                    }
                                @endphp
                                
                                @if(isset($inscription->fullname) && $inscription->fullname)
                                    <div class="info-item py-3 px-3">
                                        <div class="flex justify-between items-center">
                                            <span class="text-white opacity-80 text-sm flex items-center gap-2">
                                                Nom:
                                            </span>
                                            <span class="text-white font-semibold text-sm">{{ $inscription->fullname }}</span>
                                        </div>
                                    </div>
                                @endif
                                
                                @if(isset($inscription->organization) && $inscription->organization)
                                    <div class="info-item py-3 px-3">
                                        <div class="flex justify-between items-center">
                                            <span class="text-white opacity-80 text-sm flex items-center gap-2">
                                               Organisation:
                                            </span>
                                            <span class="text-white text-sm">{{ $inscription->organization }}</span>
                                        </div>
                                    </div>
                                @endif
                                
                                @if(isset($inscription->registration_number) && $inscription->registration_number)
                                    <div class="info-item py-3 px-3">
                                        <div class="flex justify-between items-center">

                                            <span class="text-white font-mono text-xs text-center bg-black bg-opacity-40 px-3 py-2 rounded-lg border border-white border-opacity-20">
                                                {{ $inscription->registration_number }}
                                            </span>
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="info-item py-3 px-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-white opacity-80 text-sm flex items-center gap-2">
                                            <i class="fas fa-credit-card"></i>Paiement:
                                        </span>
                                        <div>
                                            @if(isset($inscription->payment_status) && $inscription->payment_status === 'paid')
                                                <span class="text-green-300 text-sm font-semibold bg-green-500 bg-opacity-20 px-3 py-1 rounded-full border border-green-400 border-opacity-30">
                                                    <i class="fas fa-check-circle me-1"></i>PAYÉ
                                                </span>
                                            @else
                                                <span class="text-red-300 text-sm font-semibold bg-red-500 bg-opacity-20 px-3 py-1 rounded-full border border-red-400 border-opacity-30">
                                                    <i class="fas fa-times-circle me-1"></i>NON PAYÉ
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
                
            @else
                <div class="text-center">
                    <div class="result-icon text-gray-400">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <h2 class="result-title text-white">Aucun résultat</h2>
                    <p class="text-white text-opacity-70 mt-3 text-sm">Aucune information de vérification disponible.</p>
                </div>
            @endif
            
            <!-- Actions rapides -->
            @if(session('verifier_authenticated'))
                <!-- Actions pour utilisateur authentifié -->
                <div class="action-grid-centered">
                    @if(Route::has('event.verifier.history'))
                        <a href="{{ route('event.verifier.history', ['org_slug' => request()->route('org_slug'), 'event_slug' => request()->route('event_slug')]) }}" 
                           class="btn-secondary text-white py-3 px-4 rounded-xl font-semibold text-center text-sm transition-all duration-300 flex items-center justify-center gap-2">
                            <i class="fas fa-history"></i>
                            <span>Mon Historique</span>
                        </a>
                    @else
                        <a href="javascript:window.location.reload()" 
                           class="btn-secondary text-white py-3 px-4 rounded-xl font-semibold text-center text-sm transition-all duration-300 flex items-center justify-center gap-2">
                            <i class="fas fa-redo"></i>
                            <span>Actualiser</span>
                        </a>
                    @endif
                    
                    <!-- Bouton de déconnexion rapide -->
                    <form method="POST" action="{{ route('event.verifier.logout', ['org_slug' => request()->route('org_slug'), 'event_slug' => request()->route('event_slug')]) }}" class="logout-form">
                        @csrf
                        <button type="submit" class="btn-danger text-white py-3 px-4 rounded-xl font-semibold text-center text-sm transition-all duration-300 hover:shadow-xl flex items-center justify-center gap-2 w-full">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Déconnexion</span>
                        </button>
                    </form>
                </div>
            @else
                <!-- Actions pour utilisateur non authentifié -->
                <div class="d-grid mb-3">
                    <a href="{{ route('event.verifier.auth', ['org_slug' => request()->route('org_slug'), 'event_slug' => request()->route('event_slug')]) }}" 
                       class="btn btn-primary btn-lg text-white py-4 px-5 rounded-xl font-semibold text-center transition-all duration-300 hover:shadow-xl flex items-center justify-center gap-2">
                        <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                    </a>
                </div>
            @endif
            
            <!-- Informations du vérificateur -->
            <div class="mt-6 pt-4 border-t border-white border-opacity-20 text-center">
                <small class="text-white text-opacity-70 text-xs flex items-center justify-center gap-2">
                    <i class="fas fa-user-shield"></i>
                    @if(session('verifier_authenticated'))
                        Vérification par {{ session('verifier_name', 'Vérificateur') }}
                        @if(session('verifier_role'))
                            <span class="opacity-60">·</span>
                            <span class="opacity-80">{{ ucfirst(session('verifier_role')) }}</span>
                        @endif
                    @else
                        Vérification publique
                    @endif
                </small>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="text-center mt-6">
            <p class="text-white text-opacity-50 text-xs flex items-center justify-center gap-2">
                <i class="fas fa-shield-alt"></i>
                &copy; {{ date('Y') }} {{ $organization->org_name ?? 'Organisation' }} | Système de vérification sécurisé
            </p>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const status = '{{ $alert["status"] ?? "" }}';
            
            // Feedback visuel et sonore selon le résultat
            if (status === 'success') {
                console.log('✅ Vérification réussie');
                if (navigator.vibrate) {
                    navigator.vibrate([200, 100, 200]);
                }
                
                // Auto-redirection après 3 secondes pour les succès (seulement si authentifié)
                @if(session('verifier_authenticated'))
                    setTimeout(() => {
                        const scanLink = document.querySelector('a[href*="scan"]');
                        if (scanLink && !document.hidden) {
                            window.location.href = scanLink.href;
                        }
                    }, 3000);
                @endif
                
            } else if (status === 'error') {
                console.log('❌ Vérification échouée');
                if (navigator.vibrate) {
                    navigator.vibrate([400, 200, 400, 200, 400]);
                }
            } else if (status === 'already_used') {
                console.log('🔄 Ticket déjà utilisé');
                if (navigator.vibrate) {
                    navigator.vibrate([300, 150, 300, 150, 300]);
                }
            } else if (status === 'warning') {
                console.log('⚠️ Attention requise');
                if (navigator.vibrate) {
                    navigator.vibrate([300, 150, 300]);
                }
            }
            
            // Raccourcis clavier améliorés
            document.addEventListener('keydown', function(e) {
                // Éviter les conflits si un élément de saisie est actif
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
                    return;
                }
                
                switch(e.key) {
                    case 'Enter':
                    case ' ':
                        e.preventDefault();
                        const scanLink = document.querySelector('a[href*="scan"]');
                        if (scanLink) {
                            scanLink.click();
                        } else {
                            const authLink = document.querySelector('a[href*="auth"]');
                            if (authLink) authLink.click();
                        }
                        break;
                        
                    case 'h':
                    case 'H':
                        e.preventDefault();
                        const historyLink = document.querySelector('a[href*="history"]');
                        if (historyLink) historyLink.click();
                        break;
                        
                    case 'Escape':
                        e.preventDefault();
                        // Ne fait rien - pas de retour autorisé
                        break;
                        
                    case 'r':
                    case 'R':
                        e.preventDefault();
                        window.location.reload();
                        break;
                        
                    case 'l':
                    case 'L':
                        @if(session('verifier_authenticated'))
                            e.preventDefault();
                            const logoutBtn = document.querySelector('button[type="submit"]');
                            if (logoutBtn && logoutBtn.textContent.includes('Déconnexion')) {
                                logoutBtn.click();
                            }
                        @endif
                        break;
                }
            });
            
            // Gestion de la visibilité de la page (pause auto-redirection si pas visible)
            let autoRedirectPaused = false;
            document.addEventListener('visibilitychange', function() {
                if (document.hidden && status === 'success') {
                    autoRedirectPaused = true;
                }
            });
            
            // Focus automatique pour les lecteurs d'écran
            const mainTitle = document.querySelector('.result-title');
            if (mainTitle) {
                mainTitle.focus();
            }
            
            // Animation d'entrée pour l'avatar
            const avatar = document.querySelector('.participant-avatar');
            if (avatar) {
                setTimeout(() => {
                    avatar.style.transform = 'scale(1.1)';
                    setTimeout(() => {
                        avatar.style.transform = 'scale(1)';
                    }, 200);
                }, 500);
            }
        });
    </script>
</body>
</html>