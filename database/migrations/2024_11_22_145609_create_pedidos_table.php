<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePedidosTable extends Migration
{

    public function up()
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id');
            $table->date('data');
            $table->float('valor_total', 8, 2);
            $table->timestamps();

            //fk
            $table->foreign('cliente_id')->references('id')->on('clientes');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('pedidos');
    }
}
