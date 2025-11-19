<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vos identifiants de connexion</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #1113a5;
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #f9fafb;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .credentials-box {
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .credential-item {
            margin: 15px 0;
            padding: 15px;
            background: #f3f4f6;
            border-radius: 6px;
        }
        .credential-label {
            font-weight: 600;
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .credential-value {
            font-size: 18px;
            font-weight: bold;
            color: #111827;
            font-family: monospace;
        }
        .button {
            display: inline-block;
            background: #1113a5;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: 600;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 12px;
        }
        .warning {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0; font-size: 24px;">🎉 Bienvenue !</h1>
        <p style="margin: 10px 0 0 0; opacity: 0.9;">Vos identifiants de connexion</p>
    </div>
    
    <div class="content">
        <p>Bonjour <strong>{{ $collaboratorName }}</strong>,</p>
        
        <p>Vous avez été ajouté en tant que collaborateur pour l'organisation <strong>{{ $organizationName }}</strong>.</p>
        
        <p>Voici vos identifiants pour accéder au dashboard :</p>
        
        <div class="credentials-box">
            <div class="credential-item">
                <div class="credential-label">Email de connexion</div>
                <div class="credential-value">{{ $email }}</div>
            </div>
            <div class="credential-item">
                <div class="credential-label">Mot de passe temporaire</div>
                <div class="credential-value">{{ $password }}</div>
            </div>
        </div>
        
        <div class="warning">
            <strong>⚠️ Important :</strong> Pour des raisons de sécurité, veuillez changer ce mot de passe lors de votre première connexion.
        </div>
        
        <div style="text-align: center;">
            <a href="{{ $loginUrl }}" class="button">Se connecter au dashboard</a>
        </div>
        
        <p style="margin-top: 30px; color: #6b7280; font-size: 14px;">
            <strong>Code collaborateur :</strong> <code style="background: #f3f4f6; padding: 4px 8px; border-radius: 4px;">{{ $referrerCode }}</code>
        </p>
        
        <p style="color: #6b7280; font-size: 14px;">
            Vous pouvez utiliser ce code pour partager vos événements avec vos clients et suivre vos commissions.
        </p>
    </div>
    
    <div class="footer">
        <p>Cet email a été envoyé automatiquement. Ne répondez pas à cet email.</p>
        <p>&copy; {{ date('Y') }} {{ $organizationName }}. Tous droits réservés.</p>
    </div>
</body>
</html>

