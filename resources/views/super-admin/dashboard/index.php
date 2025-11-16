<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventSaaS - Administration Système</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }

        .container {
            max-width: 1800px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 16px;
            padding: 20px 30px;
            margin-bottom: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-left h1 {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 5px;
        }

        .header-left .subtitle {
            color: #666;
            font-size: 0.95rem;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info {
            text-align: right;
        }

        .user-name {
            font-weight: 600;
            color: #1a1a1a;
        }

        .user-role {
            font-size: 0.85rem;
            color: #666;
        }

        .logout-btn {
            background: linear-gradient(135deg, #dc2626, #ea580c);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(220, 38, 38, 0.3);
        }

        .navigation {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .nav-tabs {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .nav-tab {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            background: #f8fafc;
            color: #374151;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .nav-tab.active {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
        }

        .nav-tab:hover:not(.active) {
            background: #e5e7eb;
            transform: translateY(-1px);
        }

        .content-section {
            display: none;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .content-section.active {
            display: block;
        }

        .section-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 25px;
            color: #1a1a1a;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #f8fafc;
            border-radius: 12px;
            padding: 25px;
            border: 2px solid #e5e7eb;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
            border-color: #667eea;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            color: #1a1a1a;
            margin-bottom: 8px;
        }

        .stat-label {
            color: #666;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .stat-trend {
            font-size: 0.8rem;
            margin-top: 8px;
            padding: 4px 8px;
            border-radius: 6px;
            display: inline-block;
        }

        .trend-up {
            background: #dcfce7;
            color: #16a34a;
        }

        .trend-down {
            background: #fee2e2;
            color: #dc2626;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .action-card {
            background: linear-gradient(135deg, #f8fafc, #ffffff);
            border-radius: 12px;
            padding: 20px;
            border: 2px solid #e5e7eb;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .action-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
            border-color: #4f46e5;
        }

        .action-icon {
            font-size: 2.5rem;
            margin-bottom: 12px;
            display: block;
        }

        .action-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: #1a1a1a;
        }

        .action-desc {
            color: #666;
            font-size: 0.85rem;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .data-table th {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .data-table td {
            padding: 15px;
            border-bottom: 1px solid #e5e7eb;
        }

        .data-table tr:hover {
            background: #f8fafc;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-active {
            background: #dcfce7;
            color: #16a34a;
        }

        .status-suspended {
            background: #fee2e2;
            color: #dc2626;
        }

        .status-trial {
            background: #fef3c7;
            color: #d97706;
        }

        .status-paid {
            background: #dbeafe;
            color: #2563eb;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.85rem;
            margin: 0 2px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, #16a34a, #15803d);
            color: white;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .search-filter-bar {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
            align-items: center;
        }

        .search-input {
            flex: 1;
            min-width: 200px;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 0.9rem;
        }

        .filter-select {
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 0.9rem;
            min-width: 150px;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(10px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 16px;
            padding: 30px;
            max-width: 900px;
            width: 95%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.3);
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 25px;
            color: #1a1a1a;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #374151;
        }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: border-color 0.3s ease;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .field-builder {
            background: #f8fafc;
            border-radius: 12px;
            padding: 20px;
            border: 2px solid #e5e7eb;
            margin-bottom: 20px;
        }

        .field-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            border: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .field-info {
            flex: 1;
        }

        .field-name {
            font-weight: 600;
            color: #1a1a1a;
        }

        .field-details {
            font-size: 0.85rem;
            color: #666;
            margin-top: 4px;
        }

        .field-controls {
            display: flex;
            gap: 8px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid;
        }

        .alert-warning {
            background: #fef3c7;
            border-color: #f59e0b;
            color: #92400e;
        }

        .alert-success {
            background: #dcfce7;
            border-color: #10b981;
            color: #065f46;
        }

        .alert-danger {
            background: #fee2e2;
            border-color: #ef4444;
            color: #991b1b;
        }

        .alert-info {
            background: #dbeafe;
            border-color: #3b82f6;
            color: #1e3a8a;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 30px;
        }

        .page-btn {
            padding: 8px 12px;
            border: 1px solid #e5e7eb;
            background: white;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .page-btn:hover, .page-btn.active {
            background: #4f46e5;
            color: white;
            border-color: #4f46e5;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .activity-feed {
            max-height: 400px;
            overflow-y: auto;
            background: #f8fafc;
            border-radius: 12px;
            padding: 20px;
        }

        .activity-item {
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            font-size: 0.8rem;
        }

        .activity-content {
            flex: 1;
        }

        .activity-text {
            font-size: 0.9rem;
            color: #374151;
        }

        .activity-time {
            font-size: 0.8rem;
            color: #6b7280;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .stats-grid, .quick-actions, .form-grid {
                grid-template-columns: 1fr;
            }
            
            .nav-tabs {
                flex-direction: column;
            }
            
            .search-filter-bar {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-input, .filter-select {
                min-width: unset;
            }
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #4f46e5;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <h1>EventSaaS Administration</h1>
                <p class="subtitle">Gestion système et surveillance de la plateforme</p>
            </div>
            <div class="header-right">
                <div class="user-info">
                    <div class="user-name">Super Admin</div>
                    <div class="user-role">Administrateur Système</div>
                </div>
                <button class="logout-btn" onclick="logout()">
                    🚪 Déconnexion
                </button>
            </div>
        </div>

        <!-- Navigation -->
        <div class="navigation">
            <div class="nav-tabs">
                <button class="nav-tab active" onclick="showSection('dashboard')">📊 Tableau de Bord</button>
                <button class="nav-tab" onclick="showSection('organizations')">🏢 Organisations</button>
                <button class="nav-tab" onclick="showSection('events')">📅 Événements</button>
                <button class="nav-tab" onclick="showSection('users')">👥 Utilisateurs</button>
                <button class="nav-tab" onclick="showSection('forms')">📝 Formulaires</button>
            </div>
        </div>

        <!-- Dashboard Section -->
        <div class="content-section active" id="dashboard">
            <h2 class="section-title">Vue d'ensemble du système</h2>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value" id="total-organizations">2</div>
                    <div class="stat-label">Organisations Actives</div>
                    <div class="stat-trend trend-up">+2 ce mois</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="total-events">1</div>
                    <div class="stat-label">Événements Actifs</div>
                    <div class="stat-trend trend-up">+1 cette semaine</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="total-users">2</div>
                    <div class="stat-label">Utilisateurs Totaux</div>
                    <div class="stat-trend trend-up">100% actifs</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">45K FCFA</div>
                    <div class="stat-label">Revenus Potentiels/Mois</div>
                    <div class="stat-trend trend-up">Premium x2</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="total-registrations">1</div>
                    <div class="stat-label">Inscriptions Totales</div>
                    <div class="stat-trend trend-up">20K FCFA collectés</div>
                </div>
            </div>

            <div class="quick-actions">
                <div class="action-card" onclick="showSection('organizations')">
                    <div class="action-icon">🏢</div>
                    <div class="action-title">Nouvelle Organisation</div>
                    <div class="action-desc">Créer une organisation et sa base de données</div>
                </div>
                <div class="action-card" onclick="showSection('users')">
                    <div class="action-icon">👥</div>
                    <div class="action-title">Gestion Utilisateurs</div>
                    <div class="action-desc">Administrer les comptes utilisateurs</div>
                </div>
                <div class="action-card" onclick="showSection('analytics')">
                    <div class="action-icon">📈</div>
                    <div class="action-title">Rapports Analytics</div>
                    <div class="action-desc">Consulter les statistiques détaillées</div>
                </div>
            </div>

            <h3 style="margin-bottom: 20px; color: #1a1a1a;">Activité Récente</h3>
            <div class="activity-feed" id="activity-feed">
                <!-- Les activités seront chargées ici -->
            </div>
        </div>

        <!-- Organizations Section -->
        <div class="content-section" id="organizations">
            <h2 class="section-title">Gestion des Organisations</h2>

            <div class="search-filter-bar">
                <input type="text" class="search-input" placeholder="Rechercher par nom, email, clé..." id="org-search">
                <select class="filter-select" id="org-status-filter">
                    <option value="">Tous les statuts</option>
                    <option value="active">Actif</option>
                    <option value="suspended">Suspendu</option>
                    <option value="trial">Essai</option>
                </select>
                <select class="filter-select" id="org-plan-filter">
                    <option value="">Tous les plans</option>
                    <option value="trial">Essai</option>
                    <option value="basic">Basique</option>
                    <option value="premium">Premium</option>
                    <option value="enterprise">Entreprise</option>
                </select>
                <button class="btn btn-primary" onclick="openModal('create-org')">➕ Nouvelle Organisation</button>
            </div>

            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Organisation</th>
                            <th>Contact</th>
                            <th>Type</th>
                            <th>Plan</th>
                            <th>Statut</th>
                            <th>Créée le</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="organizations-table">
                        <!-- Les données seront chargées ici -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Events Section -->
        <div class="content-section" id="events">
            <h2 class="section-title">Événements Multi-Organisations</h2>

            <div class="search-filter-bar">
                <input type="text" class="search-input" placeholder="Rechercher un événement..." id="event-search">
                <select class="filter-select" id="event-org-filter">
                    <option value="">Toutes les organisations</option>
                </select>
                <select class="filter-select" id="event-status-filter">
                    <option value="">Tous les statuts</option>
                    <option value="published">Publié</option>
                    <option value="draft">Brouillon</option>
                </select>
            </div>

            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Événement</th>
                            <th>Organisation</th>
                            <th>Date</th>
                            <th>Participants</th>
                            <th>Revenus</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="events-table">
                        <!-- Les données seront chargées ici -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Users Section -->
        <div class="content-section" id="users">
            <h2 class="section-title">Gestion des Utilisateurs</h2>

            <div class="search-filter-bar">
                <input type="text" class="search-input" placeholder="Rechercher par email, nom..." id="user-search">
                <select class="filter-select" id="user-role-filter">
                    <option value="">Tous les rôles</option>
                    <option value="owner">Propriétaire</option>
                    <option value="admin">Administrateur</option>
                    <option value="manager">Manager</option>
                    <option value="user">Utilisateur</option>
                </select>
                <select class="filter-select" id="user-status-filter">
                    <option value="">Tous les statuts</option>
                    <option value="active">Actif</option>
                    <option value="inactive">Inactif</option>
                </select>
            </div>

            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Email</th>
                            <th>Organisation</th>
                            <th>Rôle</th>
                            <th>Dernière Connexion</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="users-table">
                        <!-- Les données seront chargées ici -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Forms Section -->
        <div class="content-section" id="forms">
            <h2 class="section-title">Formulaires Paramétrables</h2>

            <div class="search-filter-bar">
                <select class="filter-select" id="form-event-filter">
                    <option value="">Sélectionner un événement...</option>
                </select>
                <button class="btn btn-secondary" onclick="refreshFormsList()">🔄 Actualiser</button>
            </div>

            <div id="form-details" style="display: none;">
                <h3 style="margin-bottom: 20px; color: #1a1a1a;">Configuration du Formulaire</h3>
                
                <div class="alert alert-info">
                    <strong>Formulaire Paramétrable</strong> - Les champs peuvent être ajoutés, modifiés ou supprimés dynamiquement pour chaque événement.
                </div>

                <div class="field-builder">
                    <h4 style="margin-bottom: 15px;">Sections du Formulaire</h4>
                    <div id="form-sections">
                        <!-- Les sections seront chargées ici -->
                    </div>
                </div>

                <div class="field-builder">
                    <h4 style="margin-bottom: 15px;">Champs du Formulaire</h4>
                    <div id="form-fields">
                        <!-- Les champs seront chargés ici -->
                    </div>
                </div>

                <button class="btn btn-primary" onclick="openModal('add-field')">➕ Ajouter un Champ</button>
                <button class="btn btn-success" onclick="saveFormConfiguration()">💾 Sauvegarder</button>
            </div>

            <div id="form-empty-state" class="empty-state">
                <div class="empty-icon">📝</div>
                <h3>Sélectionnez un événement</h3>
                <p>Choisissez un événement pour voir et modifier sa configuration de formulaire</p>
            </div>
        </div>

        <!-- Analytics Section -->
        <div class="content-section" id="analytics">
            <h2 class="section-title">Analytics et Rapports</h2>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">89.5%</div>
                    <div class="stat-label">Taux de Conversion</div>
                    <div class="stat-trend trend-up">+5.2% vs mois dernier</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">4.2</div>
                    <div class="stat-label">Événements Moyens/Org</div>
                    <div class="stat-trend trend-up">+0.8 vs trimestre</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">125</div>
                    <div class="stat-label">Participants Moyens/Événement</div>
                    <div class="stat-trend trend-up">+15% croissance</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">18.5K</div>
                    <div class="stat-label">Revenus Moyens/Événement</div>
                    <div class="stat-trend trend-up">FCFA - Excellent</div>
                </div>
            </div>

            <div class="form-grid">
                <div>
                    <h3 style="margin-bottom: 15px;">Utilisation par Type d'Organisation</h3>
                    <div class="field-builder">
                        <div class="field-item">
                            <div class="field-info">
                                <div class="field-name">JCI (Jeune Chambre)</div>
                                <div class="field-details">2 organisations • 100% du portfolio</div>
                            </div>
                            <div class="stat-value" style="font-size: 1.5rem;">100%</div>
                        </div>
                        <div class="field-item">
                            <div class="field-info">
                                <div class="field-name">Rotary Club</div>
                                <div class="field-details">0 organisations</div>
                            </div>
                            <div class="stat-value" style="font-size: 1.5rem;">0%</div>
                        </div>
                        <div class="field-item">
                            <div class="field-info">
                                <div class="field-name">Lions Club</div>
                                <div class="field-details">0 organisations</div>
                            </div>
                            <div class="stat-value" style="font-size: 1.5rem;">0%</div>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 style="margin-bottom: 15px;">Performance des Plans</h3>
                    <div class="field-builder">
                        <div class="field-item">
                            <div class="field-info">
                                <div class="field-name">Premium</div>
                                <div class="field-details">2 organisations • 79.99€/mois chacune</div>
                            </div>
                            <div class="stat-value" style="font-size: 1.5rem; color: #16a34a;">159.98€</div>
                        </div>
                        <div class="field-item">
                            <div class="field-info">
                                <div class="field-name">Trial</div>
                                <div class="field-details">0 organisations en essai</div>
                            </div>
                            <div class="stat-value" style="font-size: 1.5rem;">0€</div>
                        </div>
                    </div>
                </div>
            </div>

            <h3 style="margin: 30px 0 20px; color: #1a1a1a;">Événements les Plus Performants</h3>
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Événement</th>
                            <th>Organisation</th>
                            <th>Participants</th>
                            <th>Revenus</th>
                            <th>Taux de Remplissage</th>
                            <th>Performance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Convention Locale EMERAUDE 2025</td>
                            <td>JCI Emeraude</td>
                            <td>1/150</td>
                            <td>20,000 FCFA</td>
                            <td>0.7%</td>
                            <td><span class="status-badge status-active">En cours</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- System Section -->
        <div class="content-section" id="system">
            <h2 class="section-title">Configuration Système</h2>

            <div class="form-grid">
                <div>
                    <h3 style="margin-bottom: 20px;">Paramètres Généraux</h3>
                    <div class="form-group">
                        <label class="form-label">Nom de l'application</label>
                        <input type="text" class="form-input" value="EventSaaS Platform" id="app-name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Durée d'essai (jours)</label>
                        <input type="number" class="form-input" value="14" id="trial-duration">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mode maintenance</label>
                        <select class="form-select" id="maintenance-mode">
                            <option value="false">Désactivé</option>
                            <option value="true">Activé</option>
                        </select>
                    </div>
                </div>

                <div>
                    <h3 style="margin-bottom: 20px;">Limites Système</h3>
                    <div class="form-group">
                        <label class="form-label">Limite d'organisations</label>
                        <input type="number" class="form-input" value="1000" id="max-orgs">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Stockage max par org (MB)</label>
                        <input type="number" class="form-input" value="2000" id="max-storage">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Événements max par org</label>
                        <input type="number" class="form-input" value="20" id="max-events">
                    </div>
                </div>
            </div>

            <button class="btn btn-success" onclick="saveSystemSettings()">💾 Sauvegarder les Paramètres</button>

            <h3 style="margin: 40px 0 20px; color: #1a1a1a;">État du Système</h3>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value" style="color: #16a34a;">●</div>
                    <div class="stat-label">Base de Données Master</div>
                    <div class="stat-trend trend-up">Opérationnelle</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" style="color: #16a34a;">●</div>
                    <div class="stat-label">Bases Organisations</div>
                    <div class="stat-trend trend-up">2/2 actives</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">2.1GB</div>
                    <div class="stat-label">Utilisation Stockage</div>
                    <div class="stat-trend trend-up">5% du total</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">0.3s</div>
                    <div class="stat-label">Temps de Réponse Moyen</div>
                    <div class="stat-trend trend-up">Excellent</div>
                </div>
            </div>

            <h3 style="margin: 40px 0 20px; color: #1a1a1a;">Templates de Base de Données</h3>
            <div class="field-builder">
                <div class="field-item">
                    <div class="field-info">
                        <div class="field-name">JCI Standard Template v1.0</div>
                        <div class="field-details">Template pour organisations JCI avec gestion événements, inscriptions, paiements</div>
                    </div>
                    <div class="field-controls">
                        <button class="btn btn-secondary">✏️ Modifier</button>
                        <button class="btn btn-primary">📋 Dupliquer</button>
                    </div>
                </div>
                <div class="field-item">
                    <div class="field-info">
                        <div class="field-name">Rotary Club Template v1.0</div>
                        <div class="field-details">Template pour clubs Rotary avec gestion membres, réunions, projets</div>
                    </div>
                    <div class="field-controls">
                        <button class="btn btn-secondary">✏️ Modifier</button>
                        <button class="btn btn-primary">📋 Dupliquer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Create Organization -->
    <div class="modal" id="create-org-modal">
        <div class="modal-content">
            <h3 class="modal-title">Créer une Nouvelle Organisation</h3>
            
            <form id="create-org-form">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Nom de l'organisation *</label>
                        <input type="text" class="form-input" name="org_name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Clé unique *</label>
                        <input type="text" class="form-input" name="org_key" required placeholder="ex: jci-abidjan">
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Type d'organisation *</label>
                        <select class="form-select" name="org_type" required>
                            <option value="">Sélectionner...</option>
                            <option value="jci">JCI (Jeune Chambre)</option>
                            <option value="rotary">Rotary Club</option>
                            <option value="lions">Lions Club</option>
                            <option value="association">Association</option>
                            <option value="company">Entreprise</option>
                            <option value="other">Autre</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Plan d'abonnement *</label>
                        <select class="form-select" name="subscription_plan" required>
                            <option value="trial">Essai Gratuit (14 jours)</option>
                            <option value="basic">Basique (29.99€/mois)</option>
                            <option value="premium">Premium (79.99€/mois)</option>
                            <option value="enterprise">Entreprise (199.99€/mois)</option>
                        </select>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Nom du contact *</label>
                        <input type="text" class="form-input" name="contact_name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email du contact *</label>
                        <input type="email" class="form-input" name="contact_email" required>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Téléphone</label>
                        <input type="tel" class="form-input" name="contact_phone">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sous-domaine</label>
                        <input type="text" class="form-input" name="subdomain" placeholder="Auto-généré si vide">
                    </div>
                </div>
            </form>
            
            <div style="display: flex; gap: 15px; justify-content: flex-end; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                <button class="btn btn-secondary" onclick="closeModal('create-org')">Annuler</button>
                <button class="btn btn-primary" onclick="createOrganization()">Créer l'Organisation</button>
            </div>
        </div>
    </div>

    <!-- Modal: Add Form Field -->
    <div class="modal" id="add-field-modal">
        <div class="modal-content">
            <h3 class="modal-title">Ajouter un Champ au Formulaire</h3>
            
            <form id="add-field-form">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Clé du champ *</label>
                        <input type="text" class="form-input" name="field_key" required placeholder="ex: full_name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Libellé *</label>
                        <input type="text" class="form-input" name="field_label" required placeholder="ex: Nom et Prénoms">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Type de champ *</label>
                        <select class="form-select" name="field_type" required onchange="toggleFieldOptions(this)">
                            <option value="">Sélectionner...</option>
                            <option value="text">Texte</option>
                            <option value="email">Email</option>
                            <option value="phone">Téléphone</option>
                            <option value="country_phone">Téléphone avec indicatif</option>
                            <option value="number">Nombre</option>
                            <option value="date">Date</option>
                            <option value="time">Heure</option>
                            <option value="select">Liste déroulante</option>
                            <option value="radio">Boutons radio</option>
                            <option value="checkbox">Case à cocher</option>
                            <option value="textarea">Zone de texte</option>
                            <option value="file">Fichier</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Section</label>
                        <select class="form-select" name="section_name">
                            <option value="personal_info">Informations Personnelles</option>
                            <option value="professional_info">Informations Professionnelles</option>
                            <option value="preferences">Préférences</option>
                            <option value="additional">Informations Complémentaires</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description/Aide</label>
                    <textarea class="form-textarea" name="field_description" placeholder="Description ou texte d'aide pour ce champ..."></textarea>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Largeur du champ</label>
                        <select class="form-select" name="field_width">
                            <option value="full">Pleine largeur</option>
                            <option value="half">Demi largeur</option>
                            <option value="third">Un tiers</option>
                            <option value="quarter">Un quart</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ordre d'affichage</label>
                        <input type="number" class="form-input" name="display_order" min="1" value="1">
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">
                            <input type="checkbox" name="is_required"> Champ obligatoire
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <input type="checkbox" name="is_unique"> Valeur unique
                        </label>
                    </div>
                </div>
                
                <div class="form-group" id="field-options" style="display: none;">
                    <label class="form-label">Options (pour liste déroulante)</label>
                    <textarea class="form-textarea" name="field_options" placeholder="Option 1&#10;Option 2&#10;Option 3..."></textarea>
                    <small>Une option par ligne</small>
                </div>
            </form>
            
            <div style="display: flex; gap: 15px; justify-content: flex-end; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                <button class="btn btn-secondary" onclick="closeModal('add-field')">Annuler</button>
                <button class="btn btn-primary" onclick="addFormField()">Ajouter le Champ</button>
            </div>
        </div>
    </div>

    <script>
        // Données globales simulées
        let organizations = [
            {
                id: 1,
                org_key: 'jci-nirvana',
                org_name: 'JCI Nirvana',
                org_type: 'jci',
                subscription_plan: 'premium',
                subscription_status: 'active',
                contact_email: 'president@jci-nirvana.ci',
                contact_name: 'Président JCI Nirvana',
                contact_phone: '+225 07 00 00 00 00',
                created_at: '2025-06-25',
                database_name: 'org_jci_nirvana'
            },
            {
                id: 2,
                org_key: 'jci-emeraude',
                org_name: 'JCI Emeraude',
                org_type: 'jci',
                subscription_plan: 'premium',
                subscription_status: 'active',
                contact_email: 'president@jci-emeraude.ci',
                contact_name: 'Président JCI Emeraude',
                contact_phone: '+225 07 11 22 33 44',
                created_at: '2025-06-27',
                database_name: 'org_jci_emeraude'
            }
        ];

        let events = [
            {
                id: 1,
                organization_id: 2,
                event_title: 'Convention Locale EMERAUDE 2025',
                event_date: '2025-08-15',
                event_location: 'Hôtel Pullman Abidjan',
                max_participants: 150,
                registered_participants: 1,
                total_revenue: 20000,
                is_published: true,
                organization_name: 'JCI Emeraude'
            }
        ];

        let users = [
            {
                id: 1,
                organization_id: 1,
                email: 'president@jci-nirvana.ci',
                first_name: 'Président',
                last_name: 'JCI Nirvana',
                role: 'owner',
                is_active: true,
                last_login_at: '2025-06-30 08:30:00',
                organization_name: 'JCI Nirvana'
            },
            {
                id: 2,
                organization_id: 2,
                email: 'president@jci-emeraude.ci',
                first_name: 'Président',
                last_name: 'JCI Emeraude',
                role: 'owner',
                is_active: true,
                last_login_at: '2025-06-30 10:15:00',
                organization_name: 'JCI Emeraude'
            }
        ];

        let formFields = [
            {
                id: 1,
                event_id: 1,
                field_key: 'full_name',
                field_label: 'Nom et Prénoms',
                field_type: 'text',
                section_name: 'personal_info',
                is_required: true,
                display_order: 1,
                field_width: 'full'
            },
            {
                id: 2,
                event_id: 1,
                field_key: 'email',
                field_label: 'Adresse Email',
                field_type: 'email',
                section_name: 'personal_info',
                is_required: true,
                display_order: 2,
                field_width: 'half'
            },
            {
                id: 3,
                event_id: 1,
                field_key: 'phone',
                field_label: 'Numéro Whatsapp',
                field_type: 'country_phone',
                section_name: 'personal_info',
                is_required: true,
                display_order: 3,
                field_width: 'half'
            },
            {
                id: 4,
                event_id: 1,
                field_key: 'organization',
                field_label: 'Organisation/Entreprise',
                field_type: 'text',
                section_name: 'professional_info',
                is_required: true,
                display_order: 1,
                field_width: 'full'
            },
            {
                id: 5,
                event_id: 1,
                field_key: 'jci_function',
                field_label: 'Fonction JCI',
                field_type: 'select',
                section_name: 'professional_info',
                is_required: true,
                display_order: 2,
                field_width: 'half'
            }
        ];

        // Navigation
        function showSection(sectionId) {
            document.querySelectorAll('.content-section').forEach(section => {
                section.classList.remove('active');
            });
            
            document.querySelectorAll('.nav-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            document.getElementById(sectionId).classList.add('active');
            event.target.classList.add('active');
            
            // Charger les données selon la section
            switch(sectionId) {
                case 'organizations':
                    loadOrganizations();
                    break;
                case 'events':
                    loadEvents();
                    break;
                case 'users':
                    loadUsers();
                    break;
                case 'forms':
                    loadFormsList();
                    break;
                case 'dashboard':
                    loadActivityFeed();
                    break;
            }
        }

        // Chargement des organisations
        function loadOrganizations() {
            const tbody = document.getElementById('organizations-table');
            tbody.innerHTML = '';
            
            organizations.forEach(org => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <strong>${org.org_name}</strong><br>
                        <small>${org.org_key}</small>
                    </td>
                    <td>
                        ${org.contact_name}<br>
                        <small>${org.contact_email}</small>
                    </td>
                    <td><span class="status-badge">${org.org_type.toUpperCase()}</span></td>
                    <td><span class="status-badge">${org.subscription_plan.toUpperCase()}</span></td>
                    <td><span class="status-badge status-${org.subscription_status}">${org.subscription_status.toUpperCase()}</span></td>
                    <td>${org.created_at}</td>
                    <td>
                        <button class="btn btn-secondary" onclick="viewOrgDetails(${org.id})">👁️ Voir</button>
                        <button class="btn btn-primary" onclick="accessOrgDatabase('${org.database_name}')">🗄️ BDD</button>
                        <button class="btn ${org.subscription_status === 'active' ? 'btn-danger' : 'btn-success'}" 
                                onclick="toggleOrgStatus(${org.id})">
                            ${org.subscription_status === 'active' ? '⏸️ Suspendre' : '▶️ Activer'}
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        // Chargement des événements
        function loadEvents() {
            const tbody = document.getElementById('events-table');
            tbody.innerHTML = '';
            
            events.forEach(event => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <strong>${event.event_title}</strong><br>
                        <small>${event.event_location}</small>
                    </td>
                    <td>${event.organization_name}</td>
                    <td>${event.event_date}</td>
                    <td>${event.registered_participants}/${event.max_participants}</td>
                    <td>${event.total_revenue.toLocaleString()} FCFA</td>
                    <td><span class="status-badge ${event.is_published ? 'status-active' : 'status-trial'}">${event.is_published ? 'PUBLIÉ' : 'BROUILLON'}</span></td>
                    <td>
                        <button class="btn btn-secondary" onclick="viewEventDetails(${event.id})">👁️ Voir</button>
                        <button class="btn btn-primary" onclick="manageEventForm(${event.id})">📝 Formulaire</button>
                    </td>
                `;
                tbody.appendChild(row);
            });

            // Charger les organisations dans le filtre
            const orgFilter = document.getElementById('event-org-filter');
            orgFilter.innerHTML = '<option value="">Toutes les organisations</option>';
            organizations.forEach(org => {
                const option = document.createElement('option');
                option.value = org.id;
                option.textContent = org.org_name;
                orgFilter.appendChild(option);
            });
        }

        // Chargement des utilisateurs
        function loadUsers() {
            const tbody = document.getElementById('users-table');
            tbody.innerHTML = '';
            
            users.forEach(user => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <strong>${user.first_name} ${user.last_name}</strong>
                    </td>
                    <td>${user.email}</td>
                    <td>${user.organization_name}</td>
                    <td><span class="status-badge">${user.role.toUpperCase()}</span></td>
                    <td>${user.last_login_at ? new Date(user.last_login_at).toLocaleString() : 'Jamais'}</td>
                    <td><span class="status-badge ${user.is_active ? 'status-active' : 'status-suspended'}">${user.is_active ? 'ACTIF' : 'INACTIF'}</span></td>
                    <td>
                        <button class="btn btn-secondary" onclick="viewUserDetails(${user.id})">👁️ Voir</button>
                        <button class="btn ${user.is_active ? 'btn-danger' : 'btn-success'}" 
                                onclick="toggleUserStatus(${user.id})">
                            ${user.is_active ? '⏸️ Désactiver' : '▶️ Activer'}
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        // Chargement de la liste des formulaires
        function loadFormsList() {
            const eventFilter = document.getElementById('form-event-filter');
            eventFilter.innerHTML = '<option value="">Sélectionner un événement...</option>';
            
            events.forEach(event => {
                const org = organizations.find(o => o.id === event.organization_id);
                const option = document.createElement('option');
                option.value = event.id;
                option.textContent = `${event.event_title} (${org ? org.org_name : 'N/A'})`;
                eventFilter.appendChild(option);
            });

            eventFilter.addEventListener('change', function() {
                if (this.value) {
                    loadEventFormDetails(parseInt(this.value));
                    document.getElementById('form-details').style.display = 'block';
                    document.getElementById('form-empty-state').style.display = 'none';
                } else {
                    document.getElementById('form-details').style.display = 'none';
                    document.getElementById('form-empty-state').style.display = 'block';
                }
            });
        }

        // Chargement des détails du formulaire d'un événement
        function loadEventFormDetails(eventId) {
            const eventFields = formFields.filter(field => field.event_id === eventId);
            
            // Sections
            const sectionsContainer = document.getElementById('form-sections');
            sectionsContainer.innerHTML = '';
            
            const sections = ['personal_info', 'professional_info', 'preferences', 'additional'];
            const sectionNames = {
                'personal_info': 'Informations Personnelles',
                'professional_info': 'Informations Professionnelles',
                'preferences': 'Préférences',
                'additional': 'Informations Complémentaires'
            };

            sections.forEach(section => {
                const fieldsInSection = eventFields.filter(field => field.section_name === section);
                const sectionDiv = document.createElement('div');
                sectionDiv.className = 'field-item';
                sectionDiv.innerHTML = `
                    <div class="field-info">
                        <div class="field-name">${sectionNames[section]}</div>
                        <div class="field-details">${fieldsInSection.length} champ(s)</div>
                    </div>
                    <div class="stat-value" style="font-size: 1.2rem;">${fieldsInSection.length}</div>
                `;
                sectionsContainer.appendChild(sectionDiv);
            });

            // Champs
            const fieldsContainer = document.getElementById('form-fields');
            fieldsContainer.innerHTML = '';
            
            eventFields.sort((a, b) => a.display_order - b.display_order);
            
            eventFields.forEach(field => {
                const fieldDiv = document.createElement('div');
                fieldDiv.className = 'field-item';
                fieldDiv.innerHTML = `
                    <div class="field-info">
                        <div class="field-name">${field.field_label}</div>
                        <div class="field-details">
                            Type: ${field.field_type} • Section: ${sectionNames[field.section_name]} • 
                            ${field.is_required ? 'Obligatoire' : 'Optionnel'} • 
                            Largeur: ${field.field_width}
                        </div>
                    </div>
                    <div class="field-controls">
                        <button class="btn btn-secondary" onclick="editField(${field.id})">✏️ Modifier</button>
                        <button class="btn btn-danger" onclick="deleteField(${field.id})">🗑️ Supprimer</button>
                    </div>
                `;
                fieldsContainer.appendChild(fieldDiv);
            });
        }

        // Flux d'activité
        function loadActivityFeed() {
            const activities = [
                {
                    type: 'org_created',
                    text: 'Nouvelle organisation JCI Emeraude créée',
                    time: 'Il y a 3 jours',
                    icon: '🏢',
                    color: '#4f46e5'
                },
                {
                    type: 'event_created',
                    text: 'Événement "Convention Locale EMERAUDE 2025" créé',
                    time: 'Il y a 3 jours',
                    icon: '📅',
                    color: '#16a34a'
                },
                {
                    type: 'registration',
                    text: 'Nouvelle inscription à la Convention EMERAUDE',
                    time: 'Aujourd\'hui à 10h35',
                    icon: '👤',
                    color: '#0891b2'
                },
                {
                    type: 'payment',
                    text: 'Paiement de 20,000 FCFA via Wave',
                    time: 'Aujourd\'hui à 10h35',
                    icon: '💳',
                    color: '#16a34a'
                },
                {
                    type: 'system',
                    text: 'Système en fonctionnement optimal',
                    time: 'En continu',
                    icon: '⚡',
                    color: '#f59e0b'
                }
            ];

            const activityFeed = document.getElementById('activity-feed');
            activityFeed.innerHTML = '';

            activities.forEach(activity => {
                const activityItem = document.createElement('div');
                activityItem.className = 'activity-item';
                activityItem.innerHTML = `
                    <div class="activity-icon" style="background: ${activity.color};">
                        ${activity.icon}
                    </div>
                    <div class="activity-content">
                        <div class="activity-text">${activity.text}</div>
                        <div class="activity-time">${activity.time}</div>
                    </div>
                `;
                activityFeed.appendChild(activityItem);
            });
        }

        // Actions sur les organisations
        function toggleOrgStatus(orgId) {
            const org = organizations.find(o => o.id === orgId);
            if (!org) return;

            const newStatus = org.subscription_status === 'active' ? 'suspended' : 'active';
            org.subscription_status = newStatus;
            
            showMessage(`Organisation ${org.org_name} ${newStatus === 'active' ? 'activée' : 'suspendue'}`, 'success');
            loadOrganizations();
        }

        function viewOrgDetails(orgId) {
            const org = organizations.find(o => o.id === orgId);
            if (!org) return;
            
            showMessage(`Détails de l'organisation ${org.org_name} - Fonctionnalité à implémenter`, 'info');
        }

        function accessOrgDatabase(dbName) {
            showMessage(`Accès à la base de données ${dbName} - Interface d'administration de BDD`, 'info');
        }

        // Actions sur les utilisateurs
        function toggleUserStatus(userId) {
            const user = users.find(u => u.id === userId);
            if (!user) return;

            user.is_active = !user.is_active;
            
            showMessage(`Utilisateur ${user.email} ${user.is_active ? 'activé' : 'désactivé'}`, 'success');
            loadUsers();
        }

        function viewUserDetails(userId) {
            const user = users.find(u => u.id === userId);
            if (!user) return;
            
            showMessage(`Détails de l'utilisateur ${user.email} - Fonctionnalité à implémenter`, 'info');
        }

        // Actions sur les événements
        function viewEventDetails(eventId) {
            const event = events.find(e => e.id === eventId);
            if (!event) return;
            
            showMessage(`Détails de l'événement ${event.event_title} - Statistiques complètes`, 'info');
        }

        function manageEventForm(eventId) {
            showSection('forms');
            
            // Sélectionner l'événement dans le filtre
            const eventFilter = document.getElementById('form-event-filter');
            eventFilter.value = eventId;
            eventFilter.dispatchEvent(new Event('change'));
        }

        // Gestion des formulaires
        function refreshFormsList() {
            loadFormsList();
            showMessage('Liste des formulaires actualisée', 'success');
        }

        function addFormField() {
            const form = document.getElementById('add-field-form');
            const formData = new FormData(form);
            const eventId = parseInt(document.getElementById('form-event-filter').value);
            
            if (!eventId) {
                showMessage('Veuillez d\'abord sélectionner un événement', 'error');
                return;
            }

            const newField = {
                id: formFields.length + 1,
                event_id: eventId,
                field_key: formData.get('field_key'),
                field_label: formData.get('field_label'),
                field_type: formData.get('field_type'),
                field_description: formData.get('field_description'),
                section_name: formData.get('section_name'),
                field_width: formData.get('field_width'),
                display_order: parseInt(formData.get('display_order')),
                is_required: formData.get('is_required') === 'on',
                is_unique: formData.get('is_unique') === 'on'
            };
            
            formFields.push(newField);
            loadEventFormDetails(eventId);
            closeModal('add-field');
            form.reset();
            
            showMessage('Champ ajouté avec succès !', 'success');
        }

        function editField(fieldId) {
            showMessage('Modification du champ - Fonctionnalité à implémenter', 'info');
        }

        function deleteField(fieldId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce champ ?')) {
                const index = formFields.findIndex(f => f.id === fieldId);
                if (index !== -1) {
                    formFields.splice(index, 1);
                    const eventId = parseInt(document.getElementById('form-event-filter').value);
                    loadEventFormDetails(eventId);
                    showMessage('Champ supprimé avec succès !', 'success');
                }
            }
        }

        function saveFormConfiguration() {
            const eventId = parseInt(document.getElementById('form-event-filter').value);
            if (!eventId) {
                showMessage('Aucun événement sélectionné', 'error');
                return;
            }
            
            showMessage('Configuration du formulaire sauvegardée !', 'success');
        }

        // Création d'organisation
        function createOrganization() {
            const form = document.getElementById('create-org-form');
            const formData = new FormData(form);
            
            const newOrg = {
                id: organizations.length + 1,
                org_key: formData.get('org_key'),
                org_name: formData.get('org_name'),
                org_type: formData.get('org_type'),
                subscription_plan: formData.get('subscription_plan'),
                subscription_status: 'active',
                contact_email: formData.get('contact_email'),
                contact_name: formData.get('contact_name'),
                contact_phone: formData.get('contact_phone'),
                created_at: new Date().toISOString().split('T')[0],
                database_name: `org_${formData.get('org_key').replace(/-/g, '_')}`
            };
            
            organizations.push(newOrg);
            loadOrganizations();
            closeModal('create-org');
            form.reset();
            
            showMessage(`Organisation ${newOrg.org_name} créée avec succès ! Base de données ${newOrg.database_name} initialisée.`, 'success');
        }

        // Paramètres système
        function saveSystemSettings() {
            const settings = {
                app_name: document.getElementById('app-name').value,
                trial_duration: document.getElementById('trial-duration').value,
                maintenance_mode: document.getElementById('maintenance-mode').value,
                max_orgs: document.getElementById('max-orgs').value,
                max_storage: document.getElementById('max-storage').value,
                max_events: document.getElementById('max-events').value
            };
            
            showMessage('Paramètres système sauvegardés avec succès !', 'success');
        }

        // Gestion des modales
        function openModal(modalId) {
            document.getElementById(modalId + '-modal').style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId + '-modal').style.display = 'none';
        }

        // Utilitaires
        function showMessage(message, type = 'info') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type}`;
            alertDiv.textContent = message;
            alertDiv.style.position = 'fixed';
            alertDiv.style.top = '20px';
            alertDiv.style.right = '20px';
            alertDiv.style.zIndex = '9999';
            alertDiv.style.minWidth = '300px';
            
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                if (document.body.contains(alertDiv)) {
                    document.body.removeChild(alertDiv);
                }
            }, 4000);
        }

        function toggleFieldOptions(select) {
            const fieldOptionsDiv = document.getElementById('field-options');
            if (select.value === 'select' || select.value === 'radio') {
                fieldOptionsDiv.style.display = 'block';
            } else {
                fieldOptionsDiv.style.display = 'none';
            }
        }

        function logout() {
            if (confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
                showMessage('Déconnexion en cours...', 'success');
                setTimeout(() => {
                    alert('Redirection vers la page de connexion...');
                }, 1000);
            }
        }

        // Fermer les modales en cliquant à l'extérieur
        window.addEventListener('click', function(e) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });

        // Filtres de recherche
        document.addEventListener('DOMContentLoaded', function() {
            // Filtres organisations
            const orgSearch = document.getElementById('org-search');
            const orgStatusFilter = document.getElementById('org-status-filter');
            const orgPlanFilter = document.getElementById('org-plan-filter');

            function filterOrganizations() {
                const searchTerm = orgSearch.value.toLowerCase();
                const statusFilter = orgStatusFilter.value;
                const planFilter = orgPlanFilter.value;

                const filteredOrgs = organizations.filter(org => {
                    const matchesSearch = !searchTerm || 
                        org.org_name.toLowerCase().includes(searchTerm) ||
                        org.contact_email.toLowerCase().includes(searchTerm) ||
                        org.org_key.toLowerCase().includes(searchTerm);
                    
                    const matchesStatus = !statusFilter || org.subscription_status === statusFilter;
                    const matchesPlan = !planFilter || org.subscription_plan === planFilter;
                    
                    return matchesSearch && matchesStatus && matchesPlan;
                });

                // Recharger le tableau avec les données filtrées
                const tbody = document.getElementById('organizations-table');
                tbody.innerHTML = '';
                
                filteredOrgs.forEach(org => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>
                            <strong>${org.org_name}</strong><br>
                            <small>${org.org_key}</small>
                        </td>
                        <td>
                            ${org.contact_name}<br>
                            <small>${org.contact_email}</small>
                        </td>
                        <td><span class="status-badge">${org.org_type.toUpperCase()}</span></td>
                        <td><span class="status-badge">${org.subscription_plan.toUpperCase()}</span></td>
                        <td><span class="status-badge status-${org.subscription_status}">${org.subscription_status.toUpperCase()}</span></td>
                        <td>${org.created_at}</td>
                        <td>
                            <button class="btn btn-secondary" onclick="viewOrgDetails(${org.id})">👁️ Voir</button>
                            <button class="btn btn-primary" onclick="accessOrgDatabase('${org.database_name}')">🗄️ BDD</button>
                            <button class="btn ${org.subscription_status === 'active' ? 'btn-danger' : 'btn-success'}" 
                                    onclick="toggleOrgStatus(${org.id})">
                                ${org.subscription_status === 'active' ? '⏸️ Suspendre' : '▶️ Activer'}
                            </button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            }

            if (orgSearch) orgSearch.addEventListener('input', filterOrganizations);
            if (orgStatusFilter) orgStatusFilter.addEventListener('change', filterOrganizations);
            if (orgPlanFilter) orgPlanFilter.addEventListener('change', filterOrganizations);

            // Similaire pour les autres filtres...
            
            // Initialisation
            loadOrganizations();
            loadActivityFeed();
        });
    </script>
</body>
</html>