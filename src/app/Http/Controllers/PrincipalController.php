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
    }

    /**
     * Página principal de inicio
     *
     * @return Renderable
     */
    public function inicio()
    {
        return view('principalinicio');
    }

}
?>