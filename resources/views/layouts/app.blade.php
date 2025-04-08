<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | OCE - RRHH</title>
    @include('partials.styles')
    @yield('head')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        @include('partials.navbar')
        @include('partials.sidebar-rrhh')

        <div class="content-wrapper">
            @yield('content')
        </div>

        @include('partials.footer')
    </div>

    @include('partials.scripts')
    @yield('scripts')
</body>
</html>