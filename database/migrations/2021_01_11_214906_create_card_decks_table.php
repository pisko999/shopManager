<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateCardDecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('card_decks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deck_id');
            $table->unsignedBigInteger('metaproduct_id');
            $table->unsignedBigInteger('all_product_id')->nullable();
            $table->unsignedBigInteger('stock_id')->nullable();
            $table->unsignedTinyInteger('quantity');
            $table->boolean('foil')->default(false);
            $table->boolean('sideboard')->default(false);
            $table->float('price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('card_decks');
    }
}
