<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMagentoVariationThemeOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_variation_theme_options', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('magento_attribute_id')->comment('Attribute id of magneto store');
            $table->integer('magento_variation_theme_id')->comment('reference of primary key from magento_variation_theme table');
            $table->integer('magento_option_id')->comment('option id from magento store');
            $table->string('option_value',100)->comment('Variation value of the variation product');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('magento_variation_theme_options');
    }
}
