<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Administrateur - Czotick Platform</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #2ea551 0%, #427cbc 100%);
            --secondary-gradient: linear-gradient(135deg, #427cbc 0%, #2ea551 100%);
            --accent-gradient: linear-gradient(135deg, #2ea551 0%, #427cbc 100%);
            --success-color: #2ea551;
            --error-color: #ef4444;
            --glass-bg: rgba(255, 255, 255, 0.12);
            --glass-border: rgba(255, 255, 255, 0.2);
        }
        
        body {
            background: var(--primary-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }
        
        /* Animated Background */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }
        
        .bg-animation::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: float 20s ease-in-out infinite;
        }
        
        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
        }
        
        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: floatShapes 15s infinite ease-in-out;
        }
        
        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }
        
        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }
        
        .shape:nth-child(4) {
            width: 100px;
            height: 100px;
            top: 10%;
            right: 30%;
            animation-delay: 6s;
        }
        
        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(30px, -30px) rotate(120deg); }
            66% { transform: translate(-20px, 20px) rotate(240deg); }
        }
        
        @keyframes floatShapes {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        /* Login Card */
        .login-container {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            padding: 3rem 2.5rem;
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 10;
        }
        
        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #427cbc 0%, #2ea551 100%);
            border-radius: 24px 24px 0 0;
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            background: var(--primary-gradient);
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            margin-bottom: 1rem;
            box-shadow: 0 8px 25px rgba(46, 165, 81, 0.3);
            animation: logoFloat 3s ease-in-out infinite;
        }
        
        @keyframes logoFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .brand-title {
            color: white;
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .brand-subtitle {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.95rem;
            font-weight: 400;
        }
        
        /* Form Styling */
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
            margin-bottom: 0.5rem;
            display: block;
            font-size: 0.95rem;
        }
        
        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            color: white;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            font-size: 1rem;
            padding: 1rem;
            height: 58px;
        }
        
        .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.4);
            box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.1);
            color: white;
            outline: none;
        }
        
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }
        
        /* Password Toggle */
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.7);
            cursor: pointer;
            font-size: 1.1rem;
            z-index: 5;
            transition: all 0.3s ease;
        }
        
        .password-toggle:hover {
            color: white;
        }
        
        /* Buttons */
        .btn-login {
            background: linear-gradient(135deg, #427cbc 0%, #2ea551 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            padding: 0.9rem 2rem;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(66, 124, 188, 0.4);
            position: relative;
            overflow: hidden;
        }
        
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(46, 165, 81, 0.6);
            color: white;
        }
        
        .btn-login:hover::before {
            left: 100%;
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .btn-login:disabled {
            opacity: 0.7;
            transform: none;
            cursor: not-allowed;
        }
        
        .btn-forgot {
            background: transparent;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            color: white;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            backdrop-filter: blur(10px);
        }
        
        .btn-forgot:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.5);
            color: white;
            transform: translateY(-1px);
        }
        
        /* Remember Me */
        .form-check {
            margin: 1.5rem 0 2rem 0;
        }
        
        .form-check-input {
            background-color: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 6px;
            width: 1.2rem;
            height: 1.2rem;
        }
        
        .form-check-input:checked {
            background-color: #2ea551;
            border-color: #2ea551;
        }
        
        .form-check-input:focus {
            box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.2);
        }
        
        .form-check-label {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
            margin-left: 0.5rem;
        }
        
        /* Alerts */
        .alert {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fecaca;
            border-radius: 12px;
            backdrop-filter: blur(10px);
            margin-bottom: 1.5rem;
        }
        
        .alert-success {
            background: rgba(16, 185, 129, 0.2);
            border-color: rgba(16, 185, 129, 0.3);
            color: #a7f3d0;
        }
        
        .alert-info {
            background: rgba(59, 130, 246, 0.2);
            border-color: rgba(59, 130, 246, 0.3);
            color: #93c5fd;
        }
        
        /* Loading Animation */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
            border-radius: 24px;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            z-index: 20;
        }
        
        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top: 3px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 1rem;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Security Features */
        .security-info {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 12px;
            padding: 1rem;
            margin-top: 1.5rem;
            backdrop-filter: blur(10px);
        }
        
        .security-info h6 {
            color: #93c5fd;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        
        .security-info small {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.4;
        }
        
        /* Footer */
        .login-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .login-footer p {
            color: rgba(255, 255, 255, 0.7);
            margin: 0;
            font-size: 0.9rem;
        }
        
        /* Responsive */
        @media (max-width: 576px) {
            .login-container {
                margin: 1rem;
                padding: 2rem 1.5rem;
            }
            
            .brand-title {
                font-size: 1.5rem;
            }
            
            .logo {
                width: 70px;
                height: 70px;
                font-size: 1.8rem;
            }
        }
        
        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .form-control {
                background: rgba(0, 0, 0, 0.2);
            }
        }
        
        /* Accessibility */
        .form-control:focus {
            outline: none;
        }
        
        .btn-login:focus,
        .btn-forgot:focus {
            outline: 2px solid rgba(255, 255, 255, 0.5);
            outline-offset: 2px;
        }
        
        /* Additional animations */
        .fade-in {
            animation: fadeIn 0.8s ease-out;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-animation">
        <div class="floating-shapes">
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
        </div>
    </div>
    
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="login-container fade-in">
                    <!-- Loading Overlay -->
                    <div class="loading-overlay" id="loadingOverlay">
                        <div class="loading-spinner"></div>
                        <p class="text-white">Connexion en cours...</p>
                    </div>
                    
                    <!-- Logo and Branding -->
                    <div class="logo-container">
                        <h1 class="brand-title">Czotick</h1>
                        <p class="brand-subtitle">Panneau d'Administration</p>
                    </div>
                    
                    <!-- Alert Messages -->
                    <div id="alertContainer"></div>
                    
                    <!-- Login Form -->
                    <form id="loginForm" novalidate>
                        <div class="form-group">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-2"></i>
                                Adresse Email
                            </label>
                            <input type="email" class="form-control" id="email" placeholder="admin@eventsaas.com" required>
                        </div>
                        
                        <div class="form-group position-relative">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-2"></i>
                                Mot de Passe
                            </label>
                            <input type="password" class="form-control" id="password" placeholder="Votre mot de passe" required>
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                        
                        <button type="submit" class="btn btn-login mb-3">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Se Connecter
                        </button>
                        
                        <div class="text-center">
                            <a href="#" class="btn-forgot" onclick="showForgotPassword()">
                                <i class="fas fa-key me-2"></i>
                                Mot de passe oublié ?
                            </a>
                        </div>
                    </form>
                    
                    <!-- Footer -->
                    <div class="login-footer">
                        <p>&copy; 2025 Czotick Platform. Tous droits réservés.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Form handling
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            if (!validateForm(email, password)) {
                return;
            }
            
            attemptLogin(email, password);
        });
        
        function validateForm(email, password) {
            clearAlerts();
            
            if (!email || !password) {
                showAlert('Veuillez remplir tous les champs', 'error');
                return false;
            }
            
            if (!isValidEmail(email)) {
                showAlert('Veuillez saisir une adresse email valide', 'error');
                return false;
            }
            
            if (password.length < 6) {
                showAlert('Le mot de passe doit contenir au moins 6 caractères', 'error');
                return false;
            }
            
            return true;
        }
        
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
        
        function attemptLogin(email, password) {
            showLoading(true);
        }
        
        function showLoading(show) {
            const overlay = document.getElementById('loadingOverlay');
            const submitBtn = document.querySelector('.btn-login');
            
            if (show) {
                overlay.style.display = 'flex';
                submitBtn.disabled = true;
            } else {
                overlay.style.display = 'none';
                submitBtn.disabled = false;
            }
        }
        
        function showAlert(message, type) {
            const container = document.getElementById('alertContainer');
            const alertClass = type === 'error' ? 'alert' : `alert alert-${type}`;
            
            const alertHTML = `
                <div class="${alertClass} alert-dismissible fade show" role="alert">
                    <i class="fas fa-${type === 'error' ? 'exclamation-triangle' : type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            container.innerHTML = alertHTML;
            
            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                const alert = container.querySelector('.alert');
                if (alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 5000);
        }
        
        function clearAlerts() {
            document.getElementById('alertContainer').innerHTML = '';
        }
        
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.className = 'fas fa-eye-slash';
            } else {
                passwordField.type = 'password';
                toggleIcon.className = 'fas fa-eye';
            }
        }
        
        function showForgotPassword() {
            showAlert('Fonctionnalité de récupération de mot de passe bientôt disponible. Contactez votre administrateur système.', 'info');
        }
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Enter key submits form
            if (e.key === 'Enter' && !e.ctrlKey && !e.altKey) {
                const form = document.getElementById('loginForm');
                if (document.activeElement !== form.querySelector('button[type="submit"]')) {
                    e.preventDefault();
                    form.dispatchEvent(new Event('submit'));
                }
            }
            
            // Escape key clears alerts
            if (e.key === 'Escape') {
                clearAlerts();
            }
        });
        
        // Auto-focus on email field
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('email').focus();
            
            // Show demo credentials after 3 seconds
            setTimeout(() => {
                if (!document.getElementById('email').value) {
                    showAlert('Demo: admin@eventsaas.com / admin123', 'info');
                }
            }, 3000);
        });
        
        // Prevent form resubmission on page reload
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
        
        // Security: Clear form on page unload
        window.addEventListener('beforeunload', function() {
            document.getElementById('loginForm').reset();
        });
        
        // Detect if user is already logged in (simulation)
        // In real app, check JWT token or session
        if (localStorage.getItem('adminToken')) {
            showAlert('Vous êtes déjà connecté. Redirection...', 'success');
            setTimeout(() => {
                window.location.href = '#dashboard';
            }, 1500);
        }
    </script>
</body>
</html>