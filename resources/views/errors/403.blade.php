<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso denegado — DentalDesk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #eef2f7;
            font-family: 'Segoe UI', sans-serif;
            padding: 1.5rem;
        }
        .denegado-card {
            background: #fff;
            border-radius: 18px;
            max-width: 520px;
            width: 100%;
            padding: 3rem 2.5rem;
            text-align: center;
            box-shadow: 0 18px 45px rgba(26,46,74,.12);
        }
        .denegado-icon {
            width: 84px;
            height: 84px;
            margin: 0 auto 1.5rem;
            border-radius: 50%;
            background: #ffeaea;
            color: #dc3545;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.4rem;
        }
        .denegado-code { font-size: 3rem; font-weight: 800; color: #1a2e4a; line-height: 1; }
        .denegado-title { font-size: 1.15rem; font-weight: 700; color: #1a2e4a; margin: 1rem 0 .5rem; }
        .denegado-text { color: #6c757d; font-size: .95rem; margin-bottom: 1.75rem; }
        .btn-panel { background: #1a2e4a; color: #fff; font-weight: 600; }
        .btn-panel:hover { background: #2a4a6e; color: #fff; }
    </style>
</head>
<body>

<div class="denegado-card">
    <div class="denegado-icon"><i class="bi bi-shield-lock-fill"></i></div>
    <div class="denegado-code">403</div>
    <div class="denegado-title">Acceso denegado</div>
    <p class="denegado-text">
        No tiene permiso para acceder a este recurso o realizar esta acción.
        Si cree que se trata de un error, contacte al administrador del sistema.
    </p>

    @auth
        <p class="text-muted small mb-4">
            Sesión: <strong>{{ auth()->user()->name }}</strong>
            · {{ auth()->user()->rol_etiqueta }}
        </p>
    @endauth

    <div class="d-flex gap-2 justify-content-center flex-wrap">
        <a href="{{ url('/') }}" class="btn btn-panel px-4">
            <i class="bi bi-house-door me-2"></i>Volver al panel
        </a>
        @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
                </button>
            </form>
        @endauth
    </div>
</div>

</body>
</html>
