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
        $user  = $request->user();

        $citas = Cita::with(['paciente', 'dentista', 'consultorio'])
            ->where('fecha', $fecha)
            ->whereHas('paciente')
            ->when($user->esOdontologo(), function ($query) use ($user) {
                // SDD-03 R3: el odontólogo solo ve su agenda.
                $query->where('dentista_id', $user->dentista_id);
            })
            ->orderBy('hora_inicio')
            ->get();

        $consultorio1 = $citas->filter(fn($c) => $c->consultorio->nombre === 'Consultorio 1')->values();
        $consultorio2 = $citas->filter(fn($c) => $c->consultorio->nombre === 'Consultorio 2')->values();

        return view('agenda.index', compact('fecha', 'consultorio1', 'consultorio2'));
    }
}
