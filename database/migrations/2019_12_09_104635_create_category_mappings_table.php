<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoryMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_mappings', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('range_id')->unsigned()->index()->nullable()->comment('Primary key of range_master');
            $table->integer('magento_category_id')->unsigned()->index()->nullable()->comment('Primary key of magento_categories');
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->bigInteger('modified_by')->unsigned()->nullable();
            $table->foreign('range_id')->references('id')->on('range_master')->onDelete('cascade');
            $table->foreign('magento_category_id')->references('id')->on('magento_categories')->onDelete('cascade');
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
        Schema::dropIfExists('category_mappings');
    }
}
