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
    ->name('webhook.whatsapp')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

/*
|--------------------------------------------------------------------------
| Rutas protegidas (SDD-01 R2)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Agenda
    Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');

    // Pacientes
    Route::get('/pacientes/buscar', [PacienteController::class, 'buscar'])->name('pacientes.buscar');
    Route::resource('/pacientes', PacienteController::class);

    // Citas
    Route::get('/citas/disponibilidad', [CitaController::class, 'disponibilidad'])->name('citas.disponibilidad');
    Route::post('/citas/{cita}/estado', [CitaController::class, 'cambiarEstado'])->name('citas.estado');
    Route::resource('/citas', CitaController::class)->only(['create', 'store', 'show']);
});
