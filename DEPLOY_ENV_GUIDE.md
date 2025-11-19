# Guide de Déploiement - Gestion du fichier .env

## 🔒 Pourquoi le .env est ignoré ?

Le fichier `.env` contient des **secrets** (mots de passe, clés API) et ne doit **JAMAIS** être versionné dans Git. C'est une bonne pratique de sécurité.

## 📋 Méthodes de Déploiement

### Méthode 1 : Création manuelle sur le serveur (Recommandé pour débuter)

**Sur votre VPS :**

```bash
# 1. Cloner le projet
cd /var/www
git clone VOTRE_REPO czotick
cd czotick

# 2. Créer le fichier .env depuis ENV_SETUP.md
nano .env

# 3. Copier-coller le contenu depuis ENV_SETUP.md et adapter les valeurs
# (mots de passe forts, URL de production, etc.)

# 4. Déployer
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build
```

**Avantages :** Simple, contrôle total  
**Inconvénients :** Manuel, à refaire à chaque nouveau serveur

---

### Méthode 2 : Script de déploiement automatisé

Créez un script `deploy.sh` sur votre serveur :

```bash
#!/bin/bash
# deploy.sh

set -e

echo "🚀 Déploiement de Czotick..."

# 1. Récupérer les dernières modifications
git pull origin main

# 2. Vérifier si .env existe, sinon le créer
if [ ! -f .env ]; then
    echo "⚠️  Fichier .env manquant. Créez-le d'abord !"
    echo "Consultez ENV_SETUP.md pour le template"
    exit 1
fi

# 3. Construire et démarrer
docker compose -f docker-compose.yml -f docker-compose.prod.yml build
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d

# 4. Installer les dépendances
docker compose exec -T app composer install --no-dev --optimize-autoloader
docker compose exec -T app npm install
docker compose exec -T app npm run build

# 5. Migrations et optimisations
docker compose exec -T app php artisan migrate --force
docker compose exec -T app php artisan config:cache
docker compose exec -T app php artisan route:cache
docker compose exec -T app php artisan view:cache

echo "✅ Déploiement terminé !"
```

**Utilisation :**
```bash
chmod +x deploy.sh
./deploy.sh
```

---

### Méthode 3 : Variables d'environnement système

Au lieu d'un fichier `.env`, utilisez les variables d'environnement du système :

**1. Créer un fichier `/etc/environment` ou `~/.bashrc` :**
```bash
export MYSQL_PASSWORD="votre_mot_de_passe"
export MYSQL_ROOT_PASSWORD="votre_mot_de_passe_root"
export DB_PASSWORD="votre_mot_de_passe"
# ... etc
```

**2. Modifier `docker-compose.yml` pour utiliser `env_file` :**
```yaml
services:
  mysql:
    env_file:
      - /etc/czotick.env  # Fichier avec les variables
```

**Avantages :** Centralisé, facile à gérer  
**Inconvénients :** Plus complexe à configurer

---

### Méthode 4 : Gestionnaire de secrets (Production avancée)

Pour les environnements critiques, utilisez :

- **HashiCorp Vault**
- **AWS Secrets Manager**
- **Azure Key Vault**
- **Docker Secrets** (pour Docker Swarm)

**Exemple avec Docker Secrets :**
```yaml
services:
  mysql:
    secrets:
      - mysql_password
    environment:
      MYSQL_PASSWORD_FILE: /run/secrets/mysql_password
```

---

### Méthode 5 : Template .env.example versionné

Créez un fichier `.env.example` (versionné) qui sert de template :

```bash
# Sur votre machine locale
cp .env .env.example
# Retirer les valeurs sensibles et les remplacer par des placeholders
# Puis commit .env.example

# Sur le serveur
cp .env.example .env
nano .env  # Remplir avec les vraies valeurs
```

**⚠️ Important :** Ne jamais commiter le vrai `.env` !

---

## 🚀 Workflow de Déploiement Recommandé

### Première installation

```bash
# 1. Sur le VPS
cd /var/www
git clone VOTRE_REPO czotick
cd czotick

# 2. Créer le .env (une seule fois)
nano .env
# Copier le contenu depuis ENV_SETUP.md et adapter

# 3. Déployer
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build

# 4. Configuration Laravel
docker compose exec app php artisan key:generate
docker compose exec app composer install --no-dev
docker compose exec app npm install && npm run build
docker compose exec app php artisan migrate --force
docker compose exec app php artisan storage:link
```

### Mises à jour suivantes

```bash
# 1. Récupérer les modifications
git pull origin main

# 2. Reconstruire et redémarrer
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build

# 3. Migrations si nécessaire
docker compose exec app php artisan migrate --force

# 4. Vider les caches
docker compose exec app php artisan config:clear
docker compose exec app php artisan cache:clear
```

---

## 🔐 Sécurité du .env en Production

### Bonnes pratiques

1. **Permissions restrictives :**
   ```bash
   chmod 600 .env  # Lecture/écriture uniquement pour le propriétaire
   ```

2. **Sauvegarde sécurisée :**
   ```bash
   # Sauvegarder le .env dans un endroit sécurisé (chiffré)
   gpg -c .env  # Crée .env.gpg (chiffré)
   ```

3. **Rotation des secrets :**
   - Changez les mots de passe régulièrement
   - Utilisez des mots de passe forts (min 16 caractères, majuscules, minuscules, chiffres, symboles)

4. **Ne jamais :**
   - ❌ Commiter le `.env` dans Git
   - ❌ Partager le `.env` par email non chiffré
   - ❌ Stocker le `.env` dans un cloud non sécurisé
   - ❌ Utiliser le même `.env` en dev et prod

---

## 📝 Checklist de Déploiement

Avant de déployer, vérifiez :

- [ ] Le fichier `.env` existe sur le serveur
- [ ] Tous les mots de passe sont forts et uniques
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL` pointe vers votre domaine
- [ ] Les ports MySQL/Redis ne sont pas exposés publiquement
- [ ] Les permissions du `.env` sont restrictives (600)
- [ ] HTTPS est configuré
- [ ] Le firewall est activé

---

## 🆘 Dépannage

### Le .env n'est pas chargé

```bash
# Vérifier que le fichier existe
ls -la .env

# Vérifier les permissions
chmod 600 .env

# Vérifier le contenu (sans afficher les secrets)
grep -v "PASSWORD\|SECRET\|KEY" .env
```

### Variables non prises en compte

```bash
# Forcer le rechargement
docker compose down
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d

# Vérifier les variables chargées
docker compose config | grep MYSQL
```

---

## 📚 Ressources

- `ENV_SETUP.md` - Template complet du .env
- `DEPLOYMENT_PRODUCTION.md` - Guide de déploiement complet
- `VERIFY_ENV.md` - Vérification des variables

