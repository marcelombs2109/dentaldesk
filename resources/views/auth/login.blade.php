<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión — DentalDesk</title>
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
        .login-card {
            display: flex;
            width: 100%;
            max-width: 900px;
            min-height: 520px;
            border-radius: 18px;
            overflow: hidden;
            background: #fff;
        }
        .login-brand {
            flex: 1 1 45%;
            background: linear-gradient(160deg, #1a2e4a 0%, #2a4a6e 100%);
            color: #fff;
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .login-brand .brand-icon {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            background: rgba(255,255,255,.12);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.9rem;
            margin-bottom: 1.5rem;
        }
        .login-brand h1 { font-size: 2rem; font-weight: 700; margin-bottom: .4rem; }
        .login-brand p { color: #a8b8cc; margin-bottom: 2rem; }
        .login-brand ul { list-style: none; padding: 0; margin: 0; color: #d3deea; font-size: .92rem; }
        .login-brand li { display: flex; gap: .6rem; margin-bottom: .8rem; }
        .login-brand li i { color: #4a9eff; }
        .login-form {
            flex: 1 1 55%;
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .login-form h2 { font-weight: 700; color: #1a2e4a; }
        .form-control:focus {
            border-color: #4a9eff;
            box-shadow: 0 0 0 .2rem rgba(74,158,255,.15);
        }
        .btn-ingresar { background: #1a2e4a; color: #fff; font-weight: 600; padding: .65rem; }
        .btn-ingresar:hover { background: #2a4a6e; color: #fff; }
        @media (max-width: 767.98px) {
            .login-brand { display: none; }
            .login-card { max-width: 420px; min-height: auto; }
            .login-form { padding: 2.25rem 1.75rem; }
        }
    </style>
</head>
<body>

<div class="login-card shadow-lg">
    <div class="login-brand">
        <div class="brand-icon"><i class="bi bi-heart-pulse"></i></div>
        <h1>DentalDesk</h1>
        <p>Gestión de citas odontológicas</p>
        <ul>
            <li><i class="bi bi-calendar3"></i> Agenda diaria por consultorio</li>
            <li><i class="bi bi-people"></i> Fichas e historial de pacientes</li>
            <li><i class="bi bi-whatsapp"></i> Recordatorios por WhatsApp</li>
        </ul>
    </div>

    <div class="login-form">
        <h2 class="mb-1">Iniciar sesión</h2>
        <p class="text-muted mb-4">Acceda con su correo y contraseña.</p>

        @if ($errors->any())
            <div class="alert alert-danger d-flex align-items-start gap-2 py-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                <div>{{ $errors->first('email') }}</div>
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" novalidate>
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Correo electrónico</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-envelope text-muted"></i></span>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           class="form-control @error('email') is-invalid @enderror"
                           placeholder="usuario@dentaldesk.test"
                           autocomplete="username"
                           required
                           autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-lock text-muted"></i></span>
                    <input type="password"
                           id="password"
                           name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="••••••••"
                           autocomplete="current-password"
                           required>
                </div>
            </div>

            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">
                <label class="form-check-label text-muted small" for="remember">Recordarme en este equipo</label>
            </div>

            <button type="submit" class="btn btn-ingresar w-100">
                <i class="bi bi-box-arrow-in-right me-2"></i>Ingresar
            </button>
        </form>

        <p class="text-center text-muted small mt-4 mb-0">
            DentalDesk · Sistema interno del consultorio
        </p>
    </div>
</div>

</body>
</html>
