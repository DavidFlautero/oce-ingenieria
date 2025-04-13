<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Empleado;
use App\Models\Area;
use App\Models\Documento;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NuevoEmpleado extends Component
{
    use WithFileUploads;

    // Estado del modal
    public $abrirModal = false;

    // ============ DATOS PERSONALES ============
    public $nombre;
    public $apellido;
    public $dni;
    public $cuit_cuil;
    public $fecha_nacimiento;
    public $grupo_sanguineo;
    public $alergias;
    public $telefono;
    public $email;
    public $direccion;
    public $localidad;
    public $provincia;

    // ============ DOCUMENTACIÓN ============
    public $foto_dni_frente;
    public $foto_dni_dorso;
    public $cbu;
    public $registro_conducir;
    public $vencimiento_registro;
    public $certificado_medico;
    public $otros_documentos = [];

    // ============ DATOS LABORALES ============
    public $fecha_ingreso;
    public $area_id;
    public $nueva_area_nombre;
    public $relacion_laboral;
    public $salario_base;
    
    // Campos específicos de monotributo
    public $estado_monotributo;
    public $categoria_monotributo;
    public $clave_fiscal;
    public $fecha_inscripcion_monotributo;

    // ============ CONTACTO EMERGENCIA ============
    public $contacto_emergencia_nombre;
    public $contacto_emergencia_telefono;
    public $contacto_emergencia_parentesco;
    public $contacto_emergencia_observaciones;

    // Datos para selects
    public $areas = [];

    protected $listeners = [
        'empleadoEliminado' => 'empleadoEliminado',
        'empleadoGuardado' => 'empleadoGuardado'
    ];

    public function mount()
    {
        $this->areas = Area::all();
    }

    // ============ MÉTODOS DEL MODAL ============
    public function abrirModal()
    {
        $this->resetForm();
        $this->abrirModal = true;
    }

    public function cerrarModal()
    {
        $this->abrirModal = false;
    }

    // ============ MÉTODOS PARA ACCIONES ============
    public function editar($empleadoId)
    {
        try {
            $empleado = Empleado::with(['area', 'documentos'])->findOrFail($empleadoId);
            $this->emit('abrirModalEdicion', $empleado);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('notify', [
                'type' => 'error',
                'message' => 'Error al cargar empleado: ' . $e->getMessage()
            ]);
        }
    }

    public function confirmarEliminacion($empleadoId)
    {
        $this->dispatchBrowserEvent('swal:confirm', [
            'type' => 'warning',
            'title' => '¿Estás seguro?',
            'text' => 'El empleado será eliminado permanentemente',
            'id' => $empleadoId
        ]);
    }

    public function eliminarEmpleado($empleadoId)
    {
        try {
            $empleado = Empleado::with('documentos')->findOrFail($empleadoId);
            
            // Eliminar documentos físicos
            foreach ($empleado->documentos as $documento) {
                Storage::delete($documento->ruta);
                $documento->delete();
            }
            
            $empleado->delete();
            
            $this->dispatchBrowserEvent('notify', [
                'type' => 'success',
                'message' => 'Empleado eliminado correctamente'
            ]);
            
            $this->emit('empleadoEliminado');
            
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('notify', [
                'type' => 'error',
                'message' => 'Error al eliminar: ' . $e->getMessage()
            ]);
        }
    }

    public function verDocumentos($empleadoId)
    {
        try {
            $documentos = Documento::where('empleado_id', $empleadoId)->get();
            $this->dispatchBrowserEvent('mostrarDocumentos', ['documentos' => $documentos]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('notify', [
                'type' => 'error',
                'message' => 'Error al cargar documentos: ' . $e->getMessage()
            ]);
        }
    }

    public function mostrarDetalles($empleadoId)
    {
        try {
            $empleado = Empleado::with(['area', 'documentos'])->findOrFail($empleadoId);
            $this->dispatchBrowserEvent('mostrarDetalles', ['empleado' => $empleado]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('notify', [
                'type' => 'error',
                'message' => 'Error al cargar detalles: ' . $e->getMessage()
            ]);
        }
    }

    // ============ MÉTODOS PRINCIPALES ============
    public function guardar()
    {
        $validated = $this->validate($this->reglasValidacion());

        // Procesar área
        if ($this->area_id === 'nueva_area') {
            $area = Area::firstOrCreate(['nombre' => $this->nueva_area_nombre]);
            $validated['area_id'] = $area->id;
        }

        // Guardar empleado
        $empleado = Empleado::create($validated);

        // Guardar documentos
        $this->guardarDocumentos($empleado);

        $this->cerrarModal();
        $this->emit('empleadoGuardado');
        $this->dispatchBrowserEvent('notify', [
            'type' => 'success',
            'message' => 'Empleado registrado correctamente'
        ]);
    }

    // ============ MÉTODOS PRIVADOS ============
    private function reglasValidacion()
    {
        return [
            // Datos personales
            'nombre' => 'required|string|min:3|max:100',
            'apellido' => 'required|string|max:100',
            'dni' => 'required|numeric|digits_between:7,8|unique:empleados,dni',
            'cuit_cuil' => 'required|numeric|digits:11|unique:empleados,cuit_cuil',
            'fecha_nacimiento' => 'required|date|before:-18 years',
            'grupo_sanguineo' => 'nullable|string|max:50',
            'alergias' => 'nullable|string|max:500',
            'telefono' => 'required|string|max:20',
            'email' => 'nullable|email|max:100|unique:empleados,email',
            'direccion' => 'required|string|max:255',
            'localidad' => 'required|string|max:100',
            'provincia' => 'required|string|max:50',
            
            // Documentación
            'foto_dni_frente' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'foto_dni_dorso' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'cbu' => 'nullable|digits:22',
            'registro_conducir' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'vencimiento_registro' => 'nullable|date',
            'certificado_medico' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'otros_documentos.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            
            // Datos laborales
            'fecha_ingreso' => 'required|date|after_or_equal:2000-01-01',
            'area_id' => 'required',
            'nueva_area_nombre' => 'nullable|string|max:100|required_if:area_id,nueva_area|unique:areas,nombre',
            'relacion_laboral' => 'required|in:planta,prueba,monotributista',
            'salario_base' => 'nullable|numeric|min:0',
            
            // Monotributo (condicional)
            'estado_monotributo' => 'required_if:relacion_laboral,monotributista|in:existente,crear',
            'categoria_monotributo' => 'required_if:relacion_laboral,monotributista|in:A,B,C,D,E,F,G,H,I,J,K',
            'clave_fiscal' => 'nullable|string|max:50',
            'fecha_inscripcion_monotributo' => 'nullable|date',
            
            // Contacto emergencia
            'contacto_emergencia_nombre' => 'required|string|max:100',
            'contacto_emergencia_telefono' => 'required|string|max:20',
            'contacto_emergencia_parentesco' => 'nullable|string|max:50',
            'contacto_emergencia_observaciones' => 'nullable|string|max:500',
        ];
    }

    private function guardarDocumentos($empleado)
    {
        $documentos = [
            'foto_dni_frente' => ['tipo' => 'dni_frente', 'vencimiento' => null],
            'foto_dni_dorso' => ['tipo' => 'dni_dorso', 'vencimiento' => null],
            'registro_conducir' => ['tipo' => 'registro_conducir', 'vencimiento' => $this->vencimiento_registro],
            'certificado_medico' => ['tipo' => 'certificado_medico', 'vencimiento' => null]
        ];

        foreach ($documentos as $campo => $config) {
            if ($this->$campo) {
                $path = $this->$campo->store(
                    "empleados/{$empleado->id}/documentos",
                    'public'
                );
                
                $empleado->documentos()->create([
                    'tipo' => $config['tipo'],
                    'ruta' => $path,
                    'nombre_original' => $this->$campo->getClientOriginalName(),
                    'vencimiento' => $config['vencimiento']
                ]);
            }
        }

        // Otros documentos
        if (!empty($this->otros_documentos)) {
            foreach ($this->otros_documentos as $doc) {
                $path = $doc->store(
                    "empleados/{$empleado->id}/documentos",
                    'public'
                );
                
                $empleado->documentos()->create([
                    'tipo' => 'otro',
                    'ruta' => $path,
                    'nombre_original' => $doc->getClientOriginalName()
                ]);
            }
        }
    }

    private function resetForm()
    {
        $this->reset();
        $this->resetErrorBag();
        $this->areas = Area::all();
    }

    public function render()
    {
        return view('livewire.nuevo-empleado');
    }
}