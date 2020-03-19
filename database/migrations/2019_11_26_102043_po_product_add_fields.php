<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PoProductAddFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('po_product_trans', function (Blueprint $table)
        {
            $table->decimal('vat', 10, 2)->after('unit_price');
            $table->decimal('sel_qty', 10, 2)->after('vat');
            $table->decimal('sel_price', 10, 2)->after('sel_qty');
            $table->decimal('import_duty', 10, 2)->after('sel_price');
            $table->integer('cube_per_box')->after('import_duty');
            $table->integer('total_num_cubes')->after('cube_per_box');
            $table->decimal('total_delivery_charge', 10, 2)->after('total_num_cubes');
            $table->decimal('landed_product_cost', 10, 2)->after('total_delivery_charge');
            $table->timestamps();
            
        });
        
        
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         //
        Schema::table('po_product_trans', function (Blueprint $table)
        {
            $table->removeColumn('vat');
            $table->removeColumn('sel_qty');
            $table->removeColumn('sel_price');
            $table->removeColumn('import_duty');
            $table->removeColumn('cube_per_box');
            $table->removeColumn('total_num_cubes');
            $table->removeColumn('total_delivery_charge');
            $table->removeColumn('landed_product_cost');
            $table->dropTimestamps();
        });
    }
}
