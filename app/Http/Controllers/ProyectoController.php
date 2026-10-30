<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyecto;

class ProyectoController extends Controller
{
    public function index() {
        $proyectos = Proyecto::getDatosEstaticos();
        return view('proyectos.index', compact('proyectos'));
    }

    public function create() {
        return view('proyectos.create');
    }

    public function store(Request $request) {
        $request->validate([
            'Nombre' => ['required', 'string', 'max:255'],
            'Fecha_Inicio' => ['required', 'date'],
            'Estado' => ['required', 'string', 'max:255'],
            'Responsable' => ['required', 'string', 'max:255'],
            'Monto' => ['required', 'numeric', 'min:0'],
        ]);

        return redirect()->route('proyectos.index')->with('success', 'Creación simulada: los datos no se guardaron.');
    }

    public function show($id) {
        $proyecto = Proyecto::getDatosEstaticos()->firstWhere('Id', $id);
        abort_unless($proyecto, 404);

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
        abort_unless(Proyecto::getDatosEstaticos()->firstWhere('Id', $id), 404);

        $request->validate([
            'Nombre' => ['required', 'string', 'max:255'],
            'Estado' => ['required', 'string', 'max:255'],
            'Monto' => ['required', 'numeric', 'min:0'],
        ]);

        return redirect()->route('proyectos.index')->with('success', 'Actualización simulada: los datos no se guardaron.');
    }

    public function destroy($id) {
        abort_unless(Proyecto::getDatosEstaticos()->firstWhere('Id', $id), 404);

        return redirect()->route('proyectos.index')->with('success', 'Eliminación simulada: los datos no se eliminaron.');
    }
}
