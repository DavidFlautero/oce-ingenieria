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

  //////////////////////////////////////////
  /////////////////////////////////////////
  ////GUARDAR EMPLEADO DESDE MODAL Y AREA//
  public function guardarEmpleado(Request $request)
{
    $validated = $request->validate([
        'nombre' => 'required|string|max:100',
        'apellido' => 'required|string|max:100',
        'dni' => 'required|numeric|digits_between:7,8|unique:empleados,dni',
        'fechaNacimiento' => 'required|date|before:-18 years',
        'grupoSanguineo' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
        'telefono' => 'required|string|max:20',
        'direccion' => 'required|string|max:255',
        'fechaIngreso' => 'required|date|after_or_equal:2000-01-01',
        'cargo' => 'required|string|max:100',

        // Área: puede ser seleccionada o nueva
        'area' => 'nullable|string|max:100',
        'nueva_area' => 'nullable|string|max:100',

        // Campos opcionales
        'alergias' => 'nullable|string|max:500',
        'cbu' => 'nullable|digits:22',
        'tipoContrato' => 'nullable|in:planta,temporal,prueba',
        'salarioBase' => 'nullable|numeric|min:0|max:1000000',
        'bonificaciones' => 'nullable|numeric|min:0|max:1000000',
    ]);

    // Lógica para área
    if ($request->filled('nueva_area')) {
        $area = Area::create([
            'nombre' => $request->nueva_area
        ]);
    } else {
        $area = Area::where('nombre', $request->area)->first();
    }

    if (!$area) {
        return back()->withErrors(['area' => 'Debe seleccionar un área o ingresar una nueva.']);
    }

    // Crear empleado
    $empleado = new Empleado();
    $empleado->nombre = $request->nombre;
    $empleado->apellido = $request->apellido;
    $empleado->dni = $request->dni;
    $empleado->fechaNacimiento = $request->fechaNacimiento;
    $empleado->grupoSanguineo = $request->grupoSanguineo;
    $empleado->telefono = $request->telefono;
    $empleado->direccion = $request->direccion;
    $empleado->fechaIngreso = $request->fechaIngreso;
    $empleado->cargo = $request->cargo;
    $empleado->area_id = $area->id; // Relación correcta

    // Opcionales
    $empleado->alergias = $request->alergias;
    $empleado->cbu = $request->cbu;
    $empleado->tipoContrato = $request->tipoContrato;
    $empleado->salarioBase = $request->salarioBase;
    $empleado->bonificaciones = $request->bonificaciones;

    $empleado->save();

    return redirect()->route('empleados.index')->with('success', 'Empleado creado correctamente');
}

    



   
	/////////////////////////////////////////
	/////////////////////////////////////////
	////Crea Area de Trabajo Desde Modal/////
    ////Vamos la puta madre David no frenes!!	
	
	
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