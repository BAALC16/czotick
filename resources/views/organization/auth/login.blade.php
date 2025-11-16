<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}">
    <link rel="icon" href="{{ asset('assets/images/favicon-32x32.png') }}" sizes="32x32">
    <link rel="apple-touch-icon" href="{{ asset('assets/images/apple-touch-icon.png') }}">
    <title>Connexion - 
        @if(isset($organization))
            {{ $organization->org_name }}
        @else
            Czotick Platform
        @endif
    </title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1113a5',
                        secondary: '#f99b4f'
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.5s ease-out'
                    }
                }
            }
        }
    </script>
    
    <style>
        :root {
            --primary-color: #1113a5;
            --secondary-color: #f99b4f;
            --primary-rgb: 17, 19, 165;
            --secondary-rgb: 249, 155, 79;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        .gradient-bg {
            background: var(--primary-color);
            position: relative;
            overflow: hidden;
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }
        
        .btn-gradient {
            background: var(--secondary-color);
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(var(--secondary-rgb), 0.4);
        }
        
        .btn-gradient:hover {
            background: var(--secondary-color);
            opacity: 0.9;
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(var(--secondary-rgb), 0.5);
        }
        
        .btn-gradient:active {
            transform: translateY(-1px);
        }
        
        .input-focus:focus {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.15);
            transform: translateY(-1px);
        }
        
        .input-focus {
            transition: all 0.3s ease;
        }
        
        .input-focus:hover {
            border-color: rgba(var(--primary-rgb), 0.5);
        }
        
        .text-primary-custom {
            color: var(--primary-color);
        }
        
        .border-primary-custom {
            border-color: var(--primary-color);
        }
        
        .bg-primary-custom {
            background-color: var(--primary-color);
        }
        
        .logo-container {
            width: 120px;
            height: 120px;
            border-radius: 20px;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 30px rgba(var(--primary-rgb), 0.4);
            transition: transform 0.3s ease;
        }
        
        .logo-container:hover {
            transform: scale(1.05);
        }
        
        .org-name-display {
            display: inline-block;
            padding: 1rem 2rem;
            border: 2px solid #9ca3af;
            background: white;
            color: #111827;
            font-weight: 700;
            font-size: 1.25rem;
            text-align: center;
            margin: 0 auto 1.5rem;
            border-radius: 0.5rem;
            word-break: break-word;
        }
        
        .logo-wrapper {
            display: inline-block;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(var(--primary-rgb), 0.2);
            transition: all 0.3s ease;
        }
        
        .logo-wrapper:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 35px rgba(var(--primary-rgb), 0.3);
        }
        
        .logo-image {
            max-width: 150px;
            max-height: 80px;
            width: auto;
            height: auto;
            object-fit: contain;
        }
        
        .organization-badge {
            background: var(--primary-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: inline-block;
            box-shadow: 0 4px 10px rgba(var(--primary-rgb), 0.2);
        }
        
        .decorative-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(var(--secondary-rgb), 0.15);
            animation: pulse 4s infinite;
            filter: blur(40px);
        }
        
        .decorative-circle-1 {
            width: 100px;
            height: 100px;
            top: 10%;
            left: 10%;
        }
        
        .decorative-circle-2 {
            width: 150px;
            height: 150px;
            top: 60%;
            right: 10%;
            animation-delay: 1s;
        }
        
        .decorative-circle-3 {
            width: 80px;
            height: 80px;
            bottom: 20%;
            left: 20%;
            animation-delay: 2s;
        }
        
        .shake {
            animation: shake 0.5s ease-in-out;
        }
        
        /* Classes utilitaires pour la validation */
        .border-success {
            border-color: #10b981 !important;
        }
        
        .border-error {
            border-color: #ef4444 !important;
        }
        
        /* Style pour le checkbox personnalisé */
        input[type="checkbox"]:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        input[type="checkbox"]:focus {
            box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.1);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center font-sans relative overflow-hidden">
    <!-- Éléments décoratifs en arrière-plan -->
    <div class="decorative-circle decorative-circle-1"></div>
    <div class="decorative-circle decorative-circle-2"></div>
    <div class="decorative-circle decorative-circle-3"></div>
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-md mx-auto">
            <!-- Carte de connexion principale -->
            <div class="glass-effect rounded-3xl shadow-2xl animate-fade-in" id="loginCard">
                <!-- En-tête avec logo -->
                <div class="text-center px-8 pt-8 pb-6">
                    <!-- Logo de l'organisation -->
                    @if(isset($organization) && $organization->organization_logo && $organization->organization_logo !== 'default-logo.png')
                        <div class="mb-6">
                            <div class="logo-wrapper">
                                <img src="{{ url('public/' . $organization->organization_logo) }}" 
                                     alt="{{ $organization->org_name ?? 'Logo' }}" 
                                     class="logo-image">
                            </div>
                        </div>
                    @elseif(isset($organization) && $organization->org_name)
                        <h2 class="org-name-display">
                            {{ $organization->org_name }}
                        </h2>
                    @endif
                    
                    <h1 class="text-gray-800 text-3xl font-bold mb-2">
                        Connexion
                    </h1>
                    
                
                <!-- Corps de la carte -->
                <div class="px-8 py-6">
                    <!-- Messages d'erreur et de succès -->
                    @if(session('success'))
                        <div class="bg-green-50 border-l-4 border-green-400 text-green-700 p-4 rounded-r-lg mb-4">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle mr-2"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif
                    
                    @if($errors->any())
                        <div class="bg-red-50 border-l-4 border-red-400 text-red-700 p-4 rounded-r-lg mb-4">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-triangle mr-2 mt-0.5"></i>
                                <div>
                                    @foreach($errors->all() as $error)
                                        <div>{{ $error }}</div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Formulaire de connexion -->
                    <form id="loginForm" method="POST" action="{{ route('org.login.submit', request()->route('org_slug')) }}" class="space-y-4">
                        @csrf
                        
                        <!-- Champ Email -->
                        <div class="relative">
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autofocus
                                   class="w-full px-4 py-3 pl-12 border-2 border-gray-300 rounded-xl input-focus transition-all duration-300 @error('email') border-red-400 @enderror"
                                   placeholder="votre@email.com">
                            <i class="fas fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>     
                        </div>
                        
                        <!-- Champ Mot de passe -->
                        <div class="relative">
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   required
                                   class="w-full px-4 py-3 pl-12 pr-12 border-2 border-gray-300 rounded-xl input-focus transition-all duration-300 @error('password') border-red-400 @enderror"
                                   placeholder="Mot de passe">
                            <i class="fas fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <button type="button" 
                                    onclick="togglePassword()" 
                                    class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors duration-200">
                                <i id="passwordToggleIcon" class="fas fa-eye"></i>
                            </button>
                        </div>
                   
                        <!-- Bouton de connexion -->
                        <button type="submit" 
                                id="loginBtn" 
                                class="w-full py-3 px-4 btn-gradient text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 disabled:opacity-70 disabled:transform-none disabled:shadow-none mt-6">
                            <span id="loginText" class="flex items-center justify-center">
                                <i class="fas fa-sign-in-alt mr-2"></i>Se connecter
                            </span>
                            <span id="loadingSpinner" class="hidden flex items-center justify-center">
                                <i class="fas fa-spinner fa-spin mr-2"></i>Connexion...
                            </span>
                        </button>
                    </form>
                </div>
                
                <!-- Pied de page -->
                <div class="text-center px-8 pb-8 pt-4">
                    <p class="text-gray-500 text-xs">
                        &copy; {{ date('Y') }} 
                        @if(isset($organization))
                            {{ $organization->org_name }} - 
                        @endif
                        Propulsé par <span class="text-primary-custom font-semibold">Czotick Platform</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('passwordToggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Gestion du formulaire de connexion
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('loginBtn');
            const loginText = document.getElementById('loginText');
            const loadingSpinner = document.getElementById('loadingSpinner');
            
            // Afficher le spinner de chargement
            loginText.classList.add('hidden');
            loadingSpinner.classList.remove('hidden');
            btn.disabled = true;
            
            // Si erreur, réactiver le bouton après 5 secondes
            setTimeout(() => {
                if (btn.disabled) {
                    loginText.classList.remove('hidden');
                    loadingSpinner.classList.add('hidden');
                    btn.disabled = false;
                }
            }, 5000);
        });
        
        // Validation en temps réel avec animations
        document.getElementById('email').addEventListener('input', function() {
            const email = this.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            // Retirer toutes les classes de validation
            this.classList.remove('border-gray-300', 'border-success', 'border-error', 'shake');
            
            if (email && !emailRegex.test(email)) {
                this.classList.add('border-error', 'shake');
            } else if (email && emailRegex.test(email)) {
                this.classList.add('border-success');
            } else {
                this.classList.add('border-gray-300');
            }
            
            // Remove shake animation after it completes
            setTimeout(() => {
                this.classList.remove('shake');
            }, 500);
        });
        
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            
            // Retirer toutes les classes de validation
            this.classList.remove('border-gray-300', 'border-success', 'border-error', 'shake');
            
            if (password && password.length < 6) {
                this.classList.add('border-error', 'shake');
            } else if (password && password.length >= 6) {
                this.classList.add('border-success');
            } else {
                this.classList.add('border-gray-300');
            }
            
            setTimeout(() => {
                this.classList.remove('shake');
            }, 500);
        });
        
        // Animation d'entrée au chargement
        window.addEventListener('load', function() {
            const loginCard = document.getElementById('loginCard');
            loginCard.style.opacity = '0';
            loginCard.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                loginCard.style.transition = 'all 0.5s ease';
                loginCard.style.opacity = '1';
                loginCard.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>
</html>