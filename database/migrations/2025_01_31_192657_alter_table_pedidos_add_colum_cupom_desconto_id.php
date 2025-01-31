<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePedidosAddColumCupomDescontoId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pedidos', function(Blueprint $table) {
            
            $table->unsignedBigInteger('cupom_desconto_id')->after('id')->nullable();
            $table->foreign('cupom_desconto_id')->references('id')->on('cupom_desconto');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pedidos', function(Blueprint $table) {
            $table->dropForeign(['cupom_desconto_id']);
            $table->dropColumn('cupom_desconto_id');
        });
    }
}
