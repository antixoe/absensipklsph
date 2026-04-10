<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Absensi PKL</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f5f5f5;
            color: #333;
        }

        .navbar {
            background: #f97316;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-brand {
            color: white;
            font-size: 20px;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-brand img {
            height: 40px;
            width: auto;
            object-fit: contain;
        }

        .logo-img {
            height: 80px;
            width: auto;
            object-fit: contain;
            margin-bottom: 15px;
        }

        .navbar-nav {
            display: flex;
            gap: 5px;
            margin: 0 auto;
        }

        .navbar-nav a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 4px;
            transition: background 0.3s;
            font-size: 14px;
        }

        .navbar-nav a[href*="admin"] {
            background: rgba(255, 255, 255, 0.15);
            margin-left: 10px;
        }

        .navbar-nav a[href*="admin"]:hover,
        .navbar-nav a[href*="admin"].active {
            background: rgba(255, 255, 255, 0.3);
        }

        .navbar-nav a:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .navbar-nav a.active {
            background: rgba(255, 255, 255, 0.3);
        }

        .navbar-end {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info {
            color: white;
            font-size: 14px;
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            transition: background 0.3s;
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 32px;
            color: #333;
            margin-bottom: 10px;
        }

        .page-header p {
            color: #666;
            font-size: 16px;
        }

        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            text-align: center;
        }

        .stat-card .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #f97316;
            margin-bottom: 10px;
        }

        .stat-card .stat-label {
            color: #666;
            font-size: 14px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #f97316;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
            text-align: center;
        }

        .btn:hover {
            background: #ea580c;
        }

        .btn-secondary {
            background: #6b7280;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .button-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
        }

        /* Toast/Popup Styles */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px 24px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            animation: slideIn 0.3s ease-out, slideOut 0.3s ease-out 2.7s forwards;
            z-index: 1000;
            max-width: 300px;
            word-wrap: break-word;
        }

        .toast.success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .toast.error {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 10px;
            }

            .navbar-nav {
                flex-wrap: wrap;
                width: 100%;
                justify-content: center;
            }

            .container {
                padding: 20px 15px;
            }

            .page-header h1 {
                font-size: 24px;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Toast Messages -->
    @if (session('success'))
        <div class="toast success">
            <i class="bi bi-check-circle-fill" style="margin-right: 8px;"></i>{{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="toast error">
            <i class="bi bi-x-circle-fill" style="margin-right: 8px;"></i>{{ session('error') }}
        </div>
    @endif

    <nav class="navbar">
        <a href="/dashboard" class="navbar-brand">
            <img src="https://www.permataharapanku.sch.id/images/logo_sph.png" alt="School Logo">
            Absensi PKL
        </a>

        <div class="navbar-nav">
            <a href="/dashboard" class="{{ request()->is('dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="/attendance" class="{{ request()->is('attendance*') ? 'active' : '' }}">Attendance</a>
            <a href="/logbook" class="{{ request()->is('logbook*') ? 'active' : '' }}">Logbook</a>
            <a href="/activities" class="{{ request()->is('activities*') ? 'active' : '' }}">Activities</a>
            <a href="/reports" class="{{ request()->is('reports*') ? 'active' : '' }}">Reports</a>
            @if(auth()->user()->hasRole('admin'))
                <a href="{{ route('admin.users') }}" class="{{ request()->is('admin/users*') ? 'active' : '' }}" style="margin-left: 20px;">
                    <i class="bi bi-people" style="margin-right: 5px;"></i>Users
                </a>
                <a href="{{ route('admin.roles') }}" class="{{ request()->is('admin/roles*') ? 'active' : '' }}">
                    <i class="bi bi-shield-lock" style="margin-right: 5px;"></i>Roles
                </a>
            @endif
        </div>

        <div class="navbar-end">
            <div class="user-info">
                Welcome, <strong>{{ Auth::user()->name }}</strong>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="logout-btn">Log Out</button>
            </form>
        </div>
    </nav>

    <div class="container">
        @yield('content')
    </div>

    @yield('scripts')
</body>
</html>
