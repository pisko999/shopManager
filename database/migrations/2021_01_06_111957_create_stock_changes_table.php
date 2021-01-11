<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockChangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_changes', function (Blueprint $table) {
            $table->id();
            $table->string("type");
            $table->unsignedBigInteger('stock_id');
            $table->string('id_article_MKM')->nullable();
            $table->string("data1")->nullable();
            $table->string("data2")->nullable();
            $table->integer("batch");

           // $table->foreign('stock_id')->references('id')->on('stocks');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
/*
        Schema::table('stock_changes', function(Blueprint $table){
            $table->dropForeign('[stock_id]');
        });
*/
        Schema::dropIfExists('stock_changes');
    }
}
