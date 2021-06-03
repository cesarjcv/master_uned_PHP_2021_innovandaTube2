<?php

namespace App\Innovanda;

use Illuminate\Support\Facades\Config;
use Google\Client;
use App\Models\Canal;
use Illuminate\Support\Facades\Log;

class DatosYoutube
{
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
    static public function getDatosCanalPorId($id)
    {
        // cliente de youtube
        $cliente = new Client();
        $cliente->setApplicationName(Config::get('youtube.youtube_app_name'));
        $cliente->setDeveloperKey(Config::get('youtube.youtube_key'));

        // obtener dato de canal por ChannelID
        $servicio = new \Google_Service_YouTube($cliente);
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
            
            return $canal;
        }
        else return null;
    }

    /**
     * Obtener los datos de canales por el usuario
     * @param string $usuario  usuario de canal
     * @return Canal|null
     */
    static public function getDatosCanalesPorUsuario($usuario)
    {
        // crear cliente de youtube
        $cliente = new Client();
        $cliente->setApplicationName(Config::get('youtube.youtube_app_name'));
        $cliente->setDeveloperKey(Config::get('youtube.youtube_key'));

        // buscar lista de canales por usuarios
        $servicio = new \Google_Service_YouTube($cliente);
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
    static public function getDatosCanalPorCURL($url)
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
        $nodos = $selector->query('//meta[@itemprop="channelId"]');
        if ($nodos->length == 1)
        {
            foreach($nodos[0]->attributes as $at)
            {
                if ($at->name == "content")
                {
                    $codigo = $at->value;
                    break;
                }
            }
        }
        else return null;

        return DatosYoutube::getDatosCanalPorId($codigo);
    }
}
?>