<?php
// Placer ce fichier dans le dossier public (optimize.php)

// Définir le chemin vers votre application Laravel
$basePath = dirname(__DIR__);

// Définir le chemin de l'artisan
$artisan = $basePath . '/artisan';

// Changer le répertoire de travail
chdir($basePath);

// Exécuter la commande optimize
echo shell_exec('php ' . $artisan . ' optimize');

// Supprimer ce fichier après utilisation pour des raisons de sécurité
// unlink(__FILE__);