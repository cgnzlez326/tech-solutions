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
    <form action="{{ route('proyectos.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Nombre del Proyecto:</label>
            <input type="text" name="Nombre" required>
        </div>
        <div class="form-group">
            <label>Fecha de Inicio:</label>
            <input type="date" name="Fecha_Inicio" required>
        </div>
        <div class="form-group">
            <label>Estado:</label>
            <input type="text" name="Estado" required>
        </div>
        <div class="form-group">
            <label>Responsable:</label>
            <input type="text" name="Responsable" required>
        </div>
        <div class="form-group">
            <label>Monto:</label>
            <input type="number" name="Monto" required>
        </div>
        <button type="submit" class="btn-blue">Guardar Proyecto</button>
        <a href="{{ route('proyectos.index') }}">Cancelar</a>
    </form>
</body>
</html>