<?php

namespace App\Http\Controllers;

use App\Models\CupomDesconto;
use App\Repositories\CupomDescontoRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CupomDescontoController extends Controller
{
    protected $cupomDesconto;

    public function __construct(CupomDesconto $cupomDesconto)
    {
        $this->cupomDesconto = $cupomDesconto;
    }

    public function index()
    {
        DB::beginTransaction();
        try {
            $cupomDescontoRepository = new CupomDescontoRepository($this->cupomDesconto);
            //dd('oi');
            //dd($cupomDescontoRepository);

            DB::commit();
            return response()->json([$cupomDescontoRepository->getNormal()], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['Error' => $e->getMessage()], 404);
        }
    }

    
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            //dd($this->cupomDesconto);
            $request->validate($this->cupomDesconto->regras(), $this->cupomDesconto->feedbacks());
            //dd('oi');
            
            
                //$dadosRequisitados = $request->all();
                // dd($this->cupomDesconto);
                // dd($dadosRequisitados);
                $this->cupomDesconto->codigo = $request->codigo;
                $this->cupomDesconto->valor_desconto = $request->valor_desconto;                
                // $this->cupomDesconto->save();
                // $this->cupomDesconto->fill($dadosRequisitados);
                $this->cupomDesconto->save();

                DB::commit();
                return response()->json(['Cupom cadastrado' => $this->cupomDesconto], 200);
                DB::commit();
            } catch (ValidationException $e) {
                DB::rollBack();
                return response()->json(['validation_errors' => $e->errors()], 422);  // Aqui retornamos os erros
            } catch (Exception $e) {
                DB::rollBack();
                //dd($e);
            return response()->json(['Errorrrrrrrrrrrrr' => $e->getMessage()], 404);
        }
    }
    
    
    public function show($id)
    {   
        DB::beginTransaction();
        try {
            $cupomDesconto = $this->cupomDesconto->with('pedido')->find($id);
            
            if($cupomDesconto == null) {
                return response()->json(['Error' => 'Cupom de desconto não encontrado!!!'], 401);
            }

            DB::commit();
            return response()->json(['Cupom de desconto' => $cupomDesconto], 200);
        } catch(Exception $e) {
            DB::rollBack();
            return response()->json(['Error' => $e->getMessage()], 404);
        }
    }

    public function update(Request $request, $id)
    {   
        DB::beginTransaction();
        try {
            $request->validate($this->cupomDesconto->regras(), $this->cupomDesconto->feedbacks());
            //dd('aquiiiiiiiiiiigfdgfdgfdgfdfdg');
            //dd('oi');

            $cupomDesconto = $this->cupomDesconto->find($id);
            //dd($cupomDesconto);
            $cupomDesconto->codigo = $request->codigo;
            $cupomDesconto->valor_desconto = $request->valor_desconto;
            $cupomDesconto->save();

            DB::commit();

            return response()->json(['Cupom atualizado' => $cupomDesconto], 200);
            
        } catch (ValidationException $e) { 
            DB::rollBack();
            return response()->json(['validation_errors' => $e->errors()], 422);  // Aqui retornamos os erros
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['Errorr' => $e->getMessage()], 404);
        } 
    }

    
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $cupomDesconto = $this->cupomDesconto->find($id);

            if($cupomDesconto == null) {
                return response()->json(['Error!' => 'Cupom não foi encontrado'], 404);
            }
            
            if($cupomDesconto) {
                $cupomDesconto->delete();
                DB::commit();
                return response()->json(['Cupom removido com sucesso!' => $cupomDesconto], 200);
            }            
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['Errorr' => $e->getMessage()], 404);
        }
    }
}
