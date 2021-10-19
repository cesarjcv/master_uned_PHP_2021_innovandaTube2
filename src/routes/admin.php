<?php

use Illuminate\Support\Facades\Route;
//use App\Http\Controllers\PrincipalController;

/*
|--------------------------------------------------------------------------
| Rutas de administración
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "admin" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/
Route::prefix('admin')->group(function () 
{
    
    Route::get('/login', 'LdapController@login');
    Route::post('/login', 'LdapController@autentificar');
    Route::get('/logout', 'LdapController@logout');

    Route::middleware(['ldapauth'])->group(function () 
    {
        Route::get('/', 'AdminController@canales'); 
        Route::get('/canales', 'AdminController@canales');
        Route::get('/categorias', 'AdminController@categorias');
        Route::get('/videos', 'AdminController@videos');
        Route::get('/administradores', 'AdminController@administradores');
    });
});
?>