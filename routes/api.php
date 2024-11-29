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

Route::apiResource('clientes', 'App\Http\Controllers\ClienteController');
// Route::get('account/{id}', [ContaBancariaController::class, 'show']);
Route::apiResource('conta-bancarias', 'App\Http\Controllers\ContaBancariaController');
Route::apiResource('pedidos', 'App\Http\Controllers\PedidoController');
Route::apiResource('pedido-produtos', 'App\Http\Controllers\PedidoProdutoController');
Route::apiResource('produtos', 'App\Http\Controllers\ProdutoController');
