<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('all_product_id');
            $table->unsignedFloat('initial_price');
            $table->unsignedInteger('quantity');
            $table->unsignedFloat('price');
            $table->smallInteger('stock')->default(1);
            $table->unsignedBigInteger('language_id')->default(1);
            $table->boolean('isFoil')->nullable();
            $table->boolean('signed')->nullable();
            $table->boolean('playset')->nullable();
            $table->boolean('altered')->nullable();
            $table->boolean('on_sale')->default(true);
            $table->string('state',2)->default('NM');
            $table->string('comments')->nullable(); //comment from mkm
            $table->string('idArticleMKM')->nullable(); //idArticle from mkm
            $table->string('modifiedMKM')->nullable(); //idArticle from mkm

            $table->foreign('language_id')->references('id')->on('languages');
            $table->foreign('all_product_id')->references('id')->on('all_products');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stocks', function(Blueprint $table){
            $table->dropForeign('[product_id]');
            $table->dropForeign('[language]');
        });
        Schema::dropIfExists('stocks');
    }
}
