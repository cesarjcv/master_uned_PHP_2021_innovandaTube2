<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/*Route::middleware(['ldapauth'])->group(function () 
{*/
    Route::apiResource('/canal', 'CanalController');
    Route::put('/categoria/cambiovisibilidad/{idcategoria}', 'CategoriaController@cambiarVisible');
    Route::apiResource('/categoria', 'CategoriaController');
    Route::apiResource('/video', 'VideoController');
    Route::get('/video/categorias/{idvideo}', 'VideoController@listaCategorias');    
/*});*/