<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Dentista;
use App\Models\Consultorio;
use App\Services\CitaService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CitaController extends Controller
{
    public function __construct(protected CitaService $citaService) {}

    public function create(Request $request)
    {
        $paciente    = $request->paciente_id ? Paciente::find($request->paciente_id) : null;
        $dentistas   = Dentista::where('activo', true)->get();
        $consultorios = Consultorio::where('activo', true)->get();
        $duraciones  = [30 => '30 minutos', 60 => '1 hora', 90 => '1 hora 30 min', 120 => '2 horas'];

        return view('citas.create', compact('paciente', 'dentistas', 'consultorios', 'duraciones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'paciente_id'      => 'required|uuid|exists:pacientes,id',
            'dentista_id'      => 'required|uuid|exists:dentistas,id',
            'consultorio_id'   => 'required|uuid|exists:consultorios,id',
            'fecha'            => 'required|date|after_or_equal:today',
            'hora_inicio'      => 'required|date_format:H:i',
            'duracion_minutos' => 'required|in:30,60,90,120',
            'motivo'           => 'required|string|max:255',
        ], [
            'paciente_id.required'      => 'Debe seleccionar un paciente.',
            'paciente_id.exists'        => 'El paciente seleccionado no existe.',
            'dentista_id.required'      => 'Debe seleccionar un doctor.',
            'dentista_id.exists'        => 'El doctor seleccionado no existe.',
            'consultorio_id.required'   => 'Debe seleccionar un consultorio.',
            'consultorio_id.exists'     => 'El consultorio seleccionado no existe.',
            'fecha.required'            => 'La fecha es obligatoria.',
            'fecha.after_or_equal'      => 'La fecha no puede ser anterior a hoy.',
            'hora_inicio.required'      => 'La hora de inicio es obligatoria.',
            'hora_inicio.date_format'   => 'El formato de hora no es válido.',
            'duracion_minutos.required' => 'Debe seleccionar la duración.',
            'duracion_minutos.in'       => 'La duración debe ser 30, 60, 90 o 120 minutos.',
            'motivo.required'           => 'El motivo de la cita es obligatorio.',
        ]);

        $resultado = $this->citaService->crear($request->all());

        if (!$resultado['ok']) {
            return back()->withInput()->with('error', $resultado['mensaje']);
        }

        return redirect()->route('agenda.index', ['fecha' => $request->fecha])
            ->with('success', 'Cita agendada correctamente.');
    }

    public function show(Cita $cita)
    {
        $this->authorize('view', $cita);

        $cita->load(['paciente', 'dentista', 'consultorio', 'recordatorio']);
        return view('citas.show', compact('cita'));
    }

    public function cambiarEstado(Request $request, Cita $cita)
    {
        $this->authorize('update', $cita);

        $request->validate([
            'estado' => 'required|in:pendiente,confirmada,cancelada,atendida',
        ]);

        $this->citaService->cambiarEstado($cita, $request->estado);

        return back()->with('success', 'Estado de la cita actualizado.');
    }

    public function disponibilidad(Request $request)
    {
        $request->validate([
            'fecha'            => 'required|date',
            'hora_inicio'      => 'required',
            'duracion_minutos' => 'required|in:30,60,90,120',
        ]);

        $horaFin = Carbon::parse($request->hora_inicio)
            ->addMinutes((int) $request->duracion_minutos)
            ->format('H:i:s');

        $dentistas = $this->citaService->dentistasDisponibles(
            $request->fecha,
            $request->hora_inicio,
            (int) $request->duracion_minutos
        );

        $consultorios = $this->citaService->consultoriosDisponibles(
            $request->fecha,
            $request->hora_inicio,
            $horaFin
        );

        return response()->json([
            'dentistas'    => $dentistas,
            'consultorios' => $consultorios,
        ]);
    }
}