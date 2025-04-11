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
    
    <!-- CSS de DataTables (ANTES de Bootstrap) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    
    <!-- CSS de terceros -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @vite(['resources/css/app.scss', 'resources/css/paneles/rrhh-valentina.scss'])
	<!-- CSRF Token (ESENCIAL para Laravel) -->
	<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Favicon (opcional pero recomendado) -->
	<link rel="icon" href="{{ asset('img/favicon.ico') }}">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    @yield('content')
    
    <!-- Scripts BASE (orden CRÍTICO) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    
    <!-- DataTables (DESPUÉS de jQuery y Bootstrap) -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Vite (módulos ES) -->
    @vite(['resources/js/app.js', 'resources/js/paneles/rrhh-valentina.js'])
    
    <!-- Scripts específicos de vistas -->
    @stack('scripts')
</body>
</html>