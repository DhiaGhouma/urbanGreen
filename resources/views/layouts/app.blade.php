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
            <a class="navbar-brand" href="{{ route('associations.index') }}">
                <i class="fas fa-leaf me-2"></i>UrbanGreen
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
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
                        <a class="nav-link {{ request()->routeIs('greenspaces.*') ? 'active' : '' }}"
                           href="{{ route('greenspaces.index') }}">
                            <i class="fa-solid fa-tree"></i>greenspaces
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container mt-4">
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

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
