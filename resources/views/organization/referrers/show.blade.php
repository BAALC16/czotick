@extends('organization.layouts.app')

@section('title', 'Détails collaborateur')

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
    
    .bg-primary-custom {
        background-color: var(--primary-color, #1113a5);
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $referrer->name }}</h1>
            <p class="mt-1 text-sm text-gray-500">Détails et statistiques du collaborateur</p>
        </div>
        <div class="flex gap-3">
            @php
                $hasRegistrations = ($stats['total_registrations'] ?? 0) > 0;
            @endphp
            @if(!$hasRegistrations)
                <a href="{{ route('org.collaborateurs.edit', ['org_slug' => $orgSlug, 'id' => $referrer->id]) }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-edit mr-2"></i>Modifier
                </a>
                <form action="{{ route('org.collaborateurs.destroy', ['org_slug' => $orgSlug, 'id' => $referrer->id]) }}" 
                      method="POST" 
                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce collaborateur ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition">
                        <i class="fas fa-trash mr-2"></i>Supprimer
                    </button>
                </form>
            @else
                <div class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-500 font-medium rounded-lg cursor-not-allowed" 
                     title="Ce collaborateur ne peut pas être modifié car des inscriptions ont été effectuées via son code">
                    <i class="fas fa-lock mr-2"></i>Modification désactivée
                </div>
            @endif
            <a href="{{ route('org.collaborateurs.index', ['org_slug' => $orgSlug]) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition">
                <i class="fas fa-arrow-left mr-2"></i>Retour
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Informations</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Code collaborateur</label>
                        <div class="mt-1">
                            <code class="px-3 py-2 bg-gray-100 text-gray-800 rounded-lg text-sm font-mono block">{{ $referrer->referrer_code }}</code>
                        </div>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Email</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $referrer->email ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Téléphone</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $referrer->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Statut</label>
                        <div class="mt-1">
                            @if($referrer->is_active)
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Actif
                                </span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Inactif
                                </span>
                            @endif
                        </div>
                    </div>
                    @if($referrer->notes)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Notes</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $referrer->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Statistiques -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Statistiques</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="stat-card">
                            <div class="stat-icon bg-blue-100 text-blue-600">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <div class="stat-value">{{ $stats['total_registrations'] ?? 0 }}</div>
                            <div class="stat-label">Inscriptions</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon bg-yellow-100 text-yellow-600">
                                <i class="fas fa-coins"></i>
                            </div>
                            <div class="stat-value">{{ number_format($stats['total_earnings'] ?? 0, 0, ',', ' ') }}</div>
                            <div class="stat-label">Gains totaux</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon bg-orange-100 text-orange-600">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-value">{{ number_format($stats['pending_earnings'] ?? 0, 0, ',', ' ') }}</div>
                            <div class="stat-label">En attente</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon bg-purple-100 text-purple-600">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <div class="stat-value">{{ $stats['total_events'] ?? 0 }}</div>
                            <div class="stat-label">Événements</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Événements avec commissions -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Événements et commissions</h2>
                </div>
                <div class="p-6">
                    @forelse($eventsWithCommissions as $event)
                        @php
                            $commission = $event->referrerCommissions->first();
                        @endphp
                        <div class="border-b border-gray-200 pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $event->event_title }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">
                                        <i class="fas fa-calendar mr-1"></i>{{ \Carbon\Carbon::parse($event->event_date)->format('d/m/Y') }}
                                        <span class="mx-2">•</span>
                                        <i class="fas fa-map-marker-alt mr-1"></i>{{ $event->event_location }}
                                    </p>
                                    @if($commission)
                                        <div class="mt-3">
                                            <span class="text-sm font-medium text-gray-700">Commission:</span>
                                            <span class="ml-2 px-3 py-1 bg-green-100 text-green-800 rounded-lg font-semibold">
                                                @if($commission->commission_type === 'percentage')
                                                    {{ $commission->commission_rate }}%
                                                @else
                                                    {{ number_format($commission->fixed_amount, 0, ',', ' ') }} FCFA
                                                @endif
                                            </span>
                                        </div>
                                    @else
                                        <div class="mt-3">
                                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-lg text-sm font-medium">
                                                Aucune commission configurée
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <a href="{{ route('org.events.show', ['org_slug' => $orgSlug, 'event' => $event->id]) }}"
                                   class="ml-4 text-blue-600 hover:text-blue-900 p-2 rounded hover:bg-blue-50 transition">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <i class="fas fa-calendar-times text-gray-300 text-5xl mb-4"></i>
                            <p class="text-gray-500 text-lg mb-2">Aucun événement avec commission configurée</p>
                            <p class="text-gray-400 text-sm">Les événements avec commissions attribuées apparaîtront ici</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
