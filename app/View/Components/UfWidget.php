<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Http;

class UfWidget extends Component
{
    public $valorUf;

    public function __construct()
    {
        try {
            // Aquí le puse un límite  de 2 segundos porque o sino la página se quedaba cargando eternamente si no recibía la info de la Api
            $response = Http::withoutVerifying()
                            ->timeout(2)
                            ->get('https://mindicador.cl/api/uf');
            
            if ($response->successful()) {
                $data = $response->json();
                $this->valorUf = $data['serie'][0]['valor'];
            } else {
                $this->valorUf = 0;
            }
        } catch (\Exception $e) {
            // Si la API falla o se demora más de 2 segundos, se apone en 0 para quedar en ese bucle
            $this->valorUf = 0; 
        }
    }

    public function render(): View|Closure|string
    {
        return view('components.uf-widget');
    }
}