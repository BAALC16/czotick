# Configuration des Variables d'Environnement

## ⚠️ Important

Le fichier `.env` est dans `.gitignore` (normal pour la sécurité), mais **Docker Compose charge automatiquement** le fichier `.env` s'il existe dans le même répertoire que `docker-compose.yml`.

## Création du fichier .env

Créez un fichier `.env` à la racine du projet avec le contenu suivant :

```env
# ============================================
# APPLICATION
# ============================================
APP_NAME=Czotick
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8080
APP_ADMIN_NAME=Czotick Admin

# ============================================
# DOCKER COMPOSE VARIABLES
# Ces variables sont utilisées par docker-compose.yml
# ============================================

# MySQL Configuration (pour Docker)
MYSQL_DATABASE=saas_master
MYSQL_USER=saas_master
MYSQL_PASSWORD=root
MYSQL_ROOT_PASSWORD=root
MYSQL_PORT=3307

# Redis Configuration (pour Docker)
REDIS_PORT=6379
REDIS_PASSWORD=

# Timezone
TZ=Africa/Abidjan

# ============================================
# DATABASE CONFIGURATION (Laravel)
# ============================================
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=saas_master
DB_USERNAME=saas_master
DB_PASSWORD=root

# Pour la connexion saas_master
SAAS_MASTER_DB=saas_master

# Pour les tenants (optionnel)
TENANT_DB_DATABASE=tenant_default
TENANT_DB_USERNAME=tenant_default
TENANT_DB_PASSWORD=Une@Vie@2route

# ============================================
# REDIS CONFIGURATION (Laravel)
# ============================================
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

# ============================================
# CACHE & SESSIONS
# ============================================
CACHE_DRIVER=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120
QUEUE_CONNECTION=redis

# ============================================
# MAIL CONFIGURATION
# ============================================
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=noreply@czotick.com
MAIL_FROM_NAME="${APP_NAME}"

# ============================================
# FILESYSTEM
# ============================================
FILESYSTEM_DISK=local

# ============================================
# BROADCASTING
# ============================================
BROADCAST_DRIVER=log

# ============================================
# LOGGING
# ============================================
LOG_CHANNEL=stack
LOG_LEVEL=debug
```

## Comment Docker Compose charge le .env

Docker Compose charge **automatiquement** le fichier `.env` pour les substitutions de variables dans `docker-compose.yml`.

Par exemple, dans `docker-compose.yml` :
```yaml
MYSQL_PASSWORD: ${MYSQL_PASSWORD:-root}
```

Docker Compose va :
1. Chercher la variable `MYSQL_PASSWORD` dans le fichier `.env`
2. Si elle existe, utiliser sa valeur
3. Si elle n'existe pas, utiliser la valeur par défaut `root`

## Vérification

Pour vérifier que Docker Compose charge bien votre `.env` :

```bash
# Afficher les variables chargées (sans démarrer les conteneurs)
docker compose config

# Vérifier une variable spécifique
docker compose config | grep MYSQL_PASSWORD
```

## Pour la Production

Pour la production, modifiez ces valeurs dans votre `.env` :

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-domaine.com

# Mots de passe forts
MYSQL_PASSWORD=votre_mot_de_passe_fort
MYSQL_ROOT_PASSWORD=votre_mot_de_passe_root_fort
REDIS_PASSWORD=votre_mot_de_passe_redis_fort

# Ne pas exposer les ports (retirer ou commenter)
# MYSQL_PORT=3307
# REDIS_PORT=6379
```

Puis utilisez :
```bash
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d
```

## Dépannage

### Le .env n'est pas chargé

1. Vérifiez que le fichier `.env` existe à la racine du projet (même niveau que `docker-compose.yml`)
2. Vérifiez qu'il n'y a pas d'espaces autour du `=` dans les variables
3. Vérifiez qu'il n'y a pas de guillemets inutiles (sauf pour les valeurs avec espaces)
4. Utilisez `docker compose config` pour voir les valeurs chargées

### Les variables ne sont pas prises en compte

Si Docker Compose ne charge pas vos variables, vous pouvez forcer le chargement :

```bash
# Spécifier explicitement le fichier .env
docker compose --env-file .env up -d
```

