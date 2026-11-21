<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    public function up(): void
    {
        $disco = Storage::disk('local');
        if (! $disco->exists('proyectos.json')) {
            return;
        }

        $contenido = json_decode($disco->get('proyectos.json'), true, flags: JSON_THROW_ON_ERROR);
        if (! is_array($contenido['registros'] ?? null)) {
            throw new RuntimeException('El archivo de proyectos no contiene una lista de registros válida.');
        }

        DB::transaction(function () use ($contenido) {
            $usuarios = DB::table('users')->pluck('id')->flip();
            $ahora = now();

            foreach ($contenido['registros'] as $proyecto) {
                $creador = $proyecto['created_by'] ?? null;
                DB::table('proyectos')->insert([
                    'id' => $proyecto['Id'],
                    'nombre' => $proyecto['Nombre'],
                    'fecha_inicio' => Carbon::parse($proyecto['Fecha_Inicio'])->toDateString(),
                    'estado' => $proyecto['Estado'],
                    'responsable' => $proyecto['Responsable'],
                    'monto' => $proyecto['Monto'],
                    'created_by' => $creador !== null && $usuarios->has($creador) ? $creador : null,
                    'created_at' => $ahora,
                    'updated_at' => $ahora,
                ]);
            }
        });
    }

    public function down(): void
    {
        // Los registros importados pueden haberse editado desde que se migraron.
    }
};
