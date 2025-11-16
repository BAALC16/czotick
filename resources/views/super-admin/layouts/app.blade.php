<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Super Admin') - Czotick Platform</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chart.js/3.9.1/chart.min.js"></script>
    
    <style>
        :root {
            --primary-color: #2ea551;
            --secondary-color: #427cbc;
            --sidebar-bg: #1e293b;
            --sidebar-hover: #334155;
        }
        
        .sidebar {
            background: var(--sidebar-bg);
            min-height: 100vh;
            width: 250px;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
        }
        
        .sidebar .nav-link {
            color: #cbd5e1;
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            margin: 0.25rem 1rem;
            transition: all 0.2s;
        }
        
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: var(--sidebar-hover);
            color: white;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 2rem;
        }
        
        .stat-card {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .status-active { background: #dcfce7; color: #166534; }
        .status-suspended { background: #fef2f2; color: #dc2626; }
        .status-trial { background: #fef3c7; color: #d97706; }
        
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0; }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="p-3">
            <h4 class="text-white mb-4">
                <i class="fas fa-crown me-2"></i>
                Super Admin
            </h4>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}" 
                   href="{{ route('super-admin.dashboard') }}">
                    <i class="fas fa-chart-line me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('super-admin.organizations.*') ? 'active' : '' }}" 
                   href="{{ route('super-admin.organizations.index') }}">
                    <i class="fas fa-building me-2"></i> Organisations
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('super-admin.users.*') ? 'active' : '' }}" 
                   href="{{ route('super-admin.users.index') }}">
                    <i class="fas fa-users me-2"></i> Utilisateurs
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('super-admin.metrics') ? 'active' : '' }}" 
                   href="{{ route('super-admin.metrics') }}">
                    <i class="fas fa-chart-bar me-2"></i> Métriques
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('super-admin.logs') ? 'active' : '' }}" 
                   href="{{ route('super-admin.logs') }}">
                    <i class="fas fa-list-alt me-2"></i> Logs
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('super-admin.health') ? 'active' : '' }}" 
                   href="{{ route('super-admin.health') }}">
                    <i class="fas fa-heartbeat me-2"></i> Santé Système
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('super-admin.settings') ? 'active' : '' }}" 
                   href="{{ route('super-admin.settings') }}">
                    <i class="fas fa-cog me-2"></i> Paramètres
                </a>
            </li>
            <li class="nav-item mt-3">
                <form action="{{ route('super-admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start">
                        <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                    </button>
                </form>
            </li>
        </ul>
    </nav>
    
    <!-- Main Content -->
    <main class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @yield('content')
    </main>
    
    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>