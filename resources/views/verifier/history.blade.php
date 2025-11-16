<?php 
/* var_dump($stats);
die; */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des Vérifications - {{ $event->event_title }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
    :root {
        --primary-color: {{ $event->primary_color ?? '#025225' }};
        --secondary-color: {{ $event->secondary_color ?? '#0F8A5F' }};
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --error-color: #ef4444;
    }
    
    body {
        background: var(--primary-color);
        min-height: 100vh;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .glass-effect {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 8px 32px 0 rgba(0, 20, 10, 0.37);
    }
    
    .btn-primary {
        background: var(--primary-color);
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        background: var(--secondary-color);
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }
    
    .status-badge {
        padding: 0.375rem 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .status-success {
        background-color: rgba(16, 185, 129, 0.2);
        color: #065f46;
        border: 1px solid var(--success-color);
    }
    
    .status-warning {
        background-color: rgba(245, 158, 11, 0.2);
        color: #92400e;
        border: 1px solid var(--warning-color);
    }
    
    .status-error {
        background-color: rgba(239, 68, 68, 0.2);
        color: #991b1b;
        border: 1px solid var(--error-color);
    }
    
    .status-time {
        background-color: rgba(255, 165, 0, 0.2);
        color: #cc8400;
        border: 1px solid #ff8c00;
    }
    
    .verification-item {
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        transition: background-color 0.2s ease;
    }
    
    .verification-item:hover {
        background-color: rgba(255, 255, 255, 0.05);
    }
    
    .verification-item:last-child {
        border-bottom: none;
    }
    
    @media (max-width: 640px) {
        .container {
            padding: 1rem;
        }
    }
</style>
</head>
<body class="min-h-screen">
    <div class="container mx-auto px-4 py-8">
        
        <!-- Header -->
        <div class="glass-effect rounded-2xl p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="mb-4 md:mb-0">
                    <h1 class="text-3xl font-bold text-white mb-2">
                        <i class="fas fa-history mr-3"></i>Mon Historique de Vérifications
                    </h1>
                    <p class="text-white text-opacity-80">
                        {{ $event->event_title }} - {{ $organization->org_name }}
                    </p>
                    <p class="text-white text-opacity-60 text-sm">
                        <i class="fas fa-user-shield mr-2"></i>{{ $verifier['name'] ?? 'Vérificateur' }}
                    </p>
                    <p class="text-white text-opacity-60 text-sm">
                        {{ $verifications->count() }} vérification(s) effectuée(s) par vous
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('event.verifier.scan', ['org_slug' => $org_slug, 'event_slug' => $event_slug]) }}" 
                       class="btn-primary text-white px-6 py-3 rounded-lg font-medium text-center transition-all duration-300">
                        <i class="fas fa-qrcode mr-2"></i>Retour au Scanner
                    </a>
                    <button onclick="window.location.reload()" 
                            class="bg-white bg-opacity-20 text-white px-6 py-3 rounded-lg font-medium transition-all duration-300 hover:bg-opacity-30">
                        <i class="fas fa-sync-alt mr-2"></i>Actualiser
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistiques personnelles -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="glass-effect rounded-xl p-4 text-center">
                <i class="fas fa-check-circle text-green-400 text-3xl mb-2"></i>
                <div class="text-2xl font-bold text-white">{{ $stats->successful_verifications ?? 0 }}</div>
                <div class="text-white text-opacity-70 text-sm">Mes validations</div>
            </div>
            <div class="glass-effect rounded-xl p-4 text-center">
                <i class="fas fa-exclamation-triangle text-yellow-400 text-3xl mb-2"></i>
                <div class="text-2xl font-bold text-white">{{ $stats->already_used_attempts ?? 0 }}</div>
                <div class="text-white text-opacity-70 text-sm">Déjà utilisés</div>
            </div>
            <div class="glass-effect rounded-xl p-4 text-center">
                <i class="fas fa-times-circle text-red-400 text-3xl mb-2"></i>
                <div class="text-2xl font-bold text-white">{{ $stats->failed_verifications ?? 0 }}</div>
                <div class="text-white text-opacity-70 text-sm">Mes refus</div>
            </div>
            <div class="glass-effect rounded-xl p-4 text-center">
                <i class="fas fa-clock text-orange-400 text-3xl mb-2"></i>
                <div class="text-2xl font-bold text-white">{{ $stats->time_restricted_attempts ?? 0 }}</div>
                <div class="text-white text-opacity-70 text-sm">Hors horaire</div>
            </div>
        </div>

        <!-- Statistiques par zone (si disponibles) -->
        @if(isset($stats->zone_stats) && $stats->zone_stats->count() > 0)
        <div class="glass-effect rounded-2xl p-6 mb-6">
            <h2 class="text-xl font-bold text-white mb-4">
                <i class="fas fa-map-marker-alt mr-2"></i>Mes Vérifications par Zone
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($stats->zone_stats as $zone => $zoneStats)
                <div class="bg-white bg-opacity-10 rounded-lg p-4">
                    <h3 class="text-white font-semibold mb-2">{{ ucfirst($zone) }}</h3>
                    <div class="space-y-1 text-sm">
                        <div class="flex justify-between text-white text-opacity-80">
                            <span>Total :</span>
                            <span class="font-medium">{{ $zoneStats->total_attempts }}</span>
                        </div>
                        <div class="flex justify-between text-green-300">
                            <span>Validées :</span>
                            <span class="font-medium">{{ $zoneStats->successful_entries }}</span>
                        </div>
                        @if($zoneStats->duplicate_attempts > 0)
                        <div class="flex justify-between text-yellow-300">
                            <span>Doublons :</span>
                            <span class="font-medium">{{ $zoneStats->duplicate_attempts }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Liste des vérifications -->
        <div class="glass-effect rounded-2xl overflow-hidden">
            <div class="p-6 border-b border-white border-opacity-10">
                <h2 class="text-xl font-bold text-white mb-2">
                    <i class="fas fa-list mr-2"></i>Mes Dernières Vérifications
                </h2>
                <p class="text-white text-opacity-70 text-sm">
                    Historique personnel - {{ now()->format('d/m/Y H:i:s') }}
                </p>
                @if(isset($stats->total_verifications) && $stats->total_verifications > 0)
                <p class="text-white text-opacity-60 text-xs mt-1">
                    <i class="fas fa-chart-line mr-1"></i>
                    Total: {{ $stats->total_verifications }} vérifications depuis {{ $stats->first_verification ? \Carbon\Carbon::parse($stats->first_verification)->format('d/m/Y') : 'le début' }}
                </p>
                @endif
            </div>
            
            <div class="max-h-96 overflow-y-auto">
                @if($verifications->count() > 0)
                    @foreach($verifications as $verification)
                        <div class="verification-item p-4">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div class="flex-1 mb-3 md:mb-0">
                                    <div class="flex items-start space-x-4">
                                        <!-- Statut visuel -->
                                        <div class="flex-shrink-0 mt-1">
                                            @if($verification->status === 'success')
                                                <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                                            @elseif($verification->status === 'already_used')
                                                <div class="w-3 h-3 bg-yellow-400 rounded-full"></div>
                                            @elseif(in_array($verification->status, ['access_time_restricted', 'access_not_started', 'access_ended', 'event_not_active']))
                                                <div class="w-3 h-3 bg-orange-400 rounded-full"></div>
                                            @else
                                                <div class="w-3 h-3 bg-red-400 rounded-full"></div>
                                            @endif
                                        </div>
                                        
                                        <!-- Informations participant -->
                                        <div class="flex-1">
                                            <h3 class="text-white font-semibold">
                                                {{ $verification->fullname ?? 'Participant inconnu' }}
                                            </h3>
                                            @if($verification->email)
                                                <p class="text-white text-opacity-70 text-sm">{{ $verification->email }}</p>
                                            @endif
                                            @if($verification->organization)
                                                <p class="text-white text-opacity-60 text-xs">{{ $verification->organization }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Détails vérification -->
                                <div class="flex flex-col md:flex-row md:items-center space-y-2 md:space-y-0 md:space-x-4">
                                    <!-- Zone -->
                                    @if($verification->access_zone)
                                        <span class="inline-flex items-center px-2 py-1 bg-blue-500 bg-opacity-20 text-blue-300 rounded text-xs">
                                            <i class="fas fa-map-marker-alt mr-1"></i>
                                            {{ ucfirst($verification->access_zone) }}
                                        </span>
                                    @endif
                                    
                                    <!-- Statut en français -->
                                    @if($verification->status === 'success')
                                        <span class="status-badge status-success">
                                            <i class="fas fa-check mr-1"></i>Validé
                                        </span>
                                    @elseif($verification->status === 'already_used')
                                        <span class="status-badge status-warning">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>Déjà utilisé
                                        </span>
                                    @elseif($verification->status === 'not_paid')
                                        <span class="status-badge status-error">
                                            <i class="fas fa-credit-card mr-1"></i>Non payé
                                        </span>
                                    @elseif($verification->status === 'not_confirmed')
                                        <span class="status-badge status-error">
                                            <i class="fas fa-user-times mr-1"></i>Non confirmé
                                        </span>
                                    @elseif($verification->status === 'registration_not_found')
                                        <span class="status-badge status-error">
                                            <i class="fas fa-search mr-1"></i>Inscription introuvable
                                        </span>
                                    @elseif($verification->status === 'invalid_hash')
                                        <span class="status-badge status-error">
                                            <i class="fas fa-key mr-1"></i>Code invalide
                                        </span>
                                    @elseif($verification->status === 'zone_access_denied')
                                        <span class="status-badge status-error">
                                            <i class="fas fa-ban mr-1"></i>Zone interdite
                                        </span>
                                    @elseif($verification->status === 'event_not_active')
                                        <span class="status-badge status-time">
                                            <i class="fas fa-calendar-times mr-1"></i>Événement inactif
                                        </span>
                                    @elseif($verification->status === 'access_time_restricted')
                                        <span class="status-badge status-time">
                                            <i class="fas fa-clock mr-1"></i>Hors horaires d'accès
                                        </span>
                                    @elseif($verification->status === 'access_not_started')
                                        <span class="status-badge status-time">
                                            <i class="fas fa-hourglass-start mr-1"></i>Accès pas encore ouvert
                                        </span>
                                    @elseif($verification->status === 'access_ended')
                                        <span class="status-badge status-time">
                                            <i class="fas fa-hourglass-end mr-1"></i>Accès terminé
                                        </span>
                                    @else
                                        <span class="status-badge status-error">
                                            <i class="fas fa-times mr-1"></i>Erreur inconnue
                                        </span>
                                    @endif
                                    
                                    <!-- Heure -->
                                    <span class="text-white text-opacity-60 text-xs">
                                        {{ \Carbon\Carbon::parse($verification->verified_at)->format('H:i:s') }}
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Vérificateur -->
                            <div class="mt-2 pt-2 border-t border-white border-opacity-10">
                                <p class="text-white text-opacity-50 text-xs">
                                    <i class="fas fa-user-shield mr-1"></i>
                                    Vérifié par {{ $verification->verifier_name ?? session('verifier_name', 'Vérificateur') }}
                                    @if($verification->ip_address)
                                        <span class="ml-2">IP: {{ $verification->ip_address }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="p-8 text-center">
                        <i class="fas fa-inbox text-white text-opacity-30 text-5xl mb-4"></i>
                        <h3 class="text-white text-opacity-70 text-lg mb-2">Aucune vérification personnelle</h3>
                        <p class="text-white text-opacity-50 text-sm mb-4">
                            Vous n'avez encore effectué aucune vérification pour cet événement.
                        </p>
                        <a href="{{ route('event.verifier.scan', ['org_slug' => $org_slug, 'event_slug' => $event_slug]) }}" 
                           class="btn-primary text-white px-6 py-3 rounded-lg font-medium inline-block">
                            <i class="fas fa-qrcode mr-2"></i>Commencer les Vérifications
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6">
            <p class="text-white text-opacity-50 text-xs">
                &copy; {{ date('Y') }} {{ $organization->org_name }} | Système de vérification sécurisé
            </p>
        </div>
    </div>

    <script>
        // Auto-refresh toutes les 30 secondes
        setInterval(function() {
            window.location.reload();
        }, 30000);
        
        // Raccourcis clavier
        document.addEventListener('keydown', function(e) {
            switch(e.key) {
                case 'r':
                case 'R':
                    e.preventDefault();
                    window.location.reload();
                    break;
                    
                case 's':
                case 'S':
                    e.preventDefault();
                    window.location.href = '{{ route("event.verifier.scan", ["org_slug" => $org_slug, "event_slug" => $event_slug]) }}';
                    break;
                    
                case 'Escape':
                    e.preventDefault();
                    window.history.back();
                    break;
            }
        });

    </script>
</body>
</html>