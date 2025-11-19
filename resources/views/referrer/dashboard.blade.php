<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - {{ $referrer->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        .stat-card.success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        .stat-card.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .stat-card.info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        .event-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: box-shadow 0.3s;
        }
        .event-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .share-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">{{ $referrer->name }} - Dashboard</span>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Notifications -->
        @if($notifications->count() > 0)
        <div class="alert alert-info">
            <h5><i class="fas fa-bell"></i> Notifications ({{ $notifications->count() }})</h5>
            <ul class="mb-0">
                @foreach($notifications as $notification)
                <li>{{ $notification->title }} - {{ $notification->message }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Statistiques -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card info">
                    <h6>Inscriptions</h6>
                    <h2>{{ $stats['total_registrations'] }}</h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card success">
                    <h6>Gains totaux</h6>
                    <h2>{{ number_format($stats['total_earnings'], 0, ',', ' ') }} FCFA</h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card warning">
                    <h6>En attente</h6>
                    <h2>{{ number_format($stats['pending_earnings'], 0, ',', ' ') }} FCFA</h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h6>Événements</h6>
                    <h2>{{ $stats['total_events'] }}</h2>
                </div>
            </div>
        </div>

        <!-- Événements -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Événements disponibles</h5>
            </div>
            <div class="card-body">
                @forelse($events as $event)
                <div class="event-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5>{{ $event->event_title }}</h5>
                            <p class="text-muted mb-2">
                                <i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($event->event_date)->format('d/m/Y') }}
                                <i class="fas fa-map-marker-alt ms-3"></i> {{ $event->event_location }}
                            </p>
                        </div>
                        <div>
                            <button class="share-btn" onclick="copyToClipboard('{{ $event->share_url }}')">
                                <i class="fas fa-share"></i> Copier le lien
                            </button>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">Lien de partage:</small>
                        <input type="text" class="form-control form-control-sm" value="{{ $event->share_url }}" readonly>
                    </div>
                </div>
                @empty
                <p class="text-muted">Aucun événement disponible pour le moment.</p>
                @endforelse
            </div>
        </div>

        <!-- Inscriptions récentes -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Inscriptions récentes</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Participant</th>
                                <th>Événement</th>
                                <th>Commission</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($registrations as $registration)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($registration->created_at)->format('d/m/Y H:i') }}</td>
                                <td>{{ $registration->fullname }}</td>
                                <td>{{ $registration->event_title }}</td>
                                <td>{{ number_format($registration->commission_amount, 0, ',', ' ') }} FCFA</td>
                                <td>
                                    @if($registration->commission_status === 'paid')
                                        <span class="badge bg-success">Payé</span>
                                    @else
                                        <span class="badge bg-warning">En attente</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Aucune inscription pour le moment.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Lien copié dans le presse-papiers !');
            });
        }
    </script>
</body>
</html>

