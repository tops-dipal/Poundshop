<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTotesBarcodeTrans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('totes_barcode_trans', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('totes_id')->comment('from totes_master table')->unsigned()->index()->nullable();
            $table->string('barcode',20)->nullable();
            $table->tinyInteger('status')->default('1')->comment('1-Active,2-Inactive');
            // $table->foreign('totes_id')->references('id')->on('totes_master')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('totes_barcode_trans');
    }
}
