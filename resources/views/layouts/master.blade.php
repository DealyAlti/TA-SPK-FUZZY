<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>FuzzyTsukamoto | @yield('title')</title>
    <link rel="icon" href="data:,">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{ asset('/AdminLTE-2/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/AdminLTE-2/bower_components/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/AdminLTE-2/dist/css/AdminLTE.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/AdminLTE-2/dist/css/skins/_all-skins.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/AdminLTE-2/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* ========================================
           🎨 MODERN PREMIUM DESIGN SYSTEM
        ======================================== */
        
        /* Color Variables - Elegant Red Palette */
        :root {
            --primary-red: #DC2626;
            --dark-red: #991B1B;
            --light-red: #FEE2E2;
            --medium-red: #EF4444;
            --text-red: #7F1D1D;
            --accent-red: #B91C1C;
            
            /* Neutral Colors */
            --gray-50: #F9FAFB;
            --gray-100: #F3F4F6;
            --gray-200: #E5E7EB;
            --gray-300: #D1D5DB;
            --gray-400: #9CA3AF;
            --gray-500: #6B7280;
            --gray-600: #4B5563;
            --gray-700: #374151;
            --gray-800: #1F2937;
            --gray-900: #111827;
            
            /* Shadows */
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.07);
            --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 25px rgba(0, 0, 0, 0.15);
            
            /* Border Radius */
            --radius-sm: 6px;
            --radius-md: 10px;
            --radius-lg: 16px;
            --radius-xl: 20px;
        }

        /* Global Reset & Base */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif !important;
            background: linear-gradient(135deg, var(--gray-50) 0%, var(--gray-100) 100%);
            color: var(--gray-800);
            line-height: 1.6;
        }

        /* ========================================
           🎯 HEADER - Premium Navigation Bar
        ======================================== */
        .main-header .navbar {
            background: linear-gradient(135deg, var(--dark-red) 0%, var(--accent-red) 100%) !important;
            border: none !important;
            height: 70px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(10px);
            position: relative;
        }

        .main-header .navbar::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        }

        .main-header .navbar .nav > li > a {
            color: white !important;
            height: 70px;
            line-height: 70px;
            padding: 0 25px !important;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .main-header .navbar .nav > li > a::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 3px;
            background: white;
            border-radius: 3px 3px 0 0;
            transition: width 0.3s ease;
        }

        .main-header .navbar .nav > li > a:hover::before {
            width: 70%;
        }

        .main-header .navbar .nav > li > a:hover {
            background: rgba(255, 255, 255, 0.1) !important;
        }

        .main-header .logo {
            background: linear-gradient(135deg, var(--dark-red) 0%, var(--text-red) 100%) !important;
            color: white !important;
            height: 70px;
            line-height: 70px;
            font-size: 20px;
            font-weight: 700;
            text-align: center;
            letter-spacing: 0.5px;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }

        .main-header .logo:hover {
            background: linear-gradient(135deg, var(--text-red) 0%, var(--dark-red) 100%) !important;
        }

        /* ========================================
           📱 SIDEBAR - Modern Card Style
        ======================================== */
        .main-sidebar {
            background: white !important;
            border-right: none;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.05);
            padding-top: 70px;
        }

        .sidebar-menu {
            padding: 15px 0;
        }

        .sidebar-menu > li {
            margin: 4px 12px;
            border-radius: var(--radius-md);
            transition: all 0.3s ease;
        }

        .sidebar-menu > li > a {
            color: var(--gray-600) !important;
            padding: 14px 20px !important;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: var(--radius-md);
            border-left: 3px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .sidebar-menu > li > a::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(180deg, var(--primary-red), var(--accent-red));
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .sidebar-menu > li > a:hover {
            background: linear-gradient(135deg, var(--light-red) 0%, rgba(254, 226, 226, 0.5) 100%) !important;
            color: var(--text-red) !important;
            transform: translateX(5px);
            box-shadow: var(--shadow-sm);
        }

        .sidebar-menu > li > a:hover::before {
            transform: scaleY(1);
        }

        .sidebar-menu > li.active > a {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--accent-red) 100%) !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
            transform: translateX(5px);
        }

        .sidebar-menu > li.active > a::before {
            transform: scaleY(1);
            background: white;
        }

        .sidebar-menu > li > a > .fa {
            color: inherit !important;
            width: 24px;
            margin-right: 12px;
            font-size: 16px;
            text-align: center;
        }

        /* Sidebar Section Headers */
        .sidebar-menu .header {
            background: transparent !important;
            color: var(--gray-400) !important;
            padding: 12px 20px 8px !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 1px !important;
            margin: 15px 12px 5px !important;
        }

        /* Sidebar Treeview */
        .sidebar-menu .treeview-menu {
            background: var(--gray-50) !important;
            border-radius: var(--radius-md);
            margin: 5px 12px;
            padding: 5px 0;
        }

        .sidebar-menu .treeview-menu > li > a {
            background: transparent !important;
            color: var(--gray-600) !important;
            padding: 10px 20px 10px 50px !important;
            font-size: 13px;
            transition: all 0.2s ease;
        }

        .sidebar-menu .treeview-menu > li > a:hover {
            background: white !important;
            color: var(--text-red) !important;
            transform: translateX(3px);
        }

        .sidebar-menu .treeview-menu > li.active > a {
            background: white !important;
            color: var(--primary-red) !important;
            font-weight: 600;
            border-left: 3px solid var(--primary-red);
        }

        /* ===== OVERRIDE ACTIVE MENU: SOFT (putih + garis kiri) ===== */
        .sidebar-menu > li.active > a,
        .sidebar-menu > li.menu-open > a {
            background: #fff !important;
            color: var(--primary-red) !important;
            box-shadow: none !important;
            transform: none !important;

            border-left: 4px solid var(--primary-red) !important;
        }

        /* icon ikut merah */
        .sidebar-menu > li.active > a > .fa,
        .sidebar-menu > li.menu-open > a > .fa {
            color: var(--primary-red) !important;
        }

        /* efek garis kiri (yang ::before kamu) matikan biar nggak dobel */
        .sidebar-menu > li.active > a::before,
        .sidebar-menu > li.menu-open > a::before {
            transform: none !important;
            background: transparent !important;
        }

        /* hover tetap soft */
        .sidebar-menu > li > a:hover {
            background: linear-gradient(135deg, var(--light-red) 0%, rgba(254,226,226,0.5) 100%) !important;
            color: var(--text-red) !important;
            transform: translateX(5px);
            box-shadow: var(--shadow-sm);
        }

        /* ========================================
           📄 CONTENT WRAPPER - Clean Layout
        ======================================== */
        .content-wrapper {
            background: var(--gray-50) !important;
            margin-left: 230px;
            padding-top: 0;
            min-height: calc(100vh - 70px);
        }

        .content-header {
            background: white;
            padding: 30px 35px;
            margin: 0;
            border-bottom: 1px solid var(--gray-200);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        }

        .content-header h1 {
            color: var(--gray-900);
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 8px 0;
            letter-spacing: -0.5px;
        }

        /* Breadcrumb */
        .breadcrumb {
            background: transparent !important;
            padding: 0 !important;
            margin: 0 !important;
            font-size: 13px;
        }

        .breadcrumb > li {
            color: var(--gray-500);
        }

        .breadcrumb > li > a {
            color: var(--primary-red);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .breadcrumb > li > a:hover {
            color: var(--accent-red);
        }

        .breadcrumb > li + li:before {
            content: "›";
            color: var(--gray-300);
            padding: 0 10px;
            font-size: 16px;
        }

        /* Main Content */
        .content {
            padding: 30px 35px;
        }

        /* ========================================
           🎴 CARDS/BOXES - Modern Design
        ======================================== */
        .box {
            background: white;
            border: none !important;
            border-radius: var(--radius-lg) !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08) !important;
            margin-bottom: 30px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .box:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12) !important;
            transform: translateY(-2px);
        }

        .box-header {
            background: linear-gradient(135deg, var(--gray-50) 0%, white 100%) !important;
            border-bottom: 2px solid var(--gray-100) !important;
            padding: 20px 25px !important;
            position: relative;
        }

        .box-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 25px;
            right: 25px;
            height: 2px;
            background: linear-gradient(90deg, var(--primary-red), transparent);
        }

        .box-header .box-title {
            color: var(--gray-900) !important;
            font-size: 18px !important;
            font-weight: 700 !important;
            margin: 0 !important;
            letter-spacing: -0.3px;
        }

        .box-body {
            padding: 25px !important;
        }

        /* ========================================
           🔘 BUTTONS - Modern Style
        ======================================== */
        .btn {
            border-radius: var(--radius-sm) !important;
            font-weight: 600 !important;
            padding: 10px 20px !important;
            font-size: 14px !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            border: none !important;
            letter-spacing: 0.3px;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-success {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--accent-red) 100%) !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(220, 38, 38, 0.4);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--text-red) 0%, var(--dark-red) 100%) !important;
            box-shadow: 0 4px 12px rgba(127, 29, 29, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(127, 29, 29, 0.4);
        }

        .btn-warning {
            background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%) !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        .btn-danger {
            background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%) !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .btn-info {
            background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%) !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .label-success {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--accent-red) 100%) !important;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
        }

        /* ========================================
           📝 FORM ELEMENTS
        ======================================== */
        .form-control {
            border: 2px solid var(--gray-200) !important;
            border-radius: var(--radius-sm) !important;
            padding: 12px 16px !important;
            font-size: 14px !important;
            transition: all 0.3s ease !important;
            background: white;
        }

        .form-control:focus {
            border-color: var(--primary-red) !important;
            box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.1) !important;
            outline: none !important;
            background: white;
        }

        .form-group label {
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 8px;
            font-size: 14px;
        }

        /* ========================================
           📊 TABLES - Modern Data Tables
        ======================================== */
        .table {
            font-size: 14px !important;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table > thead > tr > th {
            background: linear-gradient(135deg, var(--dark-red) 0%, var(--accent-red) 100%) !important;
            color: white !important;
            border: none !important;
            font-weight: 700 !important;
            padding: 16px 14px !important;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        .table > thead > tr > th:first-child {
            border-radius: var(--radius-sm) 0 0 0;
        }

        .table > thead > tr > th:last-child {
            border-radius: 0 var(--radius-sm) 0 0;
        }

        .table > tbody > tr {
            transition: all 0.2s ease;
        }

        .table > tbody > tr:hover {
            background: var(--light-red) !important;
            transform: scale(1.01);
        }

        .table > tbody > tr > td {
            padding: 14px !important;
            border-top: 1px solid var(--gray-100) !important;
            vertical-align: middle !important;
        }

        .table-striped > tbody > tr:nth-of-type(odd) {
            background: var(--gray-50) !important;
        }

        /* DataTables Customization */
        .dataTables_wrapper {
            font-size: 14px !important;
        }

        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border: 2px solid var(--gray-200) !important;
            border-radius: var(--radius-sm) !important;
            padding: 8px 12px !important;
            transition: all 0.3s ease !important;
        }

        .dataTables_wrapper .dataTables_length select:focus,
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: var(--primary-red) !important;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1) !important;
            outline: none !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--accent-red) 100%) !important;
            color: white !important;
            border: none !important;
            border-radius: var(--radius-sm) !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--light-red) !important;
            color: var(--text-red) !important;
            border: none !important;
        }

        /* ===============================
        `   GLOBAL: BESARIN UI DATATABLES
        (dropdown + search + text)
        =============================== */

        /* label: "Show ... entries" & "Search:" */
        .dataTables_wrapper .dataTables_length label,
        .dataTables_wrapper .dataTables_filter label {
            font-size: 15px !important;
            font-weight: 600 !important;
            color: var(--gray-700) !important;
        }

        /* dropdown "Show entries" */
        .dataTables_wrapper .dataTables_length select {
            height: 44px !important;
            min-width: 84px !important;
            padding: 6px 14px !important;
            font-size: 14px !important;
            border-radius: 12px !important;
            border: 2px solid var(--gray-200) !important;
            background: #fff !important;
            box-shadow: 0 2px 6px rgba(0,0,0,.08);
        }

        /* search input */
        .dataTables_wrapper .dataTables_filter input {
            height: 44px !important;
            width: 260px !important;  /* bisa kamu gedein/kecilin */
            padding: 10px 14px !important;
            font-size: 14px !important;
            border-radius: 12px !important;
            border: 2px solid var(--gray-200) !important;
            background: #fff !important;
            box-shadow: 0 2px 6px rgba(0,0,0,.08);
        }

        /* fokus merah */
        .dataTables_wrapper .dataTables_length select:focus,
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: var(--accent-red) !important;
            box-shadow: 0 0 0 4px rgba(183,28,28,.15) !important;
            outline: none !important;
        }

        /* rapihin posisi biar sejajar */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            padding: 8px 0 !important;
        }
        .dataTables_wrapper .dataTables_filter {
            text-align: right !important;
        }`


        /* ========================================
           📦 INFO BOXES - Dashboard Cards
        ======================================== */
        .info-boxes-container {
            display: flex;
            flex-wrap: wrap;
            gap: 25px;
            margin-bottom: 30px;
        }

        .info-box {
            flex: 1;
            min-width: 280px;
            height: 130px !important;
            border-radius: var(--radius-lg) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
            border: none !important;
            margin-bottom: 0 !important;
            display: flex !important;
            align-items: center !important;
            overflow: hidden;
            position: relative;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
        }

        .info-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-red), var(--accent-red));
        }

        .info-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15) !important;
        }

        .info-box-icon {
            width: 100px !important;
            height: 130px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 42px !important;
            color: white !important;
            position: relative;
            overflow: hidden;
        }

        .info-box-icon::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(45deg);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0%, 100% { transform: translateX(-100%) rotate(45deg); }
            50% { transform: translateX(100%) rotate(45deg); }
        }

        .info-box-content {
            flex: 1 !important;
            padding: 15px 20px !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            background: white !important;
            height: 130px !important;
        }

        .info-box-number {
            font-size: 32px !important;
            font-weight: 800 !important;
            color: var(--gray-900) !important;
            margin: 0 0 5px 0 !important;
            letter-spacing: -1px;
        }

        .info-box-text {
            font-size: 13px !important;
            font-weight: 600 !important;
            color: var(--gray-600) !important;
            margin: 0 0 8px 0 !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-box-more {
            font-size: 12px !important;
            color: var(--primary-red) !important;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .info-box-more:hover {
            color: var(--accent-red) !important;
            text-decoration: none !important;
            transform: translateX(3px);
            display: inline-block;
        }

        /* Info Box Color Schemes */
        .info-box.bg-aqua .info-box-icon {
            background: linear-gradient(135deg, #06B6D4 0%, #0891B2 100%) !important;
        }

        .info-box.bg-blue .info-box-icon {
            background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%) !important;
        }

        .info-box.bg-purple .info-box-icon {
            background: linear-gradient(135deg, #A855F7 0%, #9333EA 100%) !important;
        }

        .info-box.bg-red .info-box-icon {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--accent-red) 100%) !important;
        }

        .info-box.bg-green .info-box-icon {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%) !important;
        }

        .info-box.bg-yellow .info-box-icon {
            background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%) !important;
        }

        /* ========================================
           🔔 ALERTS - Modern Notifications
        ======================================== */
        .alert {
            border-radius: var(--radius-md) !important;
            border: none !important;
            padding: 16px 20px !important;
            border-left: 4px solid !important;
            box-shadow: var(--shadow-sm);
            font-weight: 500;
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(220, 38, 38, 0.1) 0%, rgba(220, 38, 38, 0.05) 100%) !important;
            color: var(--text-red) !important;
            border-left-color: var(--primary-red) !important;
        }

        .alert-info {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%) !important;
            color: #1E40AF !important;
            border-left-color: #3B82F6 !important;
        }

        .alert-warning {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(245, 158, 11, 0.05) 100%) !important;
            color: #92400E !important;
            border-left-color: #F59E0B !important;
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(239, 68, 68, 0.05) 100%) !important;
            color: #B91C1C !important;
            border-left-color: #EF4444 !important;
        }

        /* ========================================
           🔽 MODAL - Modern Dialog
        ======================================== */
        .modal-content {
            border-radius: var(--radius-lg) !important;
            border: none !important;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3) !important;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--gray-50) 0%, white 100%);
            border-bottom: 2px solid var(--gray-100);
            padding: 20px 25px;
            border-radius: var(--radius-lg) var(--radius-lg) 0 0 !important;
        }

        .modal-title {
            font-weight: 700;
            color: var(--gray-900);
            font-size: 20px;
        }

        .modal-body {
            padding: 25px;
        }

        .modal-footer {
            background: var(--gray-50);
            border-top: 1px solid var(--gray-200);
            padding: 15px 25px;
            border-radius: 0 0 var(--radius-lg) var(--radius-lg) !important;
        }

        /* ========================================
           👣 FOOTER
        ======================================== */
        .main-footer {
            background: white !important;
            border-top: 2px solid var(--gray-100) !important;
            color: var(--gray-600) !important;
            padding: 20px 35px !important;
            margin-left: 230px;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.03);
        }

        /* ========================================
           🔘 FLOATING NOTIFICATION BUTTON
        ======================================== */
        .floating-notif-btn {
            position: fixed;
            bottom: 35px;
            right: 35px;
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--accent-red) 100%) !important;
            color: white;
            border: none;
            border-radius: 50%;
            box-shadow: 0 8px 25px rgba(220, 38, 38, 0.4);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        .floating-notif-btn:hover {
            transform: scale(1.15) rotate(10deg);
            box-shadow: 0 12px 35px rgba(220, 38, 38, 0.5);
        }

        .floating-notif-btn::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: inherit;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%,100% {transform: scale(1);opacity: 1;}
            50% {transform: scale(1.3);opacity: 0;}
        }
        /* ========================================
        📱 RESPONSIVE DESIGN
        ======================================== */
        @media (max-width: 768px) {
            .content-wrapper {
                margin-left: 0;
            }
            
            .main-footer {
                margin-left: 0;
            }
            
            .content {
                padding: 20px 15px;
            }
            
            .content-header {
                padding: 20px 15px;
            }

            .content-header h1 {
                font-size: 22px;
            }

            .info-boxes-container {
                flex-direction: column;
                gap: 15px;
            }
            
            .info-box {
                min-width: 100%;
                height: 110px !important;
            }
            
            .info-box-icon {
                width: 80px !important;
                height: 110px !important;
                font-size: 32px !important;
            }
            
            .info-box-content {
                height: 110px !important;
                padding: 15px !important;
            }
            
            .info-box-number {
                font-size: 26px !important;
            }

            .floating-notif-btn {
                width: 60px;
                height: 60px;
                bottom: 25px;
                right: 25px;
                font-size: 24px;
            }
        }

        /* ========================================
        ✨ ANIMATIONS
        ======================================== */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .box, .info-box {
            animation: fadeIn 0.5s ease-out;
        }

        /* ========================================
        🎯 UTILITY CLASSES
        ======================================== */
        .text-gradient {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--accent-red) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .card-hover-effect {
            transition: all 0.3s ease;
        }

        .card-hover-effect:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        /* Perbesar tinggi input dan dropdown dalam modal */
        .modal .form-control {
            height: 45px !important;
            padding: 10px 16px !important;
            font-size: 15px !important;
            line-height: 1.4 !important;
        }

        /* Untuk select agar option terlihat normal */
        .modal select.form-control {
            height: 45px !important;
        }

        /* Perbesar area klik dropdown caret */
        .modal .form-control::-ms-expand {
            padding: 10px !important;
        }

        /* Tinggi wrapper form-group dalam modal */
        .modal .form-group {
            margin-bottom: 20px !important;
        }

        /* ===========================
        FIX MODAL SHIFT KANAN-KIRI
        (Bootstrap 3 + AdminLTE 2)
        =========================== */
        html {
            overflow-y: scroll;
        }

        body.modal-open {
            padding-right: 0 !important;
        }

        .modal-open .wrapper,
        .modal-open .content-wrapper,
        .modal-open .main-header,
        .modal-open .main-sidebar,
        .modal-open .main-footer {
            padding-right: 0 !important;
        }

        .modal {
            overflow-y: auto;
        }

        /* ===============================
        GLOBAL: TABLE TEXT CENTER
        (AdminLTE + Bootstrap 3)
        =============================== */
        .table > thead > tr > th,
        .table > tbody > tr > td,
        .table > tfoot > tr > td {
            text-align: center !important;
            vertical-align: middle !important;
        }

        /* Kalau ada link/tombol di dalam cell, biar ikut rapi tengah */
        .table td a,
        .table td .btn,
        .table td .label,
        .table td span {
            margin-left: auto;
            margin-right: auto;
        }

        /* ===============================
        FIX: SIDEBAR DIAM (TIDAK IKUT SCROLL)
        AdminLTE 2 + header 70px
        ================================= */

        /* 1) bikin sidebar fixed */
        .main-sidebar{
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            height: 100vh !important;
            overflow-y: auto !important;
            z-index: 1030; /* di atas konten */
            padding-top: 70px; /* karena header kamu 70px */
        }

        /* 2) pastikan header tetap di atas sidebar */
        .main-header{
            position: fixed !important;
            top: 0; left: 0; right: 0;
            z-index: 1040;
        }

        /* 3) geser konten & footer agar tidak ketimpa sidebar */
        .content-wrapper,
        .main-footer{
            margin-left: 230px !important; /* lebar sidebar adminlte */
        }

        /* 4) geser konten ke bawah karena header fixed 70px */
        .content-wrapper{
            margin-top: 70px !important;
        }

        /* 5) kalau logo juga fixed (biar sejajar header) */
        .main-header .logo{
            position: fixed !important;
            top: 0; left: 0;
            z-index: 1041;
        }

        /* 6) wrapper jangan bikin offset aneh */
        .wrapper{
            overflow: visible !important;
        }

        /* ===============================
        CONTENT HEADER: LEBIH MODERN
        (judul + breadcrumb)
        =============================== */
        .content-header{
            background: #fff !important;
            padding: 22px 28px !important;
            border-bottom: 1px solid var(--gray-200) !important;
            box-shadow: 0 8px 24px rgba(0,0,0,.06) !important;
            position: relative;
        }

        .content-header::after{
            display: none !important;
        }

        /* Judul lebih keren */
        .content-header > h1{
            margin: 0 !important;
            font-size: 34px !important;
            font-weight: 900 !important;
            letter-spacing: -0.6px;
            color: var(--gray-900) !important;
        }

        /* Breadcrumb jadi pill, rapih kanan */
        .content-header > .breadcrumb{
            top: 50% !important;
            transform: translateY(-50%);
            right: 28px !important;
            background: #fff !important;
            padding: 10px 14px !important;
            border-radius: 999px !important;
            border: 1px solid var(--gray-200) !important;
            box-shadow: 0 6px 16px rgba(0,0,0,.06);
            font-size: 13px !important;
        }

        /* item breadcrumb */
        .content-header > .breadcrumb > li{
            color: var(--gray-600) !important;
            font-weight: 600;
        }

        /* link breadcrumb */
        .content-header > .breadcrumb > li > a{
            color: var(--accent-red) !important;
            font-weight: 800;
        }

        /* separator jadi icon kecil */
        .content-header > .breadcrumb > li + li:before{
            content: "›" !important;
            color: var(--gray-300) !important;
            padding: 0 10px !important;
            font-size: 16px !important;
        }

        /* responsive biar breadcrumb turun rapi */
        @media (max-width: 768px){
            .content-header{
                padding: 18px 16px !important;
            }
            .content-header:after{
                left: 16px; right: 16px;
            }
            .content-header > h1{
                font-size: 24px !important;
            }
            .content-header > .breadcrumb{
                position: static !important;
                transform: none !important;
                margin-top: 10px !important;
                display: inline-flex;
            }
        }

        

    </style>

@stack('css')
</head>
<body class="hold-transition skin-green sidebar-mini">
    <div class="wrapper">
        @includeIf('layouts.header')

    @includeIf('layouts.sidebar')

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Content Header -->
        <section class="content-header">
            <h1>@yield('title')</h1>
            <ol class="breadcrumb">
                @section('breadcrumb')
                    <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                @show
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            @yield('content')
        </section>
    </div>

    @includeIf('layouts.footer')
</div>

<!-- jQuery 3 -->
<script src="{{ asset('AdminLTE-2/bower_components/jquery/dist/jquery.min.js') }}"></script>
<!-- Bootstrap 3.3.7 -->
<script src="{{ asset('AdminLTE-2/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<!-- Moment -->
<script src="{{ asset('AdminLTE-2/bower_components/moment/min/moment.min.js') }}"></script>
<!-- DataTables -->
<script src="{{ asset('AdminLTE-2/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('AdminLTE-2/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('AdminLTE-2/dist/js/adminlte.min.js') }}"></script>
<!-- Validator -->
<script src="{{ asset('js/validator.min.js') }}"></script>

@stack('scripts')
</body>
</html>