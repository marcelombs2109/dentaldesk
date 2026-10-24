<?php

namespace Database\Seeders;

use App\Models\Dentista;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * SDD-01 R7: usuarios de trabajo (uno por rol, más un odontólogo por doctor).
 * Se ejecuta después de DentistaSeeder porque los odontólogos se vinculan
 * al doctor existente por nombre.
 */
class UsuarioSeeder extends Seeder
{
    public const PASSWORD_PRUEBA = 'password';

    public function run(): void
    {
        $this->crear('Administrador DentalDesk', 'admin@dentaldesk.test', User::ROL_ADMINISTRADOR);

        $this->crear('Recepcion DentalDesk', 'recepcion@dentaldesk.test', User::ROL_RECEPCIONISTA);

        foreach (Dentista::orderBy('nombre')->get() as $dentista) {
            $this->crear(
                $dentista->nombre,
                $this->correoPara($dentista->nombre),
                User::ROL_ODONTOLOGO,
                $dentista->id
            );
        }
    }

    /**
     * Crea el usuario si no existe (seeder idempotente).
     * `rol` y `dentista_id` se asignan fuera de $fillable a propósito.
     */
    private function crear(string $nombre, string $email, string $rol, ?string $dentistaId = null): void
    {
        if (User::where('email', $email)->exists()) {
            return;
        }

        $usuario = new User();
        $usuario->name = $nombre;
        $usuario->email = $email;
        $usuario->password = self::PASSWORD_PRUEBA;
        $usuario->rol = $rol;
        $usuario->dentista_id = $rol === User::ROL_ODONTOLOGO ? $dentistaId : null;
        $usuario->save();
    }

    /**
     * "Dr. García" -> "dr.garcia@dentaldesk.test".
     */
    private function correoPara(string $nombreDentista): string
    {
        $slug = Str::slug(str_replace(['Dr.', 'Dra.'], '', $nombreDentista));

        return "{$slug}@dentaldesk.test";
    }
}
