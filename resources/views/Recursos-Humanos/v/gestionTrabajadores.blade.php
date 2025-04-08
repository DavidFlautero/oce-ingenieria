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
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#" class="nav-link active">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Empleados</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-certificate"></i>
                            <p>Certificaciones</p>
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
                    </div>
                    <div class="card-body">
                        <table id="tablaEmpleados" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Documento</th>
                                    <th>Cargo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Aquí se cargan los empleados dinámicamente --}}
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </section>

    </div>

</div>

{{-- Modal Nuevo Trabajador --}}
@include('Recursos-Humanos.modales.modalNuevoTrabajador')

@endsection
@push('scripts')
<script src="{{ asset('js/rrhh/empleados.js') }}"></script>
@endpush
