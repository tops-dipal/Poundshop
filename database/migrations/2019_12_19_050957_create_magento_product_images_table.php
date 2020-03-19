<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMagentoProductImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_product_images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('magento_id')->nullable();
            $table->integer('magento_product_id')->nullable();
            $table->string('image_url',750)->nullable();
            $table->string('image_file',750)->nullable();
            $table->dateTime('inserted_date')->nullable();
            $table->dateTime('modified_date')->nullable();
            $table->timestamp('last_modified');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('magento_product_images');
    }
}
