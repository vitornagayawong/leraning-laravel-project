<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContaBancaria extends Model
{
    protected $table = 'conta_bancarias';
    protected $fillable = ['cliente_id', 'saldo', 'imagem'];
    protected $primaryKey = 'id';

    public function regras() {
        return [
            'cliente_id' => 'required|numeric',
            'saldo' => 'required|numeric|min:0|unique:conta_bancarias,saldo,'.$this->id,
            'imagem' => 'required|file|mimes:png,docx,xlsx,jpeg,pdf,ppt,mp3,mp4'

            /*
                A validação unique no Laravel tem a seguinte sintaxe:

                    -----> unique:table,column,except,idColumn

                    table: Nome da tabela (neste caso, conta_bancarias).
                    column: Nome da coluna que está sendo verificada (neste caso, saldo).
                    except: ID do registro a ser ignorado.
                    idColumn: Nome da coluna do ID (opcional, por padrão, é id).
            */
        ];
    }

    public function feedbacks() {
        return [
            'required' => 'O é obrigatório informar o :attribute',
            'cliente_id.exists' => 'Id do cliente não encontrado',
            'cliente_id.unique' => 'Já existe alguma conta bancária registrada à esse Id!',
            'saldo.required' => 'É obrigatório informar o :attribute!',
            'saldo.min' => 'Seu saldo deve ser no mínimo 0 e não pode ser negativo !',
            'saldo.numeric' => 'Seu saldo deve ser um número!',
            'saldo.unique' => 'Já existe um saldo com este valor! Insira outro valor!',
            'imagem.file' => 'A imagem deve conter algum tipo de arquivo',
            'imagem.mimes' => 'A imagem deve ser dos tipos: png,docx,xlsx,jpeg,pdf,ppt,mp3,mp4',

        ];
    }

    public function cliente() {
         return $this->belongsTo('App\Models\Cliente');
        //return $this->belongsTo(Cliente::class);
    }

    use HasFactory;
}
