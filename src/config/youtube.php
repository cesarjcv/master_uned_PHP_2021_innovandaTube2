<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Nombre aplicación en youtube
    |--------------------------------------------------------------------------
    */

    'youtube_app_name' => env('YOUTUBE_APP_NAME', 'prueba'),

    /*
    |--------------------------------------------------------------------------
    | clave para aplicación
    |--------------------------------------------------------------------------
    */

    'youtube_key' => env('YOUTUBE_KEY', ''),

    /*
    |-------------------------------------------------------------------------
    | tiempos para actualizar infromación de Youtube
    |-------------------------------------------------------------------------
    */
    'youtube_tiempo_actualizar_canal' => env('YOUTUBE_HORAS_CANAL', 5),
    'youtube_tiempo_actualizar_lista' => env('YOUTUBE_HORAS_LISTA', 5),
    'youtube_tiempo_actualizar_video' => env('YOUTUBE_HORAS_VIDEO', 24),
    'youtube_maximo_consultas' => env('YOUTUBE_MAXIMO_CONSULTAS', 10000),
];
?>