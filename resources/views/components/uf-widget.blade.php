<div style="background-color: #e2f3f5; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 5px solid #007bff;">
    <strong>Indicador Económico:</strong> El valor de la UF de hoy es de 
    <strong>
        @if($valorUf > 0)
            ${{ number_format($valorUf, 2, ',', '.') }}
        @else
            No disponible temporalmente
        @endif
    </strong>
</div>