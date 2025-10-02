<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'UrbanGreen')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        :root {
            --primary-green: #2d5a3d;
            --secondary-green: #3a7f5f;
            --accent-green: #4caf50;
            --light-green: #e8f5e8;
            --forest-green: #1a4a2e;
            --mint-green: #a8e6a3;
            --sage-green: #87a96b;
            --accent-orange: #ff8c42;
            --warm-orange: #ffa726;
            --earth-brown: #8d6e63;
            --sky-blue: #87ceeb;
            --cream: #faf8f5;
            --soft-shadow: rgba(45, 90, 61, 0.1);
            --gradient-primary: linear-gradient(135deg, #2d5a3d 0%, #3a7f5f 50%, #4caf50 100%);
            --gradient-secondary: linear-gradient(135deg, #e8f5e8 0%, #a8e6a3 50%, rgba(76, 175, 80, 0.1) 100%);
            --gradient-background: linear-gradient(135deg, #faf8f5 0%, #e8f5e8 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--gradient-background);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* Nature-inspired background elements */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                radial-gradient(circle at 20% 80%, rgba(168, 230, 163, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(76, 175, 80, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(135, 169, 107, 0.05) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
        }

        /* Floating elements */
        .floating-leaves {
            position: fixed;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .leaf {
            position: absolute;
            opacity: 0.1;
            animation: float 20s infinite linear;
            font-size: 1.5rem;
            color: var(--accent-green);
        }

        @keyframes float {
            0% {
                transform: translateY(100vh) translateX(0) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 0.1;
            }
            90% {
                opacity: 0.1;
            }
            100% {
                transform: translateY(-100px) translateX(100px) rotate(360deg);
                opacity: 0;
            }
        }

        .navbar {
            background: var(--gradient-primary);
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(45, 90, 61, 0.2);
            border: none;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--mint-green), transparent);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.8rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            transition: transform 0.3s ease;
        }

        .navbar-brand:hover {
            transform: scale(1.05);
        }

        .nav-link {
            font-weight: 500;
            position: relative;
            transition: all 0.3s ease;
            border-radius: 25px;
            padding: 8px 16px !important;
            margin: 0 4px;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            transform: translateY(-2px);
        }

        .nav-link.active {
            background: rgba(255,255,255,0.2);
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
        }

        .container {
            position: relative;
        }

        .card {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(168, 230, 163, 0.3);
            border-radius: 20px;
            box-shadow:
                0 8px 32px var(--soft-shadow),
                0 1px 0 rgba(255,255,255,0.8) inset;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            overflow: hidden;
            position: relative;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--gradient-primary);
        }

        .card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow:
                0 20px 40px var(--soft-shadow),
                0 1px 0 rgba(255,255,255,0.9) inset;
            border-color: var(--accent-green);
        }

        .btn-primary {
            background: var(--gradient-primary);
            border: none;
            border-radius: 25px;
            padding: 12px 24px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(45, 90, 61, 0.3);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(45, 90, 61, 0.4);
        }

        .btn-outline-primary {
            color: var(--primary-green);
            border: 2px solid var(--primary-green);
            background: transparent;
            backdrop-filter: blur(10px);
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background: var(--primary-green);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(45, 90, 61, 0.3);
        }

        .badge-primary {
            background: var(--gradient-primary);
            border-radius: 15px;
            padding: 6px 12px;
            font-weight: 500;
        }

        .badge-warning {
            background: linear-gradient(135deg, var(--accent-orange), var(--warm-orange));
            border-radius: 15px;
            padding: 6px 12px;
            font-weight: 500;
        }

        .badge-success {
            background: linear-gradient(135deg, var(--accent-green), var(--mint-green));
            border-radius: 15px;
            padding: 6px 12px;
            font-weight: 500;
        }

        .form-control {
            border-radius: 15px;
            border: 2px solid rgba(168, 230, 163, 0.3);
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            padding: 12px 18px;
        }

        .form-control:focus {
            border-color: var(--accent-green);
            box-shadow: 0 0 0 0.25rem rgba(76, 175, 80, 0.15);
            background: rgba(255,255,255,0.95);
            transform: translateY(-2px);
        }

        .page-header {
            background: var(--gradient-secondary);
            border-radius: 25px;
            padding: 3rem 2rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(168, 230, 163, 0.3);
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 100px;
            height: 100px;
            background: rgba(76, 175, 80, 0.1);
            border-radius: 50%;
            animation: pulse 4s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.2); opacity: 0.2; }
        }

        .breadcrumb {
            background: rgba(255,255,255,0.7);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 12px 20px;
            border: 1px solid rgba(168, 230, 163, 0.3);
        }

        .breadcrumb-item a {
            color: var(--primary-green);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .breadcrumb-item a:hover {
            color: var(--accent-green);
            text-shadow: 0 1px 3px rgba(76, 175, 80, 0.3);
        }

        .table th {
            background-color: var(--light-green);
            color: var(--dark-green);
            font-weight: 600;
            border: none;
        }

        .table td {
            vertical-align: middle;
            border-color: #e9ecef;
        }
        .alert {
            border-radius: 20px;
            border: none;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(168, 230, 163, 0.9), rgba(76, 175, 80, 0.1));
            color: var(--forest-green);
            border-left: 4px solid var(--accent-green);
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(255, 140, 66, 0.1), rgba(255, 167, 38, 0.05));
            color: #d32f2f;
            border-left: 4px solid #ff5252;
        }

        .search-filter-card {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px var(--soft-shadow);
            border: 1px solid rgba(168, 230, 163, 0.3);
        }

        .stats-card {
            background: var(--gradient-primary);
            color: white;
            border-radius: 25px;
            padding: 2rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(45, 90, 61, 0.3);
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 150px;
            height: 150px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            animation: float-stats 6s ease-in-out infinite;
        }

        @keyframes float-stats {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .action-buttons .btn {
            margin: 0 3px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .action-buttons .btn:hover {
            transform: translateY(-2px);
        }

        .project-status-badge {
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 0.875rem;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
        }

        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(168, 230, 163, 0.1);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--gradient-primary);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--forest-green);
        }
        /* Custom Pagination */
        .custom-pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
            margin-bottom: 50px;
            flex-wrap: wrap;
        }

        .custom-pagination .page-link {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 12px;
            text-decoration: none;
            color: #2d5a3d;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .custom-pagination .page-link:hover {
            background: #3a7f5f;
            color: #fff;
            transform: scale(1.1);
        }

        .custom-pagination .page-link.active {
            background: #2d5a3d;
            color: #fff;
            font-weight: bold;
        }

        .custom-pagination .page-link.disabled {
            opacity: 0.5;
            pointer-events: none;
            background: #e8f5e8;
            color: #a8e6a3;
        }
        /* Add this CSS to your main stylesheet or in a <style> tag */
        .border-left-success {
            border-left: 4px solid #28a745 !important;
        }

        .badge-lg {
            font-size: 1rem;
            padding: 0.5rem 0.75rem;
        }

        .text-xs {
            font-size: 0.75rem;
        }

        .text-sm {
            font-size: 0.875rem;
        }

        .badge-secondary {
            background-color: #6c757d;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .badge-success {
            background-color: #28a745;
        }

        .badge-light {
            background-color: #f8f9fa;
            color: #6c757d;
        }

        .badge-primary {
            background-color: #007bff;
        }

        #recommendations .card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        #recommendations .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }


        /* Custom Status Badges */
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-en-attente {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-confirmee {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-annulee {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .status-terminee {
            background-color: #dbeafe;
            color: #1e40af;
        }

        /* Bootstrap-style Status Badges for Participations */
        .status-badge-en_attente {
            background: linear-gradient(135deg, #fef3c7, #fbbf24);
            color: #92400e;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.875rem;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(251, 191, 36, 0.3);
        }

        .status-badge-confirmee {
            background: linear-gradient(135deg, #d1fae5, #34d399);
            color: #065f46;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.875rem;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(52, 211, 153, 0.3);
        }

        .status-badge-annulee {
            background: linear-gradient(135deg, #fee2e2, #f87171);
            color: #991b1b;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.875rem;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(248, 113, 113, 0.3);
        }

        .status-badge-terminee {
            background: linear-gradient(135deg, #dbeafe, #60a5fa);
            color: #1e40af;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.875rem;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(96, 165, 250, 0.3);
        }

        /* Custom styles for participation views */
        .info-group {
            margin-bottom: 1.5rem;
        }

        .info-group label {
            display: block;
            font-weight: 600;
            color: var(--primary-green);
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .info-group .value {
            font-size: 1rem;
            color: #374151;
            font-weight: 500;
        }

        .info-group .value-large {
            font-size: 1.25rem;
            color: #111827;
            font-weight: 600;
        }

        .timeline-item {
            display: flex;
            align-items-center;
            margin-bottom: 1rem;
            position: relative;
        }

        .timeline-marker {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .timeline-content h6 {
            margin-bottom: 0.25rem;
            font-weight: 600;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #6b7280;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #374151;
        }

        .empty-state p {
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }

        .form-actions {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e5e7eb;
            display: flex;
            gap: 1rem;
        }

        /* Responsive enhancements */
        @media (max-width: 768px) {
            .page-header {
                padding: 2rem 1.5rem;
            }

            .card {
                margin-bottom: 1rem;
            }

            .stats-card {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Floating nature elements -->
    <div class="floating-leaves">
        <i class="leaf fas fa-leaf" style="left: 10%; animation-delay: -2s;"></i>
        <i class="leaf fas fa-seedling" style="left: 20%; animation-delay: -4s;"></i>
        <i class="leaf fas fa-leaf" style="left: 30%; animation-delay: -6s;"></i>
        <i class="leaf fas fa-tree" style="left: 40%; animation-delay: -8s;"></i>
        <i class="leaf fas fa-leaf" style="left: 50%; animation-delay: -10s;"></i>
        <i class="leaf fas fa-seedling" style="left: 60%; animation-delay: -12s;"></i>
        <i class="leaf fas fa-leaf" style="left: 70%; animation-delay: -14s;"></i>
        <i class="leaf fas fa-tree" style="left: 80%; animation-delay: -16s;"></i>
        <i class="leaf fas fa-leaf" style="left: 90%; animation-delay: -18s;"></i>
    </div>

    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-leaf me-2"></i>UrbanGreen
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
           <div class="collapse navbar-collapse" id="navbarNav">
    <!-- Left side links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('associations.*') ? 'active' : '' }}"
               href="{{ route('associations.index') }}">
                <i class="fas fa-users me-1"></i>Associations
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}"
               href="{{ route('projects.index') }}">
                <i class="fas fa-project-diagram me-1"></i>Projets
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('participations.*') ? 'active' : '' }}"
               href="{{ route('participations.index') }}">
                <i class="fas fa-hand-holding-heart me-1"></i>Participations
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('greenspaces.*') ? 'active' : '' }}"
               href="{{ route('greenspaces.index') }}">
                <i class="fa-solid fa-tree me-1"></i>Espaces Verts
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('events.*') ? 'active' : '' }}"
               href="{{ route('events.index') }}">
                <i class="fas fa-calendar-alt me-1"></i>Événements
            </a>
        </li>
         <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"
            href="{{ route('reports.index') }}">
                <i class="fas fa-flag me-1"></i>Signalements
            </a>
        </li>
    </ul>

    <!-- Right side links -->
    <ul class="navbar-nav ms-auto">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('team') ? 'active' : '' }}"
               href="{{ route('team') }}">
                <i class="fas fa-users-cog me-1"></i>Team
            </a>
        </li>
        
        @auth
            {{-- User is authenticated --}}
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user-avatar me-2">
                        <i class="fas fa-user-circle fa-lg"></i>
                    </div>
                    <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" style="min-width: 250px;">
                    <li class="dropdown-header bg-light">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user-circle fa-2x me-2 text-primary"></i>
                            <div>
                                <div class="fw-bold">{{ Auth::user()->name }}</div>
                                <small class="text-muted">{{ Auth::user()->email }}</small>
                            </div>
                        </div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item {{ request()->routeIs('auth.dashboard') ? 'active' : '' }}" href="{{ route('auth.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2 text-info"></i>Tableau de bord
                        </a>
                    </li>
                    @if(Auth::user()->isAdmin())
                        <li><hr class="dropdown-divider"></li>
                        <li class="dropdown-header">
                            <i class="fas fa-crown me-1 text-warning"></i>Administration
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-users-cog me-2 text-danger"></i>Gestion des utilisateurs
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-chart-line me-2 text-success"></i>Statistiques
                            </a>
                        </li>
                    @endif
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('auth.logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?')">
                                <i class="fas fa-sign-out-alt me-2"></i>Se déconnecter
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
        @else
            {{-- User is not authenticated --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('auth.login') ? 'active' : '' }}" href="{{ route('auth.login') }}">
                    <i class="fas fa-sign-in-alt me-1"></i>Connexion
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('auth.register') ? 'active' : '' }}" href="{{ route('auth.register') }}">
                    <i class="fas fa-user-plus me-1"></i>S'inscrire
                </a>
            </li>
        @endauth
    </ul>
</div>

        </div>
    </nav>

    <main class="container mt-4">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
