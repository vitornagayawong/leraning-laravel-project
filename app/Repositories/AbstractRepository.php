<?php

namespace App\Repositories;

use App\Models\Produto;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository {
    protected $model;

    public function __construct(Model $model) {
        $this->model = $model;
    }

    public function selectAtributosRegistrosRelacionados($atributos) {
        //$this->model = $this->model->with($atributos); //atualizando sempre o estado do model
    }

    public function filtro($filtros) {
        //$multiplasCondicoes = $request->filtro;
            //dd($multiplasCondicoes);
            $filtros = explode(';', $filtros);
            //dd($condicaoDeMultiplasCondicoes);
            foreach($filtros as $key => $value) {
                $condicaoSeparadaDeFato = explode(':', $value);

                $this->model = $this->model->where(
                    $condicaoSeparadaDeFato[0], 
                    $condicaoSeparadaDeFato[1], 
                    $condicaoSeparadaDeFato[2]
                );
            }
            //dd($condicao);
            //Eu estava usando "with" aqui, na verdade é "where"!
    }

    public function filtroNomeProd($filtros) { //interligado com o front
        $filtros = explode(':', $filtros);
        //dd($filtros);
        $filtroNome = explode(' ', $filtros[2]);
        foreach($filtroNome as $value) {
            $this->model = $this->model->where($filtros[0], $filtros[1], '%' . $value . '%');
        }
    }

    public function selectAtributos($atributos) {
        $this->model = $this->model->selectRaw($atributos);
    }

    public function moreThanZero() {        
        // $this->model = $modelos->filter(function($item) {
        // //////////////////////////////
        //     return $item->estoque > 0;
        // });
        // //dd('aaquiiiiiii');
        // //dd($this->model);

        $this->model = $this->model->where('estoque', '>', 0);
    }

    public function getPaginated() {        
        return $this->model->paginate(5); //paginação dinâmica enviada para o front  
    }

    public function getNormal() {
        return $this->model->get();
    }

    //MINHAS FUNÇÕES PARA TESTE
    public function find($id) {
        $this->model = $this->model->find($id);
        return $this->model;
    }
}

?>
