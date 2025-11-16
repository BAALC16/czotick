<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - C'zotick Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#e6e6f5',
                            100: '#cccceb',
                            500: '#1113a5',
                            600: '#0e0f84',
                            700: '#0b0c63',
                        },
                        secondary: {
                            500: '#f99b4f',
                            600: '#e8883a',
                            700: '#d6762f',
                        }
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

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-section {
            animation: fadeInUp 0.6s ease-out;
        }

        .input-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .input-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #374151;
            font-size: 0.875rem;
            letter-spacing: 0.025em;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            z-index: 10;
            color: var(--primary-color);
            transition: all 0.3s ease;
        }

        .input-focus {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            font-size: 0.9375rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background-color: #ffffff;
            color: #111827;
        }

        .input-focus::placeholder {
            color: #9ca3af;
        }

        .input-focus:hover {
            border-color: rgba(var(--primary-rgb), 0.5);
            box-shadow: 0 2px 8px rgba(var(--primary-rgb), 0.1);
        }

        .input-focus:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(var(--primary-rgb), 0.1), 0 4px 12px rgba(var(--primary-rgb), 0.15);
            transform: translateY(-1px);
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--secondary-color) 0%, #e8883a 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 0.5rem 1.5rem rgba(var(--secondary-rgb), 0.4);
            position: relative;
            color: white !important;
            overflow: hidden;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-size: 0.875rem;
        }

        .btn-primary-custom::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .btn-primary-custom:hover::before {
            left: 100%;
        }

        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(var(--secondary-rgb), 0.5);
        }

        .btn-primary-custom:active {
            transform: translateY(-1px);
        }

        .form-card {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.05);
            padding: 2.5rem;
            backdrop-filter: blur(10px);
        }

        @media (max-width: 640px) {
            .form-card {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body class="min-h-screen" style="background-color: #1113a5;">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <div class="mx-auto h-24 w-24 bg-white rounded-2xl flex items-center justify-center shadow-xl mb-4 p-3">
                    <img src="{{ asset('assets/images/logo-czotick.png') }}" alt="Logo" class="w-full h-full object-contain rounded-lg">
                </div>
                <h2 class="mt-6 text-3xl font-extrabold text-white">
                    Connexion à votre organisation
                </h2>
                <p class="mt-2 text-sm text-white opacity-90">
                    Entrez l'identifiant de votre organisation pour vous connecter
                </p>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('organization.login.redirect') }}" class="mt-8 form-card">
                @csrf
                
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-600 mr-2"></i>
                            <p class="text-sm text-red-600">
                                {{ $errors->first('org_slug') }}
                            </p>
                        </div>
                    </div>
                @endif

                <!-- Organization Slug -->
                <div class="form-section input-group">
                    <label for="org_slug">
                        <i class="fas fa-building mr-1" style="color: #1113a5;"></i>
                        Identifiant de l'organisation *
                    </label>
                    <div class="input-wrapper">
                        <input id="org_slug" name="org_slug" type="text" required
                               class="input-focus"
                               placeholder="Entrez l'identifiant de votre organisation"
                               value="{{ old('org_slug') }}">
                    </div>
                    <p class="mt-2 text-xs text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        L'identifiant est généralement le nom de votre organisation en minuscules (ex: mon-organisation)
                    </p>
                </div>

                <!-- Submit Button -->
                <div class="form-section mt-8">
                    <button type="submit"
                            class="btn-primary-custom group relative w-full flex justify-center items-center py-4 px-6 border border-transparent rounded-xl text-white focus:outline-none">
                        <span>Continuer</span>
                        <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>

                <!-- Register Link -->
                <div class="form-section text-center pt-4 border-t border-gray-200 mt-6">
                    <p class="text-sm text-gray-600">
                        Pas encore de compte ?
                        <a href="{{ route('organization.register.custom.form') }}" class="font-semibold transition-all duration-300 hover:underline" style="color: #1113a5;">
                            Créer une organisation
                            <i class="fas fa-arrow-right ml-1 text-xs"></i>
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

