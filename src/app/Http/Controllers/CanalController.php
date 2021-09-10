<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Canal;
use App\Models\ListaReproduccion;
use App\Models\Video;
use App\Innovanda\DatosYoutube;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;

class CanalController extends Controller
{
    /**
     * constructor
     */
    public function __construct()
    {

    }

    /**
     * Listado de todos los canales en orden inverso de insercción en base de datos
     * GET      /api/canal    canal.index
     */
    public function index()
    {
        $canales = Canal::orderBy('created_at', 'desc')->get();
        foreach ($canales as $canal)
        {
            $canal->categorias = DB::table('canalcategorias')->select('idcategoria')->where('idcanal', $canal->id)->get();
        }
        return $canales;
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
            $yt = new DatosYoutube();
            $canal = $yt->getDatosCanalPorId($cod);
            if ($canal === null)
            {
                return response()->json(['error' => 'No existe un canal con ese Código.']);
            }
            
            return $this->guardarCanal($canal);
        }
        //https://www.youtube.com/user/IslasDeCultura
        else if (preg_match("/^https:\/\/www\.youtube\.com\/user\/[\pL,\pN,_,-]+(\/[a-z,A-Z,0-9,_,-]*|)$/", $request->dato)) 
        {
            $nombre = substr(strstr($request->dato, 'user/'), 5);
            if (strpos($nombre, '/') !== false) $nombre = strstr($nombre, '/', true);
            $yt = new DatosYoutube();
            $canales = $yt->getDatosCanalesPorUsuario($nombre);
            if ($canales === null)
            {
                return response()->json([
                    'error' => 'No existe un canal con ese nombre de usuario',
                ]);
            }
            $oError = null;
            $salidaCanales = array(); // listado de canales de salida de función
            if (is_array($canales))
            {
                foreach($canales as $canal)
                {
                    // guardar canal en base de datos
                    $c = $this->guardarCanal($canal);
                    // comprobar si hubo error
                    if (!$c instanceof Canal) $oError = $c; // mensaje de error
                    else $salidaCanales[] = $c; // añadir canal al listado de salida
                }
            }
            if ($oError) return response()->json(['error' => $oError]);
            else return $salidaCanales;
        }
        // https://www.youtube.com/c/IslasDeCultura/videos
        else if (preg_match("/^https:\/\/www\.youtube\.com\/c\/[\pL,\pN,_,-,%]+(\/[a-z,A-Z,0-9,_,-]*|)$/u", $request->dato))   
        {
            $nombre = substr(strstr($request->dato, 'c/'), 2);
            if (strpos($nombre, '/') !== false) $nombre = strstr($nombre, '/', true);
            // si no está adaptado el nombre a URL lo convertimos
            if (!preg_match("/%/", $nombre)) $nombre = urlencode($nombre);
            $yt = new DatosYoutube();
            $canal = $yt->getDatosCanalPorCURL("https://www.youtube.com/c/" . $nombre);
            if ($canal === null)
            {
                return response()->json(['error' => 'No existe un canal con esa URL']);
            }

            return $this->guardarCanal($canal);
        }
        else
        {
            return response()->json(['error' => 'El URL o código no tiene un formato correcto']);
        }
        
    }

    /**
     * Guardar o recuperar canal en base de datos
     * @param Canal $canal datos de canal a guardar
     * @return Json|Canal
     */
    private function guardarCanal($canal)
    {
        try
        {
            // comporbar si el canal fue eliminado anteriormente
            $auxCanal = Canal::onlyTrashed()->where('channelid', $canal->channelid)->first();
            if ($auxCanal) // el canla está en la base de datos en estado "eliminado"
            {
                // restaurar canal con nuevos datos
                $auxCanal->nombre = $canal->nombre;
                $auxCanal->descripcion = $canal->descripcion;
                $auxCanal->imagen = $canal->imagen;
                $auxCanal->etagDatos = $canal->etagDatos;
                // poner valor en campo "actualizado" que obligue a la ejecutar la recuperación del listado de listas de reproducción en segundo plano
                $tiempoReferencia = new \DateTime();
                $tiempoReferencia->sub(new \DateInterval("PT" . Config::get('youtube.youtube_tiempo_actualizar_canal') . "H"));
                $auxCanal->actualizado = $tiempoReferencia->format('Y-m-d H:i:s');
                $auxCanal->restore();

                // recuperar listas de reproducción
                $auxListas = ListaReproduccion::onlyTrashed()->where('idcanal', $auxCanal->id)->get();
                foreach($auxListas as $lista)
                {
                    $lista->restore();
                    // recuperar videos de lista
                    $auxVideos = Video::onlyTrashed()->where('idlistarep', $lista->id)->get();
                    foreach($auxVideos as $video)
                    {
                        $video->restore();
                    }
                }
                return $auxCanal;
            }
            else
            {
                $canal->save(); // guardar nuevo canal en base de datos
            }
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


    /**
     * Eliminar canal y videos asociados en base de datos
     * DELETE 	/api/canal/{idcanal}      destroy
     * @param int $id   id de canal en base de datos
     * @return void
     */
    public function destroy(int $id) 	
    {
        // datos de canal
        $canal = Canal::find($id);
        // listas de reproducción asociadas al canal
        $listas = ListaReproduccion::where('idcanal', $id)->get();

        foreach ($listas as $lista)
        {
            // obtener videos de la lista
            $videos = Video::where('idlistarep', $lista->id)->get();
            // eliminar videos de lista
            foreach($videos as $video)
            {
                $video->delete();
            }
            // eliminar lista
            $lista->delete();
        }
        // eliminar canal
        $canal->delete();
    }

    /**
     * Asignar categoría a un canal
     * @param Request $request datos de consulta
     * @param int $id identificador de canal
     */
    public function establecerCategoria(Request $request, $id)
    {
        // eliminar lista de categorías actuales
        //DB::table('canalcategorias')->where('idvideo', $id)->delete();

        // insertar nueva lista de categorías
        //foreach ($request->cat as $idcat)
        {
            DB::table('canalcategorias')->insert(['idcanal' => $id, 'idcategoria' => $request->cat]);
        }
    }

    /**
     * Quitar categoría a un canal
     * @param Request $request datos de consulta
     * @param int $id identificador de canal
     */
    public function quitarCategoria(Request $request, $id)
    {
        Log::channel('single')->info($id . " - " . $request->cat );
        DB::table('canalcategorias')->where('idcanal', $id)->where('idcategoria', $request->cat)->delete();
    }
}
