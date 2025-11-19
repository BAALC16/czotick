-- Script d'initialisation de la base de données
-- Ce script s'exécute automatiquement lors de la première création du conteneur

-- Créer la base de données principale si elle n'existe pas
CREATE DATABASE IF NOT EXISTS czotick_master CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Accorder les privilèges
GRANT ALL PRIVILEGES ON czotick_master.* TO 'czotick_user'@'%';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'%';

FLUSH PRIVILEGES;

