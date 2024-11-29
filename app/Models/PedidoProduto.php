<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoProduto extends Model
{
    use HasFactory;

    protected $fillable = ['produto_id', 'pedido_id', 'quantidade_do_produto', 'valor_do_produto', 'desconto'];


    public function regras() {
        return [
            'produto_id' => 'required|exists:produtos,id|numeric|min:1',
            'pedido_id' => 'required|exists:pedidos,id|numeric|min:1',
            'quantidade_do_produto' => 'required|numeric|min:1',
            'valor_do_produto' => 'required|numeric|min:0.01',
            'desconto' => 'required|numeric|min:0'
        ];
    }

    public function feedbacks() {
        return [
            'required' => 'O campo :attribute é obrigatório!',
            'produto_id.exists' => 'Produto com o id informado não foi encontrado',
            'produto_id.numeric' => 'O id do produto deve ser do tipo numérico!',
            'produto_id.min' => 'O id do produto deve ser maior ou igual a 1!',
            'pedido_id.exists' => 'Pedido com o id informado não foi encontrado',
            'pedido_id.numeric' => 'O id do pedido deve ser do tipo numérico!',
            'pedido_id.min' => 'O id do pedido deve ser maior ou igual a 1!',
            'quantidade_do_produto.numeric' => 'A quantidade do produto deve ser do tipo numérico!',
            'quantidade_do_produto.min' => 'A quantidade do produto deve ser maior ou igual a 1',
            'valor_do_produto.numeric' => 'O valor do produto deve ser do tipo numérico!',
            'valor_do_produto.min' => 'O valor do produto deve ser maior ou igual a 0.01 reais',
            'desconto' => 'O desconto deve ser no mínimo 0'
        ];
    }

   
}
