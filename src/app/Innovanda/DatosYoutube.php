<?php

namespace App\Innovanda;

use Illuminate\Support\Facades\Config;
use Google\Client;
use App\Models\Canal;
use App\Models\ListaReproduccion;
use App\Models\Video;
use Illuminate\Support\Facades\Log;

class DatosYoutube
{
    private $cliente; // cliente de Google API

    /**
     * constructor
     */
    public function __construct()
    {
        // cliente de youtube
        $this->cliente = new Client();
        $this->cliente->setApplicationName(Config::get('youtube.youtube_app_name'));
        $this->cliente->setDeveloperKey(Config::get('youtube.youtube_key'));
    }
    
    static public function getDatosVideoPorId($id)
    {
        $cliente = new Client();
        $cliente->setApplicationName(Config::get('youtube.youtube_app_name'));
        $cliente->setDeveloperKey(Config::get('youtube.youtube_key'));

        $servicio = new \Google_Service_YouTube($cliente);
        $optParams = array(
            'id' => $id
        );
        $results = $servicio->videos->listVideos("snippet", $optParams);

        var_dump($results);
    }

    /**
     * Obtener los datos de un canal por el channelid
     * @param string $id  id de canal
     * @return Canal|null
     */
    public function getDatosCanalPorId($id)
    {
        

        // obtener dato de canal por ChannelID
        $servicio = new \Google_Service_YouTube($this->cliente);
        $optParams = array('id' => $id);
        $resultado = $servicio->channels->listChannels("snippet", $optParams);

        if ($resultado->pageInfo->totalResults == 1)
        {
            // crear objeto canal con datos
            $canal = new Canal();
            $canal->channelid = $resultado->items[0]->id;
            $canal->nombre = $resultado->items[0]->snippet->title;
            $canal->descripcion = $resultado->items[0]->snippet->description;
            $canal->fecha = str_replace(array("T", "Z"), array(" ", ""), $resultado->items[0]->snippet->publishedAt);
            $canal->imagen = $resultado->items[0]->snippet->thumbnails->medium->url;
            $canal->etagDatos = $resultado->items[0]->etag;
            
            return $canal;
        }
        else return null;
    }

    /**
     * Obtener los datos de canales por el usuario
     * @param string $usuario  usuario de canal
     * @return Canal|null
     */
    public function getDatosCanalesPorUsuario($usuario)
    {
        // buscar lista de canales por usuarios
        $servicio = new \Google_Service_YouTube($this->cliente);
        $optParams = array('forUsername' => $usuario);
        $resultado = $servicio->channels->listChannels("snippet", $optParams);

        if ($resultado->pageInfo->totalResults > 0)
        {
            $canales = array();
            for ($i = 0; $i < $resultado->pageInfo->totalResults; $i++)
            {
                // crear un objeto Canal por cada resultado
                $canal = new Canal();
                $canal->channelid = $resultado->items[$i]->id;
                $canal->nombre = $resultado->items[$i]->snippet->title;
                $canal->descripcion = $resultado->items[$i]->snippet->description;
                $canal->fecha = str_replace(array("T", "Z"), array(" ", ""), $resultado->items[$i]->snippet->publishedAt);
                $canal->imagen = $resultado->items[$i]->snippet->thumbnails->medium->url;
                $canal->etagDatos = $resultado->items[$i]->etag;
                $canales[] = $canal;
            }
            
            return $canales;
        }
        else return null;
    }

    /**
     * Obtener los datos de canal por customURL
     * @param string $url  
     * @return Canal|null
     */
    public function getDatosCanalPorCURL($url)
    {
        $vcurl = curl_init($url); // Inicia sesión cURL
        curl_setopt($vcurl, CURLOPT_RETURNTRANSFER, TRUE); // Configura cURL para devolver el resultado como cadena
        curl_setopt($vcurl, CURLOPT_SSL_VERIFYPEER, false); // Configura cURL para que no verifique el peer del certificado dado que nuestra URL utiliza el protocolo HTTPS
        curl_setopt($vcurl, CURLOPT_FOLLOWLOCATION, true); // seguir redirección de página
        $info = curl_exec($vcurl); // Establece una sesión cURL y asigna la información a la variable $info
        $res = curl_getinfo ($vcurl , CURLINFO_HTTP_CODE); // resultado de la petición a servidor
        curl_close($vcurl); // Cierra sesión cURL

        if ($info === null || $res != 200) return null; // no existe página en URL

        // crear un objeto DOMDocument para tratar página
        $documento = new \DOMDocument();
        // cargar contenido de la página, ignorando avisos
        @$documento->loadHTML($info);
        // crear selector para búsqueda de partes    
        $selector = new \DOMXPath($documento);

        // buscar el nodo "meta" que contiene la información referida al channelid
        $codigo = "";
        $nodos = $selector->query('//meta[@itemprop="channelId"]'); // buscar nodo con: itemprop="channelId"
        if ($nodos->length == 1)
        {
            foreach($nodos[0]->attributes as $at)
            {
                if ($at->name == "content") // leer propiedad content (id de canal) de etiqueta
                {
                    $codigo = $at->value;
                    break;
                }
            }
        }
        else return null;

        return $this->getDatosCanalPorId($codigo); // buscar datos de canal por channelid
    }

