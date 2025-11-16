<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Validation paiement</title>
    <style>
       body {
            font-family: Arial, sans-serif;
            background-image: url("{{ asset('assets/images/coloni1.jpg') }}");
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
    <form>
        <div>
            <h2 style="text-align: center; margin-bottom: 20px;"> Opération réussie! </h2> 
        </div>
        <button id="homeButton" class="modal-button blue-button" type="button">Retour à l'accueil</button>
    </form>
    <script>
        document.getElementById('homeButton').addEventListener('click', function() {
            const homeUrl = @json(route('home')); 
            window.location.href = homeUrl; 
        });
    </script>
</body>
</html>
