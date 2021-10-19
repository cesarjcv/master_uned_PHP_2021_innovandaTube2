<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name')}} - Administraci&oacute;n</title>

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
        <div id='app'>
            <!-- cabecera -->        
            <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm innovanda_cabecera">
                <div class="container">
                    <img src="{{ asset('img/LogoAdminFlix.png') }}" class='logo'/>
                </div>
                <div class='container menu'>
                    @auth('admin')
                    <!-- menú -->
                    <div class="btn-group" role="group" aria-label="Men&uacute;">
                        <a class="btn btn-outline-dark" href="/admin/canales" role="button">Canales</a>
                        <a class="btn btn-outline-dark" href="/admin/categorias" role="button">Categor&iacute;as</a>
                        <a class="btn btn-outline-dark" href="/admin/videos" role="button">V&iacute;deos</a>
                        @if($administrador)
                            <a class="btn btn-outline-dark" href="/admin/administradores" role="button">Administradores</a>
                        @endif
                    </div>
                    @endauth
                </div>
                <div class='container menuusuario'>
                    @auth('admin')
                    <!-- usuario y salida -->
                    <span class='usuario'>{{ $usuario }}</span>
                    <a class="btn btn-outline-primary" href="/admin/logout" role="button">Cerrar Sesi&oacute;n</a>
                    @endauth
                </div>
            </nav>
            <!-- contenido -->
            <div class='innovanda_contenido'>
                @yield('contenido')            
            
            </div>
        </div>
    </body>
</html>