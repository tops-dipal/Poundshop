<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePalletsBarcodeTrans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pallets_barcode_trans', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('pallet_id')->comment('from pallets_master table')->unsigned()->index()->nullable();
            $table->string('barcode',20)->nullable();
            $table->tinyInteger('status')->default('1')->comment('1-Active,2-Inactive');
            // $table->foreign('pallet_id')->references('id')->on('pallets_master')->onDelete('cascade');                        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pallets_barcode_trans');
    }
}
