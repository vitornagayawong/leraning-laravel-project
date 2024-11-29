<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;
    protected $fillable = ['nome', 'idade', 'altura'];

    public function regras() {
        return [
            'nome' => 'required|min:2|max:100',
            'idade' => 'required|digits_between:1,3',
            'altura' => 'required'
        ];
    }

    public function feedbacks() {
        return [
            'required' => 'O :attribute é obrigatório!',
            'nome.min' => 'O nome deve conter no mínimo 2 caracteres',
            'nome.max' => 'O nome deve conter no máximo 100 caracteres',
            'idade.digits_between' => 'A altura deve estar entre 0 e 999 anos'
        ];
    }

    public function contaBancaria() {
        return $this->hasOne('App\Models\ContaBancaria', 'cliente_id', 'id');
    }

    public function pedidos() {
        return $this->hasMany('App\Models\Pedido', 'cliente_id', 'id');

        /*
            Parâmetros do hasMany:
               1) 'App\Models\Pedido': Indica o modelo relacionado.
               2) 'cliente_id': O campo na tabela pedidos que faz referência à tabela clientes.
               3) 'id': O campo na tabela clientes que está sendo referenciado.
        */
    }
}
