<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
class AlterMagentoProductPosting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        DB::connection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        Schema::table('magento_product_posting', function (Blueprint $table) {
            $table->date('date_to_go_live')->nullable()->after('magento_vendor_id');
            $table->decimal('bulk_selling_price', 10,2)->nullable()->after('selling_price');
            $table->string('short_description', 10,2)->nullable()->after('product_description');
            $table->renameColumn('weight', 'magento_product_weight');
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
            
            $table->dropColumn('date_to_go_live');
            $table->dropColumn('bulk_selling_price');
            $table->dropColumn('short_description');
            $table->renameColumn('magento_product_weight', 'weight');
        });
    }
}
