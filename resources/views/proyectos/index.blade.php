<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tech Solutions - Gestión de Proyectos</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; color: #333; }
        h1 { color: #0056b3; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        .btn { padding: 8px 12px; text-decoration: none; color: white; border-radius: 4px; font-size: 14px; }
        .btn-blue { background-color: #007bff; }
        .btn-green { background-color: #28a745; margin-bottom: 15px; display: inline-block; }
        .btn-red { background-color: #dc3545; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Gestión de Proyectos - Tech Solutions</h1>
    
    <!-- Este sería el componente reutilizable que consume el servicio externo de la UF -->
    <x-uf-widget />

    <a href="{{ route('proyectos.create') }}" class="btn btn-green">Agregar Nuevo Proyecto</a>

    <table>
        <thead>
            <tr>
                <th>Id</th>
                <th>Nombre</th>
                <th>Fecha Inicio</th>
                <th>Estado</th>
                <th>Responsable</th>
                <th>Monto</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($proyectos as $proyecto)
            <tr>
                <td>{{ $proyecto['Id'] }}</td>
                <td>{{ $proyecto['Nombre'] }}</td>
                <td>{{ $proyecto['Fecha_Inicio'] }}</td>
                <td>{{ $proyecto['Estado'] }}</td>
                <td>{{ $proyecto['Responsable'] }}</td>
                <td>${{ number_format($proyecto['Monto'], 0, ',', '.') }}</td>
                <td>
                    <a href="{{ route('proyectos.show', $proyecto['Id']) }}" class="btn btn-blue">Ver</a>
                    <a href="{{ route('proyectos.edit', $proyecto['Id']) }}" class="btn btn-blue">Editar</a>
                    <form action="{{ route('proyectos.destroy', $proyecto['Id']) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-red">Eliminar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>