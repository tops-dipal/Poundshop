<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePoFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('purchase_order_master', function (Blueprint $table)
        {
            $table->char('incoterms',3)->after('po_import_type')->comment('EXW (Ex Works),FCA (Free Carrier),FAS (Free Alongside Ship),FOB (Free on Board),CFR (Cost and Freight) ,CIF (Cost, Insurance and Freight),CPT (Carriage Paid to) ,CIP (Carriage and Insurance Paid To),
                        DAT (Delivered at Terminal),
                        DAP (Delivered at Place),
                        DDP (Delivered Duty Paid),')->nullable();
            $table->tinyInteger('mode_of_shipment')->after('incoterms')->comment('1=Air,2=Sea,3=Truck,4=Sea and Truck')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_master', function (Blueprint $table)
        {
            $table->removeColumn('incoterms');
            $table->removeColumn('mode_of_shipment');
        });
    }
}
