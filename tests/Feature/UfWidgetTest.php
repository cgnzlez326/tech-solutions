<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UfWidgetTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['app.key' => 'base64:'.base64_encode(random_bytes(32))]);
    }

    public function test_it_renders_the_uf_for_today_in_chile(): void
    {
        Http::fake(['mindicador.cl/*' => Http::response(['serie' => [[
            'fecha' => now('America/Santiago')->toIso8601String(),
            'valor' => 39876.54,
        ]]])]);

        $this->get('/proyectos')
            ->assertOk()
            ->assertSee('UF del '.now('America/Santiago')->format('d-m-Y'))
            ->assertSee('dato de hoy en Chile')
            ->assertSee('$39.876,54');

        Http::assertSent(fn ($request) => $request->url() === 'https://mindicador.cl/api/uf');
    }

    public function test_it_marks_an_old_uf_value_with_its_actual_date(): void
    {
        Http::fake(['mindicador.cl/*' => Http::response(['serie' => [[
            'fecha' => '2000-01-01T03:00:00.000Z',
            'valor' => 123,
        ]]])]);

        $this->get('/proyectos')
            ->assertOk()
            ->assertSee('del 01-01-2000')
            ->assertSee('registro de otra fecha; no corresponde al día de Chile');
    }

    public function test_it_uses_the_chile_day_when_the_api_timestamp_is_utc(): void
    {
        $this->travelTo(new \DateTimeImmutable('2026-05-01T02:30:00Z'));
        Http::fake(['mindicador.cl/*' => Http::response(['serie' => [[
            'fecha' => '2026-05-01T02:00:00Z',
            'valor' => 123,
        ]]])]);

        $this->get('/proyectos')
            ->assertOk()
            ->assertSee('UF del 30-04-2026')
            ->assertSee('dato de hoy en Chile');

        $this->travelBack();
    }

    public function test_it_rejects_a_non_numeric_value(): void
    {
        $this->assertUnavailable(['fecha' => now('America/Santiago')->toIso8601String(), 'valor' => ['valor' => 123]]);
    }

    public function test_it_rejects_a_non_finite_or_non_positive_value(): void
    {
        $this->assertUnavailable(['fecha' => now('America/Santiago')->toIso8601String(), 'valor' => '1e309']);
        $this->assertUnavailable(['fecha' => now('America/Santiago')->toIso8601String(), 'valor' => -1]);
        $this->assertUnavailable(['fecha' => now('America/Santiago')->toIso8601String(), 'valor' => 0]);
    }

    public function test_it_rejects_an_invalid_date(): void
    {
        $this->assertUnavailable(['fecha' => '2000-02-30T03:00:00.000Z', 'valor' => 123]);
    }

    public function test_it_rejects_an_empty_series(): void
    {
        Http::fake(['mindicador.cl/*' => Http::response(['serie' => []])]);

        $this->get('/proyectos')->assertOk()->assertSee('No disponible temporalmente');
    }

    public function test_it_shows_unavailable_when_the_service_fails(): void
    {
        Http::fake(['mindicador.cl/*' => Http::response([], 503)]);

        $this->get('/proyectos')->assertOk()->assertSee('No disponible temporalmente');
    }

    public function test_it_shows_unavailable_when_the_service_connection_fails(): void
    {
        Http::fake(['mindicador.cl/*' => Http::failedConnection()]);

        $this->get('/proyectos')->assertOk()->assertSee('No disponible temporalmente');
    }

    private function assertUnavailable(array $registro): void
    {
        Http::fake(['mindicador.cl/*' => Http::response(['serie' => [$registro]])]);

        $this->get('/proyectos')->assertOk()->assertSee('No disponible temporalmente');
    }
}
