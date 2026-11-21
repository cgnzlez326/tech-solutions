<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProyectoApiTest extends TestCase
{
    use RefreshDatabase;

    private int $usuarioId;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->usuarioId = User::factory()->create()->id;
        $this->token = Auth::guard('api')->tokenById($this->usuarioId);
        $this->withToken($this->token);
    }

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

    public function test_seeder_creates_sql_projects_without_duplicating_them(): void
    {
        $this->assertDatabaseCount('proyectos', 0);
        $this->artisan('db:seed', ['--class' => 'ProyectoSeeder'])->assertSuccessful();
        $this->artisan('db:seed', ['--class' => 'ProyectoSeeder'])->assertSuccessful();
        $this->assertDatabaseCount('proyectos', 2);

        $creadorId = User::where('email', 'proyectos@tech-solutions.example')->sole()->id;
        $this->get('/api/proyectos')->assertOk()->assertJsonCount(2);
        $this->get('/api/proyectos/2')->assertOk()->assertExactJson([
            'Id' => 2,
            'Nombre' => 'Desarrollo App Móvil',
            'Fecha_Inicio' => '2026-11-15',
            'Estado' => 'En progreso',
            'Responsable' => 'Ana Gómez',
            'Monto' => 3200000,
            'created_by' => $creadorId,
        ]);
    }

    public function test_projects_require_a_valid_bearer_token(): void
    {
        $this->withoutHeader('Authorization')->get('/api/proyectos')->assertUnauthorized();
        $this->get('/api/proyectos?token='.$this->token)->assertUnauthorized();
        $this->withToken($this->token)->get('/api/proyectos')->assertOk();
        $this->withToken('token-invalido')->get('/api/proyectos')->assertUnauthorized();

        $this->travel(config('jwt.ttl') + 1)->minutes();
        $this->withToken($this->token)->get('/api/proyectos')->assertUnauthorized();
    }

    public function test_missing_project_returns_a_json_404_without_accept_header(): void
    {
        $this->get('/api/proyectos/999')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Proyecto no encontrado.']);
    }

    public function test_api_writes_change_the_persisted_list(): void
    {
        $proyectos = $this->get('/api/proyectos')->assertExactJson([])->json();
        $proyecto = ['Id' => 1, 'created_by' => $this->usuarioId] + $this->datos();

        $this->call('POST', '/api/proyectos', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$this->token,
        ], json_encode(['Id' => 99, 'created_by' => 99] + $this->datos(), JSON_THROW_ON_ERROR))
            ->assertOk()
            ->assertHeader('content-type', 'application/json')
            ->assertExactJson(['message' => 'Proyecto creado correctamente.', 'datos' => $proyecto]);
        $this->get('/api/proyectos')->assertExactJson([...$proyectos, $proyecto]);
        $this->assertDatabaseHas('proyectos', ['id' => 1, 'created_by' => $this->usuarioId]);
        $this->get('/api/proyectos/1')->assertExactJson($proyecto);

        $proyecto['Nombre'] = 'Nombre modificado por API';

        $this->put('/api/proyectos/1', ['created_by' => 99] + $proyecto)
            ->assertOk()
            ->assertExactJson(['message' => 'Proyecto actualizado correctamente.', 'datos' => $proyecto]);
        $this->get('/api/proyectos')->assertExactJson([...$proyectos, $proyecto]);
        $this->get('/api/proyectos/1')->assertExactJson($proyecto);

        $this->delete('/api/proyectos/1')
            ->assertOk()
            ->assertExactJson(['message' => 'Proyecto eliminado correctamente.', 'datos' => $proyecto]);
        $this->get('/api/proyectos')->assertExactJson($proyectos);
        $this->get('/api/proyectos/1')->assertNotFound();
        $this->post('/api/proyectos', $this->datos())->assertJsonPath('datos.Id', 2);
    }

    public function test_an_empty_legacy_file_does_not_trigger_example_projects(): void
    {
        Storage::disk('local')->put('proyectos.json', '{"siguiente_id":3,"registros":[]}');
        (require database_path('migrations/2026_11_21_053554_importar_proyectos_json.php'))->up();

        $this->artisan('db:seed', ['--class' => 'ProyectoSeeder'])->assertSuccessful();
        $this->get('/api/proyectos')->assertContent('[]');
    }

    public function test_legacy_projects_have_no_attributed_author(): void
    {
        $json = json_encode(['siguiente_id' => 9, 'registros' => [
            ['Id' => 7] + $this->datos(),
            ['Id' => 8, 'created_by' => $this->usuarioId] + $this->datos(),
        ]], JSON_THROW_ON_ERROR);
        Storage::disk('local')->put('proyectos.json', $json);

        (require database_path('migrations/2026_11_21_053554_importar_proyectos_json.php'))->up();

        $this->get('/api/proyectos/7')->assertOk()->assertExactJson(['Id' => 7, 'created_by' => null] + $this->datos());
        $this->get('/api/proyectos/8')->assertOk()->assertJsonPath('created_by', $this->usuarioId);
        $this->post('/api/proyectos', $this->datos())->assertJsonPath('datos.Id', 9);
        $this->assertSame($json, Storage::disk('local')->get('proyectos.json'));
    }

    public function test_import_rejects_an_unreadable_json_file_without_replacing_it(): void
    {
        $archivo = Storage::disk('local');
        $archivo->put('proyectos.json', '{');

        $this->expectException(\JsonException::class);
        try {
            (require database_path('migrations/2026_11_21_053554_importar_proyectos_json.php'))->up();
        } finally {
            $this->assertDatabaseCount('proyectos', 0);
            $this->assertSame('{', $archivo->get('proyectos.json'));
        }
    }

    public function test_write_validation_errors_are_json_without_accept_header(): void
    {
        $this->seed();
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
