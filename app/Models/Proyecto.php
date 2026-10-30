<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    // Con esto simulamos los datos estáticos que se pidn
    public static function getDatosEstaticos()
    {
        return collect([
            ['Id' => 1, 'Nombre' => 'Migración de Servidores', 'Fecha_Inicio' => '2026-11-01', 'Estado' => 'Planificación', 'Responsable' => 'Juan Pérez', 'Monto' => 1500000],
            ['Id' => 2, 'Nombre' => 'Desarrollo App Móvil', 'Fecha_Inicio' => '2026-11-15', 'Estado' => 'En progreso', 'Responsable' => 'Ana Gómez', 'Monto' => 3200000],
        ]);
    }
}