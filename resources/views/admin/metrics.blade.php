<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Métriques - EventSaaS Admin</title>
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
        
        .metric-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            border: none;
        }
        
        .metric-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .period-selector {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            border-radius: 25px;
            padding: 8px 20px;
            margin: 0 5px;
            transition: all 0.3s;
        }
        
        .period-selector.active {
            background: white;
            color: #667eea;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .kpi-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-radius: 15px;
            border: none;
            transition: transform 0.3s;
        }
        
        .kpi-card.users {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .kpi-card.events {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            color: #333;
        }
        
        .kpi-card.revenue {
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            color: #333;
        }
        
        .kpi-card.retention {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .kpi-card:hover {
            transform: translateY(-5px);
        }
        
        .performance-indicator {
            height: 8px;
            border-radius: 4px;
            background: rgba(255,255,255,0.3);
            overflow: hidden;
        }
        
        .performance-bar {
            height: 100%;
            background: linear-gradient(90deg, #4ade80, #22c55e);
            border-radius: 4px;
            transition: width 0.5s ease;
        }
        
        .top-org-item {
            border-left: 4px solid #667eea;
            background: #f8fafc;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            transition: all 0.3s;
        }
        
        .top-org-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .retention-chart {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 15px;
            padding: 20px;
        }
        
        .navbar-brand {
            font-weight: bold;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
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
                        <a href="#metrics" class="sidebar-link active">
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
                        <a href="#reports" class="sidebar-link">
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
                        <span class="navbar-brand">Métriques & Analytics</span>
                        
                        <div class="d-flex align-items-center">
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
                
                <!-- Metrics Content -->
                <div class="container-fluid p-4">
                    <!-- Page Header with Period Selector -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="mb-0">Métriques & Analytics</h2>
                        <div class="d-flex align-items-center">
                            <span class="me-3 text-muted">Période:</span>
                            <button class="btn period-selector active" data-period="7">7 jours</button>
                            <button class="btn period-selector" data-period="30">30 jours</button>
                            <button class="btn period-selector" data-period="90">90 jours</button>
                            <button class="btn period-selector" data-period="365">1 an</button>
                        </div>
                    </div>
                    
                    <!-- KPI Cards -->
                    <div class="row mb-4">
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="card kpi-card users">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="card-title opacity-75 mb-2">Métriques Utilisateurs</h6>
                                            <h3 class="mb-1">2,847</h3>
                                            <small class="opacity-75">Utilisateurs actifs</small>
                                            <div class="performance-indicator mt-2">
                                                <div class="performance-bar" style="width: 78%"></div>
                                            </div>
                                            <small class="text-success mt-1 d-block">
                                                <i class="fas fa-arrow-up"></i> +23% vs période précédente
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <i class="fas fa-users fa-2x opacity-50"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="card kpi-card events">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="card-title mb-2">Métriques Événements</h6>
                                            <h3 class="mb-1">1,293</h3>
                                            <small>Événements créés</small>
                                            <div class="performance-indicator mt-2">
                                                <div class="performance-bar" style="width: 65%"></div>
                                            </div>
                                            <small class="text-success mt-1 d-block">
                                                <i class="fas fa-arrow-up"></i> +18% vs période précédente
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <i class="fas fa-calendar-alt fa-2x opacity-50"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="card kpi-card revenue">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="card-title mb-2">Métriques Financières</h6>
                                            <h3 class="mb-1">€67,450</h3>
                                            <small>Revenus totaux</small>
                                            <div class="performance-indicator mt-2">
                                                <div class="performance-bar" style="width: 85%"></div>
                                            </div>
                                            <small class="text-success mt-1 d-block">
                                                <i class="fas fa-arrow-up"></i> +31% vs période précédente
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <i class="fas fa-euro-sign fa-2x opacity-50"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="card kpi-card retention">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="card-title opacity-75 mb-2">Rétention</h6>
                                            <h3 class="mb-1">89.2%</h3>
                                            <small class="opacity-75">Taux de rétention</small>
                                            <div class="performance-indicator mt-2">
                                                <div class="performance-bar" style="width: 89%"></div>
                                            </div>
                                            <small class="text-success mt-1 d-block">
                                                <i class="fas fa-arrow-up"></i> +5% vs période précédente
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <i class="fas fa-chart-line fa-2x opacity-50"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Charts Row 1 -->
                    <div class="row mb-4">
                        <div class="col-lg-8">
                            <div class="card metric-card">
                                <div class="card-header bg-transparent">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-chart-area me-2"></i>
                                        Performance Système
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="systemMetricsChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <div class="card metric-card">
                                <div class="card-header bg-transparent">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-trophy me-2"></i>
                                        Top Organisations
                                    </h5>
                                </div>
                                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                    <div class="top-org-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">JCI Emeraude</h6>
                                                <small class="text-muted">245 événements</small>
                                            </div>
                                            <div class="text-end">
                                                <div class="badge bg-success">€12,340</div>
                                                <div class="small text-muted">revenus</div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="top-org-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">Rotary Club International</h6>
                                                <small class="text-muted">189 événements</small>
                                            </div>
                                            <div class="text-end">
                                                <div class="badge bg-success">€9,870</div>
                                                <div class="small text-muted">revenus</div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="top-org-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">Lions Club Plateau</h6>
                                                <small class="text-muted">156 événements</small>
                                            </div>
                                            <div class="text-end">
                                                <div class="badge bg-success">€7,650</div>
                                                <div class="small text-muted">revenus</div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="top-org-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">Association Tech</h6>
                                                <small class="text-muted">134 événements</small>
                                            </div>
                                            <div class="text-end">
                                                <div class="badge bg-warning">€6,220</div>
                                                <div class="small text-muted">revenus</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Charts Row 2 -->
                    <div class="row mb-4">
                        <div class="col-lg-6">
                            <div class="card metric-card">
                                <div class="card-header bg-transparent">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-chart-pie me-2"></i>
                                        Répartition par Type d'Organisation
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="orgTypeChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="card metric-card">
                                <div class="card-header bg-transparent">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-chart-bar me-2"></i>
                                        Conversion par Canal
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="conversionChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Retention Analysis -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card metric-card">
                                <div class="card-header bg-transparent">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-users me-2"></i>
                                        Analyse de Rétention Utilisateurs
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="retention-chart">
                                        <canvas id="retentionChart" height="200"></canvas>
                                    </div>
                                    
                                    <!-- Retention Cohort Table -->
                                    <div class="mt-4">
                                        <h6 class="mb-3">Cohortes de Rétention</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Cohorte</th>
                                                        <th>Utilisateurs</th>
                                                        <th>Mois 1</th>
                                                        <th>Mois 2</th>
                                                        <th>Mois 3</th>
                                                        <th>Mois 6</th>
                                                        <th>Mois 12</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>Jan 2024</td>
                                                        <td>245</td>
                                                        <td><span class="badge bg-success">92%</span></td>
                                                        <td><span class="badge bg-success">85%</span></td>
                                                        <td><span class="badge bg-warning">78%</span></td>
                                                        <td><span class="badge bg-warning">71%</span></td>
                                                        <td><span class="badge bg-danger">65%</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Fév 2024</td>
                                                        <td>189</td>
                                                        <td><span class="badge bg-success">95%</span></td>
                                                        <td><span class="badge bg-success">87%</span></td>
                                                        <td><span class="badge bg-warning">79%</span></td>
                                                        <td><span class="badge bg-warning">73%</span></td>
                                                        <td>-</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Mar 2024</td>
                                                        <td>298</td>
                                                        <td><span class="badge bg-success">94%</span></td>
                                                        <td><span class="badge bg-success">89%</span></td>
                                                        <td><span class="badge bg-warning">82%</span></td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Avr 2024</td>
                                                        <td>342</td>
                                                        <td><span class="badge bg-success">96%</span></td>
                                                        <td><span class="badge bg-success">91%</span></td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    
    <script>
        // System Metrics Chart
        const systemCtx = document.getElementById('systemMetricsChart').getContext('2d');
        new Chart(systemCtx, {
            type: 'line',
            data: {
                labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', '24:00'],
                datasets: [{
                    label: 'CPU Usage (%)',
                    data: [45, 38, 42, 65, 78, 85, 52],
                    borderColor: 'rgb(102, 126, 234)',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Memory Usage (%)',
                    data: [62, 58, 65, 72, 68, 74, 66],
                    borderColor: 'rgb(245, 87, 108)',
                    backgroundColor: 'rgba(245, 87, 108, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Active Users',
                    data: [1200, 890, 1450, 2300, 2100, 1800, 1350],
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        max: 100,
                        title: {
                            display: true,
                            text: 'Pourcentage (%)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Utilisateurs'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                }
            }
        });

        // Organization Type Chart
        const orgTypeCtx = document.getElementById('orgTypeChart').getContext('2d');
        new Chart(orgTypeCtx, {
            type: 'doughnut',
            data: {
                labels: ['JCI', 'Rotary', 'Lions', 'Association', 'Entreprise', 'Autre'],
                datasets: [{
                    data: [342, 278, 189, 445, 234, 156],
                    backgroundColor: [
                        'rgba(102, 126, 234, 0.8)',
                        'rgba(245, 87, 108, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(251, 191, 36, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
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

        // Conversion Chart
        const conversionCtx = document.getElementById('conversionChart').getContext('2d');
        new Chart(conversionCtx, {
            type: 'bar',
            data: {
                labels: ['Site Web', 'Référencement', 'Social Media', 'Email', 'Direct', 'Partenaires'],
                datasets: [{
                    label: 'Visiteurs',
                    data: [1200, 850, 650, 400, 300, 250],
                    backgroundColor: 'rgba(107, 114, 128, 0.3)',
                    borderColor: 'rgba(107, 114, 128, 0.8)',
                    borderWidth: 1
                }, {
                    label: 'Conversions',
                    data: [245, 145, 89, 67, 78, 45],
                    backgroundColor: 'rgba(102, 126, 234, 0.8)',
                    borderColor: 'rgba(102, 126, 234, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    }
                }
            }
        });

        // Retention Chart
        const retentionCtx = document.getElementById('retentionChart').getContext('2d');
        new Chart(retentionCtx, {
            type: 'line',
            data: {
                labels: ['Mois 0', 'Mois 1', 'Mois 2', 'Mois 3', 'Mois 6', 'Mois 12'],
                datasets: [{
                    label: 'Rétention (%)',
                    data: [100, 94, 87, 79, 72, 66],
                    borderColor: 'rgb(102, 126, 234)',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: 'rgb(102, 126, 234)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 50,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Taux de rétention (%)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Period selector functionality
        document.querySelectorAll('.period-selector').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.period-selector').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                // Here you would typically make an AJAX call to update the data
                console.log('Period changed to:', this.dataset.period, 'days');
            });
        });

        // Sidebar navigation
        document.querySelectorAll('.sidebar-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.sidebar-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>
</html>