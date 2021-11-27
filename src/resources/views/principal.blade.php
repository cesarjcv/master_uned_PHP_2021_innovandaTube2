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
    <body class="antialiased">
        <div id="app">
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

        <footer class='pie'>
            
            <div class='texto'>
            <div class="icono"><a href="https://www.uned.es/" target="_blank"><img src="{{ asset('img/uned.png') }}"/></a>
            <a href="https://www.uned.es/universidad/facultades/informatica.html" target="_blank"><img src="{{ asset('img/ets_ingInfo.png')}}"/></a></div>
            <p><span class='fun'>Autores:</span> Luc&iacute;a Quiroga Rey, Jos&eacute; &Aacute;ngel Bernal Bermejo, Francisco Jos&eacute; Alc&aacute;zar Mart&iacute;n, Carlos Luis S&aacute;nchez Bocanegra</p>
            <p><span class='fun'>Ilustrador:</span> Esteban Espinilla Fern&aacute;ndez.</p>
            <p><span class='fun'>Desarrollador:</span> C&eacute;sar J. Caraballo Vi&ntilde;a</p>
            <p><span class='fun'>Director:</span> Rafael Pastor Vargas</p>
            </div>
        </footer>
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