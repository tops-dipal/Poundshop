<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWarehouseLocTypePrefix extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_loc_type_prefix', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('warehouse_id', false)->unsigned()->index()->nullable()->comment('reference to warehouse_master table');
            $table->bigInteger('location_type')->unsigned()->index()->nullable()->comment('reference to array helper table');
            $table->string('prefix', 100)->comment('The prefix for the location type');
            $table->timestamps();
            $table->foreign('warehouse_id')->references('id')->on('warehouse_master')->onDelete('cascade');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warehouse_loc_type_prefix');
    }
}
