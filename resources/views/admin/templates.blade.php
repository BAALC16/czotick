<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Templates Base de Données - EventSaaS Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism.min.css" rel="stylesheet">
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
        
        .template-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: none;
            transition: transform 0.3s;
        }
        
        .template-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .org-type-badge {
            font-size: 0.75rem;
            padding: 4px 12px;
            border-radius: 15px;
            font-weight: 600;
        }
        
        .type-jci { background-color: #dbeafe; color: #1e40af; }
        .type-rotary { background-color: #fef3c7; color: #92400e; }
        .type-lions { background-color: #dcfce7; color: #166534; }
        .type-association { background-color: #fce7f3; color: #be185d; }
        .type-company { background-color: #e0e7ff; color: #3730a3; }
        .type-other { background-color: #f3f4f6; color: #374151; }
        
        .version-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2px 8px;
            border-radius: 8px;
            font-size: 0.7rem;
            font-weight: bold;
        }
        
        .status-active {
            color: #16a34a;
        }
        
        .status-inactive {
            color: #dc2626;
        }
        
        .code-editor {
            background: #1e1e1e;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
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
        
        .template-preview {
            max-height: 200px;
            overflow-y: auto;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 10px;
            font-family: 'Courier New', monospace;
            font-size: 0.75rem;
        }
        
        .create-template-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border: 2px dashed rgba(255,255,255,0.3);
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .create-template-card:hover {
            transform: translateY(-5px);
            border-color: rgba(255,255,255,0.6);
        }
        
        .stats-row {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
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
                        <a href="#templates" class="sidebar-link active">
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
                        <span class="navbar-brand">Templates Base de Données</span>
                        
                        <div class="d-flex align-items-center">
                            <button class="btn btn-gradient me-3" data-bs-toggle="modal" data-bs-target="#createTemplateModal">
                                <i class="fas fa-plus me-2"></i>
                                Nouveau Template
                            </button>
                            
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
                
                <!-- Templates Content -->
                <div class="container-fluid p-4">
                    <!-- Page Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="mb-0">Gestion des Templates</h2>
                        <div class="d-flex align-items-center">
                            <select class="form-select me-3" style="width: auto;" onchange="filterByType(this.value)">
                                <option value="">Tous les types</option>
                                <option value="jci">JCI</option>
                                <option value="rotary">Rotary</option>
                                <option value="lions">Lions</option>
                                <option value="association">Association</option>
                                <option value="company">Entreprise</option>
                                <option value="other">Autre</option>
                            </select>
                            
                            <button class="btn btn-outline-primary" onclick="validateAllTemplates()">
                                <i class="fas fa-check-double me-2"></i>
                                Valider Tous
                            </button>
                        </div>
                    </div>
                    
                    <!-- Stats Row -->
                    <div class="stats-row">
                        <div class="row text-center">
                            <div class="col-md-2">
                                <div class="fw-bold fs-4 text-primary">6</div>
                                <div class="text-muted small">Templates Total</div>
                            </div>
                            <div class="col-md-2">
                                <div class="fw-bold fs-4 text-success">6</div>
                                <div class="text-muted small">Actifs</div>
                            </div>
                            <div class="col-md-2">
                                <div class="fw-bold fs-4 text-warning">0</div>
                                <div class="text-muted small">Inactifs</div>
                            </div>
                            <div class="col-md-2">
                                <div class="fw-bold fs-4 text-info">v1.1</div>
                                <div class="text-muted small">Dernière Version</div>
                            </div>
                            <div class="col-md-2">
                                <div class="fw-bold fs-4 text-secondary">1,247</div>
                                <div class="text-muted small">Orgs Utilisant</div>
                            </div>
                            <div class="col-md-2">
                                <div class="fw-bold fs-4 text-primary">99.2%</div>
                                <div class="text-muted small">Taux de Succès</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Templates Grid -->
                    <div class="row">
                        <!-- Create New Template Card -->
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card template-card create-template-card" data-bs-toggle="modal" data-bs-target="#createTemplateModal">
                                <div class="text-center">
                                    <i class="fas fa-plus fa-3x mb-3"></i>
                                    <h5>Créer un Nouveau Template</h5>
                                    <p class="mb-0">Ajouter un template personnalisé pour un type d'organisation</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- JCI Template -->
                        <div class="col-lg-4 col-md-6 mb-4" data-type="jci">
                            <div class="card template-card">
                                <div class="card-header bg-transparent">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="org-type-badge type-jci">JCI</span>
                                        <div class="d-flex align-items-center">
                                            <span class="version-badge me-2">v1.0</span>
                                            <i class="fas fa-circle status-active" title="Actif"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">JCI Standard Template</h5>
                                    <p class="card-text text-muted">Template pour les organisations JCI avec gestion complète des événements, inscriptions et paiements.</p>
                                    
                                    <div class="template-preview">
-- Gestion des événements personnalisables
CREATE TABLE events (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    event_title VARCHAR(255) NOT NULL,
    event_description TEXT,
    event_slug VARCHAR(255) UNIQUE NOT NULL,
    event_date DATE NOT NULL,
    ...
                                    </div>
                                    
                                    <div class="mt-3">
                                        <small class="text-muted">
                                            <i class="fas fa-building me-1"></i>342 organisations utilisent ce template
                                        </small>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="btn-group w-100" role="group">
                                        <button class="btn btn-outline-primary btn-sm" onclick="viewTemplate(1)">
                                            <i class="fas fa-eye me-1"></i>Voir
                                        </button>
                                        <button class="btn btn-outline-secondary btn-sm" onclick="editTemplate(1)">
                                            <i class="fas fa-edit me-1"></i>Modifier
                                        </button>
                                        <button class="btn btn-outline-info btn-sm" onclick="duplicateTemplate(4)">
                                            <i class="fas fa-copy me-1"></i>Dupliquer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Company Template -->
                        <div class="col-lg-4 col-md-6 mb-4" data-type="company">
                            <div class="card template-card">
                                <div class="card-header bg-transparent">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="org-type-badge type-company">ENTREPRISE</span>
                                        <div class="d-flex align-items-center">
                                            <span class="version-badge me-2">v1.0</span>
                                            <i class="fas fa-circle status-active" title="Actif"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">Company Template</h5>
                                    <p class="card-text text-muted">Template pour entreprises avec gestion des employés, événements corporate et budgets.</p>
                                    
                                    <div class="template-preview">
-- Template Entreprise
CREATE TABLE employees (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    employee_number VARCHAR(50) UNIQUE,
    fullname VARCHAR(191) NOT NULL,
    department VARCHAR(100),
    ...
                                    </div>
                                    
                                    <div class="mt-3">
                                        <small class="text-muted">
                                            <i class="fas fa-building me-1"></i>234 organisations utilisent ce template
                                        </small>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="btn-group w-100" role="group">
                                        <button class="btn btn-outline-primary btn-sm" onclick="viewTemplate(5)">
                                            <i class="fas fa-eye me-1"></i>Voir
                                        </button>
                                        <button class="btn btn-outline-secondary btn-sm" onclick="editTemplate(5)">
                                            <i class="fas fa-edit me-1"></i>Modifier
                                        </button>
                                        <button class="btn btn-outline-info btn-sm" onclick="duplicateTemplate(5)">
                                            <i class="fas fa-copy me-1"></i>Dupliquer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Custom Template -->
                        <div class="col-lg-4 col-md-6 mb-4" data-type="other">
                            <div class="card template-card">
                                <div class="card-header bg-transparent">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="org-type-badge type-other">PERSONNALISÉ</span>
                                        <div class="d-flex align-items-center">
                                            <span class="version-badge me-2">v1.0</span>
                                            <i class="fas fa-circle status-active" title="Actif"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">Custom Template</h5>
                                    <p class="card-text text-muted">Template personnalisable pour organisations avec besoins spécifiques.</p>
                                    
                                    <div class="template-preview">
-- Template personnalisable
CREATE TABLE participants (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    reference_number VARCHAR(50) UNIQUE,
    fullname VARCHAR(191) NOT NULL,
    participant_type VARCHAR(100),
    ...
                                    </div>
                                    
                                    <div class="mt-3">
                                        <small class="text-muted">
                                            <i class="fas fa-building me-1"></i>156 organisations utilisent ce template
                                        </small>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="btn-group w-100" role="group">
                                        <button class="btn btn-outline-primary btn-sm" onclick="viewTemplate(6)">
                                            <i class="fas fa-eye me-1"></i>Voir
                                        </button>
                                        <button class="btn btn-outline-secondary btn-sm" onclick="editTemplate(6)">
                                            <i class="fas fa-edit me-1"></i>Modifier
                                        </button>
                                        <button class="btn btn-outline-info btn-sm" onclick="duplicateTemplate(6)">
                                            <i class="fas fa-copy me-1"></i>Dupliquer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Template Modal -->
    <div class="modal fade" id="createTemplateModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-2"></i>
                        Créer un Nouveau Template
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="createTemplateForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nom du Template *</label>
                                    <input type="text" class="form-control" name="template_name" required 
                                           placeholder="Ex: JCI Advanced Template">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Type d'Organisation *</label>
                                    <select class="form-select" name="org_type" required>
                                        <option value="">Sélectionner un type</option>
                                        <option value="jci">JCI</option>
                                        <option value="rotary">Rotary</option>
                                        <option value="lions">Lions</option>
                                        <option value="association">Association</option>
                                        <option value="company">Entreprise</option>
                                        <option value="other">Autre</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Version *</label>
                                    <input type="text" class="form-control" name="template_version" required 
                                           placeholder="Ex: 1.0" value="1.0">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" name="description" rows="3" 
                                              placeholder="Description du template et de ses fonctionnalités"></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_active" checked>
                                        <label class="form-check-label">
                                            Template actif
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Structure SQL *</label>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-muted">Saisissez votre code SQL</small>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-secondary" onclick="validateSQL()">
                                                <i class="fas fa-check me-1"></i>Valider
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" onclick="formatSQL()">
                                                <i class="fas fa-code me-1"></i>Formater
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" onclick="loadTemplate()">
                                                <i class="fas fa-folder-open me-1"></i>Charger
                                            </button>
                                        </div>
                                    </div>
                                    <textarea class="form-control code-editor" name="sql_structure" rows="12" required 
                                              placeholder="-- Exemple: CREATE TABLE example_table (&#10;--   id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,&#10;--   name VARCHAR(255) NOT NULL&#10;-- );"></textarea>
                                </div>
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Conseils:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>Utilisez des noms de tables explicites</li>
                                        <li>Incluez les contraintes de clés étrangères</li>
                                        <li>Pensez aux index pour les performances</li>
                                        <li>Documentez votre code avec des commentaires</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-gradient" onclick="createTemplate()">
                        <i class="fas fa-save me-2"></i>
                        Créer le Template
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Template Modal -->
    <div class="modal fade" id="viewTemplateModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-eye me-2"></i>
                        Détails du Template
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="templateViewContent">
                        <!-- Content will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="button" class="btn btn-outline-primary" onclick="editCurrentTemplate()">
                        <i class="fas fa-edit me-2"></i>
                        Modifier
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Template Modal -->
    <div class="modal fade" id="editTemplateModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>
                        Modifier le Template
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editTemplateForm">
                        <div id="editTemplateContent">
                            <!-- Content will be loaded here -->
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-gradient" onclick="updateTemplate()">
                        <i class="fas fa-save me-2"></i>
                        Sauvegarder
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-core.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-sql.min.js"></script>
    
    <script>
        let currentTemplateId = null;
        
        // Sample template data
        const templatesData = {
            1: {
                id: 1,
                name: "JCI Standard Template",
                type: "jci",
                version: "1.0",
                description: "Template pour les organisations JCI avec gestion complète des événements",
                sql: `-- ============================================
-- GESTION DES ÉVÉNEMENTS PERSONNALISABLES
-- ============================================

CREATE TABLE events (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    -- Informations de base
    event_title VARCHAR(255) NOT NULL,
    event_description TEXT,
    event_slug VARCHAR(255) UNIQUE NOT NULL,
    
    -- Dates et lieu
    event_date DATE NOT NULL,
    event_start_time TIME,
    event_end_time TIME,
    event_location VARCHAR(500),
    
    -- Personnalisation visuelle
    primary_color VARCHAR(7) DEFAULT "#1a73e8",
    secondary_color VARCHAR(7) DEFAULT "#34a853",
    
    -- Configuration
    max_participants INT,
    is_published BOOLEAN DEFAULT FALSE,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;`,
                organizations_count: 342,
                is_active: true
            },
            2: {
                id: 2,
                name: "Rotary Club Template",
                type: "rotary", 
                version: "1.0",
                description: "Template spécialisé pour les clubs Rotary",
                sql: `-- Base Rotary Template
CREATE TABLE members (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    member_number VARCHAR(50) UNIQUE,
    fullname VARCHAR(191) NOT NULL,
    phone VARCHAR(191) NOT NULL,
    email VARCHAR(191) NOT NULL,
    profession VARCHAR(191),
    club_position ENUM("member", "secretary", "treasurer", "president") DEFAULT "member",
    membership_date DATE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;`,
                organizations_count: 278,
                is_active: true
            }
        };

        // Filter templates by type
        function filterByType(type) {
            const cards = document.querySelectorAll('[data-type]');
            cards.forEach(card => {
                if (type === '' || card.dataset.type === type) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // View template details
        function viewTemplate(id) {
            const template = templatesData[id];
            if (!template) return;
            
            currentTemplateId = id;
            
            const content = `
                <div class="row">
                    <div class="col-md-4">
                        <h6>Informations du Template</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Nom:</strong></td><td>${template.name}</td></tr>
                            <tr><td><strong>Type:</strong></td><td><span class="org-type-badge type-${template.type}">${template.type.toUpperCase()}</span></td></tr>
                            <tr><td><strong>Version:</strong></td><td><span class="version-badge">${template.version}</span></td></tr>
                            <tr><td><strong>Status:</strong></td><td><span class="text-success">Actif</span></td></tr>
                            <tr><td><strong>Organisations:</strong></td><td>${template.organizations_count}</td></tr>
                        </table>
                        
                        <h6 class="mt-4">Description</h6>
                        <p class="text-muted">${template.description}</p>
                        
                        <h6>Statistiques d'utilisation</h6>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-success" style="width: 95%">95% succès</div>
                        </div>
                        <small class="text-muted">Taux de succès de déploiement</small>
                    </div>
                    
                    <div class="col-md-8">
                        <h6>Structure SQL</h6>
                        <div class="bg-dark text-light p-3 rounded" style="max-height: 400px; overflow-y: auto;">
                            <pre><code class="language-sql">${template.sql}</code></pre>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('templateViewContent').innerHTML = content;
            new bootstrap.Modal(document.getElementById('viewTemplateModal')).show();
        }

        // Edit template
        function editTemplate(id) {
            const template = templatesData[id];
            if (!template) return;
            
            currentTemplateId = id;
            
            const content = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Nom du Template *</label>
                            <input type="text" class="form-control" name="template_name" value="${template.name}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Version *</label>
                            <input type="text" class="form-control" name="template_version" value="${template.version}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3">${template.description}</textarea>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" ${template.is_active ? 'checked' : ''}>
                                <label class="form-check-label">Template actif</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Structure SQL *</label>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">Modifiez votre code SQL</small>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-secondary" onclick="validateSQL()">
                                        <i class="fas fa-check me-1"></i>Valider
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="formatSQL()">
                                        <i class="fas fa-code me-1"></i>Formater
                                    </button>
                                </div>
                            </div>
                            <textarea class="form-control code-editor" name="sql_structure" rows="15" required>${template.sql}</textarea>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('editTemplateContent').innerHTML = content;
            new bootstrap.Modal(document.getElementById('editTemplateModal')).show();
        }

        // Duplicate template
        function duplicateTemplate(id) {
            const template = templatesData[id];
            if (!template) return;
            
            // Pre-fill create form with duplicated data
            document.querySelector('#createTemplateForm [name="template_name"]').value = template.name + ' (Copie)';
            document.querySelector('#createTemplateForm [name="org_type"]').value = template.type;
            document.querySelector('#createTemplateForm [name="template_version"]').value = '1.0';
            document.querySelector('#createTemplateForm [name="sql_structure"]').value = template.sql;
            
            new bootstrap.Modal(document.getElementById('createTemplateModal')).show();
        }

        // Create new template
        function createTemplate() {
            const form = document.getElementById('createTemplateForm');
            const formData = new FormData(form);
            
            // Validate form
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            // Show loading
            const btn = event.target;
            const originalContent = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Création...';
            btn.disabled = true;
            
            // Simulate API call
            setTimeout(() => {
                btn.innerHTML = originalContent;
                btn.disabled = false;
                
                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('createTemplateModal')).hide();
                
                // Show success message
                showToast('Template créé avec succès!', 'success');
                
                // Reset form
                form.reset();
            }, 2000);
        }

        // Update template
        function updateTemplate() {
            const form = document.getElementById('editTemplateForm');
            
            // Show loading
            const btn = event.target;
            const originalContent = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sauvegarde...';
            btn.disabled = true;
            
            // Simulate API call
            setTimeout(() => {
                btn.innerHTML = originalContent;
                btn.disabled = false;
                
                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('editTemplateModal')).hide();
                
                // Show success message
                showToast('Template mis à jour avec succès!', 'success');
            }, 2000);
        }

        // Edit current template from view modal
        function editCurrentTemplate() {
            bootstrap.Modal.getInstance(document.getElementById('viewTemplateModal')).hide();
            setTimeout(() => editTemplate(currentTemplateId), 300);
        }

        // Validate SQL
        function validateSQL() {
            showToast('SQL validé avec succès!', 'success');
        }

        // Format SQL
        function formatSQL() {
            showToast('SQL formaté!', 'info');
        }

        // Load template from file
        function loadTemplate() {
            // Create file input
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = '.sql,.txt';
            
            input.onchange = function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.querySelector('#createTemplateForm [name="sql_structure"]').value = e.target.result;
                        showToast('Template chargé depuis le fichier!', 'success');
                    };
                    reader.readAsText(file);
                }
            };
            
            input.click();
        }

        // Validate all templates
        function validateAllTemplates() {
            const btn = event.target;
            const originalContent = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Validation...';
            btn.disabled = true;
            
            // Simulate validation
            setTimeout(() => {
                btn.innerHTML = originalContent;
                btn.disabled = false;
                
                showToast('Tous les templates validés avec succès!', 'success');
            }, 3000);
        }

        // Show toast notification
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

        // Initialize syntax highlighting
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Prism !== 'undefined') {
                Prism.highlightAll();
            }
        });
    </script>
</body>
</html>="duplicateTemplate(1)">
                                            <i class="fas fa-copy me-1"></i>Dupliquer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Rotary Template -->
                        <div class="col-lg-4 col-md-6 mb-4" data-type="rotary">
                            <div class="card template-card">
                                <div class="card-header bg-transparent">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="org-type-badge type-rotary">ROTARY</span>
                                        <div class="d-flex align-items-center">
                                            <span class="version-badge me-2">v1.0</span>
                                            <i class="fas fa-circle status-active" title="Actif"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">Rotary Club Template</h5>
                                    <p class="card-text text-muted">Template spécialisé pour les clubs Rotary avec gestion des membres, réunions et projets de service.</p>
                                    
                                    <div class="template-preview">
-- Base Rotary Template
CREATE TABLE members (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    member_number VARCHAR(50) UNIQUE,
    fullname VARCHAR(191) NOT NULL,
    phone VARCHAR(191) NOT NULL,
    ...
                                    </div>
                                    
                                    <div class="mt-3">
                                        <small class="text-muted">
                                            <i class="fas fa-building me-1"></i>278 organisations utilisent ce template
                                        </small>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="btn-group w-100" role="group">
                                        <button class="btn btn-outline-primary btn-sm" onclick="viewTemplate(2)">
                                            <i class="fas fa-eye me-1"></i>Voir
                                        </button>
                                        <button class="btn btn-outline-secondary btn-sm" onclick="editTemplate(2)">
                                            <i class="fas fa-edit me-1"></i>Modifier
                                        </button>
                                        <button class="btn btn-outline-info btn-sm" onclick="duplicateTemplate(2)">
                                            <i class="fas fa-copy me-1"></i>Dupliquer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Lions Template -->
                        <div class="col-lg-4 col-md-6 mb-4" data-type="lions">
                            <div class="card template-card">
                                <div class="card-header bg-transparent">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="org-type-badge type-lions">LIONS</span>
                                        <div class="d-flex align-items-center">
                                            <span class="version-badge me-2">v1.0</span>
                                            <i class="fas fa-circle status-active" title="Actif"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">Lions Club Template</h5>
                                    <p class="card-text text-muted">Template pour les clubs Lions avec focus sur les projets de service et la gestion des dons.</p>
                                    
                                    <div class="template-preview">
-- Base Lions Template
CREATE TABLE members (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    member_number VARCHAR(50) UNIQUE,
    fullname VARCHAR(191) NOT NULL,
    sponsor_id BIGINT UNSIGNED,
    ...
                                    </div>
                                    
                                    <div class="mt-3">
                                        <small class="text-muted">
                                            <i class="fas fa-building me-1"></i>189 organisations utilisent ce template
                                        </small>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="btn-group w-100" role="group">
                                        <button class="btn btn-outline-primary btn-sm" onclick="viewTemplate(3)">
                                            <i class="fas fa-eye me-1"></i>Voir
                                        </button>
                                        <button class="btn btn-outline-secondary btn-sm" onclick="editTemplate(3)">
                                            <i class="fas fa-edit me-1"></i>Modifier
                                        </button>
                                        <button class="btn btn-outline-info btn-sm" onclick="duplicateTemplate(3)">
                                            <i class="fas fa-copy me-1"></i>Dupliquer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Association Template -->
                        <div class="col-lg-4 col-md-6 mb-4" data-type="association">
                            <div class="card template-card">
                                <div class="card-header bg-transparent">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="org-type-badge type-association">ASSOCIATION</span>
                                        <div class="d-flex align-items-center">
                                            <span class="version-badge me-2">v1.0</span>
                                            <i class="fas fa-circle status-active" title="Actif"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">Association Template</h5>
                                    <p class="card-text text-muted">Template générique pour associations avec gestion des membres, événements et finances.</p>
                                    
                                    <div class="template-preview">
-- Template Association générique
CREATE TABLE members (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    membership_number VARCHAR(50) UNIQUE,
    fullname VARCHAR(191) NOT NULL,
    membership_type ENUM('regular', 'student', 'senior', 'honorary'),
    ...
                                    </div>
                                    
                                    <div class="mt-3">
                                        <small class="text-muted">
                                            <i class="fas fa-building me-1"></i>445 organisations utilisent ce template
                                        </small>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="btn-group w-100" role="group">
                                        <button class="btn btn-outline-primary btn-sm" onclick="viewTemplate(4)">
                                            <i class="fas fa-eye me-1"></i>Voir
                                        </button>
                                        <button class="btn btn-outline-secondary btn-sm" onclick="editTemplate(4)">
                                            <i class="fas fa-edit me-1"></i>Modifier
                                        </button>
                                        <button class="btn btn-outline-info btn-sm" onclick