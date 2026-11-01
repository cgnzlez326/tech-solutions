<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UfWidgetTest extends TestCase
{
    public function test_it_renders_the_uf_for_today_in_chile(): void
    {
        $this->travelTo(new \DateTimeImmutable('2026-05-01T02:30:00Z'));
        $hoy = now('America/Santiago');

        Http::fake(['mindicador.cl/*' => Http::response(['serie' => [[
            'fecha' => $hoy->toIso8601String(),
            'valor' => 39876.54,
        ]]])]);

        $this->get('/proyectos')
            ->assertOk()
            ->assertSee('UF del '.$hoy->format('d-m-Y'))
            ->assertDontSee('dato de hoy en Chile')
            ->assertSee('$39.876,54');

        Http::assertSent(fn ($request) => $request->url() === 'https://mindicador.cl/api/uf');
        $this->travelBack();
    }

    public function test_it_does_not_show_an_old_uf_value_as_today(): void
    {
        Http::fake(['mindicador.cl/*' => Http::response(['serie' => [[
            'fecha' => '2000-01-01T03:00:00.000Z',
            'valor' => 123,
        ]]])]);

        $this->get('/proyectos')
            ->assertOk()
            ->assertSee('UF no disponible para hoy')
            ->assertDontSee('$123,00');
    }

    public function test_it_does_not_show_a_future_uf_value_as_today(): void
    {
        $this->assertUnavailable(['serie' => [[
            'fecha' => now('America/Santiago')->addDay()->toIso8601String(),
            'valor' => 123,
        ]]]);
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
            ->assertSee('$123,00')
            ->assertDontSee('UF no disponible para hoy');

        $this->travelBack();
    }

    public function test_it_rejects_missing_series_or_fields(): void
    {
        $fecha = now('America/Santiago')->toIso8601String();

        foreach ([[], ['serie' => []], ['serie' => 'invalid'], ['serie' => [['fecha' => $fecha]]], ['serie' => [['valor' => 123]]]] as $payload) {
            $this->assertUnavailable($payload);
        }
    }

    public function test_it_rejects_invalid_value_or_date_types(): void
    {
        $fecha = now('America/Santiago')->toIso8601String();

        $this->assertUnavailable(['serie' => [['fecha' => $fecha, 'valor' => ['valor' => 123]]]]);
        $this->assertUnavailable(['serie' => [['fecha' => 123, 'valor' => 123]]]);
    }

    public function test_it_rejects_a_non_finite_or_non_positive_value(): void
    {
        $fecha = now('America/Santiago')->toIso8601String();

        $this->assertUnavailable(['serie' => [['fecha' => $fecha, 'valor' => '1e309']]]);
        $this->assertUnavailable(['serie' => [['fecha' => $fecha, 'valor' => -1]]]);
        $this->assertUnavailable(['serie' => [['fecha' => $fecha, 'valor' => 0]]]);
    }

    public function test_it_rejects_an_invalid_date(): void
    {
        $this->assertUnavailable(['serie' => [['fecha' => '2000-02-30T03:00:00.000Z', 'valor' => 123]]]);
    }

    public function test_it_shows_unavailable_when_the_service_fails(): void
    {
        Http::fake(['mindicador.cl/*' => Http::response([], 503)]);

        $this->get('/proyectos')->assertOk()->assertSee('UF no disponible para hoy');
    }

    public function test_it_shows_unavailable_when_the_service_connection_fails(): void
    {
        Http::fake(['mindicador.cl/*' => Http::failedConnection()]);

        $this->get('/proyectos')->assertOk()->assertSee('UF no disponible para hoy');
    }

    private function assertUnavailable(array $response): void
    {
        Http::fake(['mindicador.cl/*' => Http::response($response)]);

        $this->get('/proyectos')->assertOk()->assertSee('UF no disponible para hoy');
    }
}
