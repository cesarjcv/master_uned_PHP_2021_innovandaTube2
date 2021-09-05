<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LdapAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // comprobar si hay sesión de usuario administrador
        if (Auth::guard('admin')->user() === null) 
        {
            // redirigir a página de entrada de credenciales
            return redirect('/admin/login');
        }
        
        return $next($request);
    }
}
