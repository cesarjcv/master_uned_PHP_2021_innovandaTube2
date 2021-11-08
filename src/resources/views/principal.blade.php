<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name')}}</title>

        <link rel="shortcut icon" href="img/adminflix.ico" type="image/x-icon" />

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>

        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    </head>
    <body class="antialiased" style='background: #007336;'>
        <div id='app'>
            <!-- cabecera -->        
            <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm innovanda_cabecera">
                <div class="container">
                     <img src="{{ asset('img/LogoAdminFlix.png') }}" class='logo' onclick="window.location.reload()"/>

                    <!-- búsqueda -->
                    <principal-buscar-componente vbus="ventanabuscar"></principal-buscar-componente>
                </div>
            </nav>
            <!-- contenido -->
            <div class='innovanda_contenido'>
                @yield('contenido')
            </div>
        </div>
        <script type='text/javascript'>
        document.body.onload = function()
        {
            // código bootstrap para elementos de notas emergentes de ayuda
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {return new bootstrap.Tooltip(tooltipTriggerEl)});
        };
        </script>
    </body>
</html>