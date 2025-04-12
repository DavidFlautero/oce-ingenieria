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

    <!-- CDN CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Vite CSS -->
    @vite([
        'resources/css/app.scss',
        'resources/css/paneles/rrhh-valentina.scss'
    ])
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    @yield('content')

    <!-- Scripts Externos (ORDEN CRÍTICO) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!-- AdminLTE debe ir DESPUÉS de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>

    <!-- Solución Nuclear para Modales -->
    <script>
    // 1. Parche para AdminLTE + Bootstrap 5
    (function() {
        const originalModal = bootstrap.Modal;
        
        bootstrap.Modal = class extends originalModal {
            constructor(element, config) {
                // Configuración segura con valores por defecto
                const safeConfig = {
                    backdrop: (config && config.backdrop) || 'static',
                    keyboard: (config && config.keyboard) || false,
                    focus: (config && config.focus) || true,
                    ...config
                };
                
                super(element, safeConfig);
            }
            
            _initializeBackDrop() {
                this._config = this._config || {};
                this._config.backdrop = this._config.backdrop === undefined ? true : this._config.backdrop;
                super._initializeBackDrop();
            }
        };
    })();

    // 2. Inicialización segura
    document.addEventListener('DOMContentLoaded', function() {
        // Función global para abrir modales
        window.iniciarModal = function(button) {
            const target = button.getAttribute('data-bs-target');
            const modal = document.querySelector(target);
            
            if (modal) {
                // Destruye instancia previa si existe
                const existingModal = bootstrap.Modal.getInstance(modal);
                if (existingModal) existingModal.dispose();
                
                // Crea nueva instancia con configuración segura
                new bootstrap.Modal(modal, {
                    backdrop: 'static',
                    keyboard: false
                }).show();
            }
        };

        // Asigna eventos a los botones existentes
        document.querySelectorAll('[data-bs-toggle="modal"]').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                iniciarModal(this);
            });
        });

        // Inicialización de AdminLTE (si es necesario)
        if (typeof $ !== 'undefined' && $.fn.Treeview) {
            $('[data-widget="treeview"]').Treeview();
        }
    });
    </script>

    <!-- Vite JS -->
    @vite([
        'resources/js/app.js',
        'resources/js/paneles/rrhh-valentina.js'
    ])

    @stack('scripts')
</body>
</html>