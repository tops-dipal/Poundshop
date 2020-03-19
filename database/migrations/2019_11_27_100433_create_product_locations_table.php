<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warehouse_master', function (Blueprint $table) {
            $table->increments('id')->unsigned()->change();
        });    

        Schema::create('product_locations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('product_id')->unsigned()->comment('Foreign key of products');
            $table->integer('warehouse_id')->unsigned()->comment('Foreign key of warehouse_master');
            $table->integer('available_quantity')->nullable();
            $table->integer('reserved_quantity')->nullable();
            $table->tinyInteger('is_best_before_date')->default('0')->comment('0 - No, 1 - Yes');
            $table->dateTime('best_before_date')->nullable();
            $table->bigInteger('created_by')->unsigned()->index()->nullable();
            $table->bigInteger('modified_by')->unsigned()->index()->nullable();
            $table->timestamps();
            
            $table->foreign('warehouse_id')->references('id')->on('warehouse_master')->onDelete('cascade');
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
        Schema::dropIfExists('product_locations');

        Schema::table('warehouse_master', function (Blueprint $table) {
            $table->integer('id')->autoIncrement()->change();
        });
    }
}
