<div style="background-color: #e2f3f5; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 5px solid #007bff;">
    <strong>Indicador Económico:</strong>
    <strong>
        @if($valorUf > 0)
            UF del {{ $fechaUf }}: ${{ number_format($valorUf, 2, ',', '.') }}
            @if($esDeHoy)
                <span>(dato de hoy en Chile)</span>
            @else
                <span>(registro de otra fecha; no corresponde al día de Chile)</span>
            @endif
        @else
            No disponible temporalmente
        @endif
    </strong>
</div>
