<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveSixColumnsFromMagentoProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magento_products', function (Blueprint $table) {
            $table->dropColumn('isbn');
            $table->dropColumn('magento_item_condition');
            $table->dropColumn('magento_condition_notes');
            $table->dropColumn('magento_vendor_id');
            $table->dropColumn('is_display');
            $table->dropColumn('is_enabled');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('magento_products', function (Blueprint $table) {
            $table->string('isbn',50)->nullable();
            $table->string('magento_item_condition',50)->nullable();
            $table->string('magento_condition_notes',255)->nullable();
            $table->integer('magento_vendor_id');
            $table->tinyInteger('is_display')->default('1')->comment('0 - product will not display, 1 - product will display');
            $table->tinyInteger('is_enabled')->default('1')->comment('0-if the product is disable, 1-if the SKU is Enable, 2 = if product is deleted');
        });
    }
}
