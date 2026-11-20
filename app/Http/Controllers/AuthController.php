<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private const REGLAS_CORREO = ['required', 'string', 'ascii', 'email', 'max:255'];

    private const FORMATO_CLAVE = 'regex:/\A[^\x00]{1,72}\z/';

    public function registro(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => self::REGLAS_CORREO,
            'password' => ['required', 'string', 'min:8', self::FORMATO_CLAVE],
        ]);
        $datos['email'] = strtolower($datos['email']);
        $datos['password'] = Hash::make($datos['password']);

        try {
            $usuario = User::create($datos);
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['email' => 'Este correo ya está registrado.']);
        }

        return response()->json(['message' => 'Usuario registrado correctamente.', 'user' => $usuario], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'email' => self::REGLAS_CORREO,
            'password' => ['required', 'string', self::FORMATO_CLAVE],
        ]);

        $guard = Auth::guard('api');
        $token = $guard->attempt([
            'email_normalized' => strtolower($datos['email']),
            'password' => $datos['password'],
        ]);

        abort_if($token === false, 401, 'Credenciales incorrectas.');

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $guard->getTTL() * 60,
        ])->header('Cache-Control', 'no-store');
    }
}
