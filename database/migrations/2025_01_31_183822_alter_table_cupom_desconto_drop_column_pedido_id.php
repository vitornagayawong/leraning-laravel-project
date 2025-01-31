<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableCupomDescontoDropColumnPedidoId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cupom_desconto', function(Blueprint $table) {
            $table->dropForeign(['pedido_id']); //para remover a chave estrangeira primeiro, preciso estar dentro de colchetes dentro das aspas simples
            $table->dropColumn('pedido_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
