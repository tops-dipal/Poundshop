<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMagentoVariationThemeOptionsMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_variation_theme_options_mapping', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('variation_theme_id')->comment('primary key of magento_variation_theme table');
            $table->integer('variation_theme_option_id')->comment('primary key of magento_variation_theme_options table');
            $table->integer('product_id')->comment('primary key of magento_products table');
            $table->integer('parent_product_id')->comment('parent primary key of magento_products table');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('magento_variation_theme_options_mapping');
    }
}
