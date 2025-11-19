#!/bin/bash

echo "🚀 Démarrage de l'application Czotick..."

# Attendre que MySQL soit prêt
echo "⏳ Attente de MySQL..."
until php artisan db:monitor 2>/dev/null; do
    echo "MySQL n'est pas encore prêt, attente..."
    sleep 2
done

echo "✅ MySQL est prêt!"

# Installer les dépendances si nécessaire
if [ ! -d "vendor" ]; then
    echo "📦 Installation des dépendances Composer..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

if [ ! -d "node_modules" ]; then
    echo "📦 Installation des dépendances NPM..."
    npm install
fi

# Copier le fichier .env si nécessaire
if [ ! -f ".env" ]; then
    echo "📝 Création du fichier .env..."
    cp .env.example .env
fi

# Générer la clé d'application
echo "🔑 Génération de la clé d'application..."
php artisan key:generate --force

# Exécuter les migrations
echo "🗄️  Exécution des migrations..."
php artisan migrate --force

# Créer les liens symboliques
echo "🔗 Création des liens symboliques..."
php artisan storage:link || true

# Optimiser l'application
echo "⚡ Optimisation de l'application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Application prête!"

# Démarrer PHP-FPM
exec php-fpm

