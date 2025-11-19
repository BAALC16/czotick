<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalisez votre paiement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .alert-box {
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .payment-summary {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        .payment-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .payment-row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 1.2em;
            color: #dc3545;
            padding-top: 15px;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-size: 12px;
        }
        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>⏳ Paiement partiel reçu</h1>
        <p style="margin: 0;">{{ $event->event_title ?? 'Événement' }}</p>
    </div>

    <div class="content">
        <p>Bonjour <strong>{{ $registration->fullname ?? 'Cher client' }}</strong>,</p>

        <p>Nous avons bien reçu votre paiement partiel pour l'événement <strong>{{ $event->event_title ?? 'Événement' }}</strong>.</p>

        <div class="alert-box">
            <h3 style="margin-top: 0; color: #856404;">⚠️ Paiement incomplet</h3>
            <p style="margin-bottom: 0; color: #856404;">
                Votre inscription est en attente de finalisation. Veuillez compléter le paiement pour confirmer votre participation.
            </p>
        </div>

        <div class="payment-summary">
            <h3 style="margin-top: 0;">Récapitulatif de votre paiement</h3>
            
            <div class="payment-row">
                <span>Montant total du ticket :</span>
                <strong>{{ number_format($ticketPrice, 0, ',', ' ') }} {{ $currency }}</strong>
            </div>
            
            <div class="payment-row">
                <span>Montant déjà payé :</span>
                <strong style="color: #28a745;">{{ number_format($amountPaid, 0, ',', ' ') }} {{ $currency }}</strong>
            </div>
            
            <div class="payment-row">
                <span>Solde restant à payer :</span>
                <strong style="color: #dc3545;">{{ number_format($balanceDue, 0, ',', ' ') }} {{ $currency }}</strong>
            </div>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $completionUrl }}" class="btn">
                💳 Finaliser mon paiement
            </a>
        </div>

        <div class="info-box">
            <p style="margin: 0;">
                <strong>💡 Alternative :</strong> Vous pouvez également accéder à la page d'inscription avec ce lien :
                <br>
                <a href="{{ $registrationUrl }}" style="color: #2196f3; word-break: break-all;">{{ $registrationUrl }}</a>
            </p>
        </div>

        <p><strong>Numéro d'inscription :</strong> {{ $registration->registration_number }}</p>

        @if(isset($event->event_date))
        <p><strong>Date de l'événement :</strong> {{ \Carbon\Carbon::parse($event->event_date)->format('d/m/Y') }}</p>
        @endif

        <p style="margin-top: 30px;">
            Si vous avez des questions, n'hésitez pas à nous contacter.
        </p>

        <p>
            Cordialement,<br>
            <strong>{{ $organization->org_name ?? 'L\'équipe' }}</strong>
        </p>
    </div>

    <div class="footer">
        <p>Cet email a été envoyé automatiquement. Merci de ne pas y répondre.</p>
        <p>&copy; {{ date('Y') }} {{ $organization->org_name ?? 'Organisation' }}. Tous droits réservés.</p>
    </div>
</body>
</html>

