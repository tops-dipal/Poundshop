<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookinPOProductCaseDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_po_product_case_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('booking_po_product_id')->unsigned()->index()->nullable();
            $table->bigInteger('parent_outer_id')->nullable();
            $table->string('barcode')->nullable();
            $table->smallInteger('case_type')->nullable()->comment('1 - Outer, 2 - Inner, 3 - Loose');
            $table->smallInteger('is_include_count')->default(0)->comment('0 - No , 1 - Yes');
            $table->integer('qty_per_box')->nullable();
            $table->integer('no_of_box')->nullable();
            $table->integer('total')->nullable();
            $table->smallInteger('is_photobooth')->default(0)->comment('0 - No, 1 - Yes');
            $table->bigInteger('created_by')->unsigned()->index();
            $table->bigInteger('modified_by')->unsigned()->index();
            $table->timestamps();
            
            $table->foreign('booking_po_product_id')->references('id')->on('booking_po_products')->onDelete('cascade');           
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
        Schema::dropIfExists('booking_po_product_case_details');
    }
}
