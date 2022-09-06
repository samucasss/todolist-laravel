<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TecnologiaRestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserRestController;
use App\Http\Controllers\PreferenciasRestController;
use App\Http\Controllers\TarefaRestController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/', function () {
    return 'Hello World com Laravel';
});

Route::get('/tecnologias', [TecnologiaRestController::class, 'findAll']);

Route::controller(AuthController::class)->group(function () {
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::get('auth/get', [AuthController::class, 'get']);
});

Route::controller(UserRestController::class)->group(function () {
    Route::post('usuarios', [UserRestController::class, 'save']);
    Route::delete('usuario', [UserRestController::class, 'delete']);
});

Route::controller(PreferenciasRestController::class)->group(function () {
    Route::post('preferencias', [PreferenciasRestController::class, 'save']);
    Route::get('preferencia', [PreferenciasRestController::class, 'get']);
    Route::delete('preferencia', [PreferenciasRestController::class, 'delete']);
});

Route::controller(TarefaRestController::class)->group(function () {
    Route::get('tarefas', [TarefaRestController::class, 'findAll']);
    Route::post('tarefas', [TarefaRestController::class, 'save']);
    Route::delete('tarefas', [TarefaRestController::class, 'delete/{id}']);
});
