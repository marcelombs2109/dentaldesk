<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $hoy  = Carbon::today()->toDateString();
        $user = $request->user();

        // SDD-03 R3: el odontólogo ve solo sus cifras.
        $base = Cita::where('fecha', $hoy)
            ->when($user->esOdontologo(), function ($query) use ($user) {
                $query->where('dentista_id', $user->dentista_id);
            });

        $stats = [
            'total'       => (clone $base)->where('estado', '!=', 'cancelada')->count(),
            'confirmadas' => (clone $base)->where('estado', 'confirmada')->count(),
            'pendientes'  => (clone $base)->where('estado', 'pendiente')->count(),
            'canceladas'  => (clone $base)->where('estado', 'cancelada')->count(),
            'atendidas'   => (clone $base)->where('estado', 'atendida')->count(),
        ];

        return view('dashboard', compact('stats', 'hoy'));
    }
}
