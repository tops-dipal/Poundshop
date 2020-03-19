<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAddProductTypeOneMore extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        Schema::table('products', function (Blueprint $table) {
            $table->smallInteger('product_identifier_type')->nullable()->default(NULL)->comment('1 - UPC, 2 - EAN, 3 - Other')->change();
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
        Schema::table('products', function (Blueprint $table) {
            $table->smallInteger('product_identifier_type')->nullable()->default(1)->comment('1 - UPC, 2 - EAN')->change();
        });
    }
}
