<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dentista extends Model
{
    use HasUuids;

    protected $table = 'dentistas';

    protected $fillable = [
        'nombre',
        'especialidad',
        'turno',
        'hora_inicio',
        'hora_fin',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'dentista_id');
    }

    public function estaDisponibleEn(string $fecha, string $horaInicio, string $horaFin): bool
    {
        // Verificar que la cita está dentro del turno del doctor
        $inicio   = strtotime($horaInicio);
        $fin      = strtotime($horaFin);
        $turnoIni = strtotime($this->hora_inicio);
        $turnoFin = strtotime($this->hora_fin);

        if ($inicio < $turnoIni || $fin > $turnoFin) {
            return false;
        }

        // Verificar que no tiene otra cita superpuesta
        return !$this->citas()
            ->where('fecha', $fecha)
            ->where('estado', '!=', 'cancelada')
            ->where('hora_inicio', '<', $horaFin)
            ->where('hora_fin', '>', $horaInicio)
            ->exists();
    }
}