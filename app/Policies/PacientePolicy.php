<?php

namespace App\Policies;

use App\Models\Paciente;
use App\Models\User;

/**
 * SDD-03: alcance de datos del odontólogo sobre pacientes.
 *
 * Los permisos de rol salen de config/permisos.php; el alcance se calcula
 * en el servidor: un odontólogo solo accede a pacientes con al menos una
 * cita suya.
 */
class PacientePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->permite($user, 'ver_pacientes');
    }

    public function view(User $user, Paciente $paciente): bool
    {
        return $this->permite($user, 'ver_pacientes')
            && $this->esDeSuAlcance($user, $paciente);
    }

    public function create(User $user): bool
    {
        return $this->permite($user, 'registrar_paciente');
    }

    public function update(User $user, Paciente $paciente): bool
    {
        return $this->permite($user, 'modificar_paciente');
    }

    public function delete(User $user, Paciente $paciente): bool
    {
        return $this->permite($user, 'borrar_paciente');
    }

    private function permite(User $user, string $permiso): bool
    {
        return in_array($user->rol, config("permisos.{$permiso}", []), true);
    }

    private function esDeSuAlcance(User $user, Paciente $paciente): bool
    {
        if (! $user->esOdontologo()) {
            return true;
        }

        if (! $user->dentista_id) {
            return false;
        }

        return $paciente->citas()
            ->where('dentista_id', $user->dentista_id)
            ->exists();
    }
}
