# Guide de Déploiement en Production

## ⚠️ Sécurité - Points Critiques

Avant de déployer en production, assurez-vous de :

1. **Changer tous les mots de passe par défaut**
2. **Ne pas exposer MySQL et Redis publiquement** (déjà configuré dans `docker-compose.prod.yml`)
3. **Utiliser HTTPS** avec des certificats SSL valides
4. **Configurer un firewall** sur votre VPS
5. **Utiliser des variables d'environnement** pour les secrets

## Configuration des Variables d'Environnement

Créez un fichier `.env` sur votre VPS avec les variables suivantes :

```env
# Application
APP_NAME=Czotick
APP_ENV=production
APP_KEY=base64:VOTRE_CLE_GENEREE_AVEC_PHP_ARTISAN_KEY_GENERATE
APP_DEBUG=false
APP_URL=https://votre-domaine.com

# Base de données MySQL
MYSQL_DATABASE=saas_master
MYSQL_USER=saas_master
MYSQL_PASSWORD=VOTRE_MOT_DE_PASSE_FORT_ICI
MYSQL_ROOT_PASSWORD=VOTRE_MOT_DE_PASSE_ROOT_FORT_ICI
MYSQL_PORT=3307  # Seulement pour le développement local

# Configuration Laravel Database
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=saas_master
DB_USERNAME=saas_master
DB_PASSWORD=VOTRE_MOT_DE_PASSE_FORT_ICI

# Redis
REDIS_HOST=redis
REDIS_PASSWORD=VOTRE_MOT_DE_PASSE_REDIS_FORT_ICI
REDIS_PORT=6379

# Cache et Sessions
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Timezone
TZ=Africa/Abidjan

# Mail (configurez selon votre fournisseur)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=noreply@votre-domaine.com
MAIL_FROM_NAME="${APP_NAME}"
```

## Déploiement sur VPS

### 1. Préparer le serveur

```bash
# Mettre à jour le système
sudo apt update && sudo apt upgrade -y

# Installer Docker et Docker Compose
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
sudo apt install docker-compose-plugin -y

# Ajouter votre utilisateur au groupe docker
sudo usermod -aG docker $USER
```

### 2. Cloner le projet

```bash
cd /var/www
git clone VOTRE_REPO czotick
cd czotick
```

### 3. Configurer l'environnement

**⚠️ IMPORTANT :** Le fichier `.env` n'est pas versionné (sécurité). Vous devez le créer manuellement sur le serveur.

```bash
# Créer le fichier .env
nano .env

# Copier le contenu depuis ENV_SETUP.md et adapter avec vos valeurs de production :
# - Mots de passe forts
# - APP_URL=https://votre-domaine.com
# - APP_ENV=production
# - APP_DEBUG=false
```

**Voir `DEPLOY_ENV_GUIDE.md` pour les différentes méthodes de gestion du .env en production.**

### 4. Construire et démarrer avec la configuration de production

```bash
# Construire les images
docker compose -f docker-compose.yml -f docker-compose.prod.yml build

# Démarrer les services
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d
```

### 5. Configuration Laravel

```bash
# Entrer dans le conteneur
docker compose exec app bash

# Installer les dépendances
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Générer la clé d'application
php artisan key:generate

# Exécuter les migrations
php artisan migrate --force

# Créer le lien symbolique pour le storage
php artisan storage:link

# Optimiser l'application pour la production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 6. Configuration Nginx avec SSL

1. Installez Certbot pour obtenir des certificats SSL gratuits :

```bash
sudo apt install certbot python3-certbot-nginx -y
```

2. Configurez votre domaine dans Nginx (sur le serveur hôte, pas dans Docker) :

```nginx
server {
    listen 80;
    server_name votre-domaine.com www.votre-domaine.com;

    location / {
        proxy_pass http://localhost:80;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

3. Obtenez un certificat SSL :

```bash
sudo certbot --nginx -d votre-domaine.com -d www.votre-domaine.com
```

## Sécurité Supplémentaire

### Firewall (UFW)

```bash
# Autoriser SSH
sudo ufw allow 22/tcp

# Autoriser HTTP et HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Activer le firewall
sudo ufw enable
```

### Sauvegardes Automatiques

Créez un script de sauvegarde (`/usr/local/bin/backup-czotick.sh`) :

```bash
#!/bin/bash
BACKUP_DIR="/backups/czotick"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR

# Sauvegarder la base de données
docker compose exec -T mysql mysqldump -u root -p$MYSQL_ROOT_PASSWORD saas_master > $BACKUP_DIR/db_$DATE.sql

# Sauvegarder les fichiers
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/czotick/storage

# Supprimer les sauvegardes de plus de 30 jours
find $BACKUP_DIR -type f -mtime +30 -delete
```

Ajoutez au crontab :

```bash
# Sauvegarde quotidienne à 2h du matin
0 2 * * * /usr/local/bin/backup-czotick.sh
```

## Monitoring

### Vérifier les logs

```bash
# Logs de tous les services
docker compose logs -f

# Logs d'un service spécifique
docker compose logs -f app
docker compose logs -f nginx
```

### Vérifier l'état des conteneurs

```bash
docker compose ps
```

## Mise à Jour

```bash
# Arrêter les services
docker compose down

# Récupérer les dernières modifications
git pull

# Reconstruire les images
docker compose -f docker-compose.yml -f docker-compose.prod.yml build

# Redémarrer
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d

# Exécuter les migrations si nécessaire
docker compose exec app php artisan migrate --force

# Vider les caches
docker compose exec app php artisan config:clear
docker compose exec app php artisan cache:clear
```

## Dépannage

### Le conteneur app ne démarre pas

```bash
docker compose logs app
```

### Problème de permissions

```bash
docker compose exec app chown -R www-data:www-data /var/www/html/storage
docker compose exec app chmod -R 775 /var/www/html/storage
```

### Redémarrer un service spécifique

```bash
docker compose restart app
docker compose restart nginx
```

