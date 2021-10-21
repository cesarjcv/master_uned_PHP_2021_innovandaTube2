<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Canal;
use App\Innovanda\DatosYoutube;
use App\Models\ListaReproduccion;
use App\Models\Video;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;

class DescargaDatosYoutube implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $youtube;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
        $this->youtube = null;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // fecha actual para contador (Los Ángeles)
        $f = new \DateTime("now", new \DateTimeZone('America/Los_Angeles'));
        // comprobar el tiempo que queda para terminar el día (periodo de cuenta)
        // si faltan menos de 10 mintos para finalizar día no realizamos opraciones
        if ($f->format('H:i:s') > '23:50:00') return;

        $this->youtube = new DatosYoutube($f->format('Y-m-d'));
        
        if ($this->youtube->dentroLimite()) // ni so se superó límite de consultas
        {
            // recuperación de datos de canales
            $this->gestionCanales();

            // recupración datos de listas de reproducción
            $this->gestionListasReproduccion();

            // recuperación datos de vídeos
            $this->gestionVideos();
        }
        

        // liberar variable
        unset($this->youtube);
    }

    /**
     * Datos sobre canales de Youtube
     * 
     * @return void
     */
    private function gestionCanales()
    {
        // calcular el valor de tiempo límite para buscar actualización de canal
        // ahora - x horas
        $tiempoReferencia = new \DateTime();
        $tiempoReferencia->sub(new \DateInterval("PT" . Config::get('youtube.youtube_tiempo_actualizar_canal') . "H"));
        $valorT = $tiempoReferencia->format('Y-m-d H:i:s');
        
        $canales = Canal::where('actualizado', '<', $valorT)->orderBy('actualizado', 'asc')->get(); // listado de canales a actualizar

        foreach($canales as $canal)
        {
            if ($canal->actualizado == "1000-01-01 00:00:00") // nunca se ha recuperado información de listas de reproducción de este canal
            {
                // recuperar lista de reproducción de videos subidos por canal (es una lista de reproducción especial de cada canal)
                $lista = $this->youtube->getListaSubidosCanalPorId($canal->id, $canal->channelid);
                if ($lista)
                {
                    $lista->save();
                }
                // poner fecha de actualización
                $canal->actualizado = date('Y-m-d H:i:s');
                $canal->save(); // atualizar base de datos
            }
            else
            {
                // leer datos de canal de yotube
                $c = $this->youtube->getDatosCanalPorId($canal->channelid);
                // comprobar si hay datos actualizados (comprobar valolr de tag)
                if ($c->etagDatos == $canal->etagDatos)
                {
                    // los datos son los mismos de la última consulta
                    $canal->actualizado = date('Y-m-d H:i:s'); // actualizar fecha de actualizado de información
                }
                else
                {
                    // actualizar datos
                    $canal->nombre = $c->nombre;
                    $canal->descripcion = $c->descripcion;
                    $canal->imagen = $c->imagen;
                    $canal->etagDatos = $c->etagDatos;
                    $canal->actualizado = date('Y-m-d H:i:s');
                }
                    
                $canal->save(); // guardar en base de datos
            }
        }
    }

    /**
     * Guardar o recuperar lista de reproducción en base de datos
     * @param ListaReproduccion $listaR datos de la lista de reproducción a guardar
     * @return void
     */
    private function guardarListaReproduccion($listaR)
    {
        try
        {
            $listaR->save(); // guardar nueva lista en base de datos
        }
        catch (QueryException $e) 
        {
            return response()->json(['error' => 'Error al insertar lista de reproducción en base de datos.']);
        }
    }

    /**
     * Datos sobre listas de reproducción de canales de Youtube
     * También incluye las lista de videos subidos
     * @return void
     */
    private function gestionListasReproduccion()
    {
        // calcular el valor de tiempo límite para buscar actualización de lista de reproducción
        // ahora - x horas
        $tiempoReferencia = new \DateTime();
        $tiempoReferencia->sub(new \DateInterval("PT" . Config::get('youtube.youtube_tiempo_actualizar_lista') . "H"));
        $valorT = $tiempoReferencia->format('Y-m-d H:i:s');
        
        $listas = ListaReproduccion::where('actualizado', '<', $valorT)->orderBy('actualizado', 'asc')->get(); // listado de listas de reproducción

        foreach($listas as $lista) // recorrer listas
        {
            if (preg_match("/^UU[a-z,A-Z,0-9,_,-]*$/", $lista->listid)) // solamente lista de videos subidos
            {
                if ($lista->actualizado == "1000-01-01 00:00:00") // nunca se ha recuperado información de videos de esta lista de reproducción 
                {
                    // obtener videos de lista de reproducción 
                    $etag = "";
                    $videos = $this->youtube->getVideosListaReproduccionPorId($lista->id, $lista->listid, $etag);
                    foreach($videos as $video)
                    {
                        $video->save();
                    }
                    // poner fecha de actualización
                    $lista->actualizado = date('Y-m-d H:i:s');
                    $lista->etagVideos= $etag;
                    $lista->save(); // atualizar base de datos
                }
                else
                {
                    // leer datos de canal de yotube
                    $etag = $lista->etagVideos;
                    $videos = $this->youtube->getVideosListaReproduccionPorId($lista->id, $lista->listid, $etag);

                    // comprobar si hay datos actualizados
                    if ($videos == null)
                    {
                        // los datos son los mismos de la última consulta
                        $lista->actualizado = date('Y-m-d H:i:s'); // actualizar fecha de actualizado de información
                    }
                    else
                    {
                        // lista de videos actuales
                        $dVideosAct = DB::table('videos')->select("videoid")->where("idlistarep",$lista->id )->get();
                        $idVideosAct = array();
                        foreach($dVideosAct as $dv) $idVideosAct[] = $dv->videoid;
                        // nueva lista de videos
                        $idVideosN = array();
                        foreach($videos as $v) $idVideosN[] = $v->videoid;

                        // comparar listas
                        $eliminar = array_diff($idVideosAct, $idVideosN); // lista de videos a eliminar
                        $insertar = array_diff($idVideosN, $idVideosAct); // lista de videos a insertar

                        // eliminar videos que ya no existen en canal
                        foreach($eliminar as $ideli)
                        {
                            DB::table('videos')->where('videoid', $ideli)->delete();
                        }

                        // insertar nuevos videos
                        foreach($insertar as $idins)
                        {
                            // crear objeto video con datos conocidos
                            $v = new Video();
                            $v->videoid = $idins;
                            $v->idlistarep = $lista->id;
                            $v->titulo = "";
                            $v->descripcion = "";
                            $v->fecha = '1000-01-01 00:00:00';
                            $v->imagen = "";
                            // guardar en base de datos
                            $v->save();
                        }

                        // actualizar datos
                        $lista->etagVideos = $etag;
                        $lista->actualizado = date('Y-m-d H:i:s');
                    }
                    $lista->save(); // guardar en base de datos*/
                }
            }
        }
    }

    /**
     * Datos sobre vídeos de Youtube
     * 
     * @return void
     */
    private function gestionVideos()
    {
        // calcular el valor de tiempo límite para buscar actualización de vídeo
        // ahora - x horas
        $tiempoReferencia = new \DateTime();
        $tiempoReferencia->sub(new \DateInterval("PT" . Config::get('youtube.youtube_tiempo_actualizar_video') . "H"));
        $valorT = $tiempoReferencia->format('Y-m-d H:i:s');
        
        $videos = Video::where('actualizado', '<', $valorT)->orderBy('actualizado', 'asc')->get(); // listado de vídeos a actualizar

        $videosactualizar = Array();

        foreach($videos as $video)
        {
            // añadir al listado para actualizar
            $videosactualizar[] = $video;
        }

        // recuperar datos de vídeo de servidor
        $this->youtube->getDatosVideos($videosactualizar);
    }
}
