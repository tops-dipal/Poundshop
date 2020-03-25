<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStockHoldDaysToRangeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('range_master', function (Blueprint $table) {
             $table->integer('stock_hold_days')->default(0)->after('path');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('range_master', function (Blueprint $table) {
             $table->dropColumn('stock_hold_days');
        });
    }
}
