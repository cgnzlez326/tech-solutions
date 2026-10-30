<!DOCTYPE html>
<html lang="es">
<head>
    <title>Eliminar Proyecto</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; text-align: center; }
        .btn-red { background-color: #dc3545; color: white; padding: 10px 15px; border: none; border-radius: 3px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Confirmar eliminación: {{ $proyecto['Nombre'] }}</h1>
    <p>Esta demostración es estática; el proyecto no se eliminará realmente.</p>
    
    <form action="{{ route('proyectos.destroy', $proyecto['Id']) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-red">Sí, Eliminar Proyecto</button>
        <a href="{{ route('proyectos.index') }}">Cancelar</a>
    </form>
</body>
</html>
