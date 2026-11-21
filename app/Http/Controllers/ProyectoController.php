<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProyectoController extends Controller
{
    public function index(Request $request) {
        $proyectos = Proyecto::orderBy('id')->get()->map->datos();

        if ($request->is('api/*')) {
            return response()->json($proyectos);
        }

        return view('proyectos.index', compact('proyectos'));
    }

    public function create() {
        return view('proyectos.create');
    }

    public function store(Request $request) {
        $proyecto = Proyecto::create($this->validarDatos($request) + [
            'created_by' => $request->user()->getAuthIdentifier(),
        ])->datos();
        $message = 'Proyecto creado correctamente.';

        return $request->is('api/*')
            ? response()->json(['message' => $message, 'datos' => $proyecto])
            : redirect()->route('proyectos.index')->with('success', $message);
    }

    public function show(Request $request, $id) {
        $proyecto = $this->buscarProyecto($id)->datos();

        if ($request->is('api/*')) {
            return response()->json($proyecto);
        }

        return view('proyectos.show', compact('proyecto'));
    }

    public function edit($id) {
        $proyecto = $this->buscarProyecto($id)->datos();

        return view('proyectos.edit', compact('proyecto'));
    }

    public function delete($id) {
        $proyecto = $this->buscarProyecto($id)->datos();

        return view('proyectos.delete', compact('proyecto'));
    }

    public function update(Request $request, $id) {
        $proyecto = DB::transaction(function () use ($id, $request) {
            $registro = $this->buscarProyecto($id, bloquear: true);
            $registro->update($this->validarDatos($request));

            return $registro->datos();
        });
        $message = 'Proyecto actualizado correctamente.';

        return $request->is('api/*')
            ? response()->json(['message' => $message, 'datos' => $proyecto])
            : redirect()->route('proyectos.index')->with('success', $message);
    }

    public function destroy(Request $request, $id) {
        $proyecto = DB::transaction(function () use ($id) {
            $registro = $this->buscarProyecto($id, bloquear: true);
            $registro->delete();

            return $registro->datos();
        });
        $message = 'Proyecto eliminado correctamente.';

        return $request->is('api/*')
            ? response()->json(['message' => $message, 'datos' => $proyecto])
            : redirect()->route('proyectos.index')->with('success', $message);
    }

    private function buscarProyecto(string $id, bool $bloquear = false): Proyecto
    {
        abort_unless(ctype_digit($id), 404, 'Proyecto no encontrado.');
        $query = Proyecto::whereKey($id);
        $proyecto = $bloquear ? $query->lockForUpdate()->first() : $query->first();
        abort_unless($proyecto, 404, 'Proyecto no encontrado.');

        return $proyecto;
    }

    private function validarDatos(Request $request): array {
        $datos = $request->validate([
            'Nombre' => ['required', 'string', 'max:255'],
            'Fecha_Inicio' => ['required', 'date'],
            'Estado' => ['required', 'string', 'max:255'],
            'Responsable' => ['required', 'string', 'max:255'],
            'Monto' => ['required', 'numeric', 'min:0', 'max:'.PHP_FLOAT_MAX],
        ]);

        return [
            'nombre' => $datos['Nombre'],
            'fecha_inicio' => Carbon::parse($datos['Fecha_Inicio'])->toDateString(),
            'estado' => $datos['Estado'],
            'responsable' => $datos['Responsable'],
            'monto' => $datos['Monto'],
        ];
    }
}
