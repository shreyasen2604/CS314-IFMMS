<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'IFMMS-ZAR Logistics')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- FullCalendar -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js'></script>
    <!-- DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- jQuery (required for DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <style>
        :root {
            --primary-color: #4ade80; /* Light green */
            --primary-dark: #22c55e; /* Darker green for hover */
            --primary-light: #86efac; /* Lighter green */
            --secondary-color: #000000; /* Black */
            --white: #ffffff;
            --gray-light: #f3f4f6;
            --gray-medium: #9ca3af;
            --gray-dark: #4b5563;
            --primary-gradient: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);
            --sidebar-width: 260px;
            --navbar-height: 70px;
            
            /* Light mode colors (default) */
            --bg-primary: #ffffff;
            --bg-secondary: #f9fafb;
            --bg-tertiary: #f3f4f6;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --text-tertiary: #9ca3af;
            --border-color: #e5e7eb;
            --card-bg: #ffffff;
            --sidebar-bg: linear-gradient(180deg, #f0fdf4 0%, #ffffff 100%);
            --navbar-bg: linear-gradient(to right, #f0fdf4, #ffffff);
        }
        
        /* Dark mode colors */
        [data-theme="dark"] {
            --bg-primary: #1f2937;
            --bg-secondary: #111827;
            --bg-tertiary: #374151;
            --text-primary: #f9fafb;
            --text-secondary: #d1d5db;
            --text-tertiary: #9ca3af;
            --border-color: #4b5563;
            --card-bg: #374151;
            --sidebar-bg: linear-gradient(180deg, #1f2937 0%, #111827 100%);
            --navbar-bg: linear-gradient(to right, #1f2937, #111827);
            --gray-light: #374151;
            --gray-medium: #6b7280;
            --gray-dark: #9ca3af;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: var(--bg-secondary);
            color: var(--text-primary);
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        
        /* Dark mode toggle button */
        .theme-toggle {
            background: linear-gradient(135deg, rgba(74, 222, 128, 0.1) 0%, rgba(74, 222, 128, 0.2) 100%);
            border: 2px solid var(--primary-light);
            border-radius: 50%;
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            color: var(--primary-dark);
        }
        
        .theme-toggle:hover {
            background: var(--primary-light);
            transform: scale(1.1);
        }
        
        [data-theme="dark"] .theme-toggle {
            background: linear-gradient(135deg, rgba(251, 191, 36, 0.1) 0%, rgba(251, 191, 36, 0.2) 100%);
            border-color: #fbbf24;
            color: #fbbf24;
        }
        
        [data-theme="dark"] .theme-toggle:hover {
            background: rgba(251, 191, 36, 0.3);
        }
        
        /* Top Navigation Bar */
        .navbar-top {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: var(--navbar-height);
            background: var(--navbar-bg);
            border-bottom: 2px solid var(--primary-light);
            box-shadow: 0 4px 6px rgba(74, 222, 128, 0.1);
            z-index: 1030;
            padding: 0 2rem;
            transition: background 0.3s ease;
        }
        
        [data-theme="dark"] .navbar-top {
            border-bottom-color: var(--border-color);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }
        
        .navbar-brand {
            display: flex;
            align-items: center;
            font-weight: 700;
            font-size: 1.5rem;
            color: #333;
            text-decoration: none;
        }
        
        .navbar-brand:hover {
            color: var(--primary-color);
        }
        
        .logo-container {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
        }
        
        .logo-container img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .logo-placeholder {
            width: 45px;
            height: 45px;
            background: var(--primary-gradient);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(74, 222, 128, 0.4);
            border: 2px solid white;
        }
        
        .logo-placeholder i {
            color: white;
            font-size: 1.5rem;
        }
        
        .navbar-nav-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        
        .nav-user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, rgba(74, 222, 128, 0.1) 0%, rgba(255, 255, 255, 0.9) 100%);
            border-radius: 25px;
            border: 2px solid var(--primary-light);
            box-shadow: 0 2px 8px rgba(74, 222, 128, 0.15);
        }
        
        .nav-user-avatar {
            width: 38px;
            height: 38px;
            background: var(--primary-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            border: 3px solid white;
            box-shadow: 0 2px 8px rgba(74, 222, 128, 0.3);
        }
        
        .btn-logout {
            background: linear-gradient(135deg, var(--secondary-color) 0%, #1a1a1a 100%);
            color: white;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 25px;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }
        
        .btn-logout:hover {
            background: linear-gradient(135deg, #1a1a1a 0%, var(--secondary-color) 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: var(--navbar-height);
            left: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            border-right: 2px solid var(--primary-light);
            overflow-y: auto;
            z-index: 1020;
            padding: 1.5rem 0;
            box-shadow: 2px 0 10px rgba(74, 222, 128, 0.05);
            transition: background 0.3s ease;
        }
        
        [data-theme="dark"] .sidebar {
            border-right-color: var(--border-color);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.3);
        }
        
        [data-theme="dark"] .sidebar .nav-link {
            color: var(--text-secondary);
        }
        
        [data-theme="dark"] .sidebar .nav-link:hover {
            color: var(--primary-light);
        }
        
        .sidebar-section {
            padding: 0 1rem;
            margin-bottom: 2rem;
            position: relative;
        }
        
        .sidebar-section:not(:first-child)::before {
            content: '';
            position: absolute;
            top: -1rem;
            left: 1rem;
            right: 1rem;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--primary-light), transparent);
        }
        
        .sidebar-heading {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--primary-dark);
            margin-bottom: 0.75rem;
            padding: 0.5rem 0.75rem;
            background: linear-gradient(90deg, rgba(74, 222, 128, 0.1) 0%, transparent 100%);
            border-left: 3px solid var(--primary-color);
        }
        
        .sidebar .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: var(--secondary-color);
            text-decoration: none;
            border-radius: 0 25px 25px 0;
            margin: 0.25rem 1rem 0.25rem 0;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            font-weight: 500;
            position: relative;
        }
        
        .sidebar .nav-link i {
            width: 24px;
            margin-right: 0.75rem;
            font-size: 1.1rem;
            color: var(--primary-dark);
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover {
            background: linear-gradient(90deg, rgba(74, 222, 128, 0.15) 0%, rgba(74, 222, 128, 0.05) 100%);
            color: var(--primary-dark);
            transform: translateX(5px);
            box-shadow: 0 3px 10px rgba(74, 222, 128, 0.2);
        }
        
        .sidebar .nav-link:hover i {
            color: var(--primary-dark);
            transform: scale(1.1);
        }
        
        .sidebar .nav-link.active {
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 4px 15px rgba(74, 222, 128, 0.4);
            font-weight: 600;
        }
        
        .sidebar .nav-link.active i {
            color: white;
        }
        
        .sidebar .nav-link.active::before {
            content: '';
            position: absolute;
            left: -1rem;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 70%;
            background: var(--primary-dark);
            border-radius: 0 2px 2px 0;
        }
        
        /* Collapsible Menu */
        .nav-link-collapse {
            justify-content: space-between;
        }
        
        .nav-link-collapse .fa-chevron-down {
            transition: transform 0.3s ease;
            font-size: 0.75rem;
            color: var(--primary-dark);
        }
        
        .nav-link-collapse[aria-expanded="true"] .fa-chevron-down {
            transform: rotate(180deg);
        }
        
        .collapse-menu {
            padding-left: 2.5rem;
            background: rgba(74, 222, 128, 0.03);
            border-left: 2px solid var(--primary-light);
            margin-left: 1rem;
            margin-top: 0.5rem;
            border-radius: 0 10px 10px 0;
        }
        
        .collapse-menu .nav-link {
            font-size: 0.9rem;
            padding: 0.6rem 0.75rem;
            margin: 0.15rem 0;
        }
        
        .collapse-menu .nav-link:hover {
            background: rgba(74, 222, 128, 0.1);
        }
        
        .collapse-menu .nav-link.active {
            background: linear-gradient(90deg, var(--primary-light) 0%, rgba(74, 222, 128, 0.2) 100%);
            color: var(--primary-dark);
            font-weight: 600;
        }
        
        .collapse-menu .nav-link.active i {
            color: var(--primary-dark);
        }
        
        /* Main Content Area */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--navbar-height);
            padding: 2rem;
            min-height: calc(100vh - var(--navbar-height));
            background: #f9fafb;
        }
        
        /* Page Header */
        .page-header {
            background: white;
            border-radius: 12px;
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        
        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.25rem;
        }
        
        .page-subtitle {
            color: #6b7280;
            font-size: 1rem;
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
            background: white;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background: transparent;
            border-bottom: 1px solid #f3f4f6;
            padding: 1.25rem 1.5rem;
        }
        
        .card-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
        }
        
        /* Gradient Backgrounds with Company Colors */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);
        }
        
        .bg-gradient-success {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        }
        
        .bg-gradient-dark {
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
        }
        
        .bg-gradient-warning {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        }
        
        .bg-gradient-info {
            background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
        }
        
        .bg-gradient-danger {
            background: linear-gradient(135deg, #f87171 0%, #ef4444 100%);
        }
        
        /* Button Styles */
        .btn-primary {
            background: var(--primary-color);
            border: none;
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        /* Badge Colors */
        .badge.bg-success {
            background: var(--primary-color) !important;
        }
        
        /* Notification Bell */
        .notification-bell {
            position: relative;
            color: var(--primary-dark);
            font-size: 1.25rem;
            transition: all 0.3s ease;
            padding: 0.5rem;
            border-radius: 50%;
            background: rgba(74, 222, 128, 0.1);
        }
        
        .notification-bell:hover {
            color: var(--primary-color);
            background: rgba(74, 222, 128, 0.2);
            transform: scale(1.1);
        }
        
        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: bold;
            border: 2px solid white;
        }
        
        /* Mobile Responsive */
        .sidebar-toggle {
            display: none;
            background: linear-gradient(135deg, rgba(74, 222, 128, 0.1) 0%, rgba(74, 222, 128, 0.2) 100%);
            border: 2px solid var(--primary-light);
            border-radius: 10px;
            padding: 0.5rem 0.75rem;
            font-size: 1.25rem;
            color: var(--primary-dark);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .sidebar-toggle:hover {
            background: var(--primary-light);
            transform: scale(1.05);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .sidebar-toggle {
                display: block;
            }
            
            .navbar-top {
                padding: 0 1rem;
            }
        }
        
        /* Utility Classes */
        .chart-placeholder {
            border-radius: 10px;
        }
        
        .activity-item, .schedule-item, .incident-item {
            border-radius: 8px;
            transition: background-color 0.2s ease;
        }
        
        .activity-item:hover, .schedule-item:hover, .incident-item:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }
    </style>
</head>
<body>
    <!-- Top Navigation Bar -->
    <nav class="navbar-top d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <button class="sidebar-toggle me-3" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <a href="{{ route(strtolower(auth()->user()->role).'.dashboard') }}" class="navbar-brand">
                <div class="logo-container">
                    @if(file_exists(public_path('images/logo.png')))
                        <img src="{{ asset('images/logo.png') }}" alt="IFMMS-ZAR Logo">
                    @elseif(file_exists(public_path('images/logo.jpg')))
                        <img src="{{ asset('images/logo.jpg') }}" alt="IFMMS-ZAR Logo">
                    @elseif(file_exists(public_path('images/logo.svg')))
                        <img src="{{ asset('images/logo.svg') }}" alt="IFMMS-ZAR Logo">
                    @else
                        <div class="logo-placeholder">
                            <i class="fas fa-truck"></i>
                        </div>
                    @endif
                </div>
                <span style="color: var(--secondary-color); font-weight: 700;">IFMMS-ZAR</span>
            </a>
        </div>
        
        <div class="navbar-nav-right">
            <!-- Dark Mode Toggle -->
            <button class="theme-toggle" onclick="toggleTheme()" title="Toggle dark mode">
                <i class="fas fa-moon" id="theme-icon"></i>
            </button>
            
            <div class="notification-bell position-relative">
                <i class="fas fa-bell"></i>
                <span class="notification-badge">3</span>
            </div>
            
            <div class="nav-user-info">
                <div class="nav-user-avatar">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div>
                    <div class="fw-semibold">{{ auth()->user()->name }}</div>
                    <div class="small text-muted">{{ auth()->user()->role }}</div>
                </div>
            </div>
            
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="fas fa-sign-out-alt me-1"></i>
                    Logout
                </button>
            </form>
        </div>
    </nav>
    
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <!-- Main Dashboard -->
        <div class="sidebar-section">
            <div class="sidebar-heading">Main</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs(strtolower(auth()->user()->role).'.dashboard') ? 'active' : '' }}" 
                       href="{{ route(strtolower(auth()->user()->role).'.dashboard') }}">
                        <i class="fas fa-home"></i>
                        Dashboard
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- Fleet Management Module -->
        <div class="sidebar-section">
            <div class="sidebar-heading">Modules</div>
            <ul class="nav flex-column">
                <!-- Fleet Management Dropdown -->
                <li class="nav-item">
                    <a class="nav-link nav-link-collapse d-flex justify-content-between align-items-center" 
                       data-bs-toggle="collapse" 
                       href="#fleetManagementMenu" 
                       role="button" 
                       aria-expanded="{{ request()->routeIs('maintenance.*') ? 'true' : 'false' }}" 
                       aria-controls="fleetManagementMenu">
                        <span>
                            <i class="fas fa-truck"></i>
                            Fleet Management
                        </span>
                        <i class="fas fa-chevron-down"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('maintenance.*') ? 'show' : '' }}" id="fleetManagementMenu">
                        <ul class="nav flex-column collapse-menu">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('maintenance.dashboard') ? 'active' : '' }}" 
                                   href="{{ route('maintenance.dashboard') }}">
                                    <i class="fas fa-chart-line"></i>
                                    Overview
                                </a>
                            </li>
                            
                            @if(auth()->user()->role === 'Driver')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('maintenance.driver.*') ? 'active' : '' }}" 
                                   href="{{ route('maintenance.driver.vehicle') }}">
                                    <i class="fas fa-car"></i>
                                    My Vehicle
                                </a>
                            </li>
                            @endif
                            
                            @if(auth()->user()->role === 'Technician')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('maintenance.technician.*') ? 'active' : '' }}" 
                                   href="{{ route('maintenance.technician.work-queue') }}">
                                    <i class="fas fa-tasks"></i>
                                    Work Queue
                                </a>
                            </li>
                            @endif
                            
                            @if(in_array(auth()->user()->role, ['Admin', 'Technician']))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('maintenance.schedule*') ? 'active' : '' }}" 
                                   href="{{ route('maintenance.schedule') }}">
                                    <i class="fas fa-calendar-alt"></i>
                                    Scheduler
                                </a>
                            </li>
                            @endif
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('maintenance.analytics') ? 'active' : '' }}" 
                                   href="{{ route('maintenance.analytics') }}">
                                    <i class="fas fa-chart-bar"></i>
                                    Analytics
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('maintenance.records*') && !request()->routeIs('maintenance.records.create') ? 'active' : '' }}" 
                                   href="{{ route('maintenance.records') }}">
                                    <i class="fas fa-clipboard-list"></i>
                                    Records
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('maintenance.alerts*') ? 'active' : '' }}" 
                                   href="{{ route('maintenance.alerts') }}">
                                    <i class="fas fa-bell"></i>
                                    Alerts
                                </a>
                            </li>
                            
                            @if(auth()->user()->role === 'Admin')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('maintenance.vehicles.index') ? 'active' : '' }}" 
                                   href="{{ route('maintenance.vehicles.index') }}">
                                    <i class="fas fa-car-side"></i>
                                    Vehicle List
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('maintenance.vehicles.create') ? 'active' : '' }}" 
                                   href="{{ route('maintenance.vehicles.create') }}">
                                    <i class="fas fa-plus-circle"></i>
                                    Add Vehicle
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('maintenance.vehicles.assignments') ? 'active' : '' }}" 
                                   href="{{ route('maintenance.vehicles.assignments') }}">
                                    <i class="fas fa-user-tag"></i>
                                    Assignments
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                
                <!-- Service Support Dropdown -->
                <li class="nav-item">
                    <a class="nav-link nav-link-collapse d-flex justify-content-between align-items-center" 
                       data-bs-toggle="collapse" 
                       href="#supportMenu" 
                       role="button" 
                       aria-expanded="{{ request()->routeIs('support.*') ? 'true' : 'false' }}" 
                       aria-controls="supportMenu">
                        <span>
                            <i class="fas fa-headset"></i>
                            Service & Support
                        </span>
                        <i class="fas fa-chevron-down"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('support.*') ? 'show' : '' }}" id="supportMenu">
                        <ul class="nav flex-column collapse-menu">
                            {{-- Service Requests (admins & techs only) --}}
                            @if(in_array(strtolower(auth()->user()->role), ['admin','technician']))
                            <li class="nav-item" data-roles="admin,technician">
                                <a class="nav-link {{ request()->routeIs('support.service-requests.index') ? 'active' : '' }}" 
                                href="{{ route('support.service-requests.index') }}">
                                    <i class="fas fa-ticket-alt"></i>
                                    Service Requests
                                </a>
                            </li>
                            @endif

                            {{-- New Request (drivers + admins) --}}
                            @if(in_array(strtolower(auth()->user()->role), ['driver','admin']))
                            <li class="nav-item" data-roles="driver,admin">
                                <a class="nav-link {{ request()->routeIs('support.service-requests.create') ? 'active' : '' }}" 
                                href="{{ route('support.service-requests.create') }}">
                                    <i class="fas fa-plus-circle"></i>
                                    New Request
                                </a>
                            </li>
                            @endif

                            {{-- Incident Reports (admins & techs only) --}}
                            @if(in_array(strtolower(auth()->user()->role), ['admin','technician']))
                            <li class="nav-item" data-roles="admin,technician">
                                <a class="nav-link {{ request()->routeIs('support.incidents.index') ? 'active' : '' }}" 
                                href="{{ route('support.incidents.index') }}">
                                    <i class="fas fa-exclamation-circle"></i>
                                    Incident Reports
                                </a>
                            </li>
                            @endif

                            {{-- Report Incident (drivers + admins) --}}
                            @if(in_array(strtolower(auth()->user()->role), ['driver','admin']))
                            <li class="nav-item" data-roles="driver,admin">
                                <a class="nav-link {{ request()->routeIs('support.incidents.create') ? 'active' : '' }}" 
                                href="{{ route('support.incidents.create') }}">
                                    <i class="fas fa-file-medical"></i>
                                    Report Incident
                                </a>
                            </li>
                            @endif

                            @if(in_array(auth()->user()->role, ['Admin', 'Technician']))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('support.statistics') ? 'active' : '' }}" 
                                   href="{{ route('support.statistics') }}">
                                    <i class="fas fa-chart-pie"></i>
                                    Support Analytics
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('support.incident-statistics') ? 'active' : '' }}" 
                                   href="{{ route('support.incident-statistics') }}">
                                    <i class="fas fa-chart-line"></i>
                                    Incident Statistics
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                
                <!-- Communication Module Dropdown -->
                <li class="nav-item">
                    <a class="nav-link nav-link-collapse d-flex justify-content-between align-items-center" 
                       data-bs-toggle="collapse" 
                       href="#communicationMenu" 
                       role="button" 
                       aria-expanded="{{ request()->routeIs('communication.*') ? 'true' : 'false' }}" 
                       aria-controls="communicationMenu">
                        <span>
                            <i class="fas fa-comments"></i>
                            Communication
                        </span>
                        <i class="fas fa-chevron-down"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('communication.*') ? 'show' : '' }}" id="communicationMenu">
                        <ul class="nav flex-column collapse-menu">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('communication.index') ? 'active' : '' }}" 
                                   href="{{ route('communication.index') }}">
                                    <i class="fas fa-tachometer-alt"></i>
                                    Communication Hub
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('communication.messages*') ? 'active' : '' }}" 
                                   href="{{ route('communication.messages') }}">
                                    <i class="fas fa-envelope"></i>
                                    Messages
                                    @php
                                        try {
                                            $unreadCount = auth()->user()->unread_messages_count;
                                        } catch (\Exception $e) {
                                            $unreadCount = 0;
                                        }
                                    @endphp
                                    @if($unreadCount > 0)
                                        <span class="badge bg-danger ms-2">{{ $unreadCount }}</span>
                                    @endif
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('communication.announcements*') && !request()->routeIs('communication.create-announcement') ? 'active' : '' }}" 
                                   href="{{ route('communication.announcements') }}">
                                    <i class="fas fa-bullhorn"></i>
                                    Announcements
                                </a>
                            </li>
                            @if(auth()->user()->role === 'Admin')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('communication.create-announcement') ? 'active' : '' }}" 
                                   href="{{ route('communication.create-announcement') }}">
                                    <i class="fas fa-plus-circle"></i>
                                    Create Announcement
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                
                <!-- Driver Performance & Route Management Module -->
                @if(auth()->user()->role === 'Admin')
                <li class="nav-item">
                    <a class="nav-link nav-link-collapse d-flex justify-content-between align-items-center" 
                       data-bs-toggle="collapse" 
                       href="#routeManagementMenu" 
                       role="button" 
                       aria-expanded="{{ request()->routeIs('maintenance.routes*') || request()->routeIs('maintenance.route-assignments*') || request()->routeIs('maintenance.driver-performance*') ? 'true' : 'false' }}" 
                       aria-controls="routeManagementMenu">
                        <span>
                            <i class="fas fa-route"></i>
                            Route & Performance
                        </span>
                        <i class="fas fa-chevron-down"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('maintenance.routes*') || request()->routeIs('maintenance.route-assignments*') || request()->routeIs('maintenance.driver-performance*') ? 'show' : '' }}" id="routeManagementMenu">
                        <ul class="nav flex-column collapse-menu">
                            <!-- Route Management -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('maintenance.routes.index') ? 'active' : '' }}" 
                                   href="{{ route('maintenance.routes.index') }}">
                                    <i class="fas fa-map-marked-alt"></i>
                                    Route Management
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('maintenance.routes.create') ? 'active' : '' }}" 
                                   href="{{ route('maintenance.routes.create') }}">
                                    <i class="fas fa-plus-circle"></i>
                                    Create Route
                                </a>
                            </li>
                            
                            <!-- Route Assignments -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('maintenance.route-assignments.index') ? 'active' : '' }}" 
                                   href="{{ route('maintenance.route-assignments.index') }}">
                                    <i class="fas fa-clipboard-check"></i>
                                    Route Assignments
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('maintenance.route-assignments.create') ? 'active' : '' }}" 
                                   href="{{ route('maintenance.route-assignments.create') }}">
                                    <i class="fas fa-calendar-plus"></i>
                                    Schedule Assignment
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('maintenance.route-assignments-calendar') ? 'active' : '' }}" 
                                   href="{{ route('maintenance.route-assignments.calendar') }}">
                                    <i class="fas fa-calendar-alt"></i>
                                    Assignment Calendar
                                </a>
                            </li>
                            
                            <!-- Driver Performance -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('maintenance.driver-performance.index') ? 'active' : '' }}" 
                                   href="{{ route('maintenance.driver-performance.index') }}">
                                    <i class="fas fa-tachometer-alt"></i>
                                    Driver Performance
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('maintenance.driver-performance-rankings') ? 'active' : '' }}" 
                                   href="{{ route('maintenance.driver-performance.rankings') }}">
                                    <i class="fas fa-trophy"></i>
                                    Performance Rankings
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('maintenance.driver-performance-analytics') ? 'active' : '' }}" 
                                   href="{{ route('maintenance.driver-performance.analytics') }}">
                                    <i class="fas fa-chart-line"></i>
                                    Performance Analytics
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('maintenance.driver-performance-compare') ? 'active' : '' }}" 
                                   href="{{ route('maintenance.driver-performance.compare') }}">
                                    <i class="fas fa-balance-scale"></i>
                                    Compare Drivers
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif
                
                @if(auth()->user()->role === 'Admin')
                <!-- Administration Dropdown -->
                <li class="nav-item">
                    <a class="nav-link nav-link-collapse d-flex justify-content-between align-items-center" 
                       data-bs-toggle="collapse" 
                       href="#adminMenu" 
                       role="button" 
                       aria-expanded="{{ request()->routeIs('admin.*') ? 'true' : 'false' }}" 
                       aria-controls="adminMenu">
                        <span>
                            <i class="fas fa-cogs"></i>
                            Administration
                        </span>
                        <i class="fas fa-chevron-down"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('admin.*') ? 'show' : '' }}" id="adminMenu">
                        <ul class="nav flex-column collapse-menu">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}" 
                                   href="{{ route('admin.users.index') }}">
                                    <i class="fas fa-users"></i>
                                    User Management
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.users.create') ? 'active' : '' }}" 
                                   href="{{ route('admin.users.create') }}">
                                    <i class="fas fa-user-plus"></i>
                                    Add New User
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.incidents.*') ? 'active' : '' }}" 
                                   href="{{ route('admin.incidents.index') }}">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Legacy Incidents
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif
                
                @if(auth()->user()->role === 'Driver')
                
                <!-- Driver Tools Dropdown -->
                <li class="nav-item">
                    <a class="nav-link nav-link-collapse d-flex justify-content-between align-items-center" 
                    data-bs-toggle="collapse" 
                    href="#driverMenu" 
                    role="button" 
                    aria-expanded="{{ request()->routeIs('driver.*') ? 'true' : 'false' }}" 
                    aria-controls="driverMenu">
                        <span>
                            <i class="fas fa-id-card"></i>
                            Driver Tools
                        </span>
                        <i class="fas fa-chevron-down"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('driver.*') ? 'show' : '' }}" id="driverMenu">
                        <ul class="nav flex-column collapse-menu">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('driver.incidents.*') ? 'active' : '' }}" 
                                href="{{ route('driver.incidents.index') }}">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    My Incidents
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('driver.service-requests.*') ? 'active' : '' }}"
                                href="{{ route('driver.service-requests.index') }}">
                                    <i class="fas fa-list"></i>
                                    My Service Requests
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif
                                
                @if(auth()->user()->role === 'Technician')
                <!-- Technician Tools Dropdown -->
                <li class="nav-item">
                    <a class="nav-link nav-link-collapse d-flex justify-content-between align-items-center" 
                       data-bs-toggle="collapse" 
                       href="#technicianMenu" 
                       role="button" 
                       aria-expanded="{{ request()->routeIs('technician.*') ? 'true' : 'false' }}" 
                       aria-controls="technicianMenu">
                        <span>
                            <i class="fas fa-tools"></i>
                            Technician Tools
                        </span>
                        <i class="fas fa-chevron-down"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('technician.*') ? 'show' : '' }}" id="technicianMenu">
                        <ul class="nav flex-column collapse-menu">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('technician.incidents.*') ? 'active' : '' }}" 
                                   href="{{ route('technician.incidents.index') }}">
                                    <i class="fas fa-wrench"></i>
                                    Assigned Work
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif
            </ul>
        </div>
        
        <div class="sidebar-section">
            <div class="sidebar-heading">Quick Links</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('help.*') ? 'active' : '' }}" 
                       href="{{ route('help.index') }}">
                        <i class="fas fa-question-circle"></i>
                        Help & Support
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-cog"></i>
                        Settings
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Sidebar Manager -->

    <script>
    window.currentUserRole = @json(strtolower(auth()->user()->role)); // 'driver' | 'admin' | 'technician'
    </script>

    <script src="{{ asset('js/sidebar-manager.js') }}"></script>
    
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.querySelector('.sidebar-toggle');
            
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });

        /*
        // Improved Sidebar Dropdown Management
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all dropdowns properly
            const dropdownToggles = document.querySelectorAll('.nav-link-collapse');
            const collapseInstances = new Map();
            
            // Initialize Bootstrap Collapse instances
            dropdownToggles.forEach(function(toggle) {
                const targetId = toggle.getAttribute('href');
                const target = document.querySelector(targetId);
                
                if (target) {
                    // Prevent Bootstrap from auto-initializing
                    toggle.removeAttribute('data-bs-toggle');
                    
                    // Create collapse instance manually
                    const bsCollapse = new bootstrap.Collapse(target, {
                        toggle: false
                    });
                    collapseInstances.set(targetId, bsCollapse);
                    
                    // Check if this dropdown should be open
                    const savedState = localStorage.getItem('dropdown_' + targetId);
                    const hasActiveChild = target.querySelector('.nav-link.active') !== null;
                    
                    // Open if saved as open OR contains active link
                    if (savedState === 'open' || (hasActiveChild && savedState !== 'closed')) {
                        bsCollapse.show();
                        toggle.setAttribute('aria-expanded', 'true');
                        // Save state if opened due to active child
                        if (hasActiveChild && !savedState) {
                            localStorage.setItem('dropdown_' + targetId, 'open');
                        }
                    } else {
                        toggle.setAttribute('aria-expanded', 'false');
                    }
                }
            });
            */
            
            // Handle dropdown toggle clicks
            dropdownToggles.forEach(function(toggle) {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const targetId = toggle.getAttribute('href');
                    const target = document.querySelector(targetId);
                    const bsCollapse = collapseInstances.get(targetId);
                    
                    if (!target || !bsCollapse) return;
                    
                    // Check current state
                    const isCurrentlyOpen = target.classList.contains('show');
                    
                    if (isCurrentlyOpen) {
                        // Close dropdown
                        bsCollapse.hide();
                        toggle.setAttribute('aria-expanded', 'false');
                        localStorage.setItem('dropdown_' + targetId, 'closed');
                    } else {
                        // Open dropdown
                        bsCollapse.show();
                        toggle.setAttribute('aria-expanded', 'true');
                        localStorage.setItem('dropdown_' + targetId, 'open');
                    }
                });
            });
            
            // Listen for Bootstrap collapse events to update chevron
            document.querySelectorAll('.collapse').forEach(function(collapseEl) {
                collapseEl.addEventListener('shown.bs.collapse', function() {
                    const toggle = document.querySelector('[href="#' + collapseEl.id + '"]');
                    if (toggle) {
                        toggle.setAttribute('aria-expanded', 'true');
                        const chevron = toggle.querySelector('.fa-chevron-down');
                        if (chevron) chevron.style.transform = 'rotate(180deg)';
                    }
                });
                
                collapseEl.addEventListener('hidden.bs.collapse', function() {
                    const toggle = document.querySelector('[href="#' + collapseEl.id + '"]');
                    if (toggle) {
                        toggle.setAttribute('aria-expanded', 'false');
                        const chevron = toggle.querySelector('.fa-chevron-down');
                        if (chevron) chevron.style.transform = 'rotate(0deg)';
                    }
                });
            });
        });
        
        // Clear old localStorage keys (migration from old system)
        document.addEventListener('DOMContentLoaded', function() {
            // Clean up old keys
            const keys = Object.keys(localStorage);
            keys.forEach(key => {
                if (key.startsWith('sidebar_')) {
                    const newKey = key.replace('sidebar_', 'dropdown_');
                    const value = localStorage.getItem(key);
                    localStorage.setItem(newKey, value);
                    localStorage.removeItem(key);
                }
            });
        });

        // Prevent dropdown from closing when clicking inside it
        document.addEventListener('click', function(e) {
            if (e.target.closest('.collapse-menu')) {
                e.stopPropagation();
            }
        });

        // Handle window resize to maintain dropdown states
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                // Re-apply saved states after resize
                const dropdownToggles = document.querySelectorAll('.nav-link-collapse');
                dropdownToggles.forEach(function(toggle) {
                    const targetId = toggle.getAttribute('href');
                    const savedState = localStorage.getItem('sidebar_' + targetId);
                    const target = document.querySelector(targetId);
                    
                    if (savedState === 'open' && target && !target.classList.contains('show')) {
                        target.classList.add('show');
                        toggle.setAttribute('aria-expanded', 'true');
                    }
                });
            }, 250);
        });
        
        // Dark Mode Toggle
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            const themeIcon = document.getElementById('theme-icon');
            
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            // Update icon
            if (newTheme === 'dark') {
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
            } else {
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
            }
        }
        
        // Load saved theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            const themeIcon = document.getElementById('theme-icon');
            
            document.documentElement.setAttribute('data-theme', savedTheme);
            
            // Set correct icon
            if (savedTheme === 'dark') {
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
            } else {
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>