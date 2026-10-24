<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recordatorio extends Model
{
    use HasUuids;

    protected $table = 'recordatorios';

    protected $fillable = [
        'cita_id',
        'enviado_en',
        'estado',
        'respuesta_wa',
    ];

    protected $casts = [
        'enviado_en' => 'datetime',
    ];

    public function cita(): BelongsTo
    {
        return $this->belongsTo(Cita::class, 'cita_id');
    }
}