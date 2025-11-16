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
    <div class="container" style="font-family: Arial, sans-serif; line-height: 1.6;">
        <img src="{{ asset('assets/front/images/logo1.png') }}" alt="Logo JCI Nirvana" class="logo" style="max-width: 200px;">
        
        <h1 style="text-align: center; color: #2c3e50;">Merci pour votre inscription !</h1>
        
        <p>Bonjour {{ $fullname }},</p>
        
        <p>Voici vos détails d'inscription :</p>
        
        <ul>
            <li><strong>Nom & Prénoms :</strong> {{ $fullname }}</li>
            <li><strong>Téléphone :</strong> {{ $phone }}</li>
            <li><strong>Email :</strong> {{ $email }}</li>
            <li><strong>Genre :</strong> {{ $gender }}</li> <!-- Affiche le genre si renseigné -->
            @if($city)
                <li><strong>Ville/Commune :</strong> {{ $city }}</li> <!-- Affiche la ville si renseignée -->
            @endif
            <li><strong>Fonction :</strong> {{ $job }}</li>
            <li><strong>Motivations :</strong> <br> {!! nl2br(e($motivations)) !!}</li>
            @if($source)
                <li><strong>Comment avez-vous connu la JCI ? :</strong> {{ $source }}</li> <!-- Affiche la source si renseignée -->
            @endif
            @if($source === 'Autre' && $other)
                <li><strong>Autre source :</strong> {{ $other }}</li> <!-- Affiche "Autre" source si renseignée -->
            @endif
            @if($haveAnOrganisation)
                <li><strong>Avez-vous déjà fait partie d’une organisation associative ? :</strong> {{ $haveAnOrganisation }}</li>
            @endif
            @if($haveAnOrganisation === 'Oui' && $organisation)
                <li><strong>Si oui, laquelle ? :</strong> {{ $organisation }}</li> <!-- Affiche l'organisation si renseignée -->
            @endif
            <li><strong>Accepter d'être contacté :</strong> {{ $agree ? 'Oui' : 'Non' }}</li> <!-- Affiche l'acceptation -->
        </ul>
        
        <p>Vous serez bientôt contacté pour les prochaines étapes de votre inscription.</p>

        <div class="footer" style="margin-top: 30px; text-align: center; font-size: 12px; color: #95a5a6;">
            <p>JCI Nirvana</p>
            <p>Pour toute question, n'hésitez pas à nous contacter à <a href="mailto:secretariat@jcinirvana.com">secretariat@jcinirvana.com</a></p>
        </div>
    </div>
</body>


</html>
