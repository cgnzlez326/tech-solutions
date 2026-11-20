@extends('auth.layout')

@section('titulo', 'Iniciar sesión')

@section('contenido')
    <form action="{{ route('login') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="email">Correo</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required>
        </div>
        <div class="form-group">
            <label for="password">Contraseña</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>
        </div>
        <button type="submit">Iniciar sesión</button>
    </form>
    <p><a href="{{ route('registro') }}">Crear una cuenta</a></p>
@endsection
