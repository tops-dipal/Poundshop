<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEnableEnabledupdateToMagentoProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magento_products', function (Blueprint $table) {
            $table->tinyInteger('is_enabled')->default('0')->comment('0=No, 1=Yes')->after('is_updated_in_product_master');
            $table->tinyInteger('is_enabled_updated')->default('0')->comment('0=No, 1=Yes')->after('is_enabled');
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
            $table->dropColumn('is_enabled');
            $table->dropColumn('is_enabled_updated');
        });
    }
}
