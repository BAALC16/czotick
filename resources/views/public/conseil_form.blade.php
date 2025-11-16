<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}">
    <!-- Pour les écrans haute résolution -->
    <link rel="icon" href="{{ asset('assets/images/favicon-32x32.png') }}" sizes="32x32">
    <!-- Pour iOS -->
    <link rel="apple-touch-icon" href="{{ asset('assets/images/apple-touch-icon.png') }}">

    <title>Conseil national 2025 - Fin des inscriptions</title>
    <style>
        :root {
            --primary-color: #1d86d9;
            --secondary-color: #28a745;
            --dark-blue: #0056b3;
            --warning-color: #ffa800;
            --background-light: #f5f9ff;
            --shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--background-light);
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            width: 100%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Event Info Panel */
        .event-panel {
            flex: 1;
            background-color: #025225;
            color: white;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            background-image: url("{{ asset('assets/images/conseil-national-2025.jpeg') }}");
            background-size: cover;
            background-position: center;
            min-height: 100vh;
        }
        
        .event-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(2, 82, 37, 0.85);
            z-index: 1;
        }
        
        .event-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 800px;
        }
        
        .event-logo {
            max-width: 200px;
            margin-bottom: 2rem;
        }
        
        .event-title {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }
        
        .event-subtitle {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        
        .event-details {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .event-detail {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .detail-icon {
            margin-right: 1rem;
            font-size: 1.2rem;
        }
        
        .end-registration-message {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            padding: 2rem;
            margin-top: 2rem;
            text-align: center;
            font-size: 1.8rem;
            font-weight: 600;
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.4);
            max-width: 800px;
            width: 100%;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .event-title {
                font-size: 2rem;
            }
            
            .event-subtitle {
                font-size: 1.2rem;
            }
            
            .end-registration-message {
                font-size: 1.5rem;
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Event Information Panel -->
        <div class="event-panel">
            <div class="event-content">
                <img src="{{ asset('assets/images/logo.png') }}" alt="JCI Logo" class="event-logo">
                <h1 class="event-title">Conseil National 2025</h1>
                <h2 class="event-subtitle">Jeune Chambre Internationale Côte d'Ivoire</h2>
                
                <div class="event-details">
                    <div class="event-detail">
                        <span class="detail-icon">📅</span>
                        <span>09-10 Mai 2025</span>
                    </div>
                    <div class="event-detail">
                        <span class="detail-icon">📍</span>
                        <span>Bouaké</span>
                    </div>
                    <div class="event-detail">
                        <span class="detail-icon">🎯</span>
                        <span>Le positionnement de la JCI Côte d'Ivoire, notre priorité.</span>
                    </div>
                    <div class="event-detail">
                        <span class="detail-icon">💰</span>
                        <span>Tarif Past President / Sénateur / Membre / Membre Potentiel / Invité.e : 15.200 FCFA</span>
                    </div>
                    <div class="event-detail">
                        <span class="detail-icon">💰</span>
                        <span>Tarif Membre universitaire : 7.600 FCFA</span>
                    </div>
                </div>
                
                <!-- Message de fin d'inscriptions -->
                <div class="end-registration-message">
                    Fin des inscriptions au Conseil National 2025 de la JCI Côte d'Ivoire
                </div>
                
                <div style="margin-top: 2rem;">
                    <div class="logos-container" style="display: flex; justify-content: center; align-items: center; gap: 2rem; margin: 0 auto;">
                        <img src="{{ asset('assets/images/logo-riseup.png') }}" alt="Logo RiseUp" style="height: 80px;">
                        <img src="{{ asset('assets/images/logo-100ans.png') }}" alt="Logo 100 ans" style="height: 80px;">
                        <img src="{{ asset('assets/images/logo1.png') }}" alt="Logo JCI CI" style="height: 80px;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>