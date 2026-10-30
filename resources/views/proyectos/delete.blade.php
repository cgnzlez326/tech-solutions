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
    <h1>¿Estás seguro de que deseas eliminar este proyecto?</h1>
    <p>Esta acción no se puede deshacer.</p>
    
    <form action="{{ route('proyectos.destroy', 1) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-red">Sí, Eliminar Proyecto</button>
        <a href="{{ route('proyectos.index') }}">Cancelar</a>
    </form>
</body>
</html>