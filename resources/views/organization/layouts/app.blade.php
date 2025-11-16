<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ $organization->org_name ?? 'Organisation' }}</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @stack('styles')

    <style>
        :root {
            --primary-color: #1113a5;
            --secondary-color: #f99b4f;
            --sidebar-width: 260px;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            direction: ltr;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: var(--primary-color);
            color: white;
            overflow-y: auto;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: white;
        }

        .sidebar-header .logo img {
            max-width: 100%;
            max-height: 60px;
            object-fit: contain;
            margin-bottom: 0.75rem;
        }

        .sidebar-header .org-name {
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.8);
            margin-top: 0.5rem;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu-item {
            margin: 0;
        }

        .sidebar-menu-link {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        .sidebar-menu-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .sidebar-menu-link.active {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            border-left-color: var(--secondary-color);
        }

        .sidebar-menu-link i {
            width: 20px;
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            background: #f5f7fa;
            display: flex;
            flex-direction: column;
        }

        .main-header {
            background: white;
            padding: 1rem 2rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 100;
            width: 100%;
            flex-shrink: 0;
            order: -1;
            margin-bottom: 0;
        }

        .main-header .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .main-header .user-info {
            text-align: right;
        }

        .main-header .user-name {
            font-weight: 600;
            color: #1f2937;
        }

        .main-header .user-role {
            font-size: 0.875rem;
            color: #6b7280;
        }

        .main-container {
            padding: 2rem;
            flex: 1;
            width: 100%;
            box-sizing: border-box;
            overflow-y: auto;
            order: 1;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .main-container {
                width: 100%;
            }

            .mobile-menu-toggle {
                display: block;
            }
        }

        .mobile-menu-toggle {
            display: none;
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.75rem;
            border-radius: 0.375rem;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            @if(isset($organization) && $organization->organization_logo && $organization->organization_logo !== 'default-logo.png')
                <div class="logo">
                    <img src="{{ url('public/' . $organization->organization_logo) }}" alt="{{ $organization->org_name ?? 'Logo' }}">
                </div>
            @endif
            <div class="org-name">
                {{ $organization->org_name ?? 'Organisation' }}
            </div>
        </div>

        <nav>
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a href="{{ route('org.dashboard', ['org_slug' => $orgSlug]) }}"
                       class="sidebar-menu-link {{ request()->routeIs('org.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('org.events.index', ['org_slug' => $orgSlug]) }}"
                       class="sidebar-menu-link {{ request()->routeIs('org.events.*') && !request()->routeIs('org.events.create') ? 'active' : '' }}">
                        <i class="fas fa-list"></i>
                        <span>Liste des événements</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('org.events.create', ['org_slug' => $orgSlug]) }}"
                       class="sidebar-menu-link {{ request()->routeIs('org.events.create') ? 'active' : '' }}">
                        <i class="fas fa-plus-circle"></i>
                        <span>Créer un événement</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('org.accounts.index', ['org_slug' => $orgSlug]) }}"
                       class="sidebar-menu-link {{ request()->routeIs('org.accounts.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        <span>Comptes</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('org.profile', ['org_slug' => $orgSlug]) }}"
                       class="sidebar-menu-link {{ request()->routeIs('org.profile') ? 'active' : '' }}">
                        <i class="fas fa-user-circle"></i>
                        <span>Mon profil</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <form method="POST" action="{{ route('org.logout', ['org_slug' => $orgSlug]) }}" class="inline">
                        @csrf
                        <button type="submit" class="sidebar-menu-link w-full text-left" style="border: none; background: none; cursor: pointer;">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Déconnexion</span>
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <header class="main-header">
            <div>
                <button class="mobile-menu-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <div class="user-menu">
                <div class="user-info">
                    <div class="user-name">{{ $user['full_name'] ?? $user['email'] ?? 'Utilisateur' }}</div>
                    <div class="user-role">{{ ucfirst($user['role'] ?? 'user') }}</div>
                </div>
                <div class="user-avatar" style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary-color); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                    {{ strtoupper(substr($user['full_name'] ?? $user['email'] ?? 'U', 0, 1)) }}
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="main-container">
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-400 text-green-700 p-4 rounded-r-lg mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-400 text-red-700 p-4 rounded-r-lg mb-4">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle mr-2 mt-0.5"></i>
                        <div>
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('open');
        }
    </script>
</body>
</html>

