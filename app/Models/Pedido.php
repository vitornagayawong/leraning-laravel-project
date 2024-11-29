<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pedido extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['cliente_id', 'data', 'valor_total'];
    protected $primaryKey = 'id';
    protected $table = 'pedidos';

    public function regras() {
        return [
            'cliente_id' => 'required|exists:clientes,id', //como o relacionamento é de 1 para N, então aqui não precisa do "unique"
            'data' => 'required|date|date_format:Y-m-d|after_or_equal:2024-11-26|before_or_equal:2024-12-31',
            'valor_total' => 'numeric|min:0'
        ];
    }

    public function feedbacks() {
        return [
            'required' => 'O :attribute é obrigatório!',
            'cliente_id.exists' => 'Id do cliente não encotrado!',
            'data.date' => 'Este campo deve ser do tipo data!',
            'data.date_format' => 'Este campo deve estar no formato dia-mês-ano!',
            'data.after_or_equal' => 'A data deve ser igual ou depois de 26/11/2024!',
            'data.before_or_equal' => 'A data deve ser igual ou antes de 31/12/2024!',
            'valor_total.numeric' => 'O valor total do pedido deve ser do tipo numérico!',
            'valor_total.min' => 'O valor total do pedido deve ser no mínimo 0 reais',
        ];
    }

    public function cliente() {
        return $this->belongsTo('App\Models\Cliente');
    }

    public function produtos() {
        return $this->belongsToMany(Produto::class, 'pedido_produtos', 'pedido_id', 'produto_id');
    }
}
