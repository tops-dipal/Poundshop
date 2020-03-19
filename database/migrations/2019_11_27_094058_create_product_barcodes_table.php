<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductBarcodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('product_barcodes');
        Schema::create('product_barcodes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('product_id')->unsigned()->comment('Foreign key of products');
            $table->tinyInteger('barcode_type')->default('1')->comment('1 - Single barcode, 2 - Inner Barcode, 3 - Outer Barcode')->nullable();
            $table->integer('inner_case_qunatity')->nullable();
            $table->string('barcode')->unique()->index();
            $table->bigInteger('created_by')->unsigned()->index()->nullable();
            $table->bigInteger('modified_by')->unsigned()->index()->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
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
        Schema::dropIfExists('product_barcodes');
    }
}
