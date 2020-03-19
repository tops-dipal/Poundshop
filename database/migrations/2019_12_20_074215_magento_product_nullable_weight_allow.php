<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MagentoProductNullableWeightAllow extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
         DB::connection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
         Schema::table('magento_products', function (Blueprint $table) {
            $table->decimal('product_weight',12,2)->nullable()->change();
            $table->decimal('product_height',12,2)->nullable()->change();
            $table->decimal('product_width',12,2)->nullable()->change();
            $table->decimal('product_length',12,2)->nullable()->change();
          
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
        Schema::table('magento_products', function (Blueprint $table) {
            $table->decimal('product_weight',12,2)->nullable(false)->change();
            $table->decimal('product_height',12,2)->nullable(false)->change();
            $table->decimal('product_width',12,2)->nullable(false)->change();
            $table->decimal('product_length',12,2)->nullable(false)->change();
          
        });
    }
}
