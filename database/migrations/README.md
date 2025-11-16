# Migrations SaaS Master Database

Ce dossier contient les migrations Laravel pour créer la base de données principale du système SaaS EventSaaS Platform.

## Structure de la base de données

### Tables principales

1. **system_settings** - Configuration système globale
2. **subscription_plans** - Plans d'abonnement disponibles
3. **organizations** - Organisations clientes
4. **system_admins** - Administrateurs système
5. **saas_users** - Utilisateurs des organisations
6. **admin_activity_logs** - Logs d'activité des administrateurs
7. **admin_dashboard_widgets** - Widgets du dashboard admin
8. **database_templates** - Templates de base de données par type d'organisation
9. **invoices** - Factures des organisations
10. **notifications** - Notifications système
11. **organization_logs** - Logs d'activité des organisations
12. **scheduled_tasks** - Tâches programmées
13. **registrations** - Inscriptions aux événements (table globale)

### Vues

1. **v_active_organizations** - Vue des organisations actives
2. **v_admin_active_users** - Vue des utilisateurs actifs (30 derniers jours)
3. **v_admin_organizations_overview** - Vue d'ensemble des organisations pour l'admin
4. **v_admin_system_metrics** - Métriques système pour l'admin
5. **v_pending_invoices** - Vue des factures en attente

## Installation

### 1. Exécuter les migrations

```bash
php artisan migrate
```

### 2. Exécuter les seeders

```bash
php artisan db:seed --class=SaasMasterSeeder
```

## Utilisation

### Connexion Super Admin

- **Email**: admin@votre-saas.com
- **Mot de passe**: admin123

### Types d'organisations supportés

- `jci` - Junior Chamber International
- `rotary` - Rotary Club
- `lions` - Lions Club
- `association` - Association générique
- `company` - Entreprise
- `other` - Autre

### Plans d'abonnement

1. **Essai Gratuit** (trial) - 1 événement, 50 participants max
2. **Basique** (basic) - 5 événements, 200 participants max
3. **Premium** (premium) - 20 événements, 1000 participants max
4. **Entreprise** (enterprise) - Illimité

## Architecture multi-tenant

Chaque organisation a sa propre base de données créée dynamiquement avec les templates appropriés. La table `registrations` dans la base principale sert de référence globale pour toutes les inscriptions.

## Tâches programmées

- **cleanup_data** - Nettoyage quotidien à 2h00
- **check_expired_trials** - Vérification des essais expirés à 9h00
- **generate_daily_metrics** - Génération des métriques quotidiennes à 1h00

## Notes importantes

- Les clés étrangères vers `event_id` et `ticket_type_id` dans la table `registrations` ne sont pas définies car ces tables existent dans les bases de données spécifiques aux organisations.
- Les vues utilisent des fonctions MySQL spécifiques comme `CURRENT_TIMESTAMP()` et `DATEDIFF()`.
- Les colonnes JSON utilisent la validation `CHECK (json_valid())` pour s'assurer de la validité des données.
