<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
 protected $fillable = [
    'dni',
    'apellido',
    'nombre',
    'fecha_nacimiento', // Se mapeará desde fechaNacimiento
    'domicilio',        // Se mapeará desde direccion
    'localidad',
    'provincia',
    'telefono',
    'email',
    'foto_dni_frente',  // Se mapeará desde dniFrente
    'foto_dni_dorso',   // Se mapeará desde dniDorso
    'fecha_ingreso',    // Se mapeará desde fechaIngreso
    'activo',
    'area_id',          // Se mapeará desde area
    'cuitCuil',
    'grupoSanguineo',
    'alergias',
    'cargo',
    'relacionLaboral',
    'salarioBase',
    'bonificaciones',
    'contactoEmergenciaNombre',
    'contactoEmergenciaTelefono',
    'contactoEmergenciaParentesco'
];

    public function documentos()
    {
        return $this->hasMany(Documento::class);
    }
}