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
     * Página administración de canales y listas de reproducción
     *
     * @return Renderable
     */
    public function canales()
    {
        //DatosYoutube::getDatosVideoPorId('SKrrIdSvnQI');
        //dd(Auth::guard('admin')->user());
        return view('admincanales',
            [
                'usuario' => Auth::guard('admin')->user()->name,
            ]);
    }

    

}
?>