<?php

namespace App\Services;

use App\Models\Cita;
use App\Models\Dentista;
use App\Models\Consultorio;
use Carbon\Carbon;

class CitaService
{
    /**
     * Valida disponibilidad y crea una nueva cita.
     */
    public function crear(array $datos): array
    {
        $horaInicio = $datos['hora_inicio'];
        $horaFin = Carbon::parse($datos['hora_inicio'])
            ->addMinutes((int) $datos['duracion_minutos'])
            ->format('H:i:s');

        $dentista = Dentista::findOrFail($datos['dentista_id']);
        $consultorio = Consultorio::findOrFail($datos['consultorio_id']);

        // Validar turno del doctor
        if (!$this->citaDentroDelTurno($dentista, $horaInicio, $horaFin)) {
            return [
                'ok'      => false,
                'mensaje' => "El Dr. {$dentista->nombre} atiende de {$dentista->hora_inicio} a {$dentista->hora_fin}.",
            ];
        }

        // Validar disponibilidad del doctor
        if (!$dentista->estaDisponibleEn($datos['fecha'], $horaInicio, $horaFin)) {
            return [
                'ok'      => false,
                'mensaje' => "El Dr. {$dentista->nombre} ya tiene una cita en ese horario.",
            ];
        }

        // Validar disponibilidad del consultorio
        if (!$consultorio->estaDisponibleEn($datos['fecha'], $horaInicio, $horaFin)) {
            return [
                'ok'      => false,
                'mensaje' => "El {$consultorio->nombre} ya está ocupado en ese horario.",
            ];
        }

        // Todo válido — crear la cita
        $cita = Cita::create([
            'paciente_id'      => $datos['paciente_id'],
            'dentista_id'      => $datos['dentista_id'],
            'consultorio_id'   => $datos['consultorio_id'],
            'fecha'            => $datos['fecha'],
            'hora_inicio'      => $horaInicio,
            'hora_fin'         => $horaFin,
            'duracion_minutos' => $datos['duracion_minutos'],
            'motivo'           => $datos['motivo'],
            'estado'           => 'pendiente',
        ]);

        return [
            'ok'   => true,
            'cita' => $cita->load(['paciente', 'dentista', 'consultorio']),
        ];
    }

    /**
     * Devuelve todas las citas de un día con sus relaciones.
     */
    public function agendaDelDia(string $fecha): array
    {
        $citas = Cita::with(['paciente', 'dentista', 'consultorio'])
            ->where('fecha', $fecha)
            ->where('estado', '!=', 'cancelada')
            ->orderBy('hora_inicio')
            ->get();

        return [
            'consultorio_1' => $citas->where('consultorio.nombre', 'Consultorio 1')->values(),
            'consultorio_2' => $citas->where('consultorio.nombre', 'Consultorio 2')->values(),
        ];
    }

    /**
     * Devuelve los doctores disponibles para una fecha, hora y duración dadas.
     */
    public function dentistasDisponibles(string $fecha, string $horaInicio, int $duracion): \Illuminate\Support\Collection
    {
        $horaFin = Carbon::parse($horaInicio)->addMinutes($duracion)->format('H:i:s');

        return Dentista::where('activo', true)
            ->get()
            ->filter(function (Dentista $dentista) use ($fecha, $horaInicio, $horaFin) {
                return $this->citaDentroDelTurno($dentista, $horaInicio, $horaFin)
                    && $dentista->estaDisponibleEn($fecha, $horaInicio, $horaFin);
            })
            ->values();
    }

    /**
     * Devuelve los consultorios disponibles para una fecha y rango horario.
     */
    public function consultoriosDisponibles(string $fecha, string $horaInicio, string $horaFin): \Illuminate\Support\Collection
    {
        return Consultorio::where('activo', true)
            ->get()
            ->filter(fn(Consultorio $c) => $c->estaDisponibleEn($fecha, $horaInicio, $horaFin))
            ->values();
    }

    /**
     * Cambia el estado de una cita.
     */
    public function cambiarEstado(Cita $cita, string $estado): Cita
    {
        $cita->update(['estado' => $estado]);
        return $cita;
    }

    /**
     * Verifica que la cita completa esté dentro del turno del doctor.
     */
    private function citaDentroDelTurno(Dentista $dentista, string $horaInicio, string $horaFin): bool
    {
        $inicio    = strtotime($horaInicio);
        $fin       = strtotime($horaFin);
        $turnoIni  = strtotime($dentista->hora_inicio);
        $turnoFin  = strtotime($dentista->hora_fin);

        return $inicio >= $turnoIni && $fin <= $turnoFin;
    }
}