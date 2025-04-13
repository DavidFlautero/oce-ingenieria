<div class="btn-group btn-group-sm" role="group" aria-label="Acciones del empleado">
    <!-- Botón Editar -->
    <button type="button" 
            class="btn btn-warning"
            wire:click="editar({{ $empleado->id }})"
            data-bs-toggle="tooltip"
            title="Editar empleado">
        <i class="fas fa-edit"></i>
    </button>
    
    <!-- Botón Eliminar -->
    <button type="button"
            class="btn btn-danger"
            wire:click="confirmarEliminacion({{ $empleado->id }})"
            data-bs-toggle="tooltip"
            title="Eliminar empleado">
        <i class="fas fa-trash-alt"></i>
    </button>
    
    <!-- Botón Documentos -->
    <button type="button"
            class="btn btn-info"
            wire:click="verDocumentos({{ $empleado->id }})"
            data-bs-toggle="tooltip"
            title="Ver documentos">
        <i class="fas fa-file-alt"></i>
    </button>
    
    <!-- Botón Detalles -->
    <button type="button"
            class="btn btn-primary"
            wire:click="mostrarDetalles({{ $empleado->id }})"
            data-bs-toggle="tooltip"
            title="Ver detalles completos">
        <i class="fas fa-eye"></i>
    </button>
</div>

@push('scripts')
<script>
    // Inicializar tooltips de Bootstrap
    document.addEventListener('livewire:load', function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>
@endpush