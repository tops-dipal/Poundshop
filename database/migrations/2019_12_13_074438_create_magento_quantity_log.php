<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMagentoQuantityLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_quantity_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('magento_id');
            $table->integer('quantity');
            $table->tinyInteger('is_quantity_posted')->default(0)->comment('0-not posted,1-posted');
            $table->dateTime('inserted_date')->nullable();
            $table->dateTime('modified_date')->nullable();
            $table->dateTime('last_modified')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('magento_quantity_log');
    }
}
