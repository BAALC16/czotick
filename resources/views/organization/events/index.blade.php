@extends('organization.layouts.app')

@section('title', 'Liste des événements')

@push('styles')
<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .event-card {
        background: white;
        border-radius: 0.5rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        margin-bottom: 1rem;
    }

    .event-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .event-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-published {
        background: #dcfce7;
        color: #166534;
    }

    .badge-draft {
        background: #f3f4f6;
        color: #374151;
    }

    .badge-upcoming {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-past {
        background: #fee2e2;
        color: #991b1b;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Liste des événements</h1>
            <p class="mt-1 text-sm text-gray-500">Gérez tous vos événements</p>
        </div>
        <a href="{{ route('org.events.create', ['org_slug' => $orgSlug]) }}"
           class="bg-primary-custom text-white px-4 py-2 rounded-lg hover:opacity-90 transition">
            <i class="fas fa-plus mr-2"></i>Créer un événement
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Total</div>
            <div class="text-2xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Publiés</div>
            <div class="text-2xl font-bold text-green-600">{{ $stats['published'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Brouillons</div>
            <div class="text-2xl font-bold text-gray-600">{{ $stats['draft'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">À venir</div>
            <div class="text-2xl font-bold text-blue-600">{{ $stats['upcoming'] ?? 0 }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4">
        <form id="searchForm" class="space-y-4">
            <div class="flex gap-4 flex-wrap items-end">
                <div class="flex-1 min-w-64">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                    <input type="text"
                           id="searchInput"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Rechercher un événement..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-custom focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select id="statusSelect" name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-custom">
                        <option value="">Tous les statuts</option>
                        <option value="published" {{ $status === 'published' ? 'selected' : '' }}>Publiés</option>
                        <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Brouillons</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de l'événement</label>
                    <input type="text"
                           id="dateRange"
                           name="date_range"
                           placeholder="Sélectionner une date ou une période"
                           class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-custom focus:border-transparent"
                           style="min-width: 250px;">
                    <input type="hidden" id="date_from" name="date_from" value="{{ $dateFrom ?? '' }}">
                    <input type="hidden" id="date_to" name="date_to" value="{{ $dateTo ?? '' }}">
                </div>

            </div>

            <div class="flex gap-2">
                <button type="button" id="searchBtn" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                    <i class="fas fa-search mr-2"></i>Filtrer
                </button>
                <button type="button" id="resetBtn" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition" style="display: none;">
                    <i class="fas fa-times mr-2"></i>Réinitialiser
                </button>
            </div>
        </form>
    </div>

    <!-- Events List -->
    <div class="bg-white rounded-lg shadow">
        <div id="eventsList">
            @if($events->count() > 0)
                <div class="divide-y divide-gray-200" id="eventsContainer">
                    @foreach($events as $event)
                        @php
                            $eventDate = $event->event_date ? Carbon\Carbon::parse($event->event_date) : null;
                            $isPast = $eventDate && $eventDate->isPast();
                            $isUpcoming = $eventDate && $eventDate->isFuture();
                        @endphp
                        <div class="event-card">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $event->event_title }}</h3>
                                        @if($event->is_published)
                                            <span class="event-badge badge-published">
                                                <i class="fas fa-check-circle mr-1"></i>Publié
                                            </span>
                                        @else
                                            <span class="event-badge badge-draft">
                                                <i class="fas fa-file-alt mr-1"></i>Brouillon
                                            </span>
                                        @endif
                                        @if($isUpcoming)
                                            <span class="event-badge badge-upcoming">
                                                <i class="fas fa-calendar-alt mr-1"></i>À venir
                                            </span>
                                        @elseif($isPast)
                                            <span class="event-badge badge-past">
                                                <i class="fas fa-history mr-1"></i>Passé
                                            </span>
                                        @endif
                                    </div>

                                    @if($event->event_description)
                                        <p class="text-gray-600 mb-3 line-clamp-2">{{ \Illuminate\Support\Str::limit($event->event_description, 150) }}</p>
                                    @endif

                                    <div class="flex flex-wrap gap-4 text-sm text-gray-500">
                                        @if($eventDate)
                                            <div>
                                                <i class="fas fa-calendar mr-1"></i>
                                                {{ $eventDate->format('d/m/Y') }}
                                            </div>
                                        @endif
                                        @if($event->event_location)
                                            <div>
                                                <i class="fas fa-map-marker-alt mr-1"></i>
                                                {{ $event->event_location }}
                                            </div>
                                        @endif
                                        @if($event->max_participants)
                                            <div>
                                                <i class="fas fa-users mr-1"></i>
                                                Max: {{ $event->max_participants }} participants
                                            </div>
                                        @endif
                                        @if($event->current_participants)
                                            <div>
                                                <i class="fas fa-user-check mr-1"></i>
                                                {{ $event->current_participants }} inscrits
                                            </div>
                                        @endif
                                    </div>
                                    
                                    @if($event->is_published && isset($event->event_slug) && $event->event_slug)
                                    <div class="mt-3">
                                        <a href="{{ url('/' . $orgSlug . '/' . $event->event_slug) }}" 
                                           target="_blank"
                                           class="inline-flex items-center gap-2 px-3 py-1 text-sm bg-green-100 text-green-700 rounded hover:bg-green-200 transition">
                                            <i class="fas fa-link mr-1"></i>
                                            Lien d'inscription
                                        </a>
                                    </div>
                                    @endif
                                </div>

                                <div class="flex gap-2 ml-4">
                                    <a href="{{ route('org.events.show', ['org_slug' => $orgSlug, 'event' => $event->id]) }}"
                                       class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition">
                                        <i class="fas fa-eye mr-1"></i>Voir
                                    </a>
                                    <a href="{{ route('org.events.edit', ['org_slug' => $orgSlug, 'event' => $event->id]) }}"
                                       class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition">
                                        <i class="fas fa-edit mr-1"></i>Modifier
                                    </a>
                                    @php
                                        $hasRegistrations = DB::connection('tenant')
                                            ->table('registrations')
                                            ->where('event_id', $event->id)
                                            ->exists();
                                    @endphp
                                    @if(!$hasRegistrations)
                                        <form action="{{ route('org.events.destroy', ['org_slug' => $orgSlug, 'event' => $event->id]) }}"
                                              method="POST"
                                              class="inline"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="px-3 py-1 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200 transition">
                                                <i class="fas fa-trash mr-1"></i>Supprimer
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="p-4" id="paginationContainer">
                    {{ $events->appends(request()->query())->links() }}
                </div>
            @else
                <div class="p-12 text-center" id="emptyState">
                    <i class="fas fa-calendar-times text-gray-300 text-6xl mb-4"></i>
                    <p class="text-gray-500 text-lg mb-2">Aucun événement trouvé</p>
                    <p class="text-gray-400 text-sm mb-6" id="emptyMessage">
                        Commencez par créer votre premier événement.
                    </p>
                    <a href="{{ route('org.events.create', ['org_slug' => $orgSlug]) }}"
                       class="inline-block bg-primary-custom text-white px-6 py-3 rounded-lg hover:opacity-90 transition">
                        <i class="fas fa-plus mr-2"></i>Créer un événement
                    </a>
                </div>
            @endif
        </div>

        <!-- Loading Indicator -->
        <div id="loadingIndicator" class="hidden p-12 text-center">
            <i class="fas fa-spinner fa-spin text-primary-custom text-4xl mb-4"></i>
            <p class="text-gray-500">Chargement...</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.getElementById('dateRange');
        const dateFromInput = document.getElementById('date_from');
        const dateToInput = document.getElementById('date_to');
        const searchForm = document.getElementById('searchForm');
        const searchBtn = document.getElementById('searchBtn');
        const resetBtn = document.getElementById('resetBtn');
        const searchInput = document.getElementById('searchInput');
        const statusSelect = document.getElementById('statusSelect');

        let dateFromValue = dateFromInput.value;
        let dateToValue = dateToInput.value;
        let searchTimeout;

        // Configuration de flatpickr
        const fp = flatpickr(dateInput, {
            locale: "fr",
            mode: "range",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d/m/Y",
            allowInput: false,
            defaultDate: dateFromValue && dateToValue ? [dateFromValue, dateToValue] : (dateFromValue ? dateFromValue : null),
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 1) {
                    dateFromInput.value = selectedDates[0].toISOString().split('T')[0];
                    dateToInput.value = '';
                } else if (selectedDates.length === 2) {
                    dateFromInput.value = selectedDates[0].toISOString().split('T')[0];
                    dateToInput.value = selectedDates[1].toISOString().split('T')[0];
                } else {
                    dateFromInput.value = '';
                    dateToInput.value = '';
                }
                checkFilters();
            },
            onClose: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 1 && !dateToInput.value) {
                    dateToInput.value = selectedDates[0].toISOString().split('T')[0];
                }
                checkFilters();
            }
        });

        // Initialiser avec les valeurs existantes
        if (dateFromValue && dateToValue) {
            if (dateFromValue === dateToValue) {
                fp.setDate(dateFromValue, false);
            } else {
                fp.setDate([dateFromValue, dateToValue], false);
            }
        } else if (dateFromValue) {
            fp.setDate(dateFromValue, false);
        }

        // Fonction pour vérifier si des filtres sont actifs
        function checkFilters() {
            const hasFilters = searchInput.value || statusSelect.value || dateFromInput.value || dateToInput.value;
            resetBtn.style.display = hasFilters ? 'block' : 'none';
        }

        // Initialiser l'état du bouton reset
        checkFilters();

        // Fonction de recherche AJAX
        function performSearch(page = 1) {
            const loadingIndicator = document.getElementById('loadingIndicator');
            const eventsList = document.getElementById('eventsList');

            loadingIndicator.classList.remove('hidden');
            eventsList.classList.add('hidden');

            const params = new URLSearchParams();
            params.append('search', searchInput.value);
            params.append('status', statusSelect.value);
            params.append('date_from', dateFromInput.value);
            params.append('date_to', dateToInput.value);
            params.append('sort', 'event_date'); // Tri par défaut: date de l'événement
            params.append('order', 'desc'); // Ordre par défaut: décroissant
            params.append('page', page);

            fetch(`{{ route('org.events.search', ['org_slug' => $orgSlug]) }}?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                loadingIndicator.classList.add('hidden');
                eventsList.classList.remove('hidden');

                if (data.success) {
                    renderEvents(data.events, data.pagination);
                } else {
                    showError('Erreur lors de la recherche');
                }
            })
            .catch(error => {
                loadingIndicator.classList.add('hidden');
                eventsList.classList.remove('hidden');
                console.error('Error:', error);
                showError('Erreur lors de la recherche');
            });
        }

        // Fonction pour échapper le HTML
        function escapeHtml(text) {
            if (!text) return '';
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }

        // Fonction pour rendre les événements
        function renderEvents(events, pagination) {
            const container = document.getElementById('eventsContainer');
            const paginationContainer = document.getElementById('paginationContainer');
            const emptyState = document.getElementById('emptyState');

            if (events.length === 0) {
                if (container) container.style.display = 'none';
                if (paginationContainer) paginationContainer.style.display = 'none';
                if (emptyState) {
                    emptyState.style.display = 'block';
                    const emptyMessage = document.getElementById('emptyMessage');
                    if (emptyMessage) {
                        const hasFilters = searchInput.value || statusSelect.value || dateFromInput.value;
                        emptyMessage.textContent = hasFilters
                            ? 'Aucun événement ne correspond à vos critères de recherche.'
                            : 'Commencez par créer votre premier événement.';
                    }
                }
                return;
            }

            if (emptyState) emptyState.style.display = 'none';

            // Rendre les événements
            const orgSlug = '{{ $orgSlug }}';
            let html = '<div class="divide-y divide-gray-200">';
            events.forEach(event => {
                const badgeHtml = event.is_published
                    ? '<span class="event-badge badge-published"><i class="fas fa-check-circle mr-1"></i>Publié</span>'
                    : '<span class="event-badge badge-draft"><i class="fas fa-file-alt mr-1"></i>Brouillon</span>';

                let dateBadgeHtml = '';
                if (event.is_upcoming) {
                    dateBadgeHtml = '<span class="event-badge badge-upcoming"><i class="fas fa-calendar-alt mr-1"></i>À venir</span>';
                } else if (event.is_past) {
                    dateBadgeHtml = '<span class="event-badge badge-past"><i class="fas fa-history mr-1"></i>Passé</span>';
                }

                const description = event.event_description
                    ? `<p class="text-gray-600 mb-3 line-clamp-2">${escapeHtml(event.event_description.substring(0, 150))}${event.event_description.length > 150 ? '...' : ''}</p>`
                    : '';

                html += `
                    <div class="event-card">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-lg font-semibold text-gray-900">${escapeHtml(event.event_title)}</h3>
                                    ${badgeHtml}
                                    ${dateBadgeHtml}
                                </div>
                                ${description}
                                <div class="flex flex-wrap gap-4 text-sm text-gray-500">
                                    ${event.event_date ? `<div><i class="fas fa-calendar mr-1"></i>${escapeHtml(event.event_date)}</div>` : ''}
                                    ${event.event_location ? `<div><i class="fas fa-map-marker-alt mr-1"></i>${escapeHtml(event.event_location)}</div>` : ''}
                                    ${event.max_participants ? `<div><i class="fas fa-users mr-1"></i>Max: ${event.max_participants} participants</div>` : ''}
                                    ${event.current_participants ? `<div><i class="fas fa-user-check mr-1"></i>${event.current_participants} inscrits</div>` : ''}
                                </div>
                                ${event.is_published && event.event_slug ? `
                                    <div class="mt-3">
                                        <a href="/${orgSlug}/${event.event_slug}" 
                                           target="_blank"
                                           class="inline-flex items-center gap-2 px-3 py-1 text-sm bg-green-100 text-green-700 rounded hover:bg-green-200 transition">
                                            <i class="fas fa-link mr-1"></i>
                                            Lien d'inscription
                                        </a>
                                    </div>
                                ` : ''}
                            </div>
                            <div class="flex gap-2 ml-4">
                                <a href="/org/${orgSlug}/events/${event.id}" class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition">
                                    <i class="fas fa-eye mr-1"></i>Voir
                                </a>
                                <a href="/org/${orgSlug}/events/${event.id}/edit" class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition">
                                    <i class="fas fa-edit mr-1"></i>Modifier
                                </a>
                                ${!event.has_registrations ? `
                                    <form action="/org/${orgSlug}/events/${event.id}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?');">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <button type="submit" class="px-3 py-1 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200 transition">
                                            <i class="fas fa-trash mr-1"></i>Supprimer
                                        </button>
                                    </form>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';

            if (container) {
                container.innerHTML = html;
                container.style.display = 'block';
            } else {
                const newContainer = document.createElement('div');
                newContainer.id = 'eventsContainer';
                newContainer.className = 'divide-y divide-gray-200';
                newContainer.innerHTML = html;
                eventsList.insertBefore(newContainer, paginationContainer);
            }

            // Rendre la pagination
            if (paginationContainer) {
                paginationContainer.style.display = 'block';
                let paginationHtml = '<div class="flex justify-center items-center gap-2">';

                if (pagination.current_page > 1) {
                    paginationHtml += `<button onclick="performSearch(${pagination.current_page - 1})" class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">Précédent</button>`;
                }

                for (let i = 1; i <= pagination.last_page; i++) {
                    if (i === 1 || i === pagination.last_page || (i >= pagination.current_page - 2 && i <= pagination.current_page + 2)) {
                        paginationHtml += `<button onclick="performSearch(${i})" class="px-3 py-1 border border-gray-300 rounded ${i === pagination.current_page ? 'bg-primary-custom text-white' : 'hover:bg-gray-50'}">${i}</button>`;
                    } else if (i === pagination.current_page - 3 || i === pagination.current_page + 3) {
                        paginationHtml += '<span class="px-3 py-1">...</span>';
                    }
                }

                if (pagination.current_page < pagination.last_page) {
                    paginationHtml += `<button onclick="performSearch(${pagination.current_page + 1})" class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">Suivant</button>`;
                }

                paginationHtml += '</div>';
                paginationContainer.innerHTML = paginationHtml;
            }
        }

        // Fonction pour afficher une erreur
        function showError(message) {
            const container = document.getElementById('eventsContainer');
            if (container) {
                container.innerHTML = `<div class="p-12 text-center text-red-500">${message}</div>`;
            }
        }

        // Écouter les changements de formulaire
        searchBtn.addEventListener('click', function(e) {
            e.preventDefault();
            performSearch();
        });

        resetBtn.addEventListener('click', function(e) {
            e.preventDefault();
            searchInput.value = '';
            statusSelect.value = '';
            dateFromInput.value = '';
            dateToInput.value = '';
            fp.clear();
            checkFilters();
            performSearch();
        });

        // Recherche en temps réel avec debounce (optionnel)
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            checkFilters();
            // Décommenter pour recherche en temps réel
            // searchTimeout = setTimeout(() => performSearch(), 500);
        });

        // Écouter les changements de select
        statusSelect.addEventListener('change', checkFilters);

        // Exposer performSearch globalement pour la pagination
        window.performSearch = performSearch;
    });
</script>
@endpush
