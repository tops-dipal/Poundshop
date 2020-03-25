<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRosOverrideToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->tinyInteger('is_override')->default(0)->after('stock_hold_days')->comment('0=No,1=Yes, 1=override by buyingrange for stockholddays');
            $table->integer('ros')->default(0)->after('last_stock_receipt_qty')->comment('Rate Of Sale');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('is_override');
            $table->dropColumn('ros');
        });
    }
}
