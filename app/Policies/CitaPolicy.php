<?php

namespace App\Policies;

use App\Models\Cita;
use App\Models\User;

/**
 * SDD-03: alcance de datos del odontólogo sobre citas.
 * Administrador y recepcionista ven todas; el odontólogo solo las suyas.
 */
class CitaPolicy
{
    public function view(User $user, Cita $cita): bool
    {
        return $this->permite($user, 'ver_cita') && $this->esDeSuAlcance($user, $cita);
    }

    public function update(User $user, Cita $cita): bool
    {
        return $this->permite($user, 'cambiar_estado_cita') && $this->esDeSuAlcance($user, $cita);
    }

    private function permite(User $user, string $permiso): bool
    {
        return in_array($user->rol, config("permisos.{$permiso}", []), true);
    }

    private function esDeSuAlcance(User $user, Cita $cita): bool
    {
        if (! $user->esOdontologo()) {
            return true;
        }

        return $user->dentista_id !== null
            && (string) $cita->dentista_id === (string) $user->dentista_id;
    }
}
