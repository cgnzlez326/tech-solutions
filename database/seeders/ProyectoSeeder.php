<?php

namespace Database\Seeders;

use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProyectoSeeder extends Seeder
{
    public function run(): void
    {
        $usuario = User::firstOrCreate(
            ['email' => 'proyectos@tech-solutions.example'],
            ['name' => 'Tech Solutions', 'password' => Str::random(32)],
        );

        Proyecto::inicializarEjemplos($usuario->id);
    }
}
