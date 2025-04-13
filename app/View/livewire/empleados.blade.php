<div>
    <div class="d-flex justify-content-between mb-3">
        <input wire:model.debounce.500ms="search" type="search" class="form-control w-25" placeholder="Buscar...">
        <button wire:click="$emit('abrirModalNuevo')" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Nuevo Empleado
        </button>
    </div>

    @livewire('nuevo-empleado')

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th wire:click="sortBy('nombre')">Nombre</th>
                    <th>DNI</th>
                    <th>Área</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($empleados as $empleado)
                <tr>
                    <td>{{ $empleado->apellido }}, {{ $empleado->nombre }}</td>
                    <td>{{ $empleado->dni }}</td>
                    <td>{{ $empleado->area->nombre ?? '-' }}</td>
                    <td>
                        <button wire:click="$emit('editarEmpleado', {{ $empleado->id }})" 
                                class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button wire:click="confirmarEliminacion({{ $empleado->id }})" 
                                class="btn btn-sm btn-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $empleados->links() }}
    </div>
</div>