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
    <form action="{{ route('proyectos.update', $proyecto['Id']) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>Nombre del Proyecto:</label>
            <input type="text" name="Nombre" value="{{ $proyecto['Nombre'] }}" required>
        </div>
        <div class="form-group">
            <label>Estado:</label>
            <input type="text" name="Estado" value="{{ $proyecto['Estado'] }}" required>
        </div>
        <div class="form-group">
            <label>Monto:</label>
            <input type="number" name="Monto" value="{{ $proyecto['Monto'] }}" required>
        </div>
        <button type="submit" class="btn-green">Actualizar Proyecto</button>
        <a href="{{ route('proyectos.index') }}">Cancelar</a>
    </form>
</body>
</html>