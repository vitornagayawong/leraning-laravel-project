<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Repositories\ClienteRepository;
use Exception;
use Facade\FlareClient\Http\Response;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    protected $cliente;

    public function __construct(Cliente $cliente)
    {
        $this->cliente = $cliente;
    }

    public function index(Request $request)
    {
        //CRIANDO O REPOSITORY
        try {

        
        
        $clienteRepository = new ClienteRepository($this->cliente);

        if ($request->has('atributos_conta')) {
            $atributos_conta = 'contaBancaria:cliente_id,' . $request->atributos_conta;
            //dd($atributos_conta);
            //dd($atributos_conta);
            //pega só os atributos da request
            $clienteRepository->selectAtributosRegistrosRelacionados($atributos_conta);
        } else {
            //pega todos os atributos da conta lá no banco de dados
            //$clientesComContaBancariaEPedidos = $this->cliente->with('contaBancaria');
            $clienteRepository->selectAtributosRegistrosRelacionados('contaBancaria');
        }



        if ($request->has('atributos_pedido')) {
            $atributos_pedido = 'pedidos:cliente_id,' . $request->atributos_pedido;
            //dd($atributos_conta);
            //pega só os atributos da request
            $clienteRepository->selectAtributosRegistrosRelacionados($atributos_pedido);
            //$clientesComContaBancariaEPedidos = $clientesComContaBancariaEPedidos->with('pedidos:cliente_id,' . $atributos_pedido);
        } else {
            //pega todos os atributos da request
            $clienteRepository->selectAtributosRegistrosRelacionados('pedidos');
            //$clientesComContaBancariaEPedidos = $clientesComContaBancariaEPedidos->with('pedidos');
        }


        if ($request->has('filtro')) {
            $clienteRepository->filtro($request->filtro);
        }

        if ($request->has('atributos_cliente')) {
            //dd($atributos_cliente);
            $clienteRepository->selectAtributos($request->atributos_cliente);
        }

        return response()->json($clienteRepository->getResultado(), 200);
        
    }catch(Exception $e){
        return response()->json([ 'Error' => $e->getMessage() ], 500);
        
    }


        //================================================================================

        //FORMA ANTIGA DE FAZER SEM O REPOSITORY

        /*
        $clientesComContaBancariaEPedidos = array();


        if($request->has('atributos_conta')) {
            $atributos_conta = $request->atributos_conta;
            //dd($atributos_conta);
            //pega só os atributos da request
            $clientesComContaBancariaEPedidos = $this->cliente->with('contaBancaria:cliente_id,' . $atributos_conta);
        } else {
            //pega todos os atributos da conta lá no banco de dados
            $clientesComContaBancariaEPedidos = $this->cliente->with('contaBancaria');
        }

        if($request->has('atributos_pedido')) {
            $atributos_pedido = $request->atributos_pedido;
            //dd($atributos_conta);
            //pega só os atributos da request
            $clientesComContaBancariaEPedidos = $clientesComContaBancariaEPedidos->with('pedidos:cliente_id,' . $atributos_pedido);
        } else {
            //pega todos os atributos da request
            $clientesComContaBancariaEPedidos = $clientesComContaBancariaEPedidos->with('pedidos');
        }

        if($request->has('filtro')) {
            //$multiplasCondicoes = $request->filtro;
            //dd($multiplasCondicoes);
            $filtros = explode(';', $request->filtro);
            //dd($condicaoDeMultiplasCondicoes);
            foreach($filtros as $key => $value) {
                $condicaoSeparadaDeFato = explode(':', $value);

                $clientesComContaBancariaEPedidos = $clientesComContaBancariaEPedidos->where($condicaoSeparadaDeFato[0], $condicaoSeparadaDeFato[1], $condicaoSeparadaDeFato[2]);

            }

            //dd($condicao);

            //Eu estava usando "with" aqui, na verdade é "where"!
        }


        if ($request->has('atributos_cliente')) {
            $atributos_cliente = $request->atributos_cliente;

            //dd($atributos_cliente);
            $clientesComContaBancariaEPedidos = $clientesComContaBancariaEPedidos->selectRaw($atributos_cliente);


        } //else {
          // $clientesComContaBancariaEPedidos = $clientesComContaBancariaEPedidos;
        //}

        //$clientesComContaBancariaEPedidos->dd();

        //get() faz um all();
        $clientesComContaBancariaEPedidos = $clientesComContaBancariaEPedidos->get();

        return response()->json($clientesComContaBancariaEPedidos, 200);
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
        //dd('oi');
        try {
            $request->validate($this->cliente->regras(), $this->cliente->feedbacks());
            $this->cliente->create($request->all());
            return response()->json('cliente cadastrado com sucesso!');
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }


    public function show($id)
    {
        //$clienteAchado = $this->cliente->find($cliente->id);
        $cliente = $this->cliente->with('contaBancaria')->find($id);

        //dd($idCliente);

        if ($cliente === null) {
            return response()->json(['msg' => 'Cliente não encontrado!'], 404);
        }

        return response()->json($cliente, 200);
    }

    /*
    public function edit(Cliente $cliente)   {


    }
    */

    public function update(Request $request, $id)
    {
        try {
            $cliente = $this->cliente->find($id);
            //dd($cliente);

            if ($cliente === null) {
                return response()->json(['msg' => 'Cliente não encontrado!'], 404);
            }

            //dd($request);

            if ($request->method() == 'PATCH') {

                $regrasDinamicas = array();

                //$teste = '';

                foreach ($cliente->regras() as $input => $regra) {
                    if (array_key_exists($input, $request->all())) {
                        //$teste .= 'Input: ' . $input . ' | Regra: ' . $regra . '<br>';
                        $regrasDinamicas[$input] = $regra;
                        //dump($regrasDinamicas);
                    }
                }

                //return $teste;
                $request->validate($regrasDinamicas, $this->cliente->feedbacks());
            } else {
                $request->validate($this->cliente->regras(), $this->cliente->feedbacks());
            }

            //dd($request);

            //PRIMEIRA MANEIRA DE PERSISTIR OS DADOS
            //$cliente->update($request->all());

            //SEGUNDA MANEIRA DE PERSISTIR OS DADOS
            $cliente->fill($request->all());
            $cliente->save();

            return response()->json($cliente, 200);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }


    public function destroy($id)
    {
        try {
            $cliente = $this->cliente->find($id);

            if ($cliente) {
                $cliente->delete();
                return response()->json($cliente, 200);
            }
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}
