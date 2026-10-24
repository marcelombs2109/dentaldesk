<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Cita extends Model
{
    use HasUuids;

    protected $table = 'citas';

    protected $fillable = [
        'paciente_id',
        'dentista_id',
        'consultorio_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'duracion_minutos',
        'motivo',
        'estado',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function dentista(): BelongsTo
    {
        return $this->belongsTo(Dentista::class, 'dentista_id');
    }

    public function consultorio(): BelongsTo
    {
        return $this->belongsTo(Consultorio::class, 'consultorio_id');
    }

    public function recordatorio(): HasOne
    {
        return $this->hasOne(Recordatorio::class, 'cita_id');
    }
}