<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre reçu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .header {
            text-align: center;
            padding: 10px 0;
        }
        .header img {
            max-width: 150px;
        }
        .content {
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #dddddd; /* Bordure légère */
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2); /* Ombre pour l'effet d'élévation */
        }
        .content h1 {
            color: #333333;
            font-size: 24px;
            margin-top: 0;
        }
        .content p {
            color: #555555;
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 15px;
        }
        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Assurez-vous que le chemin du logo est correct -->
        <img src="{{ asset('assets/front/images/logo-czotick.png') }}" alt="Logo Czotick" class="logo">
        </div>
        <div class="content">
            <h1>🎫 Votre ticket d'inscription</h1>
            <p>Bonjour {{ $fullname }},</p>
            <p>Félicitations ! Votre inscription au <strong> {{ $event->event_title }} </strong> a été confirmée.</p>

            <div style="background-color: #f0f8ff; padding: 15px; border-radius: 5px; margin: 20px 0;">
                <h3 style="color: #0066cc; margin-top: 0;">📋 Détails de votre inscription</h3>
                <p><strong>Nom :</strong> {{ $fullname }}</p>
                <p><strong>Email :</strong> {{ $email }}</p>
                <p><strong>Téléphone :</strong> {{ $phone }}</p>
                @if($organization_name)
                <p><strong>Organisation :</strong> {{ $organization_name }}</p>
                @endif
                @if($position)
                <p><strong>Fonction :</strong> {{ $position }}</p>
                @endif
                <p><strong>Numéro d'inscription :</strong> {{ $registration->registration_number }}</p>
            </div>

            <div style="background-color: #e8f5e8; padding: 15px; border-radius: 5px; margin: 20px 0;">
                <h3 style="color: #006600; margin-top: 0;">📅 Informations de l'événement</h3>
                <p><strong>Date :</strong> {{ $event->event_date ? $event->event_date->format('d/m/Y') : '9 octobre 2025' }}</p>
                <p><strong>Heure :</strong> {{ $event->event_start_time ? $event->event_start_time->format('H:i') : '15:00' }} - {{ $event->event_end_time ? $event->event_end_time->format('H:i') : '21:00' }}</p>
                <p><strong>Lieu :</strong> {{ $event->event_location ?? 'Palais des Sports, Treichville, Abidjan, Côte d\'Ivoire' }}</p>
            </div>

            <p>Vous trouverez ci-joint votre ticket électronique. Conservez-le précieusement et présentez-le à l'entrée de l'événement.</p>

            {{-- <p style="color: #0066cc; font-weight: bold;">🎉 Nous avons hâte de vous voir le {{ $event->event_date ? $event->event_date->format('d/m/Y') : '9 octobre 2025' }} !</p> --}}
        </div>
        <div class="footer">
            <p>L'équipe projet</p>
        </div>
    </div>
</body>
</html>
