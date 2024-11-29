<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableProdutosAddSoftdelete extends Migration
{

    public function up()
    {
        Schema::table('produtos', function(Blueprint $table) {
            $table->softDeletes();
        });
    }


    public function down()
    {
        Schema::table('produtos', function(Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
