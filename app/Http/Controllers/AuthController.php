<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request) {
        $credencias = $request->all(['email', 'password']);
        //autenticação (email e senha)
        //abaixo o helper auth
        //método attempt tenta autenticar
        $token = auth('api')->attempt($credencias);        
        
        //retornar um jwt
        if($token) {
            //usuário autenticado com sucesso
            return response()->json(['token' => $token]);
        } else {
            //falha na autenticação
            return response()->json(['error' => 'Falha na autenticação!']);
        }       
    }

    public function logout() { //precisa ter um jwt válido para revogar a autorização válida no momento
        auth('api')->logout();
        return response()->json(['msg' => 'Logout realizado com sucesso!']);
    }

    public function refresh() {
        $token = auth('api')->refresh(); //só vai renovar a autorização desde que o cliente encaminhe um jwt válido
        return response()->json(['tokenAtualizado' => $token]);
    }

    public function me() {
        return response()->json(auth()->user()); // a senha não é mostrada dentro do response()->json();
    }
}
