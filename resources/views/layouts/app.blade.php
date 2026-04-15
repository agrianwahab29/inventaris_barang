<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Aplikasi Inventaris Barang')</title>
    
    <!-- Favicon - Logo Tut Wuri Handayani -->
    <!-- PNG Favicon untuk browser modern (tidak stretch) -->
    <link rel="icon" type="image/png" href="{{ asset('image/tut-wuri-handayani.png') }}?v=3">
    <!-- ICO Fallback untuk browser lama -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=3">
    <!-- SVG untuk high-res display -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('image/tut-wuri-handayani.svg') }}?v=3">
    <!-- Apple Touch Icon -->
    <link rel="apple-touch-icon" href="{{ asset('image/tut-wuri-handayani.png') }}?v=3">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('image/tut-wuri-handayani.png') }}?v=3">
    
    <!-- Force favicon refresh -->
    <meta name="theme-color" content="#1e1b4b">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            --primary-color: white;
            --primary-dark: #4338ca;
            --secondary-color: #7c3aed;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #3b82f6;
            --sidebar-width: 260px;
        }
        
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        
        html {
            font-size: 14px;
            scroll-behavior: smooth;
        }
        
        body {
            background-color: #f8fafc;
            color: #1e293b;
            overflow-x: hidden;
            font-size: 0.875rem;
        }

        /* Enhanced Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes fadeInLeft {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        @keyframes fadeInRight {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-up { animation: fadeInUp 0.4s ease-out forwards; }
        .animate-fade-left { animation: fadeInLeft 0.4s ease-out forwards; }
        .animate-fade-right { animation: fadeInRight 0.4s ease-out forwards; }
        .animate-scale { animation: scaleIn 0.3s ease-out forwards; }
        .animate-delay-1 { animation-delay: 0.05s; opacity: 0; }
        .animate-delay-2 { animation-delay: 0.1s; opacity: 0; }
        .animate-delay-3 { animation-delay: 0.15s; opacity: 0; }
        .animate-delay-4 { animation-delay: 0.2s; opacity: 0; }
        
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #1e1b4b 0%, #312e81 100%);
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 24px rgba(0,0,0,0.1);
        }
        
        .sidebar-brand {
            padding: 16px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-brand h4 {
            color: white;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.125rem;
        }
        
        .sidebar-brand small {
            color: rgba(255,255,255,0.6);
            font-size: 0.6rem;
            display: block;
            margin-top: 2px;
            margin-left: 34px;
        }
        
        .nav-section {
            padding: 12px 16px 6px;
            color: rgba(255,255,255,0.4);
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 10px 16px;
            margin: 2px 12px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.8125rem;
        }
        
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(5px);
        }
        
        .sidebar .nav-link.active {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.4);
        }
        
        .sidebar .nav-link i {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }
        
        /* Admin Menu Styling - Compact & Non-Disruptive */
        .nav-link-admin {
            position: relative;
            background: rgba(239, 68, 68, 0.06) !important;
            border: 1px solid rgba(239, 68, 68, 0.12) !important;
            margin: 2px 12px !important;
            padding: 8px 12px !important;
            min-height: 0;
        }
        
        .nav-link-admin:hover {
            background: rgba(239, 68, 68, 0.12) !important;
            border-color: rgba(239, 68, 68, 0.2) !important;
            transform: translateX(3px);
        }
        
        .nav-link-admin.active {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
            border-color: rgba(239, 68, 68, 0.4) !important;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
            transform: translateX(0);
        }
        
        .nav-link-admin .nav-icon-wrapper {
            width: 24px;
            height: 24px;
            background: rgba(239, 68, 68, 0.15);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 8px;
            flex-shrink: 0;
        }
        
        .nav-link-admin.active .nav-icon-wrapper {
            background: rgba(255, 255, 255, 0.2);
        }
        
        .nav-link-admin .nav-icon-wrapper i {
            font-size: 11px;
            color: #fca5a5;
        }
        
        .nav-link-admin.active .nav-icon-wrapper i {
            color: white;
        }
        
        .nav-link-admin .nav-text {
            flex: 1;
            font-weight: 500;
            font-size: 0.8125rem;
            line-height: 1.2;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .admin-badge {
            font-size: 0.6rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 2px 6px;
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border-radius: 12px;
            border: 1px solid rgba(239, 68, 68, 0.25);
            flex-shrink: 0;
            line-height: 1;
        }
        
        .nav-link-admin.active .admin-badge {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border-color: rgba(255, 255, 255, 0.3);
        }
        
        .nav-section i {
            font-size: 10px;
            color: rgba(239, 68, 68, 0.8);
        }
        
        /* Nav Menu Container - flex: 1 untuk mengisi ruang */
        .nav-menu-container {
            flex: 1 1 auto;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 0.5rem 0 80px 0;
            min-height: 0;
        }
        
        /* Custom scrollbar for nav menu */
        .nav-menu-container::-webkit-scrollbar {
            width: 6px;
        }
        
        .nav-menu-container::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.05);
        }
        
        .nav-menu-container::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 3px;
        }
        
        .nav-menu-container::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .sidebar-footer {
            flex-shrink: 0;
            padding: 8px 12px;
            border-top: 1px solid rgba(255,255,255,0.08);
            background: linear-gradient(180deg, rgba(30, 27, 75, 0.9) 0%, #1e1b4b 100%);
            margin-top: auto;
            min-height: 50px;
        }
        
        /* User Card - Explicit sidebar scope to prevent conflicts */
        .sidebar-footer .user-card {
            background: rgba(255,255,255,0.08) !important;
            border-radius: 8px !important;
            padding: 8px 10px !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            border: none !important;
            box-shadow: none !important;
        }
        
        .sidebar-footer .user-avatar {
            width: 28px !important;
            height: 28px !important;
            border-radius: 50% !important;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-weight: 600 !important;
            font-size: 11px !important;
            flex-shrink: 0 !important;
        }
        
        .sidebar-footer .user-info {
            flex: 1 !important;
            overflow: hidden !important;
            min-width: 0 !important;
        }
        
        .sidebar-footer .user-name {
            color: white !important;
            font-weight: 600 !important;
            font-size: 0.75rem !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            line-height: 1.2 !important;
        }
        
        .sidebar-footer .user-role {
            color: rgba(255,255,255,0.6) !important;
            font-size: 0.625rem !important;
            line-height: 1.2 !important;
        }
        
        .sidebar-footer .logout-btn {
            color: rgba(255,255,255,0.7) !important;
            background: transparent !important;
            border: none !important;
            padding: 4px 6px !important;
            border-radius: 4px !important;
            cursor: pointer !important;
            font-size: 14px !important;
            transition: all 0.3s !important;
        }
        
        .sidebar-footer .logout-btn:hover {
            background: rgba(239, 68, 68, 0.2) !important;
            color: #ef4444 !important;
        }
        
        /* Main Content */
        .main-wrapper {
            margin-left: 260px;
            min-height: 100vh;
            transition: all 0.3s ease;
        }
        
        /* Top Navbar */
        .top-navbar {
            background: white;
            padding: 12px 20px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        
        .page-title {
            font-size: 1.375rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }
        
        .navbar-date-full {
            display: inline;
            font-size: 0.8125rem;
        }
        
        .navbar-date-short {
            display: none;
            font-size: 0.75rem;
        }
        
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
            font-size: 0.75rem;
        }
        
        .breadcrumb-item a {
            color: #64748b;
            text-decoration: none;
        }
        
        .breadcrumb-item.active {
            color: var(--primary-color);
            font-weight: 500;
        }
        
        /* Content Area */
        .content-area {
            padding: 16px 20px;
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
            overflow: hidden;
        }
        
        .card:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        
        /* Buttons */
        .btn {
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 10px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 0.875rem;
            position: relative;
            overflow: hidden;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #5568d3 0%, #6a42a0 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }
        
        .btn-success:hover {
            background: linear-gradient(135deg, #0d9668 0%, #047857 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border: none;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border: none;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        }
        
        .btn-info {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border: none;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 0.75rem;
        }
        
        .btn-lg {
            padding: 10px 20px;
            font-size: 0.9375rem;
        }
        
        /* Form Controls */
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            padding: 8px 12px;
            font-size: 0.8125rem;
            transition: all 0.3s ease;
            height: auto;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        
        .form-select {
            padding-right: 32px;
        }
        
        .form-label {
            font-size: 0.75rem;
            font-weight: 500;
            margin-bottom: 4px;
        }
        
        /* Tables */
        .table {
            font-size: 0.875rem;
        }
        
        .table thead th {
            font-weight: 700;
            color: #475569;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-bottom: 2px solid #e2e8f0;
            padding: 14px 16px;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .table tbody td {
            padding: 12px 16px;
            color: #475569;
            vertical-align: middle;
        }
        
        .table tbody tr {
            transition: all 0.15s ease;
        }
        
        .table tbody tr:hover {
            background-color: #f8fafc;
            transform: scale(1.002);
        }
        
        .table-responsive {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        /* Alerts */
        .alert {
            border: none;
            border-radius: 12px;
            padding: 14px 18px;
            font-size: 0.875rem;
            animation: slideDown 0.4s ease-out;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border-left: 4px solid #10b981;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }
        
        .alert-warning {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            border-left: 4px solid #f59e0b;
        }
        
        .alert-info {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
            border-left: 4px solid #3b82f6;
        }
        
        /* Badges */
        .badge {
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            letter-spacing: 0.025em;
        }
        
        .badge-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
        
        .badge-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }
        
        .badge-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }
        
        .badge-info {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
        }
        
        .badge-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        h4 {
            font-size: 1.125rem;
        }
        
        h5 {
            font-size: 0.9375rem;
        }
        
        h6 {
            font-size: 0.8125rem;
        }
        
        p, span, div, a, li, td, th {
            font-size: 0.8125rem;
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.5s ease;
        }
        
        /* Mobile Toggle */
        .mobile-toggle {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1001;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border: none;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.4);
            font-size: 18px;
        }
        
        /* Responsive */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                width: 220px;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-wrapper {
                margin-left: 0;
            }
            
            .mobile-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 999;
            }
            
            .sidebar-overlay.show {
                display: block;
            }
            
            .top-navbar {
                padding: 10px 12px;
            }
            
            .content-area {
                padding: 12px;
            }
        }
        
        /* Tablet breakpoint */
        @media (max-width: 767.98px) {
            .top-navbar {
                padding: 10px 12px;
            }
            
            .page-title {
                font-size: 1.125rem;
            }
            
            .content-area {
                padding: 12px;
            }
            
            .breadcrumb {
                font-size: 0.65rem;
            }
            
            /* Hide full date on tablet, show short format */
            .navbar-date-full {
                display: none;
            }
            .navbar-date-short {
                display: inline;
            }
        }
        
        /* Mobile breakpoint */
        @media (max-width: 575.98px) {
            .top-navbar {
                padding: 6px 10px;
            }
            
            .page-title {
                font-size: 0.9375rem;
            }
            
            .content-area {
                padding: 6px 8px;
            }
            
            .card {
                border-radius: 12px;
            }
            
            .btn {
                padding: 8px 14px;
                font-size: 0.8125rem;
            }
            
            /* iOS zoom fix - prevent auto-zoom on form inputs */
            .form-control,
            .form-select,
            input[type="text"],
            input[type="number"],
            input[type="date"],
            input[type="email"],
            input[type="password"],
            select,
            textarea {
                font-size: 16px !important;
            }
            
            .table thead th {
                padding: 10px 8px;
                font-size: 0.6875rem;
            }
            
            .table tbody td {
                padding: 8px;
                font-size: 0.75rem;
            }
        }
        
        /* Small phone breakpoint */
        @media (max-width: 374.98px) {
            .page-title {
                font-size: 0.9375rem;
            }
            
            .content-area {
                padding: 6px;
            }
            
            .stat-card {
                padding: 12px;
            }
            
            .btn {
                padding: 6px 10px;
                font-size: 0.75rem;
            }
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* Pagination Styling */
        .pagination {
            gap: 2px;
            margin: 0;
        }
        
        .pagination .page-link {
            border: none;
            padding: 4px 8px;
            font-size: 0.75rem;
            border-radius: 4px;
            color: #64748b;
            background: #f1f5f9;
            margin: 0 2px;
            min-width: 32px;
            text-align: center;
        }
        
        .pagination .page-link:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(79, 70, 229, 0.3);
        }
        
        .pagination .page-item.disabled .page-link {
            background: #f1f5f9;
            color: #cbd5e1;
        }
        
        .pagination .page-link i {
            font-size: 0.625rem;
        }
        
        .pagination-info {
            font-size: 0.75rem;
            color: #64748b;
        }
        
        .pagination-slim {
            display: flex;
            gap: 2px;
            margin: 0;
            align-items: center;
            list-style: none;
            padding: 0;
        }

        .pagination-slim .page-link {
            border: none;
            padding: 4px 8px;
            font-size: 0.75rem;
            border-radius: 4px;
            color: #64748b;
            background: #f8fafc;
            min-width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .pagination-slim .page-link:hover:not(.disabled) {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: translateY(-1px);
        }

        .pagination-slim .page-item.active .page-link {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            box-shadow: 0 2px 6px rgba(79, 70, 229, 0.25);
            font-weight: 600;
        }

        .pagination-slim .page-item.disabled .page-link {
            background: #f8fafc;
            color: #cbd5e1;
            cursor: not-allowed;
        }

        .pagination-slim .page-link i {
            font-size: 0.6rem;
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
    
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-brand">
            <h4>
                <i class="fas fa-boxes"></i>
                Inventaris
            </h4>
            <small>Sistem Manajemen ATK</small>
        </div>
        
        <div class="nav-menu-container">
            <div class="nav-section">Data Umum</div>
            
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="fas fa-home"></i>
                Dashboard
            </a>
            
            <a class="nav-link {{ request()->routeIs('barang.*') ? 'active' : '' }}" href="{{ route('barang.index') }}">
                <i class="fas fa-box"></i>
                Barang
            </a>
            
            <a class="nav-link {{ request()->routeIs('ruangan.*') ? 'active' : '' }}" href="{{ route('ruangan.index') }}">
                <i class="fas fa-door-open"></i>
                Ruangan
            </a>
            
            <div class="nav-section">Transaksi</div>
            
            <a class="nav-link {{ request()->routeIs('transaksi.create') ? 'active' : '' }}" href="{{ route('transaksi.create') }}">
                <i class="fas fa-plus-circle"></i>
                Barang Masuk/Keluar
            </a>
            
            <a class="nav-link {{ request()->routeIs('transaksi.index') ? 'active' : '' }}" href="{{ route('transaksi.index') }}">
                <i class="fas fa-history"></i>
                Riwayat
            </a>
            
            <a class="nav-link {{ request()->routeIs('quarterly-stock.*') ? 'active' : '' }}" href="{{ route('quarterly-stock.index') }}">
                <i class="fas fa-calendar-check"></i>
                Opname Triwulan
            </a>
            
            <a class="nav-link {{ request()->routeIs('surat-tanda-terima.*') ? 'active' : '' }}" href="{{ route('surat-tanda-terima.index') }}">
                <i class="fas fa-file-signature"></i>
                Surat Tanda Terima
            </a>
            
            <a class="nav-link {{ request()->routeIs('berkas-transaksi.*') ? 'active' : '' }}" href="{{ route('berkas-transaksi.index') }}">
                <i class="fas fa-file-pdf"></i>
                Berkas Transaksi
            </a>
            
            @if(Auth::user()->isAdmin())
            <div class="nav-section">
                <i class="fas fa-shield-alt me-1"></i> Admin Panel
            </div>
            
            <a class="nav-link nav-link-admin {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                <div class="nav-icon-wrapper">
                    <i class="fas fa-user-shield"></i>
                </div>
                <span class="nav-text">Manajemen User</span>
                <span class="admin-badge">Admin</span>
            </a>
            @endif
        </div>
        
        <div class="sidebar-footer">
            <div class="user-card">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="user-info">
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-role">{{ ucfirst(Auth::user()->role) }}</div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-btn" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>
    
    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Top Navbar -->
        <div class="top-navbar d-flex justify-content-between align-items-center">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        @yield('breadcrumb')
                    </ol>
                </nav>
                <h1 class="page-title">@yield('page_title', 'Dashboard')</h1>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted navbar-date-full">{{ now()->format('l, d F Y') }}</span>
                <span class="text-muted navbar-date-short" style="display: none;">{{ now()->format('d M Y') }}</span>
            </div>
        </div>
        
        <!-- Content Area -->
        <div class="content-area">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show animate-fade-in mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show animate-fade-in mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @yield('content')
        </div>
    </div>
    
    <!-- Mobile Toggle Button -->
    <button class="mobile-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('show');
            document.querySelector('.sidebar-overlay').classList.toggle('show');
        }
        
        // Auto hide alert after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
    
    @yield('scripts')
</body>
</html>
