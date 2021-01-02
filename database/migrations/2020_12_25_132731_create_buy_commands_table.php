<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuyCommandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buy_commands', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_client');
            $table->unsignedBigInteger('id_storekeeper')->nullable();
            $table->unsignedBigInteger('id_payment')->nullable();
            $table->unsignedBigInteger('id_status')->default(2);
            $table->string('comment')->default('');
            $table->float('initial_value')->default(0);
            $table->float('value')->default(0);

            $table->timestamps();

            $table->foreign('id_client')->references('id')->on('users');
            $table->foreign('id_storekeeper')->references('id')->on('users');
            $table->foreign('id_payment')->references('id')->on('payments');
            $table->foreign('id_status')->references('id')->on('statuses');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('buy_commands', function(Blueprint $table){
            $table->dropForeign('[id_client]');
            $table->dropForeign('[id_storekeeper]');
            $table->dropForeign('[id_payment]');
            $table->dropForeign('[id_status]');
        });
        Schema::dropIfExists('buy_commands');
    }
}
