<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MagenotlistinupdaeproductType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropColumn();
        Schema::table('magento_product_posting', function (Blueprint $table) {
            $table->smallInteger('magento_product_id_type')->default('1')->nullable()->after('magento_product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropColumn();
        
        Schema::table('magento_product_posting', function (Blueprint $table) {
            $table->enum('magento_product_id_type', ['upc', 'ean'])->nullable()->after('magento_product_id');
        });
    }

    public function dropColumn()
    {
        Schema::table('magento_product_posting', function (Blueprint $table) {
            $table->dropColumn('magento_product_id_type');
        });
    }
}
