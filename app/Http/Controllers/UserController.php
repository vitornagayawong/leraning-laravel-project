<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{

    //protected $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    public function store(Request $request) {
        try {
            $request->validate($this->user->regras(), $this->user->feedbacks());
            $this->user->fill($request->all());
            $this->user->password = bcrypt($request->password);
            $this->user->save();
            return response()->json(['msg' => 'User cadastrado com sucesso!']);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
