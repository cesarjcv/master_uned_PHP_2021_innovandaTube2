<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name')}}</title>

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
                    <!--<a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>-->

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