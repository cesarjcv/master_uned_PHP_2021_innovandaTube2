<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Video;

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
        }
        return $videos;
    }

    /**
     * Bísqueda en listado de todos los vídeos en orden inverso de insercción en base de datos
     * POST      /api/video/buscar
     */
    public function buscar(Request $request)
    {
        $condiciones = "";
        $aux = array();
        $queryaux = null;
        $query = null;
        
        $palabras = explode(" ", $request->texto);
        /*if ($request->titulo)
        {
            foreach($palabras as $pal) $aux[] = "titulo LIKE '%" . $pal . "%'";
        }
        if ($request->des)
        {
            foreach($palabras as $pal) $aux[] = "descripcion LIKE '%" . $pal . "%'";
        }*/
        //$query = Video::where(function($query) {
            if ($request->titulo)
            {
                $queryaux = Video::where('titulo', 'LIKE', '%' . $palabras[0] . '%');
                for ($i=1; $i < count($palabras); $i++)
                {
                    $queryaux->where('titulo', 'LIKE', '%' . $palabras[$i]. '%');
                }
                if (strlen($request->fini) > 0 ) $queryaux->whereDate('fecha', '>=', $request->fini);
                if (strlen($request->ffin) > 0 ) $queryaux->whereDate('fecha', '<=', $request->ffin);
                /*if ($request->des)
                {
                    foreach($palabras as $pal) $query->orWhere('descripcion', 'LIKE', '%' . $pal. '%');
                }*/
            }
            if ($request->des)
            {
                $query = Video::where('descripcion', 'LIKE', '%' . $palabras[0]. '%');
                for ($i=1; $i < count($palabras); $i++)
                {
                    $query->where('descripcion', 'LIKE', '%' . $palabras[$i]. '%');
                }
                if (strlen($request->fini) > 0 ) $query->whereDate('fecha', '>=', $request->fini);
                if (strlen($request->ffin) > 0 ) $query->whereDate('fecha', '<=', $request->ffin);
                if ($queryaux)
                {
                    $query->union($queryaux);
                }
            }
        if (!$query) $query = $queryaux;

        //});
        //Log::channel('single')->info($request->fini);
        //if (strlen($request->fini) > 0 ) $query->whereDate('fecha', '>=', $request->fini);// $condiciones = " and   '" .  ."'";
        //if (strlen($request->ffin) > 0 ) $query->whereDate('fecha', '<=', $request->ffin);//$condiciones = " and fecha <= '" . $request->ffin ."'";

        $videos = $query->orderBy('fecha', 'desc')->limit(100)->get();
        /*foreach ($videos as $video)
        {
            $video->categorias = DB::table('videocategorias')->select('idcategoria')->where('idvideo', $video->id)->get();
        }*/
        return $videos;
    }

    /**
     * Listado de categorias de un video
     */
    /*public function listaCategorias(Request $request, $id)
    {
        return DB::table('videocategorias')->select('idcategoria')->where('idvideo', $id)->get();
    }*/
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
