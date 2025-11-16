<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapports et Analytics - EventSaaS Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .sidebar-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            display: block;
            text-decoration: none;
            border-radius: 8px;
            margin: 4px 12px;
            transition: all 0.3s;
        }
        
        .sidebar-link:hover,
        .sidebar-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            transform: translateX(5px);
        }
        
        .report-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: none;
            transition: transform 0.3s;
            cursor: pointer;
        }
        
        .report-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .report-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 15px;
        }
        
        .report-icon.organizations {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .report-icon.revenue {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .report-icon.usage {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }
        
        .report-icon.events {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }
        
        .filter-panel {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            transition: all 0.3s;
        }
        
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .navbar-brand {
            font-weight: bold;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .export-btn {
            border: 2px solid #667eea;
            color: #667eea;
            background: transparent;
            transition: all 0.3s;
        }
        
        .export-btn:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }
        
        .data-table {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .chart-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            padding: 20px;
        }
        
        .metric-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            transition: transform 0.3s;
        }
        
        .metric-card:hover {
            transform: translateY(-5px);
        }
        
        .metric-card.blue {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .metric-card.green {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .metric-card.yellow {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }
        
        .metric-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .metric-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .metric-change {
            font-size: 0.8rem;
            margin-top: 10px;
        }
        
        .period-selector {
            background: rgba(102, 126, 234, 0.1);
            border: 2px solid #667eea;
            color: #667eea;
            border-radius: 25px;
            padding: 8px 20px;
            margin: 0 5px;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .period-selector.active {
            background: #667eea;
            color: white;
        }
        
        .progress-ring {
            width: 80px;
            height: 80px;
            margin: 0 auto;
        }
        
        .progress-ring circle {
            transition: stroke-dasharray 0.5s ease;
        }
        
        .trend-indicator {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.85rem;
        }
        
        .trend-up {
            color: #10b981;
        }
        
        .trend-down {
            color: #ef4444;
        }
        
        .trend-stable {
            color: #6b7280;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar">
                    <div class="p-4">
                        <h4 class="text-white mb-4">
                            <i class="fas fa-cog me-2"></i>
                            Admin Panel
                        </h4>
                    </div>
                    
                    <nav class="nav flex-column">
                        <a href="#dashboard" class="sidebar-link">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Dashboard
                        </a>
                        <a href="#metrics" class="sidebar-link">
                            <i class="fas fa-chart-line me-2"></i>
                            Métriques
                        </a>
                        <a href="#organizations" class="sidebar-link">
                            <i class="fas fa-building me-2"></i>
                            Organisations
                        </a>
                        <a href="#logs" class="sidebar-link">
                            <i class="fas fa-file-alt me-2"></i>
                            Logs Système
                        </a>
                        <a href="#templates" class="sidebar-link">
                            <i class="fas fa-code me-2"></i>
                            Templates DB
                        </a>
                        <a href="#maintenance" class="sidebar-link">
                            <i class="fas fa-tools me-2"></i>
                            Maintenance
                        </a>
                        <a href="#settings" class="sidebar-link">
                            <i class="fas fa-cogs me-2"></i>
                            Paramètres
                        </a>
                        <a href="#reports" class="sidebar-link active">
                            <i class="fas fa-file-export me-2"></i>
                            Rapports
                        </a>
                    </nav>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <!-- Top Navigation -->
                <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
                    <div class="container-fluid">
                        <span class="navbar-brand">Rapports et Analytics</span>
                        
                        <div class="d-flex align-items-center">
                            <div class="dropdown me-3">
                                <button class="btn export-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-download me-2"></i>
                                    Export Global
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="exportReport('pdf')"><i class="fas fa-file-pdf me-2"></i>PDF Complet</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="exportReport('excel')"><i class="fas fa-file-excel me-2"></i>Excel Détaillé</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="exportReport('csv')"><i class="fas fa-file-csv me-2"></i>CSV Data</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#" onclick="scheduleReport()"><i class="fas fa-clock me-2"></i>Programmer Export</a></li>
                                </ul>
                            </div>
                            
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user me-2"></i>
                                    Administrateur
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-user-cog me-2"></i>Profil</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt me-2"></i>Déconnexion</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>
                
                <!-- Reports Content -->
                <div class="container-fluid p-4">
                    <!-- Page Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="mb-0">Rapports et Analytics</h2>
                        <div class="d-flex align-items-center">
                            <span class="me-3 text-muted">Période:</span>
                            <button class="btn period-selector" data-period="7">7 jours</button>
                            <button class="btn period-selector active" data-period="30">30 jours</button>
                            <button class="btn period-selector" data-period="90">90 jours</button>
                            <button class="btn period-selector" data-period="365">1 an</button>
                            <button class="btn period-selector" data-period="custom">Personnalisé</button>
                        </div>
                    </div>
                    
                    <!-- Filter Panel -->
                    <div class="filter-panel">
                        <div class="row align-items-end">
                            <div class="col-md-2">
                                <label class="form-label">Type de Rapport</label>
                                <select class="form-select" id="reportType" onchange="updateReportView()">
                                    <option value="overview">Vue d'ensemble</option>
                                    <option value="organizations">Organisations</option>
                                    <option value="revenue">Revenus</option>
                                    <option value="usage">Utilisation</option>
                                    <option value="events">Événements</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Plan</label>
                                <select class="form-select" id="planFilter">
                                    <option value="">Tous les plans</option>
                                    <option value="trial">Trial</option>
                                    <option value="basic">Basic</option>
                                    <option value="premium">Premium</option>
                                    <option value="enterprise">Enterprise</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Type d'Org</label>
                                <select class="form-select" id="orgTypeFilter">
                                    <option value="">Tous les types</option>
                                    <option value="jci">JCI</option>
                                    <option value="rotary">Rotary</option>
                                    <option value="lions">Lions</option>
                                    <option value="association">Association</option>
                                    <option value="company">Entreprise</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Date de</label>
                                <input type="date" class="form-control" id="dateFrom">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Date à</label>
                                <input type="date" class="form-control" id="dateTo">
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-gradient w-100" onclick="applyFilters()">
                                    <i class="fas fa-filter me-2"></i>
                                    Appliquer
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Overview Dashboard -->
                    <div id="overview-content">
                        <!-- Key Metrics -->
                        <div class="row mb-4">
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="metric-card blue">
                                    <div class="metric-number">1,247</div>
                                    <div class="metric-label">Total Organisations</div>
                                    <div class="metric-change">
                                        <span class="trend-indicator trend-up">
                                            <i class="fas fa-arrow-up"></i>
                                            +12% ce mois
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="metric-card green">
                                    <div class="metric-number">€67,450</div>
                                    <div class="metric-label">Revenus Mensuels</div>
                                    <div class="metric-change">
                                        <span class="trend-indicator trend-up">
                                            <i class="fas fa-arrow-up"></i>
                                            +23% vs mois dernier
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="metric-card yellow">
                                    <div class="metric-number">3,429</div>
                                    <div class="metric-label">Événements Créés</div>
                                    <div class="metric-change">
                                        <span class="trend-indicator trend-up">
                                            <i class="fas fa-arrow-up"></i>
                                            +8% ce mois
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="metric-card">
                                    <div class="metric-number">89.2%</div>
                                    <div class="metric-label">Taux de Rétention</div>
                                    <div class="metric-change">
                                        <span class="trend-indicator trend-up">
                                            <i class="fas fa-arrow-up"></i>
                                            +2.1% ce trimestre
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Charts Row -->
                        <div class="row mb-4">
                            <div class="col-lg-8">
                                <div class="chart-container">
                                    <h5 class="mb-3">
                                        <i class="fas fa-chart-line me-2"></i>
                                        Évolution des Revenus
                                    </h5>
                                    <canvas id="revenueChart" height="300"></canvas>
                                </div>
                            </div>
                            
                            <div class="col-lg-4">
                                <div class="chart-container">
                                    <h5 class="mb-3">
                                        <i class="fas fa-chart-pie me-2"></i>
                                        Répartition par Plan
                                    </h5>
                                    <canvas id="planDistributionChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Report Type Specific Content -->
                    <div class="row">
                        <!-- Reports Cards -->
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card report-card" onclick="generateReport('organizations')">
                                <div class="card-body text-center">
                                    <div class="report-icon organizations mx-auto">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <h5 class="card-title">Rapport Organisations</h5>
                                    <p class="card-text text-muted">Analyse détaillée des organisations, croissance, répartition géographique</p>
                                    <div class="row text-center mt-3">
                                        <div class="col-6">
                                            <strong>1,247</strong>
                                            <br><small class="text-muted">Total</small>
                                        </div>
                                        <div class="col-6">
                                            <strong>+12%</strong>
                                            <br><small class="text-success">Croissance</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card report-card" onclick="generateReport('revenue')">
                                <div class="card-body text-center">
                                    <div class="report-icon revenue mx-auto">
                                        <i class="fas fa-euro-sign"></i>
                                    </div>
                                    <h5 class="card-title">Rapport Revenus</h5>
                                    <p class="card-text text-muted">Analyse financière, revenus par plan, prévisions de croissance</p>
                                    <div class="row text-center mt-3">
                                        <div class="col-6">
                                            <strong>€67K</strong>
                                            <br><small class="text-muted">Ce mois</small>
                                        </div>
                                        <div class="col-6">
                                            <strong>+23%</strong>
                                            <br><small class="text-success">Évolution</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card report-card" onclick="generateReport('usage')">
                                <div class="card-body text-center">
                                    <div class="report-icon usage mx-auto">
                                        <i class="fas fa-chart-bar"></i>
                                    </div>
                                    <h5 class="card-title">Rapport Utilisation</h5>
                                    <p class="card-text text-muted">Statistiques d'usage, fonctionnalités populaires, engagement</p>
                                    <div class="row text-center mt-3">
                                        <div class="col-6">
                                            <strong>89%</strong>
                                            <br><small class="text-muted">Adoption</small>
                                        </div>
                                        <div class="col-6">
                                            <strong>4.2h</strong>
                                            <br><small class="text-muted">Utilisation/jour</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card report-card" onclick="generateReport('events')">
                                <div class="card-body text-center">
                                    <div class="report-icon events mx-auto">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <h5 class="card-title">Rapport Événements</h5>
                                    <p class="card-text text-muted">Analyse des événements créés, participation, succès</p>
                                    <div class="row text-center mt-3">
                                        <div class="col-6">
                                            <strong>3,429</strong>
                                            <br><small class="text-muted">Événements</small>
                                        </div>
                                        <div class="col-6">
                                            <strong>156K</strong>
                                            <br><small class="text-muted">Participants</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Detailed Reports Table -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card data-table">
                                <div class="card-header bg-transparent">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-table me-2"></i>
                                            Données Détaillées
                                        </h5>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-primary" onclick="exportTableData('csv')">
                                                <i class="fas fa-file-csv me-1"></i>CSV
                                            </button>
                                            <button class="btn btn-sm btn-outline-primary" onclick="exportTableData('excel')">
                                                <i class="fas fa-file-excel me-1"></i>Excel
                                            </button>
                                            <button class="btn btn-sm btn-outline-primary" onclick="exportTableData('pdf')">
                                                <i class="fas fa-file-pdf me-1"></i>PDF
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0" id="reportsTable">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Organisation</th>
                                                    <th>Plan</th>
                                                    <th>Type</th>
                                                    <th>Événements</th>
                                                    <th>Participants</th>
                                                    <th>Revenus</th>
                                                    <th>Dernière Activité</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                                JE
                                                            </div>
                                                            <div>
                                                                <strong>JCI Emeraude</strong>
                                                                <br><small class="text-muted">jci-emeraude</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><span class="badge bg-success">Premium</span></td>
                                                    <td>JCI</td>
                                                    <td>
                                                        <strong>24</strong>
                                                        <br><small class="text-success">+3 ce mois</small>
                                                    </td>
                                                    <td>
                                                        <strong>1,247</strong>
                                                        <br><small class="text-muted">~52/événement</small>
                                                    </td>
                                                    <td>
                                                        <strong>€1,890</strong>
                                                        <br><small class="text-success">79.99/mois</small>
                                                    </td>
                                                    <td>
                                                        <span>30/06/2025</span>
                                                        <br><small class="text-muted">14:23</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>
                                                            Actif
                                                        </span>
                                                    </td>
                                                </tr>
                                                
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                                RC
                                                            </div>
                                                            <div>
                                                                <strong>Rotary Club Abidjan</strong>
                                                                <br><small class="text-muted">rotary-abidjan</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><span class="badge bg-warning">Basic</span></td>
                                                    <td>Rotary</td>
                                                    <td>
                                                        <strong>12</strong>
                                                        <br><small class="text-success">+1 ce mois</small>
                                                    </td>
                                                    <td>
                                                        <strong>580</strong>
                                                        <br><small class="text-muted">~48/événement</small>
                                                    </td>
                                                    <td>
                                                        <strong>€360</strong>
                                                        <br><small class="text-muted">29.99/mois</small>
                                                    </td>
                                                    <td>
                                                        <span>29/06/2025</span>
                                                        <br><small class="text-muted">09:15</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>
                                                            Actif
                                                        </span>
                                                    </td>
                                                </tr>
                                                
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                                LC
                                                            </div>
                                                            <div>
                                                                <strong>Lions Club Plateau</strong>
                                                                <br><small class="text-muted">lions-plateau</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><span class="badge bg-success">Premium</span></td>
                                                    <td>Lions</td>
                                                    <td>
                                                        <strong>18</strong>
                                                        <br><small class="text-success">+2 ce mois</small>
                                                    </td>
                                                    <td>
                                                        <strong>890</strong>
                                                        <br><small class="text-muted">~49/événement</small>
                                                    </td>
                                                    <td>
                                                        <strong>€1,440</strong>
                                                        <br><small class="text-success">79.99/mois</small>
                                                    </td>
                                                    <td>
                                                        <span>30/06/2025</span>
                                                        <br><small class="text-muted">11:42</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>
                                                            Actif
                                                        </span>
                                                    </td>
                                                </tr>
                                                
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                                AS
                                                            </div>
                                                            <div>
                                                                <strong>Association Sport Plus</strong>
                                                                <br><small class="text-muted">sport-plus</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><span class="badge bg-secondary">Trial</span></td>
                                                    <td>Association</td>
                                                    <td>
                                                        <strong>1</strong>
                                                        <br><small class="text-muted">Limite trial</small>
                                                    </td>
                                                    <td>
                                                        <strong>45</strong>
                                                        <br><small class="text-muted">45/événement</small>
                                                    </td>
                                                    <td>
                                                        <strong>€0</strong>
                                                        <br><small class="text-muted">Essai gratuit</small>
                                                    </td>
                                                    <td>
                                                        <span>28/06/2025</span>
                                                        <br><small class="text-muted">16:30</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-warning">
                                                            <i class="fas fa-clock me-1" style="font-size: 0.5rem;"></i>
                                                            Trial
                                                        </span>
                                                    </td>
                                                </tr>
                                                
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                                TC
                                                            </div>
                                                            <div>
                                                                <strong>TechCorp Events</strong>
                                                                <br><small class="text-muted">techcorp</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><span class="badge bg-primary">Enterprise</span></td>
                                                    <td>Entreprise</td>
                                                    <td>
                                                        <strong>47</strong>
                                                        <br><small class="text-success">+8 ce mois</small>
                                                    </td>
                                                    <td>
                                                        <strong>2,847</strong>
                                                        <br><small class="text-muted">~60/événement</small>
                                                    </td>
                                                    <td>
                                                        <strong>€2,400</strong>
                                                        <br><small class="text-primary">199.99/mois</small>
                                                    </td>
                                                    <td>
                                                        <span>30/06/2025</span>
                                                        <br><small class="text-muted">08:45</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>
                                                            Actif
                                                        </span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <!-- Pagination -->
                                <div class="card-footer bg-transparent">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">Affichage de 1 à 5 sur 1,247 organisations</small>
                                        
                                        <nav>
                                            <ul class="pagination pagination-sm mb-0">
                                                <li class="page-item disabled">
                                                    <span class="page-link">Précédent</span>
                                                </li>
                                                <li class="page-item active">
                                                    <span class="page-link">1</span>
                                                </li>
                                                <li class="page-item">
                                                    <a class="page-link" href="#">2</a>
                                                </li>
                                                <li class="page-item">
                                                    <a class="page-link" href="#">3</a>
                                                </li>
                                                <li class="page-item">
                                                    <span class="page-link">...</span>
                                                </li>
                                                <li class="page-item">
                                                    <a class="page-link" href="#">249</a>
                                                </li>
                                                <li class="page-item">
                                                    <a class="page-link" href="#">Suivant</a>
                                                </li>
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Generation Modal -->
    <div class="modal fade" id="reportModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-file-chart me-2"></i>
                        Génération du Rapport
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <div class="spinner-border text-primary mb-3" role="status">
                            <span class="visually-hidden">Génération...</span>
                        </div>
                        <h6 id="reportModalTitle">Génération du rapport en cours...</h6>
                        <p id="reportModalDescription" class="text-muted">Collecte et analyse des données...</p>
                        
                        <div class="progress mt-3">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                 role="progressbar" style="width: 0%" id="reportProgress"></div>
                        </div>
                        
                        <div id="reportSteps" class="mt-3 text-start">
                            <!-- Steps will be added dynamically -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Date Range Modal -->
    <div class="modal fade" id="dateRangeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-calendar me-2"></i>
                        Période Personnalisée
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date de début</label>
                            <input type="date" class="form-control" id="customDateFrom">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date de fin</label>
                            <input type="date" class="form-control" id="customDateTo">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Intervalles de rapport</label>
                        <select class="form-select" id="reportInterval">
                            <option value="daily">Quotidien</option>
                            <option value="weekly" selected>Hebdomadaire</option>
                            <option value="monthly">Mensuel</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-gradient" onclick="applyCustomDateRange()">
                        <i class="fas fa-check me-2"></i>
                        Appliquer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    
    <script>
        // Initialize charts
        document.addEventListener('DOMContentLoaded', function() {
            initializeCharts();
            setDefaultDates();
        });

        function initializeCharts() {
            // Revenue Chart
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun'],
                    datasets: [{
                        label: 'Revenus Récurrents',
                        data: [42000, 45000, 48000, 52000, 59000, 67450],
                        borderColor: 'rgb(102, 126, 234)',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Nouveaux Revenus',
                        data: [8000, 12000, 15000, 18000, 22000, 25000],
                        borderColor: 'rgb(16, 185, 129)',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '€' + (value / 1000) + 'K';
                                }
                            }
                        }
                    }
                }
            });

            // Plan Distribution Chart
            const planCtx = document.getElementById('planDistributionChart').getContext('2d');
            new Chart(planCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Premium', 'Basic', 'Enterprise', 'Trial'],
                    datasets: [{
                        data: [445, 578, 89, 135],
                        backgroundColor: [
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(251, 191, 36, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(107, 114, 128, 0.8)'
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });
        }

        // Period selector functionality
        document.querySelectorAll('.period-selector').forEach(btn => {
            btn.addEventListener('click', function() {
                if (this.dataset.period === 'custom') {
                    new bootstrap.Modal(document.getElementById('dateRangeModal')).show();
                    return;
                }
                
                document.querySelectorAll('.period-selector').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                updateDataForPeriod(this.dataset.period);
            });
        });

        function updateDataForPeriod(period) {
            console.log('Updating data for period:', period);
            showToast(`Données mises à jour pour les ${period} derniers jours`, 'info');
        }

        function applyCustomDateRange() {
            const dateFrom = document.getElementById('customDateFrom').value;
            const dateTo = document.getElementById('customDateTo').value;
            
            if (!dateFrom || !dateTo) {
                showToast('Veuillez sélectionner les deux dates', 'warning');
                return;
            }
            
            if (new Date(dateFrom) > new Date(dateTo)) {
                showToast('La date de début doit être antérieure à la date de fin', 'warning');
                return;
            }
            
            // Update active period selector
            document.querySelectorAll('.period-selector').forEach(b => b.classList.remove('active'));
            document.querySelector('[data-period="custom"]').classList.add('active');
            
            bootstrap.Modal.getInstance(document.getElementById('dateRangeModal')).hide();
            showToast('Période personnalisée appliquée', 'success');
        }

        // Generate specific reports
        function generateReport(type) {
            const reportNames = {
                'organizations': 'Rapport des Organisations',
                'revenue': 'Rapport des Revenus',
                'usage': 'Rapport d\'Utilisation',
                'events': 'Rapport des Événements'
            };
            
            showReportModal(reportNames[type], type);
        }

        function showReportModal(title, type) {
            document.getElementById('reportModalTitle').textContent = `Génération: ${title}`;
            
            const modal = new bootstrap.Modal(document.getElementById('reportModal'));
            modal.show();
            
            const steps = [
                'Collecte des données...',
                'Analyse des métriques...',
                'Génération des graphiques...',
                'Compilation du rapport...',
                'Finalisation...'
            ];
            
            simulateReportGeneration(steps, type);
        }

        function simulateReportGeneration(steps, type) {
            const progressBar = document.getElementById('reportProgress');
            const stepsContainer = document.getElementById('reportSteps');
            
            stepsContainer.innerHTML = '';
            let currentStep = 0;
            
            function executeStep() {
                if (currentStep < steps.length) {
                    const stepDiv = document.createElement('div');
                    stepDiv.className = 'mb-2';
                    stepDiv.innerHTML = `
                        <div class="d-flex align-items-center">
                            <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                            <span>${steps[currentStep]}</span>
                        </div>
                    `;
                    stepsContainer.appendChild(stepDiv);
                    
                    const progress = ((currentStep + 1) / steps.length) * 100;
                    progressBar.style.width = progress + '%';
                    
                    setTimeout(() => {
                        stepDiv.innerHTML = `
                            <div class="d-flex align-items-center text-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <span>${steps[currentStep]}</span>
                            </div>
                        `;
                        
                        currentStep++;
                        
                        if (currentStep < steps.length) {
                            setTimeout(executeStep, 800);
                        } else {
                            setTimeout(() => {
                                bootstrap.Modal.getInstance(document.getElementById('reportModal')).hide();
                                downloadReport(type);
                            }, 1000);
                        }
                    }, Math.random() * 1500 + 1000);
                }
            }
            
            executeStep();
        }

        function downloadReport(type) {
            showToast(`Téléchargement du rapport ${type} démarré`, 'success');
            // Simulate file download
            setTimeout(() => {
                showToast('Rapport téléchargé avec succès', 'success');
            }, 2000);
        }

        // Export functions
        function exportReport(format) {
            showToast(`Export ${format.toUpperCase()} démarré...`, 'info');
            
            setTimeout(() => {
                showToast(`Rapport exporté en ${format.toUpperCase()} avec succès`, 'success');
            }, 3000);
        }

        function exportTableData(format) {
            showToast(`Export des données en ${format.toUpperCase()}...`, 'info');
            
            setTimeout(() => {
                showToast(`Données exportées en ${format.toUpperCase()}`, 'success');
            }, 2000);
        }

        function scheduleReport() {
            showToast('Fonctionnalité de programmation bientôt disponible', 'info');
        }

        // Filter functions
        function applyFilters() {
            const reportType = document.getElementById('reportType').value;
            const planFilter = document.getElementById('planFilter').value;
            const orgTypeFilter = document.getElementById('orgTypeFilter').value;
            
            showToast('Filtres appliqués, mise à jour des données...', 'info');
            
            // Simulate data update
            setTimeout(() => {
                showToast('Données filtrées mises à jour', 'success');
            }, 1500);
        }

        function updateReportView() {
            const reportType = document.getElementById('reportType').value;
            console.log('Switching to report type:', reportType);
            // In a real implementation, this would update the view
        }

        // Utility functions
        function setDefaultDates() {
            const today = new Date();
            const thirtyDaysAgo = new Date();
            thirtyDaysAgo.setDate(today.getDate() - 30);
            
            document.getElementById('dateFrom').value = thirtyDaysAgo.toISOString().split('T')[0];
            document.getElementById('dateTo').value = today.toISOString().split('T')[0];
            
            document.getElementById('customDateFrom').value = thirtyDaysAgo.toISOString().split('T')[0];
            document.getElementById('customDateTo').value = today.toISOString().split('T')[0];
        }

        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            toast.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 5000);
        }

        // Sidebar navigation
        document.querySelectorAll('.sidebar-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.sidebar-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Auto-refresh data every 5 minutes
        setInterval(() => {
            if (document.visibilityState === 'visible') {
                console.log('Auto-refreshing report data...');
                // In real implementation, this would refresh the data
            }
        }, 300000);
    </script>
</body>
</html>
                                    