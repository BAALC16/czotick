@extends('organization.layouts.app')

@section('title', 'Créer un collaborateur')

@push('styles')
<style>
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
            <h1 class="text-3xl font-bold text-gray-900">Créer un collaborateur</h1>
            <p class="mt-1 text-sm text-gray-500">Ajoutez un nouveau collaborateur à votre organisation</p>
        </div>
        <a href="{{ route('org.collaborateurs.index', ['org_slug' => $orgSlug]) }}"
           class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition">
            <i class="fas fa-arrow-left mr-2"></i>Retour
        </a>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Informations du collaborateur</h2>
        </div>
        <div class="p-6">
            <form action="{{ route('org.collaborateurs.store', ['org_slug' => $orgSlug]) }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nom complet <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}" 
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-custom focus:border-transparent @error('name') border-red-500 @enderror"
                           placeholder="Entrez le nom complet">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-custom focus:border-transparent @error('email') border-red-500 @enderror"
                           placeholder="email@example.com">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Téléphone
                    </label>
                    <input type="text" 
                           id="phone" 
                           name="phone" 
                           value="{{ old('phone') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-custom focus:border-transparent @error('phone') border-red-500 @enderror"
                           placeholder="+225 XX XX XX XX XX">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Notes
                    </label>
                    <textarea id="notes" 
                              name="notes" 
                              rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-custom focus:border-transparent @error('notes') border-red-500 @enderror"
                              placeholder="Notes supplémentaires sur le collaborateur...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('org.collaborateurs.index', ['org_slug' => $orgSlug]) }}" 
                       class="inline-flex items-center px-6 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-2 bg-primary-custom text-white font-semibold rounded-lg hover:opacity-90 transition shadow-md hover:shadow-lg">
                        <i class="fas fa-save mr-2"></i>Créer le collaborateur
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
