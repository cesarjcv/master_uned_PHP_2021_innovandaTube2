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

Route::get('/categoria/{idcategoria}/videos', 'CategoriaController@videosPorCategoria');
Route::put('/categoria/porid', 'CategoriaController@categoriasPorID');
Route::put('/categoria/convideo', 'CategoriaController@categoriasConVideo');
Route::put('/video/buscar', 'VideoController@buscar');

/*Route::middleware(['ldapauth'])->group(function () 
{*/
    Route::apiResource('/canal', 'CanalController');
    Route::put('/categoria/cambiovisibilidad/{idcategoria}', 'CategoriaController@cambiarVisible');
    Route::apiResource('/categoria', 'CategoriaController');
    Route::put('/video/visibilidad', 'VideoController@cambiarVisibilidad');
    Route::apiResource('/video', 'VideoController');
    Route::put('/video/categorias/{idvideo}', 'VideoController@establecerCategorias');
    Route::put('/canal/categorias/{idcanal}', 'CanalController@establecerCategoria');
    Route::delete('/canal/categorias/{idcanal}', 'CanalController@quitarCategoria');
    
/*});*/

