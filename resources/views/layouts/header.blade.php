<header class="main-header">
    @php
        // Mapping level user
        $roles = [
            0 => 'Owner',
            1 => 'Kepala Gudang',
            2 => 'Kasir',
        ];

        $level = auth()->user()->level ?? null;
    @endphp
    <!-- Logo -->
    <a href="{{ url('/') }}" class="logo">
        <span class="logo-lg"><b>Fuzzy</b>Tsukamoto</span>
    </a>

    <!-- Navbar -->
    <nav class="navbar navbar-static-top">

        <!-- User Dropdown -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <li class="dropdown user user-menu" id="user-dropdown">
                    <a href="#" id="user-menu-toggle" class="dropdown-toggle user-dropdown-toggle">
                        <div class="user-avatar-wrapper">
                            <div class="user-avatar">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        </div>
                        <span class="hidden-xs user-name">{{ auth()->user()->name }}</span>
                        <i class="fa fa-angle-down dropdown-icon"></i>
                    </a>
                    <ul class="dropdown-menu user-dropdown-menu" id="user-dropdown-menu">
                        <li class="user-header">
                            <div class="user-avatar-large">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <p class="user-info">
                                <strong>{{ auth()->user()->name }}</strong>
                                <small>{{ auth()->user()->email }}</small>
                            </p>
                        </li>
                        <li class="user-body">
                            <div class="user-stats">
                                <div class="stat-item">
                                    <i class="fa fa-user-circle"></i>
                                    <span>{{ $roles[$level] ?? 'Tidak Dikenal' }}</span>
                                </div>
                            </div>
                        </li>
                        <li class="user-footer">
                            <div class="footer-buttons">
                                <a href="{{ route('user.profil') }}" class="btn btn-profile">
                                    <i class="fa fa-user"></i> Profil
                                </a>
                                <a href="#" class="btn btn-logout" onclick="event.preventDefault(); $('#logout-form').submit();">
                                    <i class="fa fa-sign-out-alt"></i> Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>

<form action="{{ route('logout') }}" method="POST" id="logout-form" style="display: none;">
    @csrf
</form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.getElementById('user-menu-toggle');
        const dropdown = document.getElementById('user-dropdown');
        const menu = document.getElementById('user-dropdown-menu');

        toggle.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            dropdown.classList.toggle('open');
        });

        document.addEventListener('click', function (e) {
            if (!dropdown.contains(e.target)) {
                dropdown.classList.remove('open');
            }
        });

        // Close dropdown when clicking menu items
        menu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function() {
                dropdown.classList.remove('open');
            });
        });
    });
</script>
@endpush

<style>
/* ========================================
   🎯 MODERN HEADER & DROPDOWN DESIGN
======================================== */

/* Navbar Adjustments */
.main-header .navbar {
    position: relative;
    z-index: 1030 !important;
}

.navbar-custom-menu {
    float: right;
}

.navbar-custom-menu .navbar-nav > li {
    position: relative;
}

/* ========================================
   👤 USER TOGGLE BUTTON
======================================== */
.user-dropdown-toggle {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    padding: 10px 20px !important;
    height: 70px !important;
    cursor: pointer !important;
    transition: all 0.3s ease !important;
    background: transparent !important;
    border: none !important;
}

.user-dropdown-toggle:hover {
    background: rgba(255, 255, 255, 0.15) !important;
}

/* User Avatar in Toggle */
.user-avatar-wrapper {
    position: relative;
}

.user-avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0.1));
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 16px;
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.5);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
}

.user-dropdown-toggle:hover .user-avatar {
    transform: scale(1.1);
    border-color: white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

/* User Name */
.user-name {
    color: white !important;
    font-weight: 600 !important;
    font-size: 14px !important;
    letter-spacing: 0.3px;
}

/* Dropdown Icon */
.dropdown-icon {
    color: white !important;
    font-size: 14px !important;
    transition: transform 0.3s ease;
}

.dropdown.open .dropdown-icon {
    transform: rotate(180deg);
}

/* ========================================
   📋 DROPDOWN MENU
======================================== */
.user-dropdown-menu {
    position: absolute !important;
    top: calc(100% + 10px) !important;
    right: 0 !important;
    left: auto !important;
    z-index: 9999 !important;
    display: none !important;
    min-width: 320px !important;
    padding: 0 !important;
    margin: 0 !important;
    list-style: none !important;
    background: white !important;
    border: none !important;
    border-radius: 12px !important;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2) !important;
    overflow: hidden !important;
    animation: dropdownSlideIn 0.3s ease-out;
}

