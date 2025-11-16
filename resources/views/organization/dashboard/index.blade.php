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
        <p class="mt-1 text-sm text-gray-500">Vue d'ensemble de votre organisation</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Events -->
        <div class="stat-card">
            <div class="stat-icon bg-blue-100 text-blue-600">
                <i class="fas fa-calendar"></i>
            </div>
            <div class="stat-value">{{ $stats['total_events'] ?? 0 }}</div>
            <div class="stat-label">Événements totaux</div>
        </div>

        <!-- Published Events -->
        <div class="stat-card">
            <div class="stat-icon bg-green-100 text-green-600">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-value">{{ $stats['published_events'] ?? 0 }}</div>
            <div class="stat-label">Événements publiés</div>
        </div>

        <!-- Total Registrations -->
        <div class="stat-card">
            <div class="stat-icon bg-purple-100 text-purple-600">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-value">{{ $stats['total_registrations'] ?? 0 }}</div>
            <div class="stat-label">Inscriptions totales</div>
        </div>

        <!-- Total Revenue -->
        <div class="stat-card">
            <div class="stat-icon bg-orange-100 text-orange-600">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['total_revenue'] ?? 0, 0, ',', ' ') }} FCFA</div>
            <div class="stat-label">Revenus totaux</div>
        </div>
    </div>

    <!-- Additional Statistics -->
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

    <!-- Recent Events and Upcoming Events -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Events -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">
                <i class="fas fa-clock text-gray-500 mr-2"></i>
                Événements récents
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
                                    <div class="mt-2">
                                        @if($event->is_published)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i> Publié
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <i class="fas fa-clock mr-1"></i> Brouillon
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">Aucun événement récent</p>
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
</div>
@endsection
