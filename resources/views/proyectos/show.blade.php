<!DOCTYPE html>
<html lang="es">
<head>
    <title>Detalle del Proyecto</title>
    <style>body { font-family: Arial, sans-serif; padding: 20px; }</style>
</head>
<body>
    <h1>Detalle del Proyecto: {{ $proyecto['Nombre'] }}</h1>
    <ul>
        <li><strong>Id:</strong> {{ $proyecto['Id'] }}</li>
        <li><strong>Fecha de Inicio:</strong> {{ $proyecto['Fecha_Inicio'] }}</li>
        <li><strong>Estado:</strong> {{ $proyecto['Estado'] }}</li>
        <li><strong>Responsable:</strong> {{ $proyecto['Responsable'] }}</li>
        <li><strong>Monto:</strong> ${{ number_format($proyecto['Monto'], 0, ',', '.') }}</li>
    </ul>
    <a href="{{ route('proyectos.index') }}">Volver a la lista</a>
</body>
</html>