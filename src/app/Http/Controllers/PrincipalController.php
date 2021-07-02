<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use App\Innovanda\DatosYoutube;

class PrincipalController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Página principal de inicio
     *
     * @return Renderable
     */
    public function inicio()
    {
        //DatosYoutube::getDatosVideoPorId('SKrrIdSvnQI');
        
        return view('principalinicio');
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    /*public function videos()
    {
        return view('videos');
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    /*public function category()
    {
        return view('category');
    }*/

}
?>