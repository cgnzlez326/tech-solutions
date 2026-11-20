<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateJwt
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        if ($token === null || $token === '') {
            throw new AuthenticationException('Usuario no autenticado.');
        }

        $guard = Auth::guard('api');
        $guard->forgetUser();

        try {
            $user = $guard->setToken($token)->user();
        } catch (JWTException) {
            $user = null;
        }

        if ($user === null) {
            throw new AuthenticationException('Usuario no autenticado.');
        }

        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
