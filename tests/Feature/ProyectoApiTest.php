<?php

namespace Tests\Feature;

use App\Models\Proyecto;
use Tests\TestCase;

class ProyectoApiTest extends TestCase
{
    private const MESSAGE = 'Solicitud validada. Los datos no se modifican.';

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

    public function test_store_accepts_raw_json_without_accept_header_or_changing_static_projects(): void
    {
        $proyectos = Proyecto::getDatosEstaticos()->all();
        $datos = $this->datos();

        $this->call('POST', '/api/proyectos', [], [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($datos + ['Id' => 99], JSON_THROW_ON_ERROR))
            ->assertOk()
            ->assertHeader('content-type', 'application/json')
            ->assertExactJson(['message' => self::MESSAGE, 'datos' => $datos]);

        $this->get('/api/proyectos')->assertExactJson($proyectos);
    }

    public function test_update_returns_original_record_with_validated_payload_without_changing_static_data(): void
    {
        $original = Proyecto::getDatosEstaticos()->firstWhere('Id', 1);
        $resultado = array_merge($original, $this->datos());

        $this->put('/api/proyectos/1', $this->datos())
            ->assertOk()
            ->assertExactJson(['message' => self::MESSAGE, 'datos' => $resultado]);

        $this->get('/api/proyectos/1')->assertExactJson($original);
    }

    public function test_destroy_returns_original_record_without_changing_static_data(): void
    {
        $proyectos = Proyecto::getDatosEstaticos()->all();

        $this->delete('/api/proyectos/1')
            ->assertOk()
            ->assertExactJson(['message' => self::MESSAGE, 'datos' => $proyectos[0]]);

        $this->get('/api/proyectos')->assertExactJson($proyectos);
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
