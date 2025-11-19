# 🚀 Démarrage rapide avec Docker

## Installation en 3 étapes

### 1️⃣ Préparer l'environnement

```bash
# Copier le fichier .env
cp .env.example .env
```

**Modifiez `.env` avec ces valeurs :**
```env
DB_HOST=mysql
DB_DATABASE=czotick_master
DB_USERNAME=czotick_user
DB_PASSWORD=root

REDIS_HOST=redis
CACHE_DRIVER=redis
SESSION_DRIVER=redis
```

### 2️⃣ Lancer Docker

```bash
# Construire et démarrer
docker-compose up -d --build

# Attendre 10 secondes que MySQL démarre
```

### 3️⃣ Configurer Laravel

```bash
# Installer les dépendances
docker-compose exec app composer install
docker-compose exec app npm install

# Configurer Laravel
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
docker-compose exec app php artisan storage:link
```

## ✅ C'est prêt !

Accédez à : **http://localhost:8080**

## Commandes essentielles

```bash
# Voir les logs
docker-compose logs -f

# Arrêter
docker-compose down

# Redémarrer
docker-compose restart

# Accéder au shell
docker-compose exec app bash
```

Pour plus de détails, consultez `DOCKER_SETUP.md`

