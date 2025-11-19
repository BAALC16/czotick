@extends('organization.layouts.app')

@section('title', 'Collaborateurs')

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
            <h1 class="text-3xl font-bold text-gray-900">Collaborateurs</h1>
            <p class="mt-1 text-sm text-gray-500">Gérez vos collaborateurs et leurs commissions</p>
        </div>
        <a href="{{ route('org.collaborateurs.create', ['org_slug' => $orgSlug]) }}"
           class="inline-flex items-center px-6 py-3 bg-primary-custom text-white font-semibold rounded-lg hover:opacity-90 transition shadow-md hover:shadow-lg">
            <i class="fas fa-plus mr-2"></i>Nouveau collaborateur
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="stat-card">
            <div class="stat-icon bg-blue-100 text-blue-600">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-value">{{ $stats['total'] ?? 0 }}</div>
            <div class="stat-label">Total collaborateurs</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-green-100 text-green-600">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-value">{{ $stats['active'] ?? 0 }}</div>
            <div class="stat-label">Actifs</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-yellow-100 text-yellow-600">
                <i class="fas fa-coins"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['total_earnings'] ?? 0, 0, ',', ' ') }}</div>
            <div class="stat-label">Gains totaux (FCFA)</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-orange-100 text-orange-600">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['pending_earnings'] ?? 0, 0, ',', ' ') }}</div>
            <div class="stat-label">En attente (FCFA)</div>
        </div>
    </div>

    <!-- Liste des collaborateurs -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Liste des collaborateurs</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Téléphone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inscriptions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gains totaux</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($referrers as $referrer)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $referrer->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <code class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-sm font-mono">{{ $referrer->referrer_code }}</code>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $referrer->email ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $referrer->phone ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $referrer->registrations_count ?? 0 }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ number_format($referrer->registrations_sum_commission_amount ?? 0, 0, ',', ' ') }} FCFA
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($referrer->is_active)
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Actif
                                </span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Inactif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end gap-2">
                                @php
                                    $hasRegistrations = ($referrer->registrations_count ?? 0) > 0;
                                @endphp
                                @if(!$hasRegistrations)
                                    <a href="{{ route('org.collaborateurs.edit', ['org_slug' => $orgSlug, 'id' => $referrer->id]) }}"
                                       class="text-blue-600 hover:text-blue-900 p-2 rounded hover:bg-blue-50 transition"
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('org.collaborateurs.destroy', ['org_slug' => $orgSlug, 'id' => $referrer->id]) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce collaborateur ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-red-600 hover:text-red-900 p-2 rounded hover:bg-red-50 transition"
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('org.collaborateurs.show', ['org_slug' => $orgSlug, 'id' => $referrer->id]) }}" 
                                   class="text-blue-600 hover:text-blue-900 p-2 rounded hover:bg-blue-50 transition">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('org.collaborateurs.toggle-status', ['org_slug' => $orgSlug, 'id' => $referrer->id]) }}" 
                                      method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="p-2 rounded transition {{ $referrer->is_active ? 'text-yellow-600 hover:text-yellow-900 hover:bg-yellow-50' : 'text-green-600 hover:text-green-900 hover:bg-green-50' }}">
                                        <i class="fas fa-{{ $referrer->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-users text-4xl mb-4 text-gray-300"></i>
                                <p class="text-lg font-medium mb-2">Aucun collaborateur pour le moment</p>
                                <p class="text-sm mb-4">Commencez par créer votre premier collaborateur</p>
                                <a href="{{ route('org.collaborateurs.create', ['org_slug' => $orgSlug]) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-primary-custom text-white rounded-lg hover:opacity-90 transition">
                                    <i class="fas fa-plus mr-2"></i>Créer le premier collaborateur
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
