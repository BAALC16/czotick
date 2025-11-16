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
    <!-- HEAD section ou avant la fermeture de </body> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <title>Formulaire d'inscription au conseil national 2025</title>
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
            flex-direction: row;
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
        }
        
        .event-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            /* background-color: rgba(10, 43, 78, 0.85); */
            background-color: rgba(2, 82, 37, 0.85);
            z-index: 1;
        }
        
        .event-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 700px;
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
        
        .register-btn {
            /* background-color: var(--primary-color); */
            background-color: #025225; 
            color: white;
            border: none;
            border-radius: 50px;
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: none;
        }
        
        .register-btn:hover {
            /* background-color: var(--dark-blue); */
            background-color: #025225;
            transform: translateY(-2px);
        }
        
        /* Form Panel */
        .form-panel {
            flex: 1;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background-color: white;
            overflow-y: auto;
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .form-title {
            /* color: var(--primary-color); */
            color: #025225;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }
        
        .form-subtitle {
            color: #666;
            font-size: 1rem;
        }
        
        form {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #444;
        }
        
        .required-label:after {
            content: ' *';
            color: #e53935;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border 0.3s ease;
        }
        
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="tel"]:focus,
        select:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(29, 134, 217, 0.1);
        }
        
        .radio-group {
            margin-top: 0.5rem;
        }
        
        .radio-option {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        
        input[type="radio"] {
            margin-right: 10px;
            cursor: pointer;
        }
        
        button[type="submit"] {
            /* background-color: var(--primary-color); */
            background-color: #025225;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 1rem;
            font-size: 1.1rem;
            font-weight: 600;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }
        
        button[type="submit"]:hover {
            /* background-color: var(--dark-blue); */
            background-color: #025225; 
        }
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background-color: white;
            margin: auto;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: var(--shadow);
            width: 90%;
            max-width: 500px;
            position: relative;
            transform: translateY(0);
            animation: modalAppear 0.3s ease;
        }
        
        @keyframes modalAppear {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .modal-content h2 {
            margin-bottom: 1rem;
            color: #333;
        }
        
        .close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 1.5rem;
            color: #aaa;
            cursor: pointer;
            transition: color 0.2s ease;
        }
        
        .close:hover {
            color: #333;
        }
        
        .modal-buttons-container {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .modal-button {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            flex: 1;
        }
        
        .blue-button {
            background-color: var(--primary-color);
            color: white;
        }
        
        .blue-button:hover {
            background-color: var(--dark-blue);
        }
        
        .green-button {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .green-button:hover {
            background-color: #218838;
        }
        
        .modal-button-error {
            background-color: #fa4c04;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 1rem;
        }
        
        .modal-button-error:hover {
            background-color: #fa4c04;
        }
        
        /* Responsive design */
        @media (max-width: 992px) {
            .container {
                flex-direction: column;
            }
            
            .event-panel {
                min-height: 50vh;
            }
            
            .register-btn {
                display: inline-block;
                margin-top: 1rem;
            }
            
            .form-panel {
                padding: 2rem 1rem;
            }
        }
        
        @media (max-width: 576px) {
            .event-panel {
                padding: 1.5rem;
            }
            
            .event-title {
                font-size: 2rem;
            }
            
            .event-subtitle {
                font-size: 1.2rem;
            }
            
            .event-details {
                padding: 1.5rem;
            }
            
            .modal-buttons-container {
                flex-direction: column;
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
                
                <button class="register-btn" id="mobile-register-btn">S'inscrire maintenant</button>
            </div>
        </div>
        
        <!-- Registration Form Panel -->
        <div class="form-panel" id="form-section">
            <div class="form-header" style="text-align: center;">
                <div class="logos-container" style="display: flex; justify-content: space-between; align-items: center; max-width: 600px; margin: 0 auto 1rem;">
                    <img src="{{ asset('assets/images/logo-riseup.png') }}" alt="Logo RiseUp" style="height: 80px;">
                    <img src="{{ asset('assets/images/logo-100ans.png') }}" alt="Logo 100 ans" style="height: 80px;">
                    <img src="{{ asset('assets/images/logo1.png') }}" alt="Logo JCI CI" style="height: 80px;">
                </div>

                <h2 class="form-title">Inscription au Conseil National 2025</h2>
                <p class="form-subtitle">Remplissez le formulaire ci-dessous pour participer à l'événement</p>
            </div>
            
            <form action="#" method="post">
                <div class="form-group">
                    <label for="fullname" class="required-label">Nom & Prénoms</label>
                    <input type="text" id="fullname" name="fullname" required>
                </div>
                
                <div class="form-group">
                    <label for="phone" class="required-label">Numéro whatsApp</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
                
                <div class="form-group">
                    <label for="email" class="required-label">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="organization" class="required-label">OLM</label>
                    <select name="organization" id="organization" onchange="toggleOtherInput()" required>
                        <option value="" selected disabled>Sélectionnez votre organisation</option>
                        <option value="JCI ABENGOUROU">JCI ABENGOUROU</option>
                        <option value="JCI ABIDJAN">JCI ABIDJAN</option>
                        <option value="JCI ABIDJAN ELITE">JCI ABIDJAN ELITE</option>
                        <option value="JCI ABIDJAN ETOILE">JCI ABIDJAN ETOILE</option>
                        <option value="JCI ABIDJAN IVOIRE">JCI ABIDJAN IVOIRE</option>
                        <option value="JCI ABIDJAN LAGUNE">JCI ABIDJAN LAGUNE</option>
                        <option value="JCI ABIDJAN LEADER">JCI ABIDJAN LEADER</option>
                        <option value="JCI ABIDJAN OCEAN">JCI ABIDJAN OCEAN</option>
                        <option value="JCI ABIDJAN PREMIUM">JCI ABIDJAN PREMIUM</option>
                        <option value="JCI ABIDJAN PRESTIGE">JCI ABIDJAN PRESTIGE</option>
                        <option value="JCI ABIDJAN PROSPER">JCI ABIDJAN PROSPER</option>
                        <option value="JCI ABIDJAN YELE">JCI ABIDJAN YELE</option>
                        <option value="JCI ABIDJAN SOLEIL">JCI ABIDJAN SOLEIL</option>
                        <option value="JCI ABOBO EBENE">JCI ABOBO EBENE</option>
                        <option value="JCI ABOISSO">JCI ABOISSO</option>
                        <option value="JCI ADJAME FRATERNITÉ">JCI ADJAME FRATERNITÉ</option>
                        <option value="JCI ADZOPE">JCI ADZOPE</option>
                        <option value="JCI AGBOVILLE">JCI AGBOVILLE</option>
                        <option value="JCI ANYAMA">JCI ANYAMA</option>
                        <option value="JCI ASSINI ROYALE">JCI ASSINI ROYALE</option>
                        <option value="JCI BINGERVILLE">JCI BINGERVILLE</option>
                        <option value="JCI BONDOUKOU">JCI BONDOUKOU</option>
                        <option value="JCI BONON ROC">JCI BONON ROC</option>
                        <option value="JCI BONOUA">JCI BONOUA</option>
                        <option value="JCI BOUAFLE">JCI BOUAFLE</option>
                        <option value="JCI BOUAKÉ">JCI BOUAKÉ</option>
                        <option value="JCI BOUNDIALI">JCI BOUNDIALI</option>
                        <option value="JCI DABOU">JCI DABOU</option>
                        <option value="JCI DALOA">JCI DALOA</option>
                        <option value="JCI DAOUKRO">JCI DAOUKRO</option>
                        <option value="JCI DIMBOKRO">JCI DIMBOKRO</option>
                        <option value="JCI DIVO">JCI DIVO</option>
                        <option value="JCI ELEPHANT DUEKOUE">JCI ELEPHANT DUEKOUE</option>
                        <option value="JCI EMERAUDE">JCI EMERAUDE</option>
                        <option value="JCI EXCELCIOR">JCI EXCELCIOR</option>
                        <option value="JCI GAGNOA">JCI GAGNOA</option>
                        <option value="JCI GOLDEN">JCI GOLDEN</option>
                        <option value="JCI GRAND-BASSAM">JCI GRAND-BASSAM</option>
                        <option value="JCI GRAND-LAHOU">JCI GRAND-LAHOU</option>
                        <option value="JCI ISSIA">JCI ISSIA</option>
                        <option value="JCI JACQUEVILLE">JCI JACQUEVILLE</option>
                        <option value="JCI KORHOGO">JCI KORHOGO</option>
                        <option value="JCI KOUMASSI">JCI KOUMASSI</option>
                        <option value="JCI MAN">JCI MAN</option>
                        <option value="JCI MEAGUI">JCI MEAGUI</option>
                        <option value="JCI NIRVANA">JCI NIRVANA</option>
                        <option value="JCI OUME">JCI OUME</option>
                        <option value="JCI PHILIA">JCI PHILIA</option>
                        <option value="JCI SAN-PEDRO">JCI SAN-PEDRO</option>
                        <option value="JCI SEGUELA">JCI SEGUELA</option>
                        <option value="JCI SINFRA">JCI SINFRA</option>
                        <option value="JCI SOUBRE">JCI SOUBRE</option>
                        <option value="JCI SOUTARAH">JCI SOUTARAH</option>
                        <option value="JCI TOUBA CELESTE">JCI TOUBA CELESTE</option>
                        <option value="JCI TOUMODI">JCI TOUMODI</option>
                        <option value="JCI U-ABIDJAN">JCI U-ABIDJAN</option>
                        <option value="JCI U-BOUAKÉ">JCI U-BOUAKÉ</option>
                        <option value="JCI U-COCODY">JCI U-COCODY</option>
                        <option value="JCI U-DALOA">JCI U-DALOA</option>
                        <option value="JCI U-KORHOGO">JCI U-KORHOGO</option>
                        <option value="JCI U-MAN">JCI U-MAN</option>
                        <option value="JCI U-YAMOUSSOUKRO">JCI U-YAMOUSSOUKRO</option>
                        <option value="JCI YAMOUSSOUKRO">JCI YAMOUSSOUKRO</option>
                        <option value="JCI YOPOUGON">JCI YOPOUGON</option>
                        <option value="JCI ACADEMIE">JCI ACADEMIE</option>
                        <option value="JCI ZUENOULA EXCELLENCE">JCI ZUENOULA EXCELLENCE</option>
                        <option value="Autre">Autre</option>
                    </select>
                </div>
                
                <div class="form-group" id="other-organization-container" style="display:none;">
                    <label for="other-organization">Veuillez préciser</label>
                    <input type="text" name="other_organization" id="other-organization" placeholder="Précisez votre organisation">
                </div>
                
                <div class="form-group">
                    <label for="quality" class="required-label">Qualité/Fonction</label>
                    <select name="quality" id="quality" required>
                        <option value="" selected disabled>Sélectionnez votre fonction</option>
                        <option value="Présidente Exécutive Nationale">Présidente Exécutive Nationale</option>
                        <option value="Past President National">Past President National</option>
                        <option value="Président.e d'Institution">Président.e d'Institution</option>
                        <option value="Comité Directeur National 2025">Comité Directeur National 2025</option>
                        <option value="Président.e Local.e 2025">Président.e Local.e 2025</option>
                        <option value="Sénateur / Past President Local">Sénateur / Past President Local</option>
                        <option value="Membre OLM">Membre OLM</option>
                        <option value="Membre Universitaire">Membre Universitaire</option>
                        <option value="Membre Potentiel">Membre Potentiel</option>
                        <option value="Invité.e">Invité.e</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="required-label">Ticket</label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input type="radio" id="ticket-member" name="ticket_type" value="15200" required>
                            <label for="ticket-member">15.200 FCFA (Past President / Sénateur / Membre / Membre Potentiel / Invité.e)</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="ticket-student" name="ticket_type" value="7600">
                            <label for="ticket-student">7.600 FCFA (Membre universitaire)</label>
                        </div>
                    </div>
                </div>
                
                <button type="submit">S'inscrire maintenant</button>
            </form>
        </div>
    </div>
    
    <!-- Success Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('successModal')">&times;</span>
            <h2>Inscription réussie !</h2>
            <p id="successModalMessage">
                Votre ticket vous a été envoyé par mail.
            </p>
        </div>
    </div>
    
    <!-- Error Modal -->
    <div id="errorModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('errorModal')">&times;</span>
            <h2>Erreur lors de l'inscription</h2>
            <ul id="errorModalMessage" style="padding-left: 1.2rem;"></ul>
            <button class="modal-button-error" onclick="closeModal('errorModal')">Fermer</button>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize modals
            document.getElementById('successModal').style.display = 'none';
            document.getElementById('errorModal').style.display = 'none';
            
            // Mobile register button functionality
            document.getElementById('mobile-register-btn').addEventListener('click', function() {
                document.getElementById('form-section').scrollIntoView({ behavior: 'smooth' });
            });
            
            // Home button functionality
            /* document.getElementById('homeButton').addEventListener('click', function() {
                const homeUrl = @json(route('home'));
                window.location.href = homeUrl;
            }); */
        });
        
        function toggleOtherInput() {
            const select = document.getElementById('organization');
            const otherContainer = document.getElementById('other-organization-container');
            
            if (select.value === 'Autre') {
                otherContainer.style.display = 'block';
            } else {
                otherContainer.style.display = 'none';
            }
        }
        
        /* function showModal(modalId, messages) {
            const modal = document.getElementById(modalId);
            const messageElement = document.getElementById(modalId + 'Message');

            if (!modal || !messageElement) {
                console.error('Modal or message element not found');
                return;
            }

            // Vider le contenu précédent
            messageElement.innerHTML = '';

            if (Array.isArray(messages)) {
                messages.forEach(msg => {
                    console.log('Ajout du message :', msg); // debug
                    const li = document.createElement('li');
                    li.textContent = msg;
                    messageElement.appendChild(li);
                });
            } else {
                const li = document.createElement('li');
                li.textContent = messages;
                messageElement.appendChild(li);
            }

            modal.style.display = 'flex';
        } */

        function showModal(modalId, messages) {
            const modal = document.getElementById(modalId);
            const messageElement = document.getElementById(modalId + 'Message');

            if (!modal || !messageElement) {
                console.error('Modal or message element not found');
                return;
            }

            // Vider le contenu précédent
            messageElement.innerHTML = '';

            if (modalId === 'successModal' || modalId === 'errorWave') {
                // Affichage simple (texte brut ou HTML autorisé)
                messageElement.textContent = messages;
            } else {
                // Affichage en liste pour erreurs ou autres types
                if (Array.isArray(messages)) {
                    messages.forEach(msg => {
                        const li = document.createElement('li');
                        li.textContent = msg;
                        messageElement.appendChild(li);
                    });
                } else {
                    const li = document.createElement('li');
                    li.textContent = messages;
                    messageElement.appendChild(li);
                }
            }

            modal.style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        /* function redirectToPayment() {
            const userEmail = sessionStorage.getItem('userEmail');
            const amount = sessionStorage.getItem('amount');
        
            if (userEmail && amount) {
                // Créer un formulaire caché
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ route('validation-paiement') }}";
            
                // Créer un champ caché pour l'email
                const emailField = document.createElement('input');
                emailField.type = 'hidden';
                emailField.name = 'email';
                emailField.value = userEmail;
                form.appendChild(emailField);
            
                // Créer un champ caché pour le montant
                const amountField = document.createElement('input');
                amountField.type = 'hidden';
                amountField.name = 'amount';
                amountField.value = amount;
                form.appendChild(amountField);
            
                // Ajouter l'URL de redirection après paiement
                const returnUrlField = document.createElement('input');
                returnUrlField.type = 'hidden';
                returnUrlField.name = 'returnUrl';
                returnUrlField.value = 'https://czotick.ci/jci-ci-conseil-national-2025?payment=success';
                form.appendChild(returnUrlField);
            
                // Ajouter le token CSRF (pour la sécurité)
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = "{{ csrf_token() }}";
                form.appendChild(csrfToken);
            
                // Masquer le formulaire
                form.style.display = 'none';
            
                // Ajouter le formulaire à la page sans qu'il soit visible
                document.body.appendChild(form);
            
                // Soumettre le formulaire
                form.submit();
            } else {
                console.error("Les informations nécessaires sont manquantes.");
            }
        } */

        function redirectToPayment() {
            // Vérifier que les informations essentielles sont présentes
            const email = sessionStorage.getItem('email');
            const ticket_type = sessionStorage.getItem('ticket_type');

            console.log("email:", sessionStorage.getItem("email"));
            console.log("ticket_type:", sessionStorage.getItem("ticket_type"));
            console.log("fullname:", sessionStorage.getItem("fullname"));
            console.log("phone:", sessionStorage.getItem("phone"));
            console.log("organization:", sessionStorage.getItem("organization"));
            console.log("other_organization:", sessionStorage.getItem("other_organization"));
            console.log("quality:", sessionStorage.getItem("quality"));
            console.log("ticket_type:", sessionStorage.getItem("ticket_type"));

            
            if (!email || !ticket_type) {
                console.error("Les informations nécessaires sont manquantes.");
                return;
            }
    
            // Créer un formulaire caché
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('validation-paiement') }}";
            
            // Ajouter toutes les données stockées dans sessionStorage
            const fieldsToAdd = [
                'email', 
                'fullname', 
                'phone', 
                'organization', 
                'other_organization', 
                'quality', 
                'ticket_type'
            ];
    
            fieldsToAdd.forEach(fieldName => {
                const value = sessionStorage.getItem(fieldName);
                if (value !== null) {
                    const field = document.createElement('input');
                    field.type = 'hidden';
                    field.name = fieldName;
                    field.value = value;
                    form.appendChild(field);
                }
            });
    
            // Renommer ticket_type en amount pour compatibilité avec la route existante
            const amountField = document.createElement('input');
            amountField.type = 'hidden';
            amountField.name = 'ticket_type';
            amountField.value = sessionStorage.getItem('ticket_type');
            form.appendChild(amountField);
            
            // Ajouter l'URL de redirection après paiement
            const returnUrlField = document.createElement('input');
            returnUrlField.type = 'hidden';
            returnUrlField.name = 'returnUrl';
            returnUrlField.value = 'https://czotick.com/jci-ci-conseil-national-2025?payment=success';
            form.appendChild(returnUrlField);
            
            // Ajouter le token CSRF (pour la sécurité)
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = "{{ csrf_token() }}";
            form.appendChild(csrfToken);
            
            // Masquer le formulaire
            form.style.display = 'none';
            
            // Ajouter le formulaire à la page sans qu'il soit visible
            document.body.appendChild(form);
            
            // Soumettre le formulaire
            form.submit();
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Vérifier si nous revenons d'un paiement réussi
            const urlParams = new URLSearchParams(window.location.search);
            const paymentStatus = urlParams.get('payment');
            
            if (paymentStatus === 'success') {
                // Afficher le modal de succès
                showModal('successModal', `Félicitations! Votre paiement a été effectué avec succès.

                Votre ticket du Conseil National 2025 de la JCI Côte d'Ivoire vous a été envoyé par email et via WhatsApp.

                Merci de le télécharger et bien le conserver.

                Ne partagez le ticket à personne.`);
              
                window.history.replaceState({}, document.title, window.location.pathname);
            }

            if (paymentStatus === 'error') {
                // Afficher le modal de succès
                showModal('errorWave', `Le paiement n’a pas pu aboutir. Merci de bien vouloir reprendre la procédure.`);
              
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });

        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const url = "{{ route('cn.store') }}";
            
            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    return response.json();
                } else if (response.status === 422) {
                    return response.json().then(errors => {
                        throw errors;
                    });
                } else {
                    return response.text().then(text => {
                        throw new Error('Réponse non JSON: ' + text);
                    });
                }
            })
            .then(data => {
                if (data.message) {
                    // Stocker chaque donnée individuellement dans sessionStorage
                    sessionStorage.setItem('email', data.validated.email);
                    sessionStorage.setItem('fullname', data.validated.fullname);
                    sessionStorage.setItem('phone', data.validated.phone);
                    sessionStorage.setItem('organization', data.validated.organization);
                    sessionStorage.setItem('other_organization', data.validated.other_organization || ''); // Gestion des valeurs null
                    sessionStorage.setItem('quality', data.validated.quality);
                    sessionStorage.setItem('ticket_type', data.validated.ticket_type);
                    redirectToPayment();
                } else {
                    showModal('errorModal', 'Réponse du serveur invalide');
                }
            })
            .catch(error => {
                if (error.errors) {
                    const validationErrors = error.errors;
                    let errorMessages = [];
                    for (const messages of Object.values(validationErrors)) {
                        errorMessages = errorMessages.concat(messages);
                    }

                    console.log("errorMessages", errorMessages);
                    showModal('errorModal', errorMessages);

                } else {
                    console.error('Erreur:', error);
                    showModal('errorModal', 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer.');
                }
            });
        });
                
        // Animation subtile pour améliorer l'UX
        const formInputs = document.querySelectorAll('input, select');
        formInputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('active-field');
            });
            
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentElement.classList.remove('active-field');
                }
            });
        });

        // Validation des champs en temps réel
        document.getElementById('phone').addEventListener('input', function() {
            const phoneRegex = /^[0-9+\s-]{8,15}$/;
            if (phoneRegex.test(this.value)) {
                this.style.borderColor = '#28a745';
            } else {
                this.style.borderColor = '#dc3545';
            }
        });
        
        document.getElementById('email').addEventListener('input', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (emailRegex.test(this.value)) {
                this.style.borderColor = '#28a745';
            } else {
                this.style.borderColor = '#dc3545';
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            const element1 = document.getElementById('quality');
            const element2 = document.getElementById('organization');
            const choices1 = new Choices(element1, {
                searchEnabled: true,
                shouldSort: false,
                placeholderValue: 'Sélectionnez votre fonction',
                itemSelectText: '', // enlève "Press to select"
                noResultsText: 'Aucun résultat trouvé',
                noChoicesText: 'Aucun choix disponible',
                loadingText: 'Chargement...',
            });
            const choices2 = new Choices(element2, {
                searchEnabled: true,
                shouldSort: false,
                placeholderValue: 'Sélectionnez votre organisation',
                itemSelectText: '', // enlève "Press to select"
                noResultsText: 'Aucun résultat trouvé',
                noChoicesText: 'Aucun choix disponible',
                loadingText: 'Chargement...',
            });
        });
    </script>
</body>
</html>