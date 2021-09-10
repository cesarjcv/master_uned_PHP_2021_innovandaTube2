<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Video;
use App\Models\ListaReproduccion;

use Illuminate\Support\Facades\Log;

class VideoController extends Controller
{
    /**
     * Listado de todos los vídeos en orden inverso de insercción en base de datos
     * Con lista de categorías del mismo
     * GET      /api/video    video.index
     */
    public function index()
    {
        $videos = Video::orderBy('created_at', 'desc')->paginate(50);
        foreach ($videos as $video)
        {
            $video->categorias = DB::table('videocategorias')->select('idcategoria')->where('idvideo', $video->id)->get();

            $sql1 = Video::select('idlistarep')->where('id', $video->id)->get();
            //Log::channel('single')->info($sql1);
            $sql2 = ListaReproduccion::select('idcanal')->whereIn('id', $sql1)->get();
            //Log::channel('single')->info($sql2);
            $video->canalcategorias = DB::table('canalcategorias')->select('idcategoria')->whereIn('idcanal', $sql2)->get();
        }
        return $videos;
    }

    /**
     * Bísqueda en listado de todos los vídeos en orden inverso de insercción en base de datos
     * POST      /api/video/buscar
     * @param Request $request datos de consulta
     * @return Video[]
     */
    public function buscar(Request $request)
    {
        $condiciones = "";
        $aux = array();
        $queryaux = null;
        $query = null;
        
        // crear vector con las palabras de búsqeuda
        $palabras = explode(" ", $request->texto);
 
        // crear consulta SQL
        if ($request->titulo) // hay que buscar en título
        {
            $queryaux = Video::where('titulo', 'LIKE', '%' . $palabras[0] . '%');
            for ($i=1; $i < count($palabras); $i++)
            {
                 $queryaux->where('titulo', 'LIKE', '%' . $palabras[$i]. '%');
            }
            // comporbar limitaciones de fecha
            if (strlen($request->fini) > 0 ) $queryaux->whereDate('fecha', '>=', $request->fini);
            if (strlen($request->ffin) > 0 ) $queryaux->whereDate('fecha', '<=', $request->ffin);
        }
        if ($request->des) // hay que buscar en descripción
        {
            $query = Video::where('descripcion', 'LIKE', '%' . $palabras[0]. '%');
            for ($i=1; $i < count($palabras); $i++)
            {
                 $query->where('descripcion', 'LIKE', '%' . $palabras[$i]. '%');
            }
            // comporbar limitaciones de fecha
            if (strlen($request->fini) > 0 ) $query->whereDate('fecha', '>=', $request->fini);
            if (strlen($request->ffin) > 0 ) $query->whereDate('fecha', '<=', $request->ffin);
            // se une subconsulta de título, si existe
            if ($queryaux)
            {
                $query->union($queryaux);
            }
        }
        if (!$query) $query = $queryaux;

        $videos = $query->orderBy('fecha', 'desc')->limit(100)->get();

        return $videos;
    }

    /**
     * Asignar categorías a un vídeo
     * @param Request $request datos de consulta
     * @param int $id identificador de vídeo
     */
    public function establecerCategorias(Request $request, $id)
    {
        // eliminar lista de categorías actuales
        DB::table('videocategorias')->where('idvideo', $id)->delete();

        // insertar nueva lista de categorías
        foreach ($request->cat as $idcat)
        {
            DB::table('videocategorias')->insert(['idvideo' => $id, 'idcategoria' => $idcat]);
        }
    }
}
