<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket d'entrée</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f7f7f7;
        }
        .ticket {
            width: 350px;
            border: 2px dashed #000;
            padding: 20px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            position: relative;
        }
        .ticket h1 {
            text-align: center;
            margin-bottom: 10px;
            font-size: 22px;
            color: #333;
        }
        .ticket p {
            margin: 5px 0;
            font-size: 16px;
            color: #555;
        }
        .ticket .qrcode {
            text-align: center;
            margin-top: 15px;
        }
        .ticket .qrcode img {
            max-width: 100px;
        }
    </style>
</head>
<body>
    <div class="ticket">
        <h1>Ticket d'entrée</h1>
        <p><strong>Nom & Prénoms :</strong> {{ $fullname }}</p>
        <p><strong>Téléphone :</strong> {{ $phone }}</p>
        <p><strong>Email :</strong> {{ $email }}</p>
        <p><strong>Type de ticket :</strong> {{ $ticketType }}</p>
        <p><strong>Organisation :</strong> {{ $organization }}</p>
        <p><strong>Qualité :</strong> {{ $quality }}</p>
        <div class="qrcode">
            <img src="{{ $qrCode }}" alt="QR Code">
        </div>
        <p style="text-align: center; margin-top: 15px;">Présentez ce ticket à l'entrée.</p>
    </div>
</body>
</html>
