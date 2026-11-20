<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Proyecto extends Model
{
    public static function getDatosEstaticos()
    {
        return collect([
            ['Id' => 1, 'Nombre' => 'Migración de Servidores', 'Fecha_Inicio' => '2026-11-01', 'Estado' => 'Planificación', 'Responsable' => 'Juan Pérez', 'Monto' => 1500000],
            ['Id' => 2, 'Nombre' => 'Desarrollo App Móvil', 'Fecha_Inicio' => '2026-11-15', 'Estado' => 'En progreso', 'Responsable' => 'Ana Gómez', 'Monto' => 3200000],
        ]);
    }

    public static function getProyectos(): Collection
    {
        return collect(session('proyectos.registros') ?? self::getDatosEstaticos()->keyBy('Id'));
    }

    public static function crear(array $datos): array
    {
        $id = session('proyectos.siguiente_id') ?? (self::getDatosEstaticos()->max('Id') + 1);
        $proyecto = ['Id' => $id] + $datos;

        session([
            'proyectos.registros' => self::getProyectos()->put($id, $proyecto)->all(),
            'proyectos.siguiente_id' => $id + 1,
        ]);

        return $proyecto;
    }

    public static function actualizar(array $proyecto, array $datos): array
    {
        $proyecto = array_replace($proyecto, $datos);
        session(['proyectos.registros' => self::getProyectos()->put($proyecto['Id'], $proyecto)->all()]);

        return $proyecto;
    }

    public static function eliminar(int $id): void
    {
        session(['proyectos.registros' => self::getProyectos()->forget($id)->all()]);
    }
}
