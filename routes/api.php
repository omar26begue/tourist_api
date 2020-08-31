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

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', 'API\AuthController@login');
    Route::get('recuperar/{email}', 'API\AuthController@recuperarContrasena');
});

Route::group(['middleware' => 'jwt.auth', 'prefix' => 'auth'], function () {
    Route::get('info', 'API\AuthController@info');
});

Route::group(['middleware' => 'jwt.auth', 'prefix' => 'usuarios'], function () {
    Route::get('list', 'API\UsuariosController@list_usuarios');
    Route::post('add', 'API\UsuariosController@add_usuarios');
    Route::patch('update/{idusuario}', 'API\UsuariosController@update_usuarios');
    Route::delete('delete/{idusuario}', 'API\UsuariosController@delete_usuarios');
});

Route::group(['middleware' => 'jwt.auth', 'prefix' => 'comentarios'], function () {
    Route::get('list', 'API\ComentariosController@list_comentarios');
    Route::post('add', 'API\ComentariosController@add_comentario');
});

Route::group(['middleware' => 'jwt.auth', 'prefix' => 'sitios'], function () {
    Route::get('list', 'API\SitiosController@list_sitios');
    Route::post('add', 'API\SitiosController@add_sitio');
    Route::post('uploadphoto/{idsitio}', 'API\SitiosController@subir_imagen');
    Route::post('calificar/{idsitio}/{idimagen}/{calificacion}', 'API\SitiosController@calificar_imagen');
    Route::post('imagen_position/{idimagen}', 'API\SitiosController@save_position');
    Route::delete('delete/{idsitio}', 'API\SitiosController@delele_sitio');
});
