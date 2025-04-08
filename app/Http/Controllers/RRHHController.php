<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empleado;
use Illuminate\Support\Facades\Storage;
use App\Models\Area;
use App\Models\Cargo;
use App\Models\Documento;


class RRHHController extends Controller
{
    public function index()
    {
        return view('Recursos-Humanos.v.gestionTrabajadores');
    }

  
    



    public function guardarEmpleado(Request $request) 
    {
        // 1. Validaciones con campos obligatorios según requerimiento
        $validated = $request->validate([
            // Campos Obligatorios
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'dni' => 'required|numeric|digits_between:7,8|unique:empleados,dni,'.$request->id,
            'fechaNacimiento' => 'required|date|before:-18 years',
            'grupoSanguineo' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'telefono' => 'required|string|max:20',
            'direccion' => 'required|string|max:255',
            'fechaIngreso' => 'required|date|after_or_equal:2000-01-01',
            'area' => 'required|string|max:100',
            'cargo' => 'required|string|max:100',

            // Campos Opcionales
            'alergias' => 'nullable|string|max:500',
            'dniFrente' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'dniDorso' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'cbu' => 'nullable|digits:22',
            'registroConducir' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'vencimiento_registro' => 'nullable|date|after:today',
            'certificadoMedico' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'otrosDocumentos.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'tipoContrato' => 'nullable|in:planta,temporal,prueba',
            'salarioBase' => 'nullable|numeric|min:0|max:1000000',
            'bonificaciones' => 'nullable|numeric|min:0|max:1000000',
            'contactoEmergenciaNombre' => 'nullable|string|max:100',
            'contactoEmergenciaTelefono' => 'nullable|string|max:20',
            'contactoEmergenciaParentesco' => 'nullable|string|max:50',
            'contactoEmergenciaObservaciones' => 'nullable|string|max:255'
        ], [
            // Mensajes personalizados para campos obligatorios
            'required' => 'El campo :attribute es obligatorio',
            'fechaNacimiento.before' => 'El empleado debe ser mayor de edad',
            'dni.digits_between' => 'El DNI debe tener entre 7 y 8 dígitos',
            'fechaIngreso.after_or_equal' => 'Fecha de ingreso no válida',
            'cbu.digits' => 'El CBU debe tener exactamente 22 dígitos'
        ]);

        // 2. Procesamiento de archivos
        $paths = [];
        $fileFields = [
            'dniFrente' => 'dni_frente_path',
            'dniDorso' => 'dni_dorso_path',
            'registroConducir' => 'registro_conducir_path',
            'certificadoMedico' => 'certificado_medico_path'
        ];

        foreach ($fileFields as $formField => $dbField) {
            if ($request->hasFile($formField)) {
                $paths[$dbField] = $request->file($formField)->store("documentos/$formField");
            }
        }

        // Procesar documentos múltiples
        if ($request->hasFile('otrosDocumentos')) {
            $otrosDocs = [];
            foreach ($request->file('otrosDocumentos') as $file) {
                $otrosDocs[] = $file->store('documentos/otros');
            }
            $paths['otros_documentos'] = json_encode($otrosDocs);
        }

        // 3. Guardar/Actualizar
        $empleado = Empleado::updateOrCreate(
            ['id' => $request->id],
            array_merge($validated, $paths)
        );

        // 4. Respuesta
        return response()->json([
            'success' => true,
            'data' => $empleado,
            'received_data' => env('APP_DEBUG') ? $request->all() : null
        ]);
    }
	
	
	
	public function crearArea(Request $request)
{
    $request->validate([
        'nombre' => 'required|string|unique:areas,nombre|max:255',
    ]);

    $area = Area::create([
        'nombre' => $request->nombre
    ]);

    return response()->json([
        'success' => true,
        'area' => $area
    ]);
}


	public function obtenerEmpleado($id)
{
    $empleado = Empleado::findOrFail($id);

    return response()->json($empleado);
}
    public function listarEmpleados()
    {
        $empleados = Empleado::all();
        return response()->json($empleados);
    }

    public function eliminarEmpleado($id)
    {
        Empleado::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
// Devuelve las Áreas desde la DB
public function obtenerAreas()
{
    $areas = Area::all(); // Modelo Area
    return response()->json($areas);
}

// Devuelve los Cargos según el área seleccionada
public function obtenerCargos($area)
{
    $cargos = Cargo::where('area', $area)->get(); // Modelo Cargo
    return response()->json($cargos);
}

    public function subirDocumento(Request $request)
    {
        $request->validate([
            'documento' => 'required|file|mimes:pdf,jpg,png|max:2048',
            'empleado_id' => 'required|exists:empleados,id'
        ]);

        $path = $request->file('documento')->store('documentos');

        // Guardar en base de datos
        Documento::create([
            'empleado_id' => $request->empleado_id,
            'tipo' => $request->tipo,
            'ruta' => $path
        ]);

        return response()->json(['success' => true, 'path' => $path]);
    }
}