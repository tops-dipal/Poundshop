<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeSizeOfProductDigits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('po_products', function (Blueprint $table) {
            $table->decimal('sel_price',32,2)->nullable()->change();
            $table->decimal('total_delivery_charge',32,2)->nullable()->change();
            $table->decimal('landed_product_cost',32,2)->nullable()->change();
            $table->decimal('expected_mros',32,2)->nullable()->change();
            $table->decimal('mros',32,2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('po_products', function (Blueprint $table) {
            $table->decimal('sel_price',10,2)->nullable()->change();
            $table->decimal('total_delivery_charge',10,2)->nullable()->change();
            $table->decimal('landed_product_cost',10,2)->nullable()->change();
            $table->decimal('expected_mros',10,2)->nullable()->change();
            $table->decimal('mros',10,2)->nullable()->change();
        });
    }
}
