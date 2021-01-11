<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplaintEvaluationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('complaint_evaluation', function (Blueprint $table) {
            $table->unsignedBigInteger('complaint_id');
            $table->unsignedBigInteger('evaluation_id');

            $table->foreign('complaint_id')->references('id')->on('complaints');
            $table->foreign('evaluation_id')->references('id')->on('evaluations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('complaint_evaluation', function(Blueprint $table){
            $table->dropForeign('[complaint_id]');
            $table->dropForeign('[evaluation_id]');
        });
        Schema::dropIfExists('complaint_evaluation');
    }
}