@keyframes dropdownSlideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Show dropdown when active */
.dropdown.open .user-dropdown-menu,
.user-menu.open .user-dropdown-menu {
    display: block !important;
}

/* Dropdown Arrow */
.user-dropdown-menu::before {
    content: '';
    position: absolute;
    top: -8px;
    right: 25px;
    width: 0;
    height: 0;
    border-left: 8px solid transparent;
    border-right: 8px solid transparent;
    border-bottom: 8px solid var(--dark-red);
}

/* ========================================
   👤 USER HEADER SECTION
======================================== */
.user-header {
    padding: 25px 20px !important;
    text-align: center !important;
    background: linear-gradient(135deg, var(--dark-red) 0%, var(--accent-red) 100%) !important;
    color: white !important;
    position: relative;
    overflow: hidden;
}

.user-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: rotate 20s linear infinite;
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.user-avatar-large {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 28px;
    color: white;
    margin: 0 auto 15px;
    border: 3px solid rgba(255, 255, 255, 0.5);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    position: relative;
    z-index: 1;
}

.user-info {
    margin: 0 !important;
    position: relative;
    z-index: 1;
}

.user-info strong {
    display: block;
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 5px;
    color: white;
}

.user-info small {
    display: block;
    font-size: 13px;
    opacity: 0.9;
    color: rgba(255, 255, 255, 0.9);
}

/* ========================================
   📊 USER BODY / STATS
======================================== */
.user-body {
    padding: 20px !important;
    background: var(--gray-50) !important;
    border-top: 1px solid var(--gray-100);
    border-bottom: 1px solid var(--gray-100);
}

.user-stats {
    display: flex;
    justify-content: center;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: white;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-700);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.stat-item i {
    color: var(--primary-red);
    font-size: 16px;
}

/* ========================================
   🔘 USER FOOTER / BUTTONS
======================================== */
.user-footer {
    padding: 20px !important;
    background: white !important;
}

.footer-buttons {
    display: flex;
    gap: 10px;
}

.footer-buttons .btn {
    flex: 1;
    padding: 12px 20px !important;
    border-radius: 8px !important;
    font-weight: 600 !important;
    font-size: 13px !important;
    transition: all 0.3s ease !important;
    border: none !important;
    cursor: pointer;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.btn-profile {
    background: var(--gray-100) !important;
    color: var(--gray-700) !important;
}

.btn-profile:hover {
    background: var(--gray-200) !important;
    color: var(--gray-900) !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.btn-logout {
    background: linear-gradient(135deg, var(--primary-red) 0%, var(--accent-red) 100%) !important;
    color: white !important;
    box-shadow: 0 4px 10px rgba(220, 38, 38, 0.3);
}

.btn-logout:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(220, 38, 38, 0.4);
}

.footer-buttons .btn i {
    font-size: 14px;
}

/* ========================================
   📱 RESPONSIVE DESIGN
======================================== */
@media (max-width: 768px) {
    .user-dropdown-menu {
        min-width: 280px !important;
        right: 10px !important;
    }

    .user-name {
        display: none !important;
    }

    .user-avatar {
        width: 35px;
        height: 35px;
        font-size: 14px;
    }

    .footer-buttons {
        flex-direction: column;
    }
}

/* ========================================
   🔧 UTILITY & FIXES
======================================== */

/* Ensure dropdown works properly */
.main-header .navbar .nav > li.dropdown {
    position: relative;
}

.main-header .navbar .nav > li > a {
    color: white !important;
}

/* Remove default AdminLTE conflicts */
.user-menu > .dropdown-menu {
    border-top-right-radius: 12px !important;
    border-top-left-radius: 12px !important;
}

.user-menu > .dropdown-menu > li.user-header {
    height: auto !important;
}

/* Smooth transitions */
* {
    -webkit-tap-highlight-color: transparent;
}

/* Prevent text selection on toggle */
.user-dropdown-toggle {
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}
</style>