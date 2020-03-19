<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFieldsPo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('po_product_trans', function (Blueprint $table)
        {
            $table->renameColumn('quan_per_case', 'qty_per_box');
            $table->renameColumn('total_cases', 'total_box');
            $table->renameColumn('price_per_quant', 'unit_price');
            $table->renameColumn('exp_mros', 'expected_mros');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         
        Schema::table('po_product_trans', function (Blueprint $table)
        {
            $table->renameColumn('qty_per_box', 'quan_per_case');
            $table->renameColumn('total_box', 'total_cases');
            $table->renameColumn('unit_price', 'price_per_quant');
            $table->renameColumn('expected_mros', 'exp_mros');
        });
    }
}
