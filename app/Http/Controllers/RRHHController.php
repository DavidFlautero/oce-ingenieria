<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Empleado;
use App\Models\Area;
use App\Models\Cargo;
use App\Models\Documento;
use Illuminate\Support\Facades\Storage;

class RRHHController extends Controller
{
    public function index()
    {
        $areas = Area::all();
        $empleados = Empleado::with('area')->get();
        
        return view('Recursos-Humanos.v.gestionTrabajadores', [
            'areas' => $areas,
            'empleados' => $empleados
        ]);
    }

    public function guardarEmpleado(Request $request)
    {
        $validated = $request->validate([
            // Datos Personales
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'dni' => 'required|numeric|digits_between:7,8|unique:empleados,dni',
            'cuit_cuil' => 'required|numeric|digits:11',
            'fecha_nacimiento' => 'required|date|before:-18 years',
            'grupo_sanguineo' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-,A Rh+,A Rh-,B Rh+,B Rh-,AB Rh+,AB Rh-,O Rh+,O Rh-',
            'telefono' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
            'direccion' => 'required|string|max:255',
            'localidad' => 'required|string|max:100',
            'provincia' => 'required|string|max:50',
            'alergias' => 'nullable|string|max:500',
            
            // Datos Laborales
            'fecha_ingreso' => 'required|date|after_or_equal:2000-01-01',
            'area_id' => 'required',
            'nueva_area' => 'nullable|string|max:100|required_if:area_id,nueva_area|unique:areas,nombre',
            'relacion_laboral' => 'required|in:planta,prueba,monotributista',
            'salario_base' => 'nullable|numeric|min:0',
            
            // Monotributo (condicional)
            'estado_monotributo' => 'required_if:relacion_laboral,monotributista|in:existente,crear',
            'categoria_monotributo' => 'required_if:relacion_laboral,monotributista|in:A,B,C,D,E,F,G,H,I,J,K',
            'clave_fiscal' => 'nullable|string|max:50',
            'fecha_inscripcion_monotributo' => 'nullable|date',
            
            // Documentos
            'foto_dni_frente' => 'required|file|mimes:jpg,png,pdf|max:2048',
            'foto_dni_dorso' => 'required|file|mimes:jpg,png,pdf|max:2048',
            'cbu' => 'nullable|digits:22',
            'registro_conducir' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
            'vencimiento_registro' => 'nullable|date',
            'certificado_medico' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
            'otros_documentos' => 'nullable|array',
            
            // Contacto Emergencia
            'contacto_emergencia_nombre' => 'required|string|max:100',
            'contacto_emergencia_telefono' => 'required|string|max:20',
            'contacto_emergencia_parentesco' => 'nullable|string|max:50'
        ]);

        // Procesar área (existente o nueva)
        if ($request->area_id === 'nueva_area') {
            $area = Area::create(['nombre' => $request->nueva_area]);
            $area_id = $area->id;
        } else {
            $area_id = $request->area_id;
        }

        // Crear empleado
        $empleadoData = $request->only([
            'nombre', 'apellido', 'dni', 'cuit_cuil', 'fecha_nacimiento',
            'grupo_sanguineo', 'telefono', 'email', 'direccion', 'localidad',
            'provincia', 'alergias', 'fecha_ingreso', 'relacion_laboral',
            'salario_base', 'estado_monotributo', 'categoria_monotributo',
            'clave_fiscal', 'fecha_inscripcion_monotributo',
            'contacto_emergencia_nombre', 'contacto_emergencia_telefono',
            'contacto_emergencia_parentesco'
        ]);
        
        $empleadoData['email'] = $request->email ?? 'sin-email@ejemplo.com';
        $empleadoData['alergias'] = $request->alergias ?? null;
        $empleadoData['salario_base'] = $request->salario_base ?? null;
        $empleadoData['clave_fiscal'] = $request->clave_fiscal ?? null;
        $empleadoData['contacto_emergencia_parentesco'] = $request->contacto_emergencia_parentesco ?? null;
        $empleadoData['area_id'] = $area_id;
        
        $empleado = Empleado::create($empleadoData);

        // Subir documentos
        $this->subirDocumentos($empleado, $request);

        return response()->json([
            'success' => true,
            'message' => 'Empleado registrado correctamente',
            'empleado' => $empleado
        ]);
    }

    private function subirDocumentos($empleado, $request)
    {
        $documentos = [
            'foto_dni_frente' => 'dni_frente',
            'foto_dni_dorso' => 'dni_dorso',
            'registro_conducir' => 'registro_conducir',
            'certificado_medico' => 'certificado_medico'
        ];

        foreach ($documentos as $requestKey => $tipo) {
            if ($request->hasFile($requestKey)) {
                $path = $request->file($requestKey)->store('documentos');
                $empleado->documentos()->create([
                    'tipo' => $tipo,
                    'ruta' => $path,
                    'vencimiento' => $requestKey === 'registro_conducir' 
                        ? $request->vencimiento_registro 
                        : null
                ]);
            }
        }

        // Procesar otros documentos múltiples
        if ($request->hasFile('otros_documentos')) {
            foreach ($request->file('otros_documentos') as $file) {
                $path = $file->store('documentos');
                $empleado->documentos()->create([
                    'tipo' => 'otros',
                    'ruta' => $path
                ]);
            }
        }
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
        $empleado = Empleado::with(['area', 'documentos'])->findOrFail($id);
        return response()->json($empleado);
    }

    public function listarEmpleados()
    {
        $empleados = Empleado::with('area')->get();
        return response()->json($empleados);
    }

    public function eliminarEmpleado($id)
    {
        $empleado = Empleado::findOrFail($id);
        
        // Eliminar documentos físicos
        foreach ($empleado->documentos as $documento) {
            Storage::delete($documento->ruta);
        }
        
        $empleado->documentos()->delete();
        $empleado->delete();
        
        return response()->json(['success' => true]);
    }

    public function obtenerAreas()
    {
        $areas = Area::all();
        return response()->json($areas);
    }

    public function obtenerCargos($area_id)
    {
        $cargos = Cargo::where('area_id', $area_id)->get();
        return response()->json($cargos);
    }

    public function subirDocumento(Request $request)
    {
        $request->validate([
            'documento' => 'required|file|mimes:pdf,jpg,png|max:2048',
            'empleado_id' => 'required|exists:empleados,id',
            'tipo' => 'required|string',
            'vencimiento' => 'nullable|date'
        ]);

        $path = $request->file('documento')->store('documentos');

        Documento::create([
            'empleado_id' => $request->empleado_id,
            'tipo' => $request->tipo,
            'ruta' => $path,
            'vencimiento' => $request->vencimiento
        ]);

        return response()->json(['success' => true, 'path' => $path]);
    }
}