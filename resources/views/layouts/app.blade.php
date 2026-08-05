<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="icon" type="image/x-icon" href="{{ asset('logo_lab.png') }}">
    <style>
        :root {
            --sidebar-width: 250px;
        }

        body {
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .sidebar {
            width: var(--sidebar-width);
            background-color: #ffffff;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            position: fixed;
            height: 100vh;
            z-index: 1000;
            transition: all 0.3s;
        }

        .sidebar-header {
            padding: 2rem;
            border-bottom: 2px solid rgba(0, 0, 0, 0.1);
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-direction: column;
        }

        .sidebar-content {
            padding: 1rem 0;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu-item {
            margin-bottom: 0.25rem;
        }

        .sidebar-menu-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: #495057;
            text-decoration: none;
            transition: all 0.2s;
            gap: 0.75rem;
        }

        .sidebar-menu-link:hover {
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }

        .sidebar-menu-link.active {
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
            font-weight: 500;
        }

        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            position: absolute;
            bottom: 0;
            width: 100%;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 1.5rem;
            transition: all 0.3s;
        }

        .header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            position: sticky;
            top: 0;
            z-index: 900;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .notification-badge {
            position: relative;
        }

        .notification-badge::after {
            content: '';
            position: absolute;
            top: 0.25rem;
            right: 0.25rem;
            width: 8px;
            height: 8px;
            background-color: #0d6efd;
            border-radius: 50%;
        }

        .card {
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: none;
        }

        .stats-card {
            padding: 1.25rem;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .activity-content {
            flex: 1;
        }

        .activity-time {
            color: #6c757d;
            font-size: 0.875rem;
        }

        @media (max-width: 768px) {
            .sidebar {
                margin-left: calc(var(--sidebar-width) * -1);
            }

            .sidebar.show {
                margin-left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar-toggle {
                display: block !important;
            }
        }

        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.25rem;
            color: #495057;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-brand">
                <div class="" style="width:100px; height:100px; padding:4px; display:flex; align-items:center; justify-content:;">
                    <img src="{{ asset('logo_lab.png') }}" alt="p" style="max-width:100%; max-height:100%; object-fit:contain;">
                </div>
                <div class="brand-name fw-bold">Surat Masuk/Surat Keluar LAB ICT Terpadu</div>
            </div>
        </div>

        <div class="sidebar-content">
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a href="{{ route('dashboard') }}"
                        class="sidebar-menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('surat-masuk') }}"
                        class="sidebar-menu-link {{ request()->routeIs('surat-masuk') ? 'active' : '' }}">
                        <i class="bi bi-envelope-fill"></i>
                        <span>Surat Masuk</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('surat-keluar') }}"
                        class="sidebar-menu-link {{ request()->routeIs('surat-keluar') ? 'active' : '' }}">
                        <i class="bi bi-send-fill"></i>
                        <span>Surat Keluar</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('surat-revisi') }}"
                        class="sidebar-menu-link {{ request()->routeIs('surat-revisi') ? 'active' : '' }}">
                        <i class="bi bi-pencil-square"></i>
                        <span>Revisi</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="sidebar-menu-link w-100 text-start border-0 bg-transparent">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            <div class="header-actions">
                <p class="fw-bold mb-0 fs-5">
                    <i class="bi bi-person-circle me-1"></i>
                    Selamat Datang, <span class="text-primary">{{ Auth::user()->name }}</span>!
                </p>
                {{-- <div class="notification-badge">
                    <button class="btn btn-light rounded-circle">
                        <i class="bi bi-bell"></i>
                    </button>
                </div> --}}
                {{-- <div class="dropdown"> --}}
                    {{-- <button class="btn btn-light rounded-circle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="bi bi-person"></i>
                    </button> --}}
                    {{-- <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text fw-bold">My Account</span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#">Profile</a></li>
                        <li><a class="dropdown-item" href="#">Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </li>
                    </ul> --}}
                {{-- </div> --}}
            </div>
        </div>

        <!-- Page Content -->
        <div class="container-fluid py-4">
            @yield('content')
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');

            sidebarToggle.addEventListener('click', function (e) {
                e.stopPropagation();
                sidebar.classList.toggle('show');
            });

            document.addEventListener('click', function (event) {
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isSmallScreen = window.innerWidth <= 768;

                if (!isClickInsideSidebar && sidebar.classList.contains('show') && isSmallScreen) {
                    sidebar.classList.remove('show');
                }
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('show');
                }
            });
        });
    </script>
</body>

</html>
