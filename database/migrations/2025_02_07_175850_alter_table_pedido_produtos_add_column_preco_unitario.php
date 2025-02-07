<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePedidoProdutosAddColumnPrecoUnitario extends Migration
{
   
    public function up()
    {
        Schema::table('pedido_produtos', function(Blueprint $table) {
            $table->float('preco_unitario', 8, 2)->after('pedido_id')->default(0);
        });
    }

    
    public function down()
    {
        Schema::table('pedido_produtos', function(Blueprint $table) {
            $table->dropColumn('preco_unitario');
        });
    }
}
