<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationsAssignTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations_assign', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('product_id')->unsigned()->index()->comment("reference from products");
            $table->bigInteger('location_id')->unsigned()->index()->comment("reference from locations_master");
            $table->integer('current_qty')->default(0);
            $table->integer('qty_fit_in_location')->default(0);
            $table->tinyInteger('putaway_type')->default(0)->comment('0=Location Assignment,1=Material Receipt,2= Replenishment');
            $table->unsignedInteger('booking_id')->index()->nullable()->comment("reference from bookings");
            $table->tinyInteger('is_mannual')->default(0)->comment('0=No,1=Yes, 1=record stores from location assignment');

            $table->bigInteger('created_by')->unsigned()->index();
            $table->bigInteger('modified_by')->unsigned()->index();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('locations_master')->onDelete('cascade'); 
             $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');            
            $table->foreign('modified_by')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('locations_assign');
    }
}
