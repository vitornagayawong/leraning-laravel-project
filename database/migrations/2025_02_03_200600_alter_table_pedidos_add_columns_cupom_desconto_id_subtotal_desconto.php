<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePedidosAddColumnsCupomDescontoIdSubtotalDesconto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pedidos', function(Blueprint $table) {
            $table->unsignedBigInteger('cupom_desconto_id')->after('id')->default(null)->nullable();
            $table->float('subtotal', 8, 2)->after('deleted_at');
            $table->float('desconto_porcetagem', 8, 2)->after('subtotal')->default(0);
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
            $table->dropColumn('desconto_porcetagem');
            $table->dropColumn('subtotal');
            $table->dropColumn('cupom_desconto_id');
        });
    }
}
