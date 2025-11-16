<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Authentification Vérificateur - {{ $event->event_title }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: {{ $event->primary_color ?? '#174e4b' }};
            --secondary-color: {{ $event->secondary_color ?? '#2d6a65' }};
        }
        
        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .auth-container {
            width: 100%;
            max-width: 450px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
        }
        
        .auth-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem;
            text-align: center;
            margin: -2rem -2rem 2rem -2rem;
        }
        
        .auth-header h1 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .auth-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 0.9rem;
        }
        
        .form-control {
            border-radius: 12px;
            border: 2px solid #e9ecef;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(23, 78, 75, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 12px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        
        .shield-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: rgba(255, 255, 255, 0.9);
        }
        
        .event-info {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--primary-color);
        }
        
        .error-alert {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        
        .loading-spinner {
            text-align: center;
            color: white;
        }
        
        @media (max-width: 576px) {
            .auth-container {
                padding: 1rem;
            }
            
            .auth-header {
                padding: 1.5rem;
                margin: -1.5rem -1.5rem 1.5rem -1.5rem;
            }
        }
    </style>
</head>
<body>
    
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner">
            <i class="fas fa-spinner fa-spin fa-3x mb-3"></i>
            <p>Authentification en cours...</p>
        </div>
    </div>

    <div class="auth-container">
        <div class="card auth-card border-0">
            <div class="card-body p-4">
                
                <!-- Header -->
                <div class="auth-header">
                    <i class="fas fa-shield-alt shield-icon"></i>
                    <h1>Authentification Vérificateur</h1>
                    <p>Accès sécurisé au système de vérification</p>
                </div>

                <!-- Informations de l'événement -->
                <div class="event-info">
                    <h6 class="mb-2">
                        <i class="fas fa-calendar-alt me-2"></i>{{ $event->event_title }}
                    </h6>
                    <p class="mb-1">
                        <i class="fas fa-building me-2"></i>{{ $organization->org_name }}
                    </p>
                    @if($event->event_date)
                        <p class="mb-1">
                            <i class="fas fa-clock me-2"></i>{{ \Carbon\Carbon::parse($event->event_date)->format('d/m/Y') }}
                        </p>
                    @endif
                    @if($event->event_location)
                        <p class="mb-0">
                            <i class="fas fa-map-marker-alt me-2"></i>{{ $event->event_location }}
                        </p>
                    @endif
                </div>

                <!-- Alertes d'erreur -->
                @if(isset($error))
                    <div class="error-alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>{{ $error }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="error-alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success border-0" style="border-radius: 12px;">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="error-alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('event.verifier.authenticate', ['org_slug' => $org_slug, 'event_slug' => $event_slug]) }}" id="authForm">                    
                    @csrf
                    <div class="mb-3">
                        <label for="access_code" class="form-label fw-semibold">
                            <i class="fas fa-key me-2"></i>Code d'Accès
                        </label>
                        <input type="text" 
                               class="form-control form-control-lg @error('access_code') is-invalid @enderror" 
                               id="access_code" 
                               name="access_code" 
                               placeholder="Entrez votre code d'accès"
                               value="{{ old('access_code') }}"
                               required 
                               autofocus
                               autocomplete="off">
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>Se Connecter
                        </button>
                    </div>
                </form>

                <!-- Instructions -->
                <div class="text-center">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-2"></i>
                        Demandez votre code d'accès à l'organisateur
                    </small>
                </div>

                <!-- Footer -->
                <hr class="mt-4">
                <div class="text-center">
                    <small class="text-muted">
                        <i class="fas fa-shield-alt me-1"></i>
                        Système sécurisé de vérification des tickets
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('authForm');
            const loadingOverlay = document.getElementById('loadingOverlay');
            const accessCodeInput = document.getElementById('access_code');
            
            // Auto-focus sur le champ de saisie
            accessCodeInput.focus();
            
            // Gestion de la soumission du formulaire
            form.addEventListener('submit', function(e) {
                const accessCode = accessCodeInput.value.trim();
                
                if (!accessCode) {
                    e.preventDefault();
                    alert('Veuillez entrer votre code d\'accès');
                    accessCodeInput.focus();
                    return;
                }
                
                // Afficher l'overlay de chargement
                loadingOverlay.style.display = 'flex';
                
                // Désactiver le formulaire
                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Connexion...';
            });
            
            // Transformation automatique en majuscules
            accessCodeInput.addEventListener('input', function(e) {
                e.target.value = e.target.value.toUpperCase();
            });
            
            // Raccourcis clavier
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && e.target !== accessCodeInput) {
                    accessCodeInput.focus();
                }
            });
            
            // Animation d'entrée
            document.querySelector('.auth-card').style.opacity = '0';
            document.querySelector('.auth-card').style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                document.querySelector('.auth-card').style.transition = 'all 0.5s ease';
                document.querySelector('.auth-card').style.opacity = '1';
                document.querySelector('.auth-card').style.transform = 'translateY(0)';
            }, 100);
            
            // Masquer l'overlay de chargement si erreur
            setTimeout(() => {
                loadingOverlay.style.display = 'none';
            }, 100);
        });
        
        // Gestion des erreurs réseau
        window.addEventListener('error', function() {
            document.getElementById('loadingOverlay').style.display = 'none';
        });
    </script>
</body>
</html>