# Vérification du fichier .env

## Variables requises pour Docker Compose

Vérifiez que votre fichier `.env` contient au minimum ces variables :

```env
# Variables Docker Compose (obligatoires)
MYSQL_DATABASE=saas_master
MYSQL_USER=saas_master
MYSQL_PASSWORD=votre_mot_de_passe
MYSQL_ROOT_PASSWORD=votre_mot_de_passe_root
MYSQL_PORT=3307

REDIS_PORT=6379
REDIS_PASSWORD=

TZ=Africa/Abidjan

# Variables Laravel (obligatoires)
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=saas_master
DB_USERNAME=saas_master
DB_PASSWORD=votre_mot_de_passe

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

## Vérification rapide

Pour vérifier que Docker Compose charge bien vos variables :

```powershell
# Afficher la configuration complète
docker compose config

# Vérifier une variable spécifique
docker compose config | Select-String "MYSQL_PASSWORD"
```

## Important

-   Les valeurs dans `.env` doivent correspondre entre :
    -   `MYSQL_PASSWORD` (pour Docker) = `DB_PASSWORD` (pour Laravel)
    -   `MYSQL_DATABASE` (pour Docker) = `DB_DATABASE` (pour Laravel)
    -   `MYSQL_USER` (pour Docker) = `DB_USERNAME` (pour Laravel)

## Pour la production

Assurez-vous d'utiliser des **mots de passe forts** et de ne pas exposer les ports MySQL/Redis publiquement.
