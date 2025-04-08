<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $fillable = [
        'empleado_id',
        'tipo',
        'ruta',
        'vencimiento'
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}