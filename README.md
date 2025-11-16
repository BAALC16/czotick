# Projet : Immo

###### Version : 2021.0-dev

|ENVIRONNEMENT DE DEVELOPPEMENT|
|:------|
|Architecture:  **Model(/app/Models) Vue(/resources/views) Controller(/app/Http/Controllers)**|
| Framework: **Laravel**|
| Framework version: **8.75.0**|
| Dependencies manager: **Composer** |

## Configuration requise pour l'environnement de développement/production
- Apache 2.4+ ou Nginx 1.14+
- PHP ^7.3|^8.0, PHP-cli
- Composer 2.0
- MySQL 5+
- Git

## Installation
1. Cloner le dépôt dans votre dossier web : `git clone <url vers le dépôt> immo`
2. Aller dans le dossier `immo` : `cd immo`
3. Installer/mettre à jour les dépendances : `composer install` ou `composer update`
4. Créer une base de données depuis le gestionnaire de base de données (_phpMyAdmin_ par exemple)
5. Copier le `.env.example` en `.env` et modifier le fichier `.env` avec le nom de l'application, l'environnement (development/production), les identifiants de la base de données, les paramètres d'e-mail
6. Générer la clé d'application : `php artisan key:generate`
7. Ajout de la dépendance doctrine/dbal: `composer require doctrine/dbal`
8. Migrer les tables de la base de données : `php artisan migrate --seed --force`
9. Créer un symlink pour /storage : `php artisan storage:link`
10. [Production] Mettre en cache la Configuration : `php artisan config:cache`
11. [Développement] Démarrer le serveur de développement : `php artisan serve`

## Intégration des mises à jour
Pour prendre les changements depuis le dépôt, sauvegarder entièrement la BDD (si nécessaire) et faire : `git fetch && git pull` puis `composer install` et enfin `php artisan migrate --seed --force`.
