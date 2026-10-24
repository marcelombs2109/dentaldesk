<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consultorio extends Model
{
    use HasUuids;

    protected $table = 'consultorios';

    protected $fillable = [
        'nombre',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'consultorio_id');
    }

    public function estaDisponibleEn(string $fecha, string $horaInicio, string $horaFin): bool
    {
        return !$this->citas()
            ->where('fecha', $fecha)
            ->where('estado', '!=', 'cancelada')
            ->where('hora_inicio', '<', $horaFin)
            ->where('hora_fin', '>', $horaInicio)
            ->exists();
    }
}