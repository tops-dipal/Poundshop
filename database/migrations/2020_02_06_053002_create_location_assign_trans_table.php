<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationAssignTransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('location_assign_trans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('loc_ass_id')->unsigned()->index()->nullable()->comment('PK of locations_assign');
            $table->bigInteger('case_detail_id')->unsigned()->index()->nullable()->comment('PK of booking_po_product_case_details');
            $table->integer('qty')->nullable();
            $table->date('best_before_date')->nullable();
            $table->string('barcode',191)->nullable();
            $table->tinyInteger('case_type')->nullable()->comment('1-Outer, 2-Inner,3-Loose');
            $table->foreign('loc_ass_id')->references('id')->on('locations_assign')->onDelete('cascade'); 
            $table->foreign('case_detail_id')->references('id')->on('booking_po_product_case_details')->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('location_assign_trans');
    }
}
