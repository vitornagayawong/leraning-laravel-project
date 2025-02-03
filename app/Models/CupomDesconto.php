<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class CupomDesconto extends Model
{
    use HasFactory;
    protected $table = 'cupom_desconto';
    protected $fillable = ['codigo', 'valor_desconto'];
    protected $primaryKey = 'id'; //eu tinha colocado ['id'], o laravel entende que isso é uma chave primária composta e ele dá pau!

    public function regras() {
        return [
            'codigo' => 'required|min:1|max:3|unique:cupom_desconto,codigo'.$this->id, //cupom_desconto é a tabela lá no banco que eu quero que a coluna código seja única
            'valor_desconto' => 'required|numeric|min:0|max:100'
        ];
    }

    public function feedbacks() {
        return [
            'codigo.required' => 'O código é obrigatório',
            'codigo.min' => 'O código deve conter no mínimo 1 caracter',
            'codigo.max' => 'O código deve conter no máximo 3 caracteres',
            'codigo.unique' => 'Já existe esse código, entre com um código diferente!',
            'valor_desconto.required' => 'O valor do desconto é obrigatório!',
            'valor_desconto.numeric' => 'O valor do desconto deve ser do tipo numérico!',
            'valor_desconto.min' => 'O valor do desconto deve ser no mínimo 0',
            'valor_desconto.max' => 'O valor do desconto deve ser no máximo 100',
        ];
    }

    public function pedido() {
        return $this->hasMany('App\Models\Pedido');
    }
}
