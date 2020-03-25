<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingPOPorudctLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_po_product_locations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('case_detail_id')->unsigned()->index()->nullable()->comment('Foregin key of booking_po_product_case_details');
            $table->integer('qty')->nullable();
            $table->date('best_before_date')->nullable();
            $table->string('location')->nullable();
            $table->bigInteger('created_by')->unsigned()->index();
            $table->bigInteger('modified_by')->unsigned()->index();
            $table->timestamps();
            $table->foreign('case_detail_id')->references('id')->on('booking_po_product_case_details')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade'); 
            $table->foreign('modified_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booking_po_product_locations');
    }
}
