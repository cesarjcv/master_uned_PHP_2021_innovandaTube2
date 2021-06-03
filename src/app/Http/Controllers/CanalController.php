<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Canal;
use App\Innovanda\DatosYoutube;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class CanalController extends Controller
{
    /**
     * constructor
     */
    public function __construct()
    {

    }

    /**
     * Listado de todos los canales eno rden inverso de insercción en base de datos
     * GET      /api/canal    canal.index
     */
    public function index()
    {
        return Canal::orderBy('created_at', 'desc')->get();
    }
    
    /**
     * Insertar nuevo canal en base de datos
     * POST 	/api/canal      canal.store
     * @param Request $request
     * @return string
     */
    public function store(Request $request) 	
    {
        // búsqueda por ChannelId
        // formatos: código 24 carácteres, https://www.youtube.com/channel/{código}/*
        if (preg_match("/^UC[a-z,A-Z,0-9,_,-]{22}$/", $request->dato) || 
            preg_match("/^https:\/\/www\.youtube\.com\/channel\/UC[a-z,A-Z,0-9,_,-]{22}[\/,a-z]*$/", $request->dato))
        {
            // obtener código
            if (strlen($request->dato) == 24) $cod = $request->dato;
            else $cod = substr(strstr($request->dato, 'channel/'), 8, 24);
            $canal = DatosYoutube::getDatosCanalPorId($cod);
            if ($canal === null)
            {
                return response()->json(['error' => 'No existe un canal con ese Código.']);
            }
            try
            {
                $canal->save();
            }
            catch (QueryException $e) 
            {
                if ($e->getCode() == 23000)
                {
                    return response()->json(['error' => 'Canal ya existe.']);
                }
                else
                {
                    return response()->json(['error' => 'Error al insertar canal en base de datos.']);
                }
            }

            return $canal;
        }
        //https://www.youtube.com/user/IslasDeCultura
        else if (preg_match("/^https:\/\/www\.youtube\.com\/user\/[\pL,\pN,_,-]+(\/[a-z,A-Z,0-9,_,-]*|)$/", $request->dato)) 
        {
            $nombre = substr(strstr($request->dato, 'user/'), 5);
            if (strpos($nombre, '/') !== false) $nombre = strstr($nombre, '/', true);
            $canales = DatosYoutube::getDatosCanalesPorUsuario($nombre);
            if ($canales === null)
            {
                return response()->json([
                    'error' => 'No existe un canal con ese nombre de usuario',
                ]);
            }
            $oError = null;
            if (is_array($canales))
            {
                foreach($canales as $canal)
                {
                    try
                    {
                        $canal->save();
                    }
                    catch (QueryException $e) 
                    {
                        if ($e->getCode() == 23000)
                        {
                            $oError = 'Un Canal de este usuario ya existe.';
                        }
                        else
                        {
                            $oError = 'Error al insertar canal en base de datos.';
                        }
                    }
                }
            }
            if ($oError) return response()->json(['error' => $oError]);
            else return $canales;
        }
        // https://www.youtube.com/c/IslasDeCultura/videos
        else if (preg_match("/^https:\/\/www\.youtube\.com\/c\/[\pL,\pN,_,-,%]+(\/[a-z,A-Z,0-9,_,-]*|)$/u", $request->dato))   
        {
            $nombre = substr(strstr($request->dato, 'c/'), 2);
            if (strpos($nombre, '/') !== false) $nombre = strstr($nombre, '/', true);
            // si no está adaptado el nombre a URL lo convertimos
            if (!preg_match("/%/", $nombre)) $nombre = urlencode($nombre);
            $canal = DatosYoutube::getDatosCanalPorCURL("https://www.youtube.com/c/" . $nombre);
            if ($canal === null)
            {
                return response()->json(['error' => 'No existe un canal con esa URL']);
            }
            try
            {
                $canal->save();
            }
            catch (QueryException $e) 
            {
                if ($e->getCode() == 23000)
                {
                    return response()->json(['error' => 'Canal ya existe.']);
                }
                else
                {
                    return response()->json(['error' => 'Error al insertar canal en base de datos.']);
                }
            }

            return $canal;
        }
        else
        {
            return response()->json(['error' => 'El URL o código no tiene un formato correcto']);
        }
        
    }
/*GET 	/photos/{photo} 	show 	photos.show
GET 	/photos/{photo}/edit 	edit 	photos.edit
PUT/PATCH 	/photos/{photo} 	update 	photos.update
*/

    /**
     * Eliminar canal y videos asociados en base de datos
     * DELETE 	/api/canal/{idcanal}      destroy
     * @param int $id   id de canal en base de datos
     * @return string
     */
    public function destroy(int $id) 	
    {
        $canal = Canal::find($id);
        $canal->delete();
        /////////////
        //////////BORRAR LOS VIDEOS ASOCIADOS
        ///////////////
    }
}
