<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableContaBancaria extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('conta_bancarias', function (Blueprint $table) {
            $table->unsignedBigInteger('cliente_id')->after('id')->unique(); //colocar unique na tabela mais "fraca" do relacionamento 1 para 1
            $table->string('imagem', 100)->after('cliente_id');

            $table->foreign('cliente_id')->references('id')->on('clientes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('conta_bancarias', function (Blueprint $table) {
            $table->dropColumn('imagem');
        });
    }
}
