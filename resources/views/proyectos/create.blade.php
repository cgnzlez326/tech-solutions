<!DOCTYPE html>
<html lang="es">
<head>
    <title>Crear Proyecto</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input { width: 100%; padding: 8px; box-sizing: border-box; }
        .btn-blue { background-color: #007bff; color: white; padding: 10px 15px; text-decoration: none; border: none; border-radius: 3px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Agregar Nuevo Proyecto</h1>
    @if ($errors->any())
        <div role="alert">
            <p>Corrige los siguientes campos:</p>
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif
    <form action="{{ route('proyectos.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="nombre">Nombre del Proyecto:</label>
            <input id="nombre" type="text" name="Nombre" value="{{ old('Nombre') }}" required>
        </div>
        <div class="form-group">
            <label for="fecha-inicio">Fecha de Inicio:</label>
            <input id="fecha-inicio" type="date" name="Fecha_Inicio" value="{{ old('Fecha_Inicio') }}" required>
        </div>
        <div class="form-group">
            <label for="estado">Estado:</label>
            <input id="estado" type="text" name="Estado" value="{{ old('Estado') }}" required>
        </div>
        <div class="form-group">
            <label for="responsable">Responsable:</label>
            <input id="responsable" type="text" name="Responsable" value="{{ old('Responsable') }}" required>
        </div>
        <div class="form-group">
            <label for="monto">Monto:</label>
            <input id="monto" type="number" name="Monto" value="{{ old('Monto') }}" min="0" step="any" required>
        </div>
        <button type="submit" class="btn-blue">Guardar Proyecto</button>
        <a href="{{ route('proyectos.index') }}">Cancelar</a>
    </form>
</body>
</html>
