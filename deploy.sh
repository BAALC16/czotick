#!/bin/bash
# Script de déploiement automatisé pour Czotick
# Usage: ./deploy.sh

set -e  # Arrêter en cas d'erreur

echo "🚀 Déploiement de Czotick..."

# Vérifier que le fichier .env existe
if [ ! -f .env ]; then
    echo "❌ ERREUR: Le fichier .env n'existe pas !"
    echo "📝 Créez-le d'abord en suivant les instructions dans ENV_SETUP.md"
    exit 1
fi

# Récupérer les dernières modifications
echo "📥 Récupération des modifications..."
git pull origin main || git pull origin master

# Construire les images
echo "🔨 Construction des images Docker..."
docker compose -f docker-compose.yml -f docker-compose.prod.yml build

# Démarrer les services
echo "🚀 Démarrage des services..."
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d

# Attendre que MySQL soit prêt
echo "⏳ Attente du démarrage de MySQL..."
sleep 10

# Installer les dépendances Composer
echo "📦 Installation des dépendances Composer..."
docker compose exec -T app composer install --no-dev --optimize-autoloader

# Installer les dépendances NPM
echo "📦 Installation des dépendances NPM..."
docker compose exec -T app npm install

# Compiler les assets
echo "🎨 Compilation des assets..."
docker compose exec -T app npm run build

# Exécuter les migrations
echo "🗄️  Exécution des migrations..."
docker compose exec -T app php artisan migrate --force || true

# Créer le lien symbolique pour le storage
echo "🔗 Création du lien symbolique storage..."
docker compose exec -T app php artisan storage:link || true

# Optimiser l'application
echo "⚡ Optimisation de l'application..."
docker compose exec -T app php artisan config:cache
docker compose exec -T app php artisan route:cache
docker compose exec -T app php artisan view:cache
docker compose exec -T app php artisan event:cache || true

# Vérifier l'état des conteneurs
echo "✅ Vérification de l'état des conteneurs..."
docker compose ps

echo ""
echo "✅ Déploiement terminé avec succès !"
echo "🌐 Votre application devrait être accessible sur votre domaine"

