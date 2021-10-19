<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Administrador;

use Illuminate\Support\Facades\Log;

class LdapController extends Controller
{
    /**
     * Página para proporcionar datos de autentificación
     * Entrada a administración
     *
     * @return Renderable
     */
    public function login()
    {
        return view('adminlogin');
    }

    /**
     * Cerrar sesión de administración.
    *
    * @param  \Illuminate\Http\Request $request
    * @return \Illuminate\Http\Response
    */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
    
    /**
     * Gestiona un intento de autentificación LDAP.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function autentificar(Request $request)
    {
        // objeto de validación de usuario
        $validated = $request->validate([
            'usuario' => 'required',
            'password' => 'required',
        ]);
        $datos = $request->only('usuario', 'password');

        // comprobar usuario y contraseña
        if (Auth::guard('admin')->attempt($datos)) 
        {
            $request->session()->regenerate(); // actualizar sesión
            // comprobar si el usuario tiene rol de administrador
            $adm = Administrador::where('usuario', Auth::guard('admin')->user()->usuario)->get();
            //Log::channel('single')->info(Auth::guard('admin')->user()->usuario);
            //Log::channel('single')->info($adm);
            if (count($adm) > 0)
            {
                $request->session()->put('administrador', true);
            }
            else $request->session()->put('administrador', false);
            /*Log::channel('single')->info($request->session()->has('administrador'));
            Log::channel('single')->info($request->session()->get('administrador'));
            Log::channel('single')->info($request->session()->all());*/
            return redirect()->intended('/admin'); // ir a página principal de administración
        }
        
        // devolver resultado
        return back()->withErrors([
            'errorcred' => 'El usuario o la contraseña no es correcta.',
        ])->withInput([
            'usuario' => $datos['usuario'],
        ]);
    }
    
}
?>