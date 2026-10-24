<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DentalDesk') — Consultorio Dental</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }

        /* ── Sidebar desktop ── */
        .sidebar {
            min-height: 100vh;
            background-color: #1a2e4a;
            width: 240px;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 1rem;
            z-index: 1040;
            transition: transform 0.3s ease;
        }
        .sidebar .brand {
            color: #fff;
            font-size: 1.3rem;
            font-weight: 700;
            padding: 1rem 1.5rem 2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .sidebar .nav-link {
            color: #a8b8cc;
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.95rem;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background-color: #2a4a6e;
            border-left-color: #4a9eff;
        }
        .sidebar .nav-link i {
            font-size: 1.1rem;
            width: 20px;
        }

        /* ── Layout principal ── */
        .topbar {
            background: #fff;
            border-bottom: 1px solid #dee2e6;
            padding: 0.75rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 1030;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .main-content {
            padding: 1.5rem;
            min-height: 100vh;
        }

        /* ── Desktop: sidebar fijo ── */
        @media (min-width: 992px) {
            .sidebar { transform: translateX(0) !important; }
            .topbar, .main-content { margin-left: 240px; }
            .sidebar-toggle { display: none; }
            .sidebar-overlay { display: none !important; }
        }

        /* ── Mobile: sidebar oculto por defecto ── */
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .topbar, .main-content { margin-left: 0; }
        }

        /* ── Overlay fondo oscuro mobile ── */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1039;
        }
        .sidebar-overlay.show { display: block; }

        /* ── Botón cerrar sidebar mobile ── */
        .sidebar-close {
            display: none;
            position: absolute;
            top: 1rem;
            right: 1rem;
            color: #a8b8cc;
            background: none;
            border: none;
            font-size: 1.4rem;
            cursor: pointer;
        }
        @media (max-width: 991.98px) {
            .sidebar-close { display: block; }
        }

        /* ── Badges de estado ── */
        .badge-pendiente  { background-color: #ffc107; color: #000; }
        .badge-confirmada { background-color: #198754; color: #fff; }
        .badge-cancelada  { background-color: #dc3545; color: #fff; }
        .badge-atendida   { background-color: #6c757d; color: #fff; }

        /* ── Cards ── */
        .card-stat {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .cita-card {
            border-left: 4px solid #4a9eff;
            border-radius: 8px;
            background: #fff;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        }
        .cita-card.confirmada { border-left-color: #198754; }
        .cita-card.cancelada  { border-left-color: #dc3545; }
        .cita-card.atendida   { border-left-color: #6c757d; }

        .main-content {
            padding: 1.5rem;
            min-height: 100vh;
            padding-top: 1rem;
        }

        @media (max-width: 991.98px) {
            .main-content {
                padding: 1rem;
                padding-top: 1rem;
            }
        }

    </style>

    @stack('styles')
</head>
<body>

<!-- Overlay mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
<nav class="sidebar" id="sidebar">
    <button class="sidebar-close" id="sidebarClose">
        <i class="bi bi-x-lg"></i>
    </button>
    <div class="brand">
        <i class="bi bi-heart-pulse"></i> DentalDesk
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        @can('ver_agenda')
        <li class="nav-item">
            <a href="{{ route('agenda.index') }}"
               class="nav-link {{ request()->routeIs('agenda.*') ? 'active' : '' }}">
                <i class="bi bi-calendar3"></i> Agenda
            </a>
        </li>
        @endcan
        @can('crear_cita')
        <li class="nav-item">
            <a href="{{ route('citas.create') }}"
               class="nav-link {{ request()->routeIs('citas.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle"></i> Nueva Cita
            </a>
        </li>
        @endcan
        @can('ver_pacientes')
        <li class="nav-item">
            <a href="{{ route('pacientes.index') }}"
               class="nav-link {{ request()->routeIs('pacientes.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Pacientes
            </a>
        </li>
        @endcan
    </ul>
</nav>

<!-- Topbar -->
<div class="topbar">
    <div class="d-flex align-items-center gap-3">
        <button class="btn btn-sm btn-outline-secondary sidebar-toggle d-lg-none"
                id="sidebarToggle">
            <i class="bi bi-list fs-5"></i>
        </button>
        <h6 class="mb-0 fw-semibold text-muted d-none d-sm-block">
            @yield('title', 'Dashboard')
        </h6>
    </div>
    <div class="d-flex align-items-center gap-3">
        <span class="text-muted small d-none d-md-inline">
            <i class="bi bi-calendar2-check me-1"></i>
            {{ \Carbon\Carbon::today()->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
        </span>

        @auth
            <div class="dropdown">
                <button class="btn btn-sm btn-light border d-flex align-items-center gap-2 px-2"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                    <span class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center flex-shrink-0"
                          style="width:30px;height:30px">
                        <i class="bi bi-person-fill"></i>
                    </span>
                    <span class="text-start lh-sm d-none d-sm-block">
                        <span class="d-block small fw-semibold">{{ auth()->user()->name }}</span>
                        <span class="d-block text-muted" style="font-size:.7rem">{{ auth()->user()->rol_etiqueta }}</span>
                    </span>
                    <i class="bi bi-chevron-down small text-muted"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li class="px-3 py-2">
                        <div class="fw-semibold small">{{ auth()->user()->name }}</div>
                        <div class="text-muted" style="font-size:.75rem">{{ auth()->user()->email }}</div>
                        <span class="badge bg-secondary mt-1">{{ auth()->user()->rol_etiqueta }}</span>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        @endauth
    </div>
</div>

<!-- Contenido principal -->
<div class="main-content">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Corrige los siguientes errores:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const sidebar        = document.getElementById('sidebar');
    const overlay        = document.getElementById('sidebarOverlay');
    const toggleBtn      = document.getElementById('sidebarToggle');
    const closeBtn       = document.getElementById('sidebarClose');

    function abrirSidebar() {
        sidebar.classList.add('open');
        overlay.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    function cerrarSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('show');
        document.body.style.overflow = '';
    }

    toggleBtn?.addEventListener('click', abrirSidebar);
    closeBtn?.addEventListener('click', cerrarSidebar);
    overlay?.addEventListener('click', cerrarSidebar);

    // Cerrar sidebar al navegar en mobile
    document.querySelectorAll('.sidebar .nav-link').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 992) cerrarSidebar();
        });
    });
</script>

@stack('scripts')
</body>
</html>