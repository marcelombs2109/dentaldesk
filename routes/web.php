<?php

use App\Http\Controllers\AgendaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
| Solo el login y el webhook de Twilio quedan fuera de la sesión.
| El webhook se autentica con la firma de Twilio (SDD-04).
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::post('/webhook/whatsapp', [WebhookController::class, 'whatsapp'])
    ->middleware('twilio.firma')
    ->name('webhook.whatsapp')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

/*
|--------------------------------------------------------------------------
| Rutas protegidas (SDD-01 R2) y permisos por ruta (SDD-02 R3)
|--------------------------------------------------------------------------
| Cada ruta declara el permiso que la protege; la matriz vive en
| config/permisos.php.
*/

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard: todos los roles autenticados (el odontólogo ve sus cifras).
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Agenda
    Route::get('/agenda', [AgendaController::class, 'index'])
        ->middleware('can:ver_agenda')
        ->name('agenda.index');

    // Pacientes
    Route::get('/pacientes/buscar', [PacienteController::class, 'buscar'])
        ->middleware('can:ver_pacientes')
        ->name('pacientes.buscar');

    Route::get('/pacientes', [PacienteController::class, 'index'])
        ->middleware('can:ver_pacientes')
        ->name('pacientes.index');

    Route::get('/pacientes/create', [PacienteController::class, 'create'])
        ->middleware('can:registrar_paciente')
        ->name('pacientes.create');

    Route::post('/pacientes', [PacienteController::class, 'store'])
        ->middleware('can:registrar_paciente')
        ->name('pacientes.store');

    Route::get('/pacientes/{paciente}', [PacienteController::class, 'show'])
        ->middleware('can:ver_pacientes')
        ->name('pacientes.show');

    Route::get('/pacientes/{paciente}/edit', [PacienteController::class, 'edit'])
        ->middleware('can:modificar_paciente')
        ->name('pacientes.edit');

    Route::put('/pacientes/{paciente}', [PacienteController::class, 'update'])
        ->middleware('can:modificar_paciente')
        ->name('pacientes.update');

    Route::delete('/pacientes/{paciente}', [PacienteController::class, 'destroy'])
        ->middleware('can:borrar_paciente')
        ->name('pacientes.destroy');

    // Citas
    Route::get('/citas/disponibilidad', [CitaController::class, 'disponibilidad'])
        ->middleware('can:consultar_disponibilidad')
        ->name('citas.disponibilidad');

    Route::get('/citas/create', [CitaController::class, 'create'])
        ->middleware('can:crear_cita')
        ->name('citas.create');

    Route::post('/citas', [CitaController::class, 'store'])
        ->middleware('can:crear_cita')
        ->name('citas.store');

    Route::get('/citas/{cita}', [CitaController::class, 'show'])
        ->middleware('can:ver_cita')
        ->name('citas.show');

    Route::post('/citas/{cita}/estado', [CitaController::class, 'cambiarEstado'])
        ->middleware('can:cambiar_estado_cita')
        ->name('citas.estado');
});
