<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\PedidoProduto;
use App\Models\Produto;
use App\Repositories\PedidoRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Mail\MensagemTesteMail;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Support\Facades\Mail;

class PedidoController extends Controller
{
    protected $pedido;

    public function __construct(Pedido $pedido)
    {
        $this->pedido = $pedido;
    }

    public function index(Request $request)
    {
        $pedidoRepository = new PedidoRepository($this->pedido);

        if ($request->has('atributos_cliente')) {

            $atributos_cliente = 'cliente:id,' . $request->atributos_cliente;
            $pedidoRepository->selectAtributosRegistrosRelacionados($atributos_cliente);
        } else {
            $pedidoRepository->selectAtributosRegistrosRelacionados('cliente');
        }

        if ($request->has('filtro')) {
            $filtros = $request->filtro;
            $pedidoRepository->filtro($filtros);
        }

        if ($request->has('atributos_pedido')) {
            $atributos_pedido = $request->atributos_pedido;
            $pedidoRepository->selectAtributos($atributos_pedido);
        }

        //chamando o relacionamento com produtos também
        //$pedidoRepository->selectAtributosRegistrosRelacionados('produtos');

        return response()->json($pedidoRepository->getPaginated(), 200);

        //=========================================================================
        //FAZENDO SEM REPOSITORY

        /*
        $modelos = array();
        if ($request->has('atributos')) {
            $atributos = $request->atributos;
            $modelos = $this->pedido->selectRaw($atributos)->with('cliente')->get();
            return response()->json($modelos, 200);
        } else {
            $pedidos = $this->pedido->with('cliente')->get();
            return response()->json($pedidos, 200);
        }
        */

        /* //MODO ANTIGO DE FAZER SEM O FILTRO ATRIBUTOS
        $pedido = $this->pedido->with('cliente')->get();
        //Carbon::parse('data')->format('d-m-Y'); //para formatar data
        return response()->json($pedido, 200);
        */
    }

    /*
    public function create()
    {
        //
    }
    */

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $dadosRequisitados = $request->all();

            //$this->pedido->data = $dataFormatada;

            if ($request->method() === 'PATCH') {
                
                $regrasDinamicas = array();
                
                foreach ($this->pedido->regras() as $key => $value) {
                    if (array_key_exists($key, $dadosRequisitados)) {
                        $regrasDinamicas[$key] = $value;
                    }
                }
                $request->validate($regrasDinamicas, $this->pedido->feedbacks());
            } else {                
                //$validator = Validator::make($request->all(),
                //$this->pedido->regras());
                //dd($validator->fails(), $validator->errors()->messages());
                $request->validate($this->pedido->regras(), $this->pedido->feedbacks());                
            }            
                       
            $pedido = $this->pedido->fill($dadosRequisitados);
           // $pedido->cupom_desconto_id = $dadosRequisitados['upom_desconto_id'];
            $pedido->save(); //AQUI NÃO PODE USAR O MÉTODO SAVE(), O MÉTODO SAVE() NÃO ACEITA UM ARRAY COMO PARÂMETRO ($dadosRequisitados é um array, é só dar um dd para conferir), somente o método create aceita um array como parâmetro     
            $pedidoId = $pedido->id;
            $totalProdutos = count($dadosRequisitados['produtos']);            
            
            $arrayDePedidos = [];
            $arrayDescricaoProdutos = [];
            //Sintaxe de array, Se $dadosRequisitados for um array, você precisa usar a notação de array para acessar o valor relacionado a "produtos".
            for($i = 0; $i < $totalProdutos; $i++) {
                //dd($dadosRequisitados);
                $pedidoProduto = new PedidoProduto();
                //$pedidoProduto->produto_descricao = $dadosRequisitados['produtos'][$i]['descricao'];
                $pedidoProduto->produto_id = $dadosRequisitados['produtos'][$i]['id'];
                $pedidoProduto->pedido_id = $pedidoId;
                $pedidoProduto->preco_unitario = $dadosRequisitados['produtos'][$i]['preco'];
                $pedidoProduto->quantidade_do_produto = $dadosRequisitados['produtos'][$i]['quantidade'];
                $pedidoProduto->valor_do_produto = $dadosRequisitados['produtos'][$i]['preco'] * $pedidoProduto->quantidade_do_produto;
                $pedidoProduto->desconto = (($dadosRequisitados['desconto_porcentagem'] / 100)  * $pedidoProduto->valor_do_produto);
                $pedidoProduto->valor_total = $pedidoProduto->valor_do_produto - $pedidoProduto->desconto;                 

                $pedidoProduto->save();

                array_push($arrayDePedidos, $pedidoProduto);
                array_push($arrayDescricaoProdutos, $dadosRequisitados['produtos'][$i]['descricao']);

                //dump($arrayDescricaoProdutos);
                
                if (!$pedidoProduto->save()) {
                    dd($pedidoProduto->errors()); 
                }
                
                //atualizando o estoque do banco de dados
                $produtosDoBanco = Produto::find($pedidoProduto->produto_id);           

                if($produtosDoBanco) {

                    if($produtosDoBanco->estoque < $pedidoProduto->quantidade_do_produto && $produtosDoBanco->estoque == 0) {
                        DB::rollBack();
                        return response()->json([$produtosDoBanco->nome .  ' está com estoque zerado!']);
                    }

                    if($pedidoProduto->quantidade_do_produto > $produtosDoBanco->estoque  && $produtosDoBanco->estoque > 0) {
                        DB::rollBack();
                        return response()->json(['error' => 'Quantidade requisitada do(a) ' . $produtosDoBanco->nome .  ' maior que a do estoque!']);
                    }

                    $produtosDoBanco->estoque -= $pedidoProduto->quantidade_do_produto;
                    $produtosDoBanco->save();
                }
            }         

