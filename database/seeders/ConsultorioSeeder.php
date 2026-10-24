<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ConsultorioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('consultorios')->insert([
            [
                'id'         => Str::uuid(),
                'nombre'     => 'Consultorio 1',
                'activo'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'         => Str::uuid(),
                'nombre'     => 'Consultorio 2',
                'activo'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}