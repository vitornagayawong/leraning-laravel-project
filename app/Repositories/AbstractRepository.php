<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository {
    protected $model;

    public function __construct(Model $model) {
        $this->model = $model;
    }

    public function selectAtributosRegistrosRelacionados($atributos) {
        $this->model = $this->model->with($atributos); //atualizando sempre o estado do model
    }

    public function filtro($filtros) {
        //$multiplasCondicoes = $request->filtro;
            //dd($multiplasCondicoes);
            $filtros = explode(';', $filtros);
            //dd($condicaoDeMultiplasCondicoes);
            foreach($filtros as $key => $value) {
                $condicaoSeparadaDeFato = explode(':', $value);

                $this->model = $this->model->where($condicaoSeparadaDeFato[0], $condicaoSeparadaDeFato[1], $condicaoSeparadaDeFato[2]);

            }

            //dd($condicao);

            //Eu estava usando "with" aqui, na verdade é "where"!
    }

    public function selectAtributos($atributos) {
        $this->model = $this->model->selectRaw($atributos);
    }

    public function getResultado() {
        return $this->model->get();
    }

    //MINHAS FUNÇÕES PARA TESTE
    public function find($id) {
        $this->model = $this->model->find($id);
        return $this->model;
    }
}

?>
