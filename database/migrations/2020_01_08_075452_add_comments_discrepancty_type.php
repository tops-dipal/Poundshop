<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommentsDiscrepanctyType extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public
            function up() {
        Schema::table('booking_purchase_orders_discrepancy', function (Blueprint $table) {
            $table->smallInteger("discrepancy_type")->tinyInteger('discrepancy_type')->comment("1 => 'Shortage',2 => 'Over',3 => 'Freight Damaged',4 => 'Damaged',5 => 'Internally Damaged',6 => 'Against Trading Standard',7 => 'Not Fit for Sale'")->change();
            $table->smallInteger("status")->tinyInteger('status')->comment("1-debit,2-keepit,3-dispose,4-return,5-cancelled,6-move to new po,")->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public
            function down() {
        Schema::table('booking_purchase_orders_discrepancy', function (Blueprint $table) {
            $table->smallInteger("discrepancy_type")->tinyInteger('discrepancy_type')->comment("")->change();
            $table->smallInteger("status")->tinyInteger('status')->comment("")->change();
        });
    }

}
