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

Route::group(['namespace' => 'API'], function () {
    Route::post('/login', 'AuthController@login');
});

Route::group(['middleware' => ['jwt.auth'], 'namespace' => 'API'], function () {
    Route::get('/setor', 'SetorController@index');
    Route::get('/leito', 'LeitoController@index');
    Route::get('/localidade', 'LocalidadeController@index');
});

Route::group(['middleware' => ['jwt.auth'], 'namespace' => 'API\Paciente', 'prefix' => 'paciente/'], function () {
    Route::get('/classificacao', 'ClassificacaoController@index');
});