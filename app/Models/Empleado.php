<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Empleado extends Model
{
    protected $fillable = [
        'nombre',
        'apellido',
        'dni',
        'cuit_cuil',
        'fecha_nacimiento',
        'grupo_sanguineo',
        'direccion',
        'telefono',
        'email',
        'localidad',
        'provincia',
        'alergias',
        'fecha_ingreso',
        'area_id',
        'cargo',
        'relacion_laboral',
        'salario_base',
        'bonificaciones',
        'cbu', // Asegúrate que esté en fillable para asignación masiva
        'contacto_emergencia_nombre',
        'contacto_emergencia_telefono',
        'contacto_emergencia_parentesco',
        'contacto_emergencia_observaciones',
        'estado_monotributo',
        'categoria_monotributo',
        'clave_fiscal',
        'fecha_inscripcion_monotributo'
    ];

   // Métodos que YA DEBES TENER en tu modelo:
public function setCbuAttribute($value)
{
    $this->attributes['cbu'] = $value ? Crypt::encryptString($value) : null;
}

public function getCbuAttribute($value)
{
    try {
        return $value ? Crypt::decryptString($value) : null;
    } catch (\Exception $e) {
        Log::error("Error desencriptando CBU: ".$e->getMessage());
        return null;
    }
}

public function getCbuMaskedAttribute()
{
    if (!$this->cbu) return null;
    return substr($this->cbu, 0, 4).str_repeat('•', 14).substr($this->cbu, -4);
}

public function hasValidCbu()
{
    return $this->cbu && preg_match('/^\d{22}$/', $this->cbu);
}

// Scope para búsquedas
public function scopeWithCbu($query)
{
    return $query->whereNotNull('cbu');
}
}

