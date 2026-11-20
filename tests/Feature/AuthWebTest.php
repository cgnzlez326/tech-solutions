<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_guest_can_create_an_account_sign_in_and_sign_out(): void
    {
        $this->get('/proyectos')->assertRedirect(route('login'));
        $this->get('/registro')->assertOk()->assertSee('Registrarse');

        $credenciales = ['email' => 'ana@example.com', 'password' => 'clave-segura'];
        $this->post('/registro', ['name' => 'Ana'] + $credenciales)
            ->assertRedirect(route('login'))
            ->assertSessionHas('success', 'Usuario registrado correctamente.');

        $this->post('/login', $credenciales)->assertRedirect(route('proyectos.index'));
        $this->assertAuthenticatedAs(User::sole(), 'web');
        $this->get('/proyectos/1')->assertOk();

        $this->post('/logout')->assertRedirect(route('login'));
        $this->assertGuest('web');
    }

    public function test_invalid_credentials_show_an_error_on_the_login_form(): void
    {
        $usuario = User::factory()->create();

        $this->from('/login')->followingRedirects()->post('/login', [
            'email' => $usuario->email,
            'password' => 'clave-equivocada',
        ])->assertOk()->assertSee('Credenciales incorrectas.');
        $this->assertGuest('web');
    }
}
