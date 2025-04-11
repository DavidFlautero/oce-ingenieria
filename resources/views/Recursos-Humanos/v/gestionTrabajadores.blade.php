@extends('layouts.appV')

@section('content')
<div class="wrapper">

    {{-- Navbar (se mantiene igual) --}}
    <nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom">
        <!-- ... tu navbar actual ... -->
    </nav>

    {{-- Sidebar (se mantiene igual) --}}
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- ... tu sidebar actual ... -->
    </aside>

    {{-- Contenido Principal --}}
    <div class="content-wrapper">
        <section class="content p-4">
            <div class="container-fluid">
                {{-- Botón Nuevo Empleado (se mantiene igual) --}}
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalGestionEmpleado">
                    <i class="fas fa-user-plus me-2"></i> Nuevo Empleado
                </button>

                {{-- Tabla Empleados Actualizada --}}
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
                                    <th>Área</th>
                                    <th>CBU</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($empleados as $empleado)
                                <tr>
                                    <td>{{ $empleado->nombre }} {{ $empleado->apellido }}</td>
                                    <td>{{ $empleado->dni }}</td>
                                    <td>{{ $empleado->area->nombre }}</td>
                                   <td>
    @if($empleado->cbu)
        <div class="d-flex align-items-center">
            <!-- CBU Enmascarado -->
            <span id="cbu-masked-{{ $empleado->id }}" class="cbu-masked">
                {{ $empleado->cbu_masked ?: '•••• •••• •••• •••• ••••' }}
            </span>
            
            <!-- Botón Ver -->
            <button class="btn btn-sm btn-outline-primary btn-view-cbu ms-2"
                    data-id="{{ $empleado->id }}"
                    title="Ver CBU completo">
                <i class="fas fa-eye"></i>
            </button>
            
            <!-- Botón Copiar (oculto inicialmente) -->
            <button class="btn btn-sm btn-outline-success btn-copy-cbu ms-1"
                    data-id="{{ $empleado->id }}"
                    title="Copiar CBU"
                    style="display: none;">
                <i class="fas fa-copy"></i>
            </button>
        </div>
    @else
        <span class="text-muted">No registrado</span>
    @endif
</td>
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

{{-- Modal Nuevo Trabajador (se mantiene igual) --}}
@if(isset($areas) && count($areas) > 0)
    @include('Recursos-Humanos.modales.modalNuevoTrabajador', ['areas' => $areas])
@else
    <div class="alert alert-danger m-4">
        <i class="fas fa-exclamation-triangle"></i> No se encontraron áreas registradas. 
        <a href="#" id="crearAreaInicial">Crear área inicial</a>
    </div>
@endif

<!-- Modal para Ver CBU Completo -->
<div class="modal fade" id="cbuModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ver CBU Completo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Contraseña requerida</label>
                    <input type="password" id="cbuPassword" class="form-control" 
                           placeholder="Ingrese su contraseña">
                </div>
                <div id="cbuError" class="text-danger small mb-2" style="display: none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="confirmViewCBU" class="btn btn-primary">Ver CBU</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .cbu-value {
        font-family: 'Roboto Mono', monospace;
        letter-spacing: 1px;
        background: #f8f9fa;
        padding: 2px 5px;
        border-radius: 3px;
    }
    .btn-view-cbu, .btn-copy-cbu {
        padding: 0.15rem 0.4rem;
        font-size: 0.75rem;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // 1. Inicializar datatable
        $('#tablaEmpleados').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            },
            columnDefs: [
                { orderable: false, targets: [3, 4] } // Deshabilitar ordenación para CBU y Acciones
            ]
        });

        // 2. Crear área inicial
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

        // 3. Verificación de CBU con autenticación de dos factores
        $(document).on('click', '.btn-view-cbu', async function() {
            const btn = $(this);
            const empleadoId = btn.data('id');
            
            const { value: password } = await Swal.fire({
                title: 'Autenticación requerida',
                html: `
                    <div class="text-start">
                        <p class="small text-muted">Para ver datos sensibles debe autenticarse</p>
                        <input type="password" id="swal-password" class="form-control" 
                               placeholder="Ingrese su contraseña" required>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Verificar',
                cancelButtonText: 'Cancelar',
                focusConfirm: false,
                preConfirm: () => {
                    const password = $('#swal-password').val();
                    if (!password) {
                        Swal.showValidationMessage('La contraseña es requerida');
                    }
                    return { password: password };
                }
            });

            if (password) {
                try {
                    const response = await $.ajax({
                        url: `/empleados/${empleadoId}/cbu/full`,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        data: { password: password }
                    });

                    if (response.success) {
                        $(`#cbu-masked-${empleadoId}`).text(response.full_cbu); 
                        btn.hide();
                        $(`.btn-copy-cbu[data-id="${empleadoId}"]`).show();
                        
                        // Registrar acceso exitoso
                        console.log(`Acceso a CBU de empleado ${empleadoId} autorizado`);
                    } else {
                        Swal.fire('Error', response.message || 'Acceso denegado', 'error');
                    }
                } catch (error) {
                    Swal.fire('Error', error.responseJSON?.message || 'Error en la autenticación', 'error');
                }
            }
        });

        // 4. Copiar CBU con feedback visual
        $(document).on('click', '.btn-copy-cbu', async function() {
            const empleadoId = $(this).data('id');
            const cbuText = $(`.cbu-value[data-id="${empleadoId}"]`).text();
            
            try {
                await navigator.clipboard.writeText(cbuText);
                const toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
                toast.fire({
                    icon: 'success',
                    title: 'CBU copiado al portapapeles'
                });
            } catch (err) {
                Swal.fire('Error', 'No se pudo copiar el CBU', 'error');
            }
        });
    });

    // 5. Función para mostrar/ocultar input de nueva área (se mantiene igual)
    function mostrarInputNuevaArea(select) {
        const inputNuevaArea = document.getElementById('input-nueva-area');
        if (select.value === 'nueva_area') {
            inputNuevaArea.style.display = 'block';
            select.selectedIndex = 0; 
        } else {
            inputNuevaArea.style.display = 'none';
        }
    }
</script>
@endpush