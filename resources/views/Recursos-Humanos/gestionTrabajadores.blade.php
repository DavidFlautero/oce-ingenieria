@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Botón para agregar nuevo empleado -->
    <button wire:click="abrirModal" class="btn btn-primary mb-3">
        <i class="fas fa-user-plus me-2"></i> Nuevo Empleado
    </button>

    <!-- Tabla de empleados -->
    <div class="table-responsive">
        <table id="tablaEmpleados" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Cargo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Contenido dinámico de DataTables -->
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('styles')
    <!-- DataTables CSS (mover al layout principal si se usa en varias páginas) -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
@endpush

@push('scripts')
    <!-- DataTables JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#tablaEmpleados').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("empleados.lista") }}',
                    error: function(xhr, error, code) {
                        console.error('Error DataTables:', xhr.responseText);
                    }
                },
                columns: [
                    { data: 'nombre', name: 'nombre' },
                    { data: 'email', name: 'email' },
                    { data: 'cargo', name: 'cargo' },
                    { 
                        data: 'acciones', 
                        name: 'acciones', 
                        orderable: false, 
                        searchable: false,
                        render: function(data, type, row) {
                            return data || ''; // Manejo seguro de acciones
                        }
                    }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                }
            });

            // Manejo de eventos Livewire
            window.addEventListener('empleadoGuardado', () => {
                $('#tablaEmpleados').DataTable().ajax.reload(null, false);
            });
        });
    </script>
@endpush