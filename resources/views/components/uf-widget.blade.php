<div style="background-color: #e2f3f5; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 5px solid #007bff;">
    <strong>
        @if($valorUf > 0)
            UF del {{ $fechaUf }}: ${{ number_format($valorUf, 2, ',', '.') }}
        @else
            UF no disponible para hoy
        @endif
    </strong>
</div>
