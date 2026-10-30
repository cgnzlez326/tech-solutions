<!DOCTYPE html>
<html lang="es">
<head>
    <title>Editar Proyecto</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input { width: 100%; padding: 8px; box-sizing: border-box; }
        .btn-green { background-color: #28a745; color: white; padding: 10px 15px; border: none; border-radius: 3px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Actualizar Proyecto: {{ $proyecto['Nombre'] }}</h1>
    @if ($errors->any())
        <div role="alert">
            <p>Corrige los siguientes campos:</p>
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif
    <form action="{{ route('proyectos.update', $proyecto['Id']) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="nombre">Nombre del Proyecto:</label>
            <input id="nombre" type="text" name="Nombre" value="{{ old('Nombre', $proyecto['Nombre']) }}" required>
        </div>
        <div class="form-group">
            <label for="estado">Estado:</label>
            <input id="estado" type="text" name="Estado" value="{{ old('Estado', $proyecto['Estado']) }}" required>
        </div>
        <div class="form-group">
            <label for="monto">Monto:</label>
            <input id="monto" type="number" name="Monto" value="{{ old('Monto', $proyecto['Monto']) }}" min="0" step="any" required>
        </div>
        <button type="submit" class="btn-green">Actualizar Proyecto</button>
        <a href="{{ route('proyectos.index') }}">Cancelar</a>
    </form>
</body>
</html>
