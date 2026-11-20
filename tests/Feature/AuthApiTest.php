<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_registra_un_usuario_y_devuelve_un_jwt_al_iniciar_sesion(): void
    {
        $this->post('/api/registro', [
            'name' => 'Ana Gómez',
            'email' => 'Ana@Ejemplo.com',
            'password' => 'clave-segura',
        ])->assertCreated()
            ->assertJsonPath('user.email', 'ana@ejemplo.com')
            ->assertJsonMissingPath('user.password');

        $this->assertTrue(Hash::check('clave-segura', User::sole()->password));

        $login = $this->post('/api/login', ['email' => 'ANA@ejemplo.com', 'password' => 'clave-segura']);
        $login->assertOk()->assertJsonStructure(['access_token', 'token_type', 'expires_in']);
        $this->assertCount(3, explode('.', $login->json('access_token')));
    }

    public function test_rechaza_correos_duplicados_y_credenciales_incorrectas(): void
    {
        User::factory()->create(['email' => 'Ana@ejemplo.com']);

        $this->post('/api/registro', [
            'name' => 'Ana',
            'email' => 'ana@ejemplo.com',
            'password' => 'otra-clave',
        ])->assertUnprocessable()->assertJsonValidationErrors('email');

        $this->post('/api/login', ['email' => 'ana@ejemplo.com', 'password' => 'otra-clave'])
            ->assertUnauthorized();
    }
}
