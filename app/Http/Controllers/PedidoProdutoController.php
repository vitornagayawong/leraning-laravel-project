<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\PedidoProduto;
use App\Repositories\PedidoProdutoRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\TryCatch;

class PedidoProdutoController extends Controller
{
    protected $pedidoProduto;

    public function __construct(PedidoProduto $pedidoProduto)
    {
        $this->pedidoProduto = $pedidoProduto;
    }

    public function index(Request $request)
    {

        //$pedidoProdutoRepository = new PedidoProdutoRepository($this->pedidoProduto);
        try {
            $exampleChatGpt = DB::table('pedido_produtos')
                ->join('pedidos', 'pedido_produtos.pedido_id', '=', 'pedidos.id')
                ->join('produtos', 'pedido_produtos.produto_id', '=', 'produtos.id')
                ->select('pedido_produtos.*', 'pedidos.data as pedido_data', 'produtos.nome as produto_nome', 'pedidos.created_at as pedido_created_at')
                ->get();

            //return response()->json($this->pedidoProduto->all(), 200);
            return response()->json($exampleChatGpt, 200);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 500);
        }
    }


    /*
    public function create()
    {
        //
    }
    */


    public function store(Request $request)
    {

        try {
            //$request->validate($this->pedidoProduto->regras(), $this->pedidoProduto->feedbacks());
            $pedido = Pedido::findOrFail($request->pedido_id);

            $currentTimestamp = now();

            $valorTotal = 0;
            /*
            public function calcularValorTotal($pedido)
            {
                $valorTotal = 0;

                foreach ($pedido->produtos as $produto) {
                    $valorTotal += $produto->quantidade * $produto->valor;
                }

                return $valorTotal;
            }
            */

            //PIRMEIRA FORMA DE FAZER
            /*
            $pedidoProduto = new PedidoProduto();
            $pedidoProduto->produto_id = $request->produto_id;
            $pedidoProduto->pedido_id = $request->pedido_id;
            $pedidoProduto->quantidade_do_produto = $request->quantidade_do_produto;
            $pedidoProduto->valor_do_produto = $request->valor_do_produto;
            $pedidoProduto->desconto = $request->desconto;
            $pedidoProduto->save();
            */

            /* //SEGUNDA FORMA DE FAZER
            $pedidoProduto = $this->pedidoProduto->create($request->all());
            */

            /* VAI FUNCIONAR SE EU USAR O "RAW" LÁ NO POSTMAN
            {
                "pedido_id": 7,
                "produtos" : [
                    {
                        "produto_id" : 5,
                        "quantidade_do_produto": 3,
                        "valor_do_produto": 10,
                        "desconto": 10
                    },
                    {
                        "produto_id" : 6,
                        "quantidade_do_produto": 153,
                        "valor_do_produto": 16,
                        "desconto": 17
                    }
                ]
            }

            foreach ($request->produtos as $p) {
                //dump($produto);
                $valorTotal += $p['valor_do_produto'];
            }
            */

            foreach($request->produtos as $p) {
                $valorTotal += $p['preco'];
            }

            dd($valorTotal);

            $qtdProd = $request->quantidade_do_produto;
            $valorProd = $request->valor_do_produto;
            $desc = $request->desconto / 100;

            $valorTotal = intval($qtdProd) * floatval($valorProd) * (1 - $desc);

            //TERCEIRA FORMA DE FAZER
            $pedidoProduto = $pedido->produtos()->attach(
                $request->produto_id,
                [
                    'quantidade_do_produto' => $qtdProd,
                    'valor_do_produto' => $valorProd,
                    'desconto' => $desc,
                    'created_at' => $currentTimestamp,
                    'updated_at' => $currentTimestamp,
                    'valor_total' => $valorTotal
                ]
            );

            //$pedidoProduto = $pedido->produtos()->attach($request->produto_id, $request->produtos);

            return response()->json(['msg' => 'PedidoProduto cadastrado com sucesso', 'pedidoProd' => $pedidoProduto], 200);
        } catch (\Exception $exc) {
            return response()->json(
                ["error" => $exc->getMessage()],
                500
            );
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $pedidoProdutoRepository = new PedidoProdutoRepository($this->pedidoProduto);

            if ($request->has('atributos_pedidoProduto')) {
                $atributos_pedido_produto = $request->atributos_pedidoProduto;
                $pedidoProdutoRepository->selectAtributos($atributos_pedido_produto);
            }

            if ($request->has('filtro')) {
                //dd('aqui');
                $filtros = $request->filtro;
                $pedidoProdutoRepository->filtro($filtros);
            }

            return response()->json($pedidoProdutoRepository->getResultado(), 200);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 500);
        }
    }

    /*
    public function edit(PedidoProduto $pedidoProduto)
    {
        //
        }
        */


    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            //SIMPLESMENTE CHAMANDO PEDIDOPRODUTO SEM NENHUM RELACIONAMENTO
            $pedidoProduto = $this->pedidoProduto->find($id);

            /*
            //CHAMANDO UM PEDIDOPRODUTO COM RELACIONAMENTO
            $pedProdEspecifico = DB::table('pedido_produtos')
                ->join('pedidos', 'pedido_produtos.pedido_id', '=', 'pedidos.id')
                ->join('produtos', 'pedido_produtos.produto_id', '=', 'produtos.id')
                ->select('pedido_produtos.*', 'pedidos.data as Data do Pedido', 'produtos.descricao as Descrição do Produto')
                ->where('pedido_produtos.id', $id)
                ->first();

                //dd($pedProdEspecifico);
            */

            if (isset($pedidoProduto)) {

                if ($request->method() === 'PATCH') {
                    $regrasDinamicas = array();
                    foreach ($pedidoProduto->regras() as $key => $value) {
                        //dd($pedidoProduto->regras());
                        if (array_key_exists($key, $request->all())) {
                            $regrasDinamicas[$key] = $value;
                            //dd($regrasDinamicas);
                        }
                    }
                    //Tinha esquecido de fazer a validação
                    $request->validate($regrasDinamicas, $this->pedidoProduto->feedbacks());
                    //Tinha esquecido de dar esse comando
                    $pedidoProduto->update($request->all());
                }

                $pedidoProduto->update($request->all());

                DB::commit();

                return response()->json(['msg' => 'pedidoProduto atualizado com sucesso!', $pedidoProduto], 200);
            } else {
                DB::rollBack();
                return response()->json(['msg' => 'pedidoProduto não encontrado!'], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["error" => $e->getMessage()], 500);
        }
    }

    //código do chatgpt para o método update
    /*
    public function update(Request $request, $id)
    {
        try {
            // Buscar o registro antes de iniciar a transação
            $pedidoProduto = $this->pedidoProduto->find($id);

            if (!$pedidoProduto) {
                return response()->json(['msg' => 'pedidoProduto não encontrado!'], 404);
            }

            // Validação fora da transação
            if ($request->method() === 'PATCH') {
                $regrasDinamicas = [];
                foreach ($pedidoProduto->regras() as $key => $value) {
                    if (array_key_exists($key, $request->all())) {
                        $regrasDinamicas[$key] = $value;
                    }
                }
                $request->validate($regrasDinamicas, $this->pedidoProduto->feedbacks());
            }

            // Inicia a transação
            DB::beginTransaction();

            // Atualiza o pedidoProduto
            $pedidoProduto->update($request->all());

            // Confirma a transação
            DB::commit();

            return response()->json(['msg' => 'pedidoProduto atualizado com sucesso!', $pedidoProduto], 200);

        } catch (\Exception $e) {
            // Reverte a transação em caso de erro
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    */


    public function destroy($id)
    {
        $pedidoProduto = $this->pedidoProduto->find($id);

        if (!isset($pedidoProduto)) {
            return response()->json(['msg' => 'pedProd não encontrado!'], 404);
        }

        return response()->json(['msg' => 'pedProd deletado!', $pedidoProduto->delete()], 404);
    }
}
