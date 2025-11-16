<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page non trouvée</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2ea551;
            --secondary-color: #427cbc;
            --accent-color: #34d399;
        }
        
        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 50%, var(--accent-color) 100%);
            min-height: 100vh;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .error-404 {
            font-size: 8rem;
            font-weight: 900;
            background: linear-gradient(135deg, #ff6b6b, #ffa500);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: pulse 2s ease-in-out infinite;
            line-height: 1;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            text-decoration: none;
            color: white;
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.25);
        }
        
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-1px);
            text-decoration: none;
            color: white;
        }
        
        .slide-up {
            animation: slideUp 0.8s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .icon-bounce {
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }
        
        .floating-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
            opacity: 0.1;
        }
        
        .shape {
            position: absolute;
            border-radius: 50%;
            background: white;
        }
        
        .shape1 {
            width: 60px;
            height: 60px;
            top: 20%;
            left: 10%;
            animation: float 6s ease-in-out infinite;
        }
        
        .shape2 {
            width: 80px;
            height: 80px;
            top: 60%;
            right: 20%;
            animation: float 8s ease-in-out infinite reverse;
        }
        
        .shape3 {
            width: 40px;
            height: 40px;
            top: 40%;
            left: 70%;
            animation: float 7s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        @media (max-width: 640px) {
            .error-404 {
                font-size: 5rem;
            }
        }
    </style>
</head>
<body class="flex items-center justify-center">
    <!-- Formes flottantes -->
    <div class="floating-shapes">
        <div class="shape shape1"></div>
        <div class="shape shape2"></div>
        <div class="shape shape3"></div>
    </div>

    <div class="max-w-2xl w-full mx-auto px-4">
        <div class="glass-effect rounded-2xl p-8 text-center slide-up">
            
            <!-- Icône d'erreur -->
            <div class="text-white text-6xl mb-6">
                <i class="fas fa-exclamation-triangle icon-bounce"></i>
            </div>
            
            <!-- Code d'erreur 404 -->
            <div class="error-404 mb-6">404</div>
            
            <!-- Titre principal -->
            <h1 class="text-4xl font-bold text-white mb-4">
                Page non trouvée
            </h1>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Raccourcis clavier
            document.addEventListener('keydown', function(e) {
                switch(e.key) {
                    case 'h':
                    case 'H':
                        window.location.href = '{{ url("/") }}';
                        break;
                    case 'Escape':
                        window.history.back();
                        break;
                    case 'r':
                    case 'R':
                        if (e.ctrlKey) return; // Laisser Ctrl+R fonctionner normalement
                        window.location.reload();
                        break;
                }
            });
        });
    </script>
</body>
</html>