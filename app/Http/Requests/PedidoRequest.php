<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PedidoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'cliente_id' => 'required|exists:App\Models\Cliente,id', //como o relacionamento é de 1 para N, então aqui não precisa do "unique"
            'data' => 'date|date_format:Y-m-d|after_or_equal:2024-11-26|before_or_equal:2025-12-31',
            'valor_total' => 'required|numeric|min:0',
            'forma_pgt' => 'required'
        ];
    }
}
