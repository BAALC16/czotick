@extends('organization.layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    .stat-card {
        background: white;
        border-radius: 0.5rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .stat-card .stat-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .stat-card .stat-value {
        font-size: 2rem;
        font-weight: bold;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }
    
    .stat-card .stat-label {
        color: #6b7280;
        font-size: 0.875rem;
    }
    
    .event-card {
        background: white;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .event-card:hover {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="mt-1 text-sm text-gray-500">
            @if($userRole === 'admin' || $userRole === 'owner')
                Vue d'ensemble complète de l'organisation
            @elseif($userRole === 'organizer')
                Vue d'ensemble de vos événements
            @elseif($userRole === 'referrer')
                Vue d'ensemble de vos événements et gains
            @else
                Vue d'ensemble
            @endif
        </p>
    </div>

    <!-- Notifications -->
    @if(isset($roleSpecificData['notifications']) && $roleSpecificData['notifications']->count() > 0)
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-blue-900 mb-2">
            <i class="fas fa-bell mr-2"></i>Notifications ({{ $roleSpecificData['notifications']->count() }})
        </h3>
        <div class="space-y-2">
            @foreach($roleSpecificData['notifications'] as $notification)
            <div class="bg-white rounded p-3 border border-blue-100">
                <p class="font-medium text-gray-900">{{ $notification->title }}</p>
                <p class="text-sm text-gray-600">{{ $notification->message }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @if($userRole === 'referrer')
            <!-- Statistiques pour apporteur d'affaire -->
            <div class="stat-card">
                <div class="stat-icon bg-blue-100 text-blue-600">
                    <i class="fas fa-calendar"></i>
                </div>
                <div class="stat-value">{{ $stats['total_events'] ?? 0 }}</div>
                <div class="stat-label">Événements disponibles</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-purple-100 text-purple-600">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value">{{ $stats['total_registrations'] ?? 0 }}</div>
                <div class="stat-label">Inscriptions</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-green-100 text-green-600">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-value">{{ number_format($stats['total_earnings'] ?? 0, 0, ',', ' ') }} FCFA</div>
                <div class="stat-label">Gains totaux</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-yellow-100 text-yellow-600">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-value">{{ number_format($stats['pending_earnings'] ?? 0, 0, ',', ' ') }} FCFA</div>
                <div class="stat-label">Gains en attente</div>
            </div>
        @else
            <!-- Statistiques pour admin/organisateur -->
            <div class="stat-card">
                <div class="stat-icon bg-blue-100 text-blue-600">
                    <i class="fas fa-calendar"></i>
                </div>
                <div class="stat-value">{{ $stats['total_events'] ?? 0 }}</div>
                <div class="stat-label">Événements totaux</div>
            </div>

            @if($userRole === 'admin' || $userRole === 'owner')
            <div class="stat-card">
                <div class="stat-icon bg-green-100 text-green-600">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-value">{{ $stats['published_events'] ?? 0 }}</div>
                <div class="stat-label">Événements publiés</div>
            </div>
            @endif

            <div class="stat-card">
                <div class="stat-icon bg-purple-100 text-purple-600">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value">{{ $stats['total_registrations'] ?? 0 }}</div>
                <div class="stat-label">Inscriptions totales</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-orange-100 text-orange-600">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-value">{{ number_format($stats['total_revenue'] ?? 0, 0, ',', ' ') }} FCFA</div>
                <div class="stat-label">Revenus totaux</div>
            </div>
        @endif
    </div>

    <!-- Additional Statistics -->
    @if($userRole !== 'referrer')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Confirmed Registrations -->
        <div class="stat-card">
            <div class="stat-icon bg-emerald-100 text-emerald-600">
                <i class="fas fa-check"></i>
            </div>
            <div class="stat-value">{{ $stats['confirmed_registrations'] ?? 0 }}</div>
            <div class="stat-label">Inscriptions confirmées</div>
        </div>

        <!-- Monthly Revenue -->
        <div class="stat-card">
            <div class="stat-icon bg-yellow-100 text-yellow-600">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['monthly_revenue'] ?? 0, 0, ',', ' ') }} FCFA</div>
            <div class="stat-label">Revenus du mois</div>
        </div>

        <!-- Monthly Registrations -->
        <div class="stat-card">
            <div class="stat-icon bg-indigo-100 text-indigo-600">
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="stat-value">{{ $stats['monthly_registrations'] ?? 0 }}</div>
            <div class="stat-label">Inscriptions du mois</div>
        </div>
    </div>
    @endif

    <!-- Statistiques supplémentaires pour admin -->
    @if(($userRole === 'admin' || $userRole === 'owner') && isset($stats['total_referrers']))
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="stat-card">
            <div class="stat-icon bg-teal-100 text-teal-600">
                <i class="fas fa-handshake"></i>
            </div>
            <div class="stat-value">{{ $stats['total_referrers'] ?? 0 }}</div>
            <div class="stat-label">Collaborateurs</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-cyan-100 text-cyan-600">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-value">{{ $stats['active_referrers'] ?? 0 }}</div>
            <div class="stat-label">Collaborateurs actifs</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-pink-100 text-pink-600">
                <i class="fas fa-coins"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['total_referrer_earnings'] ?? 0, 0, ',', ' ') }} FCFA</div>
            <div class="stat-label">Commissions versées</div>
        </div>
    </div>
    @endif

    <!-- Statistiques supplémentaires pour apporteur -->
    @if($userRole === 'referrer')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="stat-card">
            <div class="stat-icon bg-emerald-100 text-emerald-600">
                <i class="fas fa-check"></i>
            </div>
            <div class="stat-value">{{ $stats['confirmed_registrations'] ?? 0 }}</div>
            <div class="stat-label">Inscriptions confirmées</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-indigo-100 text-indigo-600">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['monthly_earnings'] ?? 0, 0, ',', ' ') }} FCFA</div>
            <div class="stat-label">Gains du mois</div>
        </div>
    </div>
    @endif

    <!-- Recent Events and Upcoming Events -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Events -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">
                <i class="fas fa-clock text-gray-500 mr-2"></i>
                @if($userRole === 'referrer')
                    Mes événements
                @else
                    Événements récents
                @endif
            </h2>
            
            @if($recentEvents && $recentEvents->count() > 0)
                <div class="space-y-3">
                    @foreach($recentEvents as $event)
                        <div class="event-card">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900">{{ $event->event_title }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">
                                        @if($event->event_date)
                                            <i class="fas fa-calendar-alt mr-1"></i>
                                            {{ $event->event_date->format('d/m/Y') }}
                                        @endif
                                    </p>
                                    <div class="mt-2 flex items-center gap-2">
                                        @if($userRole !== 'referrer')
                                            @if($event->is_published)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-check-circle mr-1"></i> Publié
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    <i class="fas fa-clock mr-1"></i> Brouillon
                                                </span>
                                            @endif
                                        @endif
                                        
                                        @if($userRole === 'referrer' && isset($event->share_url))
                                            <button onclick="copyToClipboard('{{ $event->share_url }}')" 
                                                    class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800 hover:bg-blue-200">
                                                <i class="fas fa-share mr-1"></i> Copier le lien
                                            </button>
                                        @endif
                                    </div>
                                    @if($userRole === 'referrer' && isset($event->share_url))
                                        <div class="mt-2">
                                            <input type="text" 
                                                   value="{{ $event->share_url }}" 
                                                   readonly 
                                                   class="w-full text-xs p-2 border rounded bg-gray-50">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">
                    @if($userRole === 'referrer')
                        Aucun événement disponible pour le moment
                    @else
                        Aucun événement récent
                    @endif
                </p>
            @endif
        </div>

        <!-- Upcoming Events -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">
                <i class="fas fa-calendar-check text-gray-500 mr-2"></i>
                Événements à venir
            </h2>
            
            @if($upcomingEvents && $upcomingEvents->count() > 0)
                <div class="space-y-3">
                    @foreach($upcomingEvents as $event)
                        <div class="event-card">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900">{{ $event->event_title }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">
                                        @if($event->event_date)
                                            <i class="fas fa-calendar-alt mr-1"></i>
                                            {{ $event->event_date->format('d/m/Y') }}
                                        @endif
                                        @if($event->event_location)
                                            <br><i class="fas fa-map-marker-alt mr-1"></i>
                                            {{ $event->event_location }}
                                        @endif
                                    </p>
                                    @if($event->max_participants)
                                        <p class="text-xs text-gray-400 mt-1">
                                            Max: {{ $event->max_participants }} participants
                                        </p>
                                    @endif
                                    @if($userRole === 'referrer' && isset($event->share_url))
                                        <div class="mt-2">
                                            <button onclick="copyToClipboard('{{ $event->share_url }}')" 
                                                    class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800 hover:bg-blue-200">
                                                <i class="fas fa-share mr-1"></i> Copier le lien
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">Aucun événement à venir</p>
            @endif
        </div>
    </div>

    <!-- Inscriptions récentes pour apporteur -->
    @if($userRole === 'referrer' && isset($roleSpecificData['recent_registrations']) && $roleSpecificData['recent_registrations']->count() > 0)
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">
            <i class="fas fa-users text-gray-500 mr-2"></i>
            Inscriptions récentes
        </h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Participant</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Événement</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Commission</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($roleSpecificData['recent_registrations'] as $registration)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($registration->created_at)->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $registration->fullname }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $registration->event_title }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-green-600">
                            {{ number_format($registration->commission_amount, 0, ',', ' ') }} FCFA
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if($registration->commission_status === 'paid')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Payé
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    En attente
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Lien copié dans le presse-papiers !');
    }, function(err) {
        console.error('Erreur lors de la copie:', err);
        // Fallback pour les navigateurs plus anciens
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        alert('Lien copié dans le presse-papiers !');
    });
}
</script>
@endpush
@endsection
