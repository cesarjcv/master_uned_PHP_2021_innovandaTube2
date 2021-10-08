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
    public function index(Request $request)
    {
        $videos = null;
        // filtrar por canal
        if ($request->fcanal > 0)
        {
            $lr = ListaReproduccion::select('id')->where('idcanal', $request->fcanal)->get();
            $videos = Video::whereIn('idlistarep', $lr);
        }
        // vídeos sin categoría
        if ($request->fcat == -1)
        {
            // listas de reproducción de canales con categoría
            $lr = ListaReproduccion::select('id')->join('canalcategorias', 'lista_reproduccions.idcanal', '=', 'canalcategorias.idcanal')->get();

            if ($videos) $videos = $videos->whereNotIn('idlistarep', $lr);
            else $videos = Video::whereNotIn('idlistarep', $lr);

            // lista de vídeos con categoría
            $lva = DB::table('videocategorias')->select('idvideo')->get();
            $lv = array();
            foreach($lva as $v) $lv[] = $v->idvideo;

            $videos = $videos->whereNotIn('id', $lv);
        }
        // filtro por categoría
        if ($request->fcat > 0)
        {
            // lista videos en reproducción de canales con la categoría
            $lr = ListaReproduccion::select('videos.id')->join('canalcategorias', 'lista_reproduccions.idcanal', '=', 'canalcategorias.idcanal')
            ->where('canalcategorias.idcategoria', $request->fcat)->join('videos', 'videos.idlistarep', '=', 'lista_reproduccions.id')->get();
            $lv = array();
            foreach($lr as $v) $lv[] = $v->id;

            // lista de vídeos para categoría
            $lva = DB::table('videocategorias')->select('idvideo')->where('idcategoria', $request->fcat)->get();
            foreach($lva as $v) $lv[] = $v->idvideo;

            // obtener los pertenecientes al listado
            if ($videos) $videos = $videos->whereIn('id', $lv);
            else $videos = Video::whereIn('id', $lv);
        }
        // filtro por texto
        $texto = trim($request->ftexto); // eliminar espacios iniciales y finales
        if (strlen($texto) > 0) // si hay texto de búsqueda
        {
            $t = explode(" ", $texto); // separar palabras
            foreach ($t as $pal)
            {
                if (strlen($pal) > 0) // ignorar los espacios en blanco
                {
                    // buscar texto en título y descripción de vídeo
                    if ($videos) $videos = $videos->where(DB::raw("CONCAT(titulo,' ',descripcion)"), 'LIKE', '%' . $pal . '%');
                    else $videos = Video::where(DB::raw("CONCAT(titulo,' ',descripcion)"), 'LIKE', '%' . $pal . '%');
                }
            }
            
        }
        if ($videos) $videos = $videos->orderBy('created_at', 'desc')->paginate(50);
        else $videos = Video::orderBy('created_at', 'desc')->paginate(50);
        foreach ($videos as $video)
        {
            // obtener categorías del vídeo
            $video->categorias = DB::table('videocategorias')->select('idcategoria')->where('idvideo', $video->id)->get();

            $sql1 = Video::select('idlistarep')->where('id', $video->id)->get();
            $sql2 = ListaReproduccion::select('idcanal')->whereIn('id', $sql1)->get();
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

    /**
     * Cambiar visibilidad a un vídeo
     * @param Request $request datos de consulta
     * @param int $id identificador de vídeo
     */
    public function cambiarVisibilidad(Request $request)
    {
        Video::where('id', $request->idvideo)->update(['visible' => $request->visible]);
    }

    /**
     * Eliminar una categoría para un vídeo
     * @param Request $request datos de consulta
     */
    public function eliminarCategoria(Request $request)
    {
        DB::table('videocategorias')->where('idvideo', $request->idvideo)->where('idcategoria', $request->categoria)->delete();
    }

    /**
     * Añadir una categoría para un vídeo
     * @param Request $request datos de consulta
     */
    public function insertarCategoria(Request $request)
    {
        DB::table('videocategorias')->insert(['idvideo' => $request->idvideo, 'idcategoria' => $request->categoria]);
    }
}
