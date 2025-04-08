@extends('layouts.appV')

@section('content')
<div class="wrapper">

    {{-- Navbar --}}
    <nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
            </li>
        </ul>

        <div class="ml-auto d-flex align-items-center">
            <span class="badge bg-primary me-3">Modo RRHH</span>
            <span class="navbar-text">
                <i class="far fa-calendar-alt me-1"></i>
                <span id="current-date"></span>
            </span>
        </div>
    </nav>

    {{-- Sidebar --}}
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="#" class="brand-link text-center py-3">
            <span class="brand-text font-weight-bold">OCE - RRHH</span>
        </a>

        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('empleados.index') }}" class="nav-link active">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Empleados</p>
                        </a>
                    </li>

                   
                </ul>
            </nav>
        </div>
    </aside>

    {{-- Contenido Principal --}}
    <div class="content-wrapper">

        <section class="content p-4">
            <div class="container-fluid">

                {{-- Botón Nuevo Empleado --}}
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalGestionEmpleado">
                    <i class="fas fa-user-plus me-2"></i> Nuevo Empleado
                </button>

                {{-- Tabla Empleados --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Listado de Empleados</h3>
                        <div class="card-tools">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" placeholder="Buscar empleado">
                                <div class="input-group-append">
                                    <button class="btn btn-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="tablaEmpleados" class="table table-bordered table-hover w-100">
                            <thead class="bg-lightblue">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Documento</th>
                                    <th>Cargo</th>
                                    <th>Área</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($empleados as $empleado)
                                <tr>
                                    <td>{{ $empleado->nombre }} {{ $empleado->apellido }}</td>
                                    <td>{{ $empleado->dni }}</td>
                                    <td>{{ $empleado->cargo }}</td>
                                    <td>{{ $empleado->area->nombre }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info" data-id="{{ $empleado->id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" data-id="{{ $empleado->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </section>

    </div>

</div>

{{-- Modal Nuevo Trabajador --}}
@if(isset($areas) && count($areas) > 0)
    @include('Recursos-Humanos.modales.modalNuevoTrabajador', ['areas' => $areas])
@else
    <div class="alert alert-danger m-4">
        <i class="fas fa-exclamation-triangle"></i> No se encontraron áreas registradas. 
        <a href="#" id="crearAreaInicial">Crear área inicial</a>
    </div>
@endif

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Inicializar datatable
        $('#tablaEmpleados').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            }
        });

        // Crear área inicial si no hay
        $('#crearAreaInicial').click(function(e) {
            e.preventDefault();
            $.ajax({
                url: '/empleados/crear-area',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    nombre: 'Área Inicial'
                },
                success: function(response) {
                    location.reload();
                }
            });
        });
    });
</script>
<script>
function mostrarInputNuevaArea(select) {
    const inputNuevaArea = document.getElementById('input-nueva-area');
    
    if (select.value === 'nueva_area') {
        inputNuevaArea.style.display = 'block';
        // Reseteamos el select
        select.selectedIndex = 0; 
    } else {
        inputNuevaArea.style.display = 'none';
    }
}
</script>
<script src="{{ asset('js/rrhh/empleados.js') }}"></script>
@endpush