    /**
     * Obtener lista de reproducción de videos subidos de un canal por el channelid
     * @param string $id  id de canal en base de datos
     * @param string $channelid identificador de canal en Youtube
     * @return ListaReproduccion|null
     */
    public function getListaSubidosCanalPorId($id, $channelid)
    {
        // obtener dato de canal por ChannelID
        $servicio = new \Google_Service_YouTube($this->cliente);
        $optParams = array('id' => $channelid);
        // lista de videos subidos
        $resultado = $servicio->channels->listChannels("contentDetails", $optParams);

        if ($resultado->pageInfo->totalResults == 1)
        {
            // crear objeto lista de reproducción con datos
            $lr = new ListaReproduccion();
            $lr->listid = $resultado->items[0]->contentDetails->relatedPlaylists->uploads;
            $lr->idcanal = $id;
            $lr->nombre = "lista videos cargados";
            $lr->descripcion = "";
            $lr->fecha = null;
            $lr->imagen = "";
            $lr->etagDatos = $resultado->items[0]->etag;
            
            return $lr;
        }
        else return null;
    }

    /**
     * Obtener listas de reproducción creadas en un canal por el channelid
     * @param string $id  id de canal en base de datos
     * @param string $channelid identificador de canal en Youtube
     * @param string $etag valor etag para lista reproducción de canal. Para conocer si cambió listas de reproducción desde última consulta.
     * @return ListaReproduccion[]|null
     */
    public function getListasReproduccionCanalPorId($id, $channelid, &$etag)
    {
        $lrTotal = array();
        // obtener datos de listas de reproducción por ChannelID
        $servicio = new \Google_Service_YouTube($this->cliente);
        $nextToken = ""; // almacena valor de token, para cuando el tamaño del listado requiere varias llamadas al servidor
        do
        {
            // obnter listado de listas de reproducción del canal
            $optParams = array('channelId' => $channelid, 'pageToken' => $nextToken, 'maxResults' => '5');
            $resultado = $servicio->playlists->listPlaylists("snippet", $optParams);

            // comprobar valor etag devuelto
            // si el igual, la lista de reproducciones no se modificaron desde la última consulta
            if ($resultado->etag == $etag) return null;

            if ($resultado->pageInfo->totalResults > 0)
            {
                foreach ($resultado->items as $item)
                {
                    // crear objeto lista de reproducción con datos
                    $lr = new ListaReproduccion();
                    $lr->listid = $item->id;
                    $lr->idcanal = $id;
                    $lr->nombre = $item->snippet->title;
                    $lr->descripcion = $item->snippet->description;
                    $lr->fecha = $item->snippet->publishedAt;
                    $lr->imagen = $item->snippet->thumbnails->default->url;
                    $lr->etagDatos = $item->etag;
                    $lrTotal[] = $lr;
                }
            }
            $nextToken = $resultado->nextPageToken;
        }
        while (strlen($nextToken) > 0); // seguir enviando solicitudes mientras queden resultados por recuperar
        $etag = $resultado->etag;
        return $lrTotal;
    }

    /**
     * Obtener videos de listas de reproducción por el listid
     * @param string $id  id de lsita de reproducción en base de datos
     * @param string $listid identificador de lista en Youtube
     * @param string $etag valor etag para videos de lista reproducción. Para conocer si cambió el listado de videos desde última consulta.
     * @return Videos[]|null
     */
    public function getVideosListaReproduccionPorId($id, $listid, &$etag)
    {
        $vTotal = array();
        // obtener datos de listas de reproducción por ChannelID
        $servicio = new \Google_Service_YouTube($this->cliente);
        $nextToken = ""; // almacena valor de token, para cuando el tamaño del listado requiere varias llamadas al servidor
        do
        {
            // obtener videos de lista de reproducción
            $optParams = array('playlistId' => $listid, 'pageToken' => $nextToken, 'maxResults' => '5');
            $resultado = $servicio->playlistItems->listPlaylistItems("snippet", $optParams);

            // comprobar valor etag devuelto
            // si el igual, la lista de reproducción no se modificó desde la última consulta
            if ($resultado->etag == $etag) return null;

            if ($resultado->pageInfo->totalResults > 0)
            {
                foreach ($resultado->items as $item)
                {
                    // crear objeto vídeo con datos
                    $v = new Video();
                    $v->videoid = $item->snippet->resourceId->videoId;
                    $v->idlistarep = $id;
                    $v->titulo = $item->snippet->title;
                    $v->descripcion = $item->snippet->description;
                    $v->fecha = $item->snippet->publishedAt;
                    $v->imagen = $item->snippet->thumbnails->medium->url;
                    $v->etagDatos = $item->etag;
                    $v->embedHtml = "";
                    $vTotal[] = $v;
                }
            }
            $nextToken = $resultado->nextPageToken;
        }
        while (strlen($nextToken) > 0); // seguir enviando solicitudes mientras queden resultados por recuperar
        $etag = $resultado->etag;
        return $vTotal;
    }
}
?>