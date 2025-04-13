<?php
namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Empleado;
use Livewire\WithPagination;

class Empleados extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'empleadoGuardado' => 'render',
        'empleadoEliminado' => 'render'
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function confirmarEliminacion($id)
    {
        $this->dispatchBrowserEvent('swal:confirm', [
            'type' => 'warning',
            'title' => '¿Estás seguro?',
            'text' => 'No podrás revertir esta acción',
            'id' => $id
        ]);
    }

    public function eliminarEmpleado($id)
    {
        try {
            $empleado = Empleado::findOrFail($id);
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

    public function render()
    {
        $empleados = Empleado::where('nombre', 'like', '%'.$this->search.'%')
            ->orWhere('apellido', 'like', '%'.$this->search.'%')
            ->orWhere('dni', 'like', '%'.$this->search.'%')
            ->paginate($this->perPage);

        return view('livewire.empleados', compact('empleados'));
    }
}