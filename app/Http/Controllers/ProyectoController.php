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
        return redirect()->route('proyectos.index')->with('success', 'Proyecto creado simuladamente');
    }

    public function show($id) {
        $proyecto = Proyecto::getDatosEstaticos()->firstWhere('Id', $id);
        return view('proyectos.show', compact('proyecto'));
    }

    public function edit($id) {
        $proyecto = Proyecto::getDatosEstaticos()->firstWhere('Id', $id);
        return view('proyectos.edit', compact('proyecto'));
    }

    public function update(Request $request, $id) {
        return redirect()->route('proyectos.index')->with('success', 'Proyecto actualizado simuladamente');
    }

    public function destroy($id) {
        return redirect()->route('proyectos.index')->with('success', 'Proyecto eliminado simuladamente');
    }
}