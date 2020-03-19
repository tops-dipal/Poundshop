<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProductTitleToMagentoProductPostingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magento_product_posting', function (Blueprint $table) {
            $table->string('product_title',250)->nullable()->after('product_description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('magento_product_posting', function (Blueprint $table) {
            $table->dropColumn('product_title');
        });
    }
}
