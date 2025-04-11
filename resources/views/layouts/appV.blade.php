<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="description" content="Panel de Gestión RRHH - OCE">
    <meta name="author" content="OCE ingenieria y mantenimiento">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'OCE - RRHH')</title>
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('img/favicon.ico') }}">
    
    <!-- CSS de DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" crossorigin="anonymous">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css" crossorigin="anonymous">
    
    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    
    @vite(['resources/css/app.scss', 'resources/css/paneles/rrhh-valentina.scss'])
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    @yield('content')
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
    
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    
    <!-- AdminLTE JS -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js" crossorigin="anonymous"></script>
    
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js" crossorigin="anonymous"></script>
    
    <!-- Vite -->
    @vite(['resources/js/app.js', 'resources/js/paneles/rrhh-valentina.js'])
    
    <!-- Scripts específicos -->
    @stack('scripts')

    <!-- Inicialización segura SIN MODIFICAR TU LÓGICA -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Solución para error _config de Bootstrap
        if (typeof bootstrap !== 'undefined') {
            document.querySelectorAll('[data-bs-toggle="modal"]').forEach(button => {
                button.addEventListener('click', function() {
                    var target = this.getAttribute('data-bs-target');
                    var modal = bootstrap.Modal.getInstance(document.querySelector(target));
                    if (!modal) {
                        new bootstrap.Modal(document.querySelector(target)).show();
                    }
                });
            });
        }

        // Inicialización segura de AdminLTE
        if (typeof $.AdminLTE !== 'undefined') {
            $.AdminLTE.layout.activate();
            if ($.AdminLTE.tree) {
                $('[data-widget="treeview"]').each(function() {
                    $.AdminLTE.tree(this);
                });
            }
        }
    });
    </script>
</body>
</html>