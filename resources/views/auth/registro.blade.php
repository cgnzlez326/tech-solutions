@extends('auth.layout')

@section('titulo', 'Crear una cuenta')

@section('contenido')
    <form action="{{ route('registro') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Nombre</label>
            <input id="name" name="name" value="{{ old('name') }}" autocomplete="name" maxlength="255" required>
        </div>
        <div class="form-group">
            <label for="email">Correo</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" maxlength="255" required>
        </div>
        <div class="form-group">
            <label for="password">Contraseña (al menos 8 caracteres)</label>
            <input id="password" name="password" type="password" autocomplete="new-password" minlength="8" required>
        </div>
        <button type="submit">Registrarse</button>
    </form>
    <p><a href="{{ route('login') }}">Iniciar sesión</a></p>
@endsection
