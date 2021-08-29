<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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