<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Services\CitaService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    public function __construct(protected CitaService $citaService) {}

    public function index(Request $request)
    {
        $fecha = $request->get('fecha', Carbon::today()->toDateString());

        $citas = Cita::with(['paciente', 'dentista', 'consultorio'])
            ->where('fecha', $fecha)
            ->whereHas('paciente')
            ->orderBy('hora_inicio')
            ->get();

        $consultorio1 = $citas->filter(fn($c) => $c->consultorio->nombre === 'Consultorio 1')->values();
        $consultorio2 = $citas->filter(fn($c) => $c->consultorio->nombre === 'Consultorio 2')->values();

        return view('agenda.index', compact('fecha', 'consultorio1', 'consultorio2'));
    }
}