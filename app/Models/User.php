<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Roles del sistema (SDD-01 R3). Tres valores fijos, sin tabla auxiliar.
     */
    public const ROL_ADMINISTRADOR = 'administrador';
    public const ROL_RECEPCIONISTA = 'recepcionista';
    public const ROL_ODONTOLOGO    = 'odontologo';

    public const ROLES = [
        self::ROL_ADMINISTRADOR,
        self::ROL_RECEPCIONISTA,
        self::ROL_ODONTOLOGO,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * `rol` y `dentista_id` se dejan fuera a propósito (SDD-01): no deben
     * poder asignarse en masa ni permitir un auto-ascenso de privilegios.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Doctor al que pertenece el usuario cuando su rol es odontólogo (SDD-01 R4).
     */
    public function dentista(): BelongsTo
    {
        return $this->belongsTo(Dentista::class, 'dentista_id');
    }

    public function esAdministrador(): bool
    {
        return $this->rol === self::ROL_ADMINISTRADOR;
    }

    public function esRecepcionista(): bool
    {
        return $this->rol === self::ROL_RECEPCIONISTA;
    }

    public function esOdontologo(): bool
    {
        return $this->rol === self::ROL_ODONTOLOGO;
    }

    /**
     * Etiqueta legible del rol para la interfaz.
     */
    public function getRolEtiquetaAttribute(): string
    {
        return match ($this->rol) {
            self::ROL_ADMINISTRADOR => 'Administrador',
            self::ROL_RECEPCIONISTA => 'Recepcionista',
            self::ROL_ODONTOLOGO    => 'Odontólogo',
            default                 => ucfirst((string) $this->rol),
        };
    }
}
