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
        Schema::create('gifts_gift_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gift_list_id');
            $table->unsignedBigInteger('gift_id');
            $table->integer('quantity');
//            $table->foreign('gift_list_id')->references('id')->on('gift_lists');
//            $table->foreign('gift_id')->references('id')->on('gifts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gifts_gift_items');
    }
};
