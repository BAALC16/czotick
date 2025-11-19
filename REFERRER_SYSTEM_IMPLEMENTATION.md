# Système d'Apporteurs d'Affaire - Documentation

## Résumé de l'implémentation

Ce document décrit l'implémentation complète du système à 3 niveaux : Administrateur, Organisateur, et Apporteur d'affaire.

## Fichiers créés

### Migrations
1. `database/migrations/tenant/2025_11_17_000001_create_referrers_table.php` - Table des apporteurs d'affaire
2. `database/migrations/tenant/2025_11_17_000002_create_referrer_commissions_table.php` - Commissions par événement
3. `database/migrations/tenant/2025_11_17_000003_create_referrer_registrations_table.php` - Inscriptions liées aux apporteurs
4. `database/migrations/tenant/2025_11_17_000004_create_notifications_table.php` - Système de notifications
5. `database/migrations/tenant/2025_11_17_000005_add_referrer_code_to_events_table.php` - Code apporteur dans les événements
6. `database/migrations/tenant/2025_11_17_000006_update_users_role_enum.php` - Ajout des rôles 'organizer' et 'referrer'

### Modèles
1. `app/Models/Referrer.php` - Modèle apporteur d'affaire
2. `app/Models/ReferrerCommission.php` - Modèle commission
3. `app/Models/ReferrerRegistration.php` - Modèle relation inscription-apporteur
4. `app/Models/Notification.php` - Modèle notification

### Contrôleurs
1. `app/Http/Controllers/Organization/ReferrerController.php` - Gestion des apporteurs d'affaire

### Modifications apportées

#### EventController.php
- Ajout de la détection du code apporteur dans `showRegistrationForm`
- Passage du `referrerCode` à la vue
- Ajout de la méthode `notifyReferrersOfNewEvent` pour notifier les apporteurs lors de la création d'événement

#### PaymentController.php
- Ajout du `referrer_code` dans les métadonnées de paiement
- Ajout de la méthode `linkRegistrationToReferrer` pour lier les inscriptions aux apporteurs
- Calcul automatique des commissions lors de l'inscription

#### Event.php (Modèle)
- Ajout de `referrer_code` dans `$fillable`
- Ajout des relations `referrer()`, `referrerCommissions()`, `referrerRegistrations()`

#### Registration.php (Modèle)
- Ajout de la relation `referrerRegistration()`

#### Routes (web.php)
- Ajout des routes pour la gestion des apporteurs d'affaire sous `/org/{org_slug}/referrers`

#### Vues
- `resources/views/public/validation_form.blade.php` - Ajout du champ caché `referrer_code`

## Fonctionnalités implémentées

### 1. Gestion des apporteurs d'affaire (Organisateur)
- ✅ Création d'apporteurs d'affaire
- ✅ Attribution de commissions par événement (pourcentage ou montant fixe)
- ✅ Visualisation des gains des apporteurs
- ✅ Activation/désactivation des apporteurs

### 2. Tracking des inscriptions
- ✅ Détection du code apporteur dans l'URL (`?ref=CODE`)
- ✅ Enregistrement automatique de la relation inscription-apporteur
- ✅ Calcul automatique des commissions

### 3. Notifications
- ✅ Notification des apporteurs lors de la création d'événement
- ✅ Notification des apporteurs lors d'une nouvelle inscription
- ✅ Notification des organisateurs lors d'inscription via apporteur

### 4. Dashboard apporteur d'affaire
- ⏳ À créer : Dashboard avec événements et gains

### 5. Dashboard administrateur
- ⏳ À créer : Vue globale et création d'événements personnalisés

## Prochaines étapes

1. Créer les vues pour la gestion des apporteurs (index, create, show, assign-commission)
2. Créer le dashboard apporteur d'affaire
3. Créer le dashboard administrateur
4. Ajouter le `referrer_code` dans toutes les métadonnées de paiement (Orange, MTN, Moov, Wave)
5. Tester le système complet

## Utilisation

### Pour l'organisateur
1. Aller dans `/org/{org_slug}/referrers`
2. Créer un apporteur d'affaire
3. Lors de la création d'un événement, attribuer des commissions aux apporteurs
4. Voir les gains de chaque apporteur

### Pour l'apporteur d'affaire
1. Recevoir une notification lors de la création d'un événement
2. Partager l'URL avec le code : `/{org_slug}/{event_slug}?ref={referrer_code}`
3. Voir les inscriptions et gains dans son dashboard

### Pour le client
1. Cliquer sur le lien partagé par l'apporteur
2. S'inscrire normalement
3. Le système enregistre automatiquement l'apporteur associé

