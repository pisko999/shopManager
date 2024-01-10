<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gift_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gift_list_id');
            $table->unsignedBigInteger('all_product_id');
            $table->integer('quantity');
            $table->integer('quantity_rest');
            $table->boolean('foil')->default(false);
//            $table->foreign('gift_list_id')->references('id')->on('gift_lists');
//            $table->foreign('all_product_id')->references('id')->on('all_products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gift_items');
    }
};
