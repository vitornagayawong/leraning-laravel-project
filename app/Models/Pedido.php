<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pedido extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['cupom_desconto_id', 'cliente_id', 'data', 'subtotal', 'desconto_porcentagem', 'valor_total', 'forma_pgt'];
    protected $primaryKey = 'id';
    protected $table = 'pedidos';

    public function regras() {
        return [
            'cliente_id' => 'required|exists:App\Models\Cliente,id', //como o relacionamento é de 1 para N, então aqui não precisa do "unique"
            'data' => 'date|date_format:Y-m-d|after_or_equal:2024-11-26|before_or_equal:2025-12-31',
            'valor_total' => 'required|numeric|min:0',
            'forma_pgt' => 'required'
        ];
    }

    public function feedbacks() {
        return [
            'cliente_id.exists' => 'Id do cliente não encontrado!',
            'cliente_id.required' => 'Id do cliente é obrigatório!',
            'data.date' => 'Este campo deve ser do tipo data!',
            'data.date_format' => 'Este campo deve estar no formato ano-mês-dia!',
            'data.after_or_equal' => 'A data deve ser igual ou depois de 26/11/2024!',
            'data.before_or_equal' => 'A data deve ser igual ou antes de 31/12/2025!',
            'valor_total.numeric' => 'O valor total do pedido deve ser do tipo numérico!',
            'valor_total.min' => 'O valor total do pedido deve ser no mínimo 0 reais',
            'valor_total.required' => 'O valor total do pedido é obrigatório',
            'forma_pgt' => 'A forma de pagamento é obrigatória!'
        ];
    }

    public function cliente() {
        return $this->belongsTo('App\Models\Cliente');
    }

    public function produtos() {
        return $this->belongsToMany(Produto::class, 'pedido_produtos', 'pedido_id', 'produto_id');
    }

    public function cupomDesconto() {
        return $this->belongsTo('App\Models\CupomDesconto');
    }
}
