<?php

namespace App\Http\Controllers;

use App\Models\ContaBancaria;
use App\Repositories\ContaBancariaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContaBancariaController extends Controller
{
    protected $contaBancaria;

    public function __construct(ContaBancaria $contaBancaria)
    {
        $this->contaBancaria = $contaBancaria;
    }

    public function index(Request $request)
    {
        //CRIANDO O REPOSITORY

        $contaBancariaRepository = new ContaBancariaRepository($this->contaBancaria);

        if($request->has('atributos_cliente')) {
            $atributos_cliente = 'cliente:id,' . $request->atributos_cliente;
            //dd($atributos_conta);
            //dd($atributos_conta);
            //pega só os atributos da request
            $contaBancariaRepository->selectAtributosRegistrosRelacionados($atributos_cliente);
        } else {
            //pega todos os atributos da conta lá no banco de dados
            //$clientesComContaBancariaEPedidos = $this->cliente->with('contaBancaria');
            $contaBancariaRepository->selectAtributosRegistrosRelacionados('cliente');
        }

        if($request->has('filtro')) {
            $contaBancariaRepository->filtro($request->filtro);
        }

        if ($request->has('atributos_conta')) {
            //dd($atributos_cliente);
            $contaBancariaRepository->selectAtributos($request->atributos_conta);
        }

        return response()->json($contaBancariaRepository->getResultado(), 200);

        /* //FORMA ANTIGA DE FAZER SEM O REPOSITORY

        $contasComSeusRespectivosClientes = array();

        if($request->has('atributos_cliente')) {
            $atributos_cliente = $request->atributos_cliente;
            //setando os atributos dinâmicos de cliente dentro da conta bancária
            $contasComSeusRespectivosClientes = $this->contaBancaria->with('cliente:id,' . $atributos_cliente);
        } else {
            //aqui ele pega todos os atributos do cliente sem nenhum filtro
            $contasComSeusRespectivosClientes = $this->contaBancaria->with('cliente');
        }

        if($request->has('filtro')) {
            $multiplcasCondicoes = explode(";", $request->filtro);
            //dd($multiplcasCondicoes);
            foreach($multiplcasCondicoes as $key => $condicao) {

                $c = explode(':', $condicao);
                //cláusula WHERE usa 3 parâmetros: O primeiro argumento é o nome da coluna. O segundo argumento é um operador, que pode ser qualquer um dos operadores suportados pelo banco de dados. O terceiro argumento é o valor a ser comparado com o valor da coluna.
                $contasComSeusRespectivosClientes = $contasComSeusRespectivosClientes->where($c[0], $c[1], $c[2]);
            }
        }

        if($request->has('atributos_conta')) {
            $atributosDaConta = $request->atributos_conta;
            //setando os atributos dinâmicos da conta bancária
            $contasComSeusRespectivosClientes = $contasComSeusRespectivosClientes->selectRaw($atributosDaConta)->get(); // NÃO USAR AQUI $this->contaBancaria->selectRaw($atributosDaConta)->get(); SENÃO IRIA SOBREPOR O QUE FOI FEITO NO IF DE CIMA
        } else {
            //aqui ele pega todos os atributos da conta sem nenhum filtro
            $contasComSeusRespectivosClientes = $contasComSeusRespectivosClientes->get();
        }

        return response()->json($contasComSeusRespectivosClientes, 200);

        //FORMA ANTIGA DE FAZER SEM O FILTRO ATRIBUTOS
        //$contas = $this->contaBancaria->all();
        //return response()->json($contas, 200);


        */
    }

    public function create() {}

    public function store(Request $request)
    {
        //dd($request->all());
        //dd($request->get('saldo'));

        /* PRIMEIRA MANEIRA DE FAZER
        $contaBancaria = new ContaBancaria();
        $contaBancaria->saldo = $request->get('saldo');
        $contaBancaria->save();
        */

        /* SEGUNDA MANEIRA DE FAZER
        $saldo = $request->get('saldo');
        $registro = $this->contaBancaria->create([
            'saldo' => $saldo
        ]);
        */

        $request->validate($this->contaBancaria->regras(), $this->contaBancaria->feedbacks());

        //TERCEIRA FORMA DE FAZER
        //$conta = $this->contaBancaria->create($request->all());

        //QUARTA FORMA DE FAZER -> INSERINDO UMA IMAGEM EM PNG
        $imagem = $request->imagem;
        $imagem_urn = $imagem->store('imagens', 'public');

        $conta = $this->contaBancaria;

        $conta->cliente_id = $request->cliente_id;
        $conta->saldo = $request->saldo;
        $conta->imagem = $imagem_urn;
        $conta->save();

        return response()->json(['msg' => 'Conta criada com sucesso!'], 201);

        //PARA CRIAR O LINK SIMBÓLICO lá dentro de storage/app/public/imagens para app/public, basta usar o comando no terminal: php artisan storage:link
    }


    public function show($id)
    {

        //PRIMEIRA FORMA DE FAZER
        //return $conta_bancaria; -> sugestão de tipo possui (ContaBancaria $conta_bancaria) no parâmetro

        //MÉTODO QUE O JOSIAS FEZ - SEGUNDA FORMA DE FAZER
        // return $conta_bancarium->where('id', '=', $id)->first();

        //TERCEIRA FORMA DE FAZER
        $conta = $this->contaBancaria->find($id);

        if ($conta === null) {
            return response()->json(['msg' => 'não foi possível encontrar esta conta!'], 404);
        }
        return response()->json($conta, 200);
    }

    /*
    public function edit(ContaBancaria $contaBancaria)
    {
        //
    }
    */

    public function update(Request $request, $id)
    {
        //dd($request->all());

        /*
        print_r($conta_bancaria->getAttributes());
        echo '<br>';
        print_r($request->all());
        */

        //dd($request->imagem);

        //$conta_bancaria->saldo = $request->saldo;
        //$conta_bancaria->save();

        //dd($conta_bancaria);

        //PRIMEIRA FORMA DE FAZER
        //$cb = $this->contaBancaria->update($request->all());

        //SEGUNDA FORMA DE FAZER
        //dd($request->saldo);
        //dd($request->saldo);

        $conta = $this->contaBancaria->find($id);

        //dd($request->saldo);
        //dd($request->imagem);
        //dd('aqui!');
        if ($conta === null) {
            return response()->json(['msg' => 'não foi possível atualizar esta conta!'], 404);
        }

        if ($request->method() === 'PATCH') {

            $regrasDinamicas = array();

            //$teste = '';

            foreach ($conta->regras() as $input => $regra) {
                //$teste .= 'Input= '. $input . ' | Regra= ' . $regra;

                if (array_key_exists($input, $request->all())) { //traduzindo: se existir esse $input dentro de $request->all() faça...
                    $regrasDinamicas[$input] = $regra;
                    //dd($regrasDinamicas);
                }
                //dd($conta->saldo);
                //dd($regrasDinamicas);
            }

            //dd($teste);

            $request->validate($regrasDinamicas, $this->contaBancaria->feedbacks());
        } else { //aqui entra no verbo PUT
            $request->validate($this->contaBancaria->regras(), $this->contaBancaria->feedbacks());
        }

        //removendo a imagem que já estava gravada antes do update
        /*
        if ($request->imagem) {
            Storage::disk('public')->delete($conta->imagem);
        }
        */

        //PARA EU ATUALIZAR UMA IMAGEM, TENHO QUE COLOCAR LÁ NO POSTMAN O VERBO POST JUNTO COM UM ATRIBUTO CHAMADO: _method, O VALOR DE _method PODE SER: PUT OU PATCH DEPENDENDO DA SITUAÇÃO! ISSO QUANDO SE USA O FORM_DATA NO POSTMAN
        //$imagem = $request->imagem;
        //$imagem_urn = $imagem->store('imagens', 'public');

        //dd($imagem_urn);

        //PRIMEIRA FORMA DE PERSISTIR OS DADOS
        /*
        $conta->update([
            'saldo' => $request->saldo,
            'imagem' => $imagem_urn
        ]);
        */

        //$conta->imagem = $imagem_urn;

        //SEGUNDA FORMA DE PERSISTIR OS DADOS
        /*
        $conta->fill($request->all());
        $conta->save();
        */

        if($request->hasFile('imagem')) {
            //removendo a imagem antiga
            Storage::disk('public')->delete($conta->imagem);

            //persistindo a imagem nova
            $imagem = $request->imagem;
            $imagem_urn = $imagem->store('imagens', 'public');
            $conta->imagem = $imagem_urn; //aqui persisti a imagem
            //dump($conta->imagem);

        }

        $conta->fill($request->except('imagem')); //se não tiver o except aqui, a imagem será sobrescrita por: C:\Users\Usuario\AppData\Local\Temp\php8D45.tmp

        //dump($conta->imagem);
        $conta->save();

        return response()->json($conta, 200);
    }


    public function destroy($id)
    {
        //PRIMEIRA FORMA DE FAZER -> tem que ter o ContaBancaria $contaBancaria nos parâmetros
        //$contaBancaria->delete();

        //SEGUNDA FORMA DE FAZER
        $conta = $this->contaBancaria->find($id);

        if ($conta === null) {
            return response()->json(['msg' => 'não foi possível remover esta conta!'], 404);
        }

        //removendo a imagem que já estava gravada antes do update
        Storage::disk('public')->delete($conta->imagem);

        //removendo a conta de fato do banco de dados
        $conta->delete();

        return response()->json(['msg' => 'removeu!'], 200);
    }
}
