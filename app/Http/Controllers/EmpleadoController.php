<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empleado;
use App\Models\Area;
use App\Models\Cargo;
use App\Models\Documento;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class EmpleadoController extends Controller
{
    // Mostrar todos los empleados
    public function index()
    {
        $empleados = Empleado::with('area')->get();
        return view('Recursos-Humanos.gestionTrabajadores', compact('empleados'));
    }
	public function gestionTrabajadores()
    {
        $empleados = \App\Models\Empleado::all(); // O tu consulta específica
    return view('Recursos-Humanos.gestionTrabajadores', compact('empleados'));
	}	

    // Crear nuevo empleado
    public function create()
    {
        $areas = Area::all();
        return view('Recursos-Humanos.createEmpleado', compact('areas'));
    }

    // Guardar nuevo empleado
    public function store(Request $request)
    {
        $validated = $this->validateEmpleado($request);

        // Procesar el área
        $area_id = $this->procesarArea($request);

        // Crear empleado
        $empleado = $this->crearEmpleado($request, $area_id);

        // Subir documentos relacionados al empleado
        $this->subirDocumentos($empleado, $request);

        return response()->json([
            'success' => true,
            'message' => 'Empleado registrado correctamente',
            'empleado' => $empleado
        ]);
    }

    // Mostrar detalles del empleado
    public function show($id)
    {
        $empleado = Empleado::with(['area', 'documentos'])->findOrFail($id);
        return response()->json($empleado);
    }

    // Editar empleado
    public function edit($id)
    {
        $empleado = Empleado::with('area')->findOrFail($id);
        $areas = Area::all();
        return view('Recursos-Humanos.editEmpleado', compact('empleado', 'areas'));
    }

    // Actualizar empleado
    public function update(Request $request, $id)
    {
        $validated = $this->validateEmpleado($request);

        $empleado = Empleado::findOrFail($id);
        $area_id = $this->procesarArea($request);

        $empleado->update($request->only([
            'nombre', 'apellido', 'dni', 'cuit_cuil', 'fecha_nacimiento', 'telefono', 
            'email', 'direccion', 'localidad', 'provincia', 'alergias', 'fecha_ingreso', 
            'relacion_laboral', 'salario_base', 'estado_monotributo', 'categoria_monotributo', 
            'clave_fiscal', 'fecha_inscripcion_monotributo', 'contacto_emergencia_nombre', 
            'contacto_emergencia_telefono', 'contacto_emergencia_parentesco', 'area_id' => $area_id
        ]));

        return response()->json(['success' => true, 'empleado' => $empleado]);
    }

    // Eliminar empleado
    public function destroy($id)
    {
        $empleado = Empleado::findOrFail($id);
        $this->eliminarDocumentos($empleado);
        $empleado->delete();

        return response()->json(['success' => true]);
    }

    // Método privado para validar los datos de un empleado
    private function validateEmpleado(Request $request)
    {
        return $request->validate([
            // Validación de datos personales, laborales, documentos, etc.
            // (usado en create y update)
        ]);
    }

    // Método para procesar áreas (crea una nueva si es necesario)
    private function procesarArea(Request $request)
    {
        if ($request->area_id === 'nueva_area') {
            $area = Area::create(['nombre' => $request->nueva_area]);
            return $area->id;
        }
        return $request->area_id;
    }

    // Crear un nuevo empleado
    private function crearEmpleado(Request $request, $area_id)
    {
        $empleadoData = $request->only([
            'nombre', 'apellido', 'dni', 'cuit_cuil', 'fecha_nacimiento', 
            'telefono', 'email', 'direccion', 'localidad', 'provincia', 
            'alergias', 'fecha_ingreso', 'relacion_laboral', 'salario_base', 
            'estado_monotributo', 'categoria_monotributo', 'clave_fiscal', 
            'fecha_inscripcion_monotributo', 'contacto_emergencia_nombre', 
            'contacto_emergencia_telefono', 'contacto_emergencia_parentesco'
        ]);
        $empleadoData['area_id'] = $area_id;

        return Empleado::create($empleadoData);
    }

    // Subir los documentos de un empleado
    private function subirDocumentos($empleado, $request)
    {
        // Lógica para subir documentos (como DNI, registro de conducir, etc.)
    }

    // Eliminar documentos asociados al empleado
    private function eliminarDocumentos($empleado)
    {
        foreach ($empleado->documentos as $documento) {
            Storage::delete($documento->ruta);
        }
        $empleado->documentos()->delete();
    }
	// Agrega este método a tu EmpleadoController
	public function lista(Request $request)
{
    if ($request->ajax()) {
        $query = Empleado::with('area')
            ->select([
                'id',
                'nombre',
                'apellido',
                'dni',
                'email',
                'cargo',
                'area_id',
                'fecha_ingreso',
                'created_at'
            ]);

        return DataTables::of($query)
            ->addColumn('nombre_completo', function($empleado) {
                return $empleado->nombre . ' ' . $empleado->apellido;
            })
            ->addColumn('acciones', function($empleado) {
                return view('Recursos-Humanos.partials.acciones-empleado', compact('empleado'))->render();
            })
            ->editColumn('fecha_ingreso', function($empleado) {
                return optional($empleado->fecha_ingreso)->format('d/m/Y') ?? 'N/A';
            })
            ->editColumn('created_at', function($empleado) {
                return $empleado->created_at->format('d/m/Y H:i');
            })
            ->filterColumn('nombre_completo', function($query, $keyword) {
                $query->whereRaw("CONCAT(nombre,' ',apellido) like ?", ["%{$keyword}%"]);
            })
            ->rawColumns(['acciones'])
            ->make(true);
    }
    
    abort(404, 'Solicitud no válida');
}
}
