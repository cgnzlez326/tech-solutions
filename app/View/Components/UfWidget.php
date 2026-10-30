<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Http;
use Illuminate\View\Component;

class UfWidget extends Component
{
    public float $valorUf = 0;

    public ?string $fechaUf = null;

    public bool $esDeHoy = false;

    public function __construct()
    {
        try {
            $response = Http::timeout(2)->get('https://mindicador.cl/api/uf');

            if (! $response->successful()) {
                return;
            }

            $registro = $response->json('serie.0');
            $valor = is_array($registro) ? ($registro['valor'] ?? null) : null;
            $fecha = is_array($registro) ? ($registro['fecha'] ?? null) : null;

            if (! is_numeric($valor) || ! is_string($fecha)) {
                return;
            }

            $valor = (float) $valor;
            $partesFecha = date_parse($fecha);
            if (! is_finite($valor) || $valor <= 0 || $partesFecha['error_count'] || $partesFecha['warning_count'] || ! checkdate($partesFecha['month'], $partesFecha['day'], $partesFecha['year'])) {
                return;
            }

            $zonaChile = new \DateTimeZone('America/Santiago');
            $fechaIndicador = preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)
                ? new \DateTimeImmutable($fecha, $zonaChile)
                : (new \DateTimeImmutable($fecha, $zonaChile))->setTimezone($zonaChile);
            $this->valorUf = $valor;
            $this->fechaUf = $fechaIndicador->format('d-m-Y');
            $this->esDeHoy = $fechaIndicador->format('Y-m-d') === now($zonaChile)->format('Y-m-d');
        } catch (\Throwable) {
            $this->valorUf = 0;
            $this->fechaUf = null;
            $this->esDeHoy = false;
        }
    }

    public function render(): View|Closure|string
    {
        return view('components.uf-widget');
    }
}
