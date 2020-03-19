<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMagentoVariationThemeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_variation_theme', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('magento_attribute_id')->comment('Attribute id of magneto store');
            $table->string('variation_theme_name',30)->nullable()->comment('variation theme name on magento');
            $table->string('attribute_code',150)->comment('magento attribute code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('magento_variation_theme');
    }
}
