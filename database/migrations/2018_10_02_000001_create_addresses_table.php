<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name')->nullable();
            $table->string('extra')->nullable();
            $table->string('street');
            $table->string('number')->nullable();
            $table->string('flat')->nullable()->default(null);
            $table->string('city');
            $table->string('country');
            $table->string('region')->nullable()->default(null);
            $table->string('postal');

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('addresses', function(Blueprint $table){
            $table->dropForeign('[user_id]');
        });
        Schema::dropIfExists('addresses');

    }
}
