<?php

namespace Database\Seeders;

use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProyectoSeeder extends Seeder
{
    public function run(): void
    {
        if (Proyecto::exists() || Storage::disk('local')->exists('proyectos.json')) {
            return;
        }

        $usuario = User::firstOrCreate(
            ['email' => 'proyectos@tech-solutions.example'],
            ['name' => 'Tech Solutions', 'password' => Str::random(32)],
        );

        Proyecto::insert([
            [
                'nombre' => 'Migración de Servidores',
                'fecha_inicio' => '2026-11-01',
                'estado' => 'Planificación',
                'responsable' => 'Juan Pérez',
                'monto' => 1500000,
                'created_by' => $usuario->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Desarrollo App Móvil',
                'fecha_inicio' => '2026-11-15',
                'estado' => 'En progreso',
                'responsable' => 'Ana Gómez',
                'monto' => 3200000,
                'created_by' => $usuario->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
