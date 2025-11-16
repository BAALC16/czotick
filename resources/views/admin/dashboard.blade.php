<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SaaS Events Platform - Dashboard Admin</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #1a73e8;
            --primary-dark: #1557b0;
            --secondary: #34a853;
            --success: #0f9d58;
            --warning: #ff9800;
            --danger: #f44336;
            --dark: #202124;
            --light: #f8f9fa;
            --border: #e0e0e0;
            --text: #3c4043;
            --text-light: #5f6368;
            --sidebar-width: 280px;
            --header-height: 70px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--light);
            color: var(--text);
            line-height: 1.6;
        }

        /* Layout */
        .app-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: var(--sidebar-width);
            background: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            position: fixed;
            height: 100vh;
            z-index: 1000;
            overflow-y: auto;
        }

        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        .header {
            background: white;
            height: var(--header-height);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .content-area {
            padding: 2rem;
        }

        /* Sidebar */
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border);
            text-align: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
        }

        .nav-menu {
            list-style: none;
            padding: 1rem 0;
        }

        .nav-item {
            margin: 0.25rem 0;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: var(--text);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .nav-link:hover, .nav-link.active {
            background: rgba(26, 115, 232, 0.1);
            border-left-color: var(--primary);
            color: var(--primary);
        }

        .nav-link i {
            margin-right: 0.75rem;
            width: 20px;
        }

        .nav-submenu {
            list-style: none;
            margin-left: 2rem;
            display: none;
        }

        .nav-item.expanded .nav-submenu {
            display: block;
        }

        .nav-submenu .nav-link {
            padding: 0.5rem 1.5rem;
            font-size: 0.9rem;
        }

        /* Header */
        .header-title {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-secondary {
            background: white;
            color: var(--text);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            background: var(--light);
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-warning {
            background: var(--warning);
            color: white;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        /* Dashboard Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border: 1px solid var(--border);
        }

        .card-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--border);
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text);
        }

        .stat-card {
            padding: 1.5rem;
            border-radius: 12px;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid var(--primary);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .stat-change {
            font-size: 0.8rem;
            margin-top: 0.5rem;
        }

        .stat-change.positive {
            color: var(--success);
        }

        .stat-change.negative {
            color: var(--danger);
        }

        /* Tables */
        .table-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .table th {
            background: var(--light);
            font-weight: 600;
            color: var(--text);
        }

        .table tbody tr:hover {
            background: rgba(26, 115, 232, 0.05);
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 2000;
            backdrop-filter: blur(4px);
        }

        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border);
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-light);
        }

        /* Forms */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text);
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 0.9rem;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        /* Badges */
        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .badge-success {
            background: rgba(15, 157, 88, 0.1);
            color: var(--success);
        }

        .badge-warning {
            background: rgba(255, 152, 0, 0.1);
            color: var(--warning);
        }

        .badge-danger {
            background: rgba(244, 67, 54, 0.1);
            color: var(--danger);
        }

        .badge-info {
            background: rgba(26, 115, 232, 0.1);
            color: var(--primary);
        }

        /* Page Content */
        .page-content {
            display: none;
        }

        .page-content.active {
            display: block;
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: var(--text-light);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .dashboard-grid,
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .header-actions {
                gap: 0.5rem;
            }
        }

        /* Field Builder Styles */
        .field-builder {
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            background: var(--light);
        }

        .field-preview {
            background: white;
            padding: 1rem;
            border-radius: 6px;
            margin-top: 1rem;
        }

        .field-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .field-item {
            background: white;
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 1rem;
            margin-bottom: 0.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .field-actions {
            display: flex;
            gap: 0.5rem;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 2rem;
        }

        .spinner {
            border: 3px solid var(--border);
            border-top: 3px solid var(--primary);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">Events SaaS</div>
            </div>
            <nav>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="#" class="nav-link active" data-page="dashboard">
                            <i data-feather="home"></i>
                            Tableau de Bord
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-page="organizations">
                            <i data-feather="building"></i>
                            Organisations
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-page="events">
                            <i data-feather="calendar"></i>
                            Événements
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-page="users">
                            <i data-feather="users"></i>
                            Utilisateurs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-page="analytics">
                            <i data-feather="bar-chart"></i>
                            Analytics
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-page="billing">
                            <i data-feather="credit-card"></i>
                            Facturation
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-page="settings">
                            <i data-feather="settings"></i>
                            Paramètres
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="header">
                <h1 class="header-title">Super Admin Dashboard</h1>
                <div class="header-actions">
                    <button class="btn btn-secondary">
                        <i data-feather="bell"></i>
                        Notifications
                    </button>
                    <button class="btn btn-primary" onclick="openModal('createOrgModal')">
                        <i data-feather="plus"></i>
                        Nouvelle Organisation
                    </button>
                </div>
            </header>

            <div class="content-area">
                <!-- Dashboard Page -->
                <div id="dashboard" class="page-content active">
                    <div class="page-header">
                        <h2 class="page-title">Vue d'ensemble</h2>
                        <p class="page-subtitle">Statistiques globales de la plateforme</p>
                    </div>

                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-value">3</div>
                            <div class="stat-label">Organisations Actives</div>
                            <div class="stat-change positive">+1 ce mois</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value">15</div>
                            <div class="stat-label">Événements Créés</div>
                            <div class="stat-change positive">+5 cette semaine</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value">247</div>
                            <div class="stat-label">Inscriptions Totales</div>
                            <div class="stat-change positive">+67 ce mois</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value">€12,450</div>
                            <div class="stat-label">Revenus Totaux</div>
                            <div class="stat-change positive">+23% vs mois dernier</div>
                        </div>
                    </div>

                    <div class="dashboard-grid">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Organisations Récentes</h3>
                            </div>
                            <div class="table-container">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Organisation</th>
                                            <th>Type</th>
                                            <th>Plan</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>INF JCI-CI</td>
                                            <td>JCI</td>
                                            <td>Premium</td>
                                            <td><span class="badge badge-success">Actif</span></td>
                                        </tr>
                                        <tr>
                                            <td>JCI Emeraude</td>
                                            <td>JCI</td>
                                            <td>Premium</td>
                                            <td><span class="badge badge-success">Actif</span></td>
                                        </tr>
                                        <tr>
                                            <td>JCI Nirvana</td>
                                            <td>JCI</td>
                                            <td>Premium</td>
                                            <td><span class="badge badge-success">Actif</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Activité Récente</h3>
                            </div>
                            <div style="space-y: 1rem;">
                                <div style="padding: 0.75rem; border-left: 3px solid var(--success); background: rgba(15, 157, 88, 0.05); margin-bottom: 0.5rem;">
                                    <strong>Nouvelle organisation créée</strong><br>
                                    <small>INF JCI-CI - il y a 2 jours</small>
                                </div>
                                <div style="padding: 0.75rem; border-left: 3px solid var(--primary); background: rgba(26, 115, 232, 0.05); margin-bottom: 0.5rem;">
                                    <strong>Événement publié</strong><br>
                                    <small>1er Dîner Gala - il y a 3 jours</small>
                                </div>
                                <div style="padding: 0.75rem; border-left: 3px solid var(--warning); background: rgba(255, 152, 0, 0.05); margin-bottom: 0.5rem;">
                                    <strong>Paiement échoué</strong><br>
                                    <small>Transaction Wave - il y a 1 heure</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Organizations Page -->
                <div id="organizations" class="page-content">
                    <div class="page-header">
                        <h2 class="page-title">Gestion des Organisations</h2>
                        <p class="page-subtitle">Créer et gérer les organisations clientes</p>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Organisations</h3>
                            <button class="btn btn-primary" onclick="openModal('createOrgModal')">
                                <i data-feather="plus"></i>
                                Nouvelle Organisation
                            </button>
                        </div>
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Organisation</th>
                                        <th>Type</th>
                                        <th>Contact</th>
                                        <th>Plan</th>
                                        <th>Statut</th>
                                        <th>Créé le</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="organizationsTable">
                                    <tr>
                                        <td>
                                            <strong>INF JCI-CI</strong><br>
                                            <small>inf-jci-ci</small>
                                        </td>
                                        <td>JCI</td>
                                        <td>inf.jcici01@gmail.com</td>
                                        <td><span class="badge badge-info">Premium</span></td>
                                        <td><span class="badge badge-success">Actif</span></td>
                                        <td>02/07/2025</td>
                                        <td>
                                            <button class="btn btn-secondary" onclick="editOrganization(3)">
                                                <i data-feather="edit"></i>
                                            </button>
                                            <button class="btn btn-primary" onclick="openModal('createEventModal', 3)">
                                                <i data-feather="calendar"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>JCI Emeraude</strong><br>
                                            <small>jci-emeraude</small>
                                        </td>
                                        <td>JCI</td>
                                        <td>president@jci-emeraude.ci</td>
                                        <td><span class="badge badge-info">Premium</span></td>
                                        <td><span class="badge badge-success">Actif</span></td>
                                        <td>27/06/2025</td>
                                        <td>
                                            <button class="btn btn-secondary" onclick="editOrganization(2)">
                                                <i data-feather="edit"></i>
                                            </button>
                                            <button class="btn btn-primary" onclick="openModal('createEventModal', 2)">
                                                <i data-feather="calendar"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Events Page -->
                <div id="events" class="page-content">
                    <div class="page-header">
                        <h2 class="page-title">Gestion des Événements</h2>
                        <p class="page-subtitle">Créer et configurer les événements</p>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Événements</h3>
                            <button class="btn btn-primary" onclick="openModal('createEventModal')">
                                <i data-feather="plus"></i>
                                Nouvel Événement
                            </button>
                        </div>
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Événement</th>
                                        <th>Organisation</th>
                                        <th>Date</th>
                                        <th>Statut</th>
                                        <th>Inscriptions</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <strong>1er DÎNER GALA DE L'INF JCI CI</strong><br>
                                            <small>Salon d'honneur du Stade de San Pedro</small>
                                        </td>
                                        <td>INF JCI-CI</td>
                                        <td>25/07/2025</td>
                                        <td><span class="badge badge-success">Publié</span></td>
                                        <td>15/200</td>
                                        <td>
                                            <button class="btn btn-secondary" onclick="editEvent(1)">
                                                <i data-feather="edit"></i>
                                            </button>
                                            <button class="btn btn-primary" onclick="openModal('editFormModal', 1)">
                                                <i data-feather="file-text"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Users Page -->
                <div id="users" class="page-content">
                    <div class="page-header">
                        <h2 class="page-title">Gestion des Utilisateurs</h2>
                        <p class="page-subtitle">Gérer les utilisateurs du système</p>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Utilisateurs</h3>
                        </div>
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Utilisateur</th>
                                        <th>Email</th>
                                        <th>Organisation</th>
                                        <th>Rôle</th>
                                        <th>Statut</th>
                                        <th>Dernière connexion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Aubin N'douffou</td>
                                        <td>inf.jcici01@gmail.com</td>
                                        <td>INF JCI-CI</td>
                                        <td><span class="badge badge-danger">Owner</span></td>
                                        <td><span class="badge badge-success">Actif</span></td>
                                        <td>04/07/2025</td>
                                    </tr>
                                    <tr>
                                        <td>Président JCI Emeraude</td>
                                        <td>president@jci-emeraude.ci</td>
                                        <td>JCI Emeraude</td>
                                        <td><span class="badge badge-danger">Owner</span></td>
                                        <td><span class="badge badge-success">Actif</span></td>
                                        <td>02/07/2025</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Analytics Page -->
                <div id="analytics" class="page-content">
                    <div class="page-header">
                        <h2 class="page-title">Analytics</h2>
                        <p class="page-subtitle">Statistiques et analyses détaillées</p>
                    </div>

                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-value">92%</div>
                            <div class="stat-label">Taux de Conversion</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value">€4,250</div>
                            <div class="stat-label">Revenus ce Mois</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value">156</div>
                            <div class="stat-label">Nouvelles Inscriptions</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value">99.2%</div>
                            <div class="stat-label">Disponibilité</div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Revenus par Organisation</h3>
                        </div>
                        <div style="padding: 2rem; text-align: center; color: var(--text-light);">
                            <i data-feather="bar-chart" style="width: 48px; height: 48px; margin-bottom: 1rem;"></i>
                            <p>Graphiques d'analytics disponibles ici</p>
                        </div>
                    </div>
                </div>

                <!-- Billing Page -->
                <div id="billing" class="page-content">
                    <div class="page-header">
                        <h2 class="page-title">Facturation</h2>
                        <p class="page-subtitle">Gestion des abonnements et facturation</p>
                    </div>

                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-value">€2,397</div>
                            <div class="stat-label">MRR (Monthly Recurring Revenue)</div>
                            <div class="stat-change positive">+15% vs mois dernier</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value">€28,764</div>
                            <div class="stat-label">ARR (Annual Recurring Revenue)</div>
                            <div class="stat-change positive">+23% vs année dernière</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value">3</div>
                            <div class="stat-label">Abonnements Actifs</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value">0</div>
                            <div class="stat-label">Factures en Retard</div>
                        </div>
                    </div>

                    <div class="dashboard-grid">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Plans d'Abonnement</h3>
                                <button class="btn btn-primary" onclick="openModal('createPlanModal')">
                                    <i data-feather="plus"></i>
                                    Nouveau Plan
                                </button>
                            </div>
                            <div class="table-container">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Plan</th>
                                            <th>Prix Mensuel</th>
                                            <th>Prix Annuel</th>
                                            <th>Abonnés</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Premium</strong></td>
                                            <td>€79.99</td>
                                            <td>€799.99</td>
                                            <td>3</td>
                                            <td>
                                                <button class="btn btn-secondary">
                                                    <i data-feather="edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Basic</strong></td>
                                            <td>€29.99</td>
                                            <td>€299.99</td>
                                            <td>0</td>
                                            <td>
                                                <button class="btn btn-secondary">
                                                    <i data-feather="edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Factures Récentes</h3>
                            </div>
                            <div style="padding: 2rem; text-align: center; color: var(--text-light);">
                                <i data-feather="file-text" style="width: 48px; height: 48px; margin-bottom: 1rem;"></i>
                                <p>Aucune facture générée</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Settings Page -->
                <div id="settings" class="page-content">
                    <div class="page-header">
                        <h2 class="page-title">Paramètres Système</h2>
                        <p class="page-subtitle">Configuration globale de la plateforme</p>
                    </div>

                    <div class="dashboard-grid">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Paramètres Généraux</h3>
                            </div>
                            <form>
                                <div class="form-group">
                                    <label class="form-label">Nom de l'Application</label>
                                    <input type="text" class="form-control" value="EventSaaS Platform">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Durée d'Essai (jours)</label>
                                    <input type="number" class="form-control" value="14">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Maximum d'Organisations</label>
                                    <input type="number" class="form-control" value="1000">
                                </div>
                                <button type="button" class="btn btn-primary">Sauvegarder</button>
                            </form>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Templates de Base de Données</h3>
                            </div>
                            <div class="table-container">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Template</th>
                                            <th>Type</th>
                                            <th>Version</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>JCI Standard Template</td>
                                            <td>JCI</td>
                                            <td>1.0</td>
                                            <td><span class="badge badge-success">Actif</span></td>
                                        </tr>
                                        <tr>
                                            <td>Rotary Club Template</td>
                                            <td>Rotary</td>
                                            <td>1.0</td>
                                            <td><span class="badge badge-success">Actif</span></td>
                                        </tr>
                                        <tr>
                                            <td>Association Template</td>
                                            <td>Association</td>
                                            <td>1.0</td>
                                            <td><span class="badge badge-success">Actif</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modals -->
    
    <!-- Create Organization Modal -->
    <div id="createOrgModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Créer une Nouvelle Organisation</h3>
                <button class="close-modal" onclick="closeModal('createOrgModal')">&times;</button>
            </div>
            <form id="createOrgForm">
                <div class="form-group">
                    <label class="form-label">Nom de l'Organisation</label>
                    <input type="text" class="form-control" name="org_name" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Clé Organisation</label>
                        <input type="text" class="form-control" name="org_key" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Type</label>
                        <select class="form-control" name="org_type" required>
                            <option value="">Sélectionner</option>
                            <option value="jci">JCI</option>
                            <option value="rotary">Rotary</option>
                            <option value="lions">Lions</option>
                            <option value="association">Association</option>
                            <option value="company">Entreprise</option>
                            <option value="other">Autre</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nom du Contact</label>
                        <input type="text" class="form-control" name="contact_name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email du Contact</label>
                        <input type="email" class="form-control" name="contact_email" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Téléphone</label>
                        <input type="tel" class="form-control" name="contact_phone">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sous-domaine</label>
                        <input type="text" class="form-control" name="subdomain" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Plan d'Abonnement</label>
                    <select class="form-control" name="plan_code" required>
                        <option value="trial">Essai Gratuit</option>
                        <option value="basic">Basic</option>
                        <option value="premium">Premium</option>
                        <option value="enterprise">Entreprise</option>
                    </select>
                </div>
                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('createOrgModal')">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer l'Organisation</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Create Event Modal -->
    <div id="createEventModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Créer un Nouvel Événement</h3>
                <button class="close-modal" onclick="closeModal('createEventModal')">&times;</button>
            </div>
            <form id="createEventForm">
                <div class="form-group">
                    <label class="form-label">Organisation</label>
                    <select class="form-control" name="organization_id" required>
                        <option value="">Sélectionner une organisation</option>
                        <option value="1">JCI Nirvana</option>
                        <option value="2">JCI Emeraude</option>
                        <option value="3">INF JCI-CI</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Titre de l'Événement</label>
                    <input type="text" class="form-control" name="event_title" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="event_description" rows="3"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Date</label>
                        <input type="date" class="form-control" name="event_date" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Lieu</label>
                        <input type="text" class="form-control" name="event_location" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Heure de Début</label>
                        <input type="time" class="form-control" name="event_start_time">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Heure de Fin</label>
                        <input type="time" class="form-control" name="event_end_time">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Couleur Principale</label>
                        <input type="color" class="form-control" name="primary_color" value="#1a73e8">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Couleur Secondaire</label>
                        <input type="color" class="form-control" name="secondary_color" value="#34a853">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Nombre Maximum de Participants</label>
                    <input type="number" class="form-control" name="max_participants" min="1">
                </div>
                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('createEventModal')">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer l'Événement</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Form Fields Modal -->
    <div id="editFormModal" class="modal">
        <div class="modal-content" style="max-width: 900px;">
            <div class="modal-header">
                <h3 class="modal-title">Configurateur de Formulaire</h3>
                <button class="close-modal" onclick="closeModal('editFormModal')">&times;</button>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <!-- Section Builder -->
                <div>
                    <h4>Sections du Formulaire</h4>
                    <div class="field-builder">
                        <div class="form-group">
                            <label class="form-label">Nom de la Section</label>
                            <input type="text" class="form-control" id="sectionName">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Titre Affiché</label>
                            <input type="text" class="form-control" id="sectionTitle">
                        </div>
                        <button type="button" class="btn btn-primary" onclick="addSection()">
                            <i data-feather="plus"></i>
                            Ajouter Section
                        </button>
                    </div>

                    <div id="sectionsList" class="field-list">
                        <div class="field-item">
                            <div>
                                <strong>personal_info</strong>
                                <br><small>Informations Personnelles</small>
                            </div>
                            <div class="field-actions">
                                <button class="btn btn-secondary">
                                    <i data-feather="edit"></i>
                                </button>
                                <button class="btn btn-danger">
                                    <i data-feather="trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="field-item">
                            <div>
                                <strong>professional_info</strong>
                                <br><small>Informations Professionnelles</small>
                            </div>
                            <div class="field-actions">
                                <button class="btn btn-secondary">
                                    <i data-feather="edit"></i>
                                </button>
                                <button class="btn btn-danger">
                                    <i data-feather="trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Field Builder -->
                <div>
                    <h4>Champs du Formulaire</h4>
                    <div class="field-builder">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Clé du Champ</label>
                                <input type="text" class="form-control" id="fieldKey">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Type</label>
                                <select class="form-control" id="fieldType" onchange="updateFieldOptions()">
                                    <option value="text">Texte</option>
                                    <option value="email">Email</option>
                                    <option value="phone">Téléphone</option>
                                    <option value="number">Nombre</option>
                                    <option value="date">Date</option>
                                    <option value="select">Liste déroulante</option>
                                    <option value="textarea">Zone de texte</option>
                                    <option value="checkbox">Case à cocher</option>
                                    <option value="radio">Boutons radio</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Label</label>
                            <input type="text" class="form-control" id="fieldLabel">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Section</label>
                            <select class="form-control" id="fieldSection">
                                <option value="personal_info">Informations Personnelles</option>
                                <option value="professional_info">Informations Professionnelles</option>
                            </select>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" id="fieldRequired"> Requis
                                </label>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Largeur</label>
                                <select class="form-control" id="fieldWidth">
                                    <option value="full">Pleine largeur</option>
                                    <option value="half">Demi-largeur</option>
                                    <option value="third">Tiers</option>
                                </select>
                            </div>
                        </div>

                        <div id="fieldOptions" style="display: none;">
                            <div class="form-group">
                                <label class="form-label">Options (une par ligne)</label>
                                <textarea class="form-control" id="fieldOptionsText" rows="3" placeholder="Option 1&#10;Option 2&#10;Option 3"></textarea>
                            </div>
                        </div>

                        <button type="button" class="btn btn-primary" onclick="addField()">
                            <i data-feather="plus"></i>
                            Ajouter Champ
                        </button>
                    </div>

                    <!-- Field Preview -->
                    <div class="field-preview">
                        <h5>Aperçu</h5>
                        <div id="fieldPreviewContent">
                            <div class="form-group">
                                <label class="form-label">Exemple de champ</label>
                                <input type="text" class="form-control" placeholder="Aperçu du champ">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fields List -->
            <div style="margin-top: 2rem;">
                <h4>Champs Existants</h4>
                <div id="fieldsList" class="field-list">
                    <div class="field-item">
                        <div>
                            <strong>full_name</strong> (text)
                            <br><small>Nom et Prénoms - Requis</small>
                        </div>
                        <div class="field-actions">
                            <button class="btn btn-secondary">
                                <i data-feather="edit"></i>
                            </button>
                            <button class="btn btn-danger">
                                <i data-feather="trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="field-item">
                        <div>
                            <strong>email</strong> (email)
                            <br><small>Adresse Email - Requis</small>
                        </div>
                        <div class="field-actions">
                            <button class="btn btn-secondary">
                                <i data-feather="edit"></i>
                            </button>
                            <button class="btn btn-danger">
                                <i data-feather="trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="field-item">
                        <div>
                            <strong>phone</strong> (country_phone)
                            <br><small>Numéro de téléphone - Requis</small>
                        </div>
                        <div class="field-actions">
                            <button class="btn btn-secondary">
                                <i data-feather="edit"></i>
                            </button>
                            <button class="btn btn-danger">
                                <i data-feather="trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem; padding-top: 1rem; border-top: 1px solid var(--border);">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editFormModal')">Fermer</button>
                <button type="button" class="btn btn-primary" onclick="saveFormConfiguration()">Sauvegarder</button>
            </div>
        </div>
    </div>

    <!-- Create Plan Modal -->
    <div id="createPlanModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Créer un Plan d'Abonnement</h3>
                <button class="close-modal" onclick="closeModal('createPlanModal')">&times;</button>
            </div>
            <form id="createPlanForm">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nom du Plan</label>
                        <input type="text" class="form-control" name="plan_name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Code</label>
                        <input type="text" class="form-control" name="plan_code" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Prix Mensuel (€)</label>
                        <input type="number" class="form-control" name="monthly_price" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Prix Annuel (€)</label>
                        <input type="number" class="form-control" name="yearly_price" step="0.01" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Max Événements</label>
                        <input type="number" class="form-control" name="max_events" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Max Participants/Événement</label>
                        <input type="number" class="form-control" name="max_participants_per_event" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Stockage (MB)</label>
                        <input type="number" class="form-control" name="max_storage_mb" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Max Utilisateurs</label>
                        <input type="number" class="form-control" name="max_users" required>
                    </div>
                </div>
                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('createPlanModal')">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer le Plan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading">
        <div class="spinner"></div>
        <p>Traitement en cours...</p>
    </div>

    <script>
        // Initialize Feather Icons
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();
        });

        // Navigation
        function switchPage(pageId) {
            // Hide all pages
            document.querySelectorAll('.page-content').forEach(page => {
                page.classList.remove('active');
            });
            
            // Show selected page
            document.getElementById(pageId).classList.add('active');
            
            // Update navigation
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            document.querySelector(`[data-page="${pageId}"]`).classList.add('active');
            
            // Update header title
            const titles = {
                dashboard: 'Super Admin Dashboard',
                organizations: 'Gestion des Organisations',
                events: 'Gestion des Événements',
                users: 'Gestion des Utilisateurs',
                analytics: 'Analytics',
                billing: 'Facturation',
                settings: 'Paramètres Système'
            };
            document.querySelector('.header-title').textContent = titles[pageId] || 'Dashboard';
        }

        // Add navigation event listeners
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = this.getAttribute('data-page');
                if (page) {
                    switchPage(page);
                }
            });
        });

        // Modal Functions
        function openModal(modalId, ...args) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
                
                // Handle specific modal setups
                if (modalId === 'createEventModal' && args[0]) {
                    // Pre-select organization if provided
                    const orgSelect = modal.querySelector('[name="organization_id"]');
                    if (orgSelect) {
                        orgSelect.value = args[0];
                    }
                }
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('active');
                document.body.style.overflow = 'auto';
                
                // Reset form if exists
                const form = modal.querySelector('form');
                if (form) {
                    form.reset();
                }
            }
        }

        // Close modal when clicking outside
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal(this.id);
                }
            });
        });

        // Form Handlers
        document.getElementById('createOrgForm').addEventListener('submit', function(e) {
            e.preventDefault();
            showLoading();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            
            // Simulate API call
            setTimeout(() => {
                console.log('Creating organization:', data);
                hideLoading();
                closeModal('createOrgModal');
                showNotification('Organisation créée avec succès!', 'success');
                // Refresh organizations table
                loadOrganizations();
            }, 2000);
        });

        document.getElementById('createEventForm').addEventListener('submit', function(e) {
            e.preventDefault();
            showLoading();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            
            // Simulate API call
            setTimeout(() => {
                console.log('Creating event:', data);
                hideLoading();
                closeModal('createEventModal');
                showNotification('Événement créé avec succès!', 'success');
                // Refresh events table
                loadEvents();
            }, 2000);
        });

        document.getElementById('createPlanForm').addEventListener('submit', function(e) {
            e.preventDefault();
            showLoading();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            
            // Simulate API call
            setTimeout(() => {
                console.log('Creating plan:', data);
                hideLoading();
                closeModal('createPlanModal');
                showNotification('Plan créé avec succès!', 'success');
            }, 1500);
        });

        // Field Builder Functions
        function updateFieldOptions() {
            const fieldType = document.getElementById('fieldType').value;
            const optionsDiv = document.getElementById('fieldOptions');
            
            if (fieldType === 'select' || fieldType === 'radio' || fieldType === 'checkbox_group') {
                optionsDiv.style.display = 'block';
            } else {
                optionsDiv.style.display = 'none';
            }
            
            updateFieldPreview();
        }

        function updateFieldPreview() {
            const fieldType = document.getElementById('fieldType').value;
            const fieldLabel = document.getElementById('fieldLabel').value || 'Exemple de champ';
            const fieldRequired = document.getElementById('fieldRequired').checked;
            const previewContent = document.getElementById('fieldPreviewContent');
            
            let html = `<div class="form-group">
                <label class="form-label">${fieldLabel}${fieldRequired ? ' *' : ''}</label>`;
            
            switch (fieldType) {
                case 'text':
                    html += `<input type="text" class="form-control" placeholder="Entrez ${fieldLabel.toLowerCase()}">`;
                    break;
                case 'email':
                    html += `<input type="email" class="form-control" placeholder="exemple@email.com">`;
                    break;
                case 'phone':
                    html += `<input type="tel" class="form-control" placeholder="+225 XX XX XX XX XX">`;
                    break;
                case 'number':
                    html += `<input type="number" class="form-control" placeholder="0">`;
                    break;
                case 'date':
                    html += `<input type="date" class="form-control">`;
                    break;
                case 'textarea':
                    html += `<textarea class="form-control" rows="3" placeholder="Entrez ${fieldLabel.toLowerCase()}"></textarea>`;
                    break;
                case 'select':
                    const options = document.getElementById('fieldOptionsText').value.split('\n').filter(o => o.trim());
                    html += `<select class="form-control">
                        <option value="">Sélectionner</option>`;
                    options.forEach(option => {
                        html += `<option value="${option.trim()}">${option.trim()}</option>`;
                    });
                    html += `</select>`;
                    break;
                case 'radio':
                    const radioOptions = document.getElementById('fieldOptionsText').value.split('\n').filter(o => o.trim());
                    radioOptions.forEach((option, index) => {
                        html += `<div>
                            <label>
                                <input type="radio" name="preview_radio" value="${option.trim()}">
                                ${option.trim()}
                            </label>
                        </div>`;
                    });
                    break;
                case 'checkbox':
                    html += `<label>
                        <input type="checkbox">
                        ${fieldLabel}
                    </label>`;
                    break;
                default:
                    html += `<input type="text" class="form-control" placeholder="Aperçu du champ">`;
            }
            
            html += `</div>`;
            previewContent.innerHTML = html;
        }

        function addSection() {
            const sectionName = document.getElementById('sectionName').value;
            const sectionTitle = document.getElementById('sectionTitle').value;
            
            if (!sectionName || !sectionTitle) {
                showNotification('Veuillez remplir tous les champs de la section', 'warning');
                return;
            }
            
            // Add to sections list
            const sectionsList = document.getElementById('sectionsList');
            const sectionItem = document.createElement('div');
            sectionItem.className = 'field-item';
            sectionItem.innerHTML = `
                <div>
                    <strong>${sectionName}</strong>
                    <br><small>${sectionTitle}</small>
                </div>
                <div class="field-actions">
                    <button class="btn btn-secondary" onclick="editSection('${sectionName}')">
                        <i data-feather="edit"></i>
                    </button>
                    <button class="btn btn-danger" onclick="deleteSection('${sectionName}')">
                        <i data-feather="trash"></i>
                    </button>
                </div>
            `;
            sectionsList.appendChild(sectionItem);
            
            // Add to field section select
            const fieldSection = document.getElementById('fieldSection');
            const option = document.createElement('option');
            option.value = sectionName;
            option.textContent = sectionTitle;
            fieldSection.appendChild(option);
            
            // Clear form
            document.getElementById('sectionName').value = '';
            document.getElementById('sectionTitle').value = '';
            
            // Reinitialize icons
            feather.replace();
            
            showNotification('Section ajoutée avec succès!', 'success');
        }

        function addField() {
            const fieldKey = document.getElementById('fieldKey').value;
            const fieldLabel = document.getElementById('fieldLabel').value;
            const fieldType = document.getElementById('fieldType').value;
            const fieldSection = document.getElementById('fieldSection').value;
            const fieldRequired = document.getElementById('fieldRequired').checked;
            const fieldWidth = document.getElementById('fieldWidth').value;
            
            if (!fieldKey || !fieldLabel || !fieldType || !fieldSection) {
                showNotification('Veuillez remplir tous les champs obligatoires', 'warning');
                return;
            }
            
            // Add to fields list
            const fieldsList = document.getElementById('fieldsList');
            const fieldItem = document.createElement('div');
            fieldItem.className = 'field-item';
            fieldItem.innerHTML = `
                <div>
                    <strong>${fieldKey}</strong> (${fieldType})
                    <br><small>${fieldLabel}${fieldRequired ? ' - Requis' : ''}</small>
                </div>
                <div class="field-actions">
                    <button class="btn btn-secondary" onclick="editField('${fieldKey}')">
                        <i data-feather="edit"></i>
                    </button>
                    <button class="btn btn-danger" onclick="deleteField('${fieldKey}')">
                        <i data-feather="trash"></i>
                    </button>
                </div>
            `;
            fieldsList.appendChild(fieldItem);
            
            // Clear form
            document.getElementById('fieldKey').value = '';
            document.getElementById('fieldLabel').value = '';
            document.getElementById('fieldType').value = 'text';
            document.getElementById('fieldRequired').checked = false;
            document.getElementById('fieldWidth').value = 'full';
            document.getElementById('fieldOptionsText').value = '';
            
            // Update preview
            updateFieldPreview();
            updateFieldOptions();
            
            // Reinitialize icons
            feather.replace();
            
            showNotification('Champ ajouté avec succès!', 'success');
        }

        function saveFormConfiguration() {
            showLoading();
            
            // Simulate API call to save form configuration
            setTimeout(() => {
                hideLoading();
                closeModal('editFormModal');
                showNotification('Configuration du formulaire sauvegardée!', 'success');
            }, 1500);
        }

        function editSection(sectionName) {
            showNotification(`Édition de la section: ${sectionName}`, 'info');
        }

        function deleteSection(sectionName) {
            if (confirm(`Êtes-vous sûr de vouloir supprimer la section "${sectionName}" ?`)) {
                // Remove from DOM
                const sectionItems = document.querySelectorAll('#sectionsList .field-item');
                sectionItems.forEach(item => {
                    if (item.querySelector('strong').textContent === sectionName) {
                        item.remove();
                    }
                });
                
                // Remove from select
                const fieldSection = document.getElementById('fieldSection');
                const option = fieldSection.querySelector(`option[value="${sectionName}"]`);
                if (option) option.remove();
                
                showNotification('Section supprimée!', 'success');
            }
        }

        function editField(fieldKey) {
            showNotification(`Édition du champ: ${fieldKey}`, 'info');
        }

        function deleteField(fieldKey) {
            if (confirm(`Êtes-vous sûr de vouloir supprimer le champ "${fieldKey}" ?`)) {
                const fieldItems = document.querySelectorAll('#fieldsList .field-item');
                fieldItems.forEach(item => {
                    if (item.querySelector('strong').textContent === fieldKey) {
                        item.remove();
                    }
                });
                showNotification('Champ supprimé!', 'success');
            }
        }

        function editOrganization(orgId) {
            showNotification(`Édition de l'organisation ID: ${orgId}`, 'info');
        }

        function editEvent(eventId) {
            showNotification(`Édition de l'événement ID: ${eventId}`, 'info');
        }

        // Utility Functions
        function showLoading() {
            document.getElementById('loadingOverlay').style.display = 'flex';
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').style.display = 'none';
        }

        function showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 1rem 1.5rem;
                border-radius: 8px;
                color: white;
                font-weight: 500;
                z-index: 9999;
                animation: slideInRight 0.3s ease;
                max-width: 400px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            `;
            
            // Set color based on type
            const colors = {
                success: '#0f9d58',
                warning: '#ff9800',
                danger: '#f44336',
                info: '#1a73e8'
            };
            notification.style.backgroundColor = colors[type] || colors.info;
            notification.textContent = message;
            
            // Add to DOM
            document.body.appendChild(notification);
            
            // Remove after delay
            setTimeout(() => {
                notification.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }

        // Data Loading Functions
        function loadOrganizations() {
            // Simulate API call to refresh organizations data
            console.log('Loading organizations...');
        }

        function loadEvents() {
            // Simulate API call to refresh events data
            console.log('Loading events...');
        }

        function loadUsers() {
            // Simulate API call to refresh users data
            console.log('Loading users...');
        }

        // Initialize field preview on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateFieldPreview();
            updateFieldOptions();
            
            // Add event listeners for field builder
            document.getElementById('fieldType').addEventListener('change', updateFieldOptions);
            document.getElementById('fieldLabel').addEventListener('input', updateFieldPreview);
            document.getElementById('fieldRequired').addEventListener('change', updateFieldPreview);
            document.getElementById('fieldOptionsText').addEventListener('input', updateFieldPreview);
        });

        // Add CSS animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            @keyframes slideOutRight {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
            
            #loadingOverlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(255, 255, 255, 0.9);
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                z-index: 9998;
                backdrop-filter: blur(4px);
            }
            
            .loading p {
                margin-top: 1rem;
                color: var(--text-light);
                font-weight: 500;
            }
            
            /* Mobile responsiveness improvements */
            @media (max-width: 768px) {
                .modal-content {
                    width: 95%;
                    padding: 1rem;
                    margin: 1rem;
                }
                
                .form-row {
                    grid-template-columns: 1fr;
                }
                
                .dashboard-grid {
                    grid-template-columns: 1fr;
                }
                
                .stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
                
                .field-builder {
                    padding: 0.75rem;
                }
                
                .field-item {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 0.5rem;
                }
                
                .field-actions {
                    align-self: flex-end;
                }
                
                .table-container {
                    overflow-x: auto;
                }
                
                .table {
                    min-width: 600px;
                }
            }
            
            @media (max-width: 480px) {
                .stats-grid {
                    grid-template-columns: 1fr;
                }
                
                .header-actions {
                    flex-direction: column;
                    gap: 0.5rem;
                }
                
                .btn {
                    font-size: 0.8rem;
                    padding: 0.4rem 0.8rem;
                }
                
                .modal-content {
                    max-height: 95vh;
                }
            }
            
            /* Enhanced form styling */
            .form-control:focus {
                border-color: var(--primary);
                box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.1);
            }
            
            .form-group label {
                font-weight: 500;
                margin-bottom: 0.5rem;
                display: block;
            }
            
            /* Enhanced table styling */
            .table tbody tr:hover {
                background-color: rgba(26, 115, 232, 0.05);
                transition: background-color 0.2s ease;
            }
            
            /* Enhanced button styling */
            .btn {
                transition: all 0.2s ease;
                border: none;
                cursor: pointer;
            }
            
            .btn:hover {
                transform: translateY(-1px);
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            }
            
            .btn:active {
                transform: translateY(0);
            }
            
            /* Enhanced card styling */
            .card {
                transition: box-shadow 0.3s ease;
            }
            
            .card:hover {
                box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            }
            
            /* Enhanced sidebar */
            .nav-link {
                transition: all 0.3s ease;
            }
            
            .nav-link:hover {
                background: rgba(26, 115, 232, 0.1);
                transform: translateX(4px);
            }
            
            /* Enhanced modal */
            .modal-content {
                box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            }
            
            /* Field builder enhancements */
            .field-builder {
                border: 2px dashed var(--border);
                transition: border-color 0.3s ease;
            }
            
            .field-builder:hover {
                border-color: var(--primary);
            }
            
            .field-preview {
                border: 1px solid var(--border);
                border-radius: 8px;
                transition: box-shadow 0.3s ease;
            }
            
            .field-preview:hover {
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            }
        `;
        document.head.appendChild(style);

        // Advanced search functionality (placeholder)
        function initializeSearch() {
            const searchInputs = document.querySelectorAll('.search-input');
            searchInputs.forEach(input => {
                input.addEventListener('input', function(e) {
                    const searchTerm = e.target.value.toLowerCase();
                    // Implement search logic here
                    console.log('Searching for:', searchTerm);
                });
            });
        }

        // Export functionality (placeholder)
        function exportData(type, format) {
            showLoading();
            setTimeout(() => {
                hideLoading();
                showNotification(`Export ${type} en cours (format: ${format})`, 'info');
            }, 1000);
        }

        // Bulk operations (placeholder)
        function performBulkOperation(operation, selectedItems) {
            showLoading();
            setTimeout(() => {
                hideLoading();
                showNotification(`Opération "${operation}" effectuée sur ${selectedItems.length} éléments`, 'success');
            }, 1500);
        }

        // Real-time updates (placeholder)
        function initializeRealtimeUpdates() {
            // Simulate real-time updates every 30 seconds
            setInterval(() => {
                // Update statistics, notifications, etc.
                console.log('Updating real-time data...');
            }, 30000);
        }

        // Initialize all features
        document.addEventListener('DOMContentLoaded', function() {
            initializeSearch();
            initializeRealtimeUpdates();
            console.log('SaaS Dashboard initialized successfully');
        });
    </script>
</body>
</html>