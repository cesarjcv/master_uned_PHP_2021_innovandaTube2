<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Video;

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
     * Listado de categorias de un video
     */
    public function listaCategorias(Request $request, $id)
    {
        return DB::table('videocategorias')->select('idcategoria')->where('idvideo', $id)->get();
    }
}
