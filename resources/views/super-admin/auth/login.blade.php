{{-- resources/views/super-admin/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connexion Administrateur - {{ config('app.name', 'Czotick Platform') }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #2ea551 0%, #427cbc 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Inter', sans-serif;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 3rem 2rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #2ea551, #427cbc);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: white;
            font-size: 1.5rem;
        }
        
        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            color: white;
            padding: 0.75rem 1rem;
        }
        
        .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.5);
            color: white;
            box-shadow: none;
        }
        
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #427cbc, #2ea551);
            border: none;
            border-radius: 10px;
            padding: 0.75rem;
            font-weight: 600;
            width: 100%;
            transition: transform 0.2s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
        }
        
        .text-white {
            color: white !important;
        }
        
        .alert {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="login-card">
                    <!-- Logo -->
                    <div class="logo">
                        <i class="fas fa-crown"></i>
                    </div>
                    
                    <!-- Titre -->
                    <h3 class="text-white text-center mb-4">{{ config('app.name', 'Czotick') }}</h3>
                    
                    <!-- Messages -->
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif
                    
                    <!-- Formulaire -->
                    <form method="POST" action="{{ route('super-admin.login.submit') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label text-white">Nom d'utilisateur</label>
                            <input type="text" 
                                   class="form-control" 
                                   name="username" 
                                   value="{{ old('username') }}"
                                   placeholder="superadmin" 
                                   required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-white">Mot de passe</label>
                            <input type="password" 
                                   class="form-control" 
                                   name="password" 
                                   placeholder="Votre mot de passe" 
                                   required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-login">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Se connecter
                        </button>
                    </form>
                    
                    <!-- Informations de test -->
                    @if(config('app.env') === 'local')
                    <div class="mt-3 text-center">
                        <small class="text-white opacity-75">
                            Test: superadmin / password
                        </small>
                    </div>
                    @endif
                    
                    <!-- Footer -->
                    <div class="text-center mt-4">
                        <small class="text-white opacity-75">
                            &copy; {{ date('Y') }} {{ config('app.name', 'Czotick') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>