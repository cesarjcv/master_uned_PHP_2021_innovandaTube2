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
        $this->youtube = new DatosYoutube();
        //Log::channel('single')->debug('Una ejecución.');
        
        // recuperación de datos de canales
        $this->gestionCanales();

        // recupración datos de listas de reproducción
        $this->gestionListasReproduccion();

        // recuperación datos de vídeos
        $this->gestionVideos();
    }

    /**
     * Datos sobre canales de Youtube
     * 
     * @return void
     */
    private function gestionCanales()
    {
        $canales = Canal::all(); // listado de canales

        // calcular el valor de tiempo límite para buscar actualización de canal
        // ahora - x horas
        $tiempoReferencia = new \DateTime();
        $tiempoReferencia->sub(new \DateInterval("PT" . Config::get('youtube.youtube_tiempo_actualizar_canal') . "H"));
        $valorT = $tiempoReferencia->format('Y-m-d H:i:s');

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
                // obtener listas de reproducción creadas en el canal
                //$etag = "";
                /*$listasR = $this->youtube->getListasReproduccionCanalPorId($canal->id, $canal->channelid, $etag);
                foreach($listasR as $l)
                {
                    $l->save();
                }*/
                // poner fecha de actualización
                $canal->actualizado = date('Y-m-d H:i:s');
                //$canal->etagListas = $etag;
                $canal->save(); // atualizar base de datos
            }
            else
            {
                // comprobar si paso el tiempo determinado para volver a leer datos y listas de reproducción del canal
                if ($canal->actualizado < $valorT)
                {
                    // leer datos de canal de yotube
                    $c = $this->youtube->getDatosCanalPorId($canal->channelid);
                    // comprobar si hay datos actualizados
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
                    
                    // Por ahora solamente lista principal
                    // leer listas, actualizar datos e insertar nuevas
                    /*$etag = $canal->etagListas;
                    $listasR = $this->youtube->getListasReproduccionCanalPorId($canal->id, $canal->channelid, $etag);
                    if ($listasR !== null) // las listas se modificaron desde la última consulta
                    {
                        // poner en estado "eliminado" todas las listas del canal
                        ListaReproduccion::where('idcanal', $canal->id)->delete();

                        // recuperar lista de videos subidos
                        $lista = $this->youtube->getListaSubidosCanalPorId($canal->id, $canal->channelid);
                        if ($lista)
                        {
                            $this->guardarListaReproduccion($lista);
                        }

                        // recuperar otras listas
                        foreach($listasR as $l)
                        {
                            $this->guardarListaReproduccion($l);
                        }
                        $canal->etagListas = $etag;
                        
                    }*/
                    $canal->save(); // guardar en base de datos
                }
                
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
            // comporbar si la lista fue eliminada anteriormente
            $auxLista = ListaReproduccion::onlyTrashed()->where('listid', $listaR->listid)->first();
            if ($auxLista)
            {
                //Log::channel('single')->info("Restaurar");
                // restaurar lista con nuevos datos
                /*$auxLista->nombre = $canal->nombre;
                $auxCanal->descripcion = $canal->descripcion;
                $auxCanal->imagen = $canal->imagen;
                $auxCanal->etagDatos = $canal->etagDatos;*/
                // poner valor en campo "actualizado" que obligue a la ejecutar la recuperación del listado de videos en segundo plano
                $tiempoReferencia = new \DateTime();
                $tiempoReferencia->sub(new \DateInterval("PT" . Config::get('youtube.youtube_tiempo_actualizar_canal') . "H"));
                $auxLista->actualizado = $tiempoReferencia->format('Y-m-d H:i:s');
                // eliminar el valor etagVideos para obligar a leer de nuevo los videos de la lista de reproducción 
                $auxLista->etagVideos = "";
                $auxLista->restore();
                //return $auxCanal;
            }
            else
            {
                $listaR->save(); // guardar nueva lista en base de datos
                //Log::channel('single')->info("Restaurar");
            }
        }
        catch (QueryException $e) 
        {
            //Log::channel('single')->info("Código: " . $e->getCode());
            /*if ($e->getCode() == 23000)
            {
                return response()->json(['error' => 'Canal ya existe.']);
            }
            else
            {
                return response()->json(['error' => 'Error al insertar canal en base de datos.']);
            }*/
        }
        //return $canal;
    }

    /**
     * Datos sobre listas de reproducción de canales de Youtube
     * También incluye las lista de videos subidos
     * @return void
     */
    private function gestionListasReproduccion()
    {
        $listas = ListaReproduccion::all(); // listado de canales

        // calcular el valor de tiempo límite para buscar actualización de lista de reproducción
        // ahora - x horas
        $tiempoReferencia = new \DateTime();
        $tiempoReferencia->sub(new \DateInterval("PT" . Config::get('youtube.youtube_tiempo_actualizar_lista') . "H"));
        $valorT = $tiempoReferencia->format('Y-m-d H:i:s');

        foreach($listas as $lista)
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
                    // comprobar si paso el tiempo determinado para volver a leer datos y listas de reproducción del canal
                    /*if ($canal->actualizado < $valorT)
                    {
                        // leer datos de canal de yotube
                        $c = $this->youtube->getDatosCanalPorId($canal->channelid);
                        // comprobar si hay datos actualizados
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
                        
                        // leer listas, actualizar datos e insertar nuevas
                        $etag = $canal->etagListas;
                        $listasR = $this->youtube->getListasReproduccionCanalPorId($canal->id, $canal->channelid, $etag);
                        if ($listasR !== null) // las listas se modificaron desde la última consulta
                        {

                        }
                        $canal->save(); // guardar en base de datos
                    }*/
                    
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
        $videos = Video::all(); // listado de vídeos

        // calcular el valor de tiempo límite para buscar actualización de canal
        // ahora - x horas
        $tiempoReferencia = new \DateTime();
        $tiempoReferencia->sub(new \DateInterval("PT" . Config::get('youtube.youtube_tiempo_actualizar_video') . "H"));
        $valorT = $tiempoReferencia->format('Y-m-d H:i:s');

        $videosnuevos = Array();
        $videosactualizar = Array();

        foreach($videos as $video)
        {
            if ($video->actualizado == "1000-01-01 00:00:00") // nunca se ha recuperado información de video
            {
                // añadir al listado de videos nuevos (para recuperar información posteriormente)
                $videosnuevos[] = $video;
                
            }
            else
            {
                // comprobar si paso el tiempo determinado para volver a leer datos de video
                if ($video->actualizado < $valorT)
                {
                    // añadir al listado para actualizar
                    $videosactualizar[] = $video;
                    
                    /*// leer datos de canal de yotube
                    $c = $this->youtube->getDatosCanalPorId($canal->channelid);
                    // comprobar si hay datos actualizados
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
                    
 
                    $canal->save(); // guardar en base de datos*/
                }
                
            }
        }
        //Log::channel('single')->info(count($videosnuevos));
        //Log::channel('single')->info(count($videosactualizar));
        $this->youtube->getDatosVideos($videosnuevos);
        $this->youtube->getDatosVideos($videosactualizar);
    }
}
