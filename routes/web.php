<?php

use App\Mail\MensagemTesteMail;
use App\Mail\newLaravelTips;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('envio-email', function () {
    $user = new User();
    $user->name = 'Vitor';
    $user->email = 'vitorlaravel10@gmail.com';
    return new newLaravelTips($user);
    //Mail::send(new newLaravelTips($user));
});

Route::get('mensagem-teste', function () {
   
    //return new MensagemTesteMail();
    //Mail::send(new newLaravelTips($user));
    Mail::to('vnwgithub@gmail.com')->send(new MensagemTesteMail());
    return 'email enviado com sucesso';
});

Route::get('pdf', 'App\Http\Controllers\PdfController@geraPdf');
