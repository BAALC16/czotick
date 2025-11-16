<?php
// Définir le chemin vers votre fichier artisan
$artisan = __DIR__ . '/artisan';

// Définir le chemin vers l'exécutable PHP
$php = '/usr/bin/php'; // Chemin standard, peut varier selon l'hébergeur

// Afficher l'exécution pour le débogage
echo "<pre>";

// Exécuter les commandes d'optimisation
echo "<h3>Configuration cache:</h3>";
echo shell_exec("$php $artisan config:clear");

echo "<h3>Route cache:</h3>";
echo shell_exec("$php $artisan route:clear");

echo "<h3>View cache:</h3>";
echo shell_exec("$php $artisan view:clear");

echo "<h3>Application cache:</h3>";
echo shell_exec("$php $artisan cache:clear");

echo "<h3>Compiled views:</h3>";
echo shell_exec("$php $artisan view:cache");

echo "<h3>All caches (optimize clear):</h3>";
echo shell_exec("$php $artisan optimize:clear");

echo "</pre>";

echo "<h2>Cache clearing complete!</h2>";
?>