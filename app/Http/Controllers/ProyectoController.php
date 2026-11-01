<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyecto;

class ProyectoController extends Controller
{
    public function index(Request $request) {
        $proyectos = Proyecto::getDatosEstaticos();

        if ($request->is('api/*')) {
            return response()->json($proyectos);
        }

        return view('proyectos.index', compact('proyectos'));
    }

    public function create() {
        return view('proyectos.create');
    }

    public function store(Request $request) {
        $datos = $request->validate([
            'Nombre' => ['required', 'string', 'max:255'],
            'Fecha_Inicio' => ['required', 'date'],
            'Estado' => ['required', 'string', 'max:255'],
            'Responsable' => ['required', 'string', 'max:255'],
            'Monto' => ['required', 'numeric', 'min:0'],
        ]);
        $message = 'Solicitud validada. Los datos no se modifican.';

        return $request->is('api/*')
            ? response()->json(['message' => $message, 'datos' => $datos])
            : redirect()->route('proyectos.index')->with('success', $message);
    }

    public function show(Request $request, $id) {
        $proyecto = Proyecto::getDatosEstaticos()->firstWhere('Id', $id);
        abort_unless($proyecto, 404, 'Proyecto no encontrado.');

        if ($request->is('api/*')) {
            return response()->json($proyecto);
        }

        return view('proyectos.show', compact('proyecto'));
    }

    public function edit($id) {
        $proyecto = Proyecto::getDatosEstaticos()->firstWhere('Id', $id);
        abort_unless($proyecto, 404);

        return view('proyectos.edit', compact('proyecto'));
    }

    public function delete($id) {
        $proyecto = Proyecto::getDatosEstaticos()->firstWhere('Id', $id);
        abort_unless($proyecto, 404);

        return view('proyectos.delete', compact('proyecto'));
    }

    public function update(Request $request, $id) {
        $proyecto = Proyecto::getDatosEstaticos()->firstWhere('Id', $id);
        abort_unless($proyecto, 404, 'Proyecto no encontrado.');

        $datos = $request->validate([
            'Nombre' => ['required', 'string', 'max:255'],
            'Fecha_Inicio' => ['required', 'date'],
            'Estado' => ['required', 'string', 'max:255'],
            'Responsable' => ['required', 'string', 'max:255'],
            'Monto' => ['required', 'numeric', 'min:0'],
        ]);
        $message = 'Solicitud validada. Los datos no se modifican.';

        return $request->is('api/*')
            ? response()->json(['message' => $message, 'datos' => array_merge($proyecto, $datos)])
            : redirect()->route('proyectos.index')->with('success', $message);
    }

    public function destroy(Request $request, $id) {
        $proyecto = Proyecto::getDatosEstaticos()->firstWhere('Id', $id);
        abort_unless($proyecto, 404, 'Proyecto no encontrado.');
        $message = 'Solicitud validada. Los datos no se modifican.';

        return $request->is('api/*')
            ? response()->json(['message' => $message, 'datos' => $proyecto])
            : redirect()->route('proyectos.index')->with('success', $message);
    }
}
