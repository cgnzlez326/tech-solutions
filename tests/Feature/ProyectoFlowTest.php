<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProyectoFlowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Http::fake(['*' => Http::response(['serie' => [['fecha' => '2026-10-30', 'valor' => 40000]]])]);
    }

    public function test_missing_project_ids_return_404_for_project_actions(): void
    {
        $this->get('/proyectos/2')->assertOk()->assertSee('Desarrollo App Móvil');
        $this->get('/proyectos/999')->assertNotFound();
        $this->get('/proyectos/999/editar')->assertNotFound();
        $this->get('/proyectos/999/eliminar')->assertNotFound();
        $this->put('/proyectos/999', [])->assertNotFound();
        $this->delete('/proyectos/999')->assertNotFound();
    }

    public function test_delete_confirmation_uses_the_requested_project_id(): void
    {
        $this->get(route('proyectos.index'))
            ->assertOk()
            ->assertSee('href="'.route('proyectos.delete', 2).'"', false);

        $this->get('/proyectos/2/eliminar')
            ->assertOk()
            ->assertSee('Proyecto: Desarrollo App Móvil')
            ->assertSee('action="'.route('proyectos.destroy', 2).'"', false);
    }

    public function test_project_can_be_created_viewed_renamed_and_deleted_in_one_session(): void
    {
        $newProject = [
            'Nombre' => 'Proyecto de prueba',
            'Fecha_Inicio' => '2026-12-01',
            'Estado' => 'Planificación',
            'Responsable' => 'Camilo',
            'Monto' => 250000,
        ];

        $cookie = config('session.cookie');
        $session = $this->get('/proyectos')->assertOk()->getCookie($cookie, false);
        $this->withUnencryptedCookie($cookie, $session->getValue());

        $this->post('/proyectos', $newProject)
            ->assertRedirect(route('proyectos.index'))
            ->assertSessionHas('success', 'Proyecto creado correctamente.');
        $this->get('/proyectos')
            ->assertOk()
            ->assertSee('role="status"', false)
            ->assertSee('Proyecto creado correctamente.')
            ->assertSee($newProject['Nombre'])
            ->assertSee(route('proyectos.show', 3));
        $this->get('/proyectos/3')->assertOk()->assertSee($newProject['Nombre']);
        $this->get('/proyectos/3/editar')->assertOk()->assertSee($newProject['Nombre']);
        $this->get('/api/proyectos/3')->assertOk()->assertJsonPath('Nombre', $newProject['Nombre']);

        $renamed = ['Nombre' => 'Nombre modificado'] + $newProject;
        $this->put('/proyectos/3', $renamed)
            ->assertRedirect(route('proyectos.index'))
            ->assertSessionHas('success', 'Proyecto actualizado correctamente.');
        $this->get('/proyectos')->assertOk()->assertSee($renamed['Nombre'])->assertDontSee($newProject['Nombre']);
        $this->get('/proyectos/3')->assertOk()->assertSee($renamed['Nombre']);

        $this->get('/proyectos/3/eliminar')->assertOk()->assertSee($renamed['Nombre']);
        $this->delete('/proyectos/3')
            ->assertRedirect(route('proyectos.index'))
            ->assertSessionHas('success', 'Proyecto eliminado correctamente.');

        $this->get('/proyectos')
            ->assertOk()
            ->assertSee('Migración de Servidores')
            ->assertSee('Desarrollo App Móvil')
            ->assertDontSee($renamed['Nombre']);
        $this->get('/proyectos/3')->assertNotFound();
    }

    public function test_invalid_project_data_is_rejected_for_create_and_update(): void
    {
        $this->from('/proyectos/crear')->post('/proyectos', [
            'Nombre' => 'Proyecto inválido',
            'Fecha_Inicio' => 'fecha-invalida',
            'Estado' => '',
            'Responsable' => '',
            'Monto' => -1,
        ])->assertSessionHasErrors(['Fecha_Inicio', 'Estado', 'Responsable', 'Monto']);

        $this->from('/proyectos/1/editar')->put('/proyectos/1', [
            'Nombre' => 'Proyecto válido',
            'Fecha_Inicio' => '2026-12-01',
            'Estado' => 'En progreso',
            'Responsable' => 'Camilo',
            'Monto' => -10,
        ])->assertSessionHasErrors('Monto');
    }

    public function test_invalid_forms_show_errors_and_restore_submitted_values(): void
    {
        $this->from('/proyectos/crear')->followingRedirects()->post('/proyectos', [
            'Nombre' => 'Proyecto inválido',
            'Fecha_Inicio' => 'fecha-invalida',
            'Estado' => '',
            'Responsable' => '',
            'Monto' => -1,
        ])
            ->assertOk()
            ->assertSee('Corrige los siguientes campos')
            ->assertSee('value="Proyecto inválido"', false);

        $this->from('/proyectos/1/editar')->followingRedirects()->put('/proyectos/1', [
            'Nombre' => 'Nombre conservado',
            'Fecha_Inicio' => '2026-12-01',
            'Estado' => 'En progreso',
            'Responsable' => 'Camilo',
            'Monto' => -10,
        ])
            ->assertOk()
            ->assertSee('Corrige los siguientes campos')
            ->assertSee('value="Nombre conservado"', false)
            ->assertSee('value="2026-12-01"', false)
            ->assertSee('value="Camilo"', false)
            ->assertSee('value="-10"', false);
    }

    public function test_create_and_edit_fields_have_associated_labels(): void
    {
        $this->get('/proyectos/crear')
            ->assertOk()
            ->assertSee('for="nombre"', false)
            ->assertSee('id="nombre"', false)
            ->assertSee('for="fecha-inicio"', false)
            ->assertSee('id="fecha-inicio"', false);

        $this->get('/proyectos/1/editar')
            ->assertOk()
            ->assertSee('for="fecha-inicio"', false)
            ->assertSee('id="fecha-inicio"', false)
            ->assertSee('for="responsable"', false)
            ->assertSee('id="responsable"', false)
            ->assertSee('for="monto"', false)
            ->assertSee('id="monto"', false);
    }
}
