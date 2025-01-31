<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Repositories\ProdutoRepository;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    protected $produto;

    public function __construct(Produto $produto)
    {
        $this->produto = $produto;
    }

    public function index(Request $request)
    {
        //CRIANDO O PRODUTO REPOSITORY
        //return $this->produto->all();

        $produtoRepository = new ProdutoRepository($this->produto);

        //dd($produtoRepository);

        if($request->has('atributos_pedidos')) {
            $atributosPedidos = $request->atributos_pedidos;
            $produtoRepository->selectAtributosRegistrosRelacionados($atributosPedidos);
        } else {
            $produtoRepository->selectAtributosRegistrosRelacionados('pedidos');
        }

        if($request->has('filtro')) {
            $filtros = $request->filtro;
            $produtoRepository->filtro($filtros);
        }
        
        if($request->has('filtroNomeProd')) { //interligado com o front
            $filtros = $request->filtroNomeProd;
            $produtoRepository->filtroNomeProd($filtros);
        }

        if($request->has('atributos_produto')) {
            $atributosProduto = $request->atributos_produto;
            $produtoRepository->selectAtributos($atributosProduto);
        }



        /////////////////////////////////////////////////////////////////////////////////
        //$produtos = Produto::all();
        //dd($produtos);
        // $produtosFiltrados = $produtos->filter(function($produto) {
        //     return $produto->estoque > 0;
        // });
        
        //dd($produtosFiltrados);

        //$produtoRepository->moreThanZero($produtos);


        $produtoRepository->moreThanZero();


        // foreach($produtos as $key => $value) {
        //     //dump($key);
        //     //dump($value->estoque);
        //     if($value->estoque <= 0) {
        //         $productsThatHasQuantityMoreThanZero = 
        //     }
        // }

        // $produtos->filter(function($produto) {
        //     return $produto->quantidade > 0;
        // });


        return response()->json($produtoRepository->getPaginated(), 200);
    }

    /*
    public function create()
    {
        //
    }
    */

    public function store(Request $request)
    {
        //dd('aqui');
        $request->validate($this->produto->regras(), $this->produto->feedbacks());
        $produto = $this->produto->create($request->all());
        return response()->json(['msg' => 'Produto cadastrado com sucesso!', $produto], 200);
    }

    public function show($id)
    {
        $produto = $this->produto->find($id);

        if(isset($produto)) {
            return response()->json(['msg' => 'Produto encontrado com sucesso!', $produto], 200);
        } else {
            return response()->json(['msg' => 'Não foi possível encontrar o produto!'], 404);
        }
    }

    /*
    public function edit(Produto $produto)
    {
        //
    }
    */

    public function update($id, Request $request)
    {
        $produto = $this->produto->find($id);
        $atributos = $request->all();
        //dd($atributos);

        if(isset($produto)) {

            if($request->method() === 'PATCH') {
                $regrasDinamicas = array();
                //$teste = '';
                foreach($produto->regras() as $key => $value) {
                    //$teste .= 'Chave: '. $key . ' ---- ' . ' Valor: ' . $value  . '<br>';
                    if(array_key_exists($key, $atributos)) {
                        $regrasDinamicas[$key] = $value;
                    }
                }

                //aqui o request entende que só vai atualizar os atributos selecionados pelo client
                $request->validate($regrasDinamicas, $this->produto->feedbacks());

                //return $teste;

                //aqui os $atributos já estão sendo escolhidos parcialmente pelo client
                $prodAtualizadoSoComOsAtributosEscolhidosPeloClient = $produto->update($atributos);

                return response()->json(['msg' => 'Produto atualizado com sucesso (PATCH)!', $prodAtualizadoSoComOsAtributosEscolhidosPeloClient], 200);

            } else {
                $produto->update($request->all());

                return response()->json(['msg' => 'Produto atualizado com sucesso!', $produto], 200);
            }

        } else {
            return response()->json(['msg' => 'Não foi possível encontrar o produto!'], 404);
        }
    }


    public function destroy($id)
    {
        //apaguei Produto $produto do parâmetro dessa função
        $produto = $this->produto->find($id);
        //dd($produto->getAttributes());
        if(isset($produto)) {
            //$produto->forceDelete();
            $produto->delete();
            return response()->json(['msg' => 'Produto deletado com sucesso!', $produto], 200);
        } else {
            return response()->json(['msg' => 'Não foi possível encontrar o produto!'], 404);
        }
    }
}
