<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Auth;
use App\Innovanda\DatosYoutube;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Página administración de canales 
     *
     * @return Renderable
     */
    public function canales()
    {
        return view('admincanales',
            [
                'usuario' => Auth::guard('admin')->user()->name,
                'administrador' => session('administrador'),
            ]);
    }

    /**
     * Página administración de categorías 
     *
     * @return Renderable
     */
    public function categorias()
    {
        return view('admincategorias',
            [
                'usuario' => Auth::guard('admin')->user()->name,
                'administrador' => session('administrador'),
            ]);
    }

    /**
     * Página administración de vídeos 
     *
     * @return Renderable
     */
    public function videos()
    {
        return view('adminvideos',
            [
                'usuario' => Auth::guard('admin')->user()->name,
                'administrador' => session('administrador'),
            ]);
    }

    /**
     * Página administración de usuarios con permisos de administrador
     *
     * @return Renderable
     */
    public function administradores()
    {
        return view('adminadministradores',
            [
                'usuario' => Auth::guard('admin')->user()->name,
                'administrador' => session('administrador'),
            ]);
    }
}
?>