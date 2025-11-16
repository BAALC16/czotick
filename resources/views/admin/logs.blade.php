.action-create { background-color: #dcfce7; color: #166534; }
        .action-update { background-color: #dbeafe; color: #1e40af; }
        .action-delete { background-color: #fecaca; color: #991b1b; }
        .action-login { background-color: #fef3c7; color: #92400e; }
        .action-error { background-color: #fee2e2; color: #991b1b; }
        
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
        
        .search-box {
            border-radius: 25px;
            border: 2px solid #e5e7eb;
            transition: all 0.3s;
        }
        
        .search-box:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .log-details {
            background: #f8fafc;
            border-left: 4px solid #667eea;
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
            display: none;
        }
        
        .pagination .page-link {
            border-radius: 8px;
            margin: 0 2px;
            border: none;
            color: #667eea;
        }
        
        .pagination .page-link:hover {
            background-color: #667eea;
            color: white;
        }
        
        .pagination .page-item.active .page-link {
            background-color: #667eea;
            border-color: #667eea;
        }
        
        .stats-summary {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .stat-item {
            text-align: center;
            padding: 10px;
        }
        
        .stat-number {
            font-size: 1.5rem;
            font-weight: bold;
            color: #667eea;
        }
        
        .export-dropdown {
            border-radius: 8px;
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
                        <a href="#logs" class="sidebar-link active">
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
                        <span class="navbar-brand">Logs Système</span>
                        
                        <div class="d-flex align-items-center">
                            <div class="dropdown me-3">
                                <button class="btn btn-outline-primary dropdown-toggle export-dropdown" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-download me-2"></i>
                                    Exporter
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-file-csv me-2"></i>CSV</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-file-excel me-2"></i>Excel</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-file-pdf me-2"></i>PDF</a></li>
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
                
                <!-- Logs Content -->
                <div class="container-fluid p-4">
                    <!-- Page Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="mb-0">Logs Système</h2>
                        <div class="d-flex align-items-center">
                            <button class="btn btn-gradient me-2" onclick="refreshLogs()">
                                <i class="fas fa-sync-alt me-2"></i>
                                Actualiser
                            </button>
                            <button class="btn btn-outline-danger" onclick="clearOldLogs()">
                                <i class="fas fa-trash me-2"></i>
                                Nettoyer
                            </button>
                        </div>
                    </div>
                    
                    <!-- Stats Summary -->
                    <div class="stats-summary">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="stat-item">
                                    <div class="stat-number">2,847</div>
                                    <div class="text-muted">Total Logs</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-item">
                                    <div class="stat-number text-success">2,698</div>
                                    <div class="text-muted">Succès</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-item">
                                    <div class="stat-number text-warning">124</div>
                                    <div class="text-muted">Avertissements</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-item">
                                    <div class="stat-number text-danger">25</div>
                                    <div class="text-muted">Erreurs</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Filters -->
                    <div class="card filter-card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-3">
                                <i class="fas fa-filter me-2"></i>
                                Filtres de Recherche
                            </h5>
                            
                            <form id="logFilters">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Organisation</label>
                                        <select class="form-select" name="organization">
                                            <option value="">Toutes les organisations</option>
                                            <option value="1">JCI Emeraude</option>
                                            <option value="2">JCI Nirvana</option>
                                            <option value="3">Rotary Club Abidjan</option>
                                            <option value="4">Lions Club Plateau</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <label class="form-label">Action</label>
                                        <select class="form-select" name="action">
                                            <option value="">Toutes les actions</option>
                                            <option value="organization_created">Création organisation</option>
                                            <option value="event_created">Création événement</option>
                                            <option value="user_login">Connexion utilisateur</option>
                                            <option value="payment_received">Paiement reçu</option>
                                            <option value="template_updated">Mise à jour template</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <label class="form-label">Date début</label>
                                        <input type="date" class="form-control" name="date_from">
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <label class="form-label">Date fin</label>
                                        <input type="date" class="form-control" name="date_to">
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="submit" class="btn btn-gradient w-100">
                                            <i class="fas fa-search me-2"></i>
                                            Filtrer
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <input type="text" class="form-control search-box" name="search" placeholder="Rechercher dans les logs...">
                                    </div>
                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-outline-secondary" onclick="resetFilters()">
                                            <i class="fas fa-times me-2"></i>
                                            Réinitialiser
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Logs Table -->
                    <div class="card log-table">
                        <div class="card-header bg-transparent">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-list me-2"></i>
                                Historique des Actions
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date/Heure</th>
                                            <th>Organisation</th>
                                            <th>Utilisateur</th>
                                            <th>Action</th>
                                            <th>Description</th>
                                            <th>IP</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="log-row" onclick="toggleLogDetails(this)">
                                            <td class="text-nowrap">
                                                <small class="text-muted">30/06/2025</small><br>
                                                <strong>14:23:45</strong>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                                        JE
                                                    </div>
                                                    <div>
                                                        <strong>JCI Emeraude</strong>
                                                        <br><small class="text-muted">jci-emeraude</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <strong>Président JCI</strong>
                                                <br><small class="text-muted">president@jci-emeraude.ci</small>
                                            </td>
                                            <td>
                                                <span class="action-badge action-create">
                                                    organization_created
                                                </span>
                                            </td>
                                            <td>Organisation JCI Emeraude créée avec succès</td>
                                            <td>
                                                <code>192.168.1.105</code>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation(); viewLogDetails(1)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="log-details">
                                            <td colspan="7">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h6>Détails de l'action</h6>
                                                        <p><strong>Action:</strong> organization_created</p>
                                                        <p><strong>Timestamp:</strong> 2025-06-30 14:23:45</p>
                                                        <p><strong>User Agent:</strong> Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6>Métadonnées</h6>
                                                        <pre class="bg-light p-2 rounded"><code>{
  "event_title": "Convention Locale EMERAUDE 2025",
  "template_used": "jci_template_v1.0",
  "database_created": "org_jci_emeraude"
}</code></pre>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        
                                        <tr class="log-row" onclick="toggleLogDetails(this)">
                                            <td class="text-nowrap">
                                                <small class="text-muted">30/06/2025</small><br>
                                                <strong>13:45:12</strong>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                                        RC
                                                    </div>
                                                    <div>
                                                        <strong>Rotary Club</strong>
                                                        <br><small class="text-muted">rotary-abidjan</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <strong>Marie Dupont</strong>
                                                <br><small class="text-muted">marie@rotary.org</small>
                                            </td>
                                            <td>
                                                <span class="action-badge action-update">
                                                    event_created
                                                </span>
                                            </td>
                                            <td>Nouvel événement: Réunion mensuelle créé</td>
                                            <td>
                                                <code>10.0.0.45</code>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation(); viewLogDetails(2)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="log-details">
                                            <td colspan="7">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h6>Détails de l'action</h6>
                                                        <p><strong>Action:</strong> event_created</p>
                                                        <p><strong>Timestamp:</strong> 2025-06-30 13:45:12</p>
                                                        <p><strong>User Agent:</strong> Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6>Métadonnées</h6>
                                                        <pre class="bg-light p-2 rounded"><code>{
  "event_name": "Réunion mensuelle",
  "event_date": "2025-07-15",
  "participants_limit": 50
}</code></pre>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        
                                        <tr class="log-row" onclick="toggleLogDetails(this)">
                                            <td class="text-nowrap">
                                                <small class="text-muted">30/06/2025</small><br>
                                                <strong>12:30:08</strong>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                                        SY
                                                    </div>
                                                    <div>
                                                        <strong>Système</strong>
                                                        <br><small class="text-muted">automatic</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <strong>Système</strong>
                                                <br><small class="text-muted">automated</small>
                                            </td>
                                            <td>
                                                <span class="action-badge action-login">
                                                    template_updated
                                                </span>
                                            </td>
                                            <td>Template JCI mis à jour vers version 1.1</td>
                                            <td>
                                                <code>127.0.0.1</code>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation(); viewLogDetails(3)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="log-details">
                                            <td colspan="7">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h6>Détails de l'action</h6>
                                                        <p><strong>Action:</strong> template_updated</p>
                                                        <p><strong>Timestamp:</strong> 2025-06-30 12:30:08</p>
                                                        <p><strong>User Agent:</strong> System/Automated</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6>Métadonnées</h6>
                                                        <pre class="bg-light p-2 rounded"><code>{
  "template_type": "jci",
  "old_version": "1.0",
  "new_version": "1.1",
  "changes": ["added_new_fields", "optimized_queries"]
}</code></pre>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        
                                        <tr class="log-row" onclick="toggleLogDetails(this)">
                                            <td class="text-nowrap">
                                                <small class="text-muted">30/06/2025</small><br>
                                                <strong>11:15:33</strong>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                                        LC
                                                    </div>
                                                    <div>
                                                        <strong>Lions Club</strong>
                                                        <br><small class="text-muted">lions-plateau</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <strong>Jean Martin</strong>
                                                <br><small class="text-muted">jean@lions.org</small>
                                            </td>
                                            <td>
                                                <span class="action-badge action-update">
                                                    payment_received
                                                </span>
                                            </td>
                                            <td>Paiement reçu pour abonnement Premium</td>
                                            <td>
                                                <code>192.168.1.78</code>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation(); viewLogDetails(4)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="log-details">
                                            <td colspan="7">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h6>Détails de l'action</h6>
                                                        <p><strong>Action:</strong> payment_received</p>
                                                        <p><strong>Timestamp:</strong> 2025-06-30 11:15:33</p>
                                                        <p><strong>User Agent:</strong> Mozilla/5.0 (iPhone; CPU iPhone OS 17_0)</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6>Métadonnées</h6>
                                                        <pre class="bg-light p-2 rounded"><code>{
  "amount": "79.99",
  "currency": "EUR",
  "plan": "premium",
  "payment_method": "stripe",
  "transaction_id": "pi_1234567890"
}</code></pre>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        
                                        <tr class="log-row" onclick="toggleLogDetails(this)">
                                            <td class="text-nowrap">
                                                <small class="text-muted">30/06/2025</small><br>
                                                <strong>09:42:15</strong>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                                        SY
                                                    </div>
                                                    <div>
                                                        <strong>Système</strong>
                                                        <br><small class="text-muted">error</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <strong>-</strong>
                                                <br><small class="text-muted">system</small>
                                            </td>
                                            <td>
                                                <span class="action-badge action-error">
                                                    database_error
                                                </span>
                                            </td>
                                            <td>Erreur de connexion base de données temporaire</td>
                                            <td>
                                                <code>-</code>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-danger" onclick="event.stopPropagation(); viewLogDetails(5)">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="log-details">
                                            <td colspan="7">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h6>Détails de l'erreur</h6>
                                                        <p><strong>Action:</strong> database_error</p>
                                                        <p><strong>Timestamp:</strong> 2025-06-30 09:42:15</p>
                                                        <p><strong>Niveau:</strong> ERROR</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6>Stack Trace</h6>
                                                        <pre class="bg-light p-2 rounded"><code>{
  "error": "Connection timeout",
  "database": "org_jci_emeraude",
  "query": "SELECT * FROM events",
  "duration": "30s"
}</code></pre>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">Affichage de 1 à 50 sur 2,847 entrées</small>
                                
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
                                            <a class="page-link" href="#">57</a>
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

    <!-- Log Details Modal -->
    <div class="modal fade" id="logModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Détails du Log</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="logModalContent">
                        <!-- Content will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Toggle log details
        function toggleLogDetails(row) {
            const detailsRow = row.nextElementSibling;
            if (detailsRow && detailsRow.classList.contains('log-details')) {
                if (detailsRow.style.display === 'none' || detailsRow.style.display === '') {
                    detailsRow.style.display = 'table-row';
                } else {
                    detailsRow.style.display = 'none';
                }
            }
        }

        // View log details in modal
        function viewLogDetails(logId) {
            const modal = new bootstrap.Modal(document.getElementById('logModal'));
            
            // Simulate loading log details
            document.getElementById('logModalContent').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Informations générales</h6>
                        <table class="table table-sm">
                            <tr><td><strong>ID:</strong></td><td>${logId}</td></tr>
                            <tr><td><strong>Date:</strong></td><td>30/06/2025 14:23:45</td></tr>
                            <tr><td><strong>Action:</strong></td><td>organization_created</td></tr>
                            <tr><td><strong>Utilisateur:</strong></td><td>president@jci-emeraude.ci</td></tr>
                            <tr><td><strong>IP:</strong></td><td>192.168.1.105</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Métadonnées complètes</h6>
                        <pre class="bg-light p-3 rounded"><code>{
  "organization_id": 2,
  "user_id": 2,
  "action": "organization_created",
  "description": "Organisation JCI Emeraude créée avec succès",
  "ip_address": "192.168.1.105",
  "user_agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36",
  "metadata": {
    "event_title": "Convention Locale EMERAUDE 2025",
    "template_used": "jci_template_v1.0",
    "database_created": "org_jci_emeraude",
    "initial_setup": true
  },
  "created_at": "2025-06-30T14:23:45Z"
}</code></pre>
                    </div>
                </div>
            `;
            
            modal.show();
        }

        // Refresh logs
        function refreshLogs() {
            // Show loading indicator
            const refreshBtn = document.querySelector('button[onclick="refreshLogs()"]');
            const originalContent = refreshBtn.innerHTML;
            refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Actualisation...';
            refreshBtn.disabled = true;
            
            // Simulate API call
            setTimeout(() => {
                refreshBtn.innerHTML = originalContent;
                refreshBtn.disabled = false;
                
                // Show success message
                showToast('Logs actualisés avec succès', 'success');
            }, 2000);
        }

        // Clear old logs
        function clearOldLogs() {
            if (confirm('Êtes-vous sûr de vouloir supprimer les anciens logs (plus de 90 jours) ?')) {
                // Show loading
                const clearBtn = document.querySelector('button[onclick="clearOldLogs()"]');
                const originalContent = clearBtn.innerHTML;
                clearBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Nettoyage...';
                clearBtn.disabled = true;
                
                // Simulate API call
                setTimeout(() => {
                    clearBtn.innerHTML = originalContent;
                    clearBtn.disabled = false;
                    
                    // Show success message
                    showToast('1,247 anciens logs supprimés', 'success');
                }, 3000);
            }
        }

        // Reset filters
        function resetFilters() {
            document.getElementById('logFilters').reset();
            // Trigger search
            document.getElementById('logFilters').dispatchEvent(new Event('submit'));
        }

        // Show toast notification
        function showToast(message, type = 'info') {
            // Create toast element
            const toast = document.createElement('div');
            toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            toast.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(toast);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 5000);
        }

        // Handle form submission
        document.getElementById('logFilters').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(this);
            const filters = Object.fromEntries(formData);
            
            console.log('Applied filters:', filters);
            
            // Here you would typically make an AJAX call to filter the logs
            showToast('Filtres appliqués', 'info');
        });

        // Search functionality
        document.querySelector('input[name="search"]').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('.log-row');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const detailsRow = row.nextElementSibling;
                
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                    if (detailsRow && detailsRow.classList.contains('log-details')) {
                        detailsRow.style.display = 'none'; // Hide details when searching
                    }
                } else {
                    row.style.display = 'none';
                    if (detailsRow && detailsRow.classList.contains('log-details')) {
                        detailsRow.style.display = 'none';
                    }
                }
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

        // Auto-refresh logs every 30 seconds
        setInterval(() => {
            // Only refresh if user is still on the page and not interacting
            if (document.visibilityState === 'visible') {
                console.log('Auto-refreshing logs...');
                // refreshLogs(); // Uncomment to enable auto-refresh
            }
        }, 30000);

        // Export functionality
        document.querySelectorAll('.dropdown-menu a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const format = this.textContent.trim().toLowerCase();
                
                // Show loading
                const exportBtn = document.querySelector('.export-dropdown');
                const originalContent = exportBtn.innerHTML;
                exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Export...';
                exportBtn.disabled = true;
                
                // Simulate export
                setTimeout(() => {
                    exportBtn.innerHTML = originalContent;
                    exportBtn.disabled = false;
                    
                    showToast(`Export ${format.toUpperCase()} généré avec succès`, 'success');
                }, 2000);
            });
        });

        // Initialize tooltips if needed
        document.addEventListener('DOMContentLoaded', function() {
            // Set current date as default for date inputs
            const today = new Date().toISOString().split('T')[0];
            document.querySelector('input[name="date_to"]').value = today;
            
            // Set date from to 7 days ago
            const weekAgo = new Date();
            weekAgo.setDate(weekAgo.getDate() - 7);
            document.querySelector('input[name="date_from"]').value = weekAgo.toISOString().split('T')[0];
        });
    </script>
</body>
</html>
                <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs Système - EventSaaS Admin</title>
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
        
        .filter-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: none;
        }
        
        .log-table {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .log-row {
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .log-row:hover {
            background-color: #f8fafc;
            transform: scale(1.01);
        }
        
        .action-badge {
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 12px;
        }
        
        .action-create { background-color: #dcfce7; color: #166534; }
        .action-update { background-color: #dbeafe; color: #1e40af; }