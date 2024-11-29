<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePedidoProdutos extends Migration
{
    public function up()
    {
        Schema::table('pedido_produtos', function(Blueprint $table) {
            $table->float('valor_total', 8, 2);
        });
    }

    public function down()
    {
        Schema::table('pedido_produtos', function(Blueprint $table) {
            $table->dropColumn('valor_total');
        });
    }
}
