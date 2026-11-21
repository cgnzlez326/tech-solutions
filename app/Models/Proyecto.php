<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Proyecto extends Model
{
    protected $table = 'proyectos';

    protected $fillable = ['nombre', 'fecha_inicio', 'estado', 'responsable', 'monto', 'created_by'];

    protected $casts = ['fecha_inicio' => 'date', 'monto' => 'double'];

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function datos(): array
    {
        return [
            'Id' => $this->id,
            'Nombre' => $this->nombre,
            'Fecha_Inicio' => $this->fecha_inicio->toDateString(),
            'Estado' => $this->estado,
            'Responsable' => $this->responsable,
            'Monto' => $this->monto,
            'created_by' => $this->created_by,
        ];
    }
}
