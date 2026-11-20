<?php

namespace Tests\Feature;

use App\Models\Proyecto;
use Tests\TestCase;

class ProyectoApiTest extends TestCase
{
    private function datos(): array
    {
        return [
            'Nombre' => 'Proyecto API',
            'Fecha_Inicio' => '2026-12-01',
            'Estado' => 'Planificación',
            'Responsable' => 'Camilo',
            'Monto' => 250000,
        ];
    }

    public function test_index_and_show_return_static_project_json_without_accept_header(): void
    {
        $proyectos = Proyecto::getDatosEstaticos()->all();

        $this->get('/api/proyectos')->assertOk()->assertExactJson($proyectos);
        $this->get('/api/proyectos/2')->assertOk()->assertExactJson($proyectos[1]);
    }

    public function test_missing_project_returns_a_json_404_without_accept_header(): void
    {
        $this->get('/api/proyectos/999')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Proyecto no encontrado.']);
    }

    public function test_api_writes_change_the_list_in_the_same_session(): void
    {
        $proyectos = Proyecto::getDatosEstaticos()->all();
        $proyecto = ['Id' => 3] + $this->datos();

        $cookie = config('session.cookie');
        $session = $this->get('/api/proyectos')->assertOk()->getCookie($cookie, false);
        $this->withUnencryptedCookie($cookie, $session->getValue());

        $this->call('POST', '/api/proyectos', [], $this->prepareCookiesForRequest(), [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['Id' => 99] + $this->datos(), JSON_THROW_ON_ERROR))
            ->assertOk()
            ->assertHeader('content-type', 'application/json')
            ->assertExactJson(['message' => 'Proyecto creado correctamente.', 'datos' => $proyecto]);
        $this->get('/api/proyectos')->assertExactJson([...$proyectos, $proyecto]);
        $this->get('/api/proyectos/3')->assertExactJson($proyecto);

        $proyecto['Nombre'] = 'Nombre modificado por API';

        $this->put('/api/proyectos/3', $proyecto)
            ->assertOk()
            ->assertExactJson(['message' => 'Proyecto actualizado correctamente.', 'datos' => $proyecto]);
        $this->get('/api/proyectos')->assertExactJson([...$proyectos, $proyecto]);
        $this->get('/api/proyectos/3')->assertExactJson($proyecto);

        $this->delete('/api/proyectos/3')
            ->assertOk()
            ->assertExactJson(['message' => 'Proyecto eliminado correctamente.', 'datos' => $proyecto]);
        $this->get('/api/proyectos')->assertExactJson($proyectos);
        $this->get('/api/proyectos/3')->assertNotFound();
        $this->post('/api/proyectos', $this->datos())->assertJsonPath('datos.Id', 4);
    }

    public function test_empty_list_stays_empty_for_its_session_only(): void
    {
        $proyectos = Proyecto::getDatosEstaticos()->all();

        $cookie = config('session.cookie');
        $session = $this->get('/api/proyectos')->assertOk()->getCookie($cookie, false);
        $this->withUnencryptedCookie($cookie, $session->getValue());

        $this->delete('/api/proyectos/1')->assertOk();
        $this->get('/api/proyectos')->assertExactJson([$proyectos[1]]);
        $this->delete('/api/proyectos/2')->assertOk();
        $this->get('/api/proyectos')->assertContent('[]');

        $this->refreshApplication();
        $this->call('GET', '/api/proyectos')->assertExactJson($proyectos);
    }

    public function test_write_validation_errors_are_json_without_accept_header(): void
    {
        $datosInvalidos = [
            'Nombre' => 'Proyecto inválido',
            'Fecha_Inicio' => 'fecha-inválida',
            'Estado' => '',
            'Responsable' => '',
            'Monto' => -1,
        ];

        $this->post('/api/proyectos', $datosInvalidos)
            ->assertUnprocessable()
            ->assertJsonStructure(['message', 'errors' => ['Fecha_Inicio', 'Estado', 'Responsable', 'Monto']]);

        $this->put('/api/proyectos/1', $datosInvalidos)
            ->assertUnprocessable()
            ->assertJsonStructure(['message', 'errors' => ['Fecha_Inicio', 'Estado', 'Responsable', 'Monto']]);
    }

    public function test_update_and_destroy_of_missing_project_return_json_404(): void
    {
        $this->put('/api/proyectos/999', $this->datos())
            ->assertNotFound()
            ->assertExactJson(['message' => 'Proyecto no encontrado.']);

        $this->delete('/api/proyectos/999')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Proyecto no encontrado.']);
    }
}
