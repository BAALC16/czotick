<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Formulaire d'inscription</title>
    <style>
       body {
            font-family: Arial, sans-serif;
            /* Pour définir une couleur de fond, vous pouvez décommenter la ligne suivante */
            /* background-color: #e0f7fa; */
            background-image: url("{{ asset('assets/images/membres.png') }}");
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        form {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 700px; /* Largeur maximale pour le formulaire */
            box-sizing: border-box;
            max-height: 80vh; /* Hauteur maximale du formulaire */
            overflow-y: auto; /* Scroll vertical */
        }

        #organisation, #organisation-label {
            display: none; /* Masque à la fois l'input et le label */
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .required-label:after {
            content: ' *';
            color: red;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="radio"] {
            margin-right: 5px;
        }

        p {
            margin-bottom: 5px;
            font-weight: bold;
        }

        button {
            background-color: #1d86d9;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            box-sizing: border-box;
        }

        button:hover {
            background-color: #1d86d9;
        }

        /* Applique une largeur de 100% pour que le textarea occupe toute la largeur du formulaire */
        textarea {
            width: 97%;
            resize: vertical; /* Permet de redimensionner verticalement */
            padding: 10px;
            margin-bottom: 10px;
            font-size: 16px;
        }

        /* Ajoute un style au placeholder */
        textarea::placeholder {
            color: #aaa;
            font-style: italic;
        }

        /* Styles pour les modals */
        .modal {
            display: none; /* Masquer les modals par défaut */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
            display: flex;
        }

        .modal-content {
            background-color: #fefefe;
            padding: 20px;
            border: 1px solid #888;
            width: 100%;
            max-width: 500px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            position: relative;
        }

        .close {
            color: #aaa;
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }

        .modal-button {
            background-color: #1d86d9;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }

        .modal-button-error {
            background-color: #ffa800;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }

        .modal-button:hover {
            background-color: #1d86d9;
        }

        .modal-button-error:hover {
            background-color: #ffa800;
        }
    </style>
</head>
<body>
    <form action="#" method="post">
        <div>
            <h2 style="text-align: center; margin-bottom: 20px;"> Formulaire d'adhésion à la JCI Nirvana</h2> 
        </div>

        <p class="required-label">Votre âge est-il compris entre 18 et 40 ans ? </p>
        <label for="age" style="margin-bottom: 25px;">
            <input type="radio" name="age" value="Oui"> Oui
            <input type="radio" name="age" value="Non"> Non
        </label>
        <!-- Message à afficher si l'âge est "Non" -->
        <div id="ageMessage" style="display: none; color: #e74c3c;  margin-top: -20px; margin-bottom: 10px;">
            <p><strong>La JCI est une organisation de jeunes entre 18 et 40 ans.</strong></p>
        </div>

        <label for="fullname" class="required-label">Nom & Prénoms:</label>
        <input type="text" id="fullname" name="fullname" required>
       
        <p class="required-label">Genre :</p>
        <label for="gender" style="margin-bottom: 25px;">
            <input type="radio" name="gender" value="M"> M
            <input type="radio" name="gender" value="F"> F
        </label>

        <label for="city" class="required-label">Ville/Commune :</label>
        <input type="text" id="city" name="city" required>
        
        <label for="phone" class="required-label">Téléphone :</label>
        <input type="text" id="phone" name="phone" required>

        <label for="email" class="required-label">Email :</label>
        <input type="email" id="email" name="email" required>

        <label for="source">Comment avez-vous connu la JCI ?</label>
        <input type="radio" name="source" value="Réseaux sociaux"> Réseaux sociaux <br>
        <input type="radio" name="source" value="Site web"> Site web <br>
        <input type="radio" name="source" value="Connaissance"> Connaissance <br>
        <input type="radio" name="source" value="Autre"> Autre
        <input type="text" id="other" name="other">

        <label for="haveAnOrganisation" style="margin-top: 25px; margin-bottom: 25px;">Avez-vous déjà fait partie d’une organisation associative ?
            <input type="radio" name="haveAnOrganisation" value="Oui"> Oui
            <input type="radio" name="haveAnOrganisation" value="Non"> Non
        </label>
        <label for="organisation" id="organisation-label" class="">Si oui, laquelle ?</label>
        <input type="text" id="organisation" name="organisation">

        
        <label for="job" class="required-label" style="margin-top: 25px;">Situation professionnelle :</label>
        <input type="text" id="job" name="job" required>

        <textarea name="motivations" id="motivations" cols="30" rows="10" placeholder="Pourquoi souhaitez-vous rejoindre la JCI max 200 caractères..."></textarea>
        
        <label for="agree"> <input type="checkbox" name="agree" value="1" class="required-label" required> J'accepte d'être contacté par la JCI pour finaliser mon adhésion</label>

        <button type="submit">Envoyer ma candidature </button>
    </form>

    <!-- Modal de succès -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('successModal')">&times;</span>
            <h2>Inscription réussie !</h2>
            <p id="successModalMessage"></p>
            <button id="homeButton" class="modal-button">Retourner à la page d'accueil</button>
        </div>
    </div>

    <!-- Modal d'échec -->
    <div id="errorModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('errorModal')">&times;</span>
            <h2>Erreur lors de l'inscription</h2>
            <p id="errorModalMessage"></p>
            <button class="modal-button-error" onclick="closeModal('errorModal')">Fermer</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Assurez-vous que les modals sont cachés au chargement
            document.getElementById('successModal').style.display = 'none';
            document.getElementById('errorModal').style.display = 'none';
        });

        document.getElementById('homeButton').addEventListener('click', function() {
            // Utilisez Laravel pour générer l'URL de la route
            const homeUrl = @json(route('home'));
            window.location.href = homeUrl;
        });

        // Écouteur pour le choix d'âge
        const ageRadios = document.querySelectorAll('input[name="age"]');
        ageRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                toggleFormState(); // Met à jour l'état du formulaire
            });
        });

        // Écouteur pour le choix de "source"
        const sourceRadios = document.querySelectorAll('input[name="source"]');
        sourceRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                toggleOtherInput(); // Affiche ou masque le champ "Autre"
            });
        });

        // Écouteur pour le choix "Organisation associative"
        const organisationRadios = document.querySelectorAll('input[name="haveAnOrganisation"]');
        organisationRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                toggleOrganisationInput(); // Affiche ou masque le champ "Si oui, laquelle ?"
            });
        });

        // Fonction pour griser le formulaire si l'âge est "Non"
        function toggleFormState() {
            const isAgeNon = document.querySelector('input[name="age"]:checked')?.value === 'Non';
            const formElements = document.querySelectorAll('input, textarea, button, select');

            formElements.forEach(element => {
                // Vérifiez si l'élément est dans la section des radios d'âge
                if (element.closest('label') && element.closest('label').querySelector('input[name="age"]')) {
                    return;  // Ignorez les éléments de la question "Votre âge est-il compris entre 18 et 40 ans ?"
                }

                if (isAgeNon) {
                    element.setAttribute('disabled', 'true');  // Désactive l'élément
                } else {
                    element.removeAttribute('disabled');  // Active l'élément
                }
            });

            // Afficher ou masquer le message en fonction du choix de l'âge
            const ageMessage = document.getElementById('ageMessage');
            if (isAgeNon) {
                ageMessage.style.display = 'block'; // Affiche le message si "Non" est sélectionné
            } else {
                ageMessage.style.display = 'none'; // Cache le message si "Oui" est sélectionné
            }
        }

        // Fonction pour afficher ou cacher le champ "Autre"
        function toggleOtherInput() {
            const otherInput = document.getElementById('other');
            const isOtherSelected = document.querySelector('input[name="source"]:checked')?.value === 'Autre';

            if (isOtherSelected) {
                otherInput.setAttribute('required', 'required');
                otherInput.style.display = 'block';
            } else {
                otherInput.removeAttribute('required');
                otherInput.style.display = 'none';
            }
        }

        // Fonction pour afficher ou cacher le champ "Si oui, laquelle ?"
        function toggleOrganisationInput() {
            const organisationInput = document.getElementById('organisation');
            const organisationLabel = document.getElementById('organisation-label');
            const isYesSelected = document.querySelector('input[name="haveAnOrganisation"]:checked')?.value === 'Oui';

            if (isYesSelected) {
                organisationInput.style.display = 'block'; // Affiche l'input
                organisationLabel.style.display = 'block'; // Affiche le label
                organisationInput.setAttribute('required', 'required'); // Rendre obligatoire
            } else {
                organisationInput.style.display = 'none'; // Masque l'input
                organisationLabel.style.display = 'none'; // Masque le label
                organisationInput.removeAttribute('required'); // Rendre non obligatoire
            }
        }

        // Initialisation
        toggleFormState();
        toggleOtherInput();
        toggleOrganisationInput();

        function showModal(modalId, message) {
            const modal = document.getElementById(modalId);
            const messageElement = document.getElementById(modalId + 'Message');
            if (messageElement) {
                messageElement.innerHTML = message; // Utilisez innerHTML pour insérer du HTML
                modal.style.display = 'flex'; // Utiliser flex pour centrer le modal
            } else {
                console.error('Element with ID "' + modalId + 'Message" not found');
            }
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const url = "{{ route('become-member.store') }}"; // Remplacez par le nom de votre route

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
                } else if (response.status === 422) { // Erreurs de validation
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
                    showModal('successModal', data.message);
                } else {
                    showModal('errorModal', 'Réponse du serveur invalide');
                }
            })
            .catch(error => {
                if (error.errors) { // Si ce sont des erreurs de validation
                    const validationErrors = error.errors;
                    let errorMessages = '';
                    for (const [field, messages] of Object.entries(validationErrors)) {
                        errorMessages += `${messages.join(', ')}<br>`;
                    }
                    // Assurez-vous que le modal est capable d'afficher du HTML correctement
                    showModal('errorModal', errorMessages);
                } else {
                    console.error('Erreur:', error);
                    showModal('errorModal', 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer.');
                }
            });

        });

    </script>
</body>
</html>
