@extends('admin')

@section('contenido')
    <div class='cuadroLogin'>
        <div></div>
        <div>
        <form method="post">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="usuario">Usuario</label>
                <input type="text" class="form-control @error('usuario') is-invalid @enderror" id="usuario" name="usuario" aria-describedby="ayudaUsuario" placeholder="Identificador de usuario" value="{{ old('usuario') }}">
            </div>
            <div class="form-group">
                <label for="password">Contrase&ntilde;a</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Contrase&ntilde;a">
            </div>
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @error('usuario')
                        <li>Debe proporcionar un identificador de usuario.</li>
                    @enderror
                    @error('password')
                        <li>Debe proporcionar la contraseña de usuario.</li>
                    @enderror
                    @error('errorcred')
                        <li>El usuario o la contraseña no es correcta.</li>
                    @endif
                </ul>
            </div>
            @endif

            <div class='centrar'>
                <button type="submit" class="btn btn-primary">Acceder</button>
            </div>
        </form>
        </div>
        <div></div>
    </div>
@endsection