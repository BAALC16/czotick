# 🎯 EventSaaS Platform - Système d'Événements Modulaire

## 📋 Vue d'ensemble

EventSaaS Platform est un système SaaS complet pour la gestion d'événements avec support multi-organisations, packs d'abonnement flexibles et création dynamique de bases de données.

## 🏗️ Architecture

### Base de données principale (saas_master)
- **Organisations** : Gestion des clients SaaS
- **Packs d'abonnement** : Configuration des fonctionnalités
- **Types d'événements** : Catalogue des types supportés
- **Pays supportés** : Gestion multi-pays
- **Inscriptions** : Processus de création d'organisations

### Bases de données des organisations (org_*)
- **Événements** : Gestion des événements spécifiques
- **Inscriptions** : Participants aux événements
- **Tickets** : Types et ventes de tickets
- **Paiements** : Transactions financières
- **Notifications** : Communications multi-canaux

## 💰 Système de tarification

### Pack Standard (5% de commission)
- ✅ Tickets par email
- ✅ Design basique
- ✅ 3 événements maximum
- ✅ 100 participants/événement
- ✅ Côte d'Ivoire uniquement

### Pack Premium (7% de commission)
- ✅ Tickets par email + WhatsApp
- ✅ Design personnalisé
- ✅ Achat multi-tickets
- ✅ Support multi-pays (7 pays)
- ✅ Domaine personnalisé
- ✅ Analytics avancées
- ✅ API access
- ✅ Support prioritaire

### Pack Personnalisé (Négociable)
- ✅ Toutes les fonctionnalités
- ✅ Limites illimitées
- ✅ Support mondial
- ✅ Configuration sur mesure

## 🌍 Pays supportés

- 🇨🇮 **Côte d'Ivoire** (+225) - XOF
- 🇧🇯 **Bénin** (+229) - XOF
- 🇹🇬 **Togo** (+228) - XOF
- 🇸🇳 **Sénégal** (+221) - XOF
- 🇨🇲 **Cameroun** (+237) - XAF
- 🇲🇱 **Mali** (+223) - XOF
- 🇧🇫 **Burkina Faso** (+226) - XOF

## 🎫 Types d'événements

1. **Concert & Spectacle** 🎵
2. **Formation** 🎓
3. **Conférence** 🎤
4. **Festival** 🎪
5. **Soirée** 🥂
6. **Gastronomie** 🍽️
7. **Tourisme** 🗺️
8. **Sport** 🏃
9. **Religion** 🙏
10. **Mariage** 💕
11. **Autres** ➕

## 🚀 Installation et Configuration

### 1. Migrations principales
```bash
php artisan migrate
php artisan db:seed --class=EventSystemSeeder
```

### 2. Création d'organisation via interface
- Accéder à `/register`
- Remplir le formulaire d'inscription
- Sélectionner le pack souhaité
- Configuration automatique de la base de données

### 3. Création d'organisation via commande
```bash
php artisan organization:create "Mon Organisation" "mon-org" "company" "John Doe" "john@example.com" --pack=premium --subdomain=mon-org
```

### 4. Migration manuelle d'une organisation
```bash
php artisan organization:migrate org_mon_org --seed
```

## 🔧 Fonctionnalités avancées

### Achat multi-tickets
- Configuration par événement
- Limite configurable par pack
- Gestion des achats groupés

### Notifications multi-canaux
- 📧 **Email** (tous les packs)
- 📱 **WhatsApp** (Premium/Custom)
- 📲 **SMS** (Premium/Custom)

### Système de paiement
- Mobile Money (tous pays)
- Virement bancaire
- Carte de crédit (Premium/Custom)
- PayPal (Premium/Custom)
- Crypto (Custom uniquement)

## 📁 Structure des fichiers

```
database/
├── migrations/
│   ├── tenant/                    # Migrations spécifiques aux organisations
│   │   ├── create_tenant_event_types_table.php
│   │   ├── add_event_features_to_events_table.php
│   │   ├── create_multi_ticket_purchases_table.php
│   │   ├── create_ticket_notifications_table.php
│   │   └── create_tenant_supported_countries_table.php
│   └── [migrations principales]  # Migrations SaaS master
├── seeders/
│   ├── EventSystemSeeder.php      # Données initiales SaaS
│   └── TenantEventSystemSeeder.php # Données initiales organisations
```

## 🎯 Utilisation

### Pour les organisateurs
1. **Inscription** : Créer un compte via `/register`
2. **Configuration** : Choisir le pack et les fonctionnalités
3. **Création d'événements** : Utiliser l'interface d'administration
4. **Gestion des tickets** : Vente et validation
5. **Analytics** : Suivi des performances

### Pour les administrateurs SaaS
1. **Gestion des organisations** : Interface super-admin
2. **Monitoring** : Tableaux de bord et métriques
3. **Support** : Gestion des tickets et problèmes
4. **Facturation** : Calcul automatique des commissions

## 🔒 Sécurité

- Isolation des données par organisation
- Chiffrement des informations sensibles
- Validation des paiements
- Audit des actions utilisateurs
- Conformité RGPD

## 📊 Monitoring

- Métriques d'utilisation par organisation
- Statistiques de vente de tickets
- Performance des paiements
- Utilisation des fonctionnalités
- Alertes automatiques

## 🛠️ Maintenance

### Commandes utiles
```bash
# Créer une organisation
php artisan organization:create "Nom" "cle" "type" "contact" "email"

# Migrer une base d'organisation
php artisan organization:migrate database_name --seed

# Vérifier le statut des migrations
php artisan migrate:status

# Nettoyer les bases orphelines
php artisan organization:cleanup
```

## 📞 Support

Pour toute question ou problème :
- 📧 Email : support@eventsaas.com
- 📱 WhatsApp : +225 XX XX XX XX XX
- 🌐 Site : https://eventsaas.com/support

---

**EventSaaS Platform** - Votre solution complète pour la gestion d'événements 🎉
