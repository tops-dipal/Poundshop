<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMagentoPriceLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_price_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('magento_id');
            $table->decimal('selling_price',10,2);
            $table->tinyInteger('is_selling_price_posted')->default(0)->comment('0 - No , 1 -Yes , 2 - Price Reversed While Reversing');
            $table->dateTime('inserted_date')->nullable();
            $table->dateTime('modified_date')->nullable();
            $table->dateTime('last_modified')->nullable();
            $table->integer('modified_by')->nullable();            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('magento_price_log');
    }
}
