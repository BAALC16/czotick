<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Organisation - C'zotick</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .pack-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .pack-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .pack-card.selected {
            border-color: #007bff;
            background-color: #f8f9ff;
        }
        .feature-list {
            list-style: none;
            padding: 0;
        }
        .feature-list li {
            padding: 5px 0;
        }
        .feature-list li i {
            color: #28a745;
            margin-right: 8px;
        }
        .loading {
            display: none;
        }
        .step {
            display: none;
        }
        .step.active {
            display: block;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h2><i class="fas fa-building me-2"></i>Créer votre organisation</h2>
                        <p class="mb-0">Rejoignez C'Zotick et gérez vos événements facilement</p>
                    </div>
                    <div class="card-body p-4">
                        <!-- Progress Bar -->
                        <div class="progress mb-4" style="height: 8px;">
                            <div class="progress-bar" role="progressbar" style="width: 33%" id="progressBar"></div>
                        </div>

                        <form id="registrationForm">
                            <!-- Step 1: Informations de base -->
                            <div class="step active" id="step1">
                                <h4 class="mb-4"><i class="fas fa-info-circle text-primary me-2"></i>Informations de base</h4>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="org_name" class="form-label">Nom de l'organisation *</label>
                                        <input type="text" class="form-control" id="org_name" name="org_name" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="org_key" class="form-label">Clé d'organisation *</label>
                                        <input type="text" class="form-control" id="org_key" name="org_key" required>
                                        <div class="form-text">Utilisée pour l'URL de votre organisation (ex: mon-org)</div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="org_type" class="form-label">Type d'organisation *</label>
                                        <select class="form-select" id="org_type" name="org_type" required>
                                            <option value="">Sélectionnez un type</option>
                                            <option value="jci">JCI</option>
                                            <option value="rotary">Rotary</option>
                                            <option value="lions">Lions</option>
                                            <option value="association">Association</option>
                                            <option value="company">Entreprise</option>
                                            <option value="other">Autre</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="subdomain" class="form-label">Sous-domaine</label>
                                        <input type="text" class="form-control" id="subdomain" name="subdomain">
                                        <div class="form-text">Optionnel, généré automatiquement si vide</div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="contact_name" class="form-label">Nom du contact *</label>
                                        <input type="text" class="form-control" id="contact_name" name="contact_name" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="contact_email" class="form-label">Email du contact *</label>
                                        <input type="email" class="form-control" id="contact_email" name="contact_email" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="contact_phone" class="form-label">Téléphone du contact</label>
                                        <input type="tel" class="form-control" id="contact_phone" name="contact_phone">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="custom_domain" class="form-label">Domaine personnalisé</label>
                                        <input type="text" class="form-control" id="custom_domain" name="custom_domain">
                                        <div class="form-text">Optionnel, pour les packs Premium/Custom</div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="button" class="btn btn-primary" onclick="nextStep()">Suivant <i class="fas fa-arrow-right ms-1"></i></button>
                                </div>
                            </div>

                            <!-- Step 2: Choix du pack -->
                            <div class="step" id="step2">
                                <h4 class="mb-4"><i class="fas fa-box text-primary me-2"></i>Choisissez votre pack</h4>
                                <div class="row">
                                    @foreach($packs as $pack)
                                    <div class="col-md-4 mb-4">
                                        <div class="card pack-card h-100" onclick="selectPack('{{ $pack->pack_key }}')">
                                            <div class="card-header text-center">
                                                <h5 class="card-title">{{ $pack->pack_name }}</h5>
                                                <div class="h4 text-primary">{{ $pack->formatted_commission }}</div>
                                                <small class="text-muted">Commission sur les tickets</small>
                                            </div>
                                            <div class="card-body">
                                                <p class="card-text">{{ $pack->pack_description }}</p>
                                                <ul class="feature-list">
                                                    <li><i class="fas fa-check"></i> Tickets par email</li>
                                                    @if($pack->whatsapp_tickets)
                                                    <li><i class="fas fa-check"></i> Tickets WhatsApp</li>
                                                    @endif
                                                    @if($pack->custom_tickets)
                                                    <li><i class="fas fa-check"></i> Design personnalisé</li>
                                                    @endif
                                                    @if($pack->multi_ticket_purchase)
                                                    <li><i class="fas fa-check"></i> Achat multi-tickets</li>
                                                    @endif
                                                    @if($pack->multi_country_support)
                                                    <li><i class="fas fa-check"></i> Support multi-pays</li>
                                                    @endif
                                                    @if($pack->custom_domain)
                                                    <li><i class="fas fa-check"></i> Domaine personnalisé</li>
                                                    @endif
                                                    @if($pack->advanced_analytics)
                                                    <li><i class="fas fa-check"></i> Analytics avancées</li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="text-end">
                                    <button type="button" class="btn btn-secondary me-2" onclick="prevStep()"><i class="fas fa-arrow-left me-1"></i> Précédent</button>
                                    <button type="button" class="btn btn-primary" onclick="nextStep()">Suivant <i class="fas fa-arrow-right ms-1"></i></button>
                                </div>
                            </div>

                            <!-- Step 3: Configuration -->
                            <div class="step" id="step3">
                                <h4 class="mb-4"><i class="fas fa-cogs text-primary me-2"></i>Configuration</h4>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Types d'événements activés</label>
                                        <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                            @foreach($eventTypes as $eventType)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="enabled_event_types[]" value="{{ $eventType->type_key }}" id="event_type_{{ $eventType->id }}" checked>
                                                <label class="form-check-label" for="event_type_{{ $eventType->id }}">
                                                    <i class="{{ $eventType->icon_class }}" style="color: {{ $eventType->formatted_color }}"></i>
                                                    {{ $eventType->type_name }}
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Pays supportés</label>
                                        <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                            @foreach($countries as $country)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="enabled_countries[]" value="{{ $country->country_code }}" id="country_{{ $country->id }}" {{ $country->country_code === 'CI' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="country_{{ $country->id }}">
                                                    {{ $country->flag_emoji }} {{ $country->display_name }}
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="button" class="btn btn-secondary me-2" onclick="prevStep()"><i class="fas fa-arrow-left me-1"></i> Précédent</button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-rocket me-1"></i> Créer l'organisation
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Loading -->
                        <div class="loading text-center py-5" id="loading">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                            <p class="mt-3">Création de votre organisation en cours...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentStep = 1;
        let selectedPack = null;

        function nextStep() {
            if (currentStep < 3) {
                document.getElementById(`step${currentStep}`).classList.remove('active');
                currentStep++;
                document.getElementById(`step${currentStep}`).classList.add('active');
                updateProgressBar();
            }
        }

        function prevStep() {
            if (currentStep > 1) {
                document.getElementById(`step${currentStep}`).classList.remove('active');
                currentStep--;
                document.getElementById(`step${currentStep}`).classList.add('active');
                updateProgressBar();
            }
        }

        function updateProgressBar() {
            const progress = (currentStep / 3) * 100;
            document.getElementById('progressBar').style.width = progress + '%';
        }

        function selectPack(packKey) {
            selectedPack = packKey;
            document.querySelectorAll('.pack-card').forEach(card => {
                card.classList.remove('selected');
            });
            event.currentTarget.classList.add('selected');
        }

        // Auto-generate subdomain
        document.getElementById('org_key').addEventListener('input', function() {
            const subdomain = this.value.toLowerCase().replace(/[^a-z0-9-]/g, '-');
            document.getElementById('subdomain').value = subdomain;
        });

        // Form submission
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!selectedPack) {
                alert('Veuillez sélectionner un pack');
                return;
            }

            // Show loading
            document.querySelectorAll('.step').forEach(step => step.style.display = 'none');
            document.getElementById('loading').style.display = 'block';

            // Prepare form data
            const formData = new FormData(this);
            formData.append('subscription_pack', selectedPack);

            // Submit form
            fetch('{{ route("organization.register") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Organisation créée avec succès !');
                    window.location.href = data.redirect_url;
                } else {
                    alert('Erreur: ' + data.message);
                    document.getElementById('loading').style.display = 'none';
                    document.getElementById('step3').style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Une erreur est survenue');
                document.getElementById('loading').style.display = 'none';
                document.getElementById('step3').style.display = 'block';
            });
        });
    </script>
</body>
</html>
