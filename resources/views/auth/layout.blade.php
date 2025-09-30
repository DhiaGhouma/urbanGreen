<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'UrbanGreen - Authentification')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23a8e6a3' fill-opacity='0.05'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
            z-index: -1;
        }

        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(168, 230, 163, 0.2);
            border-radius: 20px;
            box-shadow: 
                0 10px 40px rgba(45, 90, 61, 0.1),
                0 0 20px rgba(168, 230, 163, 0.1);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
            position: relative;
        }

        .auth-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: var(--gradient-primary);
        }

        .auth-header {
            text-align: center;
            padding: 3rem 2rem 1rem;
            background: var(--gradient-secondary);
            position: relative;
        }

        .auth-header::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background: var(--accent-green);
            border-radius: 2px;
        }

        .auth-logo {
            font-size: 2.5rem;
            color: var(--primary-green);
            margin-bottom: 0.5rem;
        }

        .auth-title {
            color: var(--forest-green);
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .auth-subtitle {
            color: var(--secondary-green);
            font-size: 1rem;
            font-weight: 400;
            opacity: 0.8;
        }

        .auth-body {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-label {
            color: var(--forest-green);
            font-weight: 500;
            margin-bottom: 0.5rem;
            display: block;
            font-size: 0.95rem;
        }

        .form-control {
            border: 2px solid rgba(168, 230, 163, 0.3);
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
            position: relative;
        }

        .form-control:focus {
            border-color: var(--accent-green);
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.15);
            background: white;
            outline: none;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
            background: rgba(220, 53, 69, 0.05);
        }

        .form-control.is-valid {
            border-color: var(--accent-green);
            background: rgba(76, 175, 80, 0.05);
        }

        .input-group {
            position: relative;
        }

        .input-group-text {
            background: transparent;
            border: 2px solid rgba(168, 230, 163, 0.3);
            border-right: none;
            border-radius: 12px 0 0 12px;
            color: var(--secondary-green);
        }

        .input-group .form-control {
            border-left: none;
        }

        .input-group:focus-within .input-group-text {
            border-color: var(--accent-green);
        }

        .input-group .btn-outline-secondary {
            border-left: none;
            border-color: rgba(168, 230, 163, 0.3);
            color: var(--secondary-green);
            border-radius: 0 12px 12px 0;
        }

        .input-group .btn-outline-secondary:hover {
            background-color: var(--light-green);
            border-color: var(--accent-green);
            color: var(--primary-green);
        }

        .input-group:focus-within .btn-outline-secondary {
            border-color: var(--accent-green);
        }

        .invalid-feedback {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: block;
        }

        .valid-feedback {
            color: var(--accent-green);
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: block;
        }

        .btn-auth {
            background: var(--gradient-primary);
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 600;
            color: white;
            font-size: 1rem;
            width: 100%;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-auth::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-auth:hover::before {
            left: 100%;
        }

        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(45, 90, 61, 0.3);
        }

        .btn-auth:active {
            transform: translateY(0);
        }

        .btn-auth:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .auth-footer {
            padding: 1rem 2rem 2rem;
            text-align: center;
            background: rgba(232, 245, 232, 0.3);
        }

        .auth-link {
            color: var(--secondary-green);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .auth-link:hover {
            color: var(--primary-green);
            text-decoration: underline;
        }

        .alert {
            border: none;
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .alert::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
        }

        .alert-success {
            background: rgba(76, 175, 80, 0.1);
            color: var(--forest-green);
            border-left: 4px solid var(--accent-green);
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .alert-warning {
            background: rgba(255, 193, 7, 0.1);
            color: #856404;
            border-left: 4px solid #ffc107;
        }

        .alert-info {
            background: rgba(13, 202, 240, 0.1);
            color: #055160;
            border-left: 4px solid #0dcaf0;
        }

        .form-check {
            margin: 1rem 0;
        }

        .form-check-input {
            width: 1.2em;
            height: 1.2em;
            border: 2px solid rgba(168, 230, 163, 0.5);
            border-radius: 4px;
        }

        .form-check-input:checked {
            background-color: var(--accent-green);
            border-color: var(--accent-green);
        }

        .form-check-label {
            color: var(--secondary-green);
            font-size: 0.9rem;
            margin-left: 0.5rem;
        }

        .form-text {
            color: var(--secondary-green);
            font-size: 0.85rem;
            margin-top: 0.25rem;
            opacity: 0.8;
        }

        /* Password strength indicator */
        .password-strength {
            margin-top: 0.5rem;
            font-size: 0.8rem;
        }

        .strength-bar {
            height: 4px;
            border-radius: 2px;
            overflow: hidden;
            background: #e9ecef;
            margin: 0.25rem 0;
        }

        .strength-fill {
            height: 100%;
            transition: width 0.3s ease, background-color 0.3s ease;
        }

        .strength-weak { background-color: #dc3545; }
        .strength-fair { background-color: #ffc107; }
        .strength-good { background-color: var(--warm-orange); }
        .strength-strong { background-color: var(--accent-green); }

        /* Loading state */
        .btn-loading {
            position: relative;
            color: transparent !important;
        }

        .btn-loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 2px solid transparent;
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive design */
        @media (max-width: 576px) {
            .auth-container {
                padding: 1rem;
            }
            
            .auth-card {
                margin: 0;
                border-radius: 16px;
            }
            
            .auth-header {
                padding: 2rem 1.5rem 1rem;
            }
            
            .auth-body {
                padding: 1.5rem;
            }
            
            .auth-title {
                font-size: 1.5rem;
            }
        }

        /* Animation for form appearance */
        .auth-card {
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>