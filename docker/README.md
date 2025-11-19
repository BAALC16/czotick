# Guide de déploiement Docker pour Czotick

## Prérequis

- Docker Desktop installé (Windows/Mac) ou Docker Engine (Linux)
- Docker Compose v2 ou supérieur

## Démarrage rapide

### 1. Cloner et configurer

```bash
# Copier le fichier .env
cp .env.example .env

# Modifier les variables d'environnement dans .env
# Assurez-vous que les paramètres de base de données correspondent à docker-compose.yml
```

### 2. Configuration du .env

Modifiez votre fichier `.env` avec ces valeurs :

```env
APP_NAME=Czotick
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8080

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=czotick_master
DB_USERNAME=czotick_user
DB_PASSWORD=root

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### 3. Construire et démarrer les conteneurs

```bash
# Construire les images
docker-compose build

# Démarrer les services
docker-compose up -d

# Voir les logs
docker-compose logs -f
```

### 4. Installer les dépendances et configurer Laravel

```bash
# Entrer dans le conteneur app
docker-compose exec app bash

# Installer les dépendances Composer
composer install

# Installer les dépendances NPM
npm install

# Compiler les assets
npm run dev
# ou pour la production
npm run build

# Générer la clé d'application
php artisan key:generate

# Exécuter les migrations
php artisan migrate

# Créer le lien symbolique pour le storage
php artisan storage:link

# Optimiser l'application
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. Accéder à l'application

- **Application** : http://localhost:8080
- **MySQL** : localhost:3307
- **Redis** : localhost:6379

## Commandes utiles

### Gestion des conteneurs

```bash
# Démarrer les services
docker-compose up -d

# Arrêter les services
docker-compose down

# Redémarrer un service spécifique
docker-compose restart app

# Voir les logs
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f mysql

# Arrêter et supprimer les volumes (⚠️ supprime les données)
docker-compose down -v
```

### Commandes Artisan

```bash
# Exécuter une commande artisan
docker-compose exec app php artisan migrate
docker-compose exec app php artisan tinker
docker-compose exec app php artisan queue:work
```

### Accès aux bases de données

```bash
# Accéder à MySQL
docker-compose exec mysql mysql -u root -proot

# Accéder à Redis
docker-compose exec redis redis-cli
```

### Nettoyage

```bash
# Nettoyer les conteneurs et volumes
docker-compose down -v

# Nettoyer les images
docker-compose down --rmi all

# Nettoyer complètement (⚠️ supprime tout)
docker system prune -a --volumes
```

## Structure des services

- **app** : Application PHP-FPM (port 9000)
- **nginx** : Serveur web (port 8080)
- **mysql** : Base de données MySQL (port 3307)
- **redis** : Cache Redis (port 6379)
- **queue** : Worker pour les queues Laravel

## Dépannage

### Problème de permissions

```bash
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Réinitialiser la base de données

```bash
docker-compose exec app php artisan migrate:fresh --seed
```

### Vider les caches

```bash
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
```

### Voir les logs

```bash
# Logs de l'application
docker-compose logs app

# Logs de Nginx
docker-compose logs nginx

# Logs de MySQL
docker-compose logs mysql
```

## Production

Pour la production, modifiez `docker-compose.yml` :

1. Changez `APP_DEBUG=false` dans `.env`
2. Utilisez des secrets pour les mots de passe
3. Configurez SSL/TLS pour Nginx
4. Utilisez un volume externe pour MySQL
5. Configurez des backups automatiques

## Support

Pour toute question ou problème, consultez la documentation Laravel ou Docker.

