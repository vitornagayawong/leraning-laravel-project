<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produto extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['nome', 'descricao', 'preco', 'peso', 'altura', 'estoque'];
    protected $table = 'produtos';
    protected $primaryKey = 'id';

    public function regras() {
        return [
            'nome' => 'required|min:2|max:50',
            'descricao' => 'required|max:100',
            'preco' => 'required|numeric|digits_between:1,4',
            'peso' => 'required|numeric|between:0,50',
            'altura' => 'required|numeric',
            'estoque' => 'required|numeric'
        ];
    }

    public function feedbacks() {
        return [
            'required' => 'O campo :attribute é obrigatório',
            'descricao.max' => 'A descricao deve conter no máximo 100 caracteres!',
            'nome.max' => 'O nome deve ter no máximo 50 caracteres',
            'nome.min' => 'O nome deve ter no mínimo 2 caracteres',
            'preco.numeric' => 'O preço deve ser do tipo numérico',
            'preco.digits_between' => 'O preço deve estar entre 0 e 9999',
            'peso.between' => 'O peso deve estar entre 0 e 50 kgs',
            'altura.numeric' => 'A altura deve ser do tipo numérico',
            'estoque.numeric' =>'O estoque deve ser do tipo numérico'
        ];
    }

    public function pedidos() {
        return $this->belongsToMany(Pedido::class, 'pedido_produtos', 'produto_id', 'pedido_id');
    }
}


