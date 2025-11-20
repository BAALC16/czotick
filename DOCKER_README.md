# 🐳 Déploiement Docker - Czotick

## 📁 Structure des fichiers

```
.
├── Dockerfile                 # Image PHP-FPM avec toutes les extensions
├── docker-compose.yml         # Configuration Docker Compose
└── docker/
    ├── nginx.conf            # Configuration Nginx
    └── php.ini               # Configuration PHP
```

## 🚀 Démarrage rapide

### 1. Créer le fichier .env

Créez un fichier `.env` à la racine avec :

```env
# Docker Compose
MYSQL_DATABASE=saas_master
MYSQL_USER=saas_master
MYSQL_PASSWORD=root
MYSQL_ROOT_PASSWORD=root
MYSQL_PORT=3307

REDIS_PORT=6379
REDIS_PASSWORD=

TZ=Africa/Abidjan

# Laravel
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=saas_master
DB_USERNAME=saas_master
DB_PASSWORD=root

REDIS_HOST=redis
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### 2. Construire et démarrer

```bash
# Construire les images
docker compose build

# Démarrer les services
docker compose up -d

# Voir les logs
docker compose logs -f
```

### 3. Configurer Laravel

```bash
# Entrer dans le conteneur
docker compose exec app bash

# Installer les dépendances
composer install
npm install
npm run build

# Générer la clé d'application
php artisan key:generate

# Exécuter les migrations
php artisan migrate

# Créer le lien symbolique
php artisan storage:link
```

### 4. Accéder à l'application

- **Application** : http://localhost:8080
- **MySQL** : localhost:3307
- **Redis** : localhost:6379

## 📋 Services disponibles

- **app** : PHP-FPM 8.1 avec extensions (MySQL, Redis, GD, etc.)
- **nginx** : Serveur web Nginx
- **mysql** : Base de données MySQL 8.0
- **redis** : Cache Redis
- **queue** : Worker Laravel pour les queues

## 🔧 Commandes utiles

```bash
# Démarrer les services
docker compose up -d

# Arrêter les services
docker compose down

# Redémarrer un service
docker compose restart app

# Voir les logs
docker compose logs -f app

# Accéder au shell du conteneur
docker compose exec app bash

# Reconstruire les images
docker compose build --no-cache
```

## 🔒 Production

Pour la production, utilisez des mots de passe forts et ne exposez pas MySQL/Redis publiquement.

Modifiez `docker-compose.yml` pour retirer les ports MySQL et Redis en production.

## 📝 Notes

- Le fichier `docker/php.ini` est monté comme volume dans le conteneur
- Le fichier `docker/nginx.conf` remplace la configuration par défaut de Nginx
- Les données MySQL et Redis sont persistées dans des volumes Docker

