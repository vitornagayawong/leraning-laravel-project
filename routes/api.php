<?php

use App\Http\Controllers\ContaBancariaController;
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


//Route::prefix('v1')->middleware('jwt.auth')->group(function() {
Route::middleware('jwt.auth')->group(function() {
    Route::apiResource('clientes', 'App\Http\Controllers\ClienteController');
    //Route::get('account/{id}', [ContaBancariaController::class, 'show']);
    Route::post('/user', 'App\Http\Controllers\UserController@store');
    Route::apiResource('conta-bancarias', 'App\Http\Controllers\ContaBancariaController');
    Route::apiResource('pedidos', 'App\Http\Controllers\PedidoController');
    Route::apiResource('pedido-produtos', 'App\Http\Controllers\PedidoProdutoController');
    Route::apiResource('produtos', 'App\Http\Controllers\ProdutoController');
    Route::post('me', 'App\Http\Controllers\AuthController@me');
    Route::post('logout', 'App\Http\Controllers\AuthController@logout');
});

Route::post('login', 'App\Http\Controllers\AuthController@login');
Route::post('refresh', 'App\Http\Controllers\AuthController@refresh');
