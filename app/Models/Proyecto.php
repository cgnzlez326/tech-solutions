<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class Proyecto extends Model
{
    public static function getDatosEstaticos(?int $creadorId = null): Collection
    {
        return collect([
            ['Id' => 1, 'Nombre' => 'Migración de Servidores', 'Fecha_Inicio' => '2026-11-01', 'Estado' => 'Planificación', 'Responsable' => 'Juan Pérez', 'Monto' => 1500000, 'created_by' => $creadorId],
            ['Id' => 2, 'Nombre' => 'Desarrollo App Móvil', 'Fecha_Inicio' => '2026-11-15', 'Estado' => 'En progreso', 'Responsable' => 'Ana Gómez', 'Monto' => 3200000, 'created_by' => $creadorId],
        ]);
    }

    public static function getProyectos(): Collection
    {
        return self::conBloqueo(fn ($ruta) => collect(self::leer($ruta)['registros']));
    }

    public static function inicializarEjemplos(int $creadorId): void
    {
        self::conBloqueo(fn ($ruta) => self::leer($ruta, $creadorId));
    }

    public static function crear(array $datos, ?int $creadorId): array
    {
        return self::conBloqueo(function ($ruta) use ($datos, $creadorId) {
            $estado = self::leer($ruta);
            $id = $estado['siguiente_id']++;
            $proyecto = ['Id' => $id, 'created_by' => $creadorId] + $datos;
            $estado['registros'][$id] = $proyecto;
            self::guardar($ruta, $estado);

            return $proyecto;
        });
    }

    public static function actualizar(int $id, array $datos): ?array
    {
        return self::conBloqueo(function ($ruta) use ($id, $datos) {
            $estado = self::leer($ruta);
            $proyecto = $estado['registros'][$id] ?? null;
            if ($proyecto === null) {
                return null;
            }

            $proyecto = array_replace($proyecto, $datos);
            $estado['registros'][$id] = $proyecto;
            self::guardar($ruta, $estado);

            return $proyecto;
        });
    }

    public static function eliminar(string|int $id): ?array
    {
        return self::conBloqueo(function ($ruta) use ($id) {
            $estado = self::leer($ruta);
            $proyecto = collect($estado['registros'])->firstWhere('Id', $id);
            if ($proyecto === null) {
                return null;
            }

            unset($estado['registros'][$proyecto['Id']]);
            self::guardar($ruta, $estado);

            return $proyecto;
        });
    }

    // shortcut: almacenamiento local compartido. Retirar el JSON y ProyectoSeeder al conectar el modelo a SQL.
    private static function conBloqueo(callable $operacion): mixed
    {
        $ruta = Storage::disk('local')->path('proyectos.json');
        File::ensureDirectoryExists(dirname($ruta));
        // El bloqueo usa un archivo separado porque el JSON se reemplaza al guardar.
        $bloqueo = fopen($ruta.'.lock', 'c');
        if ($bloqueo === false) {
            throw new RuntimeException('No se pudo abrir el archivo de bloqueo de proyectos.');
        }

        try {
            if (! flock($bloqueo, LOCK_EX)) {
                throw new RuntimeException('No se pudo bloquear el archivo de proyectos.');
            }

            return $operacion($ruta);
        } finally {
            fclose($bloqueo);
        }
    }

    private static function leer(string $ruta, ?int $creadorId = null): array
    {
        clearstatcache(true, $ruta);
        if (is_file($ruta)) {
            $estado = json_decode(File::get($ruta), true, flags: JSON_THROW_ON_ERROR);
            $estado['registros'] = array_map(
                fn (array $proyecto) => $proyecto + ['created_by' => null],
                $estado['registros'],
            );

            return $estado;
        }

        $proyectos = self::getDatosEstaticos($creadorId);
        $estado = [
            'siguiente_id' => $proyectos->max('Id') + 1,
            'registros' => $proyectos->keyBy('Id')->all(),
        ];
        self::guardar($ruta, $estado);

        return $estado;
    }

    private static function guardar(string $ruta, array $estado): void
    {
        $json = json_encode($estado, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE).PHP_EOL;
        $temporal = tempnam(dirname($ruta), 'proyectos-');
        if ($temporal === false) {
            throw new RuntimeException('No se pudo preparar el archivo de proyectos.');
        }

        try {
            if (file_put_contents($temporal, $json) !== strlen($json) || ! rename($temporal, $ruta)) {
                throw new RuntimeException('No se pudo guardar el archivo de proyectos.');
            }
        } finally {
            if (is_file($temporal)) {
                unlink($temporal);
            }
        }
    }
}