            //$produtos = Produto::all();
            //$prod = Produto::find(1);                

            //OUTRA MANEIRA DE FAZER
            /*
            $pedido = new Pedido();
            $pedido->fill($dadosRequisitados); // Preenche os campos com o array recebido.
            $pedido->save(); // Salva no banco de dados.
            */
            
            // if($dadosRequisitados['user']) {
            //     $user = new User();
            //     $user->name = $dadosRequisitados['user']['name'];
            //     //dd($dadosRequisitados['user']->name);
            //     //dd($user->name);
            //     $user->email = $dadosRequisitados['user']['email'];
            //     $user->notify(new notificaUser($user));
            // }      

            if(auth()->user()) {
                //$user = auth()->user();
                
                $usuario = $dadosRequisitados['user']['name'];
                $dataHoraAtual = Carbon::now()->toDateTimeString();
                $dataHoraAtualFormatada = Carbon::parse($dataHoraAtual)->format('d/m/Y H:i:s');

                $valorTotalCompra = 0;

                foreach($arrayDePedidos as $key => $value) {
                    $valorTotalCompra += $value->valor_total; 
                }
                //  $user->email = $dadosRequisitados['user']['email'];
               
                $pdf = FacadePdf::loadView('pdf', compact('usuario', 'arrayDePedidos', 'dataHoraAtualFormatada', 'valorTotalCompra', 'arrayDescricaoProdutos'));
                $pdf->setPaper('a4')->download('invoice.pdf');
                Mail::to('vnwgithub@gmail.com')->send(new MensagemTesteMail($pdf));
                //  $user->notify(new notificaUser($user, $pdf));
            }            

            DB::commit();

            return response()->json($pedido, 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json($e);
        }
    }


    public function show($id)
    {
        //subtitui Request $request no parâmetro por $id
        $pedido = $this->pedido->with('cliente', 'produtos')->find($id);

        if ($pedido === null) {
            return response()->json(['msg' => 'Este Pedido foi excluído com softdeletes!'], 200);
        }

        return response()->json(['msg' => 'Pedido encontrado com sucesso'], 200);
    }

    /*
    public function edit(Pedido $pedido)
    {
        //
    }
    */

    public function update(Request $request, $id)
    {
        //subtitui Pedido $pedido no parâmetro por $id
        $pedido = $this->pedido->find($id); //valor antigo

        if ($pedido == null) {
            return response()->json(['msg' => 'Pedido não encontrado, impossível realizar atualização!', $pedido], 200);
        }

        if ($request->method() === 'PATCH') {

            $regrasDinamicas = array();

            foreach ($pedido->regras() as $key => $value) {
                if (array_key_exists($key, $request->all())) {
                    $regrasDinamicas[$key] = $value;
                }
            }

            $request->validate($regrasDinamicas, $this->pedido->feedbacks());
        } else {
            $request->validate($this->pedido->regras(), $this->pedido->feedbacks());
        }

        //PRIMEIRA FORMA DE PERSISTIR OS DADOS
        //$pedido->update($request->all());

        //SEGUNDA FORMA DE PERSISTIR OS DADOS
        $pedido->fill($request->all());
        $pedido->save();

        return response()->json(['msg' => 'Pedido atualizado com sucesso', $pedido], 200);
    }

    public function destroy($id)
    {
        //subtitui Request $request no parâmetro por $id
        $pedido = $this->pedido->find($id);
        $pedido->delete();
        return response()->json(['msg' => 'Pedido deletado com sucesso', $pedido], 200);
    }
}
