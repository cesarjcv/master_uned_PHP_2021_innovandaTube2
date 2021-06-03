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
        //dd(validator());
        $validated = $request->validate([
            'usuario' => 'required',
            'password' => 'required',
        ]);
        $datos = $request->only('usuario', 'password');
        /*$request->session()->flash('usuario', $datos['usuario']);   
        dd($request->session());*/
        

        //dd($datos);
        //dd(Auth::guard('admin'));
        if (Auth::guard('admin')->attempt($datos)) 
        {
            //dd('auten');
            //dd(Auth::guard('admin')->user());
            $request->session()->regenerate();
            //dd($request->session());
            return redirect()->intended('/admin');
        }
        //dd('no auten');
        
        return back()->withErrors([
            'errorcred' => 'El usuario o la contraseña no es correcta.',
        ])->withInput([
            'usuario' => $datos['usuario'],
        ]);
    }
    
}
?>