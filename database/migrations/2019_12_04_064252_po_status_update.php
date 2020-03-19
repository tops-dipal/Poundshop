<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PoStatusUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('purchase_order_master', function (Blueprint $table) {
            $table->smallInteger('po_status')->comment('1-Draft(default),2-live po(sent po),3-negotiating with supplier,4-supplier confirmed,5-book in,6-Part Delivered,7-Delivered,8-Receiving,9-Completed,10-Cancelled')->change();
       });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('purchase_order_master', function (Blueprint $table) {
            $table->tinyInteger('po_status')->comment(' 1-open(default),2-awaiting for supplier approval,3-negotiating with supplier,4-approved from supplier,5-in transit,6-completed,7-cancelled,8-revised')->change();
       });
    }
}
