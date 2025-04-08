<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    protected $fillable = [
        'nombre',
        'dni',
        'fecha_nacimiento',
        'grupo_sanguineo',
        'telefono',
        'direccion',
        'alergias',
        'fecha_ingreso',
        'area',
        'cargo',
        'tipo_contrato',
        'salario_base',
        'bonificaciones',
        'contacto_emergencia_nombre',
        'contacto_emergencia_telefono',
        'contacto_emergencia_parentesco'
    ];

    public function documentos()
    {
        return $this->hasMany(Documento::class);
    }
}