<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuyItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buy_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_product');
            $table->unsignedBigInteger('id_stock')->nullable();
            $table->unsignedBigInteger('id_buy_command');
            $table->unsignedBigInteger('id_language')->default(1);
            $table->float('price');
            $table->integer('quantity');
            $table->string('state',2)->default('NM');
            $table->boolean('isFoil')->nullable();
            $table->boolean('playset')->nullable();
            $table->boolean('signed')->nullable();
            $table->boolean('altered')->nullable();

            $table->foreign('id_product')->references('id')->on('all_products');
            $table->foreign('id_stock')->references('id')->on('stocks');
            $table->foreign('id_buy_command')->references('id')->on('buy_commands');
            $table->foreign('id_language')->references('id')->on('languages');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('buy_items', function(Blueprint $table){
            $table->dropForeign('[id_product]');
            $table->dropForeign('[id_stock]');
            $table->dropForeign('[id_buy_command]');
            $table->dropForeign('[id_language]');
        });
        Schema::dropIfExists('buy_items');
    }
}
