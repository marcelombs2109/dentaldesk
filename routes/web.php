<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\CitaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;


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

Route::post('/webhook/whatsapp', [WebhookController::class, 'whatsapp'])
    ->name('webhook.whatsapp')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);