<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commands', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('storekeeper_id')->default(1);
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->unsignedBigInteger('shipping_method_id')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('temporary_email')->nullable();
            $table->boolean('is_presale')->default(false);
            $table->unsignedBigInteger('evaluation_id')->nullable();
            $table->unsignedDouble('article_value')->nullable();
            $table->unsignedDouble('total_value')->nullable();
            $table->unsignedBigInteger('billing_address_id')->nullable();
            $table->unsignedBigInteger('delivery_address_id')->nullable();
            $table->unsignedInteger('idOrderMKM')->nullable();


            $table->foreign('shipping_method_id')->references('id')->on('shipping_methods');
            $table->foreign('evaluation_id')->references('id')->on('evaluations');
            $table->foreign('billing_address_id')->references('id')->on('addresses');
            $table->foreign('delivery_address_id')->references('id')->on('addresses');
//            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('payment_id')->references('id')->on('payments');
            $table->foreign('status_id')->references('id')->on('statuses');
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
        Schema::table('commands', function(Blueprint $table){
            $table->dropForeign('[shipping_method_id]');
            $table->dropForeign('[evaluation_id]');
            $table->dropForeign('[billing_address_id]');
            $table->dropForeign('[delivery_address_id]');
            $table->dropForeign('[client_id]');
            $table->dropForeign('[payment_id]');
            $table->dropForeign('[status_id]');
        });
        Schema::dropIfExists('commands');
    }
}
