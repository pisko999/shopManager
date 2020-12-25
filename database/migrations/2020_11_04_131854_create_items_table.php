<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_id');
            $table->unsignedBigInteger('command_id');
            $table->unsignedFloat('price');
            $table->unsignedInteger('quantity');
            $table->foreign('stock_id')->references('id')->on('stocks');
            $table->foreign('command_id')->references('id')->on('commands');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function(Blueprint $table){
            $table->dropForeign('[stock_id]');
            $table->dropForeign('[command_id]');
        });
        Schema::dropIfExists('items');
    }
}
