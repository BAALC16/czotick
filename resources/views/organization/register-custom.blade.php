<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Organisation - C'zotick Platform</title>
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

        select.input-focus {
            padding-right: 2.5rem;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%231113a5'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1.5em 1.5em;
        }

        .form-divider {
            position: relative;
            text-align: center;
            margin: 2rem 0;
        }

        .form-divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(to right, transparent, #e5e7eb, transparent);
        }

        .form-divider span {
            position: relative;
            background: white;
            padding: 0 1rem;
            color: #6b7280;
            font-size: 0.875rem;
            font-weight: 600;
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

        .upload-area {
            position: relative;
            overflow: hidden;
            border-radius: 1rem;
        }

        .upload-area:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(var(--primary-rgb), 0.2);
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
        <div class="max-w-2xl w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <div class="mx-auto h-24 w-24 bg-white rounded-2xl flex items-center justify-center shadow-xl mb-4 p-3">
                    <img src="{{ asset('assets/images/logo-czotick.png') }}" alt="Logo" class="w-full h-full object-contain rounded-lg">
                </div>
                <h2 class="mt-6 text-3xl font-extrabold text-white">
                    Créer votre organisation
                </h2>
                <p class="mt-2 text-sm text-white opacity-90">
                    Rejoignez C'Zotick et gérez vos événements facilement
                </p>
            </div>

            <!-- Form -->
            <form id="registrationForm" class="mt-8 form-card">
                <!-- Logo Upload -->
                <div class="form-section space-y-3 mb-6">
                    <label class="block text-sm font-semibold text-gray-700 uppercase tracking-wide">
                        <i class="fas fa-image mr-2" style="color: #1113a5;"></i>
                        Logo de l'organisation <span class="text-gray-400 font-normal normal-case">(optionnel)</span>
                    </label>
                    <div class="flex items-center justify-center w-full">
                        <label for="logo" class="upload-area flex flex-col items-center justify-center w-full h-40 border-2 border-dashed cursor-pointer transition-all duration-300" style="border-color: #1113a5; background: linear-gradient(135deg, rgba(17, 19, 165, 0.03) 0%, rgba(17, 19, 165, 0.08) 100%);">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <div class="mb-3 p-4 rounded-full" style="background-color: rgba(17, 19, 165, 0.1);">
                                    <i class="fas fa-cloud-upload-alt text-3xl" style="color: #1113a5;"></i>
                                </div>
                                <p class="mb-1 text-sm font-semibold text-gray-700">
                                    Cliquez pour télécharger
                                </p>
                                <p class="text-xs text-gray-500">ou glissez-déposez votre fichier</p>
                                <p class="text-xs text-gray-400 mt-1">PNG, JPG ou GIF (MAX. 2MB)</p>
                            </div>
                            <input id="logo" name="logo" type="file" class="hidden" accept="image/*" />
                        </label>
                    </div>
                    <div id="logoPreview" class="hidden mt-3 text-center">
                        <div class="inline-block p-3 rounded-xl shadow-lg" style="background-color: rgba(17, 19, 165, 0.05);">
                            <img id="previewImage" class="max-h-32 max-w-32 w-auto h-auto object-contain rounded-lg" alt="Preview">
                        </div>
                    </div>
                </div>

                <div class="form-divider">
                    <span>Informations de l'organisation</span>
                </div>

                <!-- Organization Name -->
                <div class="form-section input-group">
                    <label for="org_name">
                        <i class="fas fa-building mr-1" style="color: #1113a5;"></i>
                        Nom de l'organisation *
                    </label>
                    <div class="input-wrapper">
                        <input id="org_name" name="org_name" type="text" required
                               class="input-focus"
                               placeholder="Entrez le nom de votre organisation">
                    </div>
                </div>

                <!-- Organization Type -->
                <div class="form-section input-group">
                    <label for="org_type">
                        <i class="fas fa-tag mr-1" style="color: #1113a5;"></i>
                        Type d'organisation *
                    </label>
                    <div class="input-wrapper">
                        <select id="org_type" name="org_type" required class="input-focus">
                            <option value="">Sélectionnez un type</option>
                            @foreach($organizationTypes ?? [] as $type)
                                <option value="{{ $type->code }}">{{ $type->display_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-divider">
                    <span>Informations de contact</span>
                </div>

                <!-- User Name -->
                <div class="form-section input-group">
                    <label for="contact_name">
                        <i class="fas fa-user mr-1" style="color: #1113a5;"></i>
                        Votre nom complet *
                    </label>
                    <div class="input-wrapper">
                        <input id="contact_name" name="contact_name" type="text" required
                               class="input-focus"
                               placeholder="Entrez votre nom complet">
                    </div>
                </div>

                <!-- Email -->
                <div class="form-section input-group">
                    <label for="contact_email">
                        <i class="fas fa-envelope mr-1" style="color: #1113a5;"></i>
                        Adresse email *
                    </label>
                    <div class="input-wrapper">
                        <input id="contact_email" name="contact_email" type="email" required
                               class="input-focus"
                               placeholder="votre.email@exemple.com">
                    </div>
                </div>

                <!-- Phone -->
                <div class="form-section input-group">
                    <label for="contact_phone">
                        <i class="fas fa-phone mr-1" style="color: #1113a5;"></i>
                        Numéro de téléphone *
                    </label>
                    <div class="input-wrapper">
                        <input id="contact_phone" name="contact_phone" type="tel"
                               class="input-focus"
                               placeholder="+225 XX XX XX XX XX">
                    </div>
                </div>

                <div class="form-divider">
                    <span>Sécurité</span>
                </div>

                <!-- Password -->
                <div class="form-section input-group">
                    <label for="password">
                        <i class="fas fa-lock mr-1" style="color: #1113a5;"></i>
                        Mot de passe *
                    </label>
                    <div class="input-wrapper">
                        <input id="password" name="password" type="password" required
                               class="input-focus"
                               placeholder="Minimum 8 caractères">
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="form-section input-group">
                    <label for="password_confirmation">
                        <i class="fas fa-lock mr-1" style="color: #1113a5;"></i>
                        Confirmer le mot de passe *
                    </label>
                    <div class="input-wrapper">
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                               class="input-focus"
                               placeholder="Confirmez votre mot de passe">
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="form-section mt-8">
                    <button type="submit" id="submitBtn"
                            class="btn-primary-custom group relative w-full flex justify-center items-center py-4 px-6 border border-transparent rounded-xl text-white focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="submitText">Créer l'organisation</span>
                        <div id="loadingSpinner" class="hidden ml-2">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                    </button>
                </div>

                <!-- Login Link -->
                <div class="form-section text-center pt-4 border-t border-gray-200 mt-6">
                    <p class="text-sm text-gray-600">
                        Déjà un compte ?
                        <a href="{{ route('organization.login.selector') }}" class="font-semibold transition-all duration-300 hover:underline" style="color: #1113a5;">
                            Se connecter
                            <i class="fas fa-arrow-right ml-1 text-xs"></i>
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Logo preview
        document.getElementById('logo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImage').src = e.target.result;
                    document.getElementById('logoPreview').classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });

        // Form validation
        function validateForm() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;

            if (password !== confirmPassword) {
                alert('Les mots de passe ne correspondent pas');
                return false;
            }

            if (password.length < 8) {
                alert('Le mot de passe doit contenir au moins 8 caractères');
                return false;
            }

            return true;
        }

        // Form submission
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            e.preventDefault();

            if (!validateForm()) {
                return;
            }

            // Show loading state
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const loadingSpinner = document.getElementById('loadingSpinner');

            submitBtn.disabled = true;
            submitText.textContent = 'Création en cours...';
            loadingSpinner.classList.remove('hidden');

            // Prepare form data
            const formData = new FormData(this);

            // Submit form
            fetch('{{ route("organization.register.custom") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Success
                    submitText.textContent = 'Organisation créée !';
                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 1500);
                } else {
                    // Error
                    alert('Erreur: ' + (data.message || 'Une erreur est survenue'));
                    resetSubmitButton();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Une erreur est survenue lors de la création');
                resetSubmitButton();
            });
        });

        function resetSubmitButton() {
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const loadingSpinner = document.getElementById('loadingSpinner');

            submitBtn.disabled = false;
            submitText.textContent = 'Créer l\'organisation';
            loadingSpinner.classList.add('hidden');
        }

        // Real-time validation
        document.getElementById('password_confirmation').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;

            if (confirmPassword && password !== confirmPassword) {
                this.classList.add('border-red-500');
                this.classList.remove('border-gray-300');
            } else {
                this.classList.remove('border-red-500');
                this.classList.add('border-gray-300');
            }
        });
    </script>
</body>
</html>
