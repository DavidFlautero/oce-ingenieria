<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Panel de Gestión RRHH - OCE">
    <meta name="author" content="OCE ingenieria y mantenimiento">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'OCE - RRHH')</title>
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- CDN CSS con SRI (Seguridad) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" 
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" 
          integrity="sha384-9XgCQwo+3+ksKjK2ZJ8X8UQv3ZUEVYfwFpXjQz8+6b8k5P5v5f5/5F5f5f5f5f5" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css" 
          integrity="sha384-9XgCQwo+3+ksKjK2ZJ8X8UQv3ZUEVYfwFpXjQz8+6b8k5P5v5f5/5F5f5f5f5f5" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" 
          integrity="sha384-9XgCQwo+3+ksKjK2ZJ8X8UQv3ZUEVYfwFpXjQz8+6b8k5P5v5f5/5F5f5f5f5f5" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
          integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Preload de assets Vite -->
    @if(app()->environment('local'))
        <link rel="modulepreload" href="{{ env('VITE_DEV_SERVER_URL') }}/@vite/client">
    @endif

    <!-- Vite CSS -->
    @vite(['resources/css/app.scss'])
    @livewireStyles

    <!-- Estilos iniciales para evitar FOUC -->
    <style>
        [x-cloak] { display: none !important; }
        body { opacity: 0; transition: opacity 0.3s ease; }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed" x-cloak>
    @yield('content')

    <!-- Scripts Externos con SRI (ORDEN CRÍTICO) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" 
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" 
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js" 
            integrity="sha384-9XgCQwo+3+ksKjK2ZJ8X8UQv3ZUEVYfwFpXjQz8+6b8k5P5v5f5/5F5f5f5f5f5" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js" 
            integrity="sha384-9XgCQwo+3+ksKjK2ZJ8X8UQv3ZUEVYfwFpXjQz8+6b8k5P5v5f5/5F5f5f5f5f5" crossorigin="anonymous"></script>
    
    <!-- AdminLTE + compatibilidad con Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js" 
            integrity="sha384-9XgCQwo+3+ksKjK2ZJ8X8UQv3ZUEVYfwFpXjQz8+6b8k5P5v5f5/5F5f5f5f5f5" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/bootstrap5-compat.min.js" 
            integrity="sha384-9XgCQwo+3+ksKjK2ZJ8X8UQv3ZUEVYfwFpXjQz8+6b8k5P5v5f5/5F5f5f5f5f5" crossorigin="anonymous"></script>

    <!-- SweetAlert2 y Toastr para notificaciones -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">

    <!-- Scripts de inicialización -->
    <script>
        // Mostrar el body cuando todo esté cargado
        document.addEventListener('DOMContentLoaded', function() {
            document.body.style.opacity = '1';
            
            // Inicializar tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
            
            // Configuración de Toastr
            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: "toast-top-right",
                preventDuplicates: true
            };
            
            // Manejo de modales mejorado
            window.iniciarModal = function(button) {
                const target = button.getAttribute('data-bs-target');
                const modal = document.querySelector(target);
                
                if (modal) {
                    const existingModal = bootstrap.Modal.getInstance(modal);
                    if (existingModal) existingModal.dispose();
                    
                    new bootstrap.Modal(modal, {
                        backdrop: 'static',
                        keyboard: false,
                        focus: true
                    }).show();
                }
            };

            // Delegación de eventos para modales
            document.body.addEventListener('click', function(e) {
                if (e.target.matches('[data-bs-toggle="modal"]')) {
                    e.preventDefault();
                    iniciarModal(e.target);
                }
            });

            // Inicialización de AdminLTE
            if (typeof $ !== 'undefined' && $.fn.Treeview) {
                $('[data-widget="treeview"]').Treeview();
            }
        });

        // Manejo de eventos Livewire
        document.addEventListener('livewire:load', function() {
            // Eventos para notificaciones
            window.addEventListener('notify', event => {
                toastr[event.detail.type](event.detail.message);
            });
            
            // Eventos para confirmación
            window.addEventListener('swal:confirm', event => {
                Swal.fire({
                    title: event.detail.title,
                    text: event.detail.text,
                    icon: event.detail.type,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Confirmar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.emit(event.detail.event, event.detail.id);
                    }
                });
            });
        });
    </script>

    <!-- Livewire Scripts -->
    @livewireScripts

    <!-- Vite JS -->
    @vite('resources/js/app.js')

    @stack('scripts')
</body>
</html>