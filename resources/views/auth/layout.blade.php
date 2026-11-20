<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo') - Tech Solutions</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; color: #333; }
        main { max-width: 400px; margin: 0 auto; }
        h1 { color: #0056b3; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input { width: 100%; padding: 8px; box-sizing: border-box; }
        button { background-color: #0056b3; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <main>
        <h1>@yield('titulo')</h1>

        @if (session('success'))
            <p role="status">{{ session('success') }}</p>
        @endif

        @if ($errors->any())
            <ul role="alert">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        @yield('contenido')
    </main>
</body>
</html>
