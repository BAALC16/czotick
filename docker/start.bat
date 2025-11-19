@echo off
echo 🚀 Démarrage de l'application Czotick...

REM Attendre que MySQL soit prêt
echo ⏳ Attente de MySQL...
timeout /t 10 /nobreak

REM Installer les dependances si necessaire
if not exist "vendor" (
    echo 📦 Installation des dependances Composer...
    composer install --no-interaction --prefer-dist --optimize-autoloader
)

if not exist "node_modules" (
    echo 📦 Installation des dependances NPM...
    npm install
)

REM Copier le fichier .env si necessaire
if not exist ".env" (
    echo 📝 Creation du fichier .env...
    copy .env.example .env
)

REM Generer la cle d'application
echo 🔑 Generation de la cle d'application...
php artisan key:generate --force

REM Executer les migrations
echo 🗄️  Execution des migrations...
php artisan migrate --force

REM Creer les liens symboliques
echo 🔗 Creation des liens symboliques...
php artisan storage:link

REM Optimiser l'application
echo ⚡ Optimisation de l'application...
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ✅ Application prete!

