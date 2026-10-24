<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DentistaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('dentistas')->insert([
            [
                'id'           => Str::uuid(),
                'nombre'       => 'Dr. García',
                'especialidad' => 'Odontología General',
                'turno'        => 'manana',
                'hora_inicio'  => '09:00:00',
                'hora_fin'     => '14:00:00',
                'activo'       => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'nombre'       => 'Dr. Perez',
                'especialidad' => 'Odontología General',
                'turno'        => 'tarde_noche',
                'hora_inicio'  => '14:00:00',
                'hora_fin'     => '22:00:00',
                'activo'       => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'nombre'       => 'Dr. Mayta',
                'especialidad' => 'Odontología General',
                'turno'        => 'libre',
                'hora_inicio'  => '09:00:00',
                'hora_fin'     => '22:00:00',
                'activo'       => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);
    }
}