<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePedidos extends Migration
{

    public function up()
    {
        Schema::table('pedidos', function(Blueprint $table) {
            $table->softDeletes();
            $table->dropColumn('valor_total');
        });
    }


    public function down()
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
