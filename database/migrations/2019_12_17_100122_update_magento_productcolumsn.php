<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateMagentoProductcolumsn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        

        DB::statement("ALTER TABLE magento_products MODIFY COLUMN product_type enum ('normal','parent','variation') default 'normal'");
        
        DB::connection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

        $this->dropColumn();
        
        Schema::table('magento_products', function (Blueprint $table) {
            $table->bigInteger('country_of_origin')->index()->unsigned()->nullable()->after('manufacturer_part_number');
            $table->foreign('country_of_origin')->references('id')->on('countries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         DB::connection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

        $this->dropColumn();
        
        Schema::table('magento_products', function (Blueprint $table) {
            $table->string('country_of_origin')->nullable()->after('manufacturer_part_number');
            $table->dropForeign('magento_products_country_of_origin_foreign');
            $table->tinyInteger('product_type')->comment('1-normal,2-variation,3-parent')->default(1)->change();
        });
    }

    public function dropColumn()
    {
        Schema::table('magento_products', function (Blueprint $table) {
            $table->dropColumn('country_of_origin');
        });
    }
}
