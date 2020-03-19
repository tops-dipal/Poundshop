<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCartonBarcodeTrans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carton_barcode_trans', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('carton_id')->comment('from carton_master table')->unsigned()->index()->nullable();
            $table->string('barcode',20)->nullable();
            $table->tinyInteger('status')->default('1')->comment('1-Active,2-Inactive');
            // $table->foreign('carton_id')->references('id')->on('carton_master')->onDelete('cascade');                        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carton_barcode_trans');
    }
}
