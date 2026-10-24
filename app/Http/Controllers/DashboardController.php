<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $hoy = Carbon::today()->toDateString();

        $stats = [
            'total'      => Cita::where('fecha', $hoy)->where('estado', '!=', 'cancelada')->count(),
            'confirmadas' => Cita::where('fecha', $hoy)->where('estado', 'confirmada')->count(),
            'pendientes'  => Cita::where('fecha', $hoy)->where('estado', 'pendiente')->count(),
            'canceladas'  => Cita::where('fecha', $hoy)->where('estado', 'cancelada')->count(),
            'atendidas'   => Cita::where('fecha', $hoy)->where('estado', 'atendida')->count(),
        ];

        return view('dashboard', compact('stats', 'hoy'));
    }
}