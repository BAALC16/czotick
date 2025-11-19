# 🐳 Guide de déploiement Docker - Czotick

## 📋 Prérequis

- **Docker Desktop** (Windows/Mac) ou **Docker Engine** + **Docker Compose** (Linux)
- **Git** (pour cloner le projet)
- Au moins **4GB de RAM** disponible

## 🚀 Démarrage rapide

### 1. Configuration initiale

```bash
# 1. Copier le fichier .env.example vers .env
cp .env.example .env

# 2. Modifier le fichier .env avec ces valeurs pour Docker:
```

**Variables importantes dans `.env` :**
```env
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

### 2. Installation complète (automatique)

**Avec Make (Linux/Mac) :**
```bash
make install
```

**Avec PowerShell (Windows) :**
```powershell
# Construire et démarrer
docker-compose build
docker-compose up -d

# Installer les dépendances
docker-compose exec app composer install
docker-compose exec app npm install

# Configurer Laravel
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
docker-compose exec app php artisan storage:link
```

### 3. Installation manuelle étape par étape

```bash
# 1. Construire les images Docker
docker-compose build

# 2. Démarrer les conteneurs
docker-compose up -d

# 3. Vérifier que tout fonctionne
docker-compose ps

# 4. Installer les dépendances Composer
docker-compose exec app composer install

# 5. Installer les dépendances NPM
docker-compose exec app npm install

# 6. Compiler les assets (développement)
docker-compose exec app npm run dev

# 7. Générer la clé d'application
docker-compose exec app php artisan key:generate

# 8. Exécuter les migrations
docker-compose exec app php artisan migrate

# 9. Créer le lien symbolique pour le storage
docker-compose exec app php artisan storage:link

# 10. Optimiser l'application
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

### 4. Accéder à l'application

- 🌐 **Application** : http://localhost:8080
- 🗄️ **MySQL** : `localhost:3307` (user: `root`, password: `root`)
- 🔴 **Redis** : `localhost:6379`

## 📚 Commandes utiles

### Gestion des conteneurs

```bash
# Démarrer
docker-compose up -d

# Arrêter
docker-compose down

# Redémarrer
docker-compose restart

# Voir les logs
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f mysql

# Voir le statut
docker-compose ps
```

### Commandes Artisan

```bash
# Exécuter une commande artisan
docker-compose exec app php artisan [commande]

# Exemples:
docker-compose exec app php artisan migrate
docker-compose exec app php artisan tinker
docker-compose exec app php artisan queue:work
docker-compose exec app php artisan make:controller TestController
```

### Accès aux services

```bash
# Accéder au shell du conteneur app
docker-compose exec app bash

# Accéder à MySQL
docker-compose exec mysql mysql -u root -proot

# Accéder à Redis
docker-compose exec redis redis-cli
```

### Nettoyage et maintenance

```bash
# Vider les caches
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

# Réinitialiser la base de données
docker-compose exec app php artisan migrate:fresh --seed

# Nettoyer les conteneurs et volumes (⚠️ supprime les données)
docker-compose down -v
```

## 🔧 Configuration

### Ports

Les ports par défaut sont :
- **8080** : Nginx (application web)
- **3307** : MySQL
- **6379** : Redis

Pour changer les ports, modifiez `docker-compose.yml` :

```yaml
nginx:
  ports:
    - "8080:80"  # Changez 8080 par le port souhaité
```

### Volumes

Les données sont persistées dans des volumes Docker :
- `mysql_data` : Base de données MySQL
- `redis_data` : Données Redis

### Variables d'environnement

Modifiez les variables dans `docker-compose.yml` ou créez un fichier `.env` pour Docker.

## 🐛 Dépannage

### Problème de permissions

```bash
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Erreur de connexion à la base de données

1. Vérifiez que MySQL est démarré : `docker-compose ps`
2. Vérifiez les variables dans `.env`
3. Attendez quelques secondes que MySQL soit complètement démarré

### Erreur "Port already in use"

Changez les ports dans `docker-compose.yml` ou arrêtez les services qui utilisent ces ports.

### Réinitialiser complètement

```bash
# Arrêter et supprimer tout
docker-compose down -v

# Supprimer les images
docker-compose down --rmi all

# Reconstruire
docker-compose build --no-cache
docker-compose up -d
```

## 🚀 Production

Pour la production, suivez ces recommandations :

1. **Sécurité** :
   - Changez tous les mots de passe par défaut
   - Utilisez des secrets Docker ou un gestionnaire de secrets
   - Activez HTTPS avec SSL/TLS

2. **Performance** :
   - Activez OPcache (déjà configuré)
   - Utilisez Redis pour le cache
   - Configurez les workers de queue

3. **Monitoring** :
   - Configurez les logs centralisés
   - Utilisez un outil de monitoring (Prometheus, Grafana)

4. **Backups** :
   - Configurez des backups automatiques de MySQL
   - Sauvegardez les volumes Docker

## 📝 Notes importantes

- Les fichiers du projet sont montés en volume, donc les modifications sont immédiates
- Pour les changements de configuration PHP/Nginx, redémarrez les conteneurs
- Les données de la base de données persistent même après `docker-compose down` (sauf si vous utilisez `-v`)

## 🆘 Support

Pour toute question ou problème :
1. Vérifiez les logs : `docker-compose logs -f`
2. Consultez la documentation Docker
3. Vérifiez les issues GitHub du projet

