<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation de votre inscription</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }
        .container {
            width: 80%;
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #dddddd; /* Bordure légère */
            box-shadow: 0 4px 8px rgba(0,0,0,0.2); /* Ombre pour l'effet d'élévation */
        }
        h1 {
            color: #4CAF50;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin-bottom: 10px;
        }
        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #777;
        }
        .logo {
            display: block;
            margin: 0 auto 20px;
            max-width: 150px;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="{{ asset('assets/front/images/logo-czotick.png') }}" alt="Logo Czotick" class="logo">
        <h1>Merci pour votre inscription !</h1>
        <p>Bonjour {{ $fullname }},</p>
        <p>Voici vos détails d'enregistrement :</p>
        <ul>
            <li>Nom & Prénoms : {{ $fullname }}</li>
            <li>Téléphone : {{ $phone }}</li>
            <li>Email : {{ $email }}</li>
            <li>OLM/OLP/OLC : {{ $organization }} 
                @if($other_organization && $organization == "Autre")
                    - {{ $other_organization }}
                @endif
            </li>
            <li>Qualité/Fonction : {{ $quality }}</li>
            <li>Ticket : {{ $ticket_type }} FCFA</li>
        </ul>
        <p>Vous recevrez votre reçu une fois votre paiement effectué via wave/orange au +2250747202787</p>
        <div class="footer">
            <p>L'équipe projet</p>
        </div>
    </div>
</body>
</html>